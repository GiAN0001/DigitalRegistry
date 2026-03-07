<x-modal name="view-ticket-modal" maxWidth="2xl" focusable>
    <div class="p-5"
         x-data="{
             ticket: null,
             loading: false,
             isAdmin: {{ auth()->user()->hasanyrole('admin|super admin') ? 'true' : 'false' }},
             currentUserId: {{ auth()->id() }},
             
             resetForm() {
                 this.ticket = null;
                 this.loading = false;
             },

             async fetchTicket(id) {
                 this.resetForm();
                 this.loading = true;
                 
                 try {
                     const response = await fetch(`/tickets/${id}`);
                     this.ticket = await response.json();
                     
                     if (!this.isAdmin && this.ticket.user_id === this.currentUserId && this.ticket.is_seen_by_user == 0) {
                         fetch(`/tickets/${this.ticket.id}/mark-seen`, {
                             method: 'POST',
                             headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                         });
                     }
                 } catch (error) {
                     console.error('Failed to fetch ticket:', error);
                 } finally {
                     this.loading = false;
                 }
             },

             formatDate(dateString) {
                 if (!dateString) return '--';
                 return new Date(dateString).toLocaleDateString('en-US', {
                     month: 'short', day: 'numeric', year: 'numeric', 
                     hour: 'numeric', minute: '2-digit', hour12: true 
                 });
             }
         }"
         x-on:fetch-ticket-data.window="fetchTicket($event.detail)"
         @if($errors->details->hasAny(['resolution_notes', 'cancellation_reason']))
             x-init="
                $dispatch('open-modal', 'view-ticket-modal');
                fetchTicket({{ session('error_ticket_id') }});
             "
         @endif
    >
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-semibold text-gray-700">
                <span x-text="ticket ? ticket.ticket_type.replace(/_/g, ' ') : ''" class="text-blue-600"></span> / Ticket Details
            </h3>
            <button x-on:click="$dispatch('close')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>

        <div x-show="loading" class="flex items-center justify-center py-16">
            <x-lucide-loader-2 class="w-10 h-10 animate-spin text-blue-600" />
        </div>

        <div x-show="!loading && ticket" class="mt-4 space-y-5">
            <div class="grid grid-cols-2 gap-4 text-xs bg-gray-50 p-3 rounded-md border">
                <div>
                    <span class="text-gray-400 font-bold uppercase">Filed On</span>
                    <p class="text-gray-700 font-medium" x-text="formatDate(ticket?.date_created)"></p>
                </div>
                <div>
                    <span class="text-gray-400 font-bold uppercase">Status</span>
                    <p class="text-gray-700 font-medium" x-text="ticket?.status"></p>
                </div>
                <div>
                    <span class="text-gray-400 font-bold uppercase">Filed by</span>
                    <p class="text-gray-700 font-medium" x-text="ticket?.user?.first_name + ' ' + ticket?.user?.last_name"></p>
                </div>
                <div>
                    <span class="text-gray-400 font-bold uppercase">Resolved On</span>
                    <p class="text-gray-700 font-medium" x-text="formatDate(ticket?.date_done)"></p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-400">Issue Description</label>
                <div class="mt-1 p-3 bg-gray-50 rounded border text-gray-700 text-sm whitespace-pre-wrap" x-text="ticket?.description"></div>
            </div>

            <div class="border-t pt-4">
                <template x-if="ticket?.status === 'Completed' || ticket?.status === 'Cancelled' || (!isAdmin && ticket?.status !== 'Completed' && ticket?.status !== 'Cancelled')">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Response / Resolution Detail</label>
                        <div class="p-3 bg-blue-50 border border-blue-100 text-blue-800 rounded italic text-sm mb-4" 
                             x-text="ticket?.resolution_notes || (ticket?.status === 'Completed' || ticket?.status === 'Cancelled' ? 'Resolved.' : 'The system administrator has not yet responded to this ticket.')">
                        </div>
                    </div>
                </template>

                @hasanyrole('admin|super admin')
                    <template x-if="ticket?.status === 'Pending' || ticket?.status === 'In Progress'">
                        <div>
                            <form x-bind:action="ticket.status === 'Pending' ? `/tickets/${ticket.id}/start` : `/tickets/${ticket.id}/resolve`" method="POST">
                                @csrf
                                <template x-if="ticket.status === 'In Progress'">
                                    <div>
                                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Admin Resolution Notes</label>
                                        <textarea name="resolution_notes" rows="3" 
                                                class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500" 
                                                placeholder="Describe the fix...">{{ old('resolution_notes') }}</textarea>
                                        <x-input-error :messages="$errors->details->get('resolution_notes')" class="mt-2" />
                                    </div>
                                </template>
                                
                                <div class="mt-4 flex justify-between">
                                    <template x-if="ticket.status === 'Pending'">
                                        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-md text-sm font-bold shadow-sm">
                                            <i class="fas fa-play mr-1"></i> Start Working
                                        </button>
                                    </template>
                                    <template x-if="ticket.status === 'In Progress'">
                                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-bold shadow-sm">
                                            <i class="fas fa-check mr-1"></i> Mark as Resolved
                                        </button>
                                    </template>
                                </div>
                            </form>
                        </div>
                    </template>
                @endhasanyrole

                <template x-if="!isAdmin && ticket?.user_id === currentUserId && ticket?.status === 'Pending'">
                    <div class="border-t border-dashed pt-4 mt-4">
                        <form x-bind:action="`/tickets/${ticket.id}/cancel`" method="POST">
                            @csrf
                            <label class="block text-xs font-bold uppercase text-red-400 mb-1">Reason for Cancellation</label>
                            <textarea name="cancellation_reason" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500" 
                                    placeholder="Why are you cancelling?">{{ old('cancellation_reason') }}</textarea>
                            
                            @if($errors->details->has('cancellation_reason'))
                                <p class="text-sm text-red-600 mt-2">{{ $errors->details->first('cancellation_reason') }}</p>
                            @endif

                            <button type="submit" class="w-full mt-2 bg-red-600 text-white px-4 py-2 rounded-md text-sm font-bold">Cancel My Ticket</button>
                        </form>
                    </div>
                </template>
            </div>
        </div>
    </div>
</x-modal>