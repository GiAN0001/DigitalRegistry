<div id="viewTicketModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50"> {{-- file created by gian --}}
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-semibold text-gray-700">
                <span id="detailType" class="text-blue-600"></span> / Ticket Details
            </h3>
            <button onclick="toggleModal('viewTicketModal')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>

        <div class="mt-4 space-y-5">
            <div class="grid grid-cols-2 gap-4 text-xs bg-gray-50 p-3 rounded-md border">
                <div>
                    <span class="text-gray-400 font-bold uppercase">Filed On</span>
                    <p id="detailFiledDate" class="text-gray-700 font-medium"></p>
                </div>
                <div>
                    <span class="text-gray-400 font-bold uppercase">Resolved On</span>
                    <p id="detailDoneDate" class="text-gray-700 font-medium">--</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-400">Issue Description</label>
                <div id="detailDescription" class="mt-1 p-3 bg-gray-50 rounded border text-gray-700 text-sm whitespace-pre-wrap"></div>
            </div>

            <div class="border-t pt-4">
                <div id="readOnlySection" class="hidden">
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Response / Resolution Detail</label>
                    <div id="detailNotesDisplay" class="p-3 bg-blue-50 border border-blue-100 text-blue-800 rounded italic text-sm mb-4"></div>
                </div>

                @hasanyrole('admin|super admin')
                    <div id="adminActionSection" class="hidden">
                        <form id="resolveForm" method="POST">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Admin Resolution Notes</label>
                                <textarea name="resolution_notes" id="detailNotesInput" rows="3" 
                                        class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500" 
                                        placeholder="Describe the fix...">{{ old('resolution_notes') }}</textarea>
                            </div>
                            
                            <div class="mt-4 flex justify-between">
                                <button type="submit" id="startBtn" class="bg-yellow-500 text-white px-4 py-2 rounded-md text-sm font-bold shadow-sm">
                                    <i class="fas fa-play mr-1"></i> Start Working
                                </button>
                                <button type="submit" id="resolveBtn" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-bold shadow-sm hidden">
                                    <i class="fas fa-check mr-1"></i> Mark as Resolved
                                </button>
                            </div>
                        </form>
                    </div>
                @endhasanyrole

                <div id="userCancelSection" class="hidden border-t border-dashed pt-4 mt-4">
                    <form id="cancelForm" method="POST">
                        @csrf
                        <label class="block text-xs font-bold uppercase text-red-400 mb-1">Reason for Cancellation</label>
                        <textarea name="cancellation_reason" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500" 
                                placeholder="Why are you cancelling?">{{ old('cancellation_reason') }}</textarea>
                        
                        {{-- FIX: Access the 'details' error bag specifically --}}
                        @if($errors->details->has('cancellation_reason'))
                            <p class="text-sm text-red-600 mt-2">{{ $errors->details->first('cancellation_reason') }}</p>
                        @endif

                        <button type="submit" class="w-full mt-2 bg-red-600 text-white px-4 py-2 rounded-md text-sm font-bold">Cancel My Ticket</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>