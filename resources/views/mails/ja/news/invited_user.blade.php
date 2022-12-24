@component('mail::message')

# {{ $news->title }}

【期間】：{{ $news->start->format('Y年m月d日') }} ～ {{ $news->end->format('Y年m月d日') }}

@if(!is_null($news->detail))
【詳細】：{{ $news->detail }}
@endif

@if(!is_null($news->detail_url))
【詳細URL】：[{{ $news->detail_url }}]({{ $news->detail_url }} "詳細URL")
@endif

@if (!is_null($news->file))
詳細資料は[こちら]({{ $news->file->url }} "詳細資料URL")
@endif

@if(!is_null($news->place))
【場所】：{{ $news->place }}
@endif

@if(!is_null($news->place_url))
【場所URL】：[{{ $news->place_url }}]({{ $news->place_url }} "場所URL")
@endif

@component('mail::button', ['url' => $urlMarkRead, 'color' => 'join'])
    既読にする
@endcomponent

{{ config('app.name') }}より
@endcomponent
