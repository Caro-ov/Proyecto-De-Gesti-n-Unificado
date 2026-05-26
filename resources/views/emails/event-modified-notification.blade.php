<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f5f5f5; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .content { padding: 20px 0; }
        .changes { background-color: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #ffc107; }
        .change-item { margin: 10px 0; }
        .details { background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .details li { margin: 8px 0; }
        .button { display: inline-block; background-color: #3490dc; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; color: #999; font-size: 12px; margin-top: 40px; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Cambios en el Evento</h1>
        </div>

        <div class="content">
            <p>Hola,</p>

            <p>Informamos que el evento <strong>{{ $event->name }}</strong> ha sido actualizado.</p>

            @if (!empty($changedFields))
            <div class="changes">
                <h3>Cambios realizados:</h3>
                @foreach ($changedFields as $field => $changes)
                <div class="change-item">
                    <strong>{{ ucfirst($field) }}:</strong>
                    <br>
                    Anterior: <code>{{ $changes['old'] ?? 'N/A' }}</code>
                    <br>
                    Nuevo: <code>{{ $changes['new'] ?? 'N/A' }}</code>
                </div>
                @endforeach
            </div>
            @else
            <p style="color: #666;">Se realizaron actualizaciones en el evento.</p>
            @endif

            <h3>Detalles del evento actualizado:</h3>

            <div class="details">
                <ul>
                    @if ($event->date)
                    <li><strong>Fecha:</strong> {{ $event->date->format('d/m/Y') }}</li>
                    @endif

                    @if ($event->time)
                    <li><strong>Hora:</strong> {{ $event->time->format('H:i') }}</li>
                    @endif

                    @if ($event->location)
                    <li><strong>Ubicación:</strong> {{ $event->location }}</li>
                    @endif
                </ul>
            </div>

            <center>
                <a href="{{ route('portal.events.show', $event) }}" class="button">Ver Evento Actualizado</a>
            </center>

            <p>Cualquier duda, no dudes en contactarnos.</p>
        </div>

        <div class="footer">
            <p>{{ config('app.name') }} - Sistema de Gestión de Eventos</p>
        </div>
    </div>
</body>
</html>
