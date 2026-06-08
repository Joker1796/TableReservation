@component('mail::message')
# Новое предложение события

Пользователь предлагает добавить событие.

**Автор:** {{ $event->author?->name }} ({{ $event->author?->email }})

**Название:** {{ $event->title }}

**Короткое описание:** {{ $event->short_description }}

@if($event->description)
**Описание:**
{{ Str::limit($event->description, 300) }}
@endif

@component('mail::button', ['url' => route('admin.events.index')])
Рассмотреть предложение
@endcomponent

@endcomponent
