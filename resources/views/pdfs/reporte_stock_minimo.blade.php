<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte General de Libros</title>
    <!-- Incluye Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Fuente Inter para una mejor legibilidad */
        body {
            font-family: 'Inter', sans-serif;
            /* Fondo gris claro */
        }

        /* Estilos para el contenedor del reporte */
        .report-container {
            max-width: 1000px;
            /* Ajustado para las columnas reducidas */
            margin: 2rem auto;
            background-color: #ffffff;
            border-radius: 1rem;
            /* Bordes redondeados */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            /* Sombra suave */
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Estilos para la tabla */
        .table-responsive {
            overflow-x: auto;
            /* Permite desplazamiento horizontal en pantallas pequeñas */
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
        }

        .report-table th,
        .report-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .report-table th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #4b5563;
            text-transform: uppercase;
            font-size: 0.875rem;
        }

        .report-table tbody tr:hover {
            background-color: #f3f4f6;
        }

        /* Estilos para el badge de estado */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            /* Píldora */
            font-size: 0.75rem;
            /* Texto más pequeño para tabla */
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-activo {
            background-color: #d1fae5;
            /* Verde claro */
            color: #065f46;
            /* Verde oscuro */
        }

        .status-inactivo {
            background-color: #fee2e2;
            /* Rojo claro */
            color: #991b1b;
            /* Rojo oscuro */
        }

        /* Estilos para el stock bajo */
        .stock-danger {
            color: #dc2626;
            /* Rojo */
            font-weight: 700;
            /* Bold */
        }

        .stock-warning {
            color: #f59e0b;
            /* Naranja */
            font-weight: 700;
        }

        .stock-success {
            color: #10b981;
            /* Verde */
            font-weight: 700;
        }

        /* La clase .book-image ya no es necesaria si no se muestra la imagen */
    </style>
</head>

<body class="bg-gray-100 p-6">
    <div class="report-container">
        <h1 class="text-4xl font-extrabold text-gray-900 text-center mb-6">Reporte de Libros con stock bajo</h1>
        <p class="text-center text-gray-600 mb-8">Un resumen de todos los libros con stock bajo en el sistema.</p>

        <div class="table-responsive">
            <table class="report-table rounded-lg overflow-hidden">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Estado</th>
                        <th>Stock / Mínimo</th>
                        <th>Fecha de Subida</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($libros as $libro)
                    <tr>
                        <td>{{ $libro->titulo }}</td>
                        <td>
                            <span class="status-badge status-{{ $libro->status }}">
                                {{ ucfirst($libro->status) }}
                            </span>
                        </td>
                        <td class="
                                @php
                                    $stock = (int) $libro->stock;
                                    $minStock = (int)($libro->stock_minimo ?? 5);
                                    if ($stock <= $minStock) echo 'stock-danger';
                                    elseif ($stock > $minStock && $stock <= $minStock * 2) echo 'stock-warning';
                                    else echo 'stock-success';
                                @endphp
                            ">
                            {{ $libro->stock }} / {{ $libro->stock_minimo ?? 'N/A' }}
                        </td>
                        <td>{{ $libro->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>