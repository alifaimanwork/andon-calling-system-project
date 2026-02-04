<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? '' }}</title>
</head>
<link rel="stylesheet" href="{{ asset(mix('css/print.css')) }}">
@yield('head')

<body>
    @yield('body')
    @yield('modals')
    @yield('templates')
    @yield('scripts')
</body>

</html>
