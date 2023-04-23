<style>
    .uk-open>.uk-dropdown, .uk-open>.uk-dropdown-blank{

    }
    .dt_colVis_buttons {
        display:none;
    }
    .summarytable {}
    .summarytable table{
        width: 30%;
        float: right;
    }
    .summarytable tr{}
    .summarytable th{}
    .summarytable td{}
</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Create Purchase Orders</h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                </div>
            </div>
            <div class="md-card-content">
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'submitFrom');
                    echo admin_form_open_multipart("purchaseorder/created2", $attrib)
                    ?>
                    <input type="hidden" name="recid" value='<?= $po_re ?>'>

                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Reference No</label>
                               <input type="text" name="reference_no" id="poref" class="md-input  label-fixed" required >
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:50px">
                        <div class="dt_colVis_buttons"></div>
                        <table class="uk-table"  style="width:100%" id="dt_tableExport">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Product Barcode</th>
                                    <th>Net Unit</th>
                                    <th>MRP</th>
                                    <th>Quantity</th>
                                    <th>Batch</th>
                                    <th>Expiry</th>
                                    <th>Discount 1</th>
                                    <th>Discount 2</th>
                                    <th>Discount 3</th>
                                    <th>Discoutn</th>
                                    <th>FED Tax</th>
                                    <th>Further Tax</th>
                                    <th>Advance Income Tax</th>
                                    <th>Product Tax</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach($finaldata['pts'] as $pt){
                                    ?>
                                        <tr>
                                            <td><?= $pt['product_name'] ?></td>
                                            <td><?= $pt['product_code'] ?></td>
                                            <td><?= $pt['net_unit_cost'] ?></td>
                                            <td><?= $pt['mrp'] ?></td>
                                            <td><?= $pt['quantity'] ?></td>
                                            <td><?= $pt['batch'] ?></td>
                                            <td><?= $pt['expiry'] ?></td>
                                            <td><?= $pt['discount_one'] ?></td>
                                            <td><?= $pt['discount_two'] ?></td>
                                            <td><?= $pt['discount_three'] ?></td>
                                            <td><?= $pt['discount'] ?></td>
                                            <td><?= $pt['fed_tax'] ?></td>
                                            <td><?= $pt['further_tax'] ?></td>
                                            <td><?= $pt['adv_tax']*$pt['quantity'] ?></td>
                                            <td>(<?= $pt['tax_rate'] ?>)<br><?= $pt['item_tax']*$pt['quantity'] ?></td>
                                            <td><?= $pt['subtotal'] ?></td>
                                        </tr>
                                    <?php
                                    }
                                ?>


                            </tbody>
                        </table>
                    </div>
                    <div class="summarytable" >
                        <table class="uk-table uk-table-striped ">
                            <tbody>
                                <tr>
                                    <td style="width:50%" ><b>Total Quantity</b></td>
                                    <td style="width:50%"  id="totalitems"><?= $finaldata['p']['total_item']; ?></td>
                                </tr>
                                <tr>
                                    <td><b>Total Product Tax</b></td>
                                    <td id="totalptax"><?= $finaldata['p']['product_tax']; ?></td>
                                </tr>
                                <tr>
                                    <td><b>Net Amount</b></td>
                                    <td id="totalnetamount"><?= $finaldata['p']['grand_total']; ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <div style="clear:both" ></div>
                    </div>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-large-1-1">
                            <button class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light" id="submitbtn" type="submit" >Submit</button>
                            <button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light" type="button" >Back to PO</button>
                        </div>
                    </div>
                
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="">
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

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function(){
        $('#dt_tableExport').DataTable({
            fixedColumns:   {left: 0,right: 1},
            scrollX: true,
            searching:false,
            paging :false

        });

        // $('#submitFrom').submit(function(e){
        //     e.preventDefault();
        //     $('#submitbtn').prop('disabled', true);
        //     $.ajax({
        //         url: '<?php echo base_url('admin/purchaseorder/submit'); ?>',
        //         type: 'POST',
        //         data: new FormData(this),
        //         contentType: false,
        //         cache: false,
        //         processData: false,
        //         success: function(data) {
        //             var obj = jQuery.parseJSON(data);
        //             if(obj.status){
        //                 toastr.success(obj.message);
        //                 $('#submitFrom')[0].reset();
        //                 localStorage.removeItem("po_items");
        //                 window.location.href = "<?php echo base_url('admin/purchaseorder/view/'); ?>"+obj.purchase_id;
        //             }
        //             else{
        //                 toastr.error(obj.message);
        //             }
        //             $('#submitbtn').prop('disabled', false);
        //         }
        //     });
        // });
    });
</script>