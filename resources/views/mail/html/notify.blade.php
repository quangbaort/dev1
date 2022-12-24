@component('mail::message')

# {{$data['title']}}

【期間】：{{$start->format('Y年m月d日')}} ～ {{$end->format('Y年m月d日')}}

@if($data['detail'])
【詳細】：{{$data['detail']}}
@endif

@if($data['detail_url'])
<!-- Markdown link： [Link text](URL "Title")　-->
【詳細URL】：[{{$data['detail_url']}}]({{$data['detail_url']}} "詳細URL")
@endif

@if ($file_url)
詳細資料は[こちら]({{url((String)$file_url)}} "詳細資料URL")
@endif

@if ($data['place'])
【場所】：{{$data['place']}}
@endif

@if($data['place_url'])
【場所URL】：[{{$data['place_url']}}]({{$data['place_url']}} "場所URL")
@endif

@component('mail::button', ['url' => $read_url, 'color' => 'join'])
既読にする
@endcomponent

{{ config('app.name') }}より
@endcomponent