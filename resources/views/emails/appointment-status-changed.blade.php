<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio de Estado de Cita</title>
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
        .status-change {
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .status-accepted {
            background-color: #d1fae5;
            border-left: 4px solid #059669;
        }
        .status-cancelled {
            background-color: #fee2e2;
            border-left: 4px solid #dc2626;
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
        .status-badge-accepted {
            display: inline-block;
            padding: 8px 16px;
            background-color: #059669;
            color: white;
            border-radius: 20px;
            font-size: 16px;
            font-weight: 600;
        }
        .status-badge-cancelled {
            display: inline-block;
            padding: 8px 16px;
            background-color: #dc2626;
            color: white;
            border-radius: 20px;
            font-size: 16px;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .patient-info {
            background-color: #fef3c7;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #f59e0b;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">DiabCare</div>
            <p>Sistema de Gestión Médica para Diabetes</p>
        </div>

        <h2 class="title">¡Hola {{ $appointment->doctor->name }}!</h2>
        
        @if($newStatus === 'aceptada')
            <div class="status-change status-accepted">
                <h3 style="margin-top: 0; color: #065f46;">Cita Aceptada</h3>
                <p>El paciente <strong>{{ $appointment->patient->name }} {{ $appointment->patient->last_name }}</strong> ha aceptado su cita médica.</p>
                <span class="status-badge-accepted">{{ ucfirst($newStatus) }}</span>
            </div>
        @else
            <div class="status-change status-cancelled">
                <h3 style="margin-top: 0; color: #991b1b;">Cita Cancelada</h3>
                <p>El paciente <strong>{{ $appointment->patient->name }} {{ $appointment->patient->last_name }}</strong> ha cancelado su cita médica.</p>
                <span class="status-badge-cancelled">{{ ucfirst($newStatus) }}</span>
            </div>
        @endif

        <div class="patient-info">
            <h3 style="margin-top: 0; color: #92400e;">Información del Paciente</h3>
            <div class="detail-row">
                <span class="detail-label">Nombre:</span>
                <span class="detail-value">{{ $appointment->patient->name }} {{ $appointment->patient->last_name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ $appointment->patient->email }}</span>
            </div>
        </div>

        <div class="appointment-details">
            <h3 style="margin-top: 0; color: #1f2937;">Detalles de la Cita</h3>
            
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
        </div>

        @if($newStatus === 'aceptada')
            <div style="background-color: #e0f2fe; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #0288d1;">
                <h3 style="margin-top: 0; color: #01579b;">Próximos Pasos</h3>
                <p>La cita está confirmada y lista para realizarse en la fecha programada.</p>
                @if($appointment->modality === 'Virtual')
                    <p><strong>Recordatorio:</strong> Proporciona el enlace de videollamada al paciente antes de la consulta.</p>
                @else
                    <p><strong>Recordatorio:</strong> Asegúrate de tener disponible el consultorio en la hora programada.</p>
                @endif
            </div>
        @else
            <div style="background-color: #fef2f2; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ef4444;">
                <h3 style="margin-top: 0; color: #991b1b;">Cita Cancelada</h3>
                <p>Este espacio de tiempo ahora está disponible para otras citas.</p>
                <p>Si es necesario, puedes contactar al paciente para reagendar la consulta.</p>
            </div>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.url') }}/doctor/appointments" class="btn">
                Ver Mis Citas
            </a>
        </div>

        <div class="footer">
            <p>Este correo fue enviado automáticamente por DiabCare.</p>
            <p>Para contactar al paciente: {{ $appointment->patient->email }}</p>
            <p>© {{ date('Y') }} DiabCare - Sistema de Gestión Médica</p>
        </div>
    </div>
</body>
</html>