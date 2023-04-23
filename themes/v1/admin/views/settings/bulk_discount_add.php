<style>
    .select2-container{
    }
</style>
<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Add Bulk Discount </h3>
            </div>
            <div class="md-card-content" >
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'addFrom');
                    echo admin_form_open_multipart("#", $attrib);
                ?>
                    <div class="uk-grid">
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Discount Code <span class="red" >*</span></label>
                                <input type="text" name="code" class="md-input md-input-success label-fixed" required>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Discount Name <span class="red" >*</span></label>
                                <input type="text" name="name" class="md-input md-input-success label-fixed" required>
                            </div>
                        </div>
                        <div class="uk-width-large-1-5">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Start Date</label>
                                <input class="md-input  label-fixed" type="text" name="start_end" data-uk-datepicker="{format:'YYYY-MM-DD'}" autocomplete="off" readonly value="" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-5">
                            <div class="md-input-wrapper md-input-filled">
                                <label>End Date</label>
                                <input class="md-input  label-fixed" type="text" name="end_date" data-uk-datepicker="{format:'YYYY-MM-DD'}" autocomplete="off" readonly value="" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-5">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Percentage <span class="red" >*</span></label>
                                <input type="number" name="percentage" class="md-input md-input-success label-fixed" required autocomplete="off">
                            </div>
                        </div>
                        <div class="uk-width-large-1-5">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Apply On <span class="red" >*</span></label>
                                <select name="apply_on" class="uk-width-1-1" >
                                    <option value="all">All</option>
                                    <option value="pos">POS</option>
                                    <option value="website">Website</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-5">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Type <span class="red" >*</span></label>
                                <select name="type" class="uk-width-1-1" >
                                    <option value="1">Bill</option>
                                    <option value="2">Item</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Categories <span class="red" >*</span></label>
                                <select name="categories[]" class="uk-width-1-1" id="category_select" multiple >
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Products <span class="red" >*</span></label>
                                <select name="products[]" class="uk-width-1-1" id="products_select" multiple >
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Suppliers <span class="red" >*</span></label>
                                <select name="suppliers[]" class="uk-width-1-1" id="suppliers_select" multiple >
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Brands <span class="red" >*</span></label>
                                <select name="brands[]" class="uk-width-1-1" id="brands_select" multiple >
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-large-1-1" style="padding-top: 20px;">
                            <button type="submit" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light" id="submitbtn" >Submit</button>
                            <a href="<?php echo base_url('admin/purchases/bulk_discounts'); ?>" class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light" >Cancel</a>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('.select2').select2();
        $('#suppliers_select').select2({
            ajax: {
                url: '<?php echo base_url("admin/general/suppliers"); ?>',
                dataType: 'json',
            },
            formatResult: function (data, term) {
                return data;
            },
        });
        $('#brands_select').select2({
            ajax: {
                url: '<?php echo base_url("admin/general/brands"); ?>',
                dataType: 'json',
            },
            formatResult: function (data, term) {
                return data;
            },
        });
        $('#category_select').select2({
            ajax: {
                url: '<?php echo base_url("admin/general/allcategories"); ?>',
                dataType: 'json',
            },
            formatResult: function (data, term) {
                return data;
            },
        });
        $('#products_select').select2({
            ajax: {
                url: '<?php echo base_url("admin/general/products"); ?>',
                dataType: 'json',
            },
            formatResult: function (data, term) {
                return data;
            },
        });
        $('#addFrom').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/system_settings/insert_bulk_discount'); ?>',
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
                        window.location.href = "<?php echo base_url('admin/system_settings/bulk_discounts'); ?>";
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

