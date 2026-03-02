<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    <x-slot name="header">
        <div class="flex items-center justify-between header-stone">
            <div>
                <h2 class="font-stone-title text-2xl text-gray-800 leading-tight">
                    {{ __('Tableau de Bord') }}
                </h2>
                <p class="text-sm text-gray-500 font-medium">Bienvenue sur l'interface de gestion — Art de la Pierre</p>
            </div>
            <button type="button" class="logout-btn-stone" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="fas fa-sign-out-alt"></i> Quitter
            </button>
        </div>
    </x-slot>

    <div class="py-12 bg-stone-light">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="dashboard-grid">

                <a href="{{ route('devis.index') }}" class="card-action devis-card">
                    <div class="card-icon">
                        <span class="icon-bg">📋</span>
                    </div>
                    <div class="card-content">
                        <h3>Registre des Devis</h3>
                        <p>Éditez vos offres commerciales et suivez vos projets en cours avec précision.</p>
                        <span class="btn-discover">Explorer <i class="fas fa-chevron-right"></i></span>
                    </div>
                </a>

                <a href="{{ route('tarifs.tarifs') }}" class="card-action tarifs-card">
                    <div class="card-icon">
                        <span class="icon-bg">💎</span>
                    </div>
                    <div class="card-content">
                        <h3>Grille Tarifaire</h3>
                        <p>Ajustez les prix des matériaux, des finitions et de la main-d'œuvre qualifiée.</p>
                        <span class="btn-discover">Gérer <i class="fas fa-chevron-right"></i></span>
                    </div>
                </a>

            </div>
        </div>
    </div>

    @include('partials.logout')
</x-app-layout>
