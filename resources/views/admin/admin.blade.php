<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{config('app.name')}}</title>
        <link rel="stylesheet" type="text/css" href="{{mix('css/app.css')}}">
    </head>
    <body>
        <div id="root"></div>
    </body>
    <script type="text/javascript" src="{{lang()}}"></script>
    <script>
        window.ck_init = {
        	admin_prefix: "{{baseURL()}}",
            user: @json(auth()->user()),
        };
    </script>
    <script type="text/javascript" src="{{mix('js/admin.js')}}"></script>
</html>
