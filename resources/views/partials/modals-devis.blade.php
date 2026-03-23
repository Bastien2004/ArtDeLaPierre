<div class="modal fade" id="modificationModal" tabindex="-1" aria-labelledby="modificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modificationModalLabel">Modifier la ligne #<span id="display_id"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Type de Pierre</label>
                        <input type="text" name="typePierre" id="edit_pierre" class="form-control">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longueur (m)</label>
                            <input type="number" step="0.0001" name="longueurM" id="edit_long" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Largeur (m)</label>
                            <input type="number" step="0.0001" name="largeurM" id="edit_larg" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Prix M² (€)</label>
                            <input type="number" step="0.0001" name="prixM2" id="edit_prix" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="number" name="nombrePierre" id="edit_nb" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Épaisseur (cm)</label>
                            <input type="number" step="0.001" name="epaisseur" id="edit_epaisseur" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Poids Total (kg)</label>
                            <input type="number" step="0.001" name="poids" id="edit_poids" class="form-control" readonly
                                   style="background-color: #f8f9fa; font-weight: bold; color: #d4af37; text-align: center; font-size: 1.2em;">
                        </div>
                    </div>

                    <hr>
                    <h6>Spécificités / Options</h6>

                    <div class="mb-3 d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="addSpecToEdit('Ciselage', 18, 'ml')">
                            <i class="fa fa-cut"></i> + Ciselage (18€/ml)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="addSpecToEdit('Rejingot', 16, 'ml')">
                            <i class="fa fa-ruler-combined"></i> + Rejingot (16€/ml)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="addSpecToEdit('Oreilles', 5, 'u')">
                            <i class="fa fa-tag"></i> + Oreilles (5€/u)
                        </button>
                    </div>

                    <div id="wrapper-specs-edit"></div>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="add-spec-manual-edit">+ Option personnalisée</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>




<div class="modal fade" id="modalLivraison" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <form id="formLivraison" method="POST" action="{{ route('devis.updateLivraison') }}">
                @csrf
                <input type="hidden" name="client" id="livraison_client">
                <input type="hidden" name="date" id="livraison_date">

                <div class="modal-header">
                    <h6 class="modal-title">Modifier le transport</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Montant HT (€)</label>
                    <input type="number" step="0.01" name="montant" id="livraison_input" class="form-control form-control-lg">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>




<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p>Êtes-vous sûr de vouloir supprimer la ligne <strong>#<span id="delete_display_id"></span></strong> ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="pdfRefModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Référence du Devis</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="custom_ref" class="form-label fw-bold">Entrez la référence :</label>
                    <input type="text" id="custom_ref" class="form-control" placeholder="Ex: REF Lambert rue des clematites 62940 haillicourt" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" id="confirmDownload" class="btn btn-primary">Générer le PDF</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEmail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-envelope me-2"></i>Envoyer le devis par mail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="email_client">
                <input type="hidden" id="email_date">
                <div class="mb-3">
                    <label class="form-label">Destinataire</label>
                    <input type="email" id="email_destinataire" class="form-control" placeholder="client@exemple.com">
                </div>
                <div class="mb-3">
                    <label class="form-label">Objet</label>
                    <input type="text" id="email_objet" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea id="email_message" class="form-control" rows="8"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" id="btn-send-email" class="btn btn-primary">
                    <i class="fa-solid fa-paper-plane me-1"></i> Envoyer
                </button>
            </div>
        </div>
    </div>
</div>
