<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f5f5f5; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .content { padding: 20px 0; }
        .qr-section { text-align: center; margin: 30px 0; }
        .qr-section img { max-width: 300px; height: auto; }
        .details { background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .details li { margin: 8px 0; }
        .button { display: inline-block; background-color: #3490dc; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; color: #999; font-size: 12px; margin-top: 40px; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Confirmación de Inscripción</h1>
        </div>

        <div class="content">
            <p>Hola <strong>{{ $registration->user->name }}</strong>,</p>

            <p>Confirmamos tu inscripción al evento:</p>

            <h2>{{ $registration->event->name }}</h2>

            <div class="details">
                <ul>
                    @if ($registration->event->date)
                    <li><strong>Fecha:</strong> {{ $registration->event->date->format('d/m/Y') }}</li>
                    @endif

                    @if ($registration->event->time)
                    <li><strong>Hora:</strong> {{ $registration->event->time->format('H:i') }}</li>
                    @endif

                    @if ($registration->event->location)
                    <li><strong>Ubicación:</strong> {{ $registration->event->location }}</li>
                    @endif
                </ul>
            </div>

            <h3>Tu Código QR</h3>
            <p>Utiliza este código QR el día del evento para el check-in:</p>

            <div class="qr-section">
                @if ($qrImageBase64)
                <img src="data:image/png;base64,{{ $qrImageBase64 }}" alt="Código QR">
                @else
                <p style="color: #999; font-size: 14px;">QR Token: <code>{{ $registration->qr_token }}</code></p>
                @endif
            </div>

            <center>
                <a href="{{ route('portal.events.show', $registration->event) }}" class="button">Ver Evento</a>
            </center>

            <p>¡Te esperamos!</p>
        </div>

        <div class="footer">
            <p>{{ config('app.name') }} - Sistema de Gestión de Eventos</p>
        </div>
    </div>
</body>
</html>
