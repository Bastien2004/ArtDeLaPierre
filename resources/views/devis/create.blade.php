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
        <div class="form-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 20px;">
            <div class="form-group">
                <label>Particulier / Entreprise</label>
                <input type="text" name="client" placeholder="Nom du client" value="{{ $clientPrefill ?? '' }}" required>
            </div>
            <div class="form-group">
                <label>Adresse du client</label>
                <input type="text" name="adresse" placeholder="Adresse" value="{{ $adressePrefill ?? '' }}">
            </div>
        </div>

        <h3 class="section-title">Pierres & Mesures</h3>
        <div id="lignes-container">
            <div class="ligne-pierre" data-index="0">
                <button type="button" class="remove-line" onclick="removeLine(this)" title="Supprimer cette pierre">×</button>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Désignation</label>
                        <input type="text" name="lignes[0][typePierre]" placeholder="Ex: Pierre Bleues de Soignies en 5cm finition...">
                    </div>
                    <div class="form-group">
                        <label>Quantité</label>
                        <input type="number" name="lignes[0][nombrePierre]" placeholder="Nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Longueur (m)</label>
                        <input type="number" step="0.001" name="lignes[0][longueurM]" placeholder="0.000" required>
                    </div>
                    <div class="form-group">
                        <label>Largeur (m)</label>
                        <input type="number" step="0.001" name="lignes[0][largeurM]" placeholder="0.000" required>
                    </div>
                    <div class="form-group">
                        <label>Prix M² (€)</label>
                        <input type="number" step="0.01" name="lignes[0][prixM2]" placeholder="0.00" required>
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
    let pierreIdx = 1;

    // Ajouter une nouvelle pierre
    document.getElementById('add-line').onclick = function() {
        let container = document.getElementById('lignes-container');
        let reference = container.querySelector('.ligne-pierre');
        let clone = reference.cloneNode(true);

        clone.dataset.index = pierreIdx;
        clone.style.opacity = '0';

        // Reset des champs pierre
        clone.querySelectorAll('input').forEach(i => {
            // Mise à jour de l'index des noms (lignes[0] -> lignes[1])
            i.name = i.name.replace(/lignes\[\d+\]/, `lignes[${pierreIdx}]`);
            i.value = '';
        });

        // Vider les spécificités clonées s'il y en avait
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
