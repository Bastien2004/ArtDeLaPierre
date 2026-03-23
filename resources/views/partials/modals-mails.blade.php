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
