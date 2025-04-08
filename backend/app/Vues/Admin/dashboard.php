<?php
/**
 * dashboard.php
 * Tableau de bord Admin
 */

// 1) Inclusion du header (ouvre <html>, <head>, <body> et <div id="wrapper">)
include __DIR__ . '/../../../../frontend/components/header.php';

// 2) Inclusion de la sidebar (menu latéral)
include __DIR__ . '/../../../../frontend/components/sidebar.php';
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
  <!-- Main Content -->
  <div id="content">

    <?php
    // 3) Inclusion du topbar (barre du haut)
    include __DIR__ . '/../../../../frontend/components/topbar.php';

    // 4) Récupération des statistiques
    try {
        // Récupération des compteurs
        $totalUniversites = $conn->query("SELECT COUNT(*) FROM universite")->fetchColumn();
        $totalSites       = $conn->query("SELECT COUNT(*) FROM site")->fetchColumn();
        $totalGroupes     = $conn->query("SELECT COUNT(*) FROM groupe")->fetchColumn();
        $totalCours       = $conn->query("SELECT COUNT(*) FROM cours")->fetchColumn();
        $totalSalles      = $conn->query("SELECT COUNT(*) FROM salle")->fetchColumn();
        $totalMateriels   = $conn->query("SELECT COUNT(*) FROM materiel")->fetchColumn();

        // Horaires générés = total de lignes dans la table planning
        $horairesGeneres  = $conn->query("SELECT COUNT(*) FROM planning")->fetchColumn();

        // Nombre d'opérations = total de lignes dans la table audit_log
        $totalOperations  = $conn->query("SELECT COUNT(*) FROM audit_log")->fetchColumn();

        // Bar Chart : Nombre de sites par université
        $stmtSites = $conn->query("
            SELECT u.nom AS universite, COUNT(s.id_site) AS nb_sites
            FROM universite u
            LEFT JOIN site s ON u.id_universite = s.id_universite
            GROUP BY u.id_universite
            ORDER BY u.id_universite
        ");
        $statsSites = $stmtSites->fetchAll(PDO::FETCH_ASSOC);

        $labelsSites = [];
        $dataSites   = [];
        foreach ($statsSites as $row) {
            $labelsSites[] = $row['universite'];
            $dataSites[]   = (int)$row['nb_sites'];
        }

        // Pie Chart : Nombre de groupes par université
        $stmtGroupes = $conn->query("
            SELECT u.nom AS universite, COUNT(g.id_groupe) AS nb_groupes
            FROM universite u
            LEFT JOIN site s ON u.id_universite = s.id_universite
            LEFT JOIN groupe_site gs ON s.id_site = gs.id_site AND gs.is_principal = 1
            LEFT JOIN groupe g ON gs.id_groupe = g.id_groupe
            GROUP BY u.id_universite
            ORDER BY u.id_universite
        ");
        $statsGroupesUniv = $stmtGroupes->fetchAll(PDO::FETCH_ASSOC);

        $labelsGroupes = [];
        $dataGroupes   = [];
        foreach ($statsGroupesUniv as $row) {
            $labelsGroupes[] = $row['universite'];
            $dataGroupes[]   = (int)$row['nb_groupes'];
        }

        // Passage des données en JSON pour Chart.js
        $labelsSitesJS   = json_encode($labelsSites);
        $dataSitesJS     = json_encode($dataSites);
        $labelsGroupesJS = json_encode($labelsGroupes);
        $dataGroupesJS   = json_encode($dataGroupes);

        // Dernières activités : audit_log
        $stmtActivities = $conn->query("
            SELECT message, created_at AS date
            FROM audit_log
            ORDER BY created_at DESC
            LIMIT 10
        ");
        $lastActivities = $stmtActivities->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        die("Erreur lors de la récupération des statistiques : " . $e->getMessage());
    }
    ?>

    <!-- Container principal (SB Admin 2) -->
    <div class="container-fluid">
      <!-- Titre + action éventuelle -->
      <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h3 mb-0 text-gray-800">Tableau de bord</h1>
          <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
              <i class="fas fa-download fa-sm text-white-50"></i> Générer un rapport
          </a>
      </div>

      <!-- Statistiques (ligne 1) -->
      <div class="row">
          <!-- Total Universités -->
          <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                          Total Universités
                      </div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">
                          <?= $totalUniversites; ?>
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-university fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
          </div>
          <!-- Total Sites -->
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Total Sites
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?= $totalSites; ?>
                    </div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Total Groupes -->
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                        Total Groupes
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?= $totalGroupes; ?>
                    </div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-users fa-2x text-gray-300"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Total Cours -->
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        Total Cours
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?= $totalCours; ?>
                    </div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-book fa-2x text-gray-300"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>

      <!-- Statistiques (ligne 2) -->
      <div class="row">
          <!-- Total Salles -->
          <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                          Total Salles
                      </div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">
                          <?= $totalSalles; ?>
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-chalkboard fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
          </div>
          <!-- Horaires générés -->
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Horaires générés
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?= $horairesGeneres; ?>
                    </div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Total Matériels -->
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                        Total Matériels
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?= $totalMateriels; ?>
                    </div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-laptop fa-2x text-gray-300"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Nombre d'opérations (audit_log) -->
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                        Nombre d'opérations
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?= $totalOperations; ?>
                    </div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-tasks fa-2x text-gray-300"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>

      <!-- Graphiques -->
      <div class="row">
          <!-- Bar Chart : Sites/Université -->
          <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Répartition des Sites par Université</h6>
              </div>
              <div class="card-body">
                  <div class="chart-bar">
                      <canvas id="myBarChart"></canvas>
                  </div>
                  <hr>
                  Nombre total de sites : <?= $totalSites; ?>
              </div>
            </div>
          </div>
          <!-- Pie Chart : Groupes/Université -->
          <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Groupes par Université</h6>
              </div>
              <div class="card-body">
                <div class="chart-pie pt-4">
                    <canvas id="myPieChart"></canvas>
                </div>
                <hr>
                Nombre total de groupes : <?= $totalGroupes; ?>
              </div>
            </div>
          </div>
      </div>

      <!-- Dernières activités -->
      <div class="row">
        <div class="col-12">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">10 Dernières Activités</h6>
            </div>
            <div class="card-body">
              <?php if (!empty($lastActivities)): ?>
                <ul class="list-group">
                  <?php foreach ($lastActivities as $activity): ?>
                    <li class="list-group-item">
                        <strong>
                          <?php echo date("d/m/Y H:i", strtotime($activity['date'])); ?> :
                        </strong>
                        <?php echo htmlspecialchars($activity['message']); ?>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php else: ?>
                <p>Aucune activité récente.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Fin .container-fluid -->

  </div>
  <!-- Fin #content -->
</div>
<!-- Fin #content-wrapper -->

<?php
// 5) Inclusion du footer (ferme <body> et <html>)
include __DIR__ . '/../../../../frontend/components/footer.php';
?>

<script>
// Bar Chart
var ctxBar = document.getElementById('myBarChart').getContext('2d');
var myBarChart = new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: <?= $labelsSitesJS; ?>,
        datasets: [{
            label: "Sites",
            backgroundColor: "rgba(78, 115, 223, 0.7)",
            hoverBackgroundColor: "rgba(78, 115, 223, 1)",
            borderColor: "#4e73df",
            data: <?= $dataSitesJS; ?>
        }]
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            x: {
                grid: { display: false },
                title: { display: true, text: "Universités" }
            },
            y: {
                beginAtZero: true,
                title: { display: true, text: "Nombre de sites" }
            }
        },
        plugins: {
            legend: { display: false }
        }
    }
});

// Pie Chart
var ctxPie = document.getElementById('myPieChart').getContext('2d');
var myPieChart = new Chart(ctxPie, {
    type: 'pie',
    data: {
        labels: <?= $labelsGroupesJS; ?>,
        datasets: [{
            data: <?= $dataGroupesJS; ?>,
            backgroundColor: [
                '#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b',
                '#858796','#5a5c69','#c0c0c0','#8e44ad','#2ecc71'
            ],
            hoverBackgroundColor: [
                '#2e59d9','#17a673','#2c9faf','#dda20a','#be2617',
                '#6c757d','#3b3d4d','#a0a0a0','#7d3c98','#27ae60'
            ],
            hoverBorderColor: "rgba(234, 236, 244, 1)"
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>
