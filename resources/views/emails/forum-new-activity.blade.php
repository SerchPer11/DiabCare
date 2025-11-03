<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Actividad en Conversación del Foro</title>
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
            border-left: 4px solid #8b5cf6;
        }
        .activity-section {
            background-color: #faf5ff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #8b5cf6;
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
        .participation-badge {
            display: inline-block;
            padding: 4px 12px;
            background-color: #f3e8ff;
            color: #7c3aed;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .answer-preview {
            background-color: white;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border: 1px solid #d1d5db;
            font-style: italic;
        }
        .author-highlight {
            background-color: #f1f5f9;
            padding: 10px;
            border-radius: 6px;
            margin: 10px 0;
            font-size: 14px;
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
            background-color: #8b5cf6;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .stats-row {
            display: flex;
            justify-content: space-between;
            background-color: #f9fafb;
            padding: 10px;
            border-radius: 6px;
            margin: 10px 0;
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

        <h2 class="title">¡Hola!</h2>
        
        <p>Hay nueva actividad en una conversación del foro en la que has participado. ¡No te pierdas la discusión!</p>

        <div class="forum-details">
            <h3 style="margin-top: 0; color: #1f2937;">Conversación Activa</h3>
            
            <div class="detail-row">
                <span class="detail-label">Título:</span>
                <span class="detail-value"><strong>{{ $forumAnswer->forum->title }}</strong></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Categoría:</span>
                <span class="detail-value"><span class="category-badge">{{ $forumAnswer->forum->category->name }}</span></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Autor original:</span>
                <span class="detail-value">{{ $forumAnswer->forum->user->name }}</span>
            </div>

            <div class="stats-row">
                <span><strong>Total respuestas:</strong> {{ $forumAnswer->forum->answers->count() }}</span>
                <span><strong>Tu participación:</strong> <span class="participation-badge">Activo</span></span>
            </div>
        </div>

        <div class="activity-section">
            <h3 style="margin-top: 0; color: #6b21a8;">Nueva Respuesta</h3>
            
            <div class="author-highlight">
                <strong>Respondido por:</strong> {{ $forumAnswer->user->name }} {{ $forumAnswer->user->last_name }}
                @if($forumAnswer->user->hasRole('doctor'))
                    <span style="color: #059669; font-weight: bold;">(Doctor)</span>
                @elseif($forumAnswer->user->hasRole('patient'))
                    <span style="color: #2563eb; font-weight: bold;">(Paciente)</span>
                @endif
                <br>
                <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($forumAnswer->created_at)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY [a las] HH:mm') }}
            </div>

            <div class="answer-preview">
                <strong>Vista previa de la respuesta:</strong>
                <p style="margin: 10px 0 0 0;">{{ Str::limit($forumAnswer->answer, 150) }}</p>
                @if(strlen($forumAnswer->answer) > 150)
                    <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">... <em>Ver respuesta completa en el foro</em></p>
                @endif
            </div>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <p><strong>¡Continúa participando en la conversación!</strong></p>
            <a href="{{ config('app.url') }}/forum/{{ $forumAnswer->forum->id }}" class="btn btn-forum">
                Ver Conversación Completa
            </a>
        </div>

        <div style="background-color: #ecfdf5; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10b981;">
            <h4 style="margin-top: 0; color: #065f46;">¿Por qué recibes este correo?</h4>
            <p style="margin: 10px 0; color: #047857;">
                Recibes esta notificación porque has participado anteriormente en esta conversación del foro. 
                Esto te permite mantenerte al día con las respuestas y continuar contribuyendo a la discusión.
            </p>
            <p style="margin: 10px 0; color: #047857; font-size: 14px;">
                <strong>Tip:</strong> Tu experiencia y conocimientos pueden ser valiosos para otros miembros de la comunidad.
            </p>
        </div>

        <div class="footer">
            <p>Este correo fue enviado automáticamente por DiabCare.</p>
            <p><strong>Comunidad DiabCare</strong> - Juntos construimos conocimiento sobre diabetes</p>
            <p>© {{ date('Y') }} DiabCare - Sistema de Gestión Médica</p>
            <hr style="margin: 15px 0; border: none; border-top: 1px solid #e5e7eb;">
            <p style="font-size: 12px; color: #9ca3af;">
                Si no deseas recibir notificaciones del foro, puedes ajustar tus preferencias en tu perfil.
            </p>
        </div>
    </div>
</body>
</html>