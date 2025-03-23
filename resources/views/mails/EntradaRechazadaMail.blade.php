@component('mail::message')
# 🚫 ¡Entrada Rechazada!

Hola **{{ $data['entrada']->ApelName }}**,

Lamentablemente, tu entrada no ha podido ser procesada debido a que el comprobante de pago que has enviado es **inválido**.

---

## 📝 Detalles de tu compra

- **🎤 Evento:** {{ $data['evento']->Evento }}
- **📅 Fecha:** {{ $data['evento']->FechaProbableInicio }}
- **📍 Ubicación:** {{ $data['establecimiento']->Establecimiento }} - {{ $data['establecimiento']->Ubicacion }}
- **🎫 Número de entradas:** {{ $data['entrada']->Cantidad }}
- **💲 Importe:** {{ number_format($data['entrada']->Importe, 2, '.', ',')}}

---

⚠️ **¿Qué debes hacer?**
Por favor, revisa tu comprobante y envía uno válido a la brevedad para poder procesar tu entrada. Si necesitas ayuda, no dudes en contactar a nuestro equipo de soporte.

@component('mail::button', ['url' => 'https://hsmodelaje.com', 'color' => 'danger'])
📩 Contactar Soporte
@endcomponent

Gracias por tu comprensión y esperamos poder resolver esto pronto.

Saludos,
**El equipo de {{ config('app.name') }}**
@endcomponent
