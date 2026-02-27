<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Devis - Art de la Pierre</title>
    <link rel="stylesheet" href="{{ asset('css/devisCreate.css') }}">
</head>
<body>
<div class="container">
    <form action="{{ route('devis.store') }}" method="POST">
        @csrf

        <h1>Création de Devis</h1>

        <input type="hidden" name="force_time" value="{{ $timePrefill ?? '' }}">

        <h3 class="section-title">Informations Client</h3>
        <div class="form-grid" style="grid-template-columns: 1fr 1fr 1fr; margin-bottom: 20px;">
            <div class="form-group">
                <label>Type de Client</label>
                <select name="type_client_global" id="type_client_global" class="form-control" required onchange="updateAllPrices()">
                    <option value="Particulier">Particulier</option>
                    <option value="Entreprise">Entreprise</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nom du Client</label>
                <input type="text" name="client" placeholder="Nom" value="{{ $clientPrefill ?? '' }}" required>
            </div>
            <div class="form-group">
                <label>Adresse</label>
                <input type="text" name="adresse" placeholder="Adresse" value="{{ $adressePrefill ?? '' }}">
            </div>
        </div>

        <h3 class="section-title">Pierres & Mesures</h3>
        <div id="lignes-container">
            <div class="ligne-pierre" data-index="0">
                <button type="button" class="remove-line" onclick="removeLine(this)" title="Supprimer cette pierre">×</button>

                <div class="form-grid" style="grid-template-columns: 1.5fr 1fr 1fr 1fr 1fr 1fr;">
                    <div class="form-group">
                        <div class="form-group" style="grid-column: span 6; margin-top: 10px;">
                            <label>Désignation personnalisée</label>
                            <input type="text" name="lignes[0][typePierre]" class="input-designation" placeholder="EX : Pierre Bleue">
                        </div>
                        <label>Finition</label>
                        <select name="lignes[0][finition]" class="select-finition" onchange="lookupPrice(this)">
                            <option value="">-- Choisir --</option>
                            <option value="Adoucie P40">Adoucie P40</option>
                            <option value="Brut de sciage">Brut de sciage</option>
                            <option value="Adoucie Foncé">Adoucie Foncé</option>
                            <option value="Ciselé">Ciselé</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Épaisseur</label>
                        <select name="lignes[0][epaisseur]" class="select-epaisseur" onchange="lookupPrice(this)">
                            <option value="2">2 cm</option>
                            <option value="3">3 cm</option>
                            <option value="4">4 cm</option>
                            <option value="5">5 cm</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Qté</label>
                        <input type="number" name="lignes[0][nombrePierre]" value="1" required>
                    </div>
                    <div class="form-group">
                        <label>Long. (m)</label>
                        <input type="number" step="0.001" name="lignes[0][longueurM]" placeholder="0.000" required>
                    </div>
                    <div class="form-group">
                        <label>Larg. (m)</label>
                        <input type="number" step="0.001" name="lignes[0][largeurM]" placeholder="0.000" required>
                    </div>
                    <div class="form-group">
                        <label>Prix M² (€)</label>
                        <input type="number" step="0.01" name="lignes[0][prixM2]" class="input-prix-m2" readonly required>
                    </div>

                </div>

                <div class="specs-wrapper">
                    <label class="sub-title">Spécificité & Travaux sur cette pierre</label>

                    <div class="quick-add-buttons" style="margin-bottom: 10px; display: flex; gap: 5px; flex-wrap: wrap;">
                        @foreach($tarifsTravaux as $tarif)
                            <button type="button" class="btn-quick-spec"
                                    onclick="addCalculatedSpec(this, '{{ $tarif->nom }}', {{ $tarif->prix }}, '{{ $tarif->unite }}')">
                                <i class="fa fa-plus-circle"></i> {{ $tarif->nom }}
                                <small>({{ $tarif->prix }}€/{{ $tarif->unite }})</small>
                            </button>
                        @endforeach
                    </div>

                    <div class="specs-container">
                    </div>

                    <button type="button" class="btn-add-spec" onclick="addSpec(this)">
                        + Ajouter manuellement
                    </button>
                </div>
            </div>
        </div>

        <button type="button" id="add-line">+ Ajouter un autre type de pierre</button>

        <button type="submit" class="btn-submit">🔨 Générer le devis technique</button>
    </form>
</div>

<script>
    // On récupère la variable PHP envoyée par le contrôleur
    const grilleTarifs = @json($allTarifs);

    // Fonction pour trouver le prix automatiquement
    function lookupPrice(element) {
        const row = element.closest('.ligne-pierre');
        const typeClient = document.getElementById('type_client_global').value;
        const finition = row.querySelector('.select-finition').value;
        const epaisseur = row.querySelector('.select-epaisseur').value;

        const inputPrix = row.querySelector('.input-prix-m2');

        if (!finition || !epaisseur) return;

        // On cherche la correspondance dans les données
        const tarifTrouve = grilleTarifs.find(t =>
            t.type_client === typeClient &&
            t.finition === finition &&
            t.epaisseur == epaisseur
        );

        if (tarifTrouve) {
            inputPrix.value = tarifTrouve.prix_m2;
            // On génère le nom pour le PDF automatiquement

            // On déclenche manuellement l'événement 'input' pour recalculer les travaux (Rejingot...)
            inputPrix.dispatchEvent(new Event('input', { bubbles: true }));
        } else {
            inputPrix.value = '';
            inputDesignation.value = '';
        }
    }

    // Fonction pour mettre à jour toutes les lignes si on change Entreprise/Particulier en haut
    function updateAllPrices() {
        document.querySelectorAll('.ligne-pierre').forEach(row => {
            const selectFinition = row.querySelector('.select-finition');
            if (selectFinition && selectFinition.value !== "") {
                lookupPrice(selectFinition);
            }
        });
    }



    let pierreIdx = 1;

    // Ajouter une nouvelle pierre
    // Ajouter une nouvelle pierre
    document.getElementById('add-line').onclick = function() {
        let container = document.getElementById('lignes-container');
        let reference = container.querySelector('.ligne-pierre');
        let clone = reference.cloneNode(true);

        clone.dataset.index = pierreIdx;
        clone.style.opacity = '0';

        // 1. Reset des INPUTS
        clone.querySelectorAll('input').forEach(i => {
            i.name = i.name.replace(/lignes\[\d+\]/, `lignes[${pierreIdx}]`);
            i.value = '';
        });

        // 2. Reset des SELECTS (Correction ici)
        clone.querySelectorAll('select').forEach(s => {
            s.name = s.name.replace(/lignes\[\d+\]/, `lignes[${pierreIdx}]`);
            s.selectedIndex = 0;
        });

        // Vider les spécificités clonées
        clone.querySelector('.specs-container').innerHTML = '';

        container.appendChild(clone);
        setTimeout(() => {
            clone.style.transition = "opacity 0.4s ease";
            clone.style.opacity = '1';
        }, 10);

        pierreIdx++;
    };

    // Ajouter une spécificité à une pierre précise
    function addSpec(button) {
        const pierreRow = button.closest('.ligne-pierre');
        const specsContainer = pierreRow.querySelector('.specs-container');
        const pIdx = pierreRow.dataset.index;
        const sIdx = specsContainer.querySelectorAll('.ligne-spec').length;

        const specHtml = `
            <div class="ligne-spec">
                <div class="form-grid-specs">
                    <input type="text" name="lignes[${pIdx}][specs][${sIdx}][nom]" placeholder="Ex: Rejingot, Ciselage...">
                    <input type="number" step="0.01" name="lignes[${pIdx}][specs][${sIdx}][prix]" placeholder="Prix (€)">
                    <button type="button" class="remove-spec" onclick="this.parentElement.parentElement.remove()">×</button>
                </div>
            </div>
        `;

        specsContainer.insertAdjacentHTML('beforeend', specHtml);
    }

    // Supprimer une pierre
    function removeLine(button) {
        const lines = document.querySelectorAll('.ligne-pierre');
        if (lines.length > 1) {
            const row = button.closest('.ligne-pierre');
            row.style.opacity = '0';
            row.style.transform = 'translateX(20px)';
            setTimeout(() => row.remove(), 300);
        } else {
            alert("Un devis doit comporter au moins une pierre.");
        }
    }

    function addCalculatedSpec(button, nom, prixUnitaire, unite) {
        const pierreRow = button.closest('.ligne-pierre');
        const specsContainer = pierreRow.querySelector('.specs-container');
        const pIdx = pierreRow.dataset.index;
        const sIdx = specsContainer.querySelectorAll('.ligne-spec').length;

        // Récupération des valeurs actuelles
        const longueur = parseFloat(pierreRow.querySelector('input[name*="[longueurM]"]').value) || 0;
        const quantite = parseFloat(pierreRow.querySelector('input[name*="[nombrePierre]"]').value) || 1;

        // Calcul : (Prix de base * Longueur si ml) * Quantité de pierres
        let prixBaseCalcule = (unite === 'ml') ? (prixUnitaire * longueur) : prixUnitaire;
        let prixFinalAffiche = prixBaseCalcule * quantite;

        const specHtml = `
    <div class="ligne-spec">
        <div class="form-grid-specs" style="display: flex; gap: 10px; margin-bottom: 5px;">
            <input type="text" name="lignes[${pIdx}][specs][${sIdx}][nom]" value="${nom}" class="form-control" readonly>
            <input type="hidden" name="lignes[${pIdx}][specs][${sIdx}][unite]" value="${unite}">
            <input type="number" step="0.01"
                   name="lignes[${pIdx}][specs][${sIdx}][prix]"
                   value="${prixFinalAffiche.toFixed(2)}"
                   class="form-control spec-prix-input"
                   data-unite="${unite}"
                   data-base-price="${prixUnitaire}">
            <button type="button" class="remove-spec" onclick="this.parentElement.remove()">×</button>
        </div>
    </div>
    `;

        specsContainer.insertAdjacentHTML('beforeend', specHtml);
    }


    // Écouteur pour la mise à jour automatique des prix (Rejingot, Ciselage...)
    document.addEventListener('input', function(e) {
        const targetName = e.target.name;

        // Si on modifie la Longueur OU la Quantité
        if (targetName && (targetName.includes('[longueurM]') || targetName.includes('[nombrePierre]'))) {
            const pierreRow = e.target.closest('.ligne-pierre');
            const nouvelleLongueur = parseFloat(pierreRow.querySelector('input[name*="[longueurM]"]').value) || 0;
            const nouvelleQuantite = parseFloat(pierreRow.querySelector('input[name*="[nombrePierre]"]').value) || 0;

            const specInputs = pierreRow.querySelectorAll('.spec-prix-input');

            specInputs.forEach(input => {
                const prixBaseUnitaire = parseFloat(input.dataset.basePrice);
                let nouveauPrix;

                if (input.dataset.unite === 'ml') {
                    // (Prix au ml * Longueur) * Quantité
                    nouveauPrix = (prixBaseUnitaire * nouvelleLongueur) * nouvelleQuantite;
                } else {
                    // Prix fixe * Quantité (ex: Oreilles)
                    nouveauPrix = prixBaseUnitaire * nouvelleQuantite;
                }

                input.value = nouveauPrix.toFixed(2);
            });
        }
    });
</script>
</body>
</html>
