@component('mail::message')
# Новая заявка на бронирование

Поступила новая заявка от пользователя.

**Автор:** {{ $bookingRequest->author?->name }} ({{ $bookingRequest->author?->email }})

**Дата бронирования:** {{ \Carbon\Carbon::parse($bookingRequest->date)->translatedFormat('j F Y') }}

@if($bookingRequest->comment)
**Комментарий:** {{ $bookingRequest->comment }}
@endif

@component('mail::button', ['url' => route('admin.requests.index')])
Открыть заявки
@endcomponent

@endcomponent
