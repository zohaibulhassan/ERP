<style>
    .uk-open>.uk-dropdown, .uk-open>.uk-dropdown-blank{

    }
</style>
<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Sale Report</h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                </div>
            </div>
            <div class="md-card-content">
                <div class="dt_colVis_buttons"></div>
                <table id="dt_tableExport" class="uk-table">
                    <thead>
                        <tr>
                        <th>Own Company</th>
                            <th>Customer NIC</th>
                            <th>Customer NTN</th>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th>P.O Number</th>
                            <th>Customer Name</th>
                            <th>E-talier Name</th>
                            <th>FBR Registeration Status</th>
                            <th>Product ID</th>
                            <th>Company Code</th>
                            <th>Barcode</th>
                            <th>Brand</th>
                            <th>HSN Code</th>
                            <th>Product Name</th>
                            <th>Carton Size</th>
                            <th>MRP</th>
                            <th>Qty Order</th>
                            <th>UOM</th>
                            <th>Carton Qty</th>
                            <th>Price Excluding Tax(TP)</th>
                            <th>Selling Price</th>
                            <th>Value Excluding Tax</th>
                            <th>Tax %</th>
                            <th>Item Tax</th>
                            <th>Advance Income Tax</th>
                            <th>Further Tax</th>
                            <th>FED Tax</th>
                            <th>Sales Value</th>
                            <th>Total Including All Taxes</th>
                            <th>Sales Incentive Value</th>
                            <th>Sales Incentive %</th>
                            <th>Trade Discount Value</th>
                            <th>Trade Discount %</th>
                            <th>Consumer Discount Value</th>
                            <th>Consumer Discount %</th>
                            <th>Total Discount</th>
                            <th>Net Amount</th>
                            <th>Expiry Date</th>
                            <th>Batch</th>
                            <th>Warehouse</th>
                            <th>Supplier Name</th>
                            <th>Remarks</th>
                            <th>M.R.P Excluding Tax</th>
                            <th>M.R.P Third Schedule</th>
                            <th>Group ID</th>
                            <th>Group Name</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- datatables -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<!-- datatables buttons-->
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/dataTables.buttons.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/custom/datatables/buttons.uikit.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/jszip/dist/jszip.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/pdfmake/build/pdfmake.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/pdfmake/build/vfs_fonts.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.colVis.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.html5.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.print.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-fixedcolumns/dataTables.fixedColumns.min.js"></script>
<!-- datatables custom integration -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/custom/datatables/datatables.uikit.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/datatable.js"></script>
<script>
    var csrfName = "<?php echo $this->security->get_csrf_token_name(); ?>",
        csrfHash = "<?php echo $this->security->get_csrf_hash(); ?>";
    var data = [];
    data[csrfName] = csrfHash;
    $.DataTableInit({
        selector:'#dt_tableExport',
        url:"<?= admin_url('reports/salesreport_ajax'); ?>",
        data:data,
        aaSorting: [[1, "desc"]],
        columnDefs: [
            { 
                "targets": 7,
                "orderable": false
            }
        ],
        fixedColumns:   {left: 0,right: 0},
        scrollX: false
    });
</script>
<script>
    $(document).ready(function(){
        $('.select2').select2();
    });
</script>