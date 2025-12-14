<x-app-layout>
    {{--
        CORRECTED GRID CONTAINER
        - Changed 'grid-cols-13' to 'grid-cols-12' (Standard Tailwind).
        - This ensures the layout works without custom configuration.
    --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 pb-10 mt-6">

        {{-- PAGE HEADER (Spans all 12 columns) --}}
        <div class="col-span-1 md:col-span-12 mb-2">
            <h2 class="text-2xl font-bold text-slate-800">Account Settings</h2>
            <p class="text-sm text-slate-500">Manage your profile information and account security.</p>
        </div>

        {{-- RIGHT COLUMN: FORMS (Spans 8 of 12 columns) --}}
        {{-- Changed from col-span-9 to col-span-8 to fit the 12-column grid --}}
        <div class="col-span-1 md:col-span-8 space-y-6">

            {{-- Personal Information Form --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Personal Information</h3>
                    <p class="text-sm text-slate-500">Update your personal details and contact info.</p>
                </div>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">First Name</label>
                            <input type="text" name="first_name" value="{{ Auth::user()->first_name }}"
                                   class="w-full rounded-lg border-slate-200 bg-slate-50 text-slate-800 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition shadow-sm">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Last Name</label>
                            <input type="text" name="last_name" value="{{ Auth::user()->last_name }}"
                                   class="w-full rounded-lg border-slate-200 bg-slate-50 text-slate-800 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition shadow-sm">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Middle Name</label>
                            <input type="text" name="middle_name" value="{{ Auth::user()->middle_name }}"
                                   class="w-full rounded-lg border-slate-200 bg-slate-50 text-slate-800 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition shadow-sm">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Extension</label>
                            <input type="text" name="extension" value="{{ Auth::user()->extension }}" placeholder="e.g. Jr, III"
                                   class="w-full rounded-lg border-slate-200 bg-slate-50 text-slate-800 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition shadow-sm">
                        </div>
                        <div class="md:col-span-2 space-y-1">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                                </div>
                                <input type="email" name="email" value="{{ Auth::user()->email }}"
                                       class="w-full pl-10 rounded-lg border-slate-200 bg-slate-50 text-slate-800 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-50">
                        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow-sm flex items-center gap-2">
                            <span>Save Details</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Security / Password Form --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Security</h3>
                    <p class="text-sm text-slate-500">Ensure your account is using a long, random password to stay secure.</p>
                </div>

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5 max-w-xl">
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Current Password</label>
                            <input type="password" name="current_password"
                                   class="w-full rounded-lg border-slate-200 bg-slate-50 text-slate-800 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition shadow-sm">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">New Password</label>
                                <input type="password" name="password"
                                       class="w-full rounded-lg border-slate-200 bg-slate-50 text-slate-800 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition shadow-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Confirm Password</label>
                                <input type="password" name="password_confirmation"
                                       class="w-full rounded-lg border-slate-200 bg-slate-50 text-slate-800 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6 mt-2">
                        <button type="submit" class="bg-white border border-slate-300 text-slate-700 px-5 py-2 rounded-lg text-sm font-medium hover:bg-slate-50 transition shadow-sm">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

        </div>
        {{-- LEFT COLUMN: IDENTITY CARD (Spans 4 of 12 columns) --}}
        <div class="col-span-1 md:col-span-4 space-y-6">

            {{-- Profile Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden relative">
                <div class="h-24 bg-gradient-to-r from-blue-500 to-blue-600"></div>
                <div class="px-6 pb-6 text-center relative">
                    <div class="relative -mt-12 inline-block">
                        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->first_name }}+{{ Auth::user()->last_name }}&color=7F9CF5&background=EBF4FF&size=128"
                             class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md mx-auto bg-white"
                             alt="Profile">
                    </div>
                    <div class="mt-3">
                        <h3 class="text-xl font-bold text-slate-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h3>
                        <div class="flex items-center justify-center gap-2 mt-1">
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                {{ Auth::user()->barangayRole->name ?? 'Staff' }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-6 border-t border-slate-50 pt-4 grid grid-cols-2 gap-4 text-center">
                        <div>
                            <span class="block text-xs text-slate-400 uppercase tracking-wider font-semibold">Joined</span>
                            <span class="text-sm font-medium text-slate-700">{{ Auth::user()->created_at->format('M Y') }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-slate-400 uppercase tracking-wider font-semibold">Status</span>
                            <span class="text-sm font-medium text-green-600 flex items-center justify-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Security Tip Widget --}}
            <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <div>
                        <h4 class="text-sm font-semibold text-blue-800">Security Tip</h4>
                        <p class="text-xs text-blue-600 mt-1">For your safety, logging out after each session is recommended.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
