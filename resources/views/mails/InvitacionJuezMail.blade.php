@component('mail::message')
# ✨ Invitación como Jurado

Hola **{{ $data['juez']->ApelName }}**,

Nos complace invitarte a ser parte de nuestro jurado para el evento **{{ $data['evento']->Evento }}**. 🎤

---

## 📅 Detalles del Evento

- **📌 Evento:** {{ $data['evento']->Evento }}
- **📅 Fecha:** {{ $data['evento']->FechaProbableInicio }}
- **📍 Ubicación:** {{ $data['establecimiento']->Establecimiento }} - {{ $data['establecimiento']->Ubicacion }}

---

Para acceder a tu panel de jurado, por favor ingresa al siguiente enlace:
@component('mail::button', ['url' => rtrim(config('app.frontend_url'), '/') . '/voto-jurado/' . $data['juez']->Token, 'color' => 'primary'])
🎟️ Acceder como Jurado
@endcomponent

Si tienes alguna pregunta o necesitas más información, no dudes en contactarnos.

¡Esperamos contar con tu valiosa participación!

Saludos,
**El equipo de {{ config('app.name') }}**
@endcomponent