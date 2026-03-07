<x-modal name="create-ticket" maxWidth="lg" focusable>
    <div class="p-5" 
         x-data
         x-init="@if($errors->default->hasAny(['ticket_type', 'description', 'priority'])) $dispatch('open-modal', 'create-ticket') @endif">
        
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-semibold text-gray-700">File a Ticket</h3>
            <button x-on:click="$dispatch('close')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('tickets.store') }}" method="POST" class="mt-4">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Issue Type</label>
                    <select name="ticket_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="" disabled selected>Select the type of issue</option>
                        <option value="PASSWORD_RESET" {{ old('ticket_type') == 'PASSWORD_RESET' ? 'selected' : '' }}>Password Change Request</option>
                        <option value="DATA_CORRECTION" {{ old('ticket_type') == 'DATA_CORRECTION' ? 'selected' : '' }}>Data Correction</option>
                        <option value="SYSTEM_BUG" {{ old('ticket_type') == 'SYSTEM_BUG' ? 'selected' : '' }}>Technical Bug / System Error</option>
                        <option value="ACCESS_DENIED" {{ old('ticket_type') == 'ACCESS_DENIED' ? 'selected' : '' }}>Permission / Access Issue</option>
                        <option value="UI_GLITCH" {{ old('ticket_type') == 'UI_GLITCH' ? 'selected' : '' }}>Visual Glitch / Display Error</option>
                        <option value="OTHER" {{ old('ticket_type') == 'OTHER' ? 'selected' : '' }}>Other Concerns</option>
                    </select>
                </div>
                <x-input-error :messages="$errors->get('ticket_type')" class="mt-2" />

                <div>
                    <label class="block text-sm font-medium text-gray-700">Priority Level</label>
                    <div class="flex space-x-6 mt-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="priority" value="Low" class="text-green-600 focus:ring-green-500" {{ old('priority', 'Low') == 'Low' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-600">Low</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="priority" value="Medium" class="text-yellow-600 focus:ring-yellow-500" {{ old('priority') == 'Medium' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-600">Medium</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="priority" value="High" class="text-red-600 focus:ring-red-500" {{ old('priority') == 'High' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-600">High</span>
                        </label>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('priority')" class="mt-2" />

                <div>
                    <label class="block text-sm font-medium text-gray-700">Detailed Description</label>
                    <textarea name="description" rows="4"  
                              placeholder="Please provide details about the issue..."
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                </div>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" x-on:click="$dispatch('close')" 
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
</x-modal>