<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <div class="p-6 bg-white shadow-md rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800">Welcome back, {{ Auth::user()->first_name }}!</h3>
        <p class="text-gray-600 mt-2">
            You are logged in as a <span class="font-bold capitalize">{{ Auth::user()->roles->first()->name ?? 'User' }}</span>.
        </p>
    </div>

</x-app-layout>