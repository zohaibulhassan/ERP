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

                <?php $attrib = array('class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form', 'id' => 'addstoreform');
                echo admin_form_open("#", $attrib);
                ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php echo lang('name', 'name'); ?>
                            <div class="controls">
                                <?php echo form_input('name', '', 'class="form-control" id="name" required="required"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php echo lang('type', 'last_name'); ?>
                            <div class="controls">
                                <select name="type" required class="form-control integration-type" >
                                    <option value="Daraz">Daraz</option>
                                    <option value="Wordpress (Wocommerce)">Wordpress (Wocommerce)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php echo lang('Warehouse', 'last_name'); ?>
                            <div class="controls">
                                <select name="warehouse" required class="form-control" >
                                    <?php 
                                        $bydefult = 1;
                                        foreach($warehouses as $warehouse){
                                            echo '<option value="'.$warehouse->id.'" ';
                                            if($warehouse->id == $bydefult){
                                                echo 'selected';
                                            }
                                            echo ' >'.$warehouse->name.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php echo lang('Update Qty In', 'last_name'); ?>
                            <div class="controls">
                                <select name="update_qty_in" required class="form-control" >
                                    <option value="single">Single</option>
                                    <option value="pack">Pack</option>
                                    <option value="carton">Carton</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php echo lang('Update Price', 'last_name'); ?>
                            <div class="controls">
                                <select name="update_price" required class="form-control" >
                                    <option value="mrp">MRP</option>
                                    <option value="consiment">Consiment</option>
                                    <option value="dropship">Dropship</option>
                                    <option value="crossdock">Crossdock</option>
                                    <option value="cost">Cost</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php echo lang('Select Discount', 'last_name'); ?>
                            <div class="controls">
                                <select name="discount" required class="form-control" >
                                    <option value="no">No Discounted</option>
                                    <option value="mrp">Discounted MRP</option>
                                    <option value="d1">Sales Incentive(D1)</option>
                                    <option value="d2">Trade Discount(D2)</option>
                                    <option value="d3">Consumer Discount(D3)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Store URL <small style="color:red;">(Enter With HTTP/HTTPS Protocol)</small></label>
                            <div class="controls">
                                <input type="text" name="store_url" class="form-control" placeholder="Example: https://example.com">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 daraz-integrate">
                        <div class="form-group">
                            <label>Daraz Store ID</label>
                            <div class="controls">
                                <input type="text" name="darazstoreid" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 daraz-integrate">
                        <div class="form-group">
                            <label>Daraz API Key</label>
                            <div class="controls">
                                <input type="text" name="darazapikey" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 wocommerce-integrate">
                        <div class="form-group">
                            <label>Wocommerce Consumer Key</label>
                            <div class="controls">
                                <input type="text" name="wocommerce_key" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 wocommerce-integrate">
                        <div class="form-group">
                            <label>Wocommerce Consumer Secret</label>
                            <div class="controls">
                                <input type="text" name="wocommerce_secret" class="form-control">
                            </div>
                        </div>
                    </div>
                     <div class="col-md-4">
                        <div class="form-group">
                            <label>Stock Margin</label>
                            <div class="controls">
                                <input type="text"  name="stockmargin" class="form-control" placeholder="Example: 100%" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Auto SO Create</label>
                            <select name="so_create" required class="form-control" >
                                <option value="no" >No</option>
                                <option value="yes" >Yes</option>
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
                                <option value="<?= $customer->id ?>" ><?= $customer->name ?></option>
                                <?php
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <input type="button" value="Add Store" id="addbtn" class="btn btn-primary" >
                <!-- <p><?php echo form_submit('add_store', 'Add Store', 'class="btn btn-primary"'); ?></p> -->

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
        $('#addbtn').click(function(){
            $('#addstoreform').submit();
        });
        $('#addstoreform').submit(function(e){
            e.preventDefault();

            $.ajax({
                url: '<?= admin_url('stores/create'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    alert(obj.codestatus);
                    if(obj.codestatus == 'Add New Store Successfully'){
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
