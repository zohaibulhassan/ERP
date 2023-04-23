<style>
    .uk-open>.uk-dropdown, .uk-open>.uk-dropdown-blank{

    }
</style>
<div id="page_content">
    <div id="page_content_inner">
    <div class="md-card">
            <div class="md-card-toolbar">
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-toggle" style="opacity: 1; transform: scale(1);"></i>
                </div>
                <h3 class="md-card-toolbar-heading-text">Filters </h3>
            </div>
            <div class="md-card-content" >
                <form action="<?php echo base_url('admin/purchases'); ?>" method="get">
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Warehouse</label>
                                <select name="warehouse" class="uk-width-1-1 select2" >
                                    <option value="all">All Warehosues</option>
                                    <?php
                                        foreach($warehouses as $w){
                                            echo '<option value="'.$w->id.'" ';
                                            if($w->id == $warehouse){
                                                echo 'selected';
                                            }
                                            echo ' >'.$w->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Supplier</label>
                                <select name="supplier" class="uk-width-1-1 select2">
                                    <option value="all">All Supplier</option>
                                    <?php
                                        foreach($suppliers as $s){
                                            echo '<option value="'.$s->id.'" ';
                                            if($s->id == $supplier){
                                                echo 'selected';
                                            }
                                            echo ' >'.$s->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4" style="padding-top: 20px;">
                            <button type="submit" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light" >Submit</button>
                            <a href="<?php echo base_url('admin/purchases'); ?>" class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light" >Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Purchases</h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                </div>
            </div>
            <div class="md-card-content">
                <div class="dt_colVis_buttons"></div>
                <table id="dt_tableExport" class="uk-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th style="width:120px" >Date</th>
                            <th>Reference No</th>
                            <th style="width:150px" >Supplier</th>
                            <th style="width:150px" >Own Company</th>
                            <th style="width:150px" >Warehosue</th>
                            <th>Grand Total</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Payment Status</th>
                            <th style="width:150px" >Created By</th>
                            <th class="dt-no-export" >Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="md-fab-wrapper md-fab-in-card" style="position: fixed;bottom: 20px;">
    <a class="md-fab md-fab-success md-fab-wave waves-effect waves-button addbtn" href="<?php echo base_url('admin/purchases/add') ?>"><i class="fa-solid fa-plus"></i></a>
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
    data['supplier'] = '<?php echo $supplier; ?>';
    data['warehouse'] = '<?php echo $warehouse; ?>';
    $.DataTableInit({
        selector:'#dt_tableExport',
        url:"<?= admin_url('purchases/get_lists'); ?>",
        data:data,
        aaSorting: [[0, "desc"]],
        columnDefs: [
            { 
                "targets": 11,
                "orderable": false
            }
        ],
        fixedColumns:   {left: 0,right: 1},
        scrollX: true
    });
</script>
<script>
    $(document).ready(function(){
        $('.select2').select2();
    });
</script>