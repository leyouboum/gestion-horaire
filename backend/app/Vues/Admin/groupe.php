<?php
/**
 * groupes.php
 * Gestion des Groupes (CRUD)
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
      <h1 class="h3 mb-4 text-gray-800">Gestion des Groupes</h1>

      <!-- Formulaire d'ajout / modification -->
      <div class="card mb-4 shadow-sm">
        <div class="card-header">
          <h6 id="formTitle" class="m-0 font-weight-bold text-primary">Ajouter un Groupe</h6>
        </div>
        <div class="card-body">
          <form id="groupeForm" onsubmit="return false;">
            <input type="hidden" id="id_groupe" />
            
            <div class="mb-3">
              <label for="id_universite" class="form-label">Université</label>
              <select class="form-select" id="id_universite" required>
                <option value="">-- Sélectionnez une université --</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="nom_groupe" class="form-label">Nom du Groupe</label>
              <input type="text" class="form-control" id="nom_groupe" required />
            </div>
            <div class="mb-3">
              <label for="nb_etudiants" class="form-label">Nombre d'étudiants</label>
              <input type="number" class="form-control" id="nb_etudiants" required min="20" max="40" />
            </div>
            <div class="mb-3">
              <label for="sitePrincipal" class="form-label">Site Principal</label>
              <select class="form-select" id="sitePrincipal" disabled>
                <option value="">-- Sélectionnez l'université ci-dessus --</option>
              </select>
            </div>
            <!-- Sites secondaires -->
            <div class="mb-3">
              <label class="form-label">Autres sites secondaires</label>
              <div id="sitesSecondairesContainer"></div>
            </div>

            <button type="submit" class="btn btn-primary" id="btnSubmit">Enregistrer</button>
            <button type="button" class="btn btn-secondary" id="btnCancel" style="display:none;">Annuler</button>
          </form>
        </div>
      </div>

      <!-- Tableau des groupes -->
      <div class="card shadow mb-4">
        <div class="card-header">
          <h6 class="m-0 font-weight-bold text-primary">Liste des Groupes</h6>
        </div>
        <div class="card-body">
          <div id="alertMsg" class="alert" style="display: none;"></div>
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTableGroupes" width="100%">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nom du Groupe</th>
                  <th>Étudiants</th>
                  <th>Université</th>
                  <th>Site Principal</th>
                  <th>Sites Secondaires</th>
                  <th style="min-width: 150px;">Actions</th>
                </tr>
              </thead>
              <tbody id="groupesBody"></tbody>
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
  loadUniversites();
  loadGroupes();

  document.getElementById('id_universite').addEventListener('change', onUniversiteChange);
  document.getElementById('groupeForm').addEventListener('submit', handleGroupeFormSubmit);
  document.getElementById('btnCancel').addEventListener('click', resetGroupeForm);

  setTimeout(() => {
    $('#dataTableGroupes').DataTable({
      responsive: true,
      pageLength: 10,
      language: { url: '../../../../frontend/assets/vendor/datatables/French.json' },
      order: [[0, 'desc']]
    });
  }, 500);
});

function loadUniversites() {
  fetch('../../../routes/admin-api.php?entity=universites&action=list')
    .then(res => res.json())
    .then(data => {
      const selectUniv = document.getElementById('id_universite');
      selectUniv.innerHTML = `<option value="">-- Sélectionnez une université --</option>`;
      data.forEach(u => {
        const opt = document.createElement('option');
        opt.value = u.id_universite;
        opt.textContent = sanitize(u.nom);
        selectUniv.appendChild(opt);
      });
    })
    .catch(err => showMessage("Erreur lors du chargement des universités", 'danger'));
}

function onUniversiteChange() {
  const univId = document.getElementById('id_universite').value;
  if (!univId) {
    document.getElementById('sitePrincipal').disabled = true;
    document.getElementById('sitePrincipal').innerHTML = `<option value="">-- Sélectionnez l'université ci-dessus --</option>`;
    document.getElementById('sitesSecondairesContainer').innerHTML = '';
    return;
  }
  loadSitesByUniversite(univId);
}

function loadSitesByUniversite(univId) {
  return fetch(`../../../routes/admin-api.php?entity=sites&action=listByUniversite&univId=${univId}`)
    .then(res => res.json())
    .then(sites => {
      renderSitePrincipal(sites);
      renderSitesSecondaires(sites);
    })
    .catch(err => showMessage("Erreur lors du chargement des sites", 'danger'));
}

function renderSitePrincipal(sites) {
  const sp = document.getElementById('sitePrincipal');
  sp.disabled = false;
  sp.innerHTML = `<option value="">-- Choisissez le site principal --</option>`;
  sites.forEach(site => {
    const opt = document.createElement('option');
    opt.value = site.id_site;
    opt.textContent = sanitize(site.nom);
    sp.appendChild(opt);
  });
}

function renderSitesSecondaires(sites) {
  const container = document.getElementById('sitesSecondairesContainer');
  container.innerHTML = '';
  if (!Array.isArray(sites) || sites.length === 0) {
    container.innerHTML = `<p>Aucun site pour cette université</p>`;
    return;
  }
  sites.forEach(site => {
    const cbId = `cbSiteSec_${site.id_site}`;
    const div = document.createElement('div');
    div.classList.add('form-check');
    div.innerHTML = `
      <input type="checkbox" class="form-check-input" id="${cbId}" value="${site.id_site}" />
      <label class="form-check-label" for="${cbId}">${sanitize(site.nom)}</label>
    `;
    container.appendChild(div);
  });
}

function loadGroupes() {
  fetch('../../../routes/admin-api.php?entity=groupes&action=list')
    .then(res => res.json())
    .then(data => renderGroupes(data))
    .catch(err => showMessage("Erreur lors du chargement des groupes", 'danger'));
}

function renderGroupes(groupes) {
  const tbody = document.getElementById('groupesBody');
  tbody.innerHTML = '';
  if (!Array.isArray(groupes) || groupes.length === 0) {
    tbody.innerHTML = `<tr><td colspan="7" class="text-center">Aucun groupe trouvé.</td></tr>`;
    return;
  }
  groupes.forEach(g => {
    let sitePrincipal = "N/A";
    let universiteName = "N/A";
    let sitesSecondaires = [];
    if (Array.isArray(g.sites)) {
      const princ = g.sites.find(s => s.is_principal);
      if (princ) {
        sitePrincipal = sanitize(princ.nom_site);
        universiteName = sanitize(princ.nom_universite);
      }
      sitesSecondaires = g.sites.filter(s => !s.is_principal).map(s => sanitize(s.nom_site));
    }
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${g.id_groupe}</td>
      <td>${sanitize(g.nom_groupe)}</td>
      <td>${g.nb_etudiants}</td>
      <td>${universiteName}</td>
      <td>${sitePrincipal}</td>
      <td>${sitesSecondaires.join(', ') || '—'}</td>
      <td>
        <button class="btn btn-warning btn-sm me-1" 
                onclick='editGroupe(${g.id_groupe}, "${sanitize(g.nom_groupe)}", ${g.nb_etudiants}, ${JSON.stringify(g.sites)})'>
          <i class="fas fa-edit"></i> Modifier
        </button>
        <button class="btn btn-danger btn-sm" 
                onclick="deleteGroupe(${g.id_groupe})">
          <i class="fas fa-trash"></i> Supprimer
        </button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function handleGroupeFormSubmit() {
  const id_groupe = document.getElementById('id_groupe').value;
  const id_universite = document.getElementById('id_universite').value;
  const nom_groupe = document.getElementById('nom_groupe').value.trim();
  const nb_etudiants = parseInt(document.getElementById('nb_etudiants').value, 10);
  const site_principal = document.getElementById('sitePrincipal').value;

  if (!id_universite) return showMessage("Veuillez sélectionner l'université.", 'danger');
  if (!nom_groupe)    return showMessage("Le nom du groupe est requis.", 'danger');
  if (isNaN(nb_etudiants) || nb_etudiants < 20 || nb_etudiants > 40) {
    return showMessage("Le nombre d'étudiants doit être entre 20 et 40.", 'danger');
  }
  if (!site_principal) return showMessage("Veuillez sélectionner un site principal.", 'danger');

  const cbs = document.querySelectorAll('#sitesSecondairesContainer input[type="checkbox"]');
  const sites_secondaires = [];
  cbs.forEach(cb => { if (cb.checked) sites_secondaires.push(parseInt(cb.value, 10)); });

  const payload = {
    id_universite: parseInt(id_universite, 10),
    nom_groupe,
    nb_etudiants,
    site_principal: parseInt(site_principal, 10),
    sites_secondaires
  };

  const method = id_groupe ? 'PUT' : 'POST';
  const url = id_groupe
    ? `../../../routes/admin-api.php?entity=groupes&action=update&id=${id_groupe}`
    : `../../../routes/admin-api.php?entity=groupes&action=create`;

  fetch(url, {
    method,
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  })
  .then(r => r.json())
  .then(resp => {
    if (resp.message) {
      showMessage(resp.message, 'success');
      resetGroupeForm();
      loadGroupes();
    } else {
      showMessage(resp.error || "Erreur lors de l'enregistrement", 'danger');
    }
  })
  .catch(err => showMessage(`Erreur lors de la requête ${method}`, 'danger'));
}

function editGroupe(id, nom, nb, sitesArr) {
  document.getElementById('id_groupe').value = id;
  document.getElementById('nom_groupe').value = nom;
  document.getElementById('nb_etudiants').value = nb;
  const princ = sitesArr.find(s => s.is_principal);
  const univId = princ ? princ.id_universite : '';
  document.getElementById('id_universite').value = univId;

  loadSitesByUniversite(univId).then(() => {
    // Sélection du site principal
    if (princ) {
      document.getElementById('sitePrincipal').value = princ.id_site;
    }
    // Sites secondaires
    const secondaries = sitesArr.filter(s => !s.is_principal).map(s => s.id_site);
    secondaries.forEach(sid => {
      const cb = document.getElementById(`cbSiteSec_${sid}`);
      if (cb) cb.checked = true;
    });
  });

  document.getElementById('formTitle').textContent = "Modifier le Groupe";
  document.getElementById('btnSubmit').textContent = "Enregistrer";
  document.getElementById('btnCancel').style.display = "inline-block";
}

function deleteGroupe(id) {
  if (!confirm("Voulez-vous vraiment supprimer ce groupe ?")) return;
  fetch(`../../../routes/admin-api.php?entity=groupes&action=delete&id=${id}`, { method: 'DELETE' })
  .then(r => r.json())
  .then(resp => {
    if (resp.message) {
      showMessage(resp.message, 'success');
      loadGroupes();
    } else {
      showMessage(resp.error || "Erreur lors de la suppression", 'danger');
    }
  })
  .catch(err => showMessage("Erreur lors de la requête DELETE", 'danger'));
}

function resetGroupeForm() {
  document.getElementById('id_groupe').value = '';
  document.getElementById('id_universite').value = '';
  document.getElementById('nom_groupe').value = '';
  document.getElementById('nb_etudiants').value = '';
  document.getElementById('sitePrincipal').innerHTML = `<option value="">-- Sélectionnez l'université ci-dessus --</option>`;
  document.getElementById('sitePrincipal').disabled = true;
  document.getElementById('sitesSecondairesContainer').innerHTML = '';
  document.getElementById('formTitle').textContent = "Ajouter un Groupe";
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
  if (typeof str !== 'string') return str;
  return str.replace(/[&<>"'`]/g, m => ({
    '&':'&amp;', '<':'&lt;', '>':'&gt;', 
    '"':'&quot;', "'":'&#39;', '`':'&#96;'
  })[m]);
}
</script>
