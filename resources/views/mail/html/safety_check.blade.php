@component('mail::message')

# {{$data['title']}}

{{$data['detail']}}

{{$data['detail_url']}}

@component('mail::button', ['url' => $answer_url . '&answer=1', 'color' => 'join'])
無事
@endcomponent

@component('mail::button', ['url' => $answer_url . '&answer=2', 'color' => 'denied'])
要支援
@endcomponent

@component('mail::button', ['url' => $comment_url, 'color' => 'login'])
ログインしてコメントを記入する
@endcomponent

{{ config('app.name') }}より
@endcomponent