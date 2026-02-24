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

        <h3 class="section-title">Informations Client</h3>
        <div class="form-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 20px;">
            <div class="form-group">
                <label>Particulier / Entreprise</label>
                <input type="text" name="client" placeholder="Nom du client" required>
            </div>
            <div class="form-group">
                <label>Adresse du client</label>
                <input type="text" name="adresse" placeholder="Adresse de l'entreprise (optionnel)">
            </div>
        </div>

        <h3 class="section-title">Pierres & Mesures</h3>
        <div id="lignes-container">
            <div class="ligne-pierre" data-index="0">
                <button type="button" class="remove-line" onclick="removeLine(this)" title="Supprimer cette pierre">×</button>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Type de Pierre</label>
                        <input type="text" name="lignes[0][typePierre]" placeholder="Ex: Pierre Bleue" required>
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
                    <label class="sub-title">Options & Travaux sur cette pierre</label>
                    <div class="specs-container">
                    </div>
                    <button type="button" class="btn-add-spec" onclick="addSpec(this)">
                        + Ajouter une spécificité (Rejingot, Ciselage...)
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
</script>
</body>
</html>
