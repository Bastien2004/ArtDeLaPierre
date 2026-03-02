<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Art de la Pierre — Gestion</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cinzel:700|inter:300,400,600" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="welcome-body">

<div class="hero-card">
    <h1 class="hero-title">Art de la Pierre</h1>
    <div class="hero-divider"></div>
    <p class="hero-subtitle">Logiciel de gestion technique & chiffrage</p>

    <div class="hero-actions">
        @if (Route::has('login'))
            @auth
                <div class="auth-info">
                    <p>Session active : <strong>{{ Auth::user()->name }}</strong></p>
                </div>

                <div class="btn-group">
                    <a href="{{ route('dashboard') }}" class="btn-primary">Accéder au Tableau de bord</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-logout-link">Se déconnecter</button>
                    </form>
                </div>
            @else
                <p class="auth-notice">Veuillez vous identifier pour accéder au registre.</p>
                <a href="{{ route('login') }}" class="btn-primary">Se connecter</a>
            @endauth
        @endif
    </div>

    <footer class="welcome-footer">
        <p>&copy; {{ date('Y') }} — Atelier Art de la Pierre</p>
    </footer>
</div>

</body>
</html>
