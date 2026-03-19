<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier des Livraisons — Art de la Pierre</title>
    <link rel="stylesheet" href="{{ asset('css/calendrier.css') }}">
    <link rel="icon" href="{{ asset('LogoHead.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="page-wrapper">

    {{-- ═══════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════ --}}
    <header class="cal-header">
        <div class="header-left">
            <a href="{{ route('devis.index') }}" class="btn-back">
                <i class="fa fa-arrow-left"></i>
            </a>
            <div class="header-title">
                <span class="header-eyebrow">Art de la Pierre</span>
                <h1>Calendrier des Livraisons</h1>
            </div>
        </div>

        <div class="header-center">
            <button class="nav-btn" id="btn-prev"><i class="fa fa-chevron-left"></i></button>
            <div class="current-period" id="current-period">—</div>
            <button class="nav-btn" id="btn-next"><i class="fa fa-chevron-right"></i></button>
            <button class="btn-today" id="btn-today">Aujourd'hui</button>
        </div>

        <div class="header-right">
            <div class="view-switcher">
                <button class="view-btn active" data-view="month">
                    <i class="fa fa-calendar"></i> Mois
                </button>
                <button class="view-btn" data-view="week">
                    <i class="fa fa-calendar-week"></i> Semaine
                </button>
            </div>
        </div>
    </header>

    {{-- ═══════════════════════════════════════════
         COMPTEUR LIVRAISONS DU MOIS
    ═══════════════════════════════════════════ --}}
    <div class="stats-bar">
        <div class="stat-chip" id="stat-total">
            <i class="fa fa-truck"></i>
            <span id="stat-count">—</span> livraison(s) ce mois
        </div>
        <div class="stat-chip" id="stat-period-label">
            <i class="fa fa-map-marker-alt"></i>
            <span id="stat-destinations">—</span> destination(s) distincte(s)
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         CALENDRIER
    ═══════════════════════════════════════════ --}}
    <main class="cal-main">
        {{-- Vue MOIS --}}
        <div id="view-month" class="cal-view active">
            <div class="month-grid-header">
                <div>Lun</div><div>Mar</div><div>Mer</div>
                <div>Jeu</div><div>Ven</div>
                <div class="weekend">Sam</div><div class="weekend">Dim</div>
            </div>
            <div class="month-grid" id="month-grid"></div>
        </div>

        {{-- Vue SEMAINE --}}
        <div id="view-week" class="cal-view">
            <div class="week-grid" id="week-grid"></div>
        </div>
    </main>

</div>

{{-- ═══════════════════════════════════════════
     MODAL DÉTAIL LIVRAISON
═══════════════════════════════════════════ --}}
<div class="modal-overlay" id="modal-overlay">
    <div class="modal-card" id="modal-card">
        <button class="modal-close" id="modal-close"><i class="fa fa-times"></i></button>
        <div class="modal-header">
            <div class="modal-icon"><i class="fa fa-truck"></i></div>
            <div>
                <div class="modal-date" id="modal-date"></div>
                <div class="modal-client" id="modal-client"></div>
            </div>
        </div>
        <div class="modal-body">
            <div class="modal-row">
                <i class="fa fa-map-marker-alt"></i>
                <span id="modal-adresse">—</span>
            </div>
            <div class="modal-row">
                <i class="fa fa-euro-sign"></i>
                <span id="modal-montant">—</span>
            </div>
            <div class="modal-row" id="modal-devis-row">
                <i class="fa fa-file-invoice"></i>
                <a id="modal-devis-link" href="#" class="modal-link">Voir le devis</a>
            </div>
        </div>
    </div>
</div>


@php
    $livraisonsJson = $livraisons->map(function($d) {
        return [
            'id'      => $d->id,
            'date'    => $d->datefindevis,
            'client'  => $d->client,
            'adresse' => $d->adresse ?? '—',
            'montant' => $d->montant_ttc ?? 0,
            'url'     => route('devis.index'),
        ];
    });
@endphp

<script>
    const LIVRAISONS = {!! json_encode($livraisonsJson) !!};


    (function () {
        'use strict';

        /* ─── État global ──────────────────────────────── */
        let today     = new Date();
        today.setHours(0, 0, 0, 0);
        let cursor    = new Date(today);   // date de référence navigation
        let activeView = 'month';          // 'month' | 'week'

        /* ─── Utilitaires date ─────────────────────────── */
        const fmt = {
            ymd: d => {
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${y}-${m}-${day}`;
            },
            dayName: d => ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'][d.getDay()],
            monthName: d => ['Janvier','Février','Mars','Avril','Mai','Juin',
                'Juillet','Août','Septembre','Octobre','Novembre','Décembre'][d.getMonth()],
            longDate: d => {
                const days = ['dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi'];
                return `${days[d.getDay()]} ${d.getDate()} ${fmt.monthName(d)} ${d.getFullYear()}`;
            },
            euro: n => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(n),
        };

        /* ─── Index des livraisons par date ────────────── */
        const byDate = {};
        (LIVRAISONS || []).forEach(l => {
            if (!l.date) return;
            const key = l.date.substring(0, 10); // "YYYY-MM-DD"
            if (!byDate[key]) byDate[key] = [];
            byDate[key].push(l);
        });

        /* ─── Éléments DOM ─────────────────────────────── */
        const periodEl   = document.getElementById('current-period');
        const monthGrid  = document.getElementById('month-grid');
        const weekGrid   = document.getElementById('week-grid');
        const statCount  = document.getElementById('stat-count');
        const statDest   = document.getElementById('stat-destinations');

        /* ─── Navigation ───────────────────────────────── */
        document.getElementById('btn-prev').addEventListener('click', () => {
            if (activeView === 'month') cursor.setMonth(cursor.getMonth() - 1);
            else cursor.setDate(cursor.getDate() - 7);
            render();
        });
        document.getElementById('btn-next').addEventListener('click', () => {
            if (activeView === 'month') cursor.setMonth(cursor.getMonth() + 1);
            else cursor.setDate(cursor.getDate() + 7);
            render();
        });
        document.getElementById('btn-today').addEventListener('click', () => {
            cursor = new Date(today);
            render();
        });

        /* ─── Switch de vue ────────────────────────────── */
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                activeView = btn.dataset.view;

                document.querySelectorAll('.cal-view').forEach(v => v.classList.remove('active'));
                document.getElementById(`view-${activeView}`).classList.add('active');
                render();
            });
        });

        /* ─── Render principal ─────────────────────────── */
        function render() {
            if (activeView === 'month') renderMonth();
            else renderWeek();
            updateStats();
        }

        /* ════════════════════════════════════════════════
           VUE MOIS
        ════════════════════════════════════════════════ */
        function renderMonth() {
            periodEl.textContent = `${fmt.monthName(cursor)} ${cursor.getFullYear()}`;
            monthGrid.innerHTML = '';

            const year  = cursor.getFullYear();
            const month = cursor.getMonth();

            // 1er du mois → ajuster pour semaine lundi-dimanche
            const firstDay = new Date(year, month, 1);
            let startOffset = firstDay.getDay() - 1; // lundi=0
            if (startOffset < 0) startOffset = 6;

            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - startOffset);

            // 6 semaines × 7 jours = 42 cellules
            for (let i = 0; i < 42; i++) {
                const cellDate = new Date(startDate);
                cellDate.setDate(startDate.getDate() + i);

                const cell = buildDayCell(cellDate, month);
                monthGrid.appendChild(cell);
            }
        }

        function buildDayCell(date, currentMonth) {
            const key      = fmt.ymd(date);
            const events   = byDate[key] || [];
            const isOther  = date.getMonth() !== currentMonth;
            const isToday  = fmt.ymd(date) === fmt.ymd(today);
            const isWeekend = date.getDay() === 0 || date.getDay() === 6;

            const cell = document.createElement('div');
            cell.className = [
                'day-cell',
                isOther   ? 'other-month' : '',
                isToday   ? 'is-today'    : '',
                isWeekend ? 'is-weekend'  : '',
            ].filter(Boolean).join(' ');

            // Numéro du jour
            const numEl = document.createElement('div');
            numEl.className = 'day-number';
            numEl.textContent = date.getDate();
            cell.appendChild(numEl);

            // Pills événements (max 3 puis badge "+n")
            const MAX = 3;
            events.slice(0, MAX).forEach(ev => {
                cell.appendChild(buildPill(ev, date));
            });
            if (events.length > MAX) {
                const more = document.createElement('div');
                more.className = 'more-badge';
                more.textContent = `+${events.length - MAX} autres`;
                more.addEventListener('click', () => {
                    // Affiche la première en attente
                    openModal(events[MAX], date);
                });
                cell.appendChild(more);
            }

            return cell;
        }

        function buildPill(ev, date) {
            const pill = document.createElement('div');
            pill.className = 'event-pill';
            pill.innerHTML = `<i class="fa fa-truck"></i>${ev.client}`;
            pill.addEventListener('click', () => openModal(ev, date));
            return pill;
        }

        /* ════════════════════════════════════════════════
           VUE SEMAINE
        ════════════════════════════════════════════════ */
        function renderWeek() {
            // Trouver le lundi de la semaine du cursor
            const monday = new Date(cursor);
            const dow = monday.getDay(); // 0=dim
            const diff = dow === 0 ? -6 : 1 - dow;
            monday.setDate(monday.getDate() + diff);

            const sunday = new Date(monday);
            sunday.setDate(monday.getDate() + 6);

            periodEl.textContent =
                `${monday.getDate()} ${fmt.monthName(monday)} — ${sunday.getDate()} ${fmt.monthName(sunday)} ${sunday.getFullYear()}`;

            weekGrid.innerHTML = '';

            // Gutter gauche (vide déco)
            const gutterHeader = document.createElement('div');
            gutterHeader.className = 'week-time-gutter week-gutter-header';
            weekGrid.appendChild(gutterHeader);

            // 7 colonnes jours
            for (let i = 0; i < 7; i++) {
                const d = new Date(monday);
                d.setDate(monday.getDate() + i);

                const isToday   = fmt.ymd(d) === fmt.ymd(today);
                const isWeekend = d.getDay() === 0 || d.getDay() === 6;

                const col = document.createElement('div');
                col.className = [
                    'week-day-col',
                    isToday   ? 'is-today'   : '',
                    isWeekend ? 'is-weekend' : '',
                ].filter(Boolean).join(' ');

                // Header
                const header = document.createElement('div');
                header.className = 'week-day-header';
                const dayNameEl = document.createElement('span');
                dayNameEl.className = 'week-day-name';
                dayNameEl.textContent = fmt.dayName(d);
                const dayNumEl = document.createElement('div');
                dayNumEl.className = 'week-day-num';
                dayNumEl.textContent = d.getDate();
                header.appendChild(dayNameEl);
                header.appendChild(dayNumEl);
                col.appendChild(header);

                // Body avec cartes
                const body = document.createElement('div');
                body.className = 'week-day-body';

                const events = byDate[fmt.ymd(d)] || [];
                events.forEach(ev => {
                    body.appendChild(buildWeekCard(ev, d));
                });

                col.appendChild(body);
                weekGrid.appendChild(col);
            }

            // Gutter corps (vide déco, doit s'aligner sous le header)
            const gutterBody = document.createElement('div');
            gutterBody.className = 'week-time-gutter week-gutter-body';
            // On le place en premier dans la grille via order
            gutterBody.style.order = '-1'; // pas possible en grid sans reorder
            // On utilise la grille CSS — pas besoin de gutter body séparé ici
        }

        function buildWeekCard(ev, date) {
            const card = document.createElement('div');
            card.className = 'week-event-card';
            card.innerHTML = `
            <div class="wec-client"><i class="fa fa-user" style="opacity:.5;margin-right:5px;font-size:.7rem"></i>${ev.client}</div>
            <div class="wec-adresse"><i class="fa fa-map-marker-alt" style="margin-right:4px;opacity:.5"></i>${ev.adresse}</div>
            <div class="wec-montant">${fmt.euro(ev.montant)}</div>
        `;
            card.addEventListener('click', () => openModal(ev, date));
            return card;
        }

        /* ════════════════════════════════════════════════
           STATS BAR
        ════════════════════════════════════════════════ */
        function updateStats() {
            let keys = [];

            if (activeView === 'month') {
                const year  = cursor.getFullYear();
                const month = cursor.getMonth();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                for (let d = 1; d <= daysInMonth; d++) {
                    const key = `${year}-${String(month + 1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
                    if (byDate[key]) keys.push(key);
                }
            } else {
                const monday = new Date(cursor);
                const dow = monday.getDay();
                const diff = dow === 0 ? -6 : 1 - dow;
                monday.setDate(monday.getDate() + diff);
                for (let i = 0; i < 7; i++) {
                    const d = new Date(monday);
                    d.setDate(monday.getDate() + i);
                    const key = fmt.ymd(d);
                    if (byDate[key]) keys.push(key);
                }
            }

            const allEvs  = keys.flatMap(k => byDate[k]);
            const total   = allEvs.length;
            const dests   = new Set(allEvs.map(e => e.adresse)).size;

            statCount.textContent = total;
            statDest.textContent  = dests;
        }

        /* ════════════════════════════════════════════════
           MODAL
        ════════════════════════════════════════════════ */
        const overlay  = document.getElementById('modal-overlay');
        const closeBtn = document.getElementById('modal-close');

        function openModal(ev, date) {
            document.getElementById('modal-date').textContent    = fmt.longDate(date);
            document.getElementById('modal-client').textContent  = ev.client;
            document.getElementById('modal-adresse').textContent = ev.adresse || '—';
            document.getElementById('modal-montant').textContent = fmt.euro(ev.montant);
            document.getElementById('modal-devis-link').href     = ev.url || '#';
            overlay.classList.add('open');
        }

        function closeModal() {
            overlay.classList.remove('open');
        }

        closeBtn.addEventListener('click', closeModal);
        overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        /* ─── Init ─────────────────────────────────────── */
        render();

    })();
</script>
</body>
</html>
