<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD ?>
<style>
    table span.pull-right {
        padding:0 !important;
    }
</style>
<div id="ajaxCall" style="display: none;"><i class="fa fa-spinner fa-pulse"></i></div>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i>Sale Return</h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form','id'=>'returnForm');
                    echo admin_form_open("#", $attrib)
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label">Sale Items</label>
                                <input type="hidden" name="sale_id" value="<?= $sale_id ?>" >
                                <div class="controls table-controls">
                                    <table class="table table-striped table-bordered table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th class="col-md-2">Product Name</th>
                                            <th class="col-md-2">Barcode</th>
                                            <th class="col-md-1">Order Qty</th>
                                            <th class="col-md-2">Batch</th>
                                            <th class="col-md-1">Return Qty</th>
                                            <th class="col-md-2">Reason</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            foreach($items as $item){

                                        ?>
                                        <tr>
                                            <td>
                                                <input name="saleitem_id[]" type="hidden" value="<?php echo $item->id; ?>">
                                                <?php echo $item->product_name; ?> 
                                            </td>
                                            <td>
                                                <span><?php echo $item->product_code; ?></span>
                                            </td>
                                            <td class="text-right">
                                                <span class="text-right scost" id=""><?php echo $item->quantity; ?></span>
                                            </td>
                                            <td class="text-right">
                                                <span class="text-right scost" id=""><?php echo $item->batch; ?></span>
                                            </td>
                                            <td>
                                                <input class="form-control text-center returnqtytxt" name="rqty[]" type="number" autocomplete="off" tabindex="2" value="0" data-id="" data-item="" id="" min="0" max="<?php echo (int)$item->quantity; ?>" data-max="<?php echo (int)$item->quantity; ?>">
                                            </td>
                                            <td>
                                                <input class="form-control " name="reason[]" type="text" autocomplete="off">
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
        /***********Submit Purchase Order************/
        $('#returnForm').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('sales/return_sale_submit'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    $('#submitbtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                    alert(data);
                    if(data == 'Return Successfully'){
                        window.top.location.href = '<?= admin_url('sales/detail/'.$sale_id); ?>';
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
            $('#returnForm').submit();
        });
        $('.returnqtytxt').change(function(){
            var qty = $(this).val();
            var max = $(this).data('max');
            if(qty == ""){
                $(this).val('0');
            }
            else if(qty > max){
                $(this).val(max);
                
            }
        });
    });
</script>
