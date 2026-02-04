@include('components.dashboard.top-nav-bar')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="/images/favicon.ico" />
    <title>{{ $pageTitle ?? 'IPOS WEB' }}</title>

    @include('snippets.styles')
    @yield('head')
</head>

<body>
    @yield('top-nav-bar')
    @yield('side-nav-bar')
    @yield('body')

    @yield('modals')
    @yield('templates')
    @include('snippets.scripts')

    <script src="{{ asset('js/pace.min.js') }}"></script>
    <script>
        Pace.options.ajax.trackWebSockets = false;
    </script>
    @yield('scripts')
</body>

</html>