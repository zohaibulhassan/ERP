<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Update Customer </h3>
            </div>
            <div class="md-card-content" >
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'addFrom');
                    echo admin_form_open_multipart("#", $attrib);
                ?>
                    <div class="uk-grid">
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Selling Price <span class="red" >*</span></label><br>
                                <input type="hidden" name="id" value="<?php echo $customer->id; ?>">
                                <select name="selling" class="uk-width-1-1 select2">
                                    <option value="">Select Price</option>
                                    <option value="cost" <?php if($customer->sales_type == "cost"){ echo 'selected'; } ?> >Cost Price</option>
                                    <option value="consignment" <?php if($customer->sales_type == "consignment"){ echo 'selected'; } ?> >Selling 1</option>
                                    <option value="dropship" <?php if($customer->sales_type == "dropship"){ echo 'selected'; } ?> >Selling 2</option>
                                    <option value="crossdock" <?php if($customer->sales_type == "crossdock"){ echo 'selected'; } ?> >Selling 3</option>
                                    <option value="mrp" <?php if($customer->sales_type == "mrp"){ echo 'selected'; } ?> >MRP</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Name <span class="red" >*</span></label>
                                <input type="text" name="name" class="md-input md-input-success label-fixed" required value="<?php echo $customer->name; ?>">
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Company <span class="red" >*</span></label>
                                <input type="text" name="company" class="md-input md-input-success label-fixed" required value="<?php echo $customer->company; ?>">
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Phone</label>
                                <input type="text" name="phone" class="md-input md-input-success label-fixed" value="<?php echo $customer->phone; ?>">
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Email</label>
                                <input type="text" name="email" class="md-input md-input-success label-fixed" value="<?php echo $customer->email; ?>">
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>CNIC</label>
                                <input type="text" name="cnic" class="md-input md-input-success label-fixed" value="<?php echo $customer->cnic; ?>">
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>VAT </label>
                                <input type="text" name="vat" class="md-input md-input-success label-fixed" value="<?php echo $customer->vat_no; ?>">
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>NTN </label>
                                <input type="text" name="ntn" class="md-input md-input-success label-fixed" value="<?php echo $customer->cf1; ?>">
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>GST/STRN Number</label>
                                <input type="text" name="gst" class="md-input md-input-success label-fixed" value="<?php echo $customer->gst_no; ?>">
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Durg Linces</label>
                                <input type="text" name="linces" class="md-input md-input-success label-fixed" value="<?php echo $customer->linces; ?>">
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Postal Code</label>
                                <input type="text" name="postal" class="md-input md-input-success label-fixed" value="<?php echo $customer->postal_code; ?>">
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>City</label>
                                <input type="text" name="city" class="md-input md-input-success label-fixed" value="<?php echo $customer->city; ?>">
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>State</label>
                                <input type="text" name="state" class="md-input md-input-success label-fixed" value="<?php echo $customer->state; ?>">
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Country</label>
                                <input type="text" name="country" class="md-input md-input-success label-fixed" value="<?php echo $customer->country; ?>">
                            </div>
                        </div>
                        <div class="uk-width-large-1-1">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Address</label>
                                <input type="text" name="address" class="md-input md-input-success label-fixed" value="<?php echo $customer->address; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-large-1-1" style="padding-top: 20px;">
                            <button type="submit" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light" id="submitbtn" >Submit</button>
                            <a href="<?php echo base_url('admin/customers'); ?>" class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light" >Cancel</a>
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
        $('#addFrom').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/customers/update'); ?>',
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
                        window.location.href = "<?php echo base_url('admin/customers'); ?>";
                    }
                    else{
                        toastr.error(obj.message);
                        $('#submitbtn').prop('disabled', false);
                    }
                }
            });
        });
    });
</script>

