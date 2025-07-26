<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LibroController;
use App\Http\Controllers\Api\CategoryController;

Route::apiResource('libros', LibroController::class);
Route::apiResource('categories', CategoryController::class); 
