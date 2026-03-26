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

                {{-- Toggle types --}}
                <div class="btn-group w-100 mb-1" role="group" id="typeToggle">
                    <input type="radio" class="btn-check" name="entreeType" id="typePierre" value="pierre" checked autocomplete="off">
                    <label class="btn btn-outline-secondary" for="typePierre"><i class="fa-solid fa-layer-group"></i> Pierre</label>

                    <input type="radio" class="btn-check" name="entreeType" id="typeBloc" value="bloc" autocomplete="off">
                    <label class="btn btn-outline-secondary" for="typeBloc"><i class="fa-solid fa-cube"></i> Bloc</label>

                    <input type="radio" class="btn-check" name="entreeType" id="typeCasson" value="casson" autocomplete="off">
                    <label class="btn btn-outline-secondary" for="typeCasson"><i class="fa-solid fa-puzzle-piece"></i> Casson</label>

                    <input type="radio" class="btn-check" name="entreeType" id="typeAutre" value="autre" autocomplete="off">
                    <label class="btn btn-outline-secondary" for="typeAutre"><i class="fa-solid fa-boxes-stacked"></i> Autre</label>

                    <input type="radio" class="btn-check" name="entreeType" id="typePrix" value="prix" autocomplete="off">
                    <label class="btn btn-outline-secondary" for="typePrix"><i class="fa-solid fa-tag"></i> Prix</label>
                </div>
            </div>

            {{-- Formulaires --}}
            <div class="modal-body p-0">

                {{-- 1. Pierre --}}
                <form id="formStock" action="{{ route('stocks.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="id" id="stock_id">
                    <div class="p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Désignation</label>
                            <input type="text" name="matiere" id="matiere" class="form-control" value="Pierre Bleue" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-6"><label class="form-label fw-bold">Quantité</label><input type="number" name="quantite" id="quantite" class="form-control" required></div>
                            <div class="col-6"><label class="form-label fw-bold">Épaisseur (cm)</label><input type="number" step="0.01" name="epaisseur" id="epaisseur" class="form-control" required></div>
                            <div class="col-6"><label class="form-label fw-bold">Longueur (m)</label><input type="number" step="0.01" name="longueur" id="longueur" class="form-control" required></div>
                            <div class="col-6"><label class="form-label fw-bold">Largeur (m)</label><input type="number" step="0.01" name="largeur" id="largeur" class="form-control" required></div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-stone">Enregistrer</button>
                    </div>
                </form>

                {{-- 2. Bloc --}}
                <form id="formBloc" action="{{ route('stocks.blocs.store') }}" method="POST" style="display:none;">
                    @csrf
                    <input type="hidden" name="_method" id="formBlocMethod" value="POST">
                    <input type="hidden" name="id" id="bloc_id">
                    <div class="p-4">
                        <div class="row g-3">
                            <div class="col-12"><label class="form-label fw-bold">Référence</label><input type="text" name="reference" id="bloc_reference" class="form-control"></div>
                            <div class="col-12"><label class="form-label fw-bold">Matière</label><input type="text" name="matiere" id="bloc_matiere" class="form-control" value="Pierre Bleue" required></div>
                            <div class="col-4"><label class="form-label fw-bold">Long (m)</label><input type="number" step="0.01" name="longueur" id="bloc_longueur" class="form-control" required></div>
                            <div class="col-4"><label class="form-label fw-bold">Larg (m)</label><input type="number" step="0.01" name="largeur" id="bloc_largeur" class="form-control" required></div>
                            <div class="col-4"><label class="form-label fw-bold">Haut (m)</label><input type="number" step="0.01" name="hauteur" id="bloc_hauteur" class="form-control" required></div>
                            <div class="col-12"><label class="form-label fw-bold">Poids (t)</label><input type="number" step="0.001" name="poids" id="bloc_poids" class="form-control" required></div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-stone">Enregistrer</button>
                    </div>
                </form>

                {{-- 3. Casson --}}
                <form id="formCasson" action="{{ route('stocks.cassons.store') }}" method="POST" style="display:none;">
                    @csrf
                    <input type="hidden" name="_method" id="formCassonMethod" value="POST">
                    <input type="hidden" name="id" id="casson_id">
                    <div class="p-4">
                        <div class="row g-3">
                            <div class="col-12"><label class="form-label fw-bold">Matière</label><input type="text" name="matiere" id="casson_matiere" class="form-control" value="Pierre Bleue" required></div>
                            <div class="col-12"><label class="form-label fw-bold">Quantité</label><input type="number" name="quantite" id="casson_quantite" class="form-control" required></div>
                            <div class="col-4"><label class="form-label fw-bold">Long (m)</label><input type="number" step="0.01" name="longueur" id="casson_longueur" class="form-control" required></div>
                            <div class="col-4"><label class="form-label fw-bold">Larg (m)</label><input type="number" step="0.01" name="largeur" id="casson_largeur" class="form-control" required></div>
                            <div class="col-4"><label class="form-label fw-bold">Épais (cm)</label><input type="number" name="epaisseur" id="casson_epaisseur" class="form-control" required></div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-stone">Enregistrer</button>
                    </div>
                </form>

                {{-- 4. Autre --}}
                <form id="formAutre" action="{{ route('stocks.autres.store') }}" method="POST" style="display:none;">
                    @csrf
                    <input type="hidden" name="_method" id="formAutreMethod" value="POST">
                    <input type="hidden" name="id" id="autre_id">
                    <div class="p-4">
                        <div class="row g-3">
                            <div class="col-12"><label class="form-label fw-bold">Matière</label><input type="text" name="matiere" id="autre_matiere" class="form-control" required></div>
                            <div class="col-6"><label class="form-label fw-bold">Quantité</label><input type="number" name="quantite" id="autre_quantite" class="form-control" value="1" required></div>
                            <div class="col-6"><label class="form-label fw-bold">Épaisseur (cm)</label><input type="number" step="0.01" name="epaisseur" id="autre_epaisseur" class="form-control" required></div>
                            <div class="col-6"><label class="form-label fw-bold">Long (m)</label><input type="number" step="0.01" name="longueur" id="autre_longueur" class="form-control" required></div>
                            <div class="col-6"><label class="form-label fw-bold">Larg (m)</label><input type="number" step="0.01" name="largeur" id="autre_largeur" class="form-control" required></div>
                            <div class="col-12"><label class="form-label fw-bold">Prix d'achat (€/m²)</label><input type="number" step="0.01" name="prix_m2" id="autre_prix_m2" class="form-control" required></div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-stone">Enregistrer</button>
                    </div>
                </form>

                {{-- 5. Prix Manuel --}}
                <form id="formPrix" action="{{ route('stocks.prix.store') }}" method="POST" style="display:none;">
                    @csrf
                    <input type="hidden" name="_method" id="formPrixMethod" value="POST">
                    <input type="hidden" name="id" id="prix_id">
                    <div class="p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Désignation / Nom</label>
                            <input type="text" name="nom" id="prix_nom" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Prix (€)</label>
                            <input type="number" step="0.01" name="prix" id="prix_valeur" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-stone">Enregistrer</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

{{-- Modals de suppression (Dynamiques) --}}
@foreach(['' => 'la pierre', 'Bloc' => 'le bloc', 'Casson' => 'le casson', 'Autre' => 'cette pierre', 'Prix' => 'ce prix'] as $key => $label)
    <div class="modal fade" id="modalDelete{{ $key }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content text-center p-3 border-top border-danger border-4">
                <div class="modal-body">
                    <div class="mb-3 text-danger"><i class="fa-solid fa-triangle-exclamation fa-3x"></i></div>
                    <h5 class="fw-bold">Supprimer {{ $label }} ?</h5>
                    <p class="text-muted small">Action irréversible.</p>
                </div>
                <form id="formDelete{{ $key }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Non</button>
                        <button type="submit" class="btn btn-danger btn-sm">Oui, supprimer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalStock = document.getElementById('modalStock');
        const bsModal = new bootstrap.Modal(modalStock);

        const forms = {
            pierre: document.getElementById('formStock'),
            bloc:   document.getElementById('formBloc'),
            casson: document.getElementById('formCasson'),
            autre:  document.getElementById('formAutre'),
            prix:   document.getElementById('formPrix')
        };

        const radios = {
            pierre: document.getElementById('typePierre'),
            bloc:   document.getElementById('typeBloc'),
            casson: document.getElementById('typeCasson'),
            autre:  document.getElementById('typeAutre'),
            prix:   document.getElementById('typePrix')
        };

        function switchType(type) {
            Object.keys(forms).forEach(key => {
                if(forms[key]) forms[key].style.display = (key === type) ? '' : 'none';
            });
        }

        // Toggle au clic sur les radios
        Object.keys(radios).forEach(key => {
            if(radios[key]) {
                radios[key].addEventListener('change', () => switchType(key));
            }
        });

        // --- BOUTON AJOUTER (RESET) ---
        const btnAjouter = document.getElementById('btnAjouter');
        if(btnAjouter) {
            btnAjouter.addEventListener('click', function () {
                Object.values(forms).forEach(f => f && f.reset());
                document.getElementById('modalTitle').textContent = 'Nouvelle Entrée';

                // Reset des Actions
                forms.pierre.action = "{{ route('stocks.store') }}";
                forms.bloc.action   = "{{ route('stocks.blocs.store') }}";
                forms.casson.action = "{{ route('stocks.cassons.store') }}";
                forms.autre.action  = "{{ route('stocks.autres.store') }}";
                forms.prix.action   = "{{ route('stocks.prix.store') }}";

                // Reset des Methods
                document.getElementById('formMethod').value = 'POST';
                document.getElementById('formBlocMethod').value = 'POST';
                document.getElementById('formCassonMethod').value = 'POST';
                document.getElementById('formAutreMethod').value = 'POST';
                document.getElementById('formPrixMethod').value = 'POST';

                switchType('pierre');
                radios.pierre.checked = true;
            });
        }

        // --- GESTION DES CLICS (EDITION & SUPPRESSION) ---
        document.addEventListener('click', function(e) {

            // 1. EDITION PIERRE
            const btnPierre = e.target.closest('.btn-edit');
            if (btnPierre) {
                switchType('pierre'); radios.pierre.checked = true;
                document.getElementById('modalTitle').textContent = 'Modifier Pierre';
                document.getElementById('stock_id').value = btnPierre.dataset.id;
                document.getElementById('matiere').value = btnPierre.dataset.matiere;
                document.getElementById('quantite').value = btnPierre.dataset.qte;
                document.getElementById('longueur').value = btnPierre.dataset.long;
                document.getElementById('largeur').value = btnPierre.dataset.larg;
                document.getElementById('epaisseur').value = btnPierre.dataset.epais;
                document.getElementById('formMethod').value = 'PUT';
                forms.pierre.action = '/stocks/' + btnPierre.dataset.id;
                bsModal.show();
            }

            // 2. EDITION BLOC
            const btnBloc = e.target.closest('.btn-edit-bloc');
            if (btnBloc) {
                switchType('bloc'); radios.bloc.checked = true;
                document.getElementById('modalTitle').textContent = 'Modifier Bloc';
                document.getElementById('bloc_id').value = btnBloc.dataset.id;
                document.getElementById('bloc_reference').value = btnBloc.dataset.reference;
                document.getElementById('bloc_matiere').value = btnBloc.dataset.matiere;
                document.getElementById('bloc_hauteur').value = btnBloc.dataset.hauteur;
                document.getElementById('bloc_largeur').value = btnBloc.dataset.largeur;
                document.getElementById('bloc_longueur').value = btnBloc.dataset.longueur;
                document.getElementById('bloc_poids').value = btnBloc.dataset.poids;
                document.getElementById('formBlocMethod').value = 'PUT';
                forms.bloc.action = '/stocks/blocs/' + btnBloc.dataset.id;
                bsModal.show();
            }

            // 3. EDITION CASSON
            const btnCasson = e.target.closest('.btn-edit-casson');
            if (btnCasson) {
                switchType('casson'); radios.casson.checked = true;
                document.getElementById('modalTitle').textContent = 'Modifier Casson';
                document.getElementById('casson_id').value = btnCasson.dataset.id;
                document.getElementById('casson_matiere').value = btnCasson.dataset.matiere;
                document.getElementById('casson_longueur').value = btnCasson.dataset.longueur;
                document.getElementById('casson_largeur').value = btnCasson.dataset.largeur;
                document.getElementById('casson_epaisseur').value = btnCasson.dataset.epaisseur;
                document.getElementById('casson_quantite').value = btnCasson.dataset.quantite;
                document.getElementById('formCassonMethod').value = 'PUT';
                forms.casson.action = '/stocks/cassons/' + btnCasson.dataset.id;
                bsModal.show();
            }

            // 4. EDITION AUTRE
            const btnAutre = e.target.closest('.btn-edit-autre');
            if (btnAutre) {
                switchType('autre'); radios.autre.checked = true;
                document.getElementById('modalTitle').textContent = 'Modifier Autre Pierre';
                document.getElementById('autre_id').value = btnAutre.dataset.id;
                document.getElementById('autre_matiere').value = btnAutre.dataset.matiere;
                document.getElementById('autre_quantite').value = btnAutre.dataset.qte;
                document.getElementById('autre_longueur').value = btnAutre.dataset.long;
                document.getElementById('autre_largeur').value = btnAutre.dataset.larg;
                document.getElementById('autre_epaisseur').value = btnAutre.dataset.epais;
                document.getElementById('autre_prix_m2').value = btnAutre.dataset.prix;
                document.getElementById('formAutreMethod').value = 'PUT';
                forms.autre.action = '/stocks/autres/' + btnAutre.dataset.id;
                bsModal.show();
            }

            // 5. EDITION PRIX
            const btnPrix = e.target.closest('.btn-edit-prix');
            if (btnPrix) {
                switchType('prix'); radios.prix.checked = true;
                document.getElementById('modalTitle').textContent = 'Modifier Prix';
                document.getElementById('prix_id').value = btnPrix.dataset.id;
                document.getElementById('prix_nom').value = btnPrix.dataset.nom;
                document.getElementById('prix_valeur').value = btnPrix.dataset.prix;
                document.getElementById('formPrixMethod').value = 'PUT';
                forms.prix.action = '/stocks/prix/' + btnPrix.dataset.id;
                bsModal.show();
            }

            // --- GESTION DES SUPPRESSIONS ---
            const delConfig = [
                { selector: '.btn-delete',       form: '#formDelete',       url: '/stocks/' },
                { selector: '.btn-delete-bloc',  form: '#formDeleteBloc',   url: '/stocks/blocs/' },
                { selector: '.btn-delete-casson',form: '#formDeleteCasson', url: '/stocks/cassons/' },
                { selector: '.btn-delete-autre', form: '#formDeleteAutre',  url: '/stocks/autres/' },
                { selector: '.btn-delete-prix',  form: '#formDeletePrix',   url: '/stocks/prix/' }
            ];

            delConfig.forEach(conf => {
                const btn = e.target.closest(conf.selector);
                if (btn) {
                    const form = document.querySelector(conf.form);
                    form.action = conf.url + btn.dataset.id;
                    const modalId = conf.form.replace('#form', '#modal');
                    new bootstrap.Modal(document.querySelector(modalId)).show();
                }
            });
        });
    });
</script>
