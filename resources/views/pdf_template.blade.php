<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <!-- (Puedes añadir estilos CSS aquí en una etiqueta <style>) -->
    <style>
        body { font-family: sans-serif; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; text-align: left; }
        h1 { color: #333; }
        h2 { border-bottom: 2px solid #eee; padding-bottom: 5px; }
        h1 text-medic-500 { color: #144594; }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
.header {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
            <div class="logo">DiabCare</div>
            <p>Sistema de Gestión Médica para Diabetes</p>
            <p style="font-size: 0.9em; color: #555;">
    Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y h:i A') }}
</p>
        </div>  
    <h2>Reporte de {{ $title }}</h2> 

    @if(!empty($filters))
        <h2>Filtros aplicados</h2>
        <ul>
            @foreach($filters as $filter)
                <li><strong>{{ $filter['label'] }}:</strong> {{ $filter['value'] ?? 'No Aplica' }}</li>
            @endforeach
        </ul>
    @endif
    
    @if($stats ?? false)
        <h2>Resumen</h2>
        <table style="margin-bottom: 20px;">
            <tr>
                @foreach($stats as $stat)
                    <td style="text-align: center;">
                        <strong style="font-size: 1.5em;">{{ $stat['value'] }}</strong>
                        <br>
                        <span>{{ $stat['label'] }}</span>
                    </td>
                @endforeach
            </tr>
        </table>
    @endif

    @if($chartImage)
        <h2>Gráfica</h2>
        <div style="text-align: center;">
            <img src="{{ $chartImage }}" style="width: 90%; margin: auto;">
        </div>
    @endif

    <h2>Datos</h2>
    <table>
        <thead>
            <tr>
                @foreach($tableData['headers'] as $header)
                    <th>{{ $header['label'] }}</th> 
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($tableData['rows'] as $row)
                <tr>
                    @foreach($tableData['headers'] as $header)
                        <td>{{ $row[$header['key']] }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
