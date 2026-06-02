<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta {{ $activo->num_inventario }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 32px 16px;
        }

        /* ===== La etiqueta ===== */
        .etiqueta {
            background: #fff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            width: {{ $tipo === 'termica' ? '58mm' : '90mm' }};
            padding: {{ $tipo === 'termica' ? '3mm' : '5mm' }};
            text-align: center;
        }
        .etiqueta .hotel {
            font-size: {{ $tipo === 'termica' ? '9pt' : '11pt' }};
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }
        .etiqueta .num-inv {
            font-size: {{ $tipo === 'termica' ? '14pt' : '18pt' }};
            font-weight: 800;
            letter-spacing: 1px;
        }
        .etiqueta .barcode { margin: 2mm 0; }
        .etiqueta .barcode svg { width: 100%; height: auto; }
        .etiqueta .meta {
            font-size: {{ $tipo === 'termica' ? '7pt' : '9pt' }};
            line-height: 1.5;
            text-align: left;
            border-top: 1px solid #e2e8f0;
            padding-top: 2mm;
            margin-top: 1mm;
        }
        .etiqueta .meta div { display: flex; justify-content: space-between; gap: 6px; }
        .etiqueta .meta .k { color: #64748b; }
        .etiqueta .meta .v { font-weight: 600; text-align: right; }

        /* ===== Controles (no se imprimen) ===== */
        .toolbar {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
        }
        .toolbar a, .toolbar button {
            font: inherit;
            font-size: 13px;
            font-weight: 600;
            padding: 9px 16px;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #334155;
            cursor: pointer;
            text-decoration: none;
        }
        .toolbar button.primary { background: #4f46e5; border-color: #4f46e5; color: #fff; }
        .hint { color: #64748b; font-size: 12px; margin-top: 16px; }

        @media print {
            body { background: #fff; padding: 0; }
            .toolbar, .hint { display: none !important; }
            .etiqueta { border: none; border-radius: 0; }
            @page { margin: 4mm; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="primary" onclick="window.print()">🖨️ Imprimir</button>
        <a href="{{ route('etiquetas.imprimir', $activo) }}?tipo=termica">Térmica</a>
        <a href="{{ route('etiquetas.imprimir', $activo) }}?tipo=estandar">Estándar</a>
        <a href="{{ route('activos.show', $activo) }}">← Volver</a>
    </div>

    <div class="etiqueta">
        <div class="hotel">{{ $datos['hotel_codigo'] }} · {{ $datos['hotel'] }}</div>
        <div class="num-inv">{{ $datos['num_inventario'] }}</div>
        <div class="barcode">{!! $barcodeSvg !!}</div>
        <div class="meta">
            @if ($datos['nombre_equipo'])
                <div><span class="k">Equipo</span><span class="v">{{ $datos['nombre_equipo'] }}</span></div>
            @endif
            @if ($datos['num_serie'])
                <div><span class="k">Serie</span><span class="v">{{ $datos['num_serie'] }}</span></div>
            @endif
            <div><span class="k">Depto.</span><span class="v">{{ $datos['departamento'] }}</span></div>
            @if ($datos['codigo_interno_ti'])
                <div><span class="k">Cód. TI</span><span class="v">{{ $datos['codigo_interno_ti'] }}</span></div>
            @endif
        </div>
    </div>

    <p class="hint">Tipo de impresión: <strong>{{ ucfirst($tipo) }}</strong> · La generación quedó registrada en la bitácora.</p>
</body>
</html>
