<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Panneau de Contrôle — Art de la Pierre') }}
            </h2>
            <button type="button" class="logout-btn-stone" data-bs-toggle="modal" data-bs-target="#logoutModal">
                🚪 Déconnexion
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="dashboard-card">
                <div class="dashboard-grid">

                    <a href="{{ route('devis.index') }}" class="card-action devis">
                        <div class="">📋</div>
                        <div class="card-body">
                            <h3>Registre des Devis</h3>
                            <p>Consulter, modifier ou créer des devis techniques.</p>
                        </div>
                        <div class="card-arrow"></div>
                    </a>

                    <a href="{{ route('tarifs.tarifs') }}" class="card-action tarifs">
                        <div class="card-icon">💎</div>
                        <div class="card-body">
                            <h3>Grille Tarifaire</h3>
                            <p>Mettre à jour les prix des pierres, finitions et travaux.</p>
                        </div>
                        <div class="card-arrow">→</div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('partials.logout')
</x-app-layout>
