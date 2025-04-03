<?php
/**
 * cours.php
 * Gestion des Cours (CRUD) sans sessions ni triggers, avec SB Admin 2.
 */

// Inclusion du header (qui ouvre <html>, <head>, <body>, <div id="wrapper">)
include __DIR__ . '/../../../../frontend/components/header.php';

// Inclusion de la sidebar et du topbar
include __DIR__ . '/../../../../frontend/components/sidebar.php';
?>
<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
  <!-- Main Content -->
  <div id="content">
    <?php include __DIR__ . '/../../../../frontend/components/topbar.php'; ?>

    <div class="container-fluid my-4">
      <h1 class="h3 mb-4 text-gray-800">Cours</h1>

      <!-- Formulaire d'ajout / modification -->
      <div class="card mb-4 shadow-sm">
        <div class="card-header">
          <h6 id="formTitle" class="m-0 font-weight-bold text-primary">Ajouter un Cours</h6>
        </div>
        <div class="card-body">
          <form id="coursForm" onsubmit="return false;">
            <input type="hidden" id="id_cours" />
            <div class="mb-3">
              <label for="code_cours" class="form-label">Code du Cours</label>
              <input type="text" class="form-control" id="code_cours" placeholder="Ex: MATH101" required />
            </div>
            <div class="mb-3">
              <label for="nom_cours" class="form-label">Nom du Cours</label>
              <input type="text" class="form-control" id="nom_cours" required />
            </div>
            <div class="mb-3">
              <label for="sites" class="form-label">Sites disponibles</label>
              <!-- Sélecteur multiple pour affecter le cours à plusieurs sites -->
              <select multiple class="form-control" id="sites"></select>
            </div>
            <div class="mb-3">
              <label for="details" class="form-label">Détails</label>
              <textarea class="form-control" id="details" rows="3" placeholder="Informations complémentaires"></textarea>
            </div>
            <div class="mb-3">
              <label for="duree" class="form-label">Durée (en heures)</label>
              <input type="number" class="form-control" id="duree" value="1" min="1" required />
            </div>
            <button type="submit" class="btn btn-primary" id="btnSubmit">Enregistrer</button>
            <button type="button" class="btn btn-secondary" id="btnCancel" style="display:none;">Annuler</button>
          </form>
        </div>
      </div>

      <!-- Tableau listant les cours -->
      <div class="card shadow mb-4">
        <div class="card-header">
          <h6 class="m-0 font-weight-bold text-primary">Liste des Cours</h6>
        </div>
        <div class="card-body">
          <div id="alertMsg" class="alert" style="display:none;"></div>
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTableCours" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Code</th>
                  <th>Nom du Cours</th>
                  <th>Sites du cours</th>
                  <th>Détails</th>
                  <th>Durée (h)</th>
                  <th style="min-width: 150px;">Actions</th>
                </tr>
              </thead>
              <tbody id="coursBody"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div><!-- Fin #content -->
</div><!-- Fin #content-wrapper -->

<?php include __DIR__ . '/../../../../frontend/components/footer.php'; ?>

<script>
// Lors du chargement
document.addEventListener('DOMContentLoaded', () => {
  loadCours();
  loadSites();

  document.getElementById('coursForm').addEventListener('submit', handleCoursFormSubmit);
  document.getElementById('btnCancel').addEventListener('click', resetCoursForm);

  // Initialisation DataTable après un léger délai
  setTimeout(() => {
    $('#dataTableCours').DataTable({
      responsive: true,
      pageLength: 10,
      language: { url: '../../../../frontend/assets/vendor/datatables/French.json' }
    });
  }, 500);
});

// Chargement des cours
async function loadCours() {
  try {
    const res = await fetch('../../../routes/admin-api.php?entity=cours&action=list');
    const data = await res.json();
    renderCours(data);
  } catch (error) {
    console.error(error);
    showCoursMessage("Erreur lors du chargement des cours.", 'danger');
  }
}

// Chargement des sites pour remplir le select multiple
async function loadSites() {
  try {
    const res = await fetch('../../../routes/admin-api.php?entity=sites&action=list');
    const data = await res.json();
    const select = document.getElementById('sites');
    select.innerHTML = "";
    data.forEach(site => {
      const option = document.createElement('option');
      option.value = site.id_site;
      option.textContent = site.nom;
      select.appendChild(option);
    });
  } catch (error) {
    console.error("Erreur lors du chargement des sites:", error);
  }
}

// Rendu du tableau des cours
function renderCours(data) {
  const tbody = document.getElementById('coursBody');
  tbody.innerHTML = "";
  if (!Array.isArray(data) || data.length === 0) {
    tbody.innerHTML = `<tr><td colspan="7" class="text-center">Aucun cours trouvé.</td></tr>`;
    return;
  }
  data.forEach(c => {
    const sitesText = c.sites ? sanitize(c.sites) : "";
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${c.id_cours}</td>
      <td>${sanitize(c.code_cours)}</td>
      <td>${sanitize(c.nom_cours)}</td>
      <td>${sitesText}</td>
      <td>${sanitize(c.details || "")}</td>
      <td>${c.duree}</td>
      <td>
        <button class="btn btn-warning btn-sm me-1" onclick="editCours(${c.id_cours}, '${sanitize(c.code_cours)}', '${sanitize(c.nom_cours)}', '${sanitize(c.details || "")}', ${c.duree}, '${sanitize(c.sites || "")}')">
          <i class="fas fa-edit"></i> Modifier
        </button>
        <button class="btn btn-danger btn-sm" onclick="deleteCours(${c.id_cours})">
          <i class="fas fa-trash"></i> Supprimer
        </button>
      </td>
    `;
    tbody.appendChild(row);
  });
}

// Envoi du formulaire (création / mise à jour)
async function handleCoursFormSubmit() {
  const id = document.getElementById('id_cours').value;
  const code_cours = document.getElementById('code_cours').value.trim();
  const nom_cours = document.getElementById('nom_cours').value.trim();
  const details = document.getElementById('details').value.trim();
  const duree = parseInt(document.getElementById('duree').value, 10);

  if (!code_cours || !nom_cours || isNaN(duree)) {
    return showCoursMessage("Les champs Code, Nom et Durée sont requis.", 'danger');
  }

  const sitesSelect = document.getElementById('sites');
  const selectedOptions = Array.from(sitesSelect.selectedOptions);
  const sites = selectedOptions.map(opt => parseInt(opt.value, 10));

  const payload = { code_cours, nom_cours, details, duree, sites };
  const method = id ? 'PUT' : 'POST';
  const url = id
    ? `../../../routes/admin-api.php?entity=cours&action=update&id=${id}`
    : `../../../routes/admin-api.php?entity=cours&action=create`;

  try {
    const res = await fetch(url, {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const result = await res.json();
    showCoursMessage(result.message || result.error, result.message ? 'success' : 'danger');
    if (result.message) {
      resetCoursForm();
      loadCours();
    }
  } catch (error) {
    console.error(error);
    showCoursMessage(`Erreur lors de la requête ${method}.`, 'danger');
  }
}

// Préremplir le formulaire en vue d'une modification
function editCours(id, code, nom, details, duree, sitesStr) {
  document.getElementById('id_cours').value = id;
  document.getElementById('code_cours').value = code;
  document.getElementById('nom_cours').value = nom;
  document.getElementById('details').value = details;
  document.getElementById('duree').value = duree;

  const sitesSelect = document.getElementById('sites');
  Array.from(sitesSelect.options).forEach(opt => (opt.selected = false));
  if (sitesStr) {
    const siteIds = sitesStr.split(',').map(s => s.trim());
    Array.from(sitesSelect.options).forEach(opt => {
      if (siteIds.includes(opt.value)) opt.selected = true;
    });
  }

  document.getElementById('formTitle').textContent = "Modifier le Cours";
  document.getElementById('btnSubmit').textContent = "Enregistrer";
  document.getElementById('btnCancel').style.display = "inline-block";
}

// Suppression d'un cours
async function deleteCours(id) {
  if (!confirm("Voulez-vous supprimer ce cours ?")) return;
  try {
    const res = await fetch(`../../../routes/admin-api.php?entity=cours&action=delete&id=${id}`, {
      method: 'DELETE'
    });
    const result = await res.json();
    showCoursMessage(result.message || result.error, result.message ? 'success' : 'danger');
    if (result.message) loadCours();
  } catch (error) {
    console.error(error);
    showCoursMessage("Erreur lors de la requête DELETE.", 'danger');
  }
}

// Réinitialiser le formulaire
function resetCoursForm() {
  document.getElementById('id_cours').value = '';
  document.getElementById('code_cours').value = '';
  document.getElementById('nom_cours').value = '';
  document.getElementById('details').value = '';
  document.getElementById('duree').value = '1';
  
  const sitesSelect = document.getElementById('sites');
  Array.from(sitesSelect.options).forEach(opt => (opt.selected = false));

  document.getElementById('formTitle').textContent = "Ajouter un Cours";
  document.getElementById('btnSubmit').textContent = "Enregistrer";
  document.getElementById('btnCancel').style.display = "none";
}

// Afficher un message (alerte)
function showCoursMessage(msg, type) {
  const alertBox = document.getElementById('alertMsg');
  alertBox.textContent = msg;
  alertBox.className = `alert alert-${type}`;
  alertBox.style.display = 'block';
  setTimeout(() => {
    alertBox.style.display = 'none';
  }, 3000);
}

// Échapper le HTML
function sanitize(str) {
  if (typeof str !== 'string') return str;
  return str.replace(/[&<>"'`]/g, m => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;',
    '"': '&quot;', "'": '&#39;', '`': '&#96;'
  })[m]);
}
</script>
