<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gradient-to-br from-[#e6f0fa] via-white to-[#dbeafe]">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-10">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-extrabold text-[#2563eb] tracking-wide">V-Meet Login</h1>
                <p class="text-lg text-gray-500 mt-2">Sign in to your account</p>
            </div>
            <x-auth-session-status class="mb-4" :status="session('status')" />
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div class="mb-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div class="flex items-center justify-between mb-6">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#2563eb] shadow-sm focus:ring-[#2563eb]" name="remember">
                        <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a class="text-sm text-[#2563eb] hover:underline" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>
                <x-primary-button class="w-full py-2 text-lg font-semibold bg-[#2563eb] hover:bg-[#1d4ed8] text-white rounded-lg">
                    {{ __('Log in') }}
                </x-primary-button>
            </form>
            <div class="mt-8 text-center">
                @if (Route::has('register'))
                    <a class="inline-block text-[#2563eb] font-bold hover:underline" href="{{ route('register') }}">
                        New employee? Register here
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-guest-layout>
