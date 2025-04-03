<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <!-- Bouton pour reduire/toogle la sidebar sur petit écran -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    <!-- Barre de recherche optionnel a ameliorer avec requetes precises -->
    <form id="navbarSearchForm" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="input-group">
            <input id="navbarSearchInput" type="text"
                   class="form-control bg-light border-0 small"
                   placeholder="Rechercher..."
                   aria-label="Search"
                   aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button id="navbarSearchButton" class="btn btn-primary" type="button">
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

<!-- Script pour faire fonctionner la barre de recherche -->
<script>
  // Quand la page est charge, on attache des evenements a la barre de recherche
  document.addEventListener("DOMContentLoaded", function() {
      // On recupere le bouton et le champ de recherche
      const searchButton = document.getElementById("navbarSearchButton");
      const searchInput = document.getElementById("navbarSearchInput");
      
      // Quand on clique sur le bouton, on recupere le terme de recherche
      searchButton.addEventListener("click", function() {
          const query = searchInput.value.trim();
          if (query !== "") {
              // On redirige vers la page search.php avec le terme en parametre
              window.location.href = "search.php?query=" + encodeURIComponent(query);
          }
      });
      
      // Optionnel : Si l'utilisateur appuie sur "Enter" dans le champ, on simule le clique sur le bouton
      searchInput.addEventListener("keydown", function(e) {
          if (e.key === "Enter") {
              e.preventDefault();
              searchButton.click();
          }
      });
  });
</script>
