<style>
    .uk-open>.uk-dropdown, .uk-open>.uk-dropdown-blank{

    }
</style>
<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Batch Wise Report</h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                </div>
            </div>
            <div class="md-card-content">
                <div class="dt_colVis_buttons"></div>
                <table id="dt_tableExport" class="uk-table">
                    <thead>
                        <tr>
                            <th>Purchase Date</th>
                            <th>Product ID</th>
                            <th>Name</th>
                            <th>MRP</th>
                            <th>Price excluding Tax</th>
                            <th>Selling 1</th>
                            <th>Selling 2</th>
                            <th>Selling 3</th>
                            <th>Tax Rate Value</th>
                            <th>Quantity Balance</th>
                            <th>Expiry</th>
                            <th>Batch</th>
                            <th>Sales Incentive</th>
                            <th>Trade Discount</th>
                            <th>Consumer Discount</th>
                            <th>Fed Tax</th>
                            <th>Company</th>
                            <th>Tax Type</th>
                            <th>Warehouse ID</th>
                            <th>Warehouse Name</th>
                            <th>Carton Size</th>
                            <th>Company Code</th>
                            <th>Category</th>
                            <th>Sub Category</th>
                            <th>Brand</th>
                            <th>Group ID</th>
                            <th>Group Name</th>
                            <th>Product Status</th>
                            <th>Batch Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php
                                foreach($rows as $row){
                                    ?>
                                    <tr>
                                        <td><?php echo $row->purchase_date?></td>
                                        <td><?php echo $row->product_id?></td>
                                        <td><?php echo $row->product_name?></td>
                                        <td><?php echo $row->mrp?></td>
                                        <td><?php echo $row->net_unit_cost?></td>
                                        <td><?php echo $row->price?></td>
                                        <td><?php echo $row->dropship?></td>
                                        <td><?php echo $row->crossdock?></td>
                                        <td><?php echo decimalallow($row->tax_rate_value) ?></td>
                                        <td><?php echo $row->quantity_balance?></td>
                                        <td><?php echo $row->expiry?></td>
                                        <td><?php echo $row->batch?></td>
                                        <td><?php echo $row->discount_one?></td>
                                        <td><?php echo $row->discount_two?></td>
                                        <td><?php echo $row->discount_three?></td>
                                        <td><?php echo $row->fed_tax?></td>
                                        <td><?php echo $row->company?></td>
                                        <td><?php echo $row->Remarks?></td>
                                        <td><?php echo $row->warehouse_id?></td>
                                        <td><?php echo $row->warehousename?></td>
                                        <td><?php echo $row->carton_size?></td>
                                        <td><?php echo $row->company_code?></td>
                                        <td><?php echo $row->category?></td>
                                        <td><?php echo $row->subcategory?></td>
                                        <td><?php echo $row->brand_name?></td>
                                        <?php
                                            if($row->product_group_id == 0){
                                                ?>
                                                <td>Unknown Group</td>
                                                <td>Unknown Group</td>
                                                <?php
                                            }
                                            else{
                                                ?>
                                                <td><?php echo $row->product_group_id?></td>
                                                <td><?php echo $row->product_group_name?></td>
                                                <?php
                                            }
                                            if($row->product_status == 1){
                                                ?>
                                                <td>Active</td>
                                                <?php
                                            }
                                            else{
                                                ?>
                                                <td>Deactive</td>
                                                <?php
                                            }
                                        ?>
                                        <td><?php echo $row->data_type?></td>
                                        <td>
                                            <button class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light md-btn-mini priceBtn" data-purchaseitemid="<?php echo $row->piid; ?>"  data-selling1="<?php echo $row->price; ?>"  data-selling2="<?php echo $row->dropship; ?>"  data-selling3="<?php echo $row->crossdock; ?>"  data-mrp="<?php echo $row->mrp; ?>" >Change Price and Tax</button>
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
<div class="uk-modal" id="modal_change">
    <?php
        $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'changeForm');
        echo admin_form_open_multipart("#", $attrib);
    ?>
        <div class="uk-modal-dialog">
            <div class="uk-modal-header">
                <h3 class="uk-modal-title">Change Price</h3>
            </div>
            <div class="uk-modal-body">
                <div class="uk-grid">
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <input type="hidden" name="purchaseitemid" id="purchaseitemid" >
                            <label>Selling 1 <span class="red" >*</span></label>
                            <input type="text" name="selling1" id="selling1Txt" class="md-input md-input-success label-fixed" required value="0" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Selling 2 <span class="red" >*</span></label>
                            <input type="text" name="selling2" id="selling2Txt" class="md-input md-input-success label-fixed" required value="0" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Selling 3 <span class="red" >*</span></label>
                            <input type="text" name="selling3" id="selling3Txt" class="md-input md-input-success label-fixed" required value="0" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>MRP <span class="red" >*</span></label>
                            <input type="text" name="mrp" id="mrpTxt" class="md-input md-input-success label-fixed" required value="0" >
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-modal-footer uk-text-right">
                <button type="submit" class="md-btn md-btn-success md-btn-flat submitbtn" >Submit</button>
                <button type="button" class="md-btn md-btn-flat uk-modal-close" >Close</button>
            </div>
        </div>
    <?php echo form_close(); ?>
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

    $(document).on('click','.priceBtn',function(){
        console.log("working");
        console.log($(this).data('purchaseitemid'));
        console.log($(this).data('selling1'));
        console.log($(this).data('selling2'));
        console.log($(this).data('selling3'));
        console.log($(this).data('mrp'));
        $('#purchaseitemid').val($(this).data('purchaseitemid'));
        $('#selling1Txt').val($(this).data('selling1'));
        $('#selling2Txt').val($(this).data('selling2'));
        $('#selling3Txt').val($(this).data('selling3'));
        $('#mrpTxt').val($(this).data('mrp'));
        UIkit.modal('#modal_change').show();
    });
    $('#changeForm').submit(function(e){
        e.preventDefault();
        $('.submitbtn').prop('disabled', true);
        $.ajax({
            url: '<?php echo base_url('admin/reports/update_batch_price'); ?>',
            type: 'POST',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                var obj = jQuery.parseJSON(data);
                console.log(obj);
                if(obj.status){
                    toastr.success(obj.message);
                    location.reload();
                }
                else{
                    toastr.error(obj.message);
                }
                $('.submitbtn').prop('disabled', false);
            }
        });
    });




    $.DataTableInit2({
        selector:'#dt_tableExport',
        aaSorting: [[1, "desc"]],
        columnDefs: [
            { 
                "targets": 9,
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