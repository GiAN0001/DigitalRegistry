<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/css/login.css', 'resources/js/app.js'])
</head>

<body class="main-grid font-sans antialiased bg-slate-100">

    <div class="side-bar">
        @include('layouts.sidebar')
    </div>

    <div class="content">
        <div class="flex-grow">
           @include('components.header.header')
        </div>
        <div class="mt-4">
            {{ $slot}}
        </div>
    </div>

</body>
</html>
