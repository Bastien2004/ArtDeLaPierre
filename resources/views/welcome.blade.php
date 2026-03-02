<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1"

    </head>
    <body>
    <header>
        @if (Route::has('login'))
            <div class="auth-navigation">
                @auth
                    <a href="{{ route('devis.index') }}" class="btn-new">Accéder aux Devis</a><br>
                    <a href="{{ route('tarifs.tarifs') }}" class="btn-new">Gérer les Tarifs</a>

                    <form method="POST" action="{{ route('logout') }}" style="margin-top: 20px;">
                        @csrf
                        <button type="submit" style="color: red; cursor: pointer; background: none; border: none; text-decoration: underline;">
                            Se déconnecter
                        </button>
                    </form>
                @else
                    <h1>Bienvenue sur Art de la Pierre</h1>
                    <p>Veuillez vous connecter pour accéder au registre.</p>
                    <a href="{{ route('login') }}" class="btn-login" style="padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px;">
                        Se connecter
                    </a>
                @endauth
            </div>
        @endif
    </header>

    <footer>

    </footer>
    </body>
</html>
