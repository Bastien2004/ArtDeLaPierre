<x-app-layout>
    <link rel="icon" href="{{ asset('LogoHead.png') }}" type="image/png">
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/registreEmail.css') }}" rel="stylesheet">

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800" style="font-family: 'Cinzel', serif; letter-spacing: 1px;">
                    📧 Registre des Adresses Mail
                </h2>
                <p class="text-sm text-gray-500 mt-1">Toutes les adresses email enregistrées lors des devis</p>
            </div>
            <a href="{{ url('/dashboard') }}" class="btn-back-stone">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="alert-mail-success">
                    <i class="fas fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="mail-card">
                <div class="mail-card-top-bar"></div>

                <div class="mail-card-header">
                    <div class="mail-card-header-left">
                        <div class="mail-header-icon">📧</div>
                        <div>
                            <p class="mail-card-title">Carnet d'adresses</p>
                            <div class="flex items-center gap-2">
                                <span class="badge-count">{{ $emails->count() }} adresse(s)</span>
                            </div>
                        </div>
                    </div>
                    <button onclick="openModal('add')" class="btn-add-mail">
                        <i class="fas fa-plus"></i> Nouveau contact
                    </button>
                </div>

                <div class="mail-card-body">
                    @if($emails->isEmpty())
                        <div class="empty-state">
                            <div class="empty-state-icon">📭</div>
                            <p>Aucune adresse email enregistrée pour le moment.</p>
                        </div>
                    @else
                        <table id="emailsTable" class="w-100">
                            <thead>
                            <tr>
                                <th>Adresse email</th>
                                <th style="width: 200px; text-align: center;">Enregistré le</th>
                                <th style="width: 100px; text-align: center;">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($emails as $email)
                                <tr>
                                    <td>
                                        <div class="email-cell">
                                            <div class="email-avatar">
                                                <i class="fas fa-envelope"></i>
                                            </div>
                                            <span class="email-adresse">{{ $email->adresse }}</span>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="date-badge">
                                            {{ $email->created_at->format('d/m/Y à H:i') }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="flex justify-center gap-2">
                                            <button onclick="openModal('edit', {{ $email->id }}, '{{ addslashes($email->adresse) }}')" class="btn-edit-mail" title="Modifier">
                                                <i class="fas fa-pen-to-square"></i>
                                            </button>

                                            <form action="{{ route('emails.destroy', $email->id) }}" method="POST"
                                                  onsubmit="return confirm('Supprimer {{ addslashes($email->adresse) }} ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-delete-mail" title="Supprimer">
                                                    <i class="fas fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="emailModal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle" class="modal-title">Ajouter un email</h3>
                <button onclick="closeModal()" class="modal-close">&times;</button>
            </div>
            <form id="emailForm" method="POST">
                @csrf
                <div id="methodContainer"></div>
                <div class="modal-body">
                    <div class="input-group">
                        <label for="modalInput">Adresse Email</label>
                        <input type="email" name="adresse" id="modalInput" required placeholder="exemple@domaine.com">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn-modal-cancel">Annuler</button>
                    <button type="submit" class="btn-modal-save">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#emailsTable').DataTable({
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
                order: [[1, 'desc']],
                columnDefs: [{ orderable: false, targets: 2 }],
                pageLength: 15,
            });
        });

        function openModal(mode, id = null, email = '') {
            const modal = $('#emailModal');
            const form = $('#emailForm');
            const title = $('#modalTitle');
            const input = $('#modalInput');
            const methodContainer = $('#methodContainer');

            if (mode === 'edit') {
                title.text('Modifier l\'adresse email');
                form.attr('action', `/emails/${id}`);
                methodContainer.html('@method("PUT")');
                input.val(email);
            } else {
                title.text('Ajouter un nouveau contact');
                form.attr('action', "{{ route('emails.storeManuel') }}");
                methodContainer.html('');
                input.val('');
            }
            modal.removeClass('hidden').addClass('flex');
        }

        function closeModal() {
            $('#emailModal').addClass('hidden').removeClass('flex');
        }

        // Fermer au clic extérieur
        $(window).on('click', function(e) {
            if ($(e.target).hasClass('modal-overlay')) closeModal();
        });
    </script>
</x-app-layout>
