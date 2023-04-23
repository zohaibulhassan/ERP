<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
    .form-group{
        margin: 0px 0 20px !important;
    }
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('create_user'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('create_user'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form', 'id' => 'editstoreform');
                echo admin_form_open("#", $attrib);
                ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php echo lang('name', 'name'); ?>
                            <?php echo form_input('name', $store->name, 'class="form-control" id="name" required="required"'); ?>
                            <input type="hidden" name="id" value="<?php echo $store->id; ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php echo lang('type', 'last_name'); ?>
                            <select name="type" required class="form-control integration-type" >
                                <option value="Daraz" <?php if($store->types == "Daraz"){ echo 'selected'; } ?> >Daraz</option>
                                <option value="Wordpress (Wocommerce)" <?php if($store->types == "Wordpress (Wocommerce)"){ echo 'selected'; } ?> >Wordpress (Wocommerce)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php echo lang('Warehouse', 'last_name'); ?>
                            <select name="warehouse" required class="form-control" >
                                <?php 
                                    foreach($warehouses as $warehouse){
                                        echo '<option value="'.$warehouse->id.'" ';
                                        if($warehouse->id == $store->warehouse_id){
                                            echo 'selected';
                                        }
                                        echo ' >'.$warehouse->name.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php echo lang('Update Qty In', 'last_name'); ?>
                            <div class="controls">
                                <select name="update_qty_in" required class="form-control" >
                                    <option value="single" <?php if($store->update_qty_in == "single"){ echo 'selected'; } ?> >Single</option>
                                    <option value="pack" <?php if($store->update_qty_in == "pack"){ echo 'selected'; } ?> >Pack</option>
                                    <option value="carton" <?php if($store->update_qty_in == "carton"){ echo 'selected'; } ?> >Carton</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php echo lang('Update Price', 'last_name'); ?>
                            <div class="controls">
                                <select name="update_price" required class="form-control" >
                                    <option value="mrp" <?php if($store->update_price == "mrp"){ echo 'selected'; } ?> >MRP</option>
                                    <option value="consiment" <?php if($store->update_price == "consiment"){ echo 'selected'; } ?> >Consiment</option>
                                    <option value="dropship" <?php if($store->update_price == "dropship"){ echo 'selected'; } ?> >Dropship</option>
                                    <option value="crossdock" <?php if($store->update_price == "crossdock"){ echo 'selected'; } ?> >Crossdock</option>
                                    <option value="cost" <?php if($store->update_price == "cost"){ echo 'selected'; } ?> >Cost</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php echo lang('Select Discount', 'last_name'); ?>
                            <div class="controls">
                                <select name="discount" required class="form-control" >
                                    <option value="no" <?php if($store->discount == "no"){ echo 'selected'; } ?> >No Discounted</option>
                                    <option value="mrp" <?php if($store->discount == "mrp"){ echo 'selected'; } ?> >Discounted MRP</option>
                                    <option value="d1" <?php if($store->discount == "d1"){ echo 'selected'; } ?> >Sales Incentive(D1)</option>
                                    <option value="d2" <?php if($store->discount == "d2"){ echo 'selected'; } ?> >Trade Discount(D2)</option>
                                    <option value="d3" <?php if($store->discount == "d3"){ echo 'selected'; } ?> >Consumer Discount(D3)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Store URL <small style="color:red;">(Enter With HTTP/HTTPS Protocol)</small></label>
                            <div class="controls">
                                <input type="text" name="store_url" class="form-control" value="<?php echo $store->store_url; ?>" placeholder="Example: https://example.com">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 daraz-integrate">
                        <div class="form-group">
                            <label>Daraz Store ID</label>
                            <div class="controls">
                                <input type="text" name="darazstoreid" value="<?php echo $store->daraz_store_id; ?>" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 daraz-integrate">
                        <div class="form-group">
                            <label>Daraz API Key</label>
                            <div class="controls">
                                <input type="text" name="darazapikey" value="<?php echo $store->daraz_api_key; ?>" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 wocommerce-integrate">
                        <div class="form-group">
                            <label>Wocommerce Consumer Key</label>
                            <div class="controls">
                                <input type="text" name="wocommerce_key" value="<?php echo $store->wordpress_wocommerce_consumer_key; ?>" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 wocommerce-integrate">
                        <div class="form-group">
                            <label>Wocommerce Consumer Secret</label>
                            <div class="controls">
                                <input type="text" name="wocommerce_secret" value="<?php echo $store->wordpress_wocommerce_consumer_secret; ?>" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php echo lang('status', 'status'); ?>
                            <div class="controls">
                                <select name="status" required class="form-control" >
                                    <option value="active" <?php if($store->status == "active"){ echo 'selected'; } ?> >Active</option>
                                    <option value="deactive" <?php if($store->status == "deactive"){ echo 'selected'; } ?> >Deactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-4">
                        <div class="form-group">
                            <label>Stock Margin</label>
                            <div class="controls">
                                <input type="text" name="stockmargin" value="<?php echo !empty($store->stock_margin)? $store->stock_margin.'%':""; ?>" placeholder="Example: 100%" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Auto SO Create</label>
                            <select name="so_create" required class="form-control" >
                                <option value="no" <?php if($store->auto_so == "no"){ echo 'selected'; } ?> >No</option>
                                <option value="yes" <?php if($store->auto_so == "yes"){ echo 'selected'; } ?> >Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Defualt Customer</label>
                            <select name="customer" required class="form-control" >
                                <option value="">Select Customer</option>
                                <?php
                                    foreach($customers as $customer){
                                ?>
                                <option value="<?= $customer->id ?>" <?php if($customer->id == $store->customer_id){ echo 'selected'; } ?> ><?= $customer->name ?></option>
                                <?php
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <input type="button" value="Edit Store" id="editbtn" class="btn btn-primary" >
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {
        $('.no').slideUp();
        $('#group').change(function (event) {
            var group = $(this).val();
            if (group == 1 || group == 2) {
                $('.no').slideUp();
            } else {
                $('.no').slideDown();
            }
        });
        $('#editbtn').click(function(){
            $('#editstoreform').submit();
        });
        $('#editstoreform').submit(function(e){
            e.preventDefault();

            $.ajax({
                url: '<?= admin_url('stores/updated'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    alert(obj.codestatus);
                    if(obj.codestatus == 'Edit Store Successfully'){
                        window.top.location.href = '<?= admin_url('stores'); ?>';
                    }
                },
                error: function(jqXHR, textStatus){
                    var errorStatus = jqXHR.status;
                    if(errorStatus==0){ 
                    }
                    else{
                    }
                }
            });

        });
    });

    $(document).ready(function(){
    var selected_type = $('.integration-type').val();
        if(selected_type=="Daraz"){
            $('.wocommerce-integrate').hide();
            $('.daraz-integrate').show();
        }
        else{
            $('.daraz-integrate').hide();
            $('.wocommerce-integrate').show();
        }
    });

    $(document).on("change", '.integration-type', function() {
       var selected_type = $(this).val();
        if(selected_type=="Daraz"){
            $('.wocommerce-integrate').hide();
            $('.daraz-integrate').show();
        }
        else{
            $('.daraz-integrate').hide();
            $('.wocommerce-integrate').show();
        }
 });
</script>
