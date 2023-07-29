<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
</head>

<body>
  <div class="container">
    <div class="row">
      <div class="col-md-12 mt-5">
        <table id="example" class="table table-striped" style="width:100%">
          <thead>
            <tr class="bg-primary text-white">
              <th>No</th>
              <th>Nama</th>
              <th>Logo</th>
              <th>Aktivasi</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
  <script>
    $(document).ready(function() {
      //$('#example').DataTable();
      table = $('#example').DataTable({
        "pagingType": "full_numbers",
        "lengthMenu": [
          [10, 25, 50, -1],
          [10, 25, 50, "All"]
        ],
        responsive: true,
        language: {
          search: "_INPUT_",
          searchPlaceholder: "Search records",
        },
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.

        // Load data for the table's content from an Ajax source
        "ajax": {
          "url": "<?php echo base_url(); ?>api/daftar_customer",
          "type": "POST",
        },

        "aoColumns": [{
            "No": "No",
            "sClass": "text-right"
          },
          {
            "Nama": "Nama",
            "sClass": "text-left"
          },
          {
            "Logo": "Logo",
            "sClass": "text-left"
          },
          {
            "Aktivasi": "Aktivasi",
            "sClass": "text-left"
          }
        ],

        //Set column definition initialisation properties.
        "columnDefs": [{
          "targets": [0], //last column
          "orderable": false, //set not orderable
          className: 'text-right'
        }, ]
      });
    });
  </script>
</body>

</html>