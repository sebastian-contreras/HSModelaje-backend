@component('mail::message')
# 🎉 ¡Compra Aprobada!

Hola **{{ $data['entrada']->ApelName }}**,

Tu compra ha sido **aprobada**. 🎉
Ya puedes ver tu entrada escaneando el código QR a continuación.

---

## 📝 Detalles de tu compra

- **🎤  Evento:** {{ $data['evento']->Evento }}
- **📅  Fecha:** {{ $data['evento']->FechaProbableInicio }}
- **📍  Ubicación:** {{ $data['establecimiento']->Establecimiento }} - {{ $data['establecimiento']->Ubicacion }}
- **🎫  Número de entradas:** {{ $data['entrada']->Cantidad }}
- **💲  Importe:** {{ number_format($data['entrada']->Importe, 2, '.', ',') }}

---

📢 **Tu entrada está lista para ser escaneada.**
<div style="text-align: center;">

<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $data['entrada']->Token }}&ecc=M" alt="Código QR" style="max-width: 100%; height: auto;" />
</div>

---

Si tienes alguna pregunta, no dudes en contactarnos.

Gracias por confiar en **{{ config('app.name') }}**. ¡Nos vemos en el evento! 🎶

@component('mail::button', ['url' => 'https://hsmodelaje.com', 'color' => 'success'])
    📩 Contactar Soporte
@endcomponent

Saludos,
**El equipo de {{ config('app.name') }}**
@endcomponent
