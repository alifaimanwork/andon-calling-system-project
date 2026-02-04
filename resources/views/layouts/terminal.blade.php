@include('components.terminal.top-nav-bar')
@include('components.terminal.side-nav-bar')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="/images/favicon.ico" />
    <title>{{ $pageTitle ?? 'IPOS TERMINAL' }}</title>

    @include('snippets.styles')
    <link rel="stylesheet" href="{{ asset('css/pace-theme-default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/minimal.css') }}">
    <style>
        body {
            background-color: #eee;
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
    <script>
        window.paceOptions = {
            startOnPageLoad: false,
            ajax: {
                trackMethods: ['GET', 'POST', 'PUT', 'DELETE', 'REMOVE']
            }
        };
    </script>
    <script src="{{ asset('js/pace.min.js') }}"></script>
    <script>
        Pace.options.ajax.trackWebSockets = false;
    </script>
    @yield('scripts')
</body>

</html>