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
            subscription: @json(auth()->user()->currentSubscription()),
            user: @json(auth()->user()),
            current_team: @json(auth()->user()->currentTeam()),
            current_team_role: @json(auth()->user()->currentTeam()->role()),
            team_settings: @json($teamSettings),
            timezone: "{{config('app.timezone')}}",
            mode: "{{config('meridian.app_mode')}}"
        };
    </script>
    <script type="text/javascript" src="{{mix('js/app.js')}}"></script>
</html>
