<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <!-- Bouton pour reduire/toogle la sidebar sur petit écran -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    <!-- Barre de recherche optionnel à amélior avec requetes précises -->
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="input-group">
            <input type="text"
                   class="form-control bg-light border-0 small"
                   placeholder="Rechercher..."
                   aria-label="Search"
                   aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>
    <!-- Message pour afficher le nom du Users si on maintient l'authentification Users si non simple Message de bienvenu -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <span class="nav-link">
                Bienvenu(e) Admin
            </span>
        </li>
    </ul>
</nav>
