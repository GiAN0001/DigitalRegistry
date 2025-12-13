<x-modal name="edit-user-modal" maxWidth="max-w-2xl" focusable>
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

            <div class="flex flex-col gap-4">

                <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                    <div>
                        <x-input-label for="first_name" class="text-slate-700">
                            <span class="text-red-600">*</span> First Name
                        </x-input-label>
                        <x-text-input id="first_name" name="first_name" type="text" class="mt-1 w-full h-10" x-model="user.first_name" />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="last_name" class="text-slate-700">
                            <span class="text-red-600">*</span> Last Name
                        </x-input-label>
                        <x-text-input id="last_name" name="last_name" type="text" class="mt-1 w-full h-10" x-model="user.last_name" />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="middle_name" value="Middle Name" class="text-slate-700" />
                        <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 w-full h-10" x-model="user.middle_name" />
                        <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="role" class="text-slate-700">
                            <span class="text-red-600">*</span> System Role
                        </x-input-label>

                        @php $roles = Spatie\Permission\Models\Role::all(); @endphp

                        <select
                            id="role"
                            name="role"
                            class="text-slate-500 mt-1 w-full h-10 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            x-bind:value="user.roles && user.roles.length ? user.roles[0].id : ''"
                        >
                            <option value="" disabled>Select System Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>

                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="barangay_role_id" class="text-slate-700">
                            <span class="text-red-600">*</span> Barangay Role (Job Title)
                        </x-input-label>

                        @php $barangayRoles = App\Models\BarangayRole::all(); @endphp

                        <select
                            id="barangay_role_id"
                            name="barangay_role_id"
                            class="text-slate-500 mt-1 w-full h-10 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            x-bind:value="user.barangay_role_id ?? ''"
                        >
                            <option value="" disabled>Select Job Title</option>
                            @foreach($barangayRoles as $job)
                                <option value="{{ $job->id }}">
                                    {{ $job->name }}
                                </option>
                            @endforeach
                        </select>

                        <x-input-error :messages="$errors->get('barangay_role_id')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="contact" class="text-slate-700">
                            <span class="text-red-600">*</span> Contact No.
                        </x-input-label>
                        <x-text-input id="contact" name="contact" type="text" class="mt-1 w-full h-10" x-model="user.contact" />
                        <x-input-error :messages="$errors->get('contact')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" class="text-slate-700">
                            <span class="text-red-600">*</span> Email
                        </x-input-label>
                        <x-text-input id="email" name="email" type="email" class="mt-1 w-full h-10" x-model="user.email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="username" class="text-slate-700">
                            <span class="text-red-600">*</span> Username
                        </x-input-label>
                        <x-text-input id="username" name="username" type="text" class="mt-1 w-full h-10" x-model="user.username" />
                        <x-input-error :messages="$errors->get('username')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="status" value="Account Status" class="text-slate-700" />
                        <select
                            id="status"
                            name="status"
                            class="mt-1 w-full h-10 border-gray-300 rounded-md shadow-sm"
                            x-bind:value="user.status ?? '1'"
                        >
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="password" value="New Password (Optional)" class="text-slate-700" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 w-full h-10" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" value="Confirm New Password" class="text-slate-700" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 w-full h-10" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
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
