<div class="w-64 bg-gray-800 text-white h-full flex flex-col transition-all duration-300 border-r border-gray-700">
    
    <div class="p-6 flex items-center justify-center border-b border-gray-700">
        <h2 class="text-xl font-bold flex items-center gap-3 tracking-wide">
            <x-lucide-building-2 class="w-8 h-8 text-blue-500" />
            <span class="text-gray-100">Namayan</span>
        </h2>
    </div>
    
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        
        <a href="{{ route('dashboard') }}" 
           class="flex items-center p-3 text-sm font-medium rounded-lg transition-colors duration-200 group
           {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
            <x-lucide-layout-dashboard class="w-5 h-5 mr-3" />
            <span>Dashboard</span>
        </a>

        <x-sidebar.dropdown title="Residents" :active="request()->routeIs('residents.*')">
            <x-slot name="icon">
                <x-lucide-users class="w-5 h-5" />
            </x-slot>
            
            <li>
                <a href="#" class="flex items-center w-full p-2 text-sm text-gray-400 transition duration-75 rounded-lg pl-11 hover:text-white hover:bg-gray-700">
                    List of Residents
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center w-full p-2 text-sm text-gray-400 transition duration-75 rounded-lg pl-11 hover:text-white hover:bg-gray-700">
                    Register Resident
                </a>
            </li>
        </x-sidebar.dropdown>

        @hasanyrole('admin|help desk')
            <x-sidebar.dropdown title="Transactions" :active="request()->routeIs('transactions.*')">
                <x-slot name="icon">
                    <x-lucide-scroll-text class="w-5 h-5" />
                </x-slot>
                
                <li>
                    <a href="#" class="flex items-center w-full p-2 text-sm text-gray-400 transition duration-75 rounded-lg pl-11 hover:text-white hover:bg-gray-700">
                        Document Requests
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center w-full p-2 text-sm text-gray-400 transition duration-75 rounded-lg pl-11 hover:text-white hover:bg-gray-700">
                        Facility Reservations
                    </a>
                </li>
            </x-sidebar.dropdown>
        @endhasanyrole

        <a href="#" class="flex items-center p-3 text-sm font-medium text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 group">
            <x-lucide-ticket class="w-5 h-5 mr-3" />
            <span>Tickets</span>
        </a>

        @role('admin')
            <div class="pt-6 mt-2">
                <p class="px-3 text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
                    Administration
                </p>

                <x-sidebar.dropdown title="Social Services" :active="request()->routeIs('services.*')">
                    <x-slot name="icon">
                        <x-lucide-heart-handshake class="w-5 h-5" />
                    </x-slot>
                    
                    <li><a href="#" class="flex items-center w-full p-2 text-sm text-gray-400 transition duration-75 rounded-lg pl-11 hover:text-white hover:bg-gray-700">Christmas Boxes</a></li>
                    <li><a href="#" class="flex items-center w-full p-2 text-sm text-gray-400 transition duration-75 rounded-lg pl-11 hover:text-white hover:bg-gray-700">TUPAD Program</a></li>
                </x-sidebar.dropdown>

                <a href="{{ route('users.index') }}" class="flex items-center p-3 text-sm font-medium text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 group">
                    <x-lucide-user-cog class="w-5 h-5 mr-3" />
                    <span>Manage Staff</span>
                </a>

                <a href="#" class="flex items-center p-3 text-sm font-medium text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 group">
                    <x-lucide-history class="w-5 h-5 mr-3" />
                    <span>System Logs</span>
                </a>
            </div>
        @endrole

    </nav>

    <div class="p-4 border-t border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                {{ substr(Auth::user()->first_name, 0, 1) }}
            </div>
            <div class="overflow-hidden">
                <p class="text-sm font-medium text-white truncate">{{ Auth::user()->first_name }}</p>
                <p class="text-xs text-gray-400 truncate">{{ Auth::user()->roles->first()->name ?? 'User' }}</p>
            </div>
        </div>
    </div>
</div>