<x-guest-layout>
    <div class="login-form-card h-[55vh] w-[65%] px-[40px] flex justify-center items-center flex-col bg-[#FAFAFA] shadow-md rounded-[16px] gap-[32px]">
        
        <div class="login-logo flex justify-center">
            <img src="{{ asset('images/logo-namayan.png') }}" alt="1N Namayan Logo" class="w-[120px] h-[120px]">
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="w-full">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-[16px]">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-[16px]">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="mt-[32px]">
                <x-primary-button class="h-[36px] w-[100%] justify-center">
                    {{ __('Reset Password') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>