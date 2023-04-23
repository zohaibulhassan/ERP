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
                <form action="<?php echo base_url('admin/salesorders'); ?>" method="get">
                    <div class="uk-grid">
                        <div class="uk-width-large-1-3">
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
                        <div class="uk-width-large-1-3">
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


                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Customers</label>
                                <select name="customer" class="uk-width-1-1 select2">
                                    <option value="all">All Customer</option>
                                    <?php
                                        foreach($customers as $c){
                                            echo '<option value="'.$c->id.'" ';
                                            if($c->id == $customer){
                                                echo 'selected';
                                            }
                                            echo ' >'.$c->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Start Date</label>
                                <input class="md-input  label-fixed" type="text" name="start_date" data-uk-datepicker="{format:'YYYY-MM-DD'}" autocomplete="off" value="<?php echo $start_date ?>" readonly >
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Start Date</label>
                                <input class="md-input  label-fixed" type="text" name="end_date" data-uk-datepicker="{format:'YYYY-MM-DD'}" autocomplete="off" readonly value="<?php echo $end_date; ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Operation Team Status</label>
                                <select name="ostatus" class="uk-width-1-1 select2"  >
                                    <option value="all">ALL</option>
                                    <option value="pending" <?php if($ostatus == "pending"){ echo 'selected'; } ?> >Pending</option>
                                    <option value="partial dispatch" <?php if($ostatus == "partial dispatch"){ echo 'selected'; } ?> >Partial Dispatch</option>
                                    <option value="complete dispatch" <?php if($ostatus == "complete dispatch"){ echo 'selected'; } ?> >Complete Dispatch</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Account Team Status</label>
                                <select name="astatus" class="uk-width-1-1 select2"  >
                                    <option value="all">ALL</option>
                                    <option value="pending" <?php if($astatus == "pending"){ echo 'selected'; } ?> >Pending</option>
                                    <option value="partial invoiced" <?php if($astatus == "partial invoiced"){ echo 'selected'; } ?> >Partial Invoiced</option>
                                    <option value="completed invoiced" <?php if($astatus == "completed invoiced"){ echo 'selected'; } ?> >Completed Invoiced</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>SO Status</label>
                                <select name="sostatus" class="uk-width-1-1 select2"  >
                                    <option value="all">ALL</option>
                                    <option value="pending" <?php if($sostatus == "pending"){ echo 'selected'; } ?> >Pending</option>
                                    <option value="partial" <?php if($sostatus == "partial"){ echo 'selected'; } ?> >Partial</option>
                                    <option value="completed" <?php if($sostatus == "completed"){ echo 'selected'; } ?> >Completed</option>
                                    <option value="closed" <?php if($sostatus == "closed"){ echo 'selected'; } ?> >Close</option>
                                    <option value="cancel" <?php if($sostatus == "cancel"){ echo 'selected'; } ?> >Cancel</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>SO Type</label>
                                <select name="type" class="uk-width-1-1 select2"  >
                                    <option value="all">ALL</option>
                                    <option value="a" <?php if($sostatus == "a"){ echo 'selected'; } ?> >Auto SO</option>
                                    <option value="m" <?php if($sostatus == "m"){ echo 'selected'; } ?> >Menual SO</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3" style="padding-top: 20px;">
                            <button type="submit" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light" >Submit</button>
                            <a href="<?php echo base_url('admin/salesorders'); ?>" class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light" >Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Sales Orders</h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                </div>
            </div>
            <div class="md-card-content">
                <div class="dt_colVis_buttons"></div>
                <table id="dt_tableExport" class="uk-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" data-md-icheck /></th>
                            <th style="width:120px">Date</th>
                            <th>Reference No</th>
                            <th style="width:120px">PO Number</th>
                            <th style="width:150px">Supplier</th>
                            <th style="width:150px">Customer</th>
                            <th>Warehouse</th>
                            <th>Demand Quantity</th>
                            <th>Demand Value</th>
                            <th>Completed Quantity</th>
                            <th>Completed Value</th>
                            <th>Completed % Quantity</th>
                            <th>Completed % Value</th>
                            <th>Created By</th>
                            <th>Account Status</th>
                            <th>Operation Status</th>
                            <th>SO Status</th>
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
    <a class="md-fab md-fab-success md-fab-wave waves-effect waves-button addbtn" href="<?php echo base_url('admin/salesorders/add') ?>"><i class="fa-solid fa-plus"></i></a>
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
    data['warehouse'] = '<?php echo $warehouse; ?>';
    data['supplier'] = '<?php echo $supplier; ?>';
    data['customer'] = '<?php echo $customer; ?>';
    data['start_date'] = '<?php echo $start_date; ?>';
    data['end_date'] = '<?php echo $end_date; ?>';
    data['ostatus'] = '<?php echo $ostatus; ?>';
    data['astatus'] = '<?php echo $astatus; ?>';
    data['sostatus'] = '<?php echo $sostatus; ?>';
    $.DataTableInit({
        selector:'#dt_tableExport',
        url:"<?= admin_url('salesorders/get_lists'); ?>",
        data:data,
        aaSorting: [[1, "desc"]],
        columnDefs: [
            { 
                "targets": 0,
                "orderable": false
            },
            { 
                "targets": 17,
                "orderable": false
            }
        ],
        fixedColumns:   {left: 1,right: 2},
        scrollX: true
    });
</script>
<script>
    $(document).ready(function(){
        $('.select2').select2();
    });
</script>