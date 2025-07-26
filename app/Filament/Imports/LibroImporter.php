<?php

namespace App\Filament\Imports;

use App\Models\Libro;
use App\Models\Category;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;


class LibroImporter extends Importer
{
    protected static ?string $model = Libro::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('titulo')
                ->label('Título'),

            ImportColumn::make('autor')
                ->label('Autor'),
            ImportColumn::make('sinopsis')
                ->label('Sinopsis'),
            ImportColumn::make('status')
                ->label('Estado'),
            ImportColumn::make('anio_publicacion')
                ->label('Año de Publicación'),
            ImportColumn::make('stock')
                ->label('Stock'),
            ImportColumn::make('precio_base')
                ->label('Precio base'),
            ImportColumn::make('precio_final')
                ->label('Precio final')
                ->fillRecordUsing(function (Libro $record, $state) {
                    // Si el precio final no se proporciona, calcularlo como 7% más que el precio base
                    if (is_null($state) || $state === '') {
                        $record->precio_final = round($record->precio_base * 1.07, 2);
                    } else {
                        $record->precio_final = floatval($state);
                    }
                }),
            ImportColumn::make('isbn')
                ->label('ISBN'),
            ImportColumn::make('category.nombre') // El nombre de la columna en CSV que contiene el NOMBRE de la categoría
                ->label('Categoría'),
            ImportColumn::make('imagen')
                ->label('URL de imagen')
                ->fillRecordUsing(function (Libro $record, $state) {
                    // $record es la instancia del modelo Libro que se está creando/actualizando
                    // $state es el valor de la columna 'imagen_url' del archivo CSV (la URL de la imagen)

                    $imagenUrl = $state; // Renombramos para mayor claridad

                    // 1. Validar que $imagenUrl sea una URL válida
                    if ($imagenUrl && filter_var($imagenUrl, FILTER_VALIDATE_URL)) {
                        try {
                            // 2. Descargar la imagen
                            $response = Http::get($imagenUrl);

                            if ($response->successful()) {
                                // 3. Generar un nombre de archivo único y obtener la extensión
                                $extension = pathinfo(parse_url($imagenUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                                $fileName = Str::random(40) . '.' . $extension; // Usamos 40 para más unicidad
                                $path = "libros_covers/{$fileName}"; // Define tu carpeta de almacenamiento

                                // 4. Guardar la imagen en el disco 'public'
                                Storage::disk('public')->put($path, $response->body());

                                // 5. Asignar la ruta de la imagen al atributo 'imagen' del modelo
                                $record->imagen = $path; // Asume que tienes un campo 'imagen' en tu tabla 'libros'
                            } else {
                                // Opcional: Manejar el caso donde la descarga no fue exitosa (ej. loguear error)
                                // Log::warning("No se pudo descargar la imagen de la URL: {$imagenUrl}");
                            }
                        } catch (\Exception $e) {
                            // Opcional: Capturar y manejar excepciones durante la descarga (ej. URL inaccesible)
                            // Log::error("Error al descargar imagen {$imagenUrl}: " . $e->getMessage());
                        }
                    }
                }),
        ];
    }

    public function resolveRecord(): ?Libro
    {

        $categoriaNombre = trim($this->data['nombre_categoria']) ?? null;

        $categoriaId = null;
        if ($categoriaNombre) {
            $categoria = Category::firstOrCreate([
                'nombre' => $categoriaNombre,
            ]);
            $categoriaId = $categoria->id;
        }

        $isbn = trim($this->data['isbn']) ?? null;
        $titulo = trim($this->data['titulo']) ?? null;

        $libro = null;

        // Primero busca por ISBN si existe 
        if ($isbn) {
            $libro = Libro::where('isbn', $isbn)->first();
        }

        // Si no se encontró por ISBN, busca por título
        if (!$libro && $titulo) {
            $libro = Libro::where('titulo', $titulo)->first();
        }

        // Si aún no se encuentra, se crea uno nuevo
        if (!$libro) {
            $libro = new Libro();
            $libro->isbn = $isbn;
            $libro->titulo = $titulo;
        }

        // Asigna la categoría
        $libro->category_id = $categoriaId;

        if (!isset($this->data['precio_final']) || $this->data['precio_final'] === null || $this->data['precio_final'] === '') {
            $precioBase = floatval($this->data['precio_base'] ?? 0);
            $libro->precio_base = $precioBase;
            $libro->precio_final = round($precioBase * 1.07, 2); // aplica ITBMS del 7%
        } else {
            $libro->precio_base = floatval($this->data['precio_base']);
            $libro->precio_final = floatval($this->data['precio_final']);
        }

        return $libro;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'La importación de libros ha finalizado con ' . number_format($import->successful_rows) . ' ' . str('fila')->plural($import->successful_rows) . ' importadas correctamente.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' no se pudieron importar.';
        }

        return $body;
    }
}
