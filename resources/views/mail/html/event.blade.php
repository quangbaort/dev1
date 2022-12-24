<!-- @Deprecated use mails/ja/event/confirmation.blade.php instead -->
@component('mail::message')

# {{$data['title']}}

【開催日時】：　{{$data['event_time']}}

【出欠回答期間】：　{{$data['period_fmt']}}

@if(!is_null($data['place']))
【場所】：　{{$data['place']}}
@endif
@if(!is_null($data['place_url']))
<!-- Markdown link： [Link text](URL "Title")　-->
【場所URL】：　[{{$data['place_url']}}]({{$data['place_url']}} "場所URL")
@endif

@if(!is_null($data['detail']))
【詳細】：　{{$data['detail']}}
@endif

@if(!is_null($data['detail_url']))
【詳細URL】：　[{{$data['detail_url']}}]({{$data['detail_url']}} "詳細URL")
@endif

@component('mail::button', ['url' => $data['join_url'], 'color' => 'join'])
出席
@endcomponent

@component('mail::button', ['url' => $data['on_hold_url'], 'color' => 'on_hold'])
保留
@endcomponent

@component('mail::button', ['url' => $data['deny_url'], 'color' => 'denied'])
欠席
@endcomponent

{{ config('app.name') }}より
@endcomponent