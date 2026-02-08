<x-app-layout>

    <div class="analytics mb-6">
        <x-analytics-widget
                :title="$stat1Title"
                :value="$stat1Value"
                icon-name="history"
                bg-color="bg-blue-600"
            />
            <x-analytics-widget
                :title="$stat2Title"
                :value="$stat2Value"
                icon-name="activity"
                bg-color="bg-blue-600"
            />
            <x-analytics-widget
                :title="$stat3Title"
                :value="$stat3Value"
                icon-name="shield-check"
                bg-color="bg-blue-600"
            />
            <x-analytics-widget
                :title="$stat4Title"
                :value="$stat4Value"
                icon-name="file-text"
                bg-color="bg-blue-600"
            />
    </div>
    
    <div class="flex flex-wrap gap-4 mb-6 w-64">
        <x-dynamic-filter
            model="App\Models\Log"
            column="date"
            title="Filter by Month"
        />
        @if(count(request()->query()) > 0)
            <a href="{{ request()->url() }}" 
            class="flex items-center gap-2 px-4 py-2 text-sm font-medium w-48 text-slate-700 hover:text-blue-800 transition-colors duration-200"
            title="Clear all active filters">
                <x-lucide-rotate-ccw class="w-4 h-4" />
                Reset Filters
            </a>
        @endif

    </div>

    <div class = "p-6 bg-white shadow-md rounded-lg">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">System Audit Logs</h2>

        <div class="overflow-x-auto mt-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-200">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-l-lg">Date</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">User</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Action</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-r-lg">Description</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                @if($log->date instanceof \Carbon\Carbon)
                                {{ $log->date->format('M d, Y h:i A') }}
                                @else
                                    {{ \Carbon\Carbon::parse($log->date)->format('M d, Y h:i A') }}
                                @endif
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $log->user->full_name ?? 'System' }}</td>
                            <td class="px-3 py-4">
                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">
                                    {{ $log->log_type->value ?? $log->log_type }}
                                </span>
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-600">{{ $log->description }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</x-app-layout>