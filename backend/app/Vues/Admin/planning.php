<?php
/**
 * planning.php
 * Gestion du Planning (CRUD)
 */

// Inclusion du header (ouvre <html>, <head>, <body> et <div id="wrapper">)
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
      <h1 class="h3 mb-4 text-gray-800">Gestion du Planning</h1>

      <!-- Formulaire d'ajout / modification -->
      <div class="card mb-4 shadow-sm">
        <div class="card-header">
          <h6 id="formTitle" class="m-0 font-weight-bold text-primary">Ajouter une Séance de Cours</h6>
        </div>
        <div class="card-body">
          <form id="planningForm" onsubmit="return false;">
            <input type="hidden" id="id_planning" />

            <div class="row g-3">
              <!-- Sélection du site -->
              <div class="col-12 col-md-4">
                <label for="id_site" class="form-label">Site</label>
                <select class="form-select" id="id_site" required>
                  <option value="">-- Sélectionnez un site --</option>
                </select>
              </div>
              <!-- Sélection de la salle (dépend du site) -->
              <div class="col-12 col-md-4">
                <label for="id_salle" class="form-label">Salle</label>
                <select class="form-select" id="id_salle" required disabled>
                  <option value="">-- Sélectionnez d'abord un site --</option>
                </select>
              </div>
              <!-- Sélection du cours (dépend du site) -->
              <div class="col-12 col-md-4">
                <label for="id_cours" class="form-label">Cours</label>
                <select class="form-select" id="id_cours" required disabled>
                  <option value="">-- Sélectionnez d'abord un site --</option>
                </select>
              </div>
              <!-- Sélection du groupe (dépend du site) -->
              <div class="col-12 col-md-4">
                <label for="id_groupe" class="form-label">Groupe</label>
                <select class="form-select" id="id_groupe" required disabled>
                  <option value="">-- Sélectionnez d'abord un site --</option>
                </select>
              </div>
              <!-- Date/Heure début -->
              <div class="col-12 col-md-4">
                <label for="date_heure_debut" class="form-label">Date & Heure de Début</label>
                <input type="datetime-local" class="form-control" id="date_heure_debut" required />
              </div>
              <!-- Date/Heure fin -->
              <div class="col-12 col-md-4" id="finContainer">
                <label for="date_heure_fin" class="form-label">Date & Heure de Fin</label>
                <input type="datetime-local" class="form-control" id="date_heure_fin" required />
              </div>
              <!-- Durée personnalisée (option) -->
              <div class="col-12 col-md-4">
                <div class="form-check mt-4">
                  <input class="form-check-input" type="checkbox" id="customDuration" />
                  <label class="form-check-label" for="customDuration">Durée personnalisée</label>
                </div>
                <div class="mt-2" id="durationContainer" style="display: none;">
                  <label for="duration" class="form-label">Durée (heures)</label>
                  <input type="number" class="form-control" id="duration" value="1" min="1" />
                </div>
              </div>
              <!-- Année académique (Select) -->
              <div class="col-12 col-md-6">
                <label for="id_annee" class="form-label">Année Académique</label>
                <select class="form-select" id="id_annee" required>
                  <option value="">-- Sélectionnez une année académique --</option>
                </select>
              </div>
              <!-- Répétition hebdomadaire -->
              <div class="col-12 col-md-6">
                <div class="form-check mt-4">
                  <input class="form-check-input" type="checkbox" id="repeatWeekly" />
                  <label class="form-check-label" for="repeatWeekly">Répéter chaque semaine</label>
                </div>
                <div class="mt-2" id="repeatContainer" style="display: none;">
                  <label for="repeatEndDate" class="form-label">Jusqu'à la date :</label>
                  <input type="date" class="form-control" id="repeatEndDate" />
                  <small class="text-muted">Ex: 2025-06-30</small>
                </div>
              </div>
              <!-- Sélection du matériel mobile -->
              <div class="col-12 col-md-4">
                <label for="mobile_materiel" class="form-label">Matériel Mobile (optionnel)</label>
                <select class="form-select" id="mobile_materiel">
                  <option value="">-- Sélectionnez un matériel mobile --</option>
                </select>
              </div>
            </div>

            <div class="mt-3 d-flex flex-wrap gap-2">
              <button type="submit" class="btn btn-primary" id="btnSubmit">Enregistrer</button>
              <button type="button" class="btn btn-secondary" id="btnCancel" style="display: none;">Annuler</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Tableau listant les séances planifiées -->
      <div class="card shadow mb-4">
        <div class="card-header">
          <h6 class="m-0 font-weight-bold text-primary">Liste des Séances Planifiées</h6>
        </div>
        <div class="card-body">
          <div id="alertMsg" class="alert" style="display: none;"></div>
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTablePlanning" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Site</th>
                  <th>Salle</th>
                  <th>Cours</th>
                  <th>Groupe</th>
                  <th>Début</th>
                  <th>Fin</th>
                  <th>Année</th>
                  <th>Matériel Mobile</th>
                  <th style="min-width: 150px;">Actions</th>
                </tr>
              </thead>
              <tbody id="planningBody"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div><!-- Fin .container-fluid -->
  </div><!-- Fin #content -->
</div><!-- Fin #content-wrapper -->

<?php include __DIR__ . '/../../../../frontend/components/footer.php'; ?>

<script>
// Mappings pour associer les identifiants aux noms
let sitesMap           = {};
let sallesMap          = {};
let coursMap           = {};
let groupesMap         = {};
let mobileMaterielMap  = {};
let anneesMap          = {};

// Au chargement
document.addEventListener('DOMContentLoaded', () => {
  loadSites();
  loadAnnees(); // Charge la liste des années académiques
  loadPlanning();
  loadMobileMateriel(null);

  document.getElementById('id_site').addEventListener('change', onSiteChange);
  document.getElementById('planningForm').addEventListener('submit', handlePlanningFormSubmit);
  document.getElementById('btnCancel').addEventListener('click', resetPlanningForm);

  document.getElementById('repeatWeekly').addEventListener('change', () => {
    document.getElementById('repeatContainer').style.display 
      = document.getElementById('repeatWeekly').checked ? 'block' : 'none';
  });

  document.getElementById('customDuration').addEventListener('change', () => {
    const show = document.getElementById('customDuration').checked;
    document.getElementById('durationContainer').style.display = show ? 'block' : 'none';
    if (show) {
      const debut = document.getElementById('date_heure_debut').value;
      if (debut) updateFinFromDuration();
    }
  });
  document.getElementById('duration').addEventListener('input', updateFinFromDuration);
  document.getElementById('date_heure_debut').addEventListener('change', () => {
    if (document.getElementById('customDuration').checked) updateFinFromDuration();
  });

  setTimeout(() => {
    $('#dataTablePlanning').DataTable({
      responsive: true,
      pageLength: 10,
      language: { url: '../../../../frontend/assets/vendor/datatables/French.json' },
      order: [[0, 'desc']]
    });
  }, 500);
});

// Charger les sites
async function loadSites() {
  try {
    const response = await fetch('../../../routes/admin-api.php?entity=sites&action=list');
    const data = await response.json();
    const selectSite = document.getElementById('id_site');
    selectSite.innerHTML = '<option value="">-- Sélectionnez un site --</option>';
    data.forEach(site => {
      sitesMap[site.id_site] = site.nom;
      const opt = document.createElement('option');
      opt.value = site.id_site;
      opt.textContent = site.nom + ' (' + site.nom_universite + ')';
      selectSite.appendChild(opt);
    });
  } catch (err) {
    console.error("Erreur chargement sites:", err);
  }
}

// Charger les années académiques
async function loadAnnees() {
  try {
    const response = await fetch('../../../routes/admin-api.php?entity=annees&action=list');
    const data = await response.json();
    const selectAnnee = document.getElementById('id_annee');
    selectAnnee.innerHTML = '<option value="">-- Sélectionnez une année académique --</option>';
    data.forEach(annee => {
      anneesMap[annee.id_annee] = annee.libelle;
      const opt = document.createElement('option');
      opt.value = annee.id_annee;
      opt.textContent = annee.libelle;
      selectAnnee.appendChild(opt);
    });
  } catch (err) {
    console.error("Erreur chargement années académiques:", err);
  }
}

// Charger le matériel mobile d'un site
async function loadMobileMateriel(siteId) {
  const select = document.getElementById('mobile_materiel');
  if (!siteId) {
    select.innerHTML = '<option value="">-- Sélectionnez un matériel mobile --</option>';
    select.disabled = true;
    return;
  }
  try {
    const response = await fetch(`../../../routes/admin-api.php?entity=materiels&action=listMobileBySite&siteId=${siteId}`);
    const data = await response.json();
    select.innerHTML = '<option value="">-- Sélectionnez un matériel mobile --</option>';
    if (data.length === 0) {
      select.disabled = true;
    } else {
      select.disabled = false;
      data.forEach(mat => {
        mobileMaterielMap[mat.id_materiel] = mat.type_materiel;
        const opt = document.createElement('option');
        opt.value = mat.id_materiel;
        opt.textContent = mat.type_materiel;
        select.appendChild(opt);
      });
    }
  } catch (err) {
    console.error("Erreur chargement matériel mobile:", err);
    select.innerHTML = '<option value="">-- Sélectionnez un matériel mobile --</option>';
    select.disabled = true;
  }
}

// Lorsqu'on change de site, charger salles, cours, groupes, et matériel mobile
async function onSiteChange() {
  const siteId = document.getElementById('id_site').value;
  if (!siteId) {
    document.getElementById('id_salle').innerHTML  = '<option value="">-- Sélectionnez d\'abord un site --</option>';
    document.getElementById('id_cours').innerHTML  = '<option value="">-- Sélectionnez d\'abord un site --</option>';
    document.getElementById('id_groupe').innerHTML = '<option value="">-- Sélectionnez d\'abord un site --</option>';
    document.getElementById('id_salle').disabled   = true;
    document.getElementById('id_cours').disabled   = true;
    document.getElementById('id_groupe').disabled  = true;
    loadMobileMateriel(null);
    return;
  }
  try {
    await loadSallesBySite(siteId);
    await loadCoursBySite(siteId);
    await loadGroupesBySite(siteId);
    await loadMobileMateriel(siteId);
  } catch (err) {
    console.error(err);
  }
}

// Charger les salles pour un site
async function loadSallesBySite(siteId) {
  try {
    const response = await fetch(`../../../routes/admin-api.php?entity=salles&action=listBySite&siteId=${siteId}`);
    const data = await response.json();
    const selectSalle = document.getElementById('id_salle');
    selectSalle.innerHTML = '<option value="">-- Sélectionnez une salle --</option>';
    data.forEach(salle => {
      sallesMap[salle.id_salle] = salle.nom_salle;
      const opt = document.createElement('option');
      opt.value = salle.id_salle;
      opt.textContent = salle.nom_salle;
      selectSalle.appendChild(opt);
    });
    selectSalle.disabled = false;
  } catch (err) {
    console.error("Erreur chargement salles:", err);
  }
}

// Charger les cours pour un site
async function loadCoursBySite(siteId) {
  try {
    const response = await fetch(`../../../routes/admin-api.php?entity=cours&action=listBySite&siteId=${siteId}`);
    const data = await response.json();
    const selectCours = document.getElementById('id_cours');
    selectCours.innerHTML = '<option value="">-- Sélectionnez un cours --</option>';
    data.forEach(c => {
      coursMap[c.id_cours] = c.nom_cours;
      const opt = document.createElement('option');
      opt.value = c.id_cours;
      opt.textContent = c.nom_cours;
      selectCours.appendChild(opt);
    });
    selectCours.disabled = false;
  } catch (err) {
    console.error("Erreur chargement cours:", err);
  }
}

// Charger les groupes pour un site
async function loadGroupesBySite(siteId) {
  try {
    const response = await fetch(`../../../routes/admin-api.php?entity=groupes&action=listBySite&siteId=${siteId}`);
    const data = await response.json();
    const selectGroupe = document.getElementById('id_groupe');
    selectGroupe.innerHTML = '<option value="">-- Sélectionnez un groupe --</option>';
    data.forEach(g => {
      groupesMap[g.id_groupe] = g.nom_groupe;
      const opt = document.createElement('option');
      opt.value = g.id_groupe;
      opt.textContent = g.nom_groupe;
      selectGroupe.appendChild(opt);
    });
    selectGroupe.disabled = false;
  } catch (err) {
    console.error("Erreur chargement groupes:", err);
  }
}

// Charger le planning complet
async function loadPlanning() {
  try {
    const response = await fetch('../../../routes/admin-api.php?entity=planning&action=listAll');
    const data = await response.json();
    renderPlanning(data);
  } catch (err) {
    console.error("Erreur chargement planning:", err);
    showPlanningMessage("Erreur lors du chargement du planning.", 'danger');
  }
}

// Affichage des lignes de planning
function renderPlanning(data) {
  const tbody = document.getElementById('planningBody');
  tbody.innerHTML = '';
  if (!Array.isArray(data) || data.length === 0) {
    tbody.innerHTML = '<tr><td colspan="10" class="text-center">Aucune séance planifiée.</td></tr>';
    return;
  }
  const now = new Date();
  data.forEach(item => {
    const nomSite   = item.site_name || 'N/A';
    const nomSalle  = item.nom_salle || 'N/A';
    const nomCours  = item.nom_cours || 'N/A';
    const nomGroupe = item.nom_groupe || 'N/A';
    const materiels = item.materiels || '-';
    const anneeLabel = anneesMap[item.id_annee] || sanitize(item.id_annee);

    const row = document.createElement('tr');
    const startDate = new Date(item.date_heure_debut.replace(' ', 'T'));
    let disableEdit = (startDate <= now);

    row.innerHTML = `
      <td>${item.id_planning}</td>
      <td>${sanitize(nomSite)}</td>
      <td>${sanitize(nomSalle)}</td>
      <td>${sanitize(nomCours)}</td>
      <td>${sanitize(nomGroupe)}</td>
      <td>${item.date_heure_debut}</td>
      <td>${item.date_heure_fin}</td>
      <td>${sanitize(anneeLabel)}</td>
      <td>${sanitize(materiels)}</td>
      <td>
        <button class="btn btn-warning btn-sm me-1" 
          onclick="editPlanning(
            ${item.id_planning}, 
            ${item.id_site ?? 'null'}, 
            ${item.id_salle}, 
            ${item.id_cours}, 
            ${item.id_groupe}, 
            '${item.date_heure_debut.replace(' ', 'T')}', 
            '${item.date_heure_fin.replace(' ', 'T')}', 
            '${sanitize(item.id_annee)}'
          )"
          ${disableEdit ? 'disabled' : ''}>
          <i class="fas fa-edit"></i> Modifier
        </button>
        <button class="btn btn-danger btn-sm" onclick="deletePlanning(${item.id_planning})">
          <i class="fas fa-trash"></i> Supprimer
        </button>
      </td>
    `;
    tbody.appendChild(row);
  });
}

// Préparer le formulaire pour une mise à jour
async function editPlanning(id, siteId, salle, cours, groupe, debut, fin, id_annee) {
  document.getElementById('id_planning').value = id;
  document.getElementById('id_site').value = siteId;
  await onSiteChange();
  document.getElementById('id_salle').value  = salle;
  document.getElementById('id_cours').value  = cours;
  document.getElementById('id_groupe').value = groupe;
  document.getElementById('date_heure_debut').value = debut;
  document.getElementById('date_heure_fin').value   = fin;
  document.getElementById('id_annee').value = id_annee;
  
  document.getElementById('customDuration').checked = false;
  document.getElementById('durationContainer').style.display = 'none';

  document.getElementById('btnSubmit').textContent = "Enregistrer";
  document.getElementById('btnCancel').style.display = "inline-block";
  document.getElementById('formTitle').textContent = "Modifier la Séance";
}

// Supprimer une séance
function deletePlanning(id) {
  if (!confirm("Voulez-vous supprimer ce créneau ?")) return;
  fetch(`../../../routes/admin-api.php?entity=planning&action=delete&id=${id}`, {
    method: 'DELETE'
  })
  .then(response => response.json())
  .then(resp => {
    if (resp.message) {
      showPlanningMessage(resp.message, 'success');
      loadPlanning();
    } else {
      showPlanningMessage(resp.error || "Erreur lors de la suppression.", 'danger');
    }
  })
  .catch(err => {
    console.error(err);
    showPlanningMessage("Erreur lors de la requête DELETE.", 'danger');
  });
}

// Mise à jour de la date de fin en fonction de la durée personnalisée
function updateFinFromDuration() {
  const debutStr = document.getElementById('date_heure_debut').value;
  const duration = parseInt(document.getElementById('duration').value, 10);
  if (!debutStr || isNaN(duration)) return;
  const debut = new Date(debutStr);
  const fin   = new Date(debut.getTime() + duration * 3600 * 1000);
  const formattedFin = fin.toISOString().slice(0,16);
  document.getElementById('date_heure_fin').value = formattedFin;
}

// Gestion de la soumission du formulaire (création/mise à jour)
function handlePlanningFormSubmit() {
  const id_planning = document.getElementById('id_planning').value;
  const id_site     = document.getElementById('id_site').value;
  const id_salle    = document.getElementById('id_salle').value;
  const id_cours    = document.getElementById('id_cours').value;
  const id_groupe   = document.getElementById('id_groupe').value;
  const dateDebut   = document.getElementById('date_heure_debut').value;
  let dateFin       = document.getElementById('date_heure_fin').value;
  const id_annee    = document.getElementById('id_annee').value.trim();

  if (!id_site || !id_salle || !id_cours || !id_groupe || !dateDebut || !dateFin || !id_annee) {
    showPlanningMessage("Tous les champs sont requis.", 'danger');
    return;
  }
  if (new Date(dateDebut) >= new Date(dateFin)) {
    showPlanningMessage("La date de début doit être antérieure à la date de fin.", 'danger');
    return;
  }
  if (document.getElementById('customDuration').checked) {
    updateFinFromDuration();
    dateFin = document.getElementById('date_heure_fin').value;
  }
  const payload = {
    id_salle,
    id_cours,
    id_groupe,
    date_heure_debut: dateDebut.replace('T',' '),
    date_heure_fin:   dateFin.replace('T',' '),
    id_annee: parseInt(id_annee, 10)
  };

  const mobileSelect = document.getElementById('mobile_materiel');
  const selectedMateriels = Array.from(mobileSelect.selectedOptions).map(opt => parseInt(opt.value, 10));
  if (selectedMateriels.length > 0) {
    payload.materiel_mobile = selectedMateriels;
  }

  const repeatWeekly = document.getElementById('repeatWeekly').checked;
  const repeatEnd    = document.getElementById('repeatEndDate').value;

  if (id_planning) {
    fetch(`../../../routes/admin-api.php?entity=planning&action=update&id=${id_planning}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(resp => {
      if (resp.errorCode) {
        handlePlanningErrorCode(resp.errorCode, resp.error);
      } else if (resp.message) {
        showPlanningMessage(resp.message, 'success');
        resetPlanningForm();
        loadPlanning();
      } else {
        showPlanningMessage(resp.error || "Erreur lors de la mise à jour.", 'danger');
      }
    })
    .catch(err => {
      console.error(err);
      showPlanningMessage("Erreur lors de la requête PUT.", 'danger');
    });
  } else {
    if (!repeatWeekly) {
      createSinglePlanning(payload);
    } else {
      if (!repeatEnd) {
        showPlanningMessage("Indiquez la date de fin de répétition.", 'danger');
        return;
      }
      createWeeklyRecurrence(payload, repeatEnd);
    }
  }
}

function createSinglePlanning(plData) {
  fetch('../../../routes/admin-api.php?entity=planning&action=create', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(plData)
  })
  .then(response => response.json())
  .then(resp => {
    if (resp.errorCode) {
      handlePlanningErrorCode(resp.errorCode, resp.error);
    } else if (resp.message) {
      showPlanningMessage(resp.message, 'success');
      resetPlanningForm();
      loadPlanning();
    } else {
      showPlanningMessage(resp.error || "Erreur lors de la création.", 'danger');
    }
  })
  .catch(err => {
    console.error(err);
    showPlanningMessage("Erreur lors de la requête POST.", 'danger');
  });
}

function createWeeklyRecurrence(plData, repeatEndDate) {
  const startDateTime = new Date(plData.date_heure_debut);
  const endDateTime   = new Date(plData.date_heure_fin);
  const endRecurDate  = new Date(repeatEndDate);
  const oneWeekMs     = 7 * 24 * 60 * 60 * 1000;
  let currentStart = new Date(startDateTime);
  let currentEnd   = new Date(endDateTime);
  const promises   = [];

  while (currentStart <= endRecurDate) {
    const occPayload = { ...plData };
    occPayload.date_heure_debut = formatDateTime(currentStart);
    occPayload.date_heure_fin   = formatDateTime(currentEnd);
    const p = fetch('../../../routes/admin-api.php?entity=planning&action=create', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(occPayload)
    }).then(r => r.json());
    promises.push(p);
    currentStart.setTime(currentStart.getTime() + oneWeekMs);
    currentEnd.setTime(currentEnd.getTime() + oneWeekMs);
  }

  Promise.all(promises)
  .then(results => {
    let hasError = false;
    results.forEach(r => {
      if (r.errorCode || r.error) {
        hasError = true;
        console.warn("Erreur sur une occurrence:", r.errorCode || r.error);
      }
    });
    if (!hasError) {
      showPlanningMessage("Création récurrente réussie.", 'success');
    } else {
      showPlanningMessage("Certaines occurrences ont échoué. Voir console.", 'danger');
    }
    resetPlanningForm();
    loadPlanning();
  })
  .catch(err => {
    console.error(err);
    showPlanningMessage("Erreur lors de la création récurrente.", 'danger');
  });
}

function formatDateTime(d) {
  const y   = d.getFullYear();
  const m   = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  const hh  = String(d.getHours()).padStart(2, '0');
  const mm  = String(d.getMinutes()).padStart(2, '0');
  return `${y}-${m}-${day} ${hh}:${mm}:00`;
}

function handlePlanningErrorCode(code, message) {
  switch (code) {
    case 'CONFLICT_SALLE':
      showPlanningMessage("Cette salle est déjà occupée sur ce créneau !", 'danger');
      break;
    case 'CONFLICT_GROUPE':
      showPlanningMessage("Ce groupe a déjà un autre cours sur ce créneau !", 'danger');
      break;
    case 'TRAVEL_TIME':
      showPlanningMessage("Pas assez de temps pour déplacer le groupe entre 2 sites !", 'danger');
      break;
    case 'PAST_DATE':
      showPlanningMessage("Impossible de modifier un horaire déjà passé.", 'danger');
      break;
    default:
      showPlanningMessage(message || "Erreur de planning inconnue.", 'danger');
  }
}

function resetPlanningForm() {
  document.getElementById('id_planning').value = '';
  document.getElementById('id_salle').innerHTML  = '<option value="">-- Sélectionnez une salle --</option>';
  document.getElementById('id_cours').innerHTML  = '<option value="">-- Sélectionnez un cours --</option>';
  document.getElementById('id_groupe').innerHTML = '<option value="">-- Sélectionnez un groupe --</option>';
  document.getElementById('id_salle').disabled   = true;
  document.getElementById('id_cours').disabled   = true;
  document.getElementById('id_groupe').disabled  = true;
  document.getElementById('date_heure_debut').value = '';
  document.getElementById('date_heure_fin').value   = '';
  document.getElementById('id_annee').value = '';
  document.getElementById('repeatWeekly').checked    = false;
  document.getElementById('repeatEndDate').value     = '';
  document.getElementById('repeatContainer').style.display = 'none';
  document.getElementById('customDuration').checked  = false;
  document.getElementById('durationContainer').style.display = 'none';
  document.getElementById('mobile_materiel').selectedIndex = -1;
  document.getElementById('formTitle').textContent = "Ajouter une Séance de Cours";
  document.getElementById('btnSubmit').textContent = "Enregistrer";
  document.getElementById('btnCancel').style.display = "none";
}

function showPlanningMessage(msg, type) {
  const alertMsg = document.getElementById('alertMsg');
  alertMsg.textContent = msg;
  alertMsg.className   = `alert alert-${type}`;
  alertMsg.style.display = 'block';
  setTimeout(() => {
    alertMsg.style.display = 'none';
  }, 3000);
}

function sanitize(str) {
  if (typeof str !== 'string') return str;
  return str.replace(/[&<>"'`]/g, m => {
    const map = {
      '&': '&amp;', '<': '&lt;', '>': '&gt;',
      '"': '&quot;', "'": '&#39;', '`': '&#96;'
    };
    return map[m];
  });
}
</script>
