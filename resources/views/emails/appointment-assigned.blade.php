<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Cita Médica Asignada</title>
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
        .appointment-details {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #2563eb;
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
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            background-color: #dbeafe;
            color: #1d4ed8;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .action-section {
            background-color: #fef3c7;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #f59e0b;
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
        .btn-success {
            background-color: #059669;
        }
        .btn-danger {
            background-color: #dc2626;
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

        <h2 class="title">¡Hola {{ $appointment->patient->name }}!</h2>
        
        <p>Se te ha asignado una nueva cita médica. A continuación encontrarás todos los detalles:</p>

        <div class="appointment-details">
            <h3 style="margin-top: 0; color: #1f2937;">📋 Detalles de la Cita</h3>
            
            <div class="detail-row">
                <span class="detail-label">Doctor:</span>
                <span class="detail-value">Dr. {{ $appointment->doctor->name }} {{ $appointment->doctor->last_name }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Fecha:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($appointment->date)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Hora:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }} hrs</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Modalidad:</span>
                <span class="detail-value">{{ $appointment->modality }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Motivo:</span>
                <span class="detail-value">{{ $appointment->reason }}</span>
            </div>

            @if($appointment->additional_notes)
            <div class="detail-row">
                <span class="detail-label">Notas:</span>
                <span class="detail-value">{{ $appointment->additional_notes }}</span>
            </div>
            @endif
            
            <div class="detail-row">
                <span class="detail-label">Estado:</span>
                <span class="detail-value"><span class="status-badge">{{ ucfirst($appointment->status->name) }}</span></span>
            </div>
        </div>

        <div class="action-section">
            <h3 style="margin-top: 0; color: #92400e;">Acción Requerida</h3>
            <p><strong>Por favor confirma tu asistencia a esta cita médica.</strong></p>
            <p>Puedes aceptar o cancelar la cita accediendo a tu cuenta en DiabCare:</p>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="{{ config('app.url') }}/patient/appointments" class="btn">
                    Ver Mis Citas
                </a>
            </div>
        </div>



        <div class="footer">
            <p>Este correo fue enviado automáticamente por DiabCare.</p>
            <p>Si tienes alguna pregunta, contacta a tu doctor: {{ $appointment->doctor->email }}</p>
            <p>© {{ date('Y') }} DiabCare - Sistema de Gestión Médica</p>
        </div>
    </div>
</body>
</html>