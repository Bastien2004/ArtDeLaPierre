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

        <a href="{{ route('devis.index') }}" class="btn-back-stone">
            <i class="fa fa-arrow-left"></i> Retour au Registre des Devis
        </a>

        <h1>Création de Devis</h1>

        <input type="hidden" name="force_time" value="{{ $timePrefill ?? '' }}">

        <h3 class="section-title">Informations Client</h3>
        <div class="form-grid" style="grid-template-columns: 1fr 1fr 1fr; margin-bottom: 20px;">
            <div class="form-group">
                <label>Type de Client</label>
                <select name="type_client_global" id="type_client_global" class="form-control lock-on-add" required onchange="updateAllPrices()">
                    <option value="Entreprise">Entreprise</option>
                    <option value="Particulier">Particulier</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nom du Client</label>
                <input type="text" name="client" class="lock-on-add" placeholder="Nom" value="{{ $clientPrefill ?? '' }}" required>
            </div>
            <div class="form-group">
                <label>Adresse</label>
                <input type="text" name="adresse" class="lock-on-add" placeholder="Adresse" value="{{ $adressePrefill ?? '' }}">
            </div>
        </div>

        <h3 class="section-title">Informations Client</h3>
        <div class="form-grid" style="grid-template-columns: 1fr 1fr 1fr 1fr; margin-bottom: 20px;">
            <div class="form-group">
                <label>Frais de Livraison (€ HT)</label>
                <input type="number" name="livraison" class="lock-on-add" step="0.01" value="{{ $livraisonPrefill ?? '0.00' }}" placeholder="0.00">            </div>
        </div>

        <h3 class="section-title">Pierres & Mesures</h3>
        <div id="lignes-container">
            <div class="ligne-pierre" data-index="0">
                <input type="hidden" name="lignes[0][livraison]" class="input-livraison-ligne" value="0.00">
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
                        <div class="form-group">
                            <label>Épaisseur</label>
                            <select name="lignes[0][epaisseur]" class="select-epaisseur" onchange="lookupPrice(this)">
                                @php
                                    // On récupère les épaisseurs uniques présentes dans la grille de tarifs
                                    $epaisseursDisponibles = $allTarifs->pluck('epaisseur')->unique()->sort();
                                @endphp

                                @foreach($epaisseursDisponibles as $ep)
                                    <option value="{{ $ep }}" {{ $ep == 3 ? 'selected' : '' }}>
                                        {{ $ep }} cm
                                    </option>
                                @endforeach
                            </select>
                        </div>
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
                        <input type="number" step="0.01" name="lignes[0][prixM2]" class="input-prix-m2" required>
                    </div>

                    <div class="form-group">
                        <label>Poids (kg)</label>
                        <input type="number" step="0.01" name="lignes[0][poids]" class="input-poids" readonly style="background: #f1f1f1; font-weight: bold;">
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
    const grilleTarifs = @json($allTarifs);

    // On attend que le DOM soit chargé pour assigner les événements
    document.addEventListener('DOMContentLoaded', function() {

        let pierreIdx = 1;

        // --- GESTION DU BOUTON AJOUTER LIGNE ---
        const btnAddLine = document.getElementById('add-line');

        if (btnAddLine) {
            btnAddLine.onclick = function() {
                // 1. RÉCUPÉRATION DE LA VALEUR
                const livraisonInput = document.querySelector('input[name="livraison"]');
                const livraisonActuelle = livraisonInput ? livraisonInput.value : "0.00";

                // --- SÉCURITÉ : On met à jour le champ caché de la ligne 0 AVANT de cloner ---
                const firstHiddenLivraison = document.querySelector('input[name="lignes[0][livraison]"]');
                if (firstHiddenLivraison) {
                    firstHiddenLivraison.value = livraisonActuelle;
                }

                // 2. VERROUILLAGE DES INFOS GLOBALES (uniquement si pré-rempli)
                const clientInput = document.querySelector('input[name="client"]');
                const estPreRempli = clientInput && clientInput.value.trim() !== "";

                if (estPreRempli) {
                    const toLock = [
                        document.querySelector('input[name="client"]'),
                        document.querySelector('input[name="adresse"]'),
                        livraisonInput,
                        document.getElementById('type_client_global')
                    ];

                    toLock.forEach(field => {
                        if (field) {
                            if (field.tagName === 'SELECT') {
                                if (!document.getElementById(field.name + '_hidden')) {
                                    let hidden = document.createElement("input");
                                    hidden.type = "hidden";
                                    hidden.name = field.name;
                                    hidden.id = field.name + '_hidden';
                                    hidden.value = field.value;
                                    field.parentNode.insertBefore(hidden, field);
                                }
                                field.disabled = true;
                            } else {
                                field.readOnly = true;
                            }
                            field.style.setProperty("background-color", "#e9ecef", "important");
                            field.style.setProperty("cursor", "not-allowed", "important");
                        }
                    });
                }

                // 3. LOGIQUE DE CLONAGE
                let container = document.getElementById('lignes-container');
                let firstLine = container.querySelector('.ligne-pierre');
                let clone = firstLine.cloneNode(true);

                clone.dataset.index = pierreIdx;

                clone.querySelectorAll('input').forEach(i => {
                    if (i.name) i.name = i.name.replace(/lignes\[\d+\]/, `lignes[${pierreIdx}]`);

                    if (i.classList.contains('input-livraison-ligne')) {
                        i.value = livraisonActuelle;
                    } else if (i.classList.contains('input-designation') || i.name.includes('longueurM') || i.name.includes('largeurM') || i.name.includes('prixM2')) {
                        i.value = '';
                    } else if (i.name.includes('nombrePierre')) {
                        i.value = '1';
                    }

                    i.readOnly = false;
                    i.disabled = false;
                    i.style.backgroundColor = "";
                    i.style.cursor = "";
                });

                clone.querySelectorAll('select').forEach(s => {
                    s.name = s.name.replace(/lignes\[\d+\]/, `lignes[${pierreIdx}]`);
                    s.selectedIndex = 0;
                    s.disabled = false;
                    s.style.backgroundColor = "";
                });

                clone.querySelector('.specs-container').innerHTML = '';
                container.appendChild(clone);

                // Animation
                clone.style.opacity = '0';
                setTimeout(() => {
                    clone.style.transition = "opacity 0.4s";
                    clone.style.opacity = '1';
                }, 10);

                pierreIdx++;
            };
        }
    });


    function lookupPrice(element) {
        const row = element.closest('.ligne-pierre');
        const typeClient = document.getElementById('type_client_global').value;
        const finition = row.querySelector('.select-finition').value;
        const epaisseur = row.querySelector('.select-epaisseur').value;
        const inputPrix = row.querySelector('.input-prix-m2');

        if (!finition || !epaisseur) return;

        const tarifTrouve = grilleTarifs.find(t =>
            t.type_client === typeClient &&
            t.finition === finition &&
            t.epaisseur == epaisseur
        );

        if (tarifTrouve) {
            inputPrix.value = tarifTrouve.prix_m2;
            inputPrix.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    function updateAllPrices() {
        document.querySelectorAll('.ligne-pierre').forEach(row => {
            const selectFinition = row.querySelector('.select-finition');
            if (selectFinition && selectFinition.value !== "") lookupPrice(selectFinition);
        });
    }

    function addSpec(button) {
        const pierreRow = button.closest('.ligne-pierre');
        const specsContainer = pierreRow.querySelector('.specs-container');
        const pIdx = pierreRow.dataset.index;
        const sIdx = specsContainer.querySelectorAll('.ligne-spec').length;

        const specHtml = `
            <div class="ligne-spec">
                <div class="form-grid-specs">
                    <input type="text" name="lignes[${pIdx}][specs][${sIdx}][nom]" placeholder="Ex: Rejingot">
                    <input type="number" step="0.01" name="lignes[${pIdx}][specs][${sIdx}][prix]" placeholder="Prix">
                    <button type="button" class="remove-spec" onclick="this.parentElement.parentElement.remove()">×</button>
                </div>
            </div>`;
        specsContainer.insertAdjacentHTML('beforeend', specHtml);
    }

    function removeLine(button) {
        if (document.querySelectorAll('.ligne-pierre').length > 1) {
            button.closest('.ligne-pierre').remove();
        } else {
            alert("Minimum une pierre requise.");
        }
    }

    function addCalculatedSpec(button, nom, prixUnitaire, unite) {
        const pierreRow = button.closest('.ligne-pierre');
        const specsContainer = pierreRow.querySelector('.specs-container');
        const pIdx = pierreRow.dataset.index;
        const sIdx = specsContainer.querySelectorAll('.ligne-spec').length;

        const longueur = parseFloat(pierreRow.querySelector('input[name*="[longueurM]"]').value) || 0;
        const quantite = parseFloat(pierreRow.querySelector('input[name*="[nombrePierre]"]').value) || 1;

        let prixFinal = (unite === 'ml' ? prixUnitaire * Math.max(longueur, 1) : prixUnitaire) * quantite;

        // Champ taille uniquement pour le rejingot
        const tailleField = nom.toLowerCase().includes('rejingot') ? `
            <div class="taille-rejingot-wrapper">
                <input type="number" name="lignes[${pIdx}][specs][${sIdx}][tailleMin]" value="2" min="0" step="0.1">
                <span class="separator">/</span>
                <input type="number" name="lignes[${pIdx}][specs][${sIdx}][tailleMax]" value="3" min="0" step="0.1">
                <span class="unite-label">cm</span>
            </div>
        ` : '';

        const specHtml = `
            <div class="ligne-spec">
                <div class="form-grid-specs" style="display: flex; gap: 10px; margin-bottom: 5px; align-items: center;">
                    <input type="text" name="lignes[${pIdx}][specs][${sIdx}][nom]" value="${nom}" class="form-control" readonly>
                    ${tailleField}
                    <input type="hidden" name="lignes[${pIdx}][specs][${sIdx}][unite]" value="${unite}">
                    <input type="number" step="0.1" name="lignes[${pIdx}][specs][${sIdx}][prix]" value="${prixFinal.toFixed(2)}" class="form-control spec-prix-input" data-unite="${unite}" data-base-price="${prixUnitaire}">
                    <button type="button" class="remove-spec" onclick="this.parentElement.remove()">×</button>
                </div>
            </div>`;
        specsContainer.insertAdjacentHTML('beforeend', specHtml);
    }

    // Mise à jour dynamique lors de la modification des mesures
    document.addEventListener('input', function(e) {
        if (e.target.name && (e.target.name.includes('[longueurM]') || e.target.name.includes('[nombrePierre]'))) {
            const pierreRow = e.target.closest('.ligne-pierre');
            const L = parseFloat(pierreRow.querySelector('input[name*="[longueurM]"]').value) || 0;
            const Q = parseFloat(pierreRow.querySelector('input[name*="[nombrePierre]"]').value) || 0;

            pierreRow.querySelectorAll('.spec-prix-input').forEach(input => {
                const nom = input.closest('.form-grid-specs').querySelector('input[type="text"]').value;
                const base = parseFloat(input.dataset.basePrice);

                if (input.dataset.unite === 'ml') {
                    // Si c'est un rejingot et que L < 1, on force L à 1 pour le calcul
                    let longueurEffective = (nom.toLowerCase().includes('rejingot') && L < 1 && L > 0) ? 1 : L;
                    input.value = (base * longueurEffective * Q).toFixed(2);
                } else {
                    input.value = (base * Q).toFixed(2);
                }
            });
        }
    });

    function verrouillerInfosClient() {
        const selectors = [
            'select[name="type_client_global"]',
            'input[name="client"]',
            'input[name="adresse"]',
            'input[name="livraison"]'
        ];

        selectors.forEach(sel => {
            const field = document.querySelector(sel);
            if (field) {
                if (field.tagName === 'SELECT') {
                    field.disabled = true;
                    if (!document.getElementById(field.name + '_hidden')) {
                        let h = document.createElement("input");
                        h.type = "hidden";
                        h.name = field.name;
                        h.id = field.name + '_hidden';
                        h.value = field.value;
                        field.parentNode.insertBefore(h, field);
                    }
                } else {
                    field.readOnly = true;
                }
                field.style.backgroundColor = "#e9ecef";
                field.style.cursor = "not-allowed";
            }
        });
    }


    // À ajouter tout en bas de votre script
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Si les champs sont pré-remplis par Laravel (cas du bouton + Ligne)
        const clientInput = document.querySelector('input[name="client"]');
        if (clientInput && clientInput.value.trim() !== "") {
            console.log("Pré-remplissage détecté, verrouillage automatique...");
            verrouillerInfosClient();
        }

        // Sécurité supplémentaire : lier la fonction au bouton s'il n'est pas déjà lié
        const btnAdd = document.getElementById('add-line');
        if(btnAdd && clientInput && clientInput.value.trim() !== "") {
            // On verrouille au clic UNIQUEMENT si on est en mode "ajout de ligne" (pré-remplissage)
            btnAdd.addEventListener('click', verrouillerInfosClient);
        }
    });


    function calculerPoidsLigne(row) {
        const L = parseFloat(row.querySelector('input[name*="[longueurM]"]').value) || 0;
        const l = parseFloat(row.querySelector('input[name*="[largeurM]"]').value) || 0;
        const ep = parseFloat(row.querySelector('.select-epaisseur').value) || 0;
        const Q = parseFloat(row.querySelector('input[name*="[nombrePierre]"]').value) || 1;

        // Formule : Longueur * Largeur * (Epaisseur en m) * Densité (2700) * Quantité
        const poids = L * l * (ep / 100) * 2700 * Q;

        const inputPoids = row.querySelector('.input-poids');
        if (inputPoids) {
            inputPoids.value = poids.toFixed(2);
        }
    }

    // Mets à jour ton document.addEventListener('input') pour inclure le poids :
    document.addEventListener('input', function(e) {
        const row = e.target.closest('.ligne-pierre');
        if (row && (e.target.name.includes('longueurM') || e.target.name.includes('largeurM') || e.target.name.includes('nombrePierre'))) {
            calculerPoidsLigne(row);
        }
    });

    // N'oublie pas de l'appeler aussi quand on change l'épaisseur
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('select-epaisseur')) {
            calculerPoidsLigne(e.target.closest('.ligne-pierre'));
        }
    });
</script>
</body>
</html>
