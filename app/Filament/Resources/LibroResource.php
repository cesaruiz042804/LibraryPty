<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LibroResource\Pages;
use App\Filament\Resources\LibroResource\RelationManagers;
use App\Models\Libro;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\FileUpload;
use App\Filament\Imports\LibroImporter; // ¡Importa tu nuevo importador!
use Filament\Tables\Actions\ImportAction; //
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Notifications\Notification; // Importa para las notificaciones
use Illuminate\Support\Facades\DB; // Para las transacciones de base de datos
use Illuminate\Support\Facades\Storage; // Para limpiar el archivo temporal

class LibroResource extends Resource
{
    protected static ?string $model = Libro::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('titulo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('autor')
                    ->required()
                    ->maxLength(255),
                TextInput::make('anio_publicacion')
                    ->label('Año de publicación')
                    ->numeric() // Asegura que solo se ingresen números
                    ->minValue(1500) // El año de publicación no puede ser menor a 1500
                    ->required(), // Hazlo obligatorio si el stock es un campo crucial
                Forms\Components\TextInput::make('isbn')
                    ->maxLength(255)
                    ->default(null)
                    ->unique(
                        ignoreRecord: true,
                        table: Libro::class,
                        column: 'isbn'
                    )
                    ->label('ISBN'),
                Forms\Components\TextInput::make('sinopsis')
                    ->maxLength(255)
                    ->default(null),
                Select::make('status')
                    ->options([
                        'activo' => 'Activo',
                        'inactivo' => 'Inactivo',
                    ])
                    ->default('activo') // Opcional: para que el valor por defecto se refleje en el form
                    ->required(),
                TextInput::make('stock')
                    ->label('Stock Disponible')
                    ->numeric() // Asegura que solo se ingresen números
                    ->minValue(0) // El stock no puede ser negativo
                    ->required() // Hazlo obligatorio si el stock es un campo crucial
                    ->default(0), // Valor por defecto en el formulario
                TextInput::make('stock_minimo')
                    ->label('Stock minimo')
                    ->numeric() // Asegura que solo se ingresen números
                    ->minValue(5), // Puedes poner 0 si quieres permitirlo, o 1 si no
                TextInput::make('precio_base')
                    ->label('Precio Base (sin Impuesto)')
                    ->numeric()
                    ->step(0.01) // Permite decimales
                    ->minValue(0) // El precio no puede ser negativo
                    ->required() // Hazlo obligatorio
                    ->live() // Esto hace que el campo se "escuche" para cambios en tiempo real.
                    // Cuando 'precio_base' cambia, el método afterStateUpdated se dispara.
                    ->afterStateUpdated(function ($state, Forms\Set $set) { // Forms\Set para actualizar otros campos
                        // Obtener la tasa de impuesto de la configuración, por defecto 0.07 (7%)
                        $tasaImpuesto = 0.07;

                        // Calcular el precio final
                        $precioFinal = $state * (1 + $tasaImpuesto);

                        // Actualizar el campo 'precio_final' en el formulario
                        $set('precio_final', round($precioFinal, 2)); // Redondear a 2 decimales
                    }),

                TextInput::make('precio_final')
                    ->label('Precio Final (con Impuesto)')
                    ->numeric()
                    ->step(0.01)
                    ->readOnly() // Hace que el campo sea de solo lectura en el formulario
                    ->default(0) // Valor por defecto
                    ->dehydrated(true),
                Select::make('category_id')
                    ->relationship('category', 'nombre') // Asumiendo que tu modelo Libro tiene una relación 'category' con el modelo Category
                    ->nullable(),
                FileUpload::make('imagen') // El nombre de la columna en la BD
                    ->label('Imagen de Portada')
                    ->image() // Valida que sea una imagen
                    ->directory('libros_covers') // Directorio dentro de 'storage/app/public'
                    ->disk('public') // Usa el disco 'public' (requiere symlink)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('titulo')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('autor')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('anio_publicacion')
                    ->sortable()
                    ->sortable(),
                /*
                Tables\Columns\TextColumn::make('isbn')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('sinopsis')
                    ->sortable()
                    ->searchable(),
                    */
                Tables\Columns\TextColumn::make('status')
                    ->sortable(),

                TextColumn::make('stock') // Nombre descriptivo para la columna combinada, aquí lo que se va a hacer es sobreescribir el valor de stock para combinar los datos de stock y stock minimo
                    ->label('Stock / Mínimo') // Etiqueta para la tabla
                    ->badge()
                    ->color(function (Libro $record): string { // Pasa el $record completo al closure
                        $stock = (int) $record->stock;
                        $minStock = (int)($record->stock_minimo ?? 5); // Usa el stock_minimo del record, con default

                        if ($stock <= $minStock) {
                            return 'danger';
                        }
                        if ($stock > $minStock && $stock <= $minStock * 2) {
                            return 'warning';
                        }
                        return 'success';
                    })
                    ->tooltip(function (Libro $record): ?string { // Pasa el $record completo al closure
                        $stock = (int) $record->stock;
                        $minStock = (int)($record->stock_minimo ?? 5); // Usa el stock_minimo del record, con default
                        return $stock <= $minStock ? '¡Stock bajo!' : null;
                    })
                    ->formatStateUsing(function (string $state, Libro $record): string {
                        // Combina el stock actual y el stock mínimo
                        return "{$record->stock} / {$record->stock_minimo}";
                    })
                    ->sortable('stock'), // Para que la columna sea sortable por el campo 'stock'

                Tables\Columns\TextColumn::make('category.nombre') // Muestra el campo nombre de la categoría
                    ->label('Categoría')
                    ->sortable()
                    ->searchable()
                    ->default('No especificado'),
                Tables\Columns\TextColumn::make('precio_base')
                    ->sortable()
                    ->label('Precio Base')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('precio_final')
                    ->sortable()
                    ->label('Precio Final')
                    ->money('USD') // Opcional: formatea como moneda
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Nueva acción para generar PDF
                Action::make('generatePdf')
                    ->label('Generar PDF')
                    ->icon('heroicon-o-document-arrow-down') // Icono opcional
                    ->color('info') // Color opcional
                    ->url(fn(Libro $record): string => route('generate.libro.pdf', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getImporters(): array
    {
        return [
            LibroImporter::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLibros::route('/'),
            'create' => Pages\CreateLibro::route('/create'),
            'edit' => Pages\EditLibro::route('/{record}/edit'),
        ];
    }
}
