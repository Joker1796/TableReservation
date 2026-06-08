@component('mail::message')
# Новое предложение новости

Пользователь предлагает добавить публикацию в ленту клуба.

**Автор:** {{ $post->author?->name }} ({{ $post->author?->email }})

**Заголовок:** {{ $post->title }}

@if($post->content)
**Содержание:**
{{ Str::limit(strip_tags($post->content), 300) }}
@endif

@component('mail::button', ['url' => route('admin.posts.index')])
Рассмотреть предложение
@endcomponent

@endcomponent
