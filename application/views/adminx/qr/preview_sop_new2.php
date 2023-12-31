<html>
  <head>
      <title><?php echo $nama_halaman; ?> | <?php echo APPS_NAME; ?></title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <link rel="icon" href="<?php echo base_url(); ?>files/uploads/icons/<?php echo $perusahaan->icon_name; ?>" type="image/x-icon">
      <meta name="description" content="<?php echo APPS_DESC; ?>" />
      <meta name="keywords" content="<?php echo APPS_KEYWORD; ?>" />
      <meta name="author" content="<?php echo APPS_AUTHOR; ?>" />
      <meta http-equiv="refresh" content="<?php echo APPS_REFRESH; ?>">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/css/bootstrap.min.css">
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
      <script src="<?php echo base_url(); ?>files/assets/plugins/web-pdf-viewer/js/pdfjs-viewer.js"></script>
      <link rel="stylesheet" href="<?php echo base_url(); ?>files/assets/plugins/web-pdf-viewer/css/pdfjs-viewer.css">
      <script>
      // Let's initialize the PDFjs library
      var pdfjsLib = window['pdfjs-dist/build/pdf'];
      pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.worker.min.js';
      </script>
      <style>
          .pdftoolbar, .pdftoolbar i {
              font-size: 14px;
          }
          .pdftoolbar span {
              margin-right: 0.5em;
              margin-left: 0.5em;
              width: 4em !important;
              font-size: 12px;
          }
          .pdftoolbar .btn-sm {
              padding: 0.12rem 0.25rem;

          }
          .pdftoolbar {
              width: 100%;
              height: auto;
              background: #ddd;
              z-index: 100;
          }
      </style>
  </head>
  <body>
    <div class="container">
      <div class="row h-100">
        <div class="col-md-12 text-center mt-4">
          <h5><?php echo strtoupper($nama_document); ?></h5>
          <p>Doc. No. #<?php echo $nomor_document; ?></p>
        </div>
        <div class="row pdfviewer p-0 row h-100" style="margin-left: auto; margin-right: auto;">
            <div class="pdftoolbar text-center row m-0 p-0">
                <div class="col-12 col-lg-6 my-1">
                    <button class="btn btn-secondary btn-sm btn-first" onclick="pdfViewer.first()"><i class="material-icons-outlined">skip_previous</i></button>
                    <button class="btn btn-secondary btn-sm btn-prev" onclick="pdfViewer.prev(); return false;"><i class="material-icons-outlined">navigate_before</i></button>
                    <span class="pageno"></span>
                    <button class="btn btn-secondary btn-sm btn-next" onclick="pdfViewer.next(); return false;"><i class="material-icons-outlined">navigate_next</i></button>
                    <button class="btn btn-secondary btn-sm btn-last" onclick="pdfViewer.last()"><i class="material-icons-outlined">skip_next</i></button>
                </div>
                <div class="col-12 col-lg-6 my-1">
                    <button class="btn btn-secondary btn-sm" onclick="pdfViewer.setZoom('out')"><i class="material-icons-outlined">zoom_out</i></button>
                    <span class="zoomval">100%</span>
                    <button class="btn btn-secondary btn-sm" onclick="pdfViewer.setZoom('in')"><i class="material-icons-outlined">zoom_in</i></button>
                    <button class="btn btn-secondary btn-sm ms-3" onclick="pdfViewer.setZoom('width')"><i class="material-icons-outlined">swap_horiz</i></button>
                    <button class="btn btn-secondary btn-sm" onclick="pdfViewer.setZoom('height')"><i class="material-icons-outlined">swap_vert</i></button>
                    <button class="btn btn-secondary btn-sm" onclick="pdfViewer.setZoom('fit')"><i class="material-icons-outlined">fit_screen</i></button>
                </div>
            </div>
            <div class="pdfjs-viewer h-100">
            </div>    
        </div>
      </div>
    </div>
  </body>
  <script>
    let pdfViewer = new PDFjsViewer($('.pdfjs-viewer'), {
      onZoomChange: function(zoom) {
        zoom = parseInt(zoom * 10000) / 100;
        $('.zoomval').text(zoom + '%');
      },
      onActivePageChanged: function(page, pageno) {
        $('.pageno').text(pageno + '/' + this.getPageCount());
      },
    });
    pdfViewer.loadDocument("<?php echo $file; ?>").then(function() {
        pdfViewer.setZoom('fit');
    });
  </script>
</html>