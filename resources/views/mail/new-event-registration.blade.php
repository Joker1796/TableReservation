@component('mail::message')
# Новая запись на событие

Пользователь зарегистрировался на событие.

**Участник:** {{ $registrant->name }} ({{ $registrant->email }})

**Событие:** {{ $event->title }}

**Дата начала:** {{ \Carbon\Carbon::parse($event->starts_at)->translatedFormat('j F Y, H:i') }}

@component('mail::button', ['url' => route('admin.events.index')])
Открыть события
@endcomponent

@endcomponent
