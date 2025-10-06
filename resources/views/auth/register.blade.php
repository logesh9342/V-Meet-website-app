<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gradient-to-br from-[#e6f0fa] via-white to-[#dbeafe]">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-10">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-extrabold text-[#2563eb] tracking-wide">V-Meet Registration</h1>
                <p class="text-lg text-gray-500 mt-2">Create your account</p>
            </div>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-4">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div class="mb-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div class="mb-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div class="mb-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
                <x-primary-button class="w-full py-2 text-lg font-semibold bg-[#2563eb] hover:bg-[#1d4ed8] text-white rounded-lg">
                    {{ __('Register') }}
                </x-primary-button>
            </form>
            <div class="mt-8 text-center">
                <a class="inline-block text-[#2563eb] font-bold hover:underline" href="{{ route('login') }}">
                    Already registered?
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
