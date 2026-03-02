<x-guest-layout>
    <div class="login-container">
        <h1 class="auth-title">Art de la Pierre</h1>
        <p class="auth-subtitle">Récupération de compte</p>

        <div class="auth-form">
            <p class="auth-description">
                {{ __('Entrez votre adresse email pour recevoir un lien de réinitialisation de mot de passe.') }}
            </p>

            <x-auth-session-status class="mb-4 text-success font-medium text-sm" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="section-title-small">Email Professionnel</label>
                    <input id="email" type="email" name="email" class="custom-input" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger text-xs" />
                </div>

                <div class="flex flex-col items-center gap-4">
                    <button type="submit" class="btn-submit">
                        {{ __('Envoyer le lien') }}
                    </button>

                    <a href="{{ route('login') }}" class="auth-link">
                        {{ __('Retour à la connexion') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
