<?php
/**
 * salle.php
 * Gestion des Salles (CRUD)
 */

include __DIR__ . '/../../../../frontend/components/header.php';
include __DIR__ . '/../../../../frontend/components/sidebar.php';
?>
<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
  <!-- Main Content -->
  <div id="content">
    <?php include __DIR__ . '/../../../../frontend/components/topbar.php'; ?>

    <div class="container-fluid my-4">
      <h1 class="h3 mb-4 text-gray-800">Gestion des Salles</h1>

      <!-- Formulaire d'ajout / modification -->
      <div class="card mb-4 shadow-sm">
        <div class="card-header">
          <h6 id="formTitle" class="m-0 font-weight-bold text-primary">Ajouter une Salle</h6>
        </div>
        <div class="card-body">
          <form id="salleForm" onsubmit="return false;">
            <input type="hidden" id="id_salle" />

            <div class="mb-3">
              <label for="id_site" class="form-label">Site</label>
              <select class="form-select" id="id_site" required>
                <option value="">-- Sélectionnez un site --</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="nom_salle" class="form-label">Nom de la Salle</label>
              <input type="text" class="form-control" id="nom_salle" required />
            </div>
            <div class="mb-3">
              <label for="capacite_max" class="form-label">Capacité Max</label>
              <input type="number" class="form-control" id="capacite_max" required min="1" />
            </div>

            <button type="submit" class="btn btn-primary" id="btnSubmit">Enregistrer</button>
            <button type="button" class="btn btn-secondary" id="btnCancel" style="display:none;">Annuler</button>
          </form>
        </div>
      </div>

      <!-- Tableau listant les salles -->
      <div class="card shadow mb-4">
        <div class="card-header">
          <h6 class="m-0 font-weight-bold text-primary">Liste des Salles</h6>
        </div>
        <div class="card-body">
          <div id="alertMsg" class="alert" style="display:none;"></div>
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTableSalles" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Site</th>
                  <th>Nom Salle</th>
                  <th>Capacité Max</th>
                  <th style="min-width:150px;">Actions</th>
                </tr>
              </thead>
              <tbody id="sallesBody"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div><!-- Fin #content -->
</div><!-- Fin #content-wrapper -->

<?php include __DIR__ . '/../../../../frontend/components/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  loadSites();
  loadSalles();

  document.getElementById('salleForm').addEventListener('submit', handleSalleFormSubmit);
  document.getElementById('btnCancel').addEventListener('click', resetSalleForm);

  setTimeout(() => {
    $('#dataTableSalles').DataTable({
      responsive: true,
      pageLength: 10,
      language: { url: '../../../../frontend/assets/vendor/datatables/French.json' },
      order: [[0, 'desc']]
    });
  }, 500);
});

async function loadSites() {
  try {
    const res = await fetch('../../../routes/admin-api.php?entity=sites&action=list');
    const data = await res.json();
    const select = document.getElementById('id_site');
    select.innerHTML = '<option value="">-- Sélectionnez un site --</option>';
    data.forEach(site => {
      const opt = document.createElement('option');
      opt.value = site.id_site;
      opt.textContent = sanitize(site.nom);
      select.appendChild(opt);
    });
  } catch (err) {
    console.error("Erreur lors du chargement des sites:", err);
  }
}

async function loadSalles() {
  try {
    const res = await fetch('../../../routes/admin-api.php?entity=salles&action=list');
    const data = await res.json();
    renderSalles(data);
  } catch (err) {
    console.error(err);
    showSalleMessage("Erreur lors du chargement des salles.", 'danger');
  }
}

function renderSalles(data) {
  const tbody = document.getElementById('sallesBody');
  tbody.innerHTML = '';
  if (!Array.isArray(data) || data.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5" class="text-center">Aucune salle trouvée.</td></tr>';
    return;
  }
  data.forEach(s => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${s.id_salle}</td>
      <td>${sanitize(s.nom_site || '')}</td>
      <td>${sanitize(s.nom_salle)}</td>
      <td>${s.capacite_max}</td>
      <td>
        <button class="btn btn-warning btn-sm me-1" onclick="editSalle(${s.id_salle}, ${s.id_site}, '${sanitize(s.nom_salle)}', ${s.capacite_max})">
          <i class="fas fa-edit"></i> Modifier
        </button>
        <button class="btn btn-danger btn-sm" onclick="deleteSalle(${s.id_salle})">
          <i class="fas fa-trash"></i> Supprimer
        </button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

async function handleSalleFormSubmit() {
  const id_salle = document.getElementById('id_salle').value;
  const id_site  = document.getElementById('id_site').value;
  const nom_salle = document.getElementById('nom_salle').value.trim();
  const capacite_max = parseInt(document.getElementById('capacite_max').value, 10);

  if (!id_site || !nom_salle || isNaN(capacite_max)) {
    return showSalleMessage("Tous les champs sont requis.", 'danger');
  }

  const payload = { id_site, nom_salle, capacite_max };
  const isUpdate = Boolean(id_salle);
  const url = `../../../routes/admin-api.php?entity=salles&action=${isUpdate ? `update&id=${id_salle}` : 'create'}`;
  const method = isUpdate ? 'PUT' : 'POST';

  try {
    const res = await fetch(url, {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const resp = await res.json();
    showSalleMessage(resp.message || resp.error, resp.message ? 'success' : 'danger');
    if (resp.message) {
      resetSalleForm();
      loadSalles();
    }
  } catch (err) {
    console.error(err);
    showSalleMessage("Erreur lors de la requête.", 'danger');
  }
}

function editSalle(id, siteId, nomSalle, cap) {
  document.getElementById('id_salle').value = id;
  document.getElementById('id_site').value = siteId;
  document.getElementById('nom_salle').value = nomSalle;
  document.getElementById('capacite_max').value = cap;

  document.getElementById('formTitle').textContent = "Modifier la Salle";
  document.getElementById('btnSubmit').textContent = "Enregistrer";
  document.getElementById('btnCancel').style.display = "inline-block";
}

async function deleteSalle(id) {
  if (!confirm("Voulez-vous supprimer cette salle ?")) return;
  try {
    const res = await fetch(`../../../routes/admin-api.php?entity=salles&action=delete&id=${id}`, { method: 'DELETE' });
    const resp = await res.json();
    showSalleMessage(resp.message || resp.error, resp.message ? 'success' : 'danger');
    if (resp.message) loadSalles();
  } catch (err) {
    console.error(err);
    showSalleMessage("Erreur lors de la requête DELETE.", 'danger');
  }
}

function resetSalleForm() {
  document.getElementById('id_salle').value = '';
  document.getElementById('id_site').value = '';
  document.getElementById('nom_salle').value = '';
  document.getElementById('capacite_max').value = '';
  document.getElementById('formTitle').textContent = "Ajouter une Salle";
  document.getElementById('btnSubmit').textContent = "Enregistrer";
  document.getElementById('btnCancel').style.display = "none";
}

function showSalleMessage(msg, type) {
  const alertBox = document.getElementById('alertMsg');
  alertBox.textContent = msg;
  alertBox.className = `alert alert-${type}`;
  alertBox.style.display = 'block';
  setTimeout(() => {
    alertBox.style.display = 'none';
  }, 3000);
}

function sanitize(str) {
  if (typeof str !== 'string') return str;
  return str.replace(/[&<>"'`]/g, m => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;',
    '"': '&quot;', "'": '&#39;', '`': '&#96;'
  })[m]);
}
</script>
