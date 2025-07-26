<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Libro: {{ $libro->titulo }}</title>
    <!-- Incluye Tailwind CSS (para visualización en navegador, DomPDF no lo procesa directamente) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Fuente Inter para una mejor legibilidad */
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Estilos para el contenedor del reporte */
        .report-container {
            max-width: 800px;
            margin: 2rem auto;
            background-color: #ffffff;
            border-radius: 1rem; /* Bordes redondeados */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Sombra suave */
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        /* Estilos para la imagen de portada */
        .cover-image {
            width: 100%;
            max-width: 250px;
            height: auto;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            object-fit: cover;
            margin-bottom: 1.5rem;
        }
        /* Estilos para los detalles del libro */
        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e5e7eb; /* Línea divisoria suave */
        }
        .detail-item:last-child {
            border-bottom: none; /* Sin línea en el último elemento */
        }
        .detail-label {
            font-weight: 600; /* Semibold */
            color: #4b5563; /* Gris oscuro */
            flex-shrink: 0; /* Evita que la etiqueta se encoja */
            margin-right: 1rem;
        }
        .detail-value {
            color: #1f2937; /* Gris muy oscuro */
            text-align: right;
            flex-grow: 1; /* Permite que el valor ocupe el espacio restante */
        }
        /* Estilos para el badge de estado */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px; /* Píldora */
            font-size: 0.875rem; /* Texto pequeño */
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-activo {
            background-color: #d1fae5; /* Verde claro */
            color: #065f46; /* Verde oscuro */
        }
        .status-inactivo {
            background-color: #fee2e2; /* Rojo claro */
            color: #991b1b; /* Rojo oscuro */
        }
        /* Estilos para el stock bajo */
        .stock-danger {
            color: #dc2626; /* Rojo */
            font-weight: 700; /* Bold */
        }
        .stock-warning {
            color: #f59e0b; /* Naranja */
            font-weight: 700;
        }
        .stock-success {
            color: #10b981; /* Verde */
            font-weight: 700;
        }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <div class="report-container">
        <h1 class="text-4xl font-extrabold text-gray-900 text-center mb-6">Reporte del Libro</h1>
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-8">{{ $libro->titulo }}</h2>

        <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
            <!-- Sección de Imagen -->
            <div class="md:w-1/3 flex justify-center">
                @if ($libro->imagen)
                    <img src="{{ asset('storage/' . $libro->imagen) }}" alt="Portada de {{ $libro->titulo }}" class="cover-image">
                @else
                    <img src="https://placehold.co/250x350/E0E0E0/6C757D?text=Sin+Imagen" alt="Sin imagen de portada" class="cover-image">
                @endif
            </div>

            <!-- Sección de Detalles del Libro -->
            <div class="md:w-2/3 w-full">
                <div class="detail-item">
                    <span class="detail-label">Autor:</span>
                    <span class="detail-value">{{ $libro->autor }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Año de Publicación:</span>
                    <span class="detail-value">{{ $libro->anio_publicacion ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">ISBN:</span>
                    <span class="detail-value">{{ $libro->isbn ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Categoría:</span>
                    <span class="detail-value">{{ $libro->category->nombre ?? 'Sin Categoría' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Estado:</span>
                    <span class="detail-value">
                        <span class="status-badge status-{{ $libro->status }}">
                            {{ ucfirst($libro->status) }}
                        </span>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Stock Disponible:</span>
                    <span class="detail-value
                        @php
                            $stock = (int) $libro->stock;
                            $minStock = (int)($libro->stock_minimo ?? 5);
                            if ($stock <= $minStock) echo 'stock-danger';
                            elseif ($stock > $minStock && $stock <= $minStock * 2) echo 'stock-warning';
                            else echo 'stock-success';
                        @endphp
                    ">
                        {{ $libro->stock }}
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Stock Mínimo:</span>
                    <span class="detail-value">{{ $libro->stock_minimo ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Precio Base:</span>
                    <span class="detail-value">${{ number_format($libro->precio_base, 2) }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Precio Final (con Impuesto):</span>
                    <span class="detail-value">${{ number_format($libro->precio_final, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Sección de Sinopsis (si existe) -->
        @if ($libro->sinopsis)
            <div class="mt-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Sinopsis</h3>
                <p class="text-gray-700 leading-relaxed">{{ $libro->sinopsis }}</p>
            </div>
        @endif
    </div>
</body>
</html>
