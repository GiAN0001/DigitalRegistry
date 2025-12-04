<x-app-layout>
    
    <div class="analytics">
        <x-analytics-widget 
            title="Total Active Users" 
            :value="$totalUsers" 
            icon-name="users" 
            bg-color="bg-blue-500" 
        />
        <x-analytics-widget 
            title="Total Inactive Users" 
            :value="$totalInactiveUsers" 
            icon-name="users" 
            bg-color="bg-blue-500" 
        />
        <x-analytics-widget 
            title="Number of Admins" 
            :value="$totalAdmins" 
            icon-name="users" 
            bg-color="bg-blue-500" 
        />
        <x-analytics-widget 
            title="Number of Help desk" 
            :value="$totalHelpDesk" 
            icon-name="users" 
            bg-color="bg-blue-500" 
        />
        <x-analytics-widget 
            title="Number of staff" 
            :value="$totalStaff" 
            icon-name="users" 
            bg-color="bg-blue-500" 
        />
   
    </div>

    <div class="p-6 bg-white shadow-md rounded-lg">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800">System User Accounts</h2>
            <a href="#" 
                x-data
                x-on:click.prevent="$dispatch('open-modal', 'create-user-modal')"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 flex items-center"
            >
                <x-lucide-plus class="w-4 h-4 mr-1" />
                Create New User
            </a>
        </div>

        <div class="mb-4">
            <x-search-bar placeholder="Search by name, username, or role..." />
        </div>

        <div class="overflow-x-auto mt-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-200">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-l-lg">Name</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Username</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Email</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">System Role</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Barangay Role (Job)</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-r-lg">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $user->first_name }} {{ $user->last_name }}
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $user->username }}
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->email }}
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm font-semibold capitalize">
                                {{ $user->roles->first()->name ?? 'N/A' }}
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->barangayRole->name ?? 'N/A' }}
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap">
                                @if($user->status == 1)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                @endif
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                
                                @if($user->id != Auth::id())
                                    <a href="#" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $user->id }}').submit();" class="text-red-600 hover:text-red-900 ml-4">Delete</a>
                                    
                                    <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @else
                                    <span class="text-gray-400 ml-4">Admin Self</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-4 text-center text-gray-500">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>

        @include('admin.users.partials._create-modal')
        
    </div>
</x-app-layout>