<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trans('app.answer_reception') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/safety.css') }}">
</head>
<body>
    <div class="container">
        <div class="wrap">
            <div class="logo">
                <img src="{{ asset('assets/images/logos/logo.png')}}" alt="logo">
            </div>
            <div class="box-alert">
                <x-alert/>
                <a href="/" class="home" target="_blank" title="ログインはこちら">ログインはこちら</a>
            </div>
        </div>
    </div>
</body>
</html>
