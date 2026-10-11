<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Role -->
        <div class="mt-4">
            <x-input-label :value="__('I am registering as a...')" />
            <div class="mt-2 space-y-2">
                <label class="flex items-center">
                    <input type="radio" name="role" value="customer"
                        class="text-indigo-600 focus:ring-indigo-500"
                        {{ old('role', 'customer') === 'customer' ? 'checked' : '' }}>
                    <span class="ms-2 text-sm text-gray-600">
                        {{ __('Customer — I need to book a repair or maintenance service') }}
                    </span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="role" value="technician"
                        class="text-indigo-600 focus:ring-indigo-500"
                        {{ old('role') === 'technician' ? 'checked' : '' }}>
                    <span class="ms-2 text-sm text-gray-600">
                        {{ __('Technician — I provide repair or maintenance services') }}
                    </span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
            <p class="mt-1 text-xs text-gray-400">
                {{ __('Technician accounts require admin verification before they appear to customers.') }}
            </p>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
