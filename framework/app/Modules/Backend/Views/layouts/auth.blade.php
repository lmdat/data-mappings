<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
     <title>@yield('title')</title>

    <!--REQUIRE CSS-->
    @foreach($css as $item)
        <link href="{{ asset($item) }}" rel="stylesheet"/>
    @endforeach
    @yield ('css-link')
    
</head>
<body>
    
   
        @yield('content')
    

   
    <!--REQUIRE JS SCRIPTS-->
    @foreach($js as $item)
        <script src="{{ asset($item) }}"></script>
    @endforeach
    @yield ('js-link')
    
    @yield ('scripts')
    
</body>
</html>