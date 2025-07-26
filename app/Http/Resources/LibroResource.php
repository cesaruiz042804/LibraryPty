<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class LibroResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'autor' => $this->autor,
            'sinopsis' => $this->sinopsis,
            //'imagen' => $this->imagen,
            //'imagen_url' => $this->imagen ? Storage::url($this->imagen) : null,
            'imagen_secure_simple' => $this->generateSimpleSecureUrl($this->imagen), // Versión simple
            'category' => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
  

    /**
     * Versión simple que siempre funciona
     */
    private function generateSimpleSecureUrl($path)
    {
        if (empty($path)) {
            return null;
        }

        // Siempre generar la URL, sin verificar si existe
        $url = Storage::url($path);
        $separator = strpos($url, '?') !== false ? '&' : '?';
        
        return $url . $separator . 'v=' . time();
    }

    /**
     * Método para debug - información sobre la imagen
     */
  
} 