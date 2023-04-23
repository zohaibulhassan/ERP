<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD ?>
<style>
    table span.pull-right {
        padding:0 !important;
    }
</style>
<div id="ajaxCall" style="display: none;"><i class="fa fa-spinner fa-pulse"></i></div>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i>Add Receiving Items</h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form','id'=>'addrecived_form');
                    echo admin_form_open("#", $attrib)
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <!-- <div class="col-md-4">
                            <div class="form-group">
                                <label>Batch Code</label>
                                <?php echo form_input('batch_code','', 'required class="form-control" id="batch_code"'); ?>
                            </div>
                        </div> -->
                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("order_items"); ?></label>

                                <div class="controls table-controls">
                                    <table id="po_tb" class="table items table-striped table-bordered table-condensed table-hover sortable_table">
                                        <thead>
                                        <tr>
                                            <th class="col-md-2">Product Name</th>
                                            <th class="col-md-2">Barcode</th>
                                            <th class="col-md-1">Order Qty</th>
                                            <th class="col-md-1">Received Qty</th>
                                            <th class="col-md-1">Unreceived Qty</th>
                                            <th class="col-md-2">Batch</th>
                                            <th class="col-md-1">Receiving Qty</th>
                                            <th class="col-md-2">Expiry Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            foreach($items as $item){
                                                $checkre = $item->qty-$item->count_receving;
                                                if($checkre != 0){

                                        ?>
                                        <tr id="itemrow<?php echo $item->id; ?>" data-item-id="<?php echo $item->id; ?>">
                                            <td>
                                                <input name="po_item[]" type="hidden" value="<?php echo $item->id; ?>">
                                                <input name="po_id[]" type="hidden" value="<?php echo $item->purchase_id; ?>">
                                                <input name="product_id[]" type="hidden" value="<?php echo $item->product_id; ?>">
                                                <span class="sname" id=""><?php echo $item->product_name; ?> <span class="label label-default"></span></span> 
                                            </td>
                                            <td>
                                                <span><?php echo $item->code; ?></span>
                                            </td>
                                            <td class="text-right">
                                                <span class="text-right scost" id=""><?php echo $item->qty; ?></span>
                                            </td>
                                            <td class="text-right">
                                                <span class="text-right scost" id=""><?php echo $item->count_receving; ?></span>
                                            </td>
                                            <td class="text-right">
                                                <span class="text-right scost" id=""><?php echo $item->qty-$item->count_receving; ?></span>
                                            </td>
                                            <td>
                                                <input class="form-control text-center" name="batch[]" type="text" autocomplete="off">
                                            </td>
                                            <td>
                                                <input class="form-control text-center" name="rqty[]" type="number" autocomplete="off" tabindex="2" value="0" data-id="" data-item="" id="" min="0">
                                            </td>
                                            <td>
                                                <input class="form-control text-center edate" value="" readonly name="expirydate[]" type="text" autocomplete="off" tabindex="2" >
                                            </td>
                                        </tr>                                        
                                        
                                        <?php
                                            }
                                            }
                                        ?>
                                        </tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <div class="from-group">
                                <button type="button" class="btn btn-primary" id="submitbtn">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script src="<?= $assets ?>plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script>
    $(document).ready(function(){
        console.log(site.dateFormats.js_sdate);
        $('.edate').datepicker({
            format: site.dateFormats.js_sdate,
            fontAwesome: true,
            startDate: "0d",
            todayBtn: "linked",
            language: 'sma', 
            autoclose: true
        });
        /***********Submit Purchase Order************/
        $('#addrecived_form').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('purchaseorder/submit_recived'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    $('#submitbtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                    alert(data);
                    // location.reload();
                    if(data == 'Add record successfully'){
                        window.top.location.href = '<?= admin_url('purchaseorder/view/'.$po_id); ?>';
                    }
                },
                error: function(jqXHR, textStatus){
                    var errorStatus = jqXHR.status;
                    if(errorStatus==0){ 
                        alert("Internet Connection Problem");
                    }
                    else{
                        alert('Try Again. Error Code '+errorStatus);
                    }
                    $('#submitbtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                }
            });
        });
        $('#submitbtn').click(function(e){
            $('#addrecived_form').submit();
        });


    });
</script>




