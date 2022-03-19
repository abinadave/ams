<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">

    <title>Assets Panagement System Panel</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <!-- <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    -->
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<style type="text/css">
    .color1 {
        background-color: #dcdcdc;
        border-radius: 15px;
    }
    @page {
        size: auto;
    }
</style>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{config('test')}}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto nav-uls">
                     
                        <li class="nav-item">
                            <a class="nav-link">
                                    <i class="fa fa-map" aria-hidden="true"></i>
                                    <router-link to="/Inventory">
                                        Inventory
                                    </router-link>
                                </a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link">
                                    <i class="fa fa-map" aria-hidden="true"></i>
                                    <router-link to="/PurchaseOrder">
                                        Purchase Order
                                    </router-link>
                                </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link">
                                    <i class="fa fa-map" aria-hidden="true"></i>
                                    <router-link to="/APR">
                                        APR
                                    </router-link>
                                </a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link">
                                    <i class="fa fa-road" aria-hidden="true"></i>
                                    <router-link to="/Delivery">
                                        Delivery
                                    </router-link>
                                </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link">
                                    <i class="fa fa-road" aria-hidden="true"></i>
                                    <router-link to="/Requisition">
                                        Requisition
                                    </router-link>
                                </a>
                        </li>
                        <li class="nav-item bg-default">
                            <a class="nav-link ">
                             <i class="fa fa-list-ol" aria-hidden="true"></i>
                             <router-link to="/Reports">Reports</router-link>
                            </a>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
            <router-view></router-view>
        </main>
    </div>
    
    <div id="report-output">
  
    </div>
    <div id="report-output2">
  
    </div>
</body>
</html>
