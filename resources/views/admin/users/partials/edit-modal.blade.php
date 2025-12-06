<x-modal name="edit-user-modal" maxWidth="max-w-4xl" focusable>
    
    <div 
        x-data="{ 
            user: {}, 
            // Blade generates the static base URL (e.g., /admin/users) once.
            updateUrlBase: '{{ url("admin/users") }}'
        }"
        
        {{-- CRITICAL: Listener moved directly to the div to capture the data immediately --}}
        x-on:edit-user-data.window="user = $event.detail;" 
        class="p-6"
    >
        
        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">
            Edit User: <span x-text="user.username ?? 'Loading...'"></span>
        </h2>

        <form method="POST" x-bind:action="updateUrlBase + '/' + user.id">
            @csrf
            @method('PUT') 
            
            <h3 class="text-lg font-bold mb-4 text-blue-700">1. Personal & Account Details</h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                <div>
                    <x-input-label for="first_name" value="First Name" />
                    <x-text-input id="first_name" name="first_name" type="text" class="mt-1 w-full h-10" x-model="user.first_name" />
                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="last_name" value="Last Name" />
                    <x-text-input id="last_name" name="last_name" type="text" class="mt-1 w-full h-10" x-model="user.last_name" />
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="middle_name" value="Middle Name" />
                    <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 w-full h-10" x-model="user.middle_name" />
                    <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="username" value="Username" />
                    <x-text-input id="username" name="username" type="text" class="mt-1 w-full h-10" x-model="user.username" />
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 w-full h-10" x-model="user.email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="contact" value="Contact No." />
                    <x-text-input id="contact" name="contact" type="text" class="mt-1 w-full h-10" x-model="user.contact" />
                    <x-input-error :messages="$errors->get('contact')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="role" value="System Role" />
                    <x-form-select 
                        model="Spatie\Permission\Models\Role" 
                        column="name" 
                        value-column="id" 
                        placeholder="Select System Role"
                        name="role"
                        class="mt-1 h-10"
                        {{-- Binds to the ID of the first role in the user's roles array --}}
                        x-bind:value="user.roles && user.roles.length ? user.roles[0].id : ''"
                    />
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="barangay_role_id" value="Barangay Role (Job Title)" />
                    <x-form-select 
                        model="App\Models\BarangayRole" 
                        column="name" 
                        value-column="id" 
                        placeholder="Select Job Title"
                        name="barangay_role_id"
                        class="mt-1 h-10"
                        {{-- Binds directly to the foreign key --}}
                        x-bind:value="user.barangay_role_id ?? ''"
                    />
                    <x-input-error :messages="$errors->get('barangay_role_id')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="status" value="Account Status" />
                    <select id="status" name="status" x-bind:value="user.status ?? '1'" class="mt-1 w-full h-10 border-gray-300 rounded-md shadow-sm">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" value="New Password (Optional)" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 w-full h-10" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="password_confirmation" value="Confirm New Password" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 w-full h-10" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')" type="button">Cancel</x-secondary-button>
                <x-primary-button class="ms-3" type="submit">
                    {{ __('Save Changes') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>