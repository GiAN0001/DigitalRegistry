@props(['title'])

<header class="flex items-center justify-between p-4 bg-white rounded-lg shadow-md">
    
    <div>
        <h2 class="text-2xl font-bold text-gray-800">
            {{ $title }}
        </h2>
    </div>

    <div class="flex items-center gap-4">
        
        <div class="text-right">
            <div class="font-medium text-gray-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
            <div class="text-sm text-gray-500 capitalize">{{ Auth::user()->roles->first()->name ?? 'User' }}</div>
        </div>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type_submit" class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition duration-150">
                <x-lucide-log-out class="w-5 h-5" />
            </button>
        </form>
    </div>

</header>