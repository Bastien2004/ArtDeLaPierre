<div class="modal fade" id="modalStock" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formStock" action="{{ route('stocks.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="stock_id">

                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-gem me-2"></i><span id="modalTitle">Nouvelle Pierre</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Désignation de la Matière</label>
                        <input type="text" name="matiere" id="matiere" class="form-control"
                               value="Pierre Bleue" placeholder="ex: Pierre Bleue Déclassée" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Quantité (Unités)</label>
                            <input type="number" name="quantite" id="quantite" class="form-control" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Épaisseur (cm)</label>
                            <input type="number" name="epaisseur" id="epaisseur" class="form-control" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Longueur (m)</label>
                            <input type="number" step="0.01" name="longueur" id="longueur" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Largeur (m)</label>
                            <input type="number" step="0.01" name="largeur" id="largeur" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-stone px-4">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center p-3 border-top border-danger border-4">
            <div class="modal-body">
                <div class="mb-3 text-danger"><i class="fa-solid fa-triangle-exclamation fa-4x"></i></div>
                <h5 class="fw-bold">Supprimer ?</h5>
                <p class="text-muted small">Cette action est irréversible.</p>
            </div>
            <form id="formDelete" method="POST">
                @csrf
                @method('DELETE')
                <div class="d-flex justify-content-center gap-2 mb-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Non</button>
                    <button type="submit" class="btn btn-danger">Oui, supprimer</button>
                </div>
            </form>
        </div>
    </div>
</div>
