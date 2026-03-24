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

                {{-- Toggle Pierre / Bloc / Casson --}}
                <div class="btn-group w-100 mb-1" role="group" id="typeToggle">
                    <input type="radio" class="btn-check" name="entreeType" id="typePierre" value="pierre" checked autocomplete="off">
                    <label class="btn btn-outline-secondary" for="typePierre">
                        <i class="fa-solid fa-layer-group me-1"></i> Pierre
                    </label>

                    <input type="radio" class="btn-check" name="entreeType" id="typeBloc" value="bloc" autocomplete="off">
                    <label class="btn btn-outline-secondary" for="typeBloc">
                        <i class="fa-solid fa-cube me-1"></i> Bloc
                    </label>

                    <input type="radio" class="btn-check" name="entreeType" id="typeCasson" value="casson" autocomplete="off">
                    <label class="btn btn-outline-secondary" for="typeCasson">
                        <i class="fa-solid fa-puzzle-piece me-1"></i> Casson
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
                                <label class="form-label fw-bold">Épaisseur (cm)</label>
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

            {{-- ── Formulaire Casson ──────────────────────────────────────── --}}
            <form id="formCasson" action="{{ route('stocks.cassons.store') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="_method" id="formCassonMethod" value="POST">
                <input type="hidden" name="id" id="casson_id">

                <div id="panelCasson">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Désignation de la Matière</label>
                                <input type="text" name="matiere" id="casson_matiere" class="form-control"
                                       value="Pierre Bleue" placeholder="ex: Calcaire, Grès…" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Longueur (m)</label>
                                <input type="number" step="0.01" name="longueur" id="casson_longueur"
                                       class="form-control" placeholder="0.00" min="0" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Largeur (m)</label>
                                <input type="number" step="0.01" name="largeur" id="casson_largeur"
                                       class="form-control" placeholder="0.00" min="0" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Épaisseur (cm)</label>
                                <input type="number" name="epaisseur" id="casson_epaisseur"
                                       class="form-control" min="1" required>
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

{{-- ── Modal Suppression Casson ────────────────────────────────────────────── --}}
<div class="modal fade" id="modalDeleteCasson" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center p-3 border-top border-danger border-4">
            <div class="modal-body">
                <div class="mb-3 text-danger"><i class="fa-solid fa-triangle-exclamation fa-4x"></i></div>
                <h5 class="fw-bold">Supprimer le casson ?</h5>
                <p class="text-muted small">Cette action est irréversible.</p>
            </div>
            <form id="formDeleteCasson" method="POST">
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalStock = document.getElementById('modalStock');
        const bsModal = new bootstrap.Modal(modalStock);

        const typePierre = document.getElementById('typePierre');
        const typeBloc   = document.getElementById('typeBloc');
        const typeCasson = document.getElementById('typeCasson');

        const formStock  = document.getElementById('formStock');
        const formBloc   = document.getElementById('formBloc');
        const formCasson = document.getElementById('formCasson');

        function switchType(type) {
            formStock.style.display  = type === 'pierre' ? '' : 'none';
            formBloc.style.display   = type === 'bloc'   ? '' : 'none';
            formCasson.style.display = type === 'casson' ? '' : 'none';
        }

        typePierre.addEventListener('change', () => switchType('pierre'));
        typeBloc.addEventListener('change',   () => switchType('bloc'));
        typeCasson.addEventListener('change', () => switchType('casson'));

        // --- BOUTON AJOUTER ---
        document.getElementById('btnAjouter').addEventListener('click', function () {
            formStock.reset();
            formBloc.reset();
            formCasson.reset();

            document.getElementById('modalTitle').textContent = 'Nouvelle Entrée';

            // Reset Methods & Actions
            document.getElementById('formMethod').value = 'POST';
            formStock.action = "{{ route('stocks.store') }}";

            document.getElementById('formBlocMethod').value = 'POST';
            formBloc.action = "{{ route('stocks.blocs.store') }}";

            document.getElementById('formCassonMethod').value = 'POST';
            formCasson.action = "{{ route('stocks.cassons.store') }}";

            switchType('pierre');
            typePierre.checked = true;
        });

        // --- ÉDITION PIERRE ---
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-edit');
            if (btn) {
                modalStock.dataset.editing = '1';
                switchType('pierre');
                typePierre.checked = true;

                document.getElementById('modalTitle').textContent = 'Modifier la pierre';
                document.getElementById('stock_id').value = btn.dataset.id;
                document.getElementById('matiere').value = btn.dataset.matiere;
                document.getElementById('quantite').value = btn.dataset.qte;
                document.getElementById('longueur').value = btn.dataset.long;
                document.getElementById('largeur').value = btn.dataset.larg;
                document.getElementById('epaisseur').value = btn.dataset.epais;

                document.getElementById('formMethod').value = 'PUT';
                formStock.action = '/stocks/' + btn.dataset.id;
                bsModal.show();
            }

            // --- ÉDITION BLOC ---
            const btnBloc = e.target.closest('.btn-edit-bloc');
            if (btnBloc) {
                modalStock.dataset.editing = '1';
                switchType('bloc');
                typeBloc.checked = true;

                document.getElementById('modalTitle').textContent = 'Modifier le bloc';
                document.getElementById('bloc_id').value = btnBloc.dataset.id;
                document.getElementById('bloc_reference').value = btnBloc.dataset.reference;
                document.getElementById('bloc_matiere').value = btnBloc.dataset.matiere;
                document.getElementById('bloc_hauteur').value = btnBloc.dataset.hauteur;
                document.getElementById('bloc_largeur').value = btnBloc.dataset.largeur;
                document.getElementById('bloc_longueur').value = btnBloc.dataset.longueur;
                document.getElementById('bloc_poids').value = btnBloc.dataset.poids;

                document.getElementById('formBlocMethod').value = 'PUT';
                formBloc.action = '/stocks/blocs/' + btnBloc.dataset.id;
                bsModal.show();
            }

            // --- ÉDITION CASSON ---
            const btnCasson = e.target.closest('.btn-edit-casson');
            if (btnCasson) {
                modalStock.dataset.editing = '1';
                switchType('casson');
                typeCasson.checked = true;

                document.getElementById('modalTitle').textContent = 'Modifier le casson';
                document.getElementById('casson_id').value = btnCasson.dataset.id;
                document.getElementById('casson_matiere').value = btnCasson.dataset.matiere;
                document.getElementById('casson_longueur').value = btnCasson.dataset.longueur;
                document.getElementById('casson_largeur').value = btnCasson.dataset.largeur;
                document.getElementById('casson_epaisseur').value = btnCasson.dataset.epaisseur;

                document.getElementById('formCassonMethod').value = 'PUT';
                formCasson.action = '/stocks/cassons/' + btnCasson.dataset.id;
                bsModal.show();
            }
        });

        // --- SUPPRESSIONS (Correction des URLs) ---
        document.addEventListener('click', function(e) {
            const delPierre = e.target.closest('.btn-delete');
            if (delPierre) {
                document.getElementById('formDelete').action = '/stocks/' + delPierre.dataset.id;
                new bootstrap.Modal(document.getElementById('modalDelete')).show();
            }

            const delBloc = e.target.closest('.btn-delete-bloc');
            if (delBloc) {
                document.getElementById('formDeleteBloc').action = '/stocks/blocs/' + delBloc.dataset.id;
                new bootstrap.Modal(document.getElementById('modalDeleteBloc')).show();
            }

            const delCasson = e.target.closest('.btn-delete-casson');
            if (delCasson) {
                document.getElementById('formDeleteCasson').action = '/stocks/cassons/' + delCasson.dataset.id;
                new bootstrap.Modal(document.getElementById('modalDeleteCasson')).show();
            }
        });
    });
</script>
