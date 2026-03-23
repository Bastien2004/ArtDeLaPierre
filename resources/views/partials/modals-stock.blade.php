<div class="modal fade" id="modalStock" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            {{-- Sélecteur de type --}}
            <div class="modal-header pb-0 border-0 flex-column align-items-start">
                <div class="d-flex justify-content-between w-100 mb-3">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-gem me-2"></i>
                        <span id="modalTitle">Nouvelle Entrée</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- Toggle Pierre / Bloc --}}
                <div class="btn-group w-100 mb-1" role="group" id="typeToggle">
                    <input type="radio" class="btn-check" name="entreeType" id="typePierre" value="pierre" checked autocomplete="off">
                    <label class="btn btn-outline-secondary" for="typePierre">
                        <i class="fa-solid fa-layer-group me-1"></i> Pierre
                    </label>

                    <input type="radio" class="btn-check" name="entreeType" id="typeBloc" value="bloc" autocomplete="off">
                    <label class="btn btn-outline-secondary" for="typeBloc">
                        <i class="fa-solid fa-cube me-1"></i> Bloc
                    </label>
                </div>
            </div>

            {{-- ── Formulaire Pierre ──────────────────────────────────────── --}}
            <form id="formStock" action="{{ route('stocks.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="stock_id">

                <div id="panelPierre">
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
                                <label class="form-label fw-bold">Épaisseur (m)</label>
                                <input type="number" step="0.01" name="epaisseur" id="epaisseur" class="form-control" required>
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
                </div>
            </form>

            {{-- ── Formulaire Bloc ────────────────────────────────────────── --}}
            <form id="formBloc" action="{{ route('stocks.blocs.store') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="_method" id="formBlocMethod" value="POST">
                <input type="hidden" name="id" id="bloc_id">

                <div id="panelBloc">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Référence</label>
                                <input type="text" name="reference" id="bloc_reference" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Désignation de la Matière</label>
                                <input type="text" name="matiere" id="bloc_matiere" class="form-control"
                                       value="Pierre Bleue" placeholder="ex: Pierre Bleue" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-bold">Longueur (m)</label>
                                <input type="number" step="0.01" name="longueur" id="bloc_longueur" class="form-control" placeholder="0" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-bold">Largeur (m)</label>
                                <input type="number" step="0.01" name="largeur" id="bloc_largeur" class="form-control" placeholder="0" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-bold">Hauteur (m)</label>
                                <input type="number" step="0.01" name="hauteur" id="bloc_hauteur" class="form-control" placeholder="0" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Poids (tonnes)</label>
                                <input type="number" step="0.001" name="poids" id="bloc_poids" class="form-control" placeholder="0.000" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-stone px-4">Enregistrer</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- ── Modal Suppression Pierre ────────────────────────────────────────────── --}}
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

{{-- ── Modal Suppression Bloc ──────────────────────────────────────────────── --}}
<div class="modal fade" id="modalDeleteBloc" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center p-3 border-top border-danger border-4">
            <div class="modal-body">
                <div class="mb-3 text-danger"><i class="fa-solid fa-triangle-exclamation fa-4x"></i></div>
                <h5 class="fw-bold">Supprimer le bloc ?</h5>
                <p class="text-muted small">Cette action est irréversible.</p>
            </div>
            <form id="formDeleteBloc" method="POST">
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


{{-- ── JS : switch Pierre / Bloc + gestion édition blocs ─────────────────── --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // ── Toggle Pierre / Bloc ────────────────────────────────────────────────
        const typePierre = document.getElementById('typePierre');
        const typeBloc   = document.getElementById('typeBloc');
        const formStock  = document.getElementById('formStock');
        const formBloc   = document.getElementById('formBloc');

        function switchType(type) {
            if (type === 'pierre') {
                formStock.style.display = '';
                formBloc.style.display  = 'none';
            } else {
                formStock.style.display = 'none';
                formBloc.style.display  = '';
            }
        }

        typePierre.addEventListener('change', () => switchType('pierre'));
        typeBloc.addEventListener('change',   () => switchType('bloc'));

        // Réinitialiser le toggle à l'ouverture du modal (sauf si édition)
        document.getElementById('modalStock').addEventListener('show.bs.modal', function () {
            if (!this.dataset.editing) {
                typePierre.checked = true;
                switchType('pierre');
            }
            delete this.dataset.editing;
        });

        // ── Bouton Ajouter (reset complet) ──────────────────────────────────────
        document.getElementById('btnAjouter').addEventListener('click', function () {
            document.getElementById('formStock')[0]?.reset?.();
            formStock.reset?.();
            formBloc.reset?.();
            document.getElementById('modalTitle').textContent = 'Nouvelle Entrée';
            document.getElementById('formMethod').value = 'POST';
            formStock.action = "{{ route('stocks.store') }}";
            document.getElementById('formBlocMethod').value = 'POST';
            formBloc.action = "{{ route('stocks.blocs.store') }}";
        });

        // ── Édition Bloc ─────────────────────────────────────────────────────────
        document.getElementById('tableBlocs')?.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-edit-bloc');
            if (!btn) return;

            document.getElementById('modalStock').dataset.editing = '1';
            typeBloc.checked = true;
            typePierre.checked = false;
            switchType('bloc');

            document.getElementById('modalTitle').textContent   = 'Modifier le bloc';
            document.getElementById('bloc_id').value            = btn.dataset.id;
            document.getElementById('bloc_reference').value     = btn.dataset.reference;
            document.getElementById('bloc_matiere').value       = btn.dataset.matiere;
            document.getElementById('bloc_hauteur').value       = btn.dataset.hauteur;
            document.getElementById('bloc_largeur').value       = btn.dataset.largeur;
            document.getElementById('bloc_longueur').value      = btn.dataset.longueur;
            document.getElementById('bloc_poids').value         = btn.dataset.poids;
            document.getElementById('formBlocMethod').value     = 'PUT';
            formBloc.action = '/stocks/blocs/' + btn.dataset.id;

            new bootstrap.Modal(document.getElementById('modalStock')).show();
        });

        // ── Suppression Bloc ─────────────────────────────────────────────────────
        document.getElementById('tableBlocs')?.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-delete-bloc');
            if (!btn) return;
            document.getElementById('formDeleteBloc').action = '/stocks/blocs/' + btn.dataset.id;
            new bootstrap.Modal(document.getElementById('modalDeleteBloc')).show();
        });
    });
</script>
