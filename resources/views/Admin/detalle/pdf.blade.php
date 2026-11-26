<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Detalle de Compra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 10mm; /* Márgenes reducidos */
            color: #333;
        }
        .header {
            display: flex;
            justify-content: flex-end; /* Logo a la derecha */
            margin-bottom: 10px;
        }
        .logo img {
            max-width: 200px; /* Logo grande */
            height: auto;
        }
        h1 {
            text-align: left; /* Título alineado a la izquierda */
            color: #1a73e8;
            font-size: 20px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px; /* Fuente compacta */
        }
        th, td {
            border: 1px solid #999; /* Bordes de cuadrícula */
            padding: 6px; /* Espaciado reducido */
            text-align: left;
        }
        th {
            background-color: #e0e0e0; /* Encabezado gris */
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f5f5f5; /* Filas alternadas */
        }
        .footer {
            text-align: left; /* Pie de página a la izquierda */
            margin-top: 15px;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('img/logo.png') }}" alt="Logo">
        </div>
    </div>
    <h1>Reporte de Detalle de Compra</h1>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Compra</th>
                <th>Proveedor</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($details as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->buy_id }}</td>
                    <td>{{ $detail->name }}</td>
                    <td>{{ $detail->cantidad }}</td>
                    <td>{{ number_format($detail->precio_unitario, 2) }}</td>
                    <td>{{ number_format($detail->subtotal, 2) }}</td>
                    <td>{{ $detail->fecha->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }} | Sistema de control de Compras para la Casa Colonial
    </div>
</body>
</html>
