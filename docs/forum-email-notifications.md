# Notificaciones por Correo del Foro - DiabCare

Este documento describe el sistema de notificaciones por correo electrónico implementado para las respuestas del foro.

## Descripción General

Cuando un usuario responde a una pregunta en el foro, el sistema envía automáticamente dos tipos de correos electrónicos:

1. **ForumAnswerReceived**: Al autor original de la pregunta
2. **ForumNewActivity**: A otros usuarios que han participado en la conversación

## Clases de Correo Creadas

### 1. ForumAnswerReceived
- **Archivo**: `app/Mail/ForumAnswerReceived.php`
- **Vista**: `resources/views/emails/forum-answer-received.blade.php`
- **Destinatario**: Autor original de la pregunta del foro
- **Propósito**: Notificar que su pregunta ha recibido una nueva respuesta

### 2. ForumNewActivity
- **Archivo**: `app/Mail/ForumNewActivity.php`
- **Vista**: `resources/views/emails/forum-new-activity.blade.php`
- **Destinatario**: Usuarios que han participado previamente en la conversación
- **Propósito**: Mantener informados a los participantes sobre nueva actividad

## Implementación

### Controlador Modificado
El controlador `ForumAnswerController` ha sido modificado para incluir la lógica de envío de correos:

```php
// Al crear una nueva respuesta, se ejecuta automáticamente:
$this->sendForumNotifications($newAnswer);
```

### Lógica de Notificaciones

1. **Notificación al Autor Original**:
   - Se verifica que el autor original no sea quien responde
   - Se envía correo específico con detalles de la nueva respuesta

2. **Notificación a Participantes**:
   - Se obtienen todos los usuarios que han respondido anteriormente
   - Se excluye al autor de la nueva respuesta y al autor original
   - Se envía correo de actividad a cada participante único

### Logging
Se incluye logging detallado para monitorear el envío de correos:
- Éxito: Registra cuántos participantes fueron notificados
- Error: Registra errores sin interrumpir el flujo principal

## Características de los Correos

### Diseño
- Diseño consistente con otros correos del sistema
- Responsive y compatible con clientes de correo
- Colores y tipografía de la marca DiabCare

### Contenido Dinámico
- **ForumAnswerReceived**:
  - Detalles de la pregunta original
  - Información del usuario que responde (con rol)
  - Respuesta completa
  - Enlace directo al foro

- **ForumNewActivity**:
  - Estadísticas de la conversación
  - Vista previa de la nueva respuesta
  - Información de participación del usuario
  - Enlace para continuar la conversación

### Elementos Incluidos
- Emojis para mejorar la experiencia visual
- Badges de categorías y roles de usuario
- Información de fechas en español
- Enlaces de acción prominentes
- Consejos y guías de uso del foro

## Configuración

### Variables de Entorno Requeridas
Asegúrate de tener configurado en `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-mail-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@diabcare.com
MAIL_FROM_NAME="DiabCare"
```

### Colas de Trabajo (Recomendado)
Los correos implementan `ShouldQueue` para envío asíncrono:
```bash
# Ejecutar worker para procesar correos en segundo plano
php artisan queue:work
```

## Pruebas

### Prueba Manual
1. Crear una pregunta en el foro con un usuario
2. Responder con otro usuario
3. Verificar que se envían los correos apropiados
4. Agregar más respuestas y verificar notificaciones a participantes

### Comando de Prueba
Para probar los correos sin crear respuestas reales:
```bash
php artisan forum:test-email tu-email@ejemplo.com
```

### Prueba con Tinker
```php
// Crear respuesta de prueba
php artisan tinker
$forum = App\Models\Forum\Forum::first();
$answer = new App\Models\Forum\ForumAnswer();
$answer->forum_id = $forum->id;
$answer->user_id = 2; // ID de usuario diferente al autor
$answer->answer = "Esta es una respuesta de prueba";
$answer->save();

// Los correos se enviarán automáticamente
```

## Consideraciones de Seguridad

- Los correos solo se envían a usuarios registrados en el sistema
- Se verifica que el usuario tenga permisos para participar en el foro
- Los enlaces incluyen la URL base configurada en `config/app.url`
- No se expone información sensible en los correos

## Personalización

### Modificar Plantillas
Las plantillas se pueden personalizar editando:
- `resources/views/emails/forum-answer-received.blade.php`
- `resources/views/emails/forum-new-activity.blade.php`

### Agregar Más Tipos de Notificaciones
Para agregar nuevos tipos de notificaciones del foro:
1. Crear nueva clase Mailable
2. Crear vista correspondiente
3. Modificar `sendForumNotifications()` en el controlador

## Comandos Disponibles

### Prueba de Correos
```bash
php artisan forum:test-email correo@ejemplo.com
```
Este comando envía un correo de prueba usando datos reales del foro para verificar que las plantillas y configuración funcionan correctamente.

## Logs y Monitoreo

Los logs se guardan en `storage/logs/laravel.log`:
```
[timestamp] local.INFO: Notificaciones del foro enviadas correctamente {"forum_id":1,"answer_id":5,"original_author_notified":true,"participants_notified":2}
```

## Troubleshooting

### Correos No Se Envían
1. Verificar configuración de correo en `.env`
2. Revisar logs en `storage/logs/laravel.log`
3. Verificar que el worker de colas esté ejecutándose
4. Comprobar que los usuarios tengan emails válidos

### Performance
- Los correos se procesan en colas para no afectar la respuesta
- Se optimizan las consultas para evitar N+1 queries
- Se controla el número máximo de participantes notificados