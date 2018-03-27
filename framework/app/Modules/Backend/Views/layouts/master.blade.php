<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title')</title>

    <!--REQUIRE CSS-->
    @foreach($css as $item)
        <link href="{{ asset($item) }}" rel="stylesheet"/>
    @endforeach
    @yield ('css-link')
    
</head>
<body class="app sidebar-mini">
    <header class="app-header">
        <a class="app-header__logo" href="{{url('/')}}">x.d.m</a>
        <!-- Sidebar toggle button-->
        <a class="app-sidebar__toggle" href="#" data-toggle="sidebar"></a>

        <ul class="app-nav">
            <!-- User Menu-->
            <li><a id="select_company" class="app-nav__item" href="javascript:void(0)" data-toggle="dropdown">Working Company: {{session()->get('selected_company_name', 'None')}}</a></li>
            <li class="dropdown"><a class="app-nav__item" href="javascript:void(0)" data-toggle="dropdown"><i class="fa fa-user fa-lg"></i></a>
                <ul class="dropdown-menu settings-menu dropdown-menu-right">
                    <li><a class="dropdown-item" href="#"><i class="fa fa-cog fa-lg"></i> Settings</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa fa-user fa-lg"></i> Profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('backend-logout') }}"><i class="fa fa-sign-out fa-lg"></i> Logout</a></li>
                </ul>
            </li>
        </ul>
    </header>

    <!-- Sidebar menu-->
    @include('Backend::layouts.sidebar')

    <main class="app-content">
        @yield('content')
    </main>

    <!-- Main Footer -->
    <footer class="main-footer">
        <!-- To the right -->
        <div class="pull-right hidden-xs text-right">
            Developed by <a href="mailto:sirminhdat@gmail.com" target="_blank">Dat Le</a><br/>
            <strong>e.</strong> <a href="mailto:sirminhdat@gmail.com" target="_blank">sirminhdat@gmail.com</a> | 
            <strong>m.</strong> (+84) 919 564 515<br/>
            Copyright &copy; {{$copy_right_year}}. All Rights Reserved | Powered by <a href="http://laravel.com/" target="_blank">Laravel Framework</a>
        </div>
    </footer>

    <!--REQUIRE JS SCRIPTS-->
    @foreach($js as $item)
        <script src="{{ asset($item) }}"></script>
    @endforeach
    @yield ('js-link')
    
    @yield ('scripts')
    <script>
        $(function(){
            $('#select_company').on('click', function(e){
                e.preventDefault();
                location.href = "{{route('company-get-select')}}";
            });
        });
    </script>
</body>
</html>