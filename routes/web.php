<?php

use Illuminate\Support\Facades\Route;
use App\Models\Libro;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\LibroController;
use Doctrine\DBAL\Schema\Index;

// Cambia la ruta raíz para que use el LibroController
Route::get('/', [LibroController::class, 'index'])->name('home'); 

Route::get('/libros/{libro}/pdf', function (Libro $libro) {
    $pdf = Pdf::loadView('pdfs.libro_detalle', compact('libro'));
    return $pdf->download('libro_' . date('Ymd_His') . '.pdf'); // O return $pdf->stream('libro_' . $libro->id . '.pdf'); para mostrarlo en el navegador
})->name('generate.libro.pdf');

Route::get('/reporte-general/pdf', function () {
    $libros = Libro::all(); // Asegúrate de que tienes los libros cargados
    $pdf = Pdf::loadView('pdfs.reporte_general', compact('libros'));
    return $pdf->download('reporte_general_' . date('Ymd_His') . '.pdf');
})->name('generate.libro.reporte_general.pdf');

Route::get('/reporte-stock-minimo/pdf', function () {
    $libros = Libro::where('stock', '<', 5)->get(); // Libros con stock bajo
    $pdf = Pdf::loadView('pdfs.reporte_stock_minimo', compact('libros'));
    return $pdf->download('reporte_stock_minimo_' . date('Ymd_His') . '.pdf');
})->name('generate.libro.reporte_stock_minimo.pdf');

Route::get('/library', [LibroController::class, 'index'])->name('libros.index');
// routes/web.php
Route::get('/libro/{id}', [LibroController::class, 'show'])->name('libros.show');

Route::get('/libros/search', [LibroController::class, 'search'])->name('libros.search');

