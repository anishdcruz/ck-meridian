<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('app.name')}}</title>
    <link rel="stylesheet" type="text/css" href="{{mix('css/frontend.css')}}">
</head>
<body>
    <div class="container mt-5 mb-5 text-center">
        <img src="{{asset('img/meridian-logo.png')}}" align="Meridian" style="width: 200px;">
    </div>
    <div id="app" class="mb-5">
        @yield('content')
    </div>
</body>
    @stack('scripts_before')
    <script type="text/javascript" src="{{mix('js/frontend.js')}}"></script>
    @stack('scripts_after')
</html>