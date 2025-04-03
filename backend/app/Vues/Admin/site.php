<?php
/**
 * site.php
 * Gestion des Sites Universitaires (CRUD), sans session.
 * Utilise SB Admin 2 + dataTables.
 */

// Inclusion du header (ouvre <html>, <head>, <body>, <div id="wrapper">)
include __DIR__ . '/../../../../frontend/components/header.php';

// Inclusion du sidebar et du topbar
include __DIR__ . '/../../../../frontend/components/sidebar.php';
?>
<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
  <!-- Main Content -->
  <div id="content">
    <?php include __DIR__ . '/../../../../frontend/components/topbar.php'; ?>

    <div class="container-fluid my-4">
      <h1 class="h3 mb-4 text-gray-800">Gestion des Sites Universitaires</h1>

      <!-- Formulaire d'ajout/modification de site -->
      <div class="card mb-4 shadow-sm">
        <div class="card-header">
          <h6 id="formTitle" class="m-0 font-weight-bold text-primary">Ajouter un Site</h6>
        </div>
        <div class="card-body">
          <form id="siteForm" onsubmit="return false;">
            <input type="hidden" id="id_site" />

            <div class="mb-3">
              <label for="id_universite" class="form-label">Université</label>
              <select class="form-select" id="id_universite" required>
                <option value="">Sélectionnez une université</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="nom_site" class="form-label">Nom du Site</label>
              <input type="text" class="form-control" id="nom_site" placeholder="Ex: Campus Nord" required />
            </div>

            <div class="mb-3">
              <label for="heure_ouverture" class="form-label">Heure d'ouverture</label>
              <input type="time" class="form-control" id="heure_ouverture" required />
            </div>

            <div class="mb-3">
              <label for="heure_fermeture" class="form-label">Heure de fermeture</label>
              <input type="time" class="form-control" id="heure_fermeture" required />
            </div>

            <button type="submit" class="btn btn-primary" id="btnSubmit">Enregistrer</button>
            <button type="button" class="btn btn-secondary" id="btnCancel" style="display: none;">Annuler</button>
          </form>
        </div>
      </div>

      <!-- Liste des sites -->
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Liste des Sites</h6>
        </div>
        <div class="card-body">
          <div id="alertMsg" class="alert" style="display: none;"></div>
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTableSites" width="100%">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Université</th>
                  <th>Nom</th>
                  <th>Ouverture</th>
                  <th>Fermeture</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="sitesBody"></tbody>
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
  loadUniversites();
  loadSites();
  document.getElementById('siteForm').addEventListener('submit', submitForm);
  document.getElementById('btnCancel').addEventListener('click', resetForm);
});

async function loadUniversites() {
  try {
    const res = await fetch('../../../routes/admin-api.php?entity=universites&action=list');
    const universites = await res.json();
    if (!Array.isArray(universites)) throw new Error("Réponse invalide.");

    const select = document.getElementById('id_universite');
    select.innerHTML = '<option value="">Sélectionnez une université</option>';
    universites.forEach(u => {
      select.innerHTML += `<option value="${u.id_universite}">${sanitize(u.nom)}</option>`;
    });
  } catch (error) {
    showMessage("Échec du chargement des universités.", 'danger');
  }
}

async function loadSites() {
  try {
    const res = await fetch('../../../routes/admin-api.php?entity=sites&action=list');
    const sites = await res.json();
    if (!Array.isArray(sites)) throw new Error("Réponse invalide.");

    const tbody = document.getElementById('sitesBody');
    tbody.innerHTML = sites.length ? sites.map(site => `
      <tr>
        <td>${site.id_site}</td>
        <td>${sanitize(site.nom_universite || '')}</td>
        <td>${sanitize(site.nom)}</td>
        <td>${sanitize(site.heure_ouverture)}</td>
        <td>${sanitize(site.heure_fermeture)}</td>
        <td>
          <button class="btn btn-warning btn-sm me-1"
                  onclick="editSite(${site.id_site}, ${site.id_universite}, '${sanitize(site.nom)}', '${site.heure_ouverture}', '${site.heure_fermeture}')">
            <i class="fas fa-edit"></i> Modifier
          </button>
          <button class="btn btn-danger btn-sm"
                  onclick="deleteSite(${site.id_site})">
            <i class="fas fa-trash"></i> Supprimer
          </button>
        </td>
      </tr>
    `).join('') : `<tr><td colspan="6" class="text-center">Aucun site trouvé.</td></tr>`;
    initDataTable();
  } catch (error) {
    showMessage("Erreur lors du chargement des sites.", 'danger');
  }
}

function initDataTable() {
  const table = $('#dataTableSites');
  if ($.fn.DataTable.isDataTable(table)) table.DataTable().destroy();

  setTimeout(() => {
    table.DataTable({
      responsive: true,
      pageLength: 10,
      language: {
        url: '../../../../frontend/assets/vendor/datatables/French.json'
      }
    });
  }, 200);
}

async function submitForm() {
  const id = document.getElementById('id_site').value;
  const payload = {
    id_universite: document.getElementById('id_universite').value,
    nom: document.getElementById('nom_site').value.trim(),
    heure_ouverture: document.getElementById('heure_ouverture').value,
    heure_fermeture: document.getElementById('heure_fermeture').value
  };

  if (!payload.id_universite || !payload.nom || !payload.heure_ouverture || !payload.heure_fermeture) {
    return showMessage("Tous les champs sont requis.", 'danger');
  }

  const isUpdate = Boolean(id);
  const url = `../../../routes/admin-api.php?entity=sites&action=${isUpdate ? `update&id=${id}` : 'create'}`;
  const method = isUpdate ? 'PUT' : 'POST';

  try {
    const res = await fetch(url, {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const result = await res.json();
    showMessage(result.message || result.error, result.message ? 'success' : 'danger');
    if (result.message) setTimeout(() => location.reload(), 1000);
  } catch (error) {
    showMessage("Erreur lors de l’envoi du formulaire.", 'danger');
  }
}

function editSite(id, univId, nom, ouv, ferm) {
  document.getElementById('id_site').value = id;
  document.getElementById('id_universite').value = univId;
  document.getElementById('nom_site').value = nom;
  document.getElementById('heure_ouverture').value = ouv;
  document.getElementById('heure_fermeture').value = ferm;
  document.getElementById('formTitle').textContent = "Modifier le Site";
  document.getElementById('btnSubmit').textContent = "Mettre à jour";
  document.getElementById('btnCancel').style.display = "inline-block";
}

async function deleteSite(id) {
  if (!confirm("Confirmer la suppression de ce site ?")) return;
  try {
    const res = await fetch(`../../../routes/admin-api.php?entity=sites&action=delete&id=${id}`, { method: 'DELETE' });
    const result = await res.json();
    showMessage(result.message || result.error, result.message ? 'success' : 'danger');
    if (result.message) setTimeout(() => location.reload(), 1000);
  } catch (error) {
    showMessage("Erreur lors de la suppression.", 'danger');
  }
}

function resetForm() {
  ['id_site','id_universite','nom_site','heure_ouverture','heure_fermeture'].forEach(id => {
    document.getElementById(id).value = '';
  });
  document.getElementById('formTitle').textContent = "Ajouter un Site";
  document.getElementById('btnSubmit').textContent = "Enregistrer";
  document.getElementById('btnCancel').style.display = "none";
}

function showMessage(msg, type) {
  const alert = document.getElementById('alertMsg');
  alert.textContent = msg;
  alert.className = `alert alert-${type}`;
  alert.style.display = 'block';
  setTimeout(() => alert.style.display = 'none', 3000);
}

function sanitize(str) {
  return String(str).replace(/[&<>"'`]/g, m => ({
    '&':'&amp;', '<':'&lt;', '>':'&gt;',
    '"':'&quot;', "'":'&#39;', '`':'&#96;'
  })[m]);
}
</script>
