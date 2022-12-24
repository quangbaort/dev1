<!DOCTYPE html>
<html lang="{{app()->getLocale()}}">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="cache-control" content="no-cache" />
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <meta http-equiv="Cache-Control" content="no-store" />
  <title>{{ env('APP_NAME') }}</title>

  <link rel="icon" href="/assets/images/favicon/favicon.ico?v=time">
  <script src="{{ mix('assets/js/manifest.js') }}"></script>
  <script src="{{ mix('assets/js/vendor.js') }}"></script>
  <script src="{{ mix('assets/js/app.js') }}" defer></script>
  <script src="/assets/js/ja.js"></script>
  <style>
  @media print {
    @page {
        margin-top: 0;
        margin-bottom: 0;
    }
  }
  </style>

</head>

<body>
  <noscript>
    <strong>Browser do not support javascipt!</strong>
  </noscript>

  <div id="app"></div>
</body>

</html>
