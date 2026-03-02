<x-guest-layout>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <div class="login-container">
        <h1 class="auth-title">Art de la Pierre</h1>
        <p class="auth-subtitle">Accès Administration</p>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="auth-form">
            @csrf

            <div class="form-group">
                <label for="email" class="section-title-small">Email Professionnel</label>
                <input id="email" class="block mt-1 w-full custom-input" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
            </div>

            <div class="mt-6 form-group">
                <label for="password" class="section-title-small">Mot de Passe</label>
                <input id="password" class="block mt-1 w-full custom-input"
                       type="password"
                       name="password"
                       required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-stone-dark shadow-sm focus:ring-stone-accent" name="remember">
                    <span class="ms-2 text-sm text-gray-600">Rester connecté</span>
                </label>
            </div>

            <div class="flex flex-col items-center gap-4 mt-8">
                <button type="submit" class="btn-submit">
                    {{ __('Se Connecter') }}
                </button>

                @if (Route::has('password.request'))
                    <a class="text-sm text-stone-medium hover:text-stone-dark transition" href="{{ route('password.request') }}">
                        {{ __('Mot de passe oublié ?') }}
                    </a>
                @endif
            </div>
        </form>
    </div>
</x-guest-layout>
