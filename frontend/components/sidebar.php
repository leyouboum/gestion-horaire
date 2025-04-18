<?php
require_once __DIR__ . '/../config.php';
?>
<!-- SIDEBAR -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" 
       href="<?= $baseUrlBackend ?>Admin/dashboard.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Gestion Horaires</div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="<?= $baseUrlBackend ?>Admin/dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Tableau de bord</span>
        </a>
    </li>
    <!-- Nav Item - Universités -->
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Gestion Globale</div>
    <li class="nav-item">
        <a class="nav-link" href="<?= $baseUrlBackend ?>Admin/universite.php">
            <i class="fas fa-fw fa-school"></i>
            <span>Universités</span>
        </a>
    </li>
    <!-- Nav Item - Années Académiques -->
    <li class="nav-item">
        <a class="nav-link" href="<?= $baseUrlBackend ?>Admin/annees.php">
            <i class="fas fa-fw fa-calendar-alt"></i>
            <span>Années Académiques</span>
        </a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Gestion Locale</div>
    <!-- Nav Item - Sites universitaires -->
    <li class="nav-item">
        <a class="nav-link" href="<?= $baseUrlBackend ?>Admin/site.php">
            <i class="fas fa-fw fa-university"></i>
            <span>Sites Universitaires</span>
        </a>
    </li>
    <!-- Nav Item - Groupes d'étudiants -->
    <li class="nav-item">
        <a class="nav-link" href="<?= $baseUrlBackend ?>Admin/groupe.php">
            <i class="fas fa-fw fa-users"></i>
            <span>Groupes</span>
        </a>
    </li>
    <!-- Nav Item - Salles de cours -->
    <li class="nav-item">
        <a class="nav-link" href="<?= $baseUrlBackend ?>Admin/salle.php">
            <i class="fas fa-fw fa-school"></i>
            <span>Salles de classe</span>
        </a>
    </li>
    <!-- Nav Item - Cours -->
    <li class="nav-item">
        <a class="nav-link" href="<?= $baseUrlBackend ?>Admin/cours.php">
            <i class="fas fa-fw fa-book"></i>
            <span>Cours</span>
        </a>
    </li>
    <!-- Nav Item - Horaires -->
    <li class="nav-item">
        <a class="nav-link" href="<?= $baseUrlBackend ?>Admin/planning.php">
            <i class="fas fa-fw fa-calendar"></i>
            <span>Planification Horaires</span>
        </a>
    </li>
    <!-- Nav Item - Matériels -->
    <li class="nav-item">
        <a class="nav-link" href="<?= $baseUrlBackend ?>Admin/materiel.php">
            <i class="fas fa-fw fa-laptop"></i>
            <span>Matériels</span>
        </a>
    </li>
    
    <!-- Nav Item - dernières activités -->
    <li class="nav-item">
        <a class="nav-link" href="<?= $baseUrlBackend ?>Admin/activite.php">
            <i class="fas fa-fw fa-history"></i>
            <span>Voir dernières activités</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <!-- Bouton "Sortir" -->
    <li class="nav-item">
        <a class="nav-link" href="../../../../frontend/index.html" onclick="handleExit()">
            <i class="fas fa-fw fa-door-open"></i>
            <span>Quitter</span>
        </a>
    </li>

    <div class="text-center d-none d-md-inline mt-2">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- END SIDEBAR -->

<script>
function handleExit() {
    // Confirmation avant de quitter
    alert("Etes-vous sûr(e) de vouloir quitter le programme ?");
}
</script>
