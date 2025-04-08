<?php
/**
 * materiel.php
 * Gestion du Matériel (CRUD) sans sessions, SB Admin 2.
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
      <h1 class="h3 mb-4 text-gray-800">Matériels</h1>

      <!-- Formulaire d'ajout / modification -->
      <div class="card mb-4 shadow-sm">
        <div class="card-header">
          <h6 id="formTitle" class="m-0 font-weight-bold text-primary">Ajouter un Matériel</h6>
        </div>
        <div class="card-body">
          <form id="materielForm" onsubmit="return false;">
            <input type="hidden" id="id_materiel" />
            <div class="mb-3">
              <label for="type_materiel" class="form-label">Type du Matériel</label>
              <input type="text" class="form-control" id="type_materiel" required />
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="is_mobile" />
              <label class="form-check-label" for="is_mobile">Matériel Mobile</label>
            </div>
            <div class="mb-3" id="siteAffectationContainer">
              <label for="affectation_site" class="form-label">Site d'affectation</label>
              <select class="form-select" id="affectation_site">
                <option value="">-- Sélectionnez un site --</option>
              </select>
            </div>
            <div class="mb-3" id="salleFixeContainer">
              <label for="id_salle_fixe" class="form-label">Salle Fixe</label>
              <select class="form-select" id="id_salle_fixe">
                <option value="">-- Aucune --</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary" id="btnSubmit">Enregistrer</button>
            <button type="button" class="btn btn-secondary" id="btnCancel" style="display:none;">Annuler</button>
          </form>
        </div>
      </div>

      <!-- Tableau listant les matériels -->
      <div class="card shadow mb-4">
        <div class="card-header">
          <h6 class="m-0 font-weight-bold text-primary">Liste des Matériels</h6>
        </div>
        <div class="card-body">
          <div id="alertMsg" class="alert" style="display:none;"></div>
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTableMateriel" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Type</th>
                  <th>Mobile</th>
                  <th>Salle Fixe</th>
                  <th>Site d'affectation</th>
                  <th style="min-width:150px;">Actions</th>
                </tr>
              </thead>
              <tbody id="materielBody"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div><!-- Fin #content -->
</div><!-- Fin #content-wrapper -->

<?php include __DIR__ . '/../../../../frontend/components/footer.php'; ?>

<script>
let sallesDisponibles = {};
let affectationSiteMap = {};

// Au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
  loadSitesAffectation();
  loadMateriel();

  document.getElementById('materielForm').addEventListener('submit', handleMaterielFormSubmit);
  document.getElementById('btnCancel').addEventListener('click', resetMaterielForm);
  document.getElementById('is_mobile').addEventListener('change', toggleSalleFixe);
  document.getElementById('affectation_site').addEventListener('change', () => {
    loadSallesBySite(document.getElementById('affectation_site').value);
  });

  toggleSalleFixe();

  setTimeout(() => {
    $('#dataTableMateriel').DataTable({
      responsive: true,
      pageLength: 10,
      language: { url: '../../../../frontend/assets/vendor/datatables/French.json' }
    });
  }, 500);
});

// Charger la liste des sites pour l'affectation
async function loadSitesAffectation() {
  try {
    const res = await fetch('../../../routes/admin-api.php?entity=sites&action=list');
    const sites = await res.json();
    const select = document.getElementById('affectation_site');
    select.innerHTML = '<option value="">-- Sélectionnez un site --</option>';
    sites.forEach(site => {
      affectationSiteMap[site.id_site] = site.nom;
      const opt = document.createElement('option');
      opt.value = site.id_site;
      opt.textContent = `${sanitize(site.nom)} (${sanitize(site.nom_universite)})`;
      select.appendChild(opt);
    });
  } catch (error) {
    console.error("Erreur lors du chargement des sites:", error);
  }
}

// Charger la liste des salles pour un site donné
async function loadSallesBySite(siteId) {
  const select = document.getElementById('id_salle_fixe');
  select.innerHTML = '<option value="">-- Aucune --</option>';
  sallesDisponibles = {};
  if (!siteId) return;
  try {
    const res = await fetch(`../../../routes/admin-api.php?entity=salles&action=listBySite&siteId=${siteId}`);
    const salles = await res.json();
    salles.forEach(salle => {
      sallesDisponibles[salle.id_salle] = salle.nom_salle;
      const opt = document.createElement('option');
      opt.value = salle.id_salle;
      opt.textContent = sanitize(salle.nom_salle);
      select.appendChild(opt);
    });
  } catch (error) {
    console.error("Erreur lors du chargement des salles:", error);
  }
}

// Afficher / masquer le bloc Salle Fixe selon is_mobile
function toggleSalleFixe() {
  const isMobile = document.getElementById('is_mobile').checked;
  document.getElementById('salleFixeContainer').style.display = isMobile ? 'none' : 'block';
}

// Charger la liste des matériels
async function loadMateriel() {
  try {
    const res = await fetch('../../../routes/admin-api.php?entity=materiels&action=list');
    const data = await res.json();
    renderMateriel(data);
  } catch (error) {
    console.error(error);
    showMaterielMessage("Erreur lors du chargement du matériel.", 'danger');
  }
}

// Affiche la liste des matériels dans le tableau
function renderMateriel(data) {
  const tbody = document.getElementById('materielBody');
  tbody.innerHTML = '';
  if (!Array.isArray(data) || data.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6" class="text-center">Aucun matériel trouvé.</td></tr>';
    return;
  }
  data.forEach(m => {
    // S'il est mobile, on n'affiche pas la salle
    const salleText = m.is_mobile ? '-' : (m.salle_fixe ? sanitize(m.salle_fixe) : '-');
    const siteAffectation = m.site_affectation ? sanitize(m.site_affectation) : '-';

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${m.id_materiel}</td>
      <td>${sanitize(m.type_materiel)}</td>
      <td>${m.is_mobile ? 'Oui' : 'Non'}</td>
      <td>${salleText}</td>
      <td>${siteAffectation}</td>
      <td>
        <button class="btn btn-warning btn-sm me-1"
          onclick="editMateriel(${m.id_materiel}, '${sanitize(m.type_materiel)}', ${m.is_mobile}, ${m.id_salle_fixe || 'null'}, ${m.id_site_affectation || 'null'})">
          <i class="fas fa-edit"></i> Modifier
        </button>
        <button class="btn btn-danger btn-sm" onclick="deleteMateriel(${m.id_materiel})">
          <i class="fas fa-trash"></i> Supprimer
        </button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

// Remplir le formulaire pour modification
function editMateriel(id, type, isMobile, salleFixe, siteAffectation) {
  document.getElementById('id_materiel').value = id;
  document.getElementById('type_materiel').value = type;
  document.getElementById('is_mobile').checked = isMobile;

  // Assigner le site dans la liste
  document.getElementById('affectation_site').value = siteAffectation || '';
  
  // Si ce n'est pas mobile, on charge la liste des salles du site (si siteAffectation est défini)
  if (!isMobile && siteAffectation) {
    loadSallesBySite(siteAffectation).then(() => {
      // Quand les salles sont chargées, on peut sélectionner la bonne salle
      document.getElementById('id_salle_fixe').value = salleFixe || '';
    });
  } else {
    // Matériel mobile ou pas de site
    document.getElementById('id_salle_fixe').innerHTML = '<option value="">-- Aucune --</option>';
  }

  toggleSalleFixe();

  document.getElementById('formTitle').textContent = "Modifier le Matériel";
  document.getElementById('btnSubmit').textContent = "Enregistrer";
  document.getElementById('btnCancel').style.display = "inline-block";
}

// Soumission du formulaire Matériel (CREATE ou UPDATE)
async function handleMaterielFormSubmit() {
  const id_materiel = document.getElementById('id_materiel').value;
  const type_materiel = document.getElementById('type_materiel').value.trim();
  const is_mobile = document.getElementById('is_mobile').checked;
  const affectationSite = document.getElementById('affectation_site').value;
  let id_salle_fixe = document.getElementById('id_salle_fixe').value;

  if (!type_materiel) {
    return showMaterielMessage("Le type de matériel est requis.", 'danger');
  }

  if (is_mobile) {
    id_salle_fixe = null;
  } else {
    id_salle_fixe = id_salle_fixe ? parseInt(id_salle_fixe, 10) : null;
  }

  const payload = {
    type_materiel,
    is_mobile,
    id_salle_fixe,
    // N'affecte le site que s'il est mobile et qu'on a un site
    // (ou si vous voulez l'autoriser même pour le fixe, à vous de voir)
    id_site_affectation: affectationSite ? parseInt(affectationSite, 10) : null
  };

  const method = id_materiel ? 'PUT' : 'POST';
  const url = id_materiel
    ? `../../../routes/admin-api.php?entity=materiels&action=update&id=${id_materiel}`
    : `../../../routes/admin-api.php?entity=materiels&action=create`;

  try {
    const res = await fetch(url, {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const resp = await res.json();
    showMaterielMessage(resp.message || resp.error, resp.message ? 'success' : 'danger');
    if (resp.message) {
      resetMaterielForm();
      loadMateriel();
    }
  } catch (error) {
    console.error(error);
    showMaterielMessage(`Erreur lors de la requête ${method}.`, 'danger');
  }
}

// Supprimer un matériel
async function deleteMateriel(id) {
  if (!confirm("Voulez-vous supprimer ce matériel ?")) return;
  try {
    const res = await fetch(`../../../routes/admin-api.php?entity=materiels&action=delete&id=${id}`, {
      method: 'DELETE'
    });
    const resp = await res.json();
    showMaterielMessage(resp.message || resp.error, resp.message ? 'success' : 'danger');
    if (resp.message) loadMateriel();
  } catch (error) {
    console.error(error);
    showMaterielMessage("Erreur lors de la requête DELETE.", 'danger');
  }
}

// Réinitialiser le formulaire
function resetMaterielForm() {
  document.getElementById('id_materiel').value = '';
  document.getElementById('type_materiel').value = '';
  document.getElementById('is_mobile').checked = false;
  document.getElementById('affectation_site').value = '';
  document.getElementById('id_salle_fixe').innerHTML = '<option value="">-- Aucune --</option>';

  document.getElementById('formTitle').textContent = "Ajouter un Matériel";
  document.getElementById('btnSubmit').textContent = "Enregistrer";
  document.getElementById('btnCancel').style.display = "none";
  toggleSalleFixe();
}

// Afficher un message temporaire (success/danger)
function showMaterielMessage(msg, type) {
  const alertDiv = document.getElementById('alertMsg');
  alertDiv.textContent = msg;
  alertDiv.className = `alert alert-${type}`;
  alertDiv.style.display = 'block';
  setTimeout(() => { alertDiv.style.display = 'none'; }, 3000);
}

// Échapper les caractères spéciaux pour éviter XSS
function sanitize(str) {
  if (typeof str !== 'string') return str;
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
    '`': '&#96;'
  };
  return str.replace(/[&<>"'`]/g, m => map[m]);
}
</script>
