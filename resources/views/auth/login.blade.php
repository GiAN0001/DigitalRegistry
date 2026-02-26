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
                    <x-text-input id="password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password" />
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
</x-guest-layout>