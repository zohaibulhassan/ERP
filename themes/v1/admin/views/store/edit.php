<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Edit Store </h3>
            </div>
            <div class="md-card-content" >
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'editFrom');
                    echo admin_form_open_multipart("#", $attrib);
                ?>
                    <div class="uk-grid">
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Name <span class="red" >*</span></label>
                                <input type="hidden" name="id" value="<?php echo $store->id ?>">
                                <input type="text" name="name" class="md-input md-input-success label-fixed" required value="<?php echo $store->name ?>">
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Warehouse <span class="red" >*</span></label><br>
                                <select name="warehouse" class="uk-width-1-1 select2">
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
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Default Update Price <span class="red" >*</span></label><br>
                                <input type="hidden" name="update_qty_in" value="single" >
                                <input type="hidden" name="discount" value="no" >
                                <select name="update_price" class="uk-width-1-1 select2">
                                    <option value="mrp" <?php if($store->update_price == "mrp"){ echo 'selected'; } ?> >MRP</option>
                                    <option value="cost" <?php if($store->update_price == "cost"){ echo 'selected'; } ?> >Cost</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Type <span class="red" >*</span></label><br>
                                <select name="type" class="uk-width-1-1 select2 integration-type">
                                    <option value="Wordpress (Wocommerce)" <?php if($store->types == "Wordpress (Wocommerce)"){ echo 'selected'; } ?> >Wordpress (Wocommerce)</option>
                                    <option value="Shopify" <?php if($store->types == "Shopify"){ echo 'selected'; } ?> >Shopify</option>
                                    <option value="Daraz" <?php if($store->types == "Daraz"){ echo 'selected'; } ?> >Daraz</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Store URL <small style="color:red;">(Enter With HTTP/HTTPS Protocol)</small><span class="red" >*</span></label>
                                <input type="text" name="store_url" class="md-input md-input-success label-fixed" required placeholder="Example: https://example.com" value="<?php echo $store->store_url ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3 daraz-integrate">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Daraz Store ID</label>
                                <input type="text" name="darazstoreid" class="md-input md-input-success label-fixed" value="<?php echo $store->daraz_store_id ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3 daraz-integrate">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Daraz API Key</label>
                                <input type="text" name="darazapikey" class="md-input md-input-success label-fixed" value="<?php echo $store->daraz_api_key ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3 wocommerce-integrate">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Consumer Key</label>
                                <input type="text" name="wocommerce_key" class="md-input md-input-success label-fixed" value="<?php echo $store->wordpress_wocommerce_consumer_key ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3 wocommerce-integrate">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Consumer Secret</label>
                                <input type="text" name="wocommerce_secret" class="md-input md-input-success label-fixed" value="<?php echo $store->wordpress_wocommerce_consumer_secret ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Stock Margin <span class="red" >*</span></label>
                                <input type="text" name="stockmargin" class="md-input md-input-success label-fixed" required  value="<?php echo $store->stock_margin ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Auto SO Create</label><br>
                                <select name="so_create" class="uk-width-1-1 select2">
                                    <option value="no" <?php if($store->auto_so == "no"){ echo 'selected'; } ?> >No</option>
                                    <option value="yes" <?php if($store->auto_so == "yes"){ echo 'selected'; } ?> >Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Auto Batch Select</label><br>
                                <select name="so_batch_select" class="uk-width-1-1 select2">
                                    <option value="0" <?php if($store->auto_batch_selete == "0"){ echo 'selected'; } ?> >No</option>
                                    <option value="1" <?php if($store->auto_batch_selete == "1"){ echo 'selected'; } ?> >Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Auto Invoice Create</label><br>
                                <select name="so_invoice_create" class="uk-width-1-1 select2">
                                    <option value="0" <?php if($store->auto_invoice == "0"){ echo 'selected'; } ?> >No</option>
                                    <option value="1" <?php if($store->auto_invoice == "1"){ echo 'selected'; } ?> >Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Defualt Customer</label><br>
                                <select name="customer" class="uk-width-1-1 select2">
                                    <option value="">Select Customer</option>
                                    <?php
                                        foreach($customers as $customer){
                                    ?>
                                    <option value="<?= $customer->id ?>" <?php if($customer->id==$store->customer_id){ echo 'selected'; } ?> ><?= $customer->name ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Status</label><br>
                                <select name="status" class="uk-width-1-1 select2">
                                    <option value="active" <?php if($store->status == "active"){ echo 'selected'; } ?> >Active</option>
                                    <option value="deactive" <?php if($store->status == "deactive"){ echo 'selected'; } ?> >Deactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-large-1-1" style="padding-top: 20px;">
                            <button type="submit" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light" id="submitbtn" >Submit</button>
                            <a href="<?php echo base_url('admin/stores'); ?>" class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light" >Cancel</a>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<!-- CK Editor 5 -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/ckeditor5/ckeditor.js"></script>
<script>
    $(document).ready(function(){
        $('.select2').select2();
        $('#editFrom').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/stores/updated'); ?>',
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
                        window.location.href = "<?php echo base_url('admin/stores'); ?>";
                    }
                    else{
                        toastr.error(obj.message);
                        $('#submitbtn').prop('disabled', false);
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
        console.log(selected_type);
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

