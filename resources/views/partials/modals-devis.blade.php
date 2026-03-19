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

{{-- ═══ MODAL CARNET EMAILS ═══ --}}
<div id="modal-emails-overlay" style="
    display: none; position: fixed; inset: 0;
    background: rgba(14,20,28,0.6); backdrop-filter: blur(6px);
    z-index: 9999; align-items: center; justify-content: center;">

    <div style="
        background: white; border-radius: 20px; width: 480px; max-width: 90vw;
        max-height: 80vh; display: flex; flex-direction: column;
        box-shadow: 0 25px 60px rgba(0,0,0,0.2);
        border-top: 5px solid var(--stone-dark); overflow: hidden;">

        {{-- Header --}}
        <div style="padding: 24px 28px 16px; border-bottom: 1px solid #f0ece6; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-family: 'Cinzel', serif; font-size: 0.6rem; letter-spacing: 3px; color: var(--stone-accent); text-transform: uppercase; margin-bottom: 4px;">Art de la Pierre</div>
                <h3 style="margin: 0; font-family: 'Cinzel', serif; font-size: 1.1rem; color: var(--stone-dark);">Carnet d'adresses</h3>
            </div>
            <button onclick="fermerModalEmails()" style="
                width: 32px; height: 32px; border-radius: 50%; border: 1.5px solid #e0ece6;
                background: #f8fafb; cursor: pointer; font-size: 0.85rem; color: #64748b;
                display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
                    onmouseover="this.style.background='#ef4444';this.style.color='white'"
                    onmouseout="this.style.background='#f8fafb';this.style.color='#64748b'">
                <i class="fa fa-times"></i>
            </button>
        </div>

        {{-- Barre de recherche --}}
        <div style="padding: 16px 28px; border-bottom: 1px solid #f0ece6;">
            <div style="position: relative;">
                <i class="fa fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #95a5a6; font-size: 0.85rem;"></i>
                <input type="text" id="modal-email-search"
                       placeholder="Rechercher une adresse..."
                       oninput="filtrerEmails(this.value)"
                       style="width: 100%; padding: 10px 12px 10px 36px; border: 1.5px solid #e1e8ed;
                              border-radius: 10px; font-size: 0.9rem; box-sizing: border-box;
                              font-family: 'Inter', sans-serif; outline: none; transition: all 0.2s;"
                       onfocus="this.style.borderColor='var(--stone-dark)'"
                       onblur="this.style.borderColor='#e1e8ed'">
            </div>
        </div>

        {{-- Liste des emails --}}
        <div id="modal-emails-list" style="overflow-y: auto; padding: 12px 16px; flex: 1;">
            @forelse($emailsCarnet ?? [] as $mail)
                <div class="email-carnet-item" data-email="{{ $mail->adresse }}"
                     onclick="selectionnerEmail('{{ $mail->adresse }}')">
                    <div class="email-carnet-avatar">
                        <i class="fa fa-envelope"></i>
                    </div>
                    <span class="email-carnet-adresse">{{ $mail->adresse }}</span>
                    <i class="fa fa-chevron-right email-carnet-chevron"></i>
                </div>
            @empty
                <div style="text-align: center; padding: 40px 20px; color: #95a5a6;">
                    <i class="fa fa-inbox" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                    Aucune adresse enregistrée
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div style="padding: 14px 28px; border-top: 1px solid #f0ece6; text-align: center;">
            <span style="font-size: 0.75rem; color: #95a5a6;">Cliquez sur une adresse pour la sélectionner</span>
        </div>
    </div>
</div>
