<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Devis - Taille de Pierre</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/devisCreate.css') }}">

</head>
<body>

<div class="container">
    <div class="header-section">
        <h1>Création de Devis</h1>
        <a href="{{ route('devis.index') }}" style="text-decoration: none; color: var(--stone-medium);">← Retour au registre</a>
    </div>

    <div class="form-card">
        <form action="{{ route('devis.store') }}" method="POST">
            @csrf

            <h3 class="section-title">Informations Client</h3>
            <div class="form-grid">
                <div class="form-group full">
                    <label>Nom du Client / Entreprise</label>
                    <input type="text" name="client" placeholder="Ex: Jean Dupont ou Menuiserie SARL" required>
                </div>
                <div class="form-group full">
                    <label>Adresse de livraison / chantier</label>
                    <input type="text" name="adresse" placeholder="Adresse complète">
                </div>
            </div>

            <h3 class="section-title">Caractéristiques de la Pierre</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>Type de Pierre</label>
                    <input type="text" name="typePierre" placeholder="Ex: Pierre de Lens, Granit..." required>
                </div>
                <div class="form-group">
                    <label>Prix au M² (€)</label>
                    <input type="number" step="0.01" name="prixM2" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label>Longueur (cm)</label>
                    <input type="number" step="0.1" name="longueurCM" placeholder="0.0" required>
                </div>
                <div class="form-group">
                    <label>Largeur (cm)</label>
                    <input type="number" step="0.1" name="largeurCM" placeholder="0.0" required>
                </div>
                <div class="form-group">
                    <label>Épaisseur (cm)</label>
                    <input type="number" step="0.1" name="epaisseurCM" placeholder="0.0" required>
                </div>
                <div class="form-group">
                    <label>Prix Matière Forfaitaire (€)</label>
                    <input type="number" step="0.01" name="matiere" placeholder="0.00" required>
                </div>
            </div>

            <h3 class="section-title">Finitions & Options</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>Rejingot (ML)</label>
                    <input type="number" step="0.01" name="rejingotML" value="0">
                </div>
                <div class="form-group">
                    <label>Nombre d'oreilles</label>
                    <input type="number" name="oreilles" value="0">
                </div>
            </div>

            <button type="submit" class="btn-submit">🔨 Générer le devis</button>
        </form>
    </div>
</div>

</body>
</html>
