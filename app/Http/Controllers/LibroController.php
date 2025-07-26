<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Libro;
use App\Models\Category;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use Detection\MobileDetect;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage; // Don't forget this!
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver; //

class LibroController extends Controller
{
    public function index()
    {
        $libros = Libro::all();
        $categorias = Category::all();
        $manager = new ImageManager(new Driver());
        foreach ($libros as $libro) {
            $rutaRelativa = $libro->imagen;
            $rutaImagen = storage_path('app/public/' . $rutaRelativa);

            if (file_exists($rutaImagen)) {
                try {
                    $image = $manager->read($rutaImagen); // Use read() instead of make()

                    $image->resize(420, 520, function ($constraint) { // Este es una forma para redimensionar imagenes ¿
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->save($rutaImagen);
                } catch (\Exception $e) {
                    // Handle image processing errors, e.g., log them
                }
            }
        }
        return view('library.index')->with(['libros' => $libros, 'categorias' => $categorias]);
    }

    public function show($id)
    {
        $libro = Libro::findOrFail($id); // Puedes usar find($id) si no quieres lanzar error 404
        $manager = new ImageManager(new Driver());
        $rutaRelativa = $libro->imagen;
        $rutaImagen = storage_path('app/public/' . $rutaRelativa);
        if (file_exists($rutaImagen)) {
            try {
                $image = $manager->read($rutaImagen); // Use read() instead of make()

                $image->resize(420, 420, function ($constraint) { // Este es una forma para redimensionar imagenes ¿
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($rutaImagen);
            } catch (\Exception $e) {
                // Handle image processing errors, e.g., log them
            }
        }

        return view('library.show', compact('libro'));
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $categoryId = $request->input('category');

        if ($categoryId) {
            $libros = Libro::where('category_id', $categoryId)
                ->where(function ($query) use ($searchTerm) {
                    $query->where('titulo', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('autor', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('sinopsis', 'LIKE', '%' . $searchTerm . '%');
                })->get();
        } else {
            // Si no se selecciona categoría, buscar solo por término de búsqueda
            $libros = Libro::where('titulo', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('autor', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('sinopsis', 'LIKE', '%' . $searchTerm . '%')
                ->get();
        }
        $categorias = Category::all();

        return view('library.search_results', compact('libros', 'searchTerm', 'categorias'));
    }
}
