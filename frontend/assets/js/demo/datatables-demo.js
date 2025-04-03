// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable').DataTable();
});

$(document).ready(function() {
  $('#dataTable').DataTable({
      "language": {
          "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/French.json"
      }
  });
});
