<x-app-layout>
    <div class="analytics">

        <x-analytics-widget
            title="Total Residents"
            :value="$totalResidents"
            icon-name="users"
            bg-color="bg-blue-500"
        />

        <x-analytics-widget
            title="Total Households"
            :value="$totalHousehold"
            icon-name="house"
            bg-color="bg-blue-500"
        />
        <x-analytics-widget
            title="Total Active Users"
            :value="$totalActiveUsers"
            icon-name="square-user"
            bg-color="bg-blue-500"
        />
    </div>
    
    <div>
        <div>
            <p>Barangay Staff Activity</p>
            <a href="#">See all staff</a>
        </div>
        <table>
            <thead>
                <th>Name</th>
                <th>Barangay Role</th>
                <th>System Role</th>
                <th>House Registered</th>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td >
                            {{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}
                        </td>
                        
                        <td>
                            {{ $user->barangayRole->name ?? 'N/A' }}
                        </td>
                        
                        <td>
                            {{ $user->roles->first()->name ?? 'N/A' }}
                        </td>
                        
                        <td>
                            {{ $user->households_registered_count ?? 0 }}
                        </td> 
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-3 py-4 text-center text-gray-500">
                            No users found matching the criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="graphs">
        <x-chart-widget 
            title="Population Count by Purok and Street"
            type="pie" 
            :data="$populationChartData" 
        />
        <x-demographics-chart 
            title="Population Demographics by Age Group"
            :data="$demographicsChartData" 
            class="mt-8"
        />
    </div>
  

</x-app-layout>
