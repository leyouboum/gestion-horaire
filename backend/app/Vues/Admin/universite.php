<?php
/**
 * universite.php
 * Gestion des Universités (CRUD), sans session, utilisant SB Admin 2.
 */

// Inclusion du header (ouvre <html>, <head>, <body>, <div id="wrapper">)
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
      <h1 class="h3 mb-4 text-gray-800">Gestion des Universités</h1>

      <div class="card mb-4 shadow-sm">
        <div class="card-header">
          <h6 class="m-0 font-weight-bold text-primary" id="formTitle">Ajouter une Université</h6>
        </div>
        <div class="card-body">
          <form id="univForm" onsubmit="return false;">
            <input type="hidden" id="id_universite" />
            <div class="mb-3">
              <label for="nom_universite" class="form-label">Nom de l'Université</label>
              <input type="text" class="form-control" id="nom_universite" placeholder="Ex: Université de Paris" required />
            </div>
            <button type="submit" class="btn btn-primary" id="btnSubmit">Enregistrer</button>
            <button type="button" class="btn btn-secondary" id="btnCancel" style="display: none;">Annuler</button>
          </form>
        </div>
      </div>

      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Liste des Universités</h6>
        </div>
        <div class="card-body">
          <div id="alertMsg" class="alert" style="display: none;"></div>
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTableUniv" width="100%">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nom</th>
                  <th style="min-width:150px;">Actions</th>
                </tr>
              </thead>
              <tbody id="univBody"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div> <!-- Fin #content -->
</div> <!-- Fin #content-wrapper -->

<?php include __DIR__ . '/../../../../frontend/components/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('univForm');
  const cancelBtn = document.getElementById('btnCancel');
  const idInput = document.getElementById('id_universite');
  const nameInput = document.getElementById('nom_universite');

  form.addEventListener('submit', () => handleSubmit(idInput.value, nameInput.value.trim()));
  cancelBtn.addEventListener('click', resetForm);

  loadUniversites();
});

async function loadUniversites() {
  try {
    const response = await fetch('../../../routes/admin-api.php?entity=universites&action=list');
    const data = await response.json();
    const tbody = document.getElementById('univBody');
    if (!Array.isArray(data)) throw new Error("Réponse invalide.");
    
    tbody.innerHTML = data.length 
      ? data.map(u => `
        <tr>
          <td>${u.id_universite}</td>
          <td>${sanitize(u.nom)}</td>
          <td>
            <button class="btn btn-warning btn-sm me-1" 
                    onclick="editUniversite(${u.id_universite}, '${sanitize(u.nom)}')">
              <i class="fas fa-edit"></i> Modifier
            </button>
            <button class="btn btn-danger btn-sm" 
                    onclick="deleteUniversite(${u.id_universite})">
              <i class="fas fa-trash"></i> Supprimer
            </button>
          </td>
        </tr>`).join('') 
      : `<tr><td colspan="3" class="text-center">Aucune université trouvée.</td></tr>`;

    initDataTable();
  } catch (error) {
    showMessage("Impossible de charger les universités.", 'danger');
  }
}

function initDataTable() {
  const table = $('#dataTableUniv');
  if ($.fn.DataTable.isDataTable(table)) {
    table.DataTable().destroy();
  }
  setTimeout(() => {
    table.DataTable({
      responsive: true,
      pageLength: 10,
      language: {
        url: '../../../../frontend/assets/vendor/datatables/French.json'
      },
      order: [[0, 'desc']]
    });
  }, 200);
}

async function handleSubmit(id, nom) {
  if (!nom) return showMessage("Merci d’indiquer un nom.", 'danger');

  const isUpdate = Boolean(id);
  const url = `../../../routes/admin-api.php?entity=universites&action=${isUpdate ? `update&id=${id}` : 'create'}`;
  const method = isUpdate ? 'PUT' : 'POST';

  try {
    const response = await fetch(url, {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nom })
    });
    const res = await response.json();
    showMessage(res.message || res.error, res.message ? 'success' : 'danger');
    if (res.message) setTimeout(() => location.reload(), 1000);
  } catch (error) {
    showMessage("Erreur serveur", 'danger');
  }
}

function editUniversite(id, nom) {
  document.getElementById('id_universite').value = id;
  document.getElementById('nom_universite').value = nom;
  document.getElementById('formTitle').textContent = "Modifier l'Université";
  document.getElementById('btnSubmit').textContent = "Mettre à jour";
  document.getElementById('btnCancel').style.display = "inline-block";
}

async function deleteUniversite(id) {
  if (!confirm("Confirmer la suppression ?")) return;
  try {
    const response = await fetch(`../../../routes/admin-api.php?entity=universites&action=delete&id=${id}`, { 
      method: 'DELETE' 
    });
    const res = await response.json();
    showMessage(res.message || res.error, res.message ? 'success' : 'danger');
    if (res.message) setTimeout(() => location.reload(), 1000);
  } catch (error) {
    showMessage("Erreur lors de la suppression", 'danger');
  }
}

function resetForm() {
  document.getElementById('univForm').reset();
  document.getElementById('id_universite').value = '';
  document.getElementById('formTitle').textContent = "Ajouter une Université";
  document.getElementById('btnSubmit').textContent = "Enregistrer";
  document.getElementById('btnCancel').style.display = "none";
}

function showMessage(msg, type) {
  const alertBox = document.getElementById('alertMsg');
  alertBox.textContent = msg;
  alertBox.className = `alert alert-${type}`;
  alertBox.style.display = 'block';
  setTimeout(() => alertBox.style.display = 'none', 3000);
}

function sanitize(str) {
  return String(str).replace(/[&<>"'`]/g, m => ({
    '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;',
    "'":'&#39;', '`':'&#96;'
  })[m]);
}
</script>
