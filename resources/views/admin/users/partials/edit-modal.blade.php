<x-modal name="edit-user-modal" maxWidth="max-w-2xl" focusable>
    <div
        x-data="{
            user: {
                id: '{{ old('user_id', '') }}'
            },
            loading: false,
            updateUrlBase: '{{ url('admin/users') }}',
            
            async fetchUser(id) {
                this.loading = true;
                try {
                    const response = await fetch(`${this.updateUrlBase}/${id}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    
                    this.user = {
                        ...data,
                    };
                } catch (error) {
                    console.error('Failed to fetch user:', error);
                } finally {
                    this.loading = false;
                }
            }
        }"
        {{-- AUTO-OPEN: Triggers if any 'user_id' validation errors exist in the session --}}
        x-init="
            @if($errors->any() && old('user_id'))
                $dispatch('open-modal', 'edit-user-modal');
                user.id = '{{ old('user_id') }}';
            @endif
        "
        x-on:edit-user-data.window="fetchUser($event.detail)"
        class="p-6 relative"
    >
        {{-- Loading Spinner Overlay --}}
        <div x-show="loading" class="absolute inset-0 bg-white/80 flex items-center justify-center z-50">
            <x-lucide-loader-2 class="w-10 h-10 animate-spin text-blue-600" />
        </div>

        <div class="flex justify-between items-center border-b pb-2 mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                Edit User: <span class="text-blue-600" x-text="user.username || user.first_name || 'Loading...'"></span>
            </h2>
            <button x-on:click.prevent="$dispatch('close')" type="button" class="text-gray-400 hover:text-gray-600">
                <x-lucide-x class="w-6 h-6" />
            </button>
        </div>

        <form method="POST" x-bind:action="updateUrlBase + '/' + user.id" x-show="!loading">
            @csrf
            @method('PUT')

            {{-- Store the ID in session so the form knows which URL to use after a reload on error --}}
            <input type="hidden" name="user_id" x-model="user.id">

            <h3 class="text-lg font-bold mb-4 text-blue-700">1. Personal & Account Details</h3>

            <div class="flex flex-col gap-4">

                <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                    <div>
                        <x-input-label for="edit_first_name" class="text-slate-700">
                            <span class="text-red-600">*</span> First Name
                        </x-input-label>
                        <x-text-input 
                            id="edit_first_name" 
                            name="first_name" 
                            type="text" 
                            class="mt-1 w-full h-10" 
                            x-model="user.first_name" 
                            value="{{ old('first_name') }}"
                            x-init="if($el.value) user.first_name = $el.value"
                        />
                        <x-input-error :messages="!old('user_id') ? [] : $errors->get('first_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="edit_last_name" class="text-slate-700">
                            <span class="text-red-600">*</span> Last Name
                        </x-input-label>
                        <x-text-input 
                            id="edit_last_name" 
                            name="last_name" 
                            type="text" 
                            class="mt-1 w-full h-10" 
                            x-model="user.last_name" 
                            value="{{ old('last_name') }}"
                            x-init="if($el.value) user.last_name = $el.value"
                        />
                        <x-input-error :messages="!old('user_id') ? [] : $errors->get('last_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="edit_middle_name" value="Middle Name" class="text-slate-700" />
                        <x-text-input 
                            id="edit_middle_name" 
                            name="middle_name" 
                            type="text" 
                            class="mt-1 w-full h-10" 
                            x-model="user.middle_name" 
                            value="{{ old('middle_name') }}"
                            x-init="if($el.value) user.middle_name = $el.value"
                        />
                        <x-input-error :messages="!old('user_id') ? [] : $errors->get('middle_name')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="edit_role" class="text-slate-700">
                            <span class="text-red-600">*</span> System Role
                        </x-input-label>

                        @php $roles = Spatie\Permission\Models\Role::all(); @endphp

                        <select
                            id="edit_role"
                            name="role"
                            class="text-slate-500 mt-1 w-full h-10 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            x-model="user.role_name"
                            x-init="let oldVal = '{{ old('role') }}'; if(oldVal) user.role_name = oldVal"
                        >
                            <option value="" disabled>Select System Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>

                        <x-input-error :messages="!old('user_id') ? [] : $errors->get('role')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="edit_barangay_role_id" class="text-slate-700">
                            <span class="text-red-600">*</span> Barangay Role (Job Title)
                        </x-input-label>

                        @php $barangayRoles = App\Models\BarangayRole::all(); @endphp

                        <select
                            id="edit_barangay_role_id"
                            name="barangay_role_id"
                            class="text-slate-500 mt-1 w-full h-10 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            x-model="user.barangay_role_id"
                            x-init="let oldVal = '{{ old('barangay_role_id') }}'; if(oldVal) user.barangay_role_id = oldVal"
                        >
                            <option value="" disabled>Select Job Title</option>
                            @foreach($barangayRoles as $job)
                                <option value="{{ $job->id }}">
                                    {{ $job->name }}
                                </option>
                            @endforeach
                        </select>

                        <x-input-error :messages="!old('user_id') ? [] : $errors->get('barangay_role_id')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="edit_contact" class="text-slate-700">
                            <span class="text-red-600">*</span> Contact No.
                        </x-input-label>
                        <x-text-input 
                            id="edit_contact" 
                            name="contact" 
                            type="text" 
                            class="mt-1 w-full h-10" 
                            x-model="user.contact" 
                            value="{{ old('contact') }}"
                            x-init="if($el.value) user.contact = $el.value"
                        />
                        <x-input-error :messages="!old('user_id') ? [] : $errors->get('contact')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="edit_email" class="text-slate-700">
                            <span class="text-red-600">*</span> Email
                        </x-input-label>
                        <x-text-input 
                            id="edit_email" 
                            name="email" 
                            type="email" 
                            class="mt-1 w-full h-10" 
                            x-model="user.email" 
                            value="{{ old('email') }}"
                            x-init="if($el.value) user.email = $el.value"
                        />
                        <x-input-error :messages="!old('user_id') ? [] : $errors->get('email')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="edit_username" class="text-slate-700">
                            <span class="text-red-600">*</span> Username
                        </x-input-label>
                        <x-text-input 
                            id="edit_username" 
                            name="username" 
                            type="text" 
                            class="mt-1 w-full h-10" 
                            x-model="user.username" 
                            value="{{ old('username') }}"
                            x-init="if($el.value) user.username = $el.value"
                        />
                        <x-input-error :messages="!old('user_id') ? [] : $errors->get('username')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="edit_status" value="Account Status" class="text-slate-700" />
                        <select
                            id="edit_status"
                            name="status"
                            class="mt-1 w-full h-10 border-gray-300 rounded-md shadow-sm"
                            x-model="user.status"
                            x-init="let oldVal = '{{ old('status') }}'; if(oldVal !== '') user.status = oldVal"
                        >
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <x-input-error :messages="!old('user_id') ? [] : $errors->get('status')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="edit_password" value="New Password (Optional)" class="text-slate-700" />
                        <x-text-input id="edit_password" name="password" type="password" class="mt-1 w-full h-10" />
                        <x-input-error :messages="!old('user_id') ? [] : $errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="edit_password_confirmation" value="Confirm New Password" class="text-slate-700" />
                        <x-text-input id="edit_password_confirmation" name="password_confirmation" type="password" class="mt-1 w-full h-10" />
                        <x-input-error :messages="!old('user_id') ? [] : $errors->get('password_confirmation')" class="mt-2" />
                    </div>
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
