<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Stocks - Pierres</title>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/stocks.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('LogoHead.png') }}" type="image/png">
</head>
<body>

<div class="container-fluid">

    {{-- ── Barre du haut ────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('dashboard') }}" class="btn-back-stone">
            <i class="fa fa-arrow-left"></i> Retour à l'accueil
        </a>
        <div class="d-flex gap-3">
            <a href="{{ route('stocks.pdf') }}" class="btn btn-outline-danger">
                <i class="fa-solid fa-file-pdf me-2"></i>Exporter PDF
            </a>
            <button class="btn btn-stone" data-bs-toggle="modal" data-bs-target="#modalStock" id="btnAjouter">
                <i class="fa fa-plus-circle me-2"></i>Nouvelle Entrée
            </button>
        </div>
    </div>

    @php
        $tarifs = [
            2  => 33.25,
            3  => 44.00,
            4  => 50.50,
            5  => 51.50,
            6  => 60.75,
            8  => 70.75,
            10 => 83.75,
            12 => 100.75,
            15 => 135.75,
        ];
        ksort($tarifs);
    @endphp

    {{-- ── Barre de filtres ─────────────────────────────────────────────── --}}
    <div class="card shadow-sm border-0 filtres-bar p-3 mb-3">
        <div class="d-flex flex-wrap gap-3 align-items-end">

            <div style="flex:1; min-width:140px;">
                <label class="form-label small text-muted mb-1">Matière</label>
                <select id="filtreMatiere" class="form-select form-select-sm">
                    <option value="">Toutes</option>
                    @foreach($stocks->pluck('matiere')->unique()->sort() as $mat)
                        <option value="{{ strtolower($mat) }}">{{ strtoupper($mat) }}</option>
                    @endforeach
                </select>
            </div>

            <div style="flex:1; min-width:180px;">
                <label class="form-label small text-muted mb-1">Épaisseur (cm)</label>
                <div class="d-flex gap-2 align-items-center">
                    <input type="number" id="filtreEpaisMin" class="form-control form-control-sm" placeholder="Min" min="0">
                    <span class="text-muted">—</span>
                    <input type="number" id="filtreEpaisMax" class="form-control form-control-sm" placeholder="Max" min="0">
                </div>
            </div>

            <div style="flex:1; min-width:180px;">
                <label class="form-label small text-muted mb-1">Longueur (m)</label>
                <div class="d-flex gap-2 align-items-center">
                    <input type="number" id="filtreLongMin" class="form-control form-control-sm" placeholder="Min" step="0.01" min="0">
                    <span class="text-muted">—</span>
                    <input type="number" id="filtreLongMax" class="form-control form-control-sm" placeholder="Max" step="0.01" min="0">
                </div>
            </div>

            <div style="flex:1; min-width:180px;">
                <label class="form-label small text-muted mb-1">Largeur (m)</label>
                <div class="d-flex gap-2 align-items-center">
                    <input type="number" id="filtreLargMin" class="form-control form-control-sm" placeholder="Min" step="0.01" min="0">
                    <span class="text-muted">—</span>
                    <input type="number" id="filtreLargMax" class="form-control form-control-sm" placeholder="Max" step="0.01" min="0">
                </div>
            </div>

            <div style="flex:1; min-width:180px;">
                <label class="form-label small text-muted mb-1">Surface (m²)</label>
                <div class="d-flex gap-2 align-items-center">
                    <input type="number" id="filtreSurfMin" class="form-control form-control-sm" placeholder="Min" step="0.01" min="0">
                    <span class="text-muted">—</span>
                    <input type="number" id="filtreSurfMax" class="form-control form-control-sm" placeholder="Max" step="0.01" min="0">
                </div>
            </div>

            <div style="flex:1; min-width:180px;">
                <label class="form-label small text-muted mb-1">Valeur est. (€)</label>
                <div class="d-flex gap-2 align-items-center">
                    <input type="number" id="filtreValMin" class="form-control form-control-sm" placeholder="Min" min="0">
                    <span class="text-muted">—</span>
                    <input type="number" id="filtreValMax" class="form-control form-control-sm" placeholder="Max" min="0">
                </div>
            </div>

            <div class="d-flex gap-2 align-self-end">
                <button id="btnFiltrer" class="btn btn-filtrer">
                    <i class="fa fa-filter me-1"></i>Filtrer
                </button>
                <button id="btnReset" class="btn btn-reset">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>

        <div id="filtresActifs" class="mt-2 d-flex flex-wrap gap-2" style="display:none !important;"></div>
    </div>

    {{-- ── Inventaire des Pierres Bleues ────────────────────────────────── --}}
    <div class="card shadow-sm border-0 p-4">
        <h2 class="mb-4">
            <i class="fa-solid fa-layer-group me-2" style="color: var(--stone-gold);"></i>Inventaire des Pierres Bleues
        </h2>

        <table id="tableStock" class="display table table-hover" style="width:100%">
            <thead>
            <tr>
                <th>Quantité</th>
                <th>Matière</th>
                <th>Dimensions</th>
                <th>Épaisseur</th>
                <th>Surface</th>
                <th>Valeur Est.</th>
                <th class="text-center">Actions</th>
            </tr>
            </thead>
            <tbody>
            @isset($stocks)
                @foreach($stocks as $item)
                    @php
                        $surface = $item->longueur * $item->largeur * $item->quantite;
                        $prixM2  = 0;
                        foreach ($tarifs as $seuil => $prix) {
                            if ($seuil >= $item->epaisseur) { $prixM2 = $prix; break; }
                        }
                        $valeurTotale = $prixM2 > 0
                            ? $surface * $prixM2
                            : ($item->longueur * $item->largeur * ($item->epaisseur / 100) * 2500) * $item->quantite;
                    @endphp
                    <tr>
                        <td class="fw-bold">{{ $item->quantite }} pcs</td>
                        <td><span class="text-uppercase fw-bold">{{ $item->matiere }}</span></td>
                        <td data-longueur="{{ $item->longueur }}" data-largeur="{{ $item->largeur }}">
                            <small>{{ number_format($item->longueur, 2) }} x {{ number_format($item->largeur, 2) }} m</small>
                        </td>
                        <td data-order="{{ $item->epaisseur }}">
                            <span class="badge bg-dark px-3 py-2">{{ $item->epaisseur }} cm</span>
                        </td>
                        <td class="fw-bold">{{ number_format($surface, 2, ',', ' ') }} m²</td>
                        <td class="text-success fw-bold">{{ number_format($valeurTotale, 2, ',', ' ') }} €</td>
                        <td class="text-center">
                            <div class="btn-group gap-2">
                                <button class="btn btn-sm btn-outline-primary border-0 btn-edit"
                                        data-id="{{ $item->id }}"
                                        data-qte="{{ $item->quantite }}"
                                        data-long="{{ $item->longueur }}"
                                        data-larg="{{ $item->largeur }}"
                                        data-matiere="{{ $item->matiere }}"
                                        data-epais="{{ $item->epaisseur }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger border-0 btn-delete"
                                        data-id="{{ $item->id }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endisset
            </tbody>
        </table>
    </div>

    {{-- ── Inventaire des Blocs ──────────────────────────────────────────── --}}
    <div class="card shadow-sm border-0 p-4 mt-4">
        <h2 class="mb-4">
            <i class="fa-solid fa-cube me-2" style="color: var(--stone-gold);"></i>Inventaire des Blocs
        </h2>

        <table id="tableBlocs" class="display table table-hover" style="width:100%">
            <thead>
            <tr>
                <th>Référence</th>
                <th>Matière</th>
                <th>Dimensions (m)</th>
                <th>Volume (m³)</th>
                <th>Poids (t)</th>
                <th>Prix Est.</th>
                <th class="text-center">Actions</th>
            </tr>
            </thead>
            <tbody>
            @isset($blocs)
                @foreach($blocs as $bloc)
                    @php
                        $volume = $bloc->hauteur * $bloc->largeur * $bloc->longueur;
                        $prix   = $bloc->poids * 155.75;
                    @endphp
                    <tr>
                        <td class="fw-bold font-monospace">{{ $bloc->reference }}</td>
                        <td><span class="text-uppercase fw-bold">{{ $bloc->matiere }}</span></td>
                        <td>
                            <small>
                                {{ number_format($bloc->longueur, 0) }}
                                × {{ number_format($bloc->largeur, 0) }}
                                × {{ number_format($bloc->hauteur, 0) }} m
                            </small>
                        </td>
                        <td class="fw-bold">{{ number_format($volume, 3, ',', ' ') }} m³</td>
                        <td>
                            <span class="badge bg-secondary px-3 py-2">
                                {{ number_format($bloc->poids, 3, ',', ' ') }} t
                            </span>
                        </td>
                        <td class="text-success fw-bold">{{ number_format($prix, 2, ',', ' ') }} €</td>
                        <td class="text-center">
                            <div class="btn-group gap-2">
                                <button class="btn btn-sm btn-outline-primary border-0 btn-edit-bloc"
                                        data-id="{{ $bloc->id }}"
                                        data-reference="{{ $bloc->reference }}"
                                        data-matiere="{{ $bloc->matiere }}"
                                        data-hauteur="{{ $bloc->hauteur }}"
                                        data-largeur="{{ $bloc->largeur }}"
                                        data-longueur="{{ $bloc->longueur }}"
                                        data-poids="{{ $bloc->poids }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger border-0 btn-delete-bloc"
                                        data-id="{{ $bloc->id }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endisset
            </tbody>
        </table>
    </div>

    {{-- ── Inventaire des Cassons ────────────────────────────────────────── --}}
    <div class="card shadow-sm border-0 p-4 mt-4">
        <h2 class="mb-4">
            <i class="fa-solid fa-puzzle-piece me-2" style="color: var(--stone-gold);"></i>Inventaire des Cassons
        </h2>

        <table id="tableCassons" class="display table table-hover" style="width:100%">
            <thead>
            <tr>
                <th>Matière</th>
                <th>Quantité</th>
                <th>Longueur (m)</th>
                <th>Largeur (m)</th>
                <th>Épaisseur (cm)</th>
                <th>Valeur Est.</th>
                <th class="text-center">Actions</th>
            </tr>
            </thead>
            <tbody>
            @isset($cassons)
                @foreach($cassons as $casson)
                    @php
                        $valeurCasson = $casson->largeur * $casson->longueur * ($casson->epaisseur / 100) * 2500;
                    @endphp
                    <tr>
                        <td><span class="text-uppercase fw-bold">{{ $casson->matiere }}</span></td>
                        <td>{{ number_format($casson->quantite) }}</td>
                        <td>{{ number_format($casson->longueur, 2) }} m</td>
                        <td>{{ number_format($casson->largeur, 2) }} m</td>
                        <td><span class="badge bg-dark px-3 py-2">{{ $casson->epaisseur }} cm</span></td>
                        <td class="text-success fw-bold">{{ number_format($valeurCasson, 2, ',', ' ') }} €</td>
                        <td class="text-center">
                            <div class="btn-group gap-2">
                                <button class="btn btn-sm btn-outline-primary border-0 btn-edit-casson"
                                        data-id="{{ $casson->id }}"
                                        data-matiere="{{ $casson->matiere }}"
                                        data-longueur="{{ $casson->longueur }}"
                                        data-largeur="{{ $casson->largeur }}"
                                        data-epaisseur="{{ $casson->epaisseur }}"
                                        data-quantite="{{ $casson->quantite }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger border-0 btn-delete-casson"
                                        data-id="{{ $casson->id }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endisset
            </tbody>
        </table>
    </div>

    {{-- ── Inventaire des Autres Pierres ────────────────────────────────── --}}
    <div class="card shadow-sm border-0 p-4 mt-4">
        <h2 class="mb-4">
            <i class="fa-solid fa-boxes-stacked me-2" style="color: var(--stone-gold);"></i>Autres Pierres
        </h2>

        <table id="tableAutres" class="display table table-hover" style="width:100%">
            <thead>
            <tr>
                <th>Matière</th>
                <th>Dimensions</th>
                <th>Épaisseur</th>
                <th>Quantité</th>
                <th>Surface totale</th>
                <th>Prix/m²</th>
                <th>Valeur Est.</th>
                <th class="text-center">Actions</th>
            </tr>
            </thead>
            <tbody>
            @isset($autres)
                @foreach($autres as $autre)
                    @php
                        $surfTotale   = $autre->longueur * $autre->largeur * $autre->quantite;
                        $valeurTotale = $surfTotale * $autre->prix_m2;
                    @endphp
                    <tr>
                        <td><span class="text-uppercase fw-bold">{{ $autre->matiere }}</span></td>
                        <td data-longueur="{{ $autre->longueur }}" data-largeur="{{ $autre->largeur }}">
                            <small>{{ number_format($autre->longueur, 2) }} × {{ number_format($autre->largeur, 2) }} m</small>
                        </td>
                        <td data-order="{{ $autre->epaisseur }}">
                            <span class="badge bg-dark px-3 py-2">{{ $autre->epaisseur }} cm</span>
                        </td>
                        <td class="fw-bold">{{ $autre->quantite }} pcs</td>
                        <td class="fw-bold">{{ number_format($surfTotale, 2, ',', ' ') }} m²</td>
                        <td class="text-muted">{{ number_format($autre->prix_m2, 2, ',', ' ') }} €/m²</td>
                        <td class="text-success fw-bold">{{ number_format($valeurTotale, 2, ',', ' ') }} €</td>
                        <td class="text-center">
                            <div class="btn-group gap-2">
                                <button class="btn btn-sm btn-outline-primary border-0 btn-edit-autre"
                                        data-id="{{ $autre->id }}"
                                        data-matiere="{{ $autre->matiere }}"
                                        data-long="{{ $autre->longueur }}"
                                        data-larg="{{ $autre->largeur }}"
                                        data-epais="{{ $autre->epaisseur }}"
                                        data-qte="{{ $autre->quantite }}"
                                        data-prix="{{ $autre->prix_m2 }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger border-0 btn-delete-autre"
                                        data-id="{{ $autre->id }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endisset
            </tbody>
        </table>
    </div>

</div>{{-- fin container-fluid --}}

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ── DataTables + Filtres (jQuery ready) ─────────────────────────────────────
    $(document).ready(function () {

        const table = $('#tableStock').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
            pageLength: 25,
            order: [[3, 'asc']],
            drawCallback: function () { $('.dataTables_paginate > .pagination').addClass('pagination-sm'); }
        });

        $('#tableBlocs').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
            pageLength: 25,
            order: [[0, 'asc']],
            drawCallback: function () { $('.dataTables_paginate > .pagination').addClass('pagination-sm'); }
        });

        $('#tableCassons').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
            pageLength: 25,
            order: [[0, 'asc']],
            drawCallback: function () { $('.dataTables_paginate > .pagination').addClass('pagination-sm'); }
        });

        $('#tableAutres').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
            pageLength: 25,
            order: [[0, 'asc']],
            drawCallback: function () { $('.dataTables_paginate > .pagination').addClass('pagination-sm'); }
        });

        function colRaw(rowNode, colIndex) {
            const cell = $(rowNode).find('td').eq(colIndex);
            const raw  = cell.data('order');
            if (raw !== undefined) return parseFloat(raw) || 0;
            return parseFloat(cell.text().replace(/[^\d.,-]/g, '').replace(',', '.')) || 0;
        }

        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'tableStock') return true;
            const min = parseFloat($('#filtreEpaisMin').val()), max = parseFloat($('#filtreEpaisMax').val());
            const val = colRaw(table.row(dataIndex).node(), 3);
            if (!isNaN(min) && val < min) return false;
            if (!isNaN(max) && val > max) return false;
            return true;
        });

        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'tableStock') return true;
            const min = parseFloat($('#filtreSurfMin').val()), max = parseFloat($('#filtreSurfMax').val());
            const val = colRaw(table.row(dataIndex).node(), 4);
            if (!isNaN(min) && val < min) return false;
            if (!isNaN(max) && val > max) return false;
            return true;
        });

        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'tableStock') return true;
            const min = parseFloat($('#filtreValMin').val()), max = parseFloat($('#filtreValMax').val());
            const val = colRaw(table.row(dataIndex).node(), 5);
            if (!isNaN(min) && val < min) return false;
            if (!isNaN(max) && val > max) return false;
            return true;
        });

        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'tableStock') return true;
            const longMin = parseFloat($('#filtreLongMin').val()), longMax = parseFloat($('#filtreLongMax').val());
            const largMin = parseFloat($('#filtreLargMin').val()), largMax = parseFloat($('#filtreLargMax').val());
            const cell = $(table.row(dataIndex).node()).find('td').eq(2);
            const long = parseFloat(cell.data('longueur')), larg = parseFloat(cell.data('largeur'));
            if (!isNaN(longMin) && long < longMin) return false;
            if (!isNaN(longMax) && long > longMax) return false;
            if (!isNaN(largMin) && larg < largMin) return false;
            if (!isNaN(largMax) && larg > largMax) return false;
            return true;
        });

        $('#btnFiltrer').on('click', function () {
            table.column(1).search($('#filtreMatiere').val(), false, false).draw();
            const tags = [];
            const matVal = $('#filtreMatiere').val();
            if (matVal) tags.push({ label: matVal.toUpperCase(), id: 'matiere' });
            const epaisMin = $('#filtreEpaisMin').val(), epaisMax = $('#filtreEpaisMax').val();
            if (epaisMin || epaisMax) tags.push({ label: `Épais : ${epaisMin||'0'}–${epaisMax||'∞'} cm`, id: 'epais' });
            const longMin = $('#filtreLongMin').val(), longMax = $('#filtreLongMax').val();
            if (longMin || longMax) tags.push({ label: `Long : ${longMin||'0'}–${longMax||'∞'} m`, id: 'long' });
            const largMin = $('#filtreLargMin').val(), largMax = $('#filtreLargMax').val();
            if (largMin || largMax) tags.push({ label: `Larg : ${largMin||'0'}–${largMax||'∞'} m`, id: 'larg' });
            const surfMin = $('#filtreSurfMin').val(), surfMax = $('#filtreSurfMax').val();
            if (surfMin || surfMax) tags.push({ label: `Surface : ${surfMin||'0'}–${surfMax||'∞'} m²`, id: 'surf' });
            const valMin = $('#filtreValMin').val(), valMax = $('#filtreValMax').val();
            if (valMin || valMax) tags.push({ label: `Valeur : ${valMin||'0'}–${valMax||'∞'} €`, id: 'val' });
            const container = $('#filtresActifs');
            container.empty();
            if (tags.length) {
                tags.forEach(t => container.append(
                    `<span class="badge bg-secondary d-flex align-items-center gap-1">
                    ${t.label}
                    <i class="fa fa-times ms-1" style="cursor:pointer;" data-tag="${t.id}"></i>
                </span>`
                ));
                container.css('display', 'flex');
            } else {
                container.hide();
            }
        });

        $('#filtresActifs').on('click', '.fa-times', function () {
            const tag = $(this).data('tag');
            const map = {
                matiere: ['#filtreMatiere'],
                epais:   ['#filtreEpaisMin', '#filtreEpaisMax'],
                long:    ['#filtreLongMin',  '#filtreLongMax'],
                larg:    ['#filtreLargMin',  '#filtreLargMax'],
                surf:    ['#filtreSurfMin',  '#filtreSurfMax'],
                val:     ['#filtreValMin',   '#filtreValMax'],
            };
            (map[tag] || []).forEach(id => $(id).val(''));
            $('#btnFiltrer').trigger('click');
        });

        $('#btnReset').on('click', function () {
            $([
                '#filtreMatiere','#filtreEpaisMin','#filtreEpaisMax',
                '#filtreLongMin','#filtreLongMax','#filtreLargMin','#filtreLargMax',
                '#filtreSurfMin','#filtreSurfMax','#filtreValMin','#filtreValMax'
            ].join(',')).val('');
            table.column(1).search('').draw();
            $('#filtresActifs').empty().hide();
        });
    });

    // ── Logique des modals (Bootstrap — après chargement du DOM) ─────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const modalStock = document.getElementById('modalStock');
        const bsModal = new bootstrap.Modal(modalStock);

        // Radios
        const typePierre = document.getElementById('typePierre');
        const typeBloc   = document.getElementById('typeBloc');
        const typeCasson = document.getElementById('typeCasson');
        const typeAutre  = document.getElementById('typeAutre');

        // Formulaires
        const formStock  = document.getElementById('formStock');
        const formBloc   = document.getElementById('formBloc');
        const formCasson = document.getElementById('formCasson');
        const formAutre  = document.getElementById('formAutre');

        function switchType(type) {
            formStock.style.display  = type === 'pierre' ? '' : 'none';
            formBloc.style.display   = type === 'bloc'   ? '' : 'none';
            formCasson.style.display = type === 'casson' ? '' : 'none';
            formAutre.style.display  = type === 'autre'  ? '' : 'none';
        }

        typePierre.addEventListener('change', () => switchType('pierre'));
        typeBloc.addEventListener('change',   () => switchType('bloc'));
        typeCasson.addEventListener('change', () => switchType('casson'));
        typeAutre.addEventListener('change',  () => switchType('autre'));

        // --- BOUTON AJOUTER (Reset total) ---
        document.getElementById('btnAjouter').addEventListener('click', function () {
            [formStock, formBloc, formCasson, formAutre].forEach(f => f.reset());
            document.getElementById('modalTitle').textContent = 'Nouvelle Entrée';

            // Reset Routes
            formStock.action = "{{ route('stocks.store') }}";
            formBloc.action = "{{ route('stocks.blocs.store') }}";
            formCasson.action = "{{ route('stocks.cassons.store') }}";
            formAutre.action = "{{ route('stocks.autres.store') }}";

            switchType('pierre');
            typePierre.checked = true;
        });

        // --- CALCULS EN DIRECT POUR "AUTRE" ---
        const calculateAutre = () => {
            const qte = parseFloat(document.getElementById('autre_quantite').value) || 0;
            const L = parseFloat(document.getElementById('autre_longueur').value) || 0;
            const l = parseFloat(document.getElementById('autre_largeur').value) || 0;
            const prix = parseFloat(document.getElementById('autre_prix_m2').value) || 0;

            const surface = qte * L * l;
            const total = surface * prix;

            document.getElementById('previewSurface').textContent = surface.toFixed(2) + ' m²';
            document.getElementById('previewValeur').textContent = total.toFixed(2) + ' €';
        };

        ['autre_quantite', 'autre_longueur', 'autre_largeur', 'autre_prix_m2'].forEach(id => {
            document.getElementById(id).addEventListener('input', calculateAutre);
        });

        // --- ÉDITION "AUTRE" ---
        document.addEventListener('click', function(e) {
            const btnAutre = e.target.closest('.btn-edit-autre');
            if (btnAutre) {
                switchType('autre');
                typeAutre.checked = true;
                document.getElementById('modalTitle').textContent = 'Modifier autre pierre';

                document.getElementById('autre_id').value = btnAutre.dataset.id;
                document.getElementById('autre_matiere').value = btnAutre.dataset.matiere;
                document.getElementById('autre_quantite').value = btnAutre.dataset.qte;
                document.getElementById('autre_longueur').value = btnAutre.dataset.long;
                document.getElementById('autre_largeur').value = btnAutre.dataset.larg;
                document.getElementById('autre_epaisseur').value = btnAutre.dataset.epais;
                document.getElementById('autre_prix_m2').value = btnAutre.dataset.prix;

                document.getElementById('formAutreMethod').value = 'PUT';
                formAutre.action = '/stocks/autres/' + btnAutre.dataset.id;
                calculateAutre();
                bsModal.show();
            }

            // Gestion suppression Autre
            const delAutre = e.target.closest('.btn-delete-autre');
            if (delAutre) {
                document.getElementById('formDeleteAutre').action = '/stocks/autres/' + delAutre.dataset.id;
                new bootstrap.Modal(document.getElementById('modalDeleteAutre')).show();
            }
        });
    });
</script>

@include('partials.modals-stock')

</body>
</html>
