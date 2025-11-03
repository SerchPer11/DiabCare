<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Respuesta en el Foro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .title {
            color: #1f2937;
            margin-bottom: 20px;
        }
        .forum-details {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #10b981;
        }
        .original-post {
            background-color: #fefefe;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border: 1px solid #e5e7eb;
        }
        .answer-section {
            background-color: #ecfdf5;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #10b981;
        }
        .detail-row {
            margin-bottom: 10px;
            display: flex;
        }
        .detail-label {
            font-weight: bold;
            color: #374151;
            min-width: 120px;
        }
        .detail-value {
            color: #1f2937;
        }
        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            background-color: #dbeafe;
            color: #1d4ed8;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .author-info {
            background-color: #f1f5f9;
            padding: 10px;
            border-radius: 6px;
            margin: 10px 0;
            font-size: 14px;
        }
        .answer-content {
            background-color: white;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border: 1px solid #d1d5db;
            font-style: italic;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 10px 5px 0 0;
        }
        .btn-forum {
            background-color: #10b981;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">DiabCare</div>
            <p>Sistema de Gestión Médica para Diabetes</p>
        </div>

        <h2 class="title">¡Hola {{ $forumAnswer->forum->user->name }}!</h2>
        
        <p>¡Buenas noticias! Tu pregunta en el foro ha recibido una nueva respuesta de la comunidad DiabCare.</p>

        <div class="forum-details">
            <h3 style="margin-top: 0; color: #1f2937;">Detalles de tu Pregunta</h3>
            
            <div class="detail-row">
                <span class="detail-label">Título:</span>
                <span class="detail-value"><strong>{{ $forumAnswer->forum->title }}</strong></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Categoría:</span>
                <span class="detail-value"><span class="category-badge">{{ $forumAnswer->forum->category->name }}</span></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Fecha publicada:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($forumAnswer->forum->created_at)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY [a las] HH:mm') }}</span>
            </div>

            <div class="original-post">
                <strong>Tu pregunta original:</strong>
                <p style="margin: 10px 0 0 0; color: #4b5563;">"{{ Str::limit($forumAnswer->forum->content, 200) }}"</p>
            </div>
        </div>

        <div class="answer-section">
            <h3 style="margin-top: 0; color: #065f46;">Nueva Respuesta Recibida</h3>
            
            <div class="author-info">
                <strong>Respondido por:</strong> {{ $forumAnswer->user->name }} {{ $forumAnswer->user->last_name }}
                <br>
                <strong>Fecha de respuesta:</strong> {{ \Carbon\Carbon::parse($forumAnswer->created_at)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY [a las] HH:mm') }}
                @if($forumAnswer->user->hasRole('doctor'))
                    <br><strong>Tipo de usuario:</strong> <span style="color: #059669; font-weight: bold;">Doctor</span>
                @elseif($forumAnswer->user->hasRole('patient'))
                    <br><strong>Tipo de usuario:</strong> <span style="color: #2563eb; font-weight: bold;">Paciente</span>
                @endif
            </div>

            <div class="answer-content">
                <strong>Respuesta:</strong>
                <p style="margin: 10px 0 0 0;">{{ $forumAnswer->answer }}</p>
            </div>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <p><strong>¿Quieres ver la respuesta completa?</strong></p>
            <a href="{{ config('app.url') }}/forum/{{ $forumAnswer->forum->id }}" class="btn btn-forum">
                Ver en el Foro
            </a>
        </div>

        <div class="footer">
            <p>Este correo fue enviado automáticamente por DiabCare.</p>
            <p><strong>Mantente conectado con nuestra comunidad</strong> - Juntos cuidamos mejor tu diabetes</p>
            <p>© {{ date('Y') }} DiabCare - Sistema de Gestión Médica</p>
        </div>
    </div>
</body>
</html>