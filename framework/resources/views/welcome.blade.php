<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,400i,700" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Niconne" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #ccc;
                color: dimgrey;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 30px;
                font-weight: bold;
            }

            .links > a {
                color: #636b6f;
                font-size: 30px;
                font-weight: bold;
                letter-spacing: .1rem;
                text-decoration: none;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .xdm{
                font-family: 'Niconne', sans-serif;
                font-size: 40px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md links">
                    <a href="{{ url('/cpanel') }}"><span class="xdm">x.d.m</span> Cpanel &rarr;</a>
                </div>

            </div>
        </div>
    </body>
</html>
