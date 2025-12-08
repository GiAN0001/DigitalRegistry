<x-modal name="delete-confirmation-modal" maxWidth="md" focusable>
    <div 
        x-data="{ 
            // FIX 1: Initialize targetId to a safe, empty string
            targetId: '',
            deleteUrlBase: '{{ url("admin/users") }}', 
            
            init() {
                // FIX 2: Ensure the listener is placed outside init() 
                // This is a redundant check, but safer to use the window modifier.
                // The main listener is moved to the x-on: attribute below.
            }
        }"
        {{-- FIX 3: Listener moved to the div element itself for maximum robustness --}}
        x-on:set-delete-target.window="targetId = $event.detail; $dispatch('open-modal', 'delete-confirmation-modal')"
        class="p-6"
    >
        <h2 class="text-xl font-medium text-red-600 mb-2">
            Confirm Account Deletion
        </h2>
        <p class="text-sm text-gray-600 mb-6">
            Are you sure you want to delete this user account? This action requires password verification.
        </p>

        <form method="POST" x-bind:action="`${deleteUrlBase}/${targetId}`">
            @csrf
            @method('DELETE')

            <h3 class="text-sm font-semibold mb-3">Admin Verification</h3>
            
            <div class="mb-4">
                <x-input-label for="current_password" value="Your Password" />
                <x-text-input 
                    id="current_password" 
                    name="current_password" 
                    type="password" 
                    class="mt-1 block w-full" 
                    placeholder="Enter your current password" 
                />
                {{-- Display error if password check failed --}}
                <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" type="button">
                    Cancel
                </x-secondary-button>
                
                <x-danger-button type="submit">
                    Verify & Delete User
                </x-danger-button>
            </div>
        </form>
    </div>
</x-modal>