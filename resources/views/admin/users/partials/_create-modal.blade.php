<x-modal name="create-user-modal" maxWidth="max-w-4xl" focusable>
    <div class="p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Create New User</h2>

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <h3 class="text-lg font-bold mb-4 text-blue-700">1. Personal & Account Details</h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                <div>
                    <x-input-label for="first_name" value="First Name" />
                    <x-text-input id="first_name" name="first_name" type="text" class="mt-1 w-full h-10"/>
                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="last_name">
                        <span class="text-red-600">*</span> Last Name
                    </x-input-label>
                    <x-text-input id="last_name" name="last_name" type="text" class="mt-1 w-full h-10" :value="old('last_name')" />
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="middle_name" value="Middle Name" />
                    <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 w-full h-10" :value="old('middle_name')" />
                    <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="username">
                        <span class="text-red-600">*</span> Username
                    </x-input-label>
                    <x-text-input id="username" name="username" type="text" class="mt-1 w-full h-10" :value="old('username')" />
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email">
                        <span class="text-red-600">*</span> Email
                    </x-input-label>
                    <x-text-input id="email" name="email" type="email" class="mt-1 w-full h-10" :value="old('email')" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="contact">
                        <span class="text-red-600">*</span> Contact No.
                    </x-input-label>
                    <x-text-input id="contact" name="contact" type="text" class="mt-1 w-full h-10" :value="old('contact')" />
                    <x-input-error :messages="$errors->get('contact')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="role">
                        <span class="text-red-600">*</span> System Role
                    </x-input-label>

                    @php $roles = Spatie\Permission\Models\Role::all(); @endphp

                    <select id="role" name="role" class="mt-1 w-full h-10 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select System Role</option>

                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>

                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="barangay_role_id">
                        <span class="text-red-600">*</span> Barangay Role (Job Title)
                    </x-input-label>

                    @php $barangayRoles = App\Models\BarangayRole::all(); @endphp

                    <select id="barangay_role_id" name="barangay_role_id" class="mt-1 w-full h-10 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="" disabled {{ old('barangay_role_id') ? '' : 'selected' }}>Select Job Title</option>

                        @foreach($barangayRoles as $job)
                            <option value="{{ $job->id }}" {{ old('barangay_role_id') == $job->id ? 'selected' : '' }}>
                                {{ $job->name }}
                            </option>
                        @endforeach
                    </select>

                    <x-input-error :messages="$errors->get('barangay_role_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="status" value="Account Status" />
                    <select id="status" name="status" class="mt-1 w-full h-10 border-gray-300 rounded-md shadow-sm">
                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password">
                        <span class="text-red-600">*</span> Password
                    </x-input-label>
                    <x-text-input id="password" name="password" type="password" class="mt-1 w-full h-10" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Confirm Password" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 w-full h-10" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>

                <x-primary-button class="ms-3" type="submit">
                    {{ __('Create User') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>
