<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/css/login.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">

        <div class="login-container">

            <div class="login-left-column">
                <div class="login-header-text h-[200px] w-[80%] flex justify-center items-center">
                   <div class="flex gap-1">
                        <img src="{{ asset('images/seal-mandaluyong.png') }}" alt="Mandaluyong Seal" class="w-[100px] h-[100px]">
                        <img src="{{ asset('images/seal-barangay.png') }}" alt="Barangay Seal" class="w-[100px] h-[100px]">
                   </div>
                    <div class=" flex flex-col items-left gap-.8">
                        <p class="font-[Times_New_Roman]">REPUBLIC OF THE PHILIPPINES</p>
                        <div class="border border-solid border-white"></div>
                        <p class="font-[Times_New_Roman]">BARANGAY NAMAYAN, MANDALUYONG CITY</p>
                    </div>
                </div>
            </div>
                {{-- This is a Blade for testing the AI. --}}
            <div class="login-right-column">
                {{ $slot }}
            </div>

        </div>

    </body>
</html>
