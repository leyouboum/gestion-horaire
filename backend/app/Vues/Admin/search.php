<?php
// page de recherche à partir de la barre de recherche du topbar
// Récupère le terme de recherche depuis l'URL (ex: search.php?query=bruxelles)
// Si y'en a pas, on met une chaîne vide
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

// On Inclus la connexion à la DB et la variable $conn
require_once __DIR__ . '/../../../../backend/config/database.php';
use app\Config\Database;
$conn = Database::getConnection();

// On fait les requêtes de recherche dans les différentes tables de notre bdd

// Recherche dans la table universite
$stmtUni = $conn->prepare("SELECT * FROM universite WHERE nom LIKE :query");
$stmtUni->execute(['query' => "%$query%"]);
$universites = $stmtUni->fetchAll(PDO::FETCH_ASSOC);

// Recherche dans la table cours
$stmtCours = $conn->prepare("SELECT * FROM cours WHERE nom_cours LIKE :query");
$stmtCours->execute(['query' => "%$query%"]);
$cours = $stmtCours->fetchAll(PDO::FETCH_ASSOC);

// Recherche dans la table groupe
$stmtGroupe = $conn->prepare("SELECT * FROM groupe WHERE nom_groupe LIKE :query");
$stmtGroupe->execute(['query' => "%$query%"]);
$groupes = $stmtGroupe->fetchAll(PDO::FETCH_ASSOC);

// Recherche dans la table site
$stmtSite = $conn->prepare("SELECT * FROM site WHERE nom LIKE :query");
$stmtSite->execute(['query' => "%$query%"]);
$sites = $stmtSite->fetchAll(PDO::FETCH_ASSOC);

// Recherche dans la table salle (salles de classe)
$stmtSalle = $conn->prepare("SELECT * FROM salle WHERE nom_salle LIKE :query");
$stmtSalle->execute(['query' => "%$query%"]);
$salles = $stmtSalle->fetchAll(PDO::FETCH_ASSOC);

// Recherche dans la table planning (horaire)
// On cherche ici dans l'année académique et aussi dans les dates de début/fin
$stmtPlanning = $conn->prepare("SELECT * FROM planning WHERE annee_academique LIKE :query OR date_heure_debut LIKE :query OR date_heure_fin LIKE :query");
$stmtPlanning->execute(['query' => "%$query%"]);
$plannings = $stmtPlanning->fetchAll(PDO::FETCH_ASSOC);

// Recherche dans la table materiel
$stmtMateriel = $conn->prepare("SELECT * FROM materiel WHERE type_materiel LIKE :query");
$stmtMateriel->execute(['query' => "%$query%"]);
$materiels = $stmtMateriel->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Inclusion du header (ouvre <html>, <head>, <body>, <div id="wrapper">) -->
<?php include __DIR__ . '/../../../../frontend/components/header.php'; ?>
<!-- Inclusion de la sidebar -->
<?php include __DIR__ . '/../../../../frontend/components/sidebar.php'; ?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
  <!-- Main Content -->
  <div id="content">
    <!-- Inclusion du topbar -->
    <?php include __DIR__ . '/../../../../frontend/components/topbar.php'; ?>

    <!-- Contenu principal -->
    <div class="container-fluid my-4">
      <!-- Titre de la page avec le terme recherché -->
      <h1 class="h3 mb-4 text-gray-800">Résultats de recherche pour "<?php echo htmlspecialchars($query); ?>"</h1>
      
      <!-- Carte pour les Universités -->
      <div class="card mb-4 shadow-sm">
        <div class="card-header">
          <h6 class="m-0 font-weight-bold text-primary">Universités</h6>
        </div>
        <div class="card-body">
          <?php if (count($universites) > 0): ?>
            <ul class="list-group">
              <?php foreach ($universites as $uni): ?>
                <li class="list-group-item"><?php echo htmlspecialchars($uni['nom']); ?></li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p>Aucune université trouvée.</p>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Carte pour les Cours -->
      <div class="card mb-4 shadow-sm">
        <div class="card-header">
          <h6 class="m-0 font-weight-bold text-primary">Cours</h6>
        </div>
        <div class="card-body">
          <?php if (count($cours) > 0): ?>
            <ul class="list-group">
              <?php foreach ($cours as $c): ?>
                <li class="list-group-item"><?php echo htmlspecialchars($c['nom_cours']); ?></li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p>Aucun cours trouvé.</p>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Carte pour les Groupes -->
      <div class="card mb-4 shadow-sm">
        <div class="card-header">
          <h6 class="m-0 font-weight-bold text-primary">Groupes</h6>
        </div>
        <div class="card-body">
          <?php if (count($groupes) > 0): ?>
            <ul class="list-group">
              <?php foreach ($groupes as $g): ?>
                <li class="list-group-item"><?php echo htmlspecialchars($g['nom_groupe']); ?></li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p>Aucun groupe trouvé.</p>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Carte pour les Sites -->
      <div class="card mb-4 shadow-sm">
        <div class="card-header">
          <h6 class="m-0 font-weight-bold text-primary">Sites</h6>
        </div>
        <div class="card-body">
          <?php if (count($sites) > 0): ?>
            <ul class="list-group">
              <?php foreach ($sites as $s): ?>
                <li class="list-group-item"><?php echo htmlspecialchars($s['nom']); ?></li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p>Aucun site trouvé.</p>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Carte pour les Salles de classe -->
      <div class="card mb-4 shadow-sm">
        <div class="card-header">
          <h6 class="m-0 font-weight-bold text-primary">Salles de classe</h6>
        </div>
        <div class="card-body">
          <?php if (count($salles) > 0): ?>
            <ul class="list-group">
              <?php foreach ($salles as $sal): ?>
                <li class="list-group-item"><?php echo htmlspecialchars($sal['nom_salle']); ?></li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p>Aucune salle trouvée.</p>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Carte pour les Horaires (Planning) -->
      <div class="card mb-4 shadow-sm">
        <div class="card-header">
          <h6 class="m-0 font-weight-bold text-primary">Horaires</h6>
        </div>
        <div class="card-body">
          <?php if (count($plannings) > 0): ?>
            <ul class="list-group">
              <?php foreach ($plannings as $p): ?>
                <li class="list-group-item">
                  <?php 
                    echo "Début: " . htmlspecialchars($p['date_heure_debut']) . " - Fin: " . htmlspecialchars($p['date_heure_fin']);
                    echo " (Année: " . htmlspecialchars($p['annee_academique']) . ")";
                  ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p>Aucun horaire trouvé.</p>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Carte pour les Matériels -->
      <div class="card mb-4 shadow-sm">
        <div class="card-header">
          <h6 class="m-0 font-weight-bold text-primary">Matériels</h6>
        </div>
        <div class="card-body">
          <?php if (count($materiels) > 0): ?>
            <ul class="list-group">
              <?php foreach ($materiels as $mat): ?>
                <li class="list-group-item"><?php echo htmlspecialchars($mat['type_materiel']); ?></li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p>Aucun matériel trouvé.</p>
          <?php endif; ?>
        </div>
      </div>
      
    </div> <!-- Fin du container-fluid my-4 -->
  </div> <!-- Fin #content -->
</div> <!-- Fin #content-wrapper -->

<!-- Inclusion du footer -->
<?php include __DIR__ . '/../../../../frontend/components/footer.php'; ?>
