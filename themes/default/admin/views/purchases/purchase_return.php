<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD ?>
<style>
    .help-block{
        color:red;
    }
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-minus-circle"></i><?= lang('return_purchase'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('enter_info'); ?></p>
            </div>
            <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form','id'=>'returForm');
                echo admin_form_open("#", $attrib)
        ?>

                <input type="hidden" name="purchase_id" value="<?= $purchase_id; ?>" >
                <div class="row">
                    <div class="col-lg-12">
                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <div class="controls table-controls">
                                    <table id="reTable" class="table items table-striped table-bordered table-condensed table-hover">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Received Qty</th>
                                                <th>Balance Qty</th>
                                                <th>Returned Quantity</th>
                                                <th>Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                foreach($items as $item){
                                            ?>
                                                <tr>
                                                    <td><?php echo $item->product_name; ?></td>
                                                    <td><?php echo $item->quantity; ?></td>
                                                    <td><?php echo $item->quantity_balance; ?></td>
                                                    <td>
                                                        <input class="form-control text-center rquantity" min="0" max="<?php echo $item->quantity_balance; ?>" name="return_qty[]" type="number" value="0">
                                                        <input class="form-control text-center" name="item_id[]" id="itemid-<?php echo $item->id; ?>" type="hidden" value="<?php echo $item->id; ?>">
                                                    </td>
                                                    <td>
                                                        <input class="form-control text-center reason" name="reason[]" type="text" value="">
                                                    </td>
                                                </tr>
                                            <?php
                                                }
                                                if(count($items)==0){
                                            ?>
                                                    <tr>
                                                        <td colspan="5" style="text-align:center;color:red;font-size:18px;font-weight:bold">All items sold</td>
                                                    </tr>                                                    
                                            <?php
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="fprom-group">
                                <input type="button" value="Submit" id="addReturnBtn" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;">
                            </div>
                        </div>
                    </div>
                </div>
           <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#addReturnBtn').click(function(){
            $('#returForm').submit();
        });
        $('#returForm').submit(function(e){
            e.preventDefault();
            // $('#addReturnBtn').prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('purchases/purchase_return_submit'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    $('#addReturnBtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                    alert(obj.message);
                    if(obj.codestatus){
                        window.top.location.href = '<?= admin_url('purchases/view/'.$purchase_id); ?>';
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
                    $('#addReturnBtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                }
            });

        });
        $('.rquantity').change(function(){
            var val = $(this).val();
            console.log(val);
            if(val == "" || val < 0){
                $(this).val(0);
            }
        });
    });
</script>
