<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content modal-content-stone">

            <div class="modal-header modal-header-stone">
                <h5 class="modal-title modal-title-stone">Confirmation de session</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0">
                <div class="stone-action-card text-center">
                    <div class="mb-4">
                        <span style="font-size: 3rem; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">🚪</span>
                    </div>

                    <h4 class="mb-2" style="font-family: 'Cinzel', serif; color: var(--stone-dark); font-size: 1.1rem;">Prêt à partir ?</h4>
                    <p class="mb-4 text-muted" style="font-size: 0.9rem;">
                        Souhaitez-vous vraiment quitter <br>
                        <strong style="color: var(--stone-gold);">ART DE LA PIERRE</strong> ?
                    </p>

                    <div class="btn-group-stone-horizontal">
                        <button type="button" class="btn-stone-secondary" data-bs-dismiss="modal">
                            Annuler
                        </button>

                        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 flex-grow-1">
                            @csrf
                            <button type="submit" class="btn-stone-primary w-100">
                                Quitter
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
