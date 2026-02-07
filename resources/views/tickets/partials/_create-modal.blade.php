<div id="createTicketModal"  
     class="{{ ($errors->default->has('ticket_type') || $errors->default->has('description')) ? '' : 'hidden' }} fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50"> {{-- file created by gian --}}
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-semibold text-gray-700">File a Ticket</h3>
            <button onclick="toggleModal('createTicketModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('tickets.store') }}" method="POST" class="mt-4">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Issue Type</label>
                    <select name="ticket_type"  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="" disabled selected>Select the type of issue</option>
                        <option value="PASSWORD_RESET">Password Change Request</option>
                        <option value="DATA_CORRECTION">Data Correction</option>
                        <option value="SYSTEM_BUG">Technical Bug / System Error</option>
                        <option value="ACCESS_DENIED">Permission / Access Issue</option>
                        <option value="UI_GLITCH">Visual Glitch / Display Error</option>
                        <option value="OTHER">Other Concerns</option>
                    </select>
                </div>
                <x-input-error :messages="$errors->get('ticket_type')" class="mt-2" />

                <div>
                    <label class="block text-sm font-medium text-gray-700">Priority Level</label>
                    <div class="flex space-x-6 mt-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="priority" value="Low" class="text-green-600 focus:ring-green-500" checked>
                            <span class="ml-2 text-sm text-gray-600">Low</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="priority" value="Medium" class="text-yellow-600 focus:ring-yellow-500">
                            <span class="ml-2 text-sm text-gray-600">Medium</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="priority" value="High" class="text-red-600 focus:ring-red-500">
                            <span class="ml-2 text-sm text-gray-600">High</span>
                        </label>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('priority')" class="mt-2" />

                <div>
                    <label class="block text-sm font-medium text-gray-700">Detailed Description</label>
                    <textarea name="description" rows="4"  
                              placeholder="Please provide details about the issue..."
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="toggleModal('createTicketModal')" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition shadow-sm">
                    Submit Ticket
                </button>
            </div>
        </form>
    </div>
</div>