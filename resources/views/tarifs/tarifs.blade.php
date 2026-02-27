<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Tarifs - L'Art de la Pierre</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{asset('css/tarifs.css')}}" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold text-dark mb-1">TARIF {{ date('Y') }}</h2>
            <p class="text-muted mb-0">Gestion de la grille de prix</p>
        </div>
        <a href="{{ route('devis.index') }}" class="btn-return">
            <i class="fa fa-arrow-left"></i> Retour au Registre
        </a>
    </div>

    @if(session('success'))
        <div class="alert-custom-success">
            <i class="fa-solid fa-circle-check fa-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('tarifs.updateAll') }}" method="POST">
        @csrf

        @foreach(['Entreprise', 'Particulier'] as $type)
            <div class="mb-5 pb-3">
                <h4 class="title-stabilo">
                    <i class="fa-solid fa-user-tag me-2"></i>{{ $type }} :
                </h4>

                <table class="table table-bordered align-middle table-tarifs">
                    <thead>
                    <tr class="text-center">
                        <th class="bg-epaisseur">Épaisseur</th>
                        <th style="background-color: #fdfd96; width: 20%;">
                            Adoucie P40<br>
                            <small class="text-muted opacity-75">COEF {{ $type == 'Entreprise' ? '3.2' : '3.5' }}</small>
                        </th>
                        <th style="background-color: #c1f0c1; width: 20%;">
                            Brut de sciage<br>
                            <small class="text-muted opacity-75">COEF 2.7</small>
                        </th>
                        <th style="background-color: #cfe2f3; width: 20%;">
                            Adoucie foncé<br>
                            <small class="text-muted opacity-75">COEF 3.8</small>
                        </th>
                        <th style="background-color: #d9d9d9; width: 20%;">
                            CISELÉ<br>
                            <small class="text-muted opacity-75">COEF 3.8</small>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach([2, 3, 4, 5] as $ep)
                        <tr>
                            <td class="fw-bold bg-epaisseur text-dark">{{ $ep }} cm</td>

                            @foreach(['Adoucie P40', 'Brut de sciage', 'Adoucie Foncé', 'Ciselé'] as $finition)
                                @php
                                    $tarif = $allTarifs->where('type_client', $type)
                                                       ->where('finition', $finition)
                                                       ->where('epaisseur', $ep)
                                                       ->first();
                                @endphp
                                <td class="p-0 text-center">
                                    @if($tarif)
                                        <div class="d-flex align-items-center justify-content-center px-3">
                                            <input type="number" step="0.01"
                                                   name="prix[{{ $tarif->id }}]"
                                                   value="{{ $tarif->prix_m2 }}"
                                                   class="form-control text-center input-prix-invisible">
                                            <span class="text-muted fw-bold ms-1 small">€</span>
                                        </div>
                                    @else
                                        <div class="py-3 text-muted small italic">N/A</div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        <div class="mb-5 pb-3">
            <h4 class="title-stabilo" style="background: linear-gradient(100deg, #c1f0c1 0%, #a8e6a8 100%) !important; color: #155724 !important;">
                <i class="fa-solid fa-hammer me-2"></i> TRAVAUX SPÉCIFIQUES & OPTIONS :
            </h4>

            <table class="table table-bordered align-middle table-tarifs" style="max-width: 800px;">
                <thead>
                <tr class="text-center">
                    <th class="bg-epaisseur" style="width: 40%;">Désignation du travail</th>
                    <th style="background-color: #f8f9fa; width: 30%;">Unité</th>
                    <th style="background-color: #fdfd96; width: 30%;">Prix unitaire (€)</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tarifsTravaux as $travail)
                    <tr>
                        <td class="fw-bold bg-epaisseur text-dark text-start ps-4">
                            {{ $travail->nom }}
                        </td>
                        <td class="text-center text-muted">
                            {{ $travail->unite == 'ml' ? 'Mètre linéaire (ml)' : 'À l\'unité' }}
                        </td>
                        <td class="p-0 text-center">
                            <div class="d-flex align-items-center justify-content-center px-3">
                                <input type="number" step="0.01"
                                       name="travaux[{{ $travail->id }}]"
                                       value="{{ $travail->prix }}"
                                       class="form-control text-center input-prix-invisible">
                                <span class="text-muted fw-bold ms-1 small">€</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="text-center mt-5">
            <button type="submit" class="btn-save-premium">
                <i class="fa fa-save"></i> Enregistrer les tarifs 2024
            </button>
        </div>
    </form>
</div>

</body>
</html>
