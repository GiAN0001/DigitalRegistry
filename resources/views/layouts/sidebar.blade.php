<div class="text-white h-full flex flex-col transition-all duration-300">

    <nav class="flex-1 space-y-2 overflow-y-auto">
        
        <a href="{{ route('dashboard') }}"  

        <a href="{{ route('dashboard') }}"
           class="flex items-center p-2 text-sm font-medium rounded-lg transition-colors duration-200 group
           {{ request()->routeIs('dashboard') ? 'bg-blue-700 text-white shadow-md' : 'text-slate-700 hover:bg-blue-700 hover:text-white' }}">
            <x-lucide-layout-dashboard class="w-5 h-5 mr-2" />
            <span>Dashboard</span>
        </a>

        <a href="{{ route('residents.index') }}" class="
        flex items-center p-2 text-sm font-medium rounded-lg transition-colors duration-200 group
        {{ request()->routeIs('residents.*') ? 'bg-blue-700 text-slate-50' : 'text-slate-700 hover:bg-blue-700 hover:text-slate-50' }}
         ">
            <x-lucide-users class="w-5 h-5 mr-2" />
            <span>Residents</span>
        </a>

        @hasanyrole('admin|help desk')
            <x-sidebar.dropdown title="Transactions" :active="request()->routeIs('documents.*', 'reservations.*')">
                <x-slot name="icon">
                    <x-lucide-scroll-text class="w-5 h-5 mr-2 {{ request()->routeIs('documents.*', 'reservations.*') ? 'text-blue-700' : '' }}" />
                </x-slot>

                <li>
                    <a href="{{ route('transaction.document') }}" class="flex items-center w-full p-2 text-sm font-medium text-slate-700 transition duration-75 rounded-lg hover:text-white hover:bg-blue-700">
                    <a href="#"
                       class="flex items-center w-full p-2 text-sm font-medium transition duration-75 rounded-lg
                       {{ request()->routeIs('documents.*') ? 'bg-blue-700 text-white' : 'text-slate-700 hover:text-white hover:bg-blue-700' }}">
                        Document Requests
                    </a>
                </li>
                <li>
                    <a href="#"
                       class="flex items-center w-full p-2 text-sm font-medium transition duration-75 rounded-lg
                       {{ request()->routeIs('reservations.*') ? 'bg-blue-700 text-white' : 'text-slate-700 hover:text-white hover:bg-blue-700' }}">
                        Facility Reservations
                    </a>
                </li>
            </x-sidebar.dropdown>
        @endhasanyrole

        <a href="#" class="flex items-center w-full p-2 text-sm font-medium text-slate-700 transition duration-75 rounded-lg hover:text-white hover:bg-blue-700">
            <x-lucide-ticket class="w-5 h-5 mr-2" />
            <span>Tickets</span>
        </a>

        @role('admin')
            <div class="pt-6 mt-2">
                <p class="px-3 text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
                    Administration
                </p>

                <x-sidebar.dropdown title="Social Services" :active="request()->routeIs('services.*')">
                    <x-slot name="icon">
                        <x-lucide-heart-handshake class="w-5 h-5 {{ request()->routeIs('services.*') ? 'text-blue-700' : '' }}" />
                    </x-slot>

                    <li>
                        <a href="#"
                           class="flex items-center w-full p-2 text-sm font-medium transition duration-75 rounded-lg
                           {{ request()->routeIs('services.christmas.*') ? 'bg-blue-700 text-white' : 'text-slate-700 hover:text-white hover:bg-blue-700' }}">
                           Christmas Boxes
                        </a>
                    </li>
                    <li>
                        <a href="#"
                           class="flex items-center w-full p-2 text-sm font-medium transition duration-75 rounded-lg
                           {{ request()->routeIs('services.tupad.*') ? 'bg-blue-700 text-white' : 'text-slate-700 hover:text-white hover:bg-blue-700' }}">
                           TUPAD Program
                        </a>
                    </li>
                </x-sidebar.dropdown>

                <x-sidebar.dropdown title="Manage Staff" :active="request()->routeIs('manageStaff.*', 'admin.users.*')">
                    <x-slot name="icon">
                        <x-lucide-scroll-text class="w-5 h-5 {{ request()->routeIs('manageStaff.*', 'admin.users.*') ? 'text-blue-700' : '' }}" />
                    </x-slot>

                    <li>
                        <a href="{{ route('admin.users.index') }}"
                           class="flex items-center w-full p-2 text-sm font-medium transition duration-75 rounded-lg
                           {{ request()->routeIs('admin.users.*') ? 'bg-blue-700 text-white' : 'text-slate-700 hover:text-white hover:bg-blue-700' }}">
                            Account Management
                        </a>
                    </li>
                    <li>
                        <a href="#"
                           class="flex items-center w-full p-2 text-sm font-medium transition duration-75 rounded-lg
                           {{ request()->routeIs('manageStaff.activity') ? 'bg-blue-700 text-white' : 'text-slate-700 hover:text-white hover:bg-blue-700' }}">
                            User Activity
                        </a>
                    </li>
                </x-sidebar.dropdown>

                <a href="#" class="flex items-center w-full p-2 text-sm font-medium text-slate-700 transition duration-75 rounded-lg hover:text-white hover:bg-blue-700">
                    <x-lucide-history class="w-5 h-5 mr-2" />
                    <span>System Logs</span>
                </a>
            </div>
        @endrole
    </nav>
</div>
