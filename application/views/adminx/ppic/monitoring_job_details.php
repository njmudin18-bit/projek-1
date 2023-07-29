<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title><?php echo $nama_halaman; ?> | <?php echo APPS_NAME; ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="description" content="<?php echo APPS_DESC; ?>" />
  <meta name="keywords" content="<?php echo APPS_KEYWORD; ?>" />
  <meta name="author" content="<?php echo APPS_AUTHOR; ?>" />
  <meta http-equiv="refresh" content="<?php echo APPS_REFRESH; ?>">

  <?php $this->load->view('adminx/components/header_css_datatable_fix_column'); ?>
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/loading.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/daterangepicker.css" />
</head>

<body>

  <div class="loader-bg">
    <div class="loader-bar"></div>
  </div>

  <div id="pcoded" class="pcoded">
    <div class="pcoded-overlay-box"></div>
    <div class="pcoded-container navbar-wrapper">

      <?php $this->load->view('adminx/components/navbar'); ?>

      <?php $this->load->view('adminx/components/navbar_chat'); ?>

      <div class="pcoded-main-container">
        <div class="pcoded-wrapper">

          <?php $this->load->view('adminx/components/sidebar'); ?>

          <div class="pcoded-content">

            <?php $this->load->view('adminx/components/breadcrumb'); ?>

            <div class="pcoded-inner-content">
              <div class="main-body">
                <div class="page-wrapper">
                  <div class="page-body">
                    <div class="row">
                      <div class="col-sm-12">
                        <div class="card">
                          <div class="card-header text-center">
                            <h5 id="test">
                              <?php //echo strtoupper($nama_halaman); 
                              ?>
                              DETAIL JOBS NO. <?php echo $no_job; ?>
                            </h5>
                          </div>
                          <div class="card-block m-t-30 m-b-30">
                            <div class="dt-responsive table-responsiveX">
                              <table id="order-table" class="table table-striped table-bordered nowrap" width="100%" border="1" cellpadding="0" cellspacing="0">
                                <thead>
                                  <tr class="">
                                    <th class="text-center bg-primary">No.</th>
                                    <th class="text-center bg-primary">Partname</th>
                                    <th class="text-center bg-primary">Unit ID</th>
                                    <th class="text-center bg-primary">Qty In</th>
                                    <th class="text-center bg-primary">Prod Scan Loc</th>
                                    <th class="text-center bg-primary">Prod Time Scanned</th>
                                    <th class="text-center bg-primary">Prod Scanned by</th>
                                    <th class="text-center bg-primary">QC Scan Loc</th>
                                    <th class="text-center bg-primary">QC Time Scanned</th>
                                    <th class="text-center bg-primary">QC Scanned by</th>
                                    <th class="text-center bg-primary">QC Status</th>
                                    <th class="text-center bg-primary">WH Scan Loc</th>
                                    <th class="text-center bg-primary">WH Time Scanned</th>
                                    <th class="text-center bg-primary">WH Scanned by</th>
                                    <th class="text-center bg-primary">Prod Barcode No.</th>
                                  </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                  <tr class="bg-info">
                                    <!-- <th class="text-center">TOTAL</th> -->
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                  </tr>
                                </tfoot>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div id="styleSelector"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL VIEW NG DETAIL -->
  <div class="modal fade" id="modal_ng" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Detail Produk NG</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="dt-responsive table-responsive">
            <table class="table table-bordered nowrap">
              <thead>
                <tr class="bg-info">
                  <th class="text-center">No</th>
                  <th class="text-center">Barcode No.</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">PIC Repair</th>
                  <th class="text-center">Tanggal</th>
                </tr>
              </thead>
              <tbody id="data_ng">

              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-outline-danger waves-effect md-trigger" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <?php $this->load->view('adminx/components/bottom_js_datatable_fix_column'); ?>
  <!-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/moment.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/daterangepicker.min.js"></script>
  <script type="text/javascript">
    $(function() {

      var start = moment().subtract(5, 'days');
      var end = moment();

      function cb(start, end) {
        var sd = start.format('YYYY-MM-DD');
        var ed = end.format('YYYY-MM-DD');

        $('#tanggal').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
        $('#start_date').val(start.format('YYYY-MM-DD'));
        $('#end_date').val(end.format('YYYY-MM-DD'));
      }

      $('#tanggal').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: {
          format: 'YYYY-MM-DD'
        },
        function(start, end, label) {
          startDate = start;
          endDate = end;
          console.log(startDate);
          console.log(endDate);
          console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        }
      }, cb);
      cb(start, end);
    });
  </script>
  <script type="text/javascript">
    $(document).ready(function() {
      table = $('#order-table').DataTable({
        dom: 'Bfrltip',
        "pageLength": 25,
        buttons: [{
            extend: 'excel',
            footer: true
          },
          //'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        "footerCallback": function(row, data, start, end, display) {
          var api = this.api(),
            data;

          // converting to interger to find total
          var intVal = function(i) {
            return typeof i === 'string' ?
              i.replace(/[\$,]/g, '') * 1 :
              typeof i === 'number' ?
              i : 0;
          };

          // computing column Total of the complete result 
          var total_qty = api
            .column(3)
            .data()
            .reduce(function(a, b) {
              return intVal(a) + intVal(b);
            }, 0);

          // Update footer by showing the total with the reference of the column index 
          $(api.column(2).footer()).html('TOTAL');
          $(api.column(3).footer()).html(formatNumber(total_qty));
        },
        scrollY: "100p%",
        scrollX: true,
        scrollCollapse: true,
        paging: true,
        fixedColumns: {
          leftColumns: 1,
          //rightColumns: 1
        },
        'processing': true,
        'serverSide': false,
        'serverMethod': 'POST',
        'ajax': {
          url: "<?php echo base_url(); ?>ppic/monitoring_job_details_list",
          type: 'POST',
          "data": function(data) {
            data.no_job = '<?php echo $no_job ?>';
            data.bulan = '<?php echo $bulan ?>';
            data.tahun = '<?php echo $tahun ?>';
          }
        },

        'aoColumns': [{
            "No.": "No.",
            "sClass": "text-right"
          },
          {
            "Partname": "Partname",
            "sClass": "text-left"
          },
          {
            "UnitID": "UnitID",
            "sClass": "text-center"
          },
          {
            "Qty In": "Qty In",
            "sClass": "text-right"
          },
          {
            "Prod Scan Loc": "Prod Scan Loc",
            "sClass": "text-center"
          },
          {
            "Prod Time Scanned": "Prod Time Scanned",
            "sClass": "text-center"
          },
          {
            "Prod Scanned by": "Prod Scanned by",
            "sClass": "text-center"
          },
          {
            "QC Scan Loc": "QC Scan Loc",
            "sClass": "text-center"
          },
          {
            "QC Time Scanned": "QC Time Scanned",
            "sClass": "text-center"
          },
          {
            "QC Scanned by": "QC Scanned by",
            "sClass": "text-center"
          },
          {
            "QC Status": "QC Status",
            "sClass": "text-center"
          },
          {
            "WH Scan Loc": "WH Scan Loc",
            "sClass": "text-center"
          },
          {
            "WH Time Scanned": "WH Time Scanned",
            "sClass": "text-center"
          },
          {
            "WH Scanned by": "WH Scanned by",
            "sClass": "text-center"
          },
          {
            "Prod Barcode No.": "Prod Barcode No.",
            "sClass": "text-left"
          }
        ],

        "columnDefs": [{
          "targets": [0], //last column
          "orderable": false, //set not orderable
          className: 'text-right'
        }, ]
      });

      function formatNumber(n) {
        return n.toLocaleString(); // or whatever you prefer here
      }
    });
  </script>
</body>

</html>