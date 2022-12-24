<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <link rel="stylesheet" href="{{ asset('assets/css/safety.css') }}">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="box_susscess">
                <p class="text">{{trans('app.safe_answer_success')}}</p>
                <a class="home" href="{{ url('/') }}" target="_blank"> {{trans('app.back_home')}} </a>
            </div>

        </div>
    </div>
</body>
</html>