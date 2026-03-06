<x-app-layout> {{-- file created by gian --}}


    @hasanyrole('admin|super admin')
        <div class = "analytics mb-6">
            <x-analytics-widget
                title="Pending Tickets"
                :value="$pendingTickets"
                icon-name="clock"
                bg-color="bg-gray-500"
                />
            <x-analytics-widget
                title="In Progress Tickets"
                :value="$inProgressTickets"
                icon-name="loader"
                bg-color="bg-blue-500"
                />
            <x-analytics-widget
                title="Completed Tickets"
                :value="$completedTickets"
                icon-name="check"
                bg-color="bg-green-500"
                />
            <x-analytics-widget
                title="Cancelled Tickets"
                :value="$cancelledTickets"
                icon-name="x"   
                bg-color="bg-red-500"
                />
        </div>
    @endhasanyrole
        <div class="filters">
            <x-search-bar placeholder="Search residents..." />
        </div>
    <div class="p-6 bg-white shadow-md rounded-lg">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Support Tickets</h2>
                <button x-data @click="$dispatch('open-modal', 'create-ticket')" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition">
                    <x-lucide-plus class="w-4 h-4 mr-2" />
                    <span>File New Ticket</span>
                </button>
        </div>

        <div class="overflow-x-auto mt-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-200">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-l-lg">Ticket Info</th>
                        @hasanyrole('admin|super admin')
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reporter</th>
                        @endrole
                        <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Priority</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Date Opened</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Date Closed</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-r-lg">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <div class="text-xs text-gray-900">{{ $ticket->ticket_type }}</div>
                            </td>
                            @hasanyrole('admin|super admin')
                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-sm text-gray-600">
                                    {{ $ticket->user->first_name }} {{ $ticket->user->last_name }}
                                </td>
                            @endhasanyrole
                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <span class="px-2 py-1 rounded-full text-xs font-bold 
                                    {{ $ticket->priority == 'High' ? 'bg-red-100 text-red-700' : ($ticket->priority == 'Medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                    {{ $ticket->priority }}
                                </span>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <span class="px-2 py-1 rounded-full text-xs font-bold 
                                    {{ $ticket->status == 'Pending' ? 'bg-gray-100 text-gray-700' : ($ticket->status == 'In Progress' ? 'bg-blue-100 text-blue-700' : ($ticket->status == 'Completed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700')) }}">
                                    {{ $ticket->status }}
                                </span>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-sm text-gray-500">
                                @if($ticket->date_created instanceof \Carbon\Carbon)
                                {{ $ticket->date_created->format('M d, Y h:i A') }}
                                @else
                                    {{ \Carbon\Carbon::parse($ticket->date_created)->format('M d, Y h:i A') }}
                                @endif
                            </td>
                           <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-sm text-gray-500">
                                {{-- Display date_done if ticket is Completed OR Cancelled --}}
                                @if($ticket->date_done && in_array($ticket->status, ['Completed', 'Cancelled']))
                                    {{ \Carbon\Carbon::parse($ticket->date_done)->format('M d, Y h:i A') }}
                                @else
                                    <span class="text-gray-300 italic">--</span>
                                @endif
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">
                                <button x-data 
                                        @click="$dispatch('open-modal', 'view-ticket-modal'); $dispatch('fetch-ticket-data', {{ $ticket->id }})"
                                        class="text-blue-600 hover:text-blue-800 text-sm transition">
                                    View Details
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                                No open tickets found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@include('tickets.partials._create-modal')
@include('tickets.partials._details-modal')

@if(session('success'))
            <x-success-modal name="action-success" :show="true">
                {{ session('success') }}
            </x-success-modal>
    @endif
    @if(session('error'))
        <x-error-modal name="action-error" :show="true">
            <div class="text-center">
                <p class="text-gray-600 mb-4">{{ session('error') }}</p>
            </div>
        </x-error-modal>
    @endif

</x-app-layout>