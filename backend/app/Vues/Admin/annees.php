<?php
/**
 * annees.php
 * Gestion des Années Académiques (CRUD)
 */

// Inclusion de la connexion à la base de données si nécessaire (si utilisée pour d'autres opérations côté PHP)
require_once __DIR__ . '/../../../config/database.php';

// Inclusion du header, sidebar et topbar
include __DIR__ . '/../../../../frontend/components/header.php';
include __DIR__ . '/../../../../frontend/components/sidebar.php';
?>
<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
  <!-- Main Content -->
  <div id="content">
    <?php 
      include __DIR__ . '/../../../../frontend/components/topbar.php'; 
    ?>

    <div class="container-fluid my-4">
      <h1 class="h3 mb-4 text-gray-800">Gestion des Années Académiques</h1>

      <!-- Formulaire d'ajout / modification -->
      <div class="card mb-4 shadow-sm">
        <div class="card-header">
          <h6 id="formTitle" class="m-0 font-weight-bold text-primary">Ajouter une Année Académique</h6>
        </div>
        <div class="card-body">
          <form id="anneeForm" onsubmit="return false;">
            <input type="hidden" id="id_annee_form" />

            <div class="row g-3">
              <!-- Libellé de l'année -->
              <div class="col-12 col-md-4">
                <label for="libelle" class="form-label">Libellé</label>
                <input type="text" class="form-control" id="libelle" placeholder="Ex: 2024-2025" required />
              </div>
              <!-- Date de début -->
              <div class="col-12 col-md-4">
                <label for="date_debut" class="form-label">Date de Début</label>
                <input type="date" class="form-control" id="date_debut" required />
              </div>
              <!-- Date de fin -->
              <div class="col-12 col-md-4">
                <label for="date_fin" class="form-label">Date de Fin</label>
                <input type="date" class="form-control" id="date_fin" required />
              </div>
            </div>

            <div class="mt-3 d-flex flex-wrap gap-2">
              <button type="submit" class="btn btn-primary" id="btnSubmitAnnee">Enregistrer</button>
              <button type="button" class="btn btn-secondary" id="btnCancelAnnee" style="display: none;">Annuler</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Tableau listant les années académiques -->
      <div class="card shadow mb-4">
        <div class="card-header">
          <h6 class="m-0 font-weight-bold text-primary">Liste des Années Académiques</h6>
        </div>
        <div class="card-body">
          <div id="alertMsgAnnee" class="alert" style="display: none;"></div>
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTableAnnee" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Libellé</th>
                  <th>Date de Début</th>
                  <th>Date de Fin</th>
                  <th style="min-width: 150px;">Actions</th>
                </tr>
              </thead>
              <tbody id="anneeBody"></tbody>
            </table>
          </div>
        </div>
      </div>

    </div><!-- Fin .container-fluid -->
  </div><!-- Fin #content -->
</div><!-- Fin #content-wrapper -->

<?php 
include __DIR__ . '/../../../../frontend/components/footer.php'; 
?>

<script>
// Mapping pour stocker les années académiques
let anneesMap = {};

// Chargement lors de la fin du DOM
document.addEventListener('DOMContentLoaded', () => {
  loadAnnees();
  document.getElementById('anneeForm').addEventListener('submit', handleAnneeFormSubmit);
  document.getElementById('btnCancelAnnee').addEventListener('click', resetAnneeForm);
  
  setTimeout(() => {
    $('#dataTableAnnee').DataTable({
      responsive: true,
      pageLength: 10,
      language: { url: '../../../../frontend/assets/vendor/datatables/French.json' },
      order: [[0, 'desc']]
    });
  }, 500);
});

// Fonction de chargement des années académiques via l'API
async function loadAnnees() {
  try {
    const response = await fetch('../../../routes/admin-api.php?entity=annees&action=list');
    const data = await response.json();
    console.log("Années reçues:", data); // Pour débogage
    const tbody = document.getElementById('anneeBody');

    // Réinitialisation du tableau
    tbody.innerHTML = '';

    // Récupération de la date actuelle
    const now = new Date();

    data.forEach(annee => {
      // Convertir la date de fin en objet Date
      const dateFin = new Date(annee.date_fin);
      // Désactiver le bouton "Modifier" si l'année est terminée (date de fin antérieure à aujourd'hui)
      const disableEdit = dateFin < now;

      // Stocker la valeur dans anneesMap (si nécessaire pour d'autres usages)
      anneesMap[annee.id_annee] = annee.libelle;

      // Création de la ligne du tableau
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${annee.id_annee}</td>
        <td>${sanitize(annee.libelle)}</td>
        <td>${annee.date_debut}</td>
        <td>${annee.date_fin}</td>
        <td>
          <button class="btn btn-warning btn-sm me-1"
            onclick="editAnnee(${annee.id_annee}, '${sanitize(annee.libelle)}', '${annee.date_debut}', '${annee.date_fin}')"
            ${disableEdit ? 'disabled' : ''}>
            <i class="fas fa-edit"></i> Modifier
          </button>
          <button class="btn btn-danger btn-sm" onclick="deleteAnnee(${annee.id_annee})">
            <i class="fas fa-trash"></i> Supprimer
          </button>
        </td>
      `;
      tbody.appendChild(row);
    });
  } catch (err) {
    console.error("Erreur chargement années académiques:", err);
  }
}



// Gestion de la soumission du formulaire (Création/Modification)
function handleAnneeFormSubmit() {
  const id_annee = document.getElementById('id_annee_form').value;
  const libelle = document.getElementById('libelle').value.trim();
  const dateDebut = document.getElementById('date_debut').value;
  const dateFin   = document.getElementById('date_fin').value;

  if (!libelle || !dateDebut || !dateFin) {
    showAnneeMessage("Tous les champs sont requis.", "danger");
    return;
  }
  if (new Date(dateDebut) >= new Date(dateFin)) {
    showAnneeMessage("La date de début doit être antérieure à la date de fin.", "danger");
    return;
  }

  const payload = {
    libelle,
    date_debut: dateDebut,
    date_fin: dateFin
  };

  if (id_annee) {
    // Mise à jour
    fetch(`../../../routes/admin-api.php?entity=annees&action=update&id=${id_annee}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    })
    .then(resp => resp.json())
    .then(data => {
      if (data.message) {
        showAnneeMessage(data.message, "success");
        resetAnneeForm();
        loadAnnees();
      } else {
        showAnneeMessage(data.error || "Erreur lors de la mise à jour.", "danger");
      }
    })
    .catch(err => {
      console.error(err);
      showAnneeMessage("Erreur lors de la requête PUT.", "danger");
    });
  } else {
    // Création
    fetch("../../../routes/admin-api.php?entity=annees&action=create", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    })
    .then(resp => resp.json())
    .then(data => {
      if (data.message) {
        showAnneeMessage(data.message, "success");
        resetAnneeForm();
        loadAnnees();
      } else {
        showAnneeMessage(data.error || "Erreur lors de la création.", "danger");
      }
    })
    .catch(err => {
      console.error(err);
      showAnneeMessage("Erreur lors de la requête POST.", "danger");
    });
  }
}

// Prépare le formulaire pour modifier une année académique
function editAnnee(id, libelle, dateDebut, dateFin) {
  document.getElementById('id_annee_form').value = id;
  document.getElementById('libelle').value = libelle;
  document.getElementById('date_debut').value = dateDebut;
  document.getElementById('date_fin').value = dateFin;
  document.getElementById('btnSubmitAnnee').textContent = "Enregistrer";
  document.getElementById('btnCancelAnnee').style.display = "inline-block";
  document.getElementById('formTitle').textContent = "Modifier l'Année Académique";
}

// Supprime une année académique
function deleteAnnee(id) {
  if (!confirm("Voulez-vous supprimer cette année académique ?")) return;
  fetch(`../../../routes/admin-api.php?entity=annees&action=delete&id=${id}`, {
    method: "DELETE"
  })
  .then(resp => resp.json())
  .then(data => {
    if (data.message) {
      showAnneeMessage(data.message, "success");
      loadAnnees();
    } else {
      showAnneeMessage(data.error || "Erreur lors de la suppression.", "danger");
    }
  })
  .catch(err => {
    console.error(err);
    showAnneeMessage("Erreur lors de la requête DELETE.", "danger");
  });
}

// Réinitialise le formulaire
function resetAnneeForm() {
  document.getElementById('id_annee_form').value = "";
  document.getElementById('libelle').value = "";
  document.getElementById('date_debut').value = "";
  document.getElementById('date_fin').value = "";
  document.getElementById('btnSubmitAnnee').textContent = "Enregistrer";
  document.getElementById('btnCancelAnnee').style.display = "none";
  document.getElementById('formTitle').textContent = "Ajouter une Année Académique";
}

// Affiche un message d'alerte
function showAnneeMessage(msg, type) {
  const alertMsg = document.getElementById('alertMsgAnnee');
  alertMsg.textContent = msg;
  alertMsg.className = `alert alert-${type}`;
  alertMsg.style.display = "block";
  setTimeout(() => {
    alertMsg.style.display = "none";
  }, 3000);
}

// Fonction de sanitization pour éviter toute injection HTML
function sanitize(str) {
  if (typeof str !== "string") return str;
  return str.replace(/[&<>"'`]/g, m => {
    const map = {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#39;",
      "`": "&#96;"
    };
    return map[m];
  });
}
</script>
