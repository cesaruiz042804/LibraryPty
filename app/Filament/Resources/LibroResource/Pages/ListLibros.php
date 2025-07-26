<?php

namespace App\Filament\Resources\LibroResource\Pages;

use App\Filament\Resources\LibroResource;
use Filament\Actions; // ¡Este es el namespace correcto para las acciones de página!
use Filament\Resources\Pages\ListRecords;
use App\Filament\Exports\LibrosExporter;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\FileUpload;
use App\Filament\Imports\LibroImporter;
use Filament\Actions\ImportAction; // Usa esta para acciones de cabecera de página
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Filament\Actions\Action; // Usa esta para la acción personalizada de página


use App\Models\Libro;
use App\Models\Category;

class ListLibros extends ListRecords
{
    protected static string $resource = LibroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('generateGeneralPdfReport') // Renombrado para mayor claridad
                ->label('Generar Reporte General PDF')
                ->icon('heroicon-o-document-arrow-down') // Icono opcional
                ->color('info') // Color opcional
                ->outlined()
                ->url(fn(): string => route('generate.libro.reporte_general.pdf'))
                ->openUrlInNewTab(),

            Action::make('generateLibrosStockMin') // Renombrado para mayor claridad
                ->label('Stock bajo PDF')
                ->icon('heroicon-o-document-arrow-down') // Icono opcional
                ->color('info') // Color opcional
                ->outlined()
                ->url(fn(): string => route('generate.libro.reporte_stock_minimo.pdf'))
                ->openUrlInNewTab(),

            // ImportAction de Filament\Actions (para la cabecera de página)
            ImportAction::make()
                ->label('Importar Libros desde Excel')
                ->color('info')
                ->outlined()
                ->importer(LibroImporter::class)
                ->chunkSize(1000)
                ->maxRows(5000)
                ->csvDelimiter(','),

            // Action genérica de Filament\Actions (para tu acción personalizada importXml)
            Action::make('importXml')
                ->label('Importar Libros (XML)') // Este label está duplicado más abajo
                ->icon('heroicon-o-document-arrow-up')
                ->label('Importar Libros (XML)') // Elimina el label duplicado si no es intencional
                ->color('info')
                ->outlined()
                ->modalSubmitActionLabel('Importar')
                ->form([
                    FileUpload::make('xml_file')
                        ->label('Selecciona un archivo XML')
                        ->required()
                        ->acceptedFileTypes(['application/xml', 'text/xml'])
                        ->disk('local')
                        ->directory('temp-imports')
                        ->visibility('private'),
                ])
                ->action(function (array $data) {
                    $filePath = $data['xml_file'];
                    $importedCount = 0;
                    $failedIsbns = [];

                    DB::beginTransaction();

                    try {
                        $xmlContent = Storage::disk('local')->get($filePath);
                        $xml = simplexml_load_string($xmlContent);

                        if ($xml === false) {
                            throw new \Exception("No se pudo parsear el archivo XML. Verifique el formato.");
                        }

                        if (!isset($xml->libro)) {
                            throw new \Exception("No se encontraron elementos '<libro>' en el XML. Verifique la estructura.");
                        }

                        foreach ($xml->libro as $libroData) {
                            try {
                                $isbn = (string)($libroData->isbn ?? '');

                                if (empty($isbn)) {
                                    $failedIsbns[] = 'Un libro sin ISBN en la fila ' . ($importedCount + 1);
                                    continue;
                                }

                                $libro = Libro::firstOrNew(['isbn' => $isbn]);

                                $libro->titulo = (string)($libroData->titulo ?? $libro->titulo);
                                $libro->autor = (string)($libroData->autor ?? $libro->autor);
                                $libro->anio_publicacion = (int)($libroData->anio_publicacion ?? $libro->anio_publicacion);
                                $libro->sinopsis = (string)($libroData->sinopsis ?? $libro->sinopsis);
                                $libro->stock = (int)($libroData->stock ?? 0);
                                $libro->status = (string)($libroData->status ?? 'activo');
                                $libro->precio_base = (float)($libroData->precio_base ?? 0.0);
                                $libro->stock_minimo = 5;

                                if ($libro->isDirty('precio_base') || !isset($libroData->precio_final)) { // Verifica si hay un cambio en el precio o si ha modificado, osea que si es diferente, entonces se le eagina otro valor 
                                    $tasaImpuesto = 0.07;
                                    $libro->precio_final = round($libro->precio_base * (1 + $tasaImpuesto), 2); // También se puede calcular como precio_base * 0.07 directamente
                                } else {
                                    $libro->precio_final = (float)($libroData->precio_final ?? 0.0); // En tal caso si se proprocione el precio final en el xml
                                }

                                if (isset($libroData->categoria_nombre)) {
                                    $categoryName = (string)$libroData->categoria_nombre;
                                    $category = Category::firstOrCreate(['nombre' => $categoryName]); // Lo que hace esto es ver si existe dicha categoría, en tal caso no sea así, entonces la crea, devuelve la instancia de esa categoría y se la asgina automáticamente
                                    $libro->category_id = $category->id; // Se guarda el id para relacionarlo a una categoría
                                } elseif (isset($libroData->category_id)) {
                                    $libro->category_id = (int)$libroData->category_id; // En tal caso exista, se le asiga la instancia de la categoría que se encontró anteriormente en el método de firstOrCreate
                                } else {
                                    $libro->category_id = null;
                                }

                                $libro->save();
                                $importedCount++;
                            } catch (\Exception $e) {
                                $failedIsbns[] = "ISBN: {$isbn} - Error: {$e->getMessage()}";
                            }
                        }

                        DB::commit();

                        $notificationTitle = 'Importación XML Completada';
                        $notificationBody = "Se han importado/actualizado {$importedCount} libros.";
                        if (!empty($failedIsbns)) {
                            $notificationBody .= " Se encontraron errores en " . count($failedIsbns) . " libros.";
                            Notification::make()
                                ->title('Importación XML con Advertencias')
                                ->body($notificationBody)
                                ->warning()
                                ->send();
                        } else {
                            Notification::make()
                                ->title($notificationTitle)
                                ->body($notificationBody)
                                ->success()
                                ->send();
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Notification::make()
                            ->title('Error Grave en Importación XML')
                            ->body('Ocurrió un error: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    } finally {
                        if (Storage::disk('local')->exists($filePath)) {
                            Storage::disk('local')->delete($filePath);
                        }
                    }
                })
                ->modalIcon('heroicon-o-arrow-up-on-square')
                ->modalWidth('xl'),
        ];
    }
}
