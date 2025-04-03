<?php
// emploi-du-temps.php
$group_id = isset($_GET['group_id']) ? (int) $_GET['group_id'] : 0;
if ($group_id <= 0) {
    die("Identifiant de groupe invalide.");
}

$api_url = "http://127.0.0.1/gestion-horaire/backend/routes/public-api.php?action=emploi_du_temps&group_id=$group_id";
$eventsJson = @file_get_contents($api_url);
if ($eventsJson === false) {
    $eventsJson = '[]';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Emploi du Temps | Projet SGBD</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FullCalendar CSS -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
  <style>
    body { 
      font-family: 'Nunito', sans-serif; 
      background: #f8f9fc; 
    }
    .calendar-container { 
      max-width: 1100px; 
      margin: auto; 
    }
    .sidebar-info { 
      background-color: #fff; 
      border-radius: 8px; 
      padding: 15px; 
      margin-bottom: 1rem; 
      box-shadow: 0 2px 6px rgba(0,0,0,0.1); 
    }
    .modal-body p { 
      margin-bottom: 5px; 
    }
    .filter-container { 
      display: flex; 
      justify-content: center; 
      align-items: center;
      gap: 15px; 
      margin-bottom: 20px; 
      flex-wrap: nowrap;
    }
    .filter-container select { 
      min-width: 200px; 
    }
    .btn-views .btn { 
      margin-right: 5px; 
    }
    .fc-event { 
      cursor: pointer; 
    }
    .fc-event, .fc-event-dot { 
      font-size: 0.85rem; 
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
    <div class="container">
      <h1 class="navbar-brand">Projet-SGBD | Emploi du Temps</h1>
      <a href="../../../../frontend/index.html" class="btn btn-danger">Changer de Groupe</a>
    </div>
  </nav>

  <div class="container">
    <h2 class="text-center mb-4">Emploi du Temps du Groupe #<?php echo htmlspecialchars($group_id); ?></h2>
    <div class="sidebar-info mb-4">
      <p><strong>Groupe :</strong> Affichage complet de l’emploi du temps pour le groupe sélectionné.</p>
      <p>Chaque événement représente un cours avec la salle, le site et le matériel (fixe et mobile).</p>
      <p><em>Astuce :</em> Cliquez sur un cours pour plus de détails.</p>
    </div>

    <div class="filter-container">
      <select id="siteFilter" class="form-select">
        <option value="">Tous les sites</option>
      </select>
      <select id="coursFilter" class="form-select">
        <option value="">Tous les cours</option>
      </select>
      <select id="classeFilter" class="form-select">
        <option value="">Toutes les salles</option>
      </select>
      <button class="btn btn-outline-primary" onclick="applyFilters()">Filtrer</button>
      <button class="btn btn-outline-secondary" onclick="resetFilters()">Réinitialiser</button>
    </div>

    <div class="d-flex justify-content-center mb-3 btn-views">
      <button class="btn btn-outline-secondary" onclick="setCalendarView('dayGridMonth')">Mois</button>
      <button class="btn btn-outline-secondary" onclick="setCalendarView('timeGridWeek')">Semaine</button>
      <button class="btn btn-outline-secondary" onclick="setCalendarView('timeGridDay')">Jour</button>
    </div>

    <div class="calendar-container mb-5">
      <div id="calendar"></div>
    </div>
  </div>

  <div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Détails du Cours</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p><strong>Cours :</strong> <span id="modalTitle"></span></p>
          <p><strong>Horaire :</strong> <span id="modalTime"></span></p>
          <p><strong>Salle :</strong> <span id="modalSalle"></span></p>
          <p><strong>Site :</strong> <span id="modalSite"></span></p>
          <p><strong>Matériel :</strong> <span id="modalMateriel"></span></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- FullCalendar JS -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script>
    // Transformation des données récupérées via l'API en événements FullCalendar
    let rawEvents = <?php echo $eventsJson; ?>;
    let allEvents = rawEvents.map(ev => {
      // Concatène les matériels fixes et mobiles pour l'affichage
      let materiels = "";
      if (ev.materiels_fixes && ev.materiels_mobiles) {
          materiels = ev.materiels_fixes + ", " + ev.materiels_mobiles;
      } else if (ev.materiels_fixes) {
          materiels = ev.materiels_fixes;
      } else if (ev.materiels_mobiles) {
          materiels = ev.materiels_mobiles;
      } else {
          materiels = "N/A";
      }
      let color = getColorByCourseId(ev.id_cours || 0);
      return {
        title: ev.nom_cours,
        start: ev.date_heure_debut,
        end: ev.date_heure_fin,
        backgroundColor: color,
        borderColor: color,
        extendedProps: {
          salle: ev.nom_salle,
          site: ev.site_name,
          materiel: materiels,
          course_id: ev.id_cours,
          // Ajout des heures d'ouverture et fermeture du site
          heure_ouverture: ev.heure_ouverture,
          heure_fermeture: ev.heure_fermeture
        }
      };
    });
    
    // Détermine dynamiquement les plages horaires à afficher
    let defaultSlotMinTime = "08:00:00";
    let defaultSlotMaxTime = "22:00:00";
    if(allEvents.length > 0 && allEvents[0].extendedProps.heure_ouverture && allEvents[0].extendedProps.heure_fermeture) {
      defaultSlotMinTime = allEvents[0].extendedProps.heure_ouverture;
      defaultSlotMaxTime = allEvents[0].extendedProps.heure_fermeture;
    }

    let calendar;
    document.addEventListener('DOMContentLoaded', function() {
      const calendarEl = document.getElementById('calendar');
      calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: ''
        },
        // Utilisation des heures d'ouverture/fermeture du site
        slotMinTime: defaultSlotMinTime,
        slotMaxTime: defaultSlotMaxTime,
        weekends: true,
        firstDay: 1,
        locale: 'fr',
        nowIndicator: true,
        events: allEvents,
        eventClick: function(info) {
          document.getElementById('modalTitle').textContent = info.event.title;
          let startStr = info.event.start ? info.event.start.toLocaleString([], { dateStyle: 'short', timeStyle: 'short' }) : '';
          let endStr = info.event.end ? info.event.end.toLocaleString([], { dateStyle: 'short', timeStyle: 'short' }) : '';
          document.getElementById('modalTime').textContent = `${startStr} - ${endStr}`;
          document.getElementById('modalSalle').textContent = info.event.extendedProps.salle || "Non spécifiée";
          document.getElementById('modalSite').textContent = info.event.extendedProps.site || "Non spécifié";
          document.getElementById('modalMateriel').textContent = info.event.extendedProps.materiel || "N/A";
          new bootstrap.Modal(document.getElementById('eventModal')).show();
        }
      });
      calendar.render();
      loadFilters();
    });

    function loadFilters() {
      fetch(`http://127.0.0.1/gestion-horaire/backend/routes/public-api.php?action=filters&group_id=<?php echo $group_id; ?>`)
        .then(response => response.json())
        .then(data => {
          const siteSelect = document.getElementById('siteFilter');
          const coursSelect = document.getElementById('coursFilter');
          const classeSelect = document.getElementById('classeFilter');
          
          siteSelect.innerHTML = '<option value="">Tous les sites</option>';
          data.sites.forEach(item => {
            const option = document.createElement('option');
            option.value = item.nom;
            option.textContent = item.nom;
            siteSelect.appendChild(option);
          });
          
          coursSelect.innerHTML = '<option value="">Tous les cours</option>';
          data.courses.forEach(item => {
            const option = document.createElement('option');
            option.value = item.nom_cours;
            option.textContent = item.nom_cours;
            coursSelect.appendChild(option);
          });
          
          classeSelect.innerHTML = '<option value="">Toutes les salles</option>';
          data.classes.forEach(item => {
            const option = document.createElement('option');
            option.value = item.nom_salle;
            option.textContent = item.nom_salle;
            classeSelect.appendChild(option);
          });
        })
        .catch(error => console.error("Erreur lors du chargement des filtres:", error));
    }

    function applyFilters() {
      const siteVal = document.getElementById('siteFilter').value;
      const coursVal = document.getElementById('coursFilter').value;
      const classeVal = document.getElementById('classeFilter').value;
      const filteredEvents = allEvents.filter(ev => {
        let match = true;
        if (siteVal && ev.extendedProps.site !== siteVal) match = false;
        if (coursVal && ev.title !== coursVal) match = false;
        if (classeVal && ev.extendedProps.salle !== classeVal) match = false;
        return match;
      });
      calendar.removeAllEvents();
      calendar.addEventSource(filteredEvents);
    }

    function resetFilters() {
      document.getElementById('siteFilter').value = "";
      document.getElementById('coursFilter').value = "";
      document.getElementById('classeFilter').value = "";
      calendar.removeAllEvents();
      calendar.addEventSource(allEvents);
    }

    function setCalendarView(view) {
      if (calendar) {
        calendar.changeView(view);
      }
    }

    function getColorByCourseId(courseId) {
      const palette = ['#1e90ff', '#28a745', '#ff5722', '#6f42c1', '#e83e8c', '#fd7e14', '#20c997', '#17a2b8'];
      return palette[ courseId % palette.length ];
    }
  </script>
</body>
</html>
