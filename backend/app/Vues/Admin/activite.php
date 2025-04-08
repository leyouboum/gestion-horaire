<?php
// Inclusion de la connexion à la base de données
require_once __DIR__ . '/../../../config/database.php';

// Inclusion du header (ouvre <html>, <head>, <body> et <div id="wrapper">)
include __DIR__ . '/../../../../frontend/components/header.php';

// Inclusion de la sidebar
include __DIR__ . '/../../../../frontend/components/sidebar.php';
?>
<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
  <!-- Main Content -->
  <div id="content">
    <?php 
    // Inclusion du topbar
    include __DIR__ . '/../../../../frontend/components/topbar.php'; 
    ?>

    <div class="container-fluid my-4">
      <h1 class="h3 mb-4 text-gray-800">Journal d'Activité</h1>

      <?php
      // Récupération des activités depuis la table audit_log
      try {
          $sql = "SELECT 
                    id_audit, 
                    table_name, 
                    record_id, 
                    operation, 
                    message, 
                    created_at 
                  FROM audit_log 
                  ORDER BY created_at DESC";
          $stmt = $conn->query($sql);
          $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
      } catch (Exception $e) {
          die("Erreur lors de la récupération des activités : " . $e->getMessage());
      }
      ?>

      <!-- Affichage du tableau des activités -->
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Toutes les activités</h6>
        </div>
        <div class="card-body">
          <?php if (!empty($activities)): ?>
            <div class="table-responsive">
              <table class="table table-bordered" id="tableAuditLog" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th>ID Audit</th>
                    <th>Table</th>
                    <th>Record ID</th>
                    <th>Opération</th>
                    <th>Message</th>
                    <th>Date / Heure</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($activities as $act): ?>
                    <tr>
                      <td><?= htmlspecialchars($act['id_audit']); ?></td>
                      <td><?= htmlspecialchars($act['table_name']); ?></td>
                      <td><?= htmlspecialchars($act['record_id']); ?></td>
                      <td><?= htmlspecialchars($act['operation']); ?></td>
                      <td><?= htmlspecialchars($act['message']); ?></td>
                      <td><?= date("d/m/Y H:i:s", strtotime($act['created_at'])); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p>Aucune activité trouvée dans le journal.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div> <!-- Fin #content -->
</div> <!-- Fin #content-wrapper -->

<?php 
// Inclusion du footer
include __DIR__ . '/../../../../frontend/components/footer.php'; 
?>

<!-- Initialisation de DataTables -->
<script>
$(document).ready(function() {
    $('#tableAuditLog').DataTable({
        responsive: true,
        pageLength: 10,
        language: {
            url: '../../../../frontend/assets/vendor/datatables/French.json'
        },
        order: [[0, 'desc']]
    });
});
</script>
