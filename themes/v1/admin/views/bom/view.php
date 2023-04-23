<style>
    .md-card-toolbar-actions ul {
        margin-right:10px;
    }
    .md-card-toolbar-actions ul li i {
        font-size: 14px;
    }
</style>
<div id="page_content">
    <div id="page_content_inner">
    <div class="md-card">
            <div class="md-card-toolbar">
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate">&#xE5D0;</i>
                </div>
            </div>
            <div class="md-card-content" >
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                            <span class="uk-text-muted uk-text-small uk-text-italic"><b>Detail:</b></span>
                            <address>
                                <p><b>ID:</b> <?php echo $bom->id ?></p>
                                <p><b>Date:</b> <?php echo $bom->date ?></p>
                                <p><b>Product ID:</b> <?php echo $bom->product_id ?></p>
                                <p><b>Product Name:</b> <?php echo $bom->product_name ?></p>
                                <!-- <p><b>Created By:</b> <?php echo $bom->created_by ?></p> -->
                            </address>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                        </div>
                    </div>
                </div>
                <h4 class="table_heading" >Items List</h4>
                <div class="dt_colVis_buttons"></div>
                <table id="itemsTable" class="uk-table">
                    <thead>
                        <tr>
                            <th>Material ID</th>
                            <th>Material Name</th>
                            <th>Barcode</th>
                            <th>Unit Cost</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($items as $item){
                                ?>
                                <tr>
                                    <td><?php echo $item->material_id ?></td>
                                    <td><?php echo $item->product_name ?></td>
                                    <td><?php echo $item->product_code ?></td>
                                    <td><?php echo $item->rate ?></td>
                                    <td><?php echo $item->quantity ?></td>
                                    <td><?php echo $item->total ?></td>
                                </tr>
                                
                                <?php
                            }
                        
                        ?>
                    </tbody>
                    <thead>
                        <tr>
                            <th colspan="5" style="text-align:right;padding: 14px 10px;">Total Material Cost</th>
                            <th  style="padding: 14px 10px;" ><?php echo $bom->material_cost ?></th>
                        </tr>
                    </thead>
                </table>
                <div style="clear:both"></div>
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
    data['id'] = '<?php echo $bom->id; ?>';
    // New Items

</script>



