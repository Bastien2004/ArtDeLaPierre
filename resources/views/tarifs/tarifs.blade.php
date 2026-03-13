<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Tarifs - L'Art de la Pierre</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/tarifs.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('LogoHead.png') }}" type="image/png">
    <style>
        .btn-delete-row { color: #dc3545; opacity: 0.5; transition: 0.3s; border: none; background: none; }
        .btn-delete-row:hover { opacity: 1; transform: scale(1.2); }
        .bg-epaisseur { position: relative; }
    </style>
</head>
<body>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold text-dark mb-1">TARIF {{ date('Y') }}</h2>
            <p class="text-muted mb-0">Gestion de la grille de prix — Art de la Pierre</p>
        </div>
        <a href="{{ url('/dashboard') }}" class="btn-back-stone text-decoration-none">
            <i class="fa fa-arrow-left me-2"></i>Retour à l’accueil
        </a>
    </div>

    @if(session('success'))
        <div class="alert-custom-success mb-4">
            <i class="fa-solid fa-circle-check fa-lg me-2"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('tarifs.updateAll') }}" method="POST" id="mainTarifForm">
        @csrf

        @foreach(['Entreprise', 'Particulier'] as $type)
            <div class="mb-5 pb-3">
                <h4 class="title-stabilo mb-4">
                    <i class="fa-solid fa-user-tag me-2"></i>{{ $type }} :
                </h4>

                <div class="table-responsive shadow-sm rounded">
                    <table class="table table-bordered align-middle table-tarifs mb-0 text-center">
                        <thead>
                        <tr>
                            <th class="bg-epaisseur">Épaisseur</th>
                            <th style="background-color: #fdfd96; width: 22%;">Adoucie P40</th>
                            <th style="background-color: #c1f0c1; width: 22%;">Brut de sciage</th>
                            <th style="background-color: #cfe2f3; width: 22%;">Adoucie foncé</th>
                            <th style="background-color: #d9d9d9; width: 22%;">CISELÉ</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $epaisseurs = $allTarifs->where('type_client', $type)->pluck('epaisseur')->unique()->sort();
                        @endphp

                        @foreach($epaisseurs as $ep)
                            <tr>
                                <td class="fw-bold bg-epaisseur text-dark">
                                    <span class="flex-grow-1 text-center">{{ $ep }} cm</span>
                                    <div class="delete-zone">
                                        <button type="button" class="btn-delete-row" title="Supprimer l'épaisseur {{ $ep }}cm"
                                                onclick="deleteEpaisseur('{{ $ep }}')">
                                            <i class="fa-solid fa-trash-can fa-xs"></i>
                                        </button>
                                    </div>
                                </td>

                                @foreach(['Adoucie P40', 'Brut de sciage', 'Adoucie Foncé', 'Ciselé'] as $finition)
                                    @php
                                        $tarif = $allTarifs->where('type_client', $type)
                                                           ->where('finition', $finition)
                                                           ->where('epaisseur', $ep)
                                                           ->first();
                                    @endphp
                                    <td class="p-0">
                                        @if($tarif)
                                            <div class="d-flex align-items-center justify-content-center px-2">
                                                <input type="number" step="0.01" name="prix[{{ $tarif->id }}]" value="{{ $tarif->prix_m2 }}" class="form-control text-center input-prix-invisible">
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
            </div>
        @endforeach

        <div class="card border-0 shadow-sm mb-5 bg-light border-start border-4 border-primary">
            <div class="card-body">
                <h5 class="fw-bold text-dark mb-3"><i class="fa fa-plus-circle text-primary me-2"></i>Ajouter une nouvelle épaisseur</h5>
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted text-uppercase">Valeur (cm)</label>
                        <input type="number" name="new_epaisseur" class="form-control" placeholder="Ex: 6">
                    </div>
                    <div class="col-md-9">
                        <p class="small text-muted mb-0">Crée automatiquement la ligne pour Particuliers et Entreprises.</p>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5 opacity-25">

        <div class="mb-5 pb-3">
            <h4 class="title-stabilo mb-4" style="background: linear-gradient(100deg, #c1f0c1 0%, #a8e6a8 100%) !important; color: #155724 !important;">
                <i class="fa-solid fa-hammer me-2"></i> TRAVAUX SPÉCIFIQUES & OPTIONS :
            </h4>

            <div class="table-responsive shadow-sm rounded" style="max-width: 950px;">
                <table class="table table-bordered align-middle table-tarifs mb-0">
                    <thead class="table-light text-center">
                    <tr>
                        <th class="bg-epaisseur" style="width: 40%;">Désignation du travail</th>
                        <th style="width: 25%;">Unité</th>
                        <th style="width: 25%;">Prix unitaire (€)</th>
                        <th style="width: 10%;">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tarifsTravaux as $travail)
                        <tr>
                            <td class="fw-bold bg-epaisseur text-dark ps-4">{{ $travail->nom }}</td>
                            <td class="text-center text-muted small">{{ $travail->unite == 'ml' ? 'Mètre linéaire (ml)' : 'À l\'unité' }}</td>
                            <td class="p-0">
                                <div class="d-flex align-items-center justify-content-center px-4">
                                    <input type="number" step="0.01" name="travaux[{{ $travail->id }}]" value="{{ $travail->prix }}" class="form-control text-center input-prix-invisible">
                                    <span class="text-muted fw-bold ms-1">€</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn-delete-row" onclick="deleteTravail({{ $travail->id }})">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach

                    <tr class="bg-white">
                        <td class="p-2"><input type="text" name="new_travail_nom" class="form-control form-control-sm border-primary-subtle" placeholder="Ex: Polissage..."></td>
                        <td class="p-2">
                            <select name="new_travail_unite" class="form-select form-select-sm border-primary-subtle">
                                <option value="ml">Mètre linéaire (ml)</option>
                                <option value="unite">À l'unité</option>
                            </select>
                        </td>
                        <td class="p-2" colspan="2">
                            <div class="input-group input-group-sm">
                                <input type="number" step="0.01" name="new_travail_prix" class="form-control border-primary-subtle" placeholder="0.00">
                                <span class="input-group-text bg-primary-subtle border-primary-subtle">€</span>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-center mt-5 mb-5">
            <button type="submit" class="btn-save-premium px-5 py-3">
                <i class="fa fa-save me-2"></i>Enregistrer les tarifs {{ date('Y') }}
            </button>
        </div>
    </form>
</div>

<form id="deleteEpaisseurForm" method="POST" style="display:none;">
    @csrf @method('DELETE')
</form>

<form id="deleteTravailForm" method="POST" style="display:none;">
    @csrf @method('DELETE')
</form>

<script>
    function deleteEpaisseur(val) {
        if(confirm("Supprimer l'épaisseur " + val + "cm pour TOUS les clients ? Cette action est irréversible.")) {
            let form = document.getElementById('deleteEpaisseurForm');
            form.action = "/tarifs/epaisseur/" + val;
            form.submit();
        }
    }

    function deleteTravail(id) {
        if(confirm("Supprimer ce travail spécifique de la liste ?")) {
            let form = document.getElementById('deleteTravailForm');
            form.action = "/tarifs/travail/" + id;
            form.submit();
        }
    }
</script>

</body>
</html>
