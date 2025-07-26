<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('image_url')) {
    /**
     * Genera una URL hasheada para una imagen
     *
     * @param string|null $path
     * @param string $default
     * @return string
     */
    function image_url($path, $default = 'assets/img/product/post-card1-4.png')
    {
        if ($path && Storage::exists($path)) {
            return Storage::url($path);
        }
        
        return asset($default);
    }
}

if (!function_exists('secure_image_url')) {
    /**
     * Genera una URL hasheada con parámetros adicionales de seguridad
     *
     * @param string|null $path
     * @param string $default
     * @return string
     */
    function secure_image_url($path, $default = 'assets/img/product/post-card1-4.png')
    {
        if ($path && Storage::exists($path)) {
            $url = Storage::url($path);
            
            // Agregar timestamp para evitar cache
            $url .= (strpos($url, '?') !== false ? '&' : '?') . 'v=' . time();
            
            return $url;
        }
        
        return asset($default);
    }
} 