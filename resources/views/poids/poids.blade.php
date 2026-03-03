<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/poids.css') }}">

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex justify-end mb-6">
                <a href="{{ route('dashboard') }}" class="btn-retour-stone">
                    <i class="fas fa-arrow-left mr-2"></i> Retour au tableau de bord
                </a>
            </div>

            <div class="calculateur-box">
                <h2 class="font-stone-title mb-6" style="color: #2c3e50; font-size: 1.5rem;">⚖️ Calculateur de Poids Rapide</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Épaisseur (en cm)</label>
                        <input type="number" id="calc_cm" step="0.1" class="w-full rounded-lg border-gray-300 shadow-sm" placeholder="Ex: 3">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Surface (en m²)</label>
                        <input type="number" id="calc_m2" step="0.01" value="1" class="w-full rounded-lg border-gray-300 shadow-sm">
                    </div>

                    <div class="resultat-display">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Poids total estimé</label>
                        <span id="calc_resultat" class="text-3xl font-bold text-dark">0,00</span>
                        <span class="text-xl font-bold text-dark"> kg</span>
                    </div>
                </div>
            </div>

            <div class="poids-container">
                <div class="mb-4">
                    <h2>Tableau de référence des poids (Pierre Bleue)</h2>
                </div>

                <div class="abaque-scroll">
                    <table class="table-poids">
                        <thead>
                        <tr>
                            <th style="text-align: left;">ÉPAISSEUR</th>
                            @foreach($materiaux as $m)
                                <th>{{ number_format($m->epaisseurCM, 1, ',', ' ') }} cm</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="text-align: left; background: rgba(0,0,0,0.2); color: #c5a059; font-weight: bold;">POIDS AU M²</td>
                            @foreach($materiaux as $m)
                                <td>{{ round($m->poids_m2) }} <small class="opacity-50">kg</small></td>
                            @endforeach
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-between items-center opacity-40 italic text-xs">
                    <span>* Basé sur une densité de 2700 kg/m³</span>
                    <span>Source : Fiche technique Art de la Pierre</span>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const champCm = document.getElementById('calc_cm');
            const champM2 = document.getElementById('calc_m2');
            const affichage = document.getElementById('calc_resultat');

            function faireLeCalcul() {
                const epaisseur = parseFloat(champCm.value) || 0;
                const surface = parseFloat(champM2.value) || 0;

                // Formule : 27kg par cm d'épaisseur pour 1m2
                const total = 27 * epaisseur * surface;

                affichage.innerText = total.toLocaleString('fr-FR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            champCm.oninput = faireLeCalcul;
            champM2.oninput = faireLeCalcul;
        });
    </script>
</x-app-layout>
