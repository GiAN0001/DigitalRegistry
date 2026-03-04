<x-guest-layout>
    <div class="flex flex-col items-center w-full">

        {{-- Success Alert: Positioned outside the card to prevent design distortion --}}
        @if (session('status'))
            <div class="w-[65%] mb-6">
                <x-auth-session-status :status="session('status')" />
            </div>
        @endif

        {{-- The Login Card: Structure preserved from your registry design --}}
        <div class="login-form-card h-[55vh] w-[65%] px-[40px] flex justify-center items-center flex-col bg-[#FAFAFA] shadow-md rounded-[16px] gap-[32px]">

            <div class="login-logo flex justify-center">
                <img src="{{ asset('images/logo-namayan.png') }}" alt="1N Namayan Logo" class="w-[120px] h-[120px]">
            </div>

            <form method="POST" action="{{ route('login') }}" class="w-full">
                @csrf

                <div>
                    <x-input-label for="username" :value="__('Username')" />
                    <x-text-input id="username" class="block mt-1 w-full"
                                  type="text"
                                  name="username"
                                  :value="old('username')"
                                  required
                                  autofocus
                                  autocomplete="username" />
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>

                <div class="mt-[16px]">
                    <x-input-label for="password" :value="__('Password')" />

                    {{-- [NEW] Wrapped the input in a relative div --}}
                    <div class="relative mt-1">
                        {{-- Added pr-10 to the class to make room for the eye icon --}}
                        <x-text-input id="password" class="block w-full pr-10"
                                        type="password"
                                        name="password"
                                        required
                                        autocomplete="current-password" />

                        {{-- [NEW] Show/Hide Password Toggle Button --}}
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 focus:outline-none">
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg id="eye-off-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                <line x1="2" x2="22" y1="2" y2="22"/>
                            </svg>
                        </button>
                    </div>

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-blue-700 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <div class="mt-[32px]">
                    <x-primary-button class="h-[36px] w-[100%] justify-center">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    {{-- [NEW] Script for toggling password visibility --}}
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                eyeIcon.style.display = 'block';
                eyeOffIcon.style.display = 'none';
            }
        }
    </script>
</x-guest-layout>
