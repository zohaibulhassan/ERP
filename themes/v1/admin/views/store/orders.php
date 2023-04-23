<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Store Orders</h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                </div>
            </div>
            <div class="md-card-content">
                <div class="dt_colVis_buttons"></div>
                <table id="dt_tableExport" class="uk-table">
                    <thead>
                        <tr>
                            <th>Order No</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Fulfillment Status</th>
                            <th>Tracking No</th>
                            <th>Tracking Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($rows['orders'] as $row){
                                ?>
                                <tr>
                                    <td><?php echo $row->order_number; ?></td>
                                    <td><?php echo dateformate($row->created_at,'Y-m-d H:i:s'); ?></td>
                                    <td><?php echo $row->shipping_address->name; ?></td>
                                    <td><?php echo $row->total_price; ?></td>
                                    <td><?php echo $row->financial_status; ?></td>
                                    <td><?php echo $row->fulfillment_status; ?></td>
                                    <td><?php
                                        foreach($row->fulfillments as $key => $f){
                                            foreach($f->tracking_numbers as $num){
                                                echo $num.'<br>';
                                            }
                                        }
                                    ?></td>
                                    <td><?php
                                        foreach($row->fulfillments as $key => $f){
                                            foreach($f->tracking_numbers as $num){

                                                echo get_traking_status($num).'<br>';
                                            }
                                        }
                                    ?></td>
                                    <td>
                                        <a href="<?php echo base_url('admin/stores/order?sid='.$store->id.'&code='.$row->id) ?>" class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" >Detail</a>
                                    </td>
                                </tr>

                                
                                <?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="md-fab-wrapper md-fab-in-card" style="position: fixed;bottom: 20px;">
    <a class="md-fab md-fab-success md-fab-wave waves-effect waves-button" href="<?php echo base_url('admin/stores/add'); ?>"><i class="fa-solid fa-plus"></i></a>
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


    var data = {
        [csrfName]: csrfHash,
    };

    $.DataTableInit2({
        selector:'#dt_tableExport',
        aaSorting: [[0, "desc"]],
        columnDefs: [
            { 
                "targets": 7,
                "orderable": false
            }
        ],
        fixedColumns:   {left: 0,right: 1},
        scrollX: false
    });
</script>
<script>
    $(document).ready(function(){
        $('.select2').select2();



    });
</script>