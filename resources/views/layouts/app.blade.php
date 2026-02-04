<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="/images/favicon.ico" />
    <title>{{ $pageTitle ?? 'IPOS WEB' }}</title>  

    @include('snippets.styles')
    <style>
        #dashboard-container {
            padding: 0 1rem;
        }

        #dashboard-container .card {
            box-shadow: 0px 0px 10px #00000040;
        }

        .title-text {
            font-size: 1.5rem;
        }

        .mobile-card .card-header {
            width: 100%;
        }

        .mobile-card .card-body div{
            display: flex;
            font-size: 40px;
        }

        .mobile-card .card-body div i:hover {
            cursor: pointer;
            transform: scale(1.1);
            transition: all 0.5s;

        }

        @media only screen and (max-width: 768px) {
            .mobile-card {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 100%;
            }

            .mobile-card .card-header {
                text-align: center;
            }

            .mobile-card .card-body div{
                justify-content: center;
                align-items: center;
                gap: 1rem;
                font-size: 60px;
            }
        }

        .center-all {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
    @yield('head')
</head>

<body>
    @yield('top-nav-bar')
    @yield('side-nav-bar')
    @yield('body')

    @yield('modals')
    @yield('templates')
    @include('snippets.scripts')
    @yield('scripts')
</body>

</html>