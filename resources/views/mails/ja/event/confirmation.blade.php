@component('mail::message')

# {{ $event->title }}

【開催日時】：{{ $event->event_time }}

【出欠回答期間】：{{ $event->period->translatedFormat('Y年m月d日（D）') }}

@if(!is_null($event->place))
【場所】：{{ $event->place }}
@endif

@if(!is_null($event->place_url))
<!-- Markdown link： [Link text](URL "Title")　-->
【場所URL】：[{{$event->place_url}}]({{$event->place_url}} "場所URL")
@endif

@if(!is_null($event->detail))
【詳細】：{{$event->detail}}
@endif

@if(!is_null($event->detail_url))
【詳細URL】：[{{$event->detail_url}}]({{$event->detail_url}} "詳細URL")
@endif

@component('mail::button', ['url' => $urls['join'], 'color' => 'join'])
    出席
@endcomponent

@component('mail::button', ['url' => $urls['hold'], 'color' => 'on_hold'])
    保留
@endcomponent

@component('mail::button', ['url' => $urls['deny'], 'color' => 'denied'])
    欠席
@endcomponent

{{ config('app.name') }}より
@endcomponent
