<x-app-layout>
    <div>
        <h2 class="text-2xl font-bold mb-6 text-gray-800">System Audit Logs</h2>
        
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($logs as $log)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if($log->date instanceof \Carbon\Carbon)
                                {{ $log->date->format('M d, Y h:i A') }}
                                @else
                                    {{ \Carbon\Carbon::parse($log->date)->format('M d, Y h:i A') }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $log->user->full_name ?? 'System' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">
                                    {{ $log->log_type->value ?? $log->log_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $log->description }}</td>
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