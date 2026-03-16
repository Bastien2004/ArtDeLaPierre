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

                {{-- En-tête --}}
                <div class="mail-card-header">
                    <div class="mail-card-header-left">
                        <div class="mail-header-icon">📧</div>
                        <div>
                            <p class="mail-card-title">Carnet d'adresses</p>
                            <p class="mail-card-subtitle">Enregistrées automatiquement à la création des devis</p>
                        </div>
                    </div>
                    <span class="badge-count">{{ $emails->count() }} adresse(s)</span>
                </div>

                {{-- Corps --}}
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
                                <th style="width: 80px; text-align: center;">Action</th>
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
                                        <form action="{{ route('emails.destroy', $email->id) }}" method="POST"
                                              onsubmit="return confirm('Supprimer {{ addslashes($email->adresse) }} ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete-mail" title="Supprimer">
                                                <i class="fas fa-trash-can"></i>
                                            </button>
                                        </form>
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

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#emailsTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                },
                order: [[1, 'desc']],
                columnDefs: [
                    { orderable: false, targets: 2 }
                ],
                pageLength: 15,
            });
        });
    </script>

</x-app-layout>
