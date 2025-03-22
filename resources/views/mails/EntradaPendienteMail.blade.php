@component('mail::message')
# 🎉 ¡Compra confirmada!

Hola **{{ $data['entrada']->ApelName }}**,

Queremos agradecerte por tu compra. 🎟️
Tu pedido ha sido confirmado y actualmente tu entrada está en **proceso de verificación**.
Una vez completado, recibirás tu entrada en este mismo correo.

---

## 📝 Detalles de tu compra

- **🎤 Evento:** {{ $data['evento']->Evento }}
- **📅 Fecha:** {{ $data['evento']->FechaProbableInicio }}
- **📍 Ubicación:** {{ $data['establecimiento']->Establecimiento }} - {{ $data['establecimiento']->Ubicacion }}
- **🎫 Número de entradas:** {{ $data['entrada']->Cantidad }}
- **💲 Importe:** {{ number_format($data['entrada']->Importe, 2, '.', ',')}}

---

📢 **Recibirás tu entrada pronto.**
Te notificaremos en cuanto esté lista.

Si tienes alguna duda, nuestro equipo de soporte está disponible para ayudarte.

Gracias por confiar en **{{ config('app.name') }}**.
¡Nos vemos en el evento! 🎶

@component('mail::button', ['url' => 'https://hsmodelaje.com', 'color' => 'success'])
📩 Contactar Soporte
@endcomponent

Saludos,
**El equipo de {{ config('app.name') }}**
@endcomponent
