@php
    $module = '';
    $page = '';

    if (request()->routeIs('dashboard')) {
        $module = 'Page';
        $page = 'Dashboard';
    } elseif (request()->routeIs('residents.*')) {
        $module = 'Residents';
        $page = 'Residents';
    } elseif (request()->routeIs('transaction.document') || request()->routeIs('document-request.*') || request()->routeIs('document.*')) {
        $module = 'Transaction';
        $page = 'Document Request';
    } elseif (request()->routeIs('transaction.facility') || request()->routeIs('facility.*')) {
        $module = 'Transaction';
        $page = 'Facility Reservation';
    } elseif (request()->routeIs('admin.users.*')) {
        $module = 'Admin';
        $page = 'User Management';

        if (request()->routeIs('admin.users.logs')) {
            $page = 'Audit Logs';
        }     
    } elseif (request()->routeIs('profile.*')) {
        $module = 'Account';
        $page = 'Profile Settings';
    }
@endphp

<div class="sub-content">
    <header class="bg-slate-50 shadow-sm rounded-2xl p-4">
        <div class="flex items-center justify-between">

            {{-- DIRECTORY DESIGN (expl: Page // Dashboard) --}}
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <span class="text-sm font-medium text-gray-500">
                            {{ $module }}
                        </span>
                    </li>
                    <li>
                        <div class="flex items-center">
                            @if($module)
                                <span class="mx-2 text-gray-400">
                                    /
                                </span>
                            @endif
                            <span class="text-sm font-medium text-slate-800" aria-current="page">
                                {{ $page }}
                            </span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- USER PROFILE DROPDOWN --}}
            <div x-data="{ open: false }" class="relative">

                <button @click="open = !open" class="flex items-center space-x-3 focus:outline-none">
                    {{-- Image profile --}}
                    <img class="w-10 h-10 rounded-full"
                        src="https://ui-avatars.com/api/?name={{ Auth::user()->first_name }}+{{ Auth::user()->last_name }}&color=7F9CF5&background=EBF4FF"
                        alt="User avatar">

                    {{-- User Information --}}
                    <div class="text-left hidden md:block">
                        <div class="font-semibold text-sm text-gray-900">{{ Auth::user()->first_name }}
                            {{ Auth::user()->last_name }} {{ Auth::user()->extension }}</div>
                        <div class="text-xs text-gray-500">{{ Auth::user()->barangayRole->name }}</div>
                    </div>

                    {{-- Drop down Icon --}}
                    <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-show="open" @click.away="open = false" x-transition
                    class="absolute right-0 mt-2 w-48 md:w-full bg-white py-1 rounded-lg shadow-sm border border-gray-200 z-50"
                    style="display: none;">

                    <div class="flex items-center px-4 p-2 border-b border-gray-100">
                        <img class="w-6 h-6 rounded-full"
                            src="https://ui-avatars.com/api/?name={{ Auth::user()->first_name }}+{{ Auth::user()->last_name }}&color=7F9CF5&background=EBF4FF"
                            alt="User avatar">
                        <div class="ml-3 overflow-hidden">
                            <div class="text-xs text-gray-900 truncate">{{ Auth::user()->first_name }}
                                {{ Auth::user()->last_name }}</div>
                        </div>
                    </div>

                    {{-- profile and settings section --}}
                    <nav class="py-2">
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-1 text-xs text-gray-700 hover:bg-gray-100">
                            <svg class="w-5 h-5 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.096 2.573-1.066z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="ml-3 truncate">Profile Settings</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                                class="flex items-center px-4 py-1 text-xs text-gray-700 hover:bg-gray-100"
                                onclick="event.preventDefault(); this.closest('form').submit();">

                                <svg class="w-5 h-5 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                    </path>
                                </svg>
                                <span class="ml-3">Log Out</span>
                            </a>
                        </form>
                    </nav>
                </div>
            </div>
        </div>
    </header>
</div>
