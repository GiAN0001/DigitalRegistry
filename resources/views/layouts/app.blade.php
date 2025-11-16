<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/css/login.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100">

    <div class="flex h-screen ">

        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col overflow-hidden">

            <header class="flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200 shadow-sm">
                <h2 class="text-xl font-semibold text-gray-800">
                    {{ $header ?? 'Dashboard' }}
                </h2>

                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">{{ Auth::user()->first_name }}</span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">
                            Log Out
                        </button>
                    </form>
                </div>
            </header>

            <main class="w-full flex-grow mt-8 overflow-y-auto">
                {{ $slot }}
            </main>
        </div>

    </div>
</body>
</html>