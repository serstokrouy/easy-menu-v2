<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EASY-MENU</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>

    @include('admin.includes.header')

    <main class="flex-grow">
        @yield('content')
    </main>

    @include('admin.includes.footer')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    @yield('scripts')

</body>
</html>
