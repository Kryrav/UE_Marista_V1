<script>
    const base_url = "<?= base_url(); ?>";
</script>

<!-- Essential javascripts -->
<script src="<?= media(); ?>/js/jquery-3.3.1.min.js"></script>
<script src="<?= media(); ?>/js/popper.min.js"></script>
<script src="<?= media(); ?>/js/bootstrap.min.js"></script>
<script src="<?= media(); ?>/js/main.js"></script>
<script src="<?= media();?>/js/fontawesome.js"></script>

<!-- Plugins -->
<script src="<?= media(); ?>/js/plugins/pace.min.js"></script>
<script src="<?= media(); ?>/js/plugins/sweetalert.min.js"></script>
<script src="<?= media();?>/js/plugins/bootstrap-select.min.js"></script>

<!-- DataTables Core -->
<script src="<?= media(); ?>/js/plugins/jquery.dataTables.min.js"></script>
<script src="<?= media(); ?>/js/plugins/dataTables.bootstrap.min.js"></script>

<!-- DataTables Buttons - TODOS LOS PLUGINS -->
<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script> 
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script> 

<!-- Scripts del proyecto -->
<script src="<?= media();?>/js/functions_admin.js"></script>
<script src="<?= media(); ?>/js/<?= $data['page_functions_js']; ?>"></script>

<!-- Chart plugins -->
<script src="<?= media(); ?>/js/plugins/chart.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>