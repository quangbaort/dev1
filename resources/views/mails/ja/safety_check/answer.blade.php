@component('mail::message')

# {{ $safetyCheck->title }}

{{ $safetyCheck->detail }}

{{ $safetyCheck->detail_url }}


@component('mail::button', ['url' => $urls['normal'], 'color' => 'join'])
    無事
@endcomponent

@component('mail::button', ['url' => $urls['urgent'], 'color' => 'denied'])
    要支援
@endcomponent

@component('mail::button', ['url' => url(''), 'color' => 'login'])
    ログインしてコメントを記入する
@endcomponent


{{ config('app.name') }}より
@endcomponent
