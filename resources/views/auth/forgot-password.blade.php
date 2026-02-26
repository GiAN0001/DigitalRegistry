<x-guest-layout>
    <div class="login-form-card h-[55vh] w-[65%] px-[40px] flex justify-center items-center flex-col bg-[#FAFAFA] shadow-md rounded-[16px] gap-[32px]">
        
        <div class="login-logo flex justify-center">
            <img src="{{ asset('images/logo-namayan.png') }}" alt="1N Namayan Logo" class="w-[120px] h-[120px]">
        </div>

        <div class="w-full">
            <div class="mb-4 text-sm text-gray-600 text-center">
                {{ __('Forgot your password? Enter your email and we will send you a reset link that expires in 15 minutes.') }}
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="w-full">
                @csrf
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="mt-[32px]">
                    <x-primary-button class="h-[36px] w-[100%] justify-center">
                        {{ __('Email Password Reset Link') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>