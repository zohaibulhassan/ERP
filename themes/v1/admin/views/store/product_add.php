<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Link Product </h3>
            </div>
            <div class="md-card-content" >
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'addFrom');
                    echo admin_form_open_multipart("#", $attrib);
                ?>
                    <div class="uk-grid">

                        <div class="uk-width-large-1-1" style="margin-top: 15px;">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Product <span class="red" >*</span></label>
                                <input type="hidden" name="store_id" value="<?php echo $store->id; ?>">
                                <select name="product" class="uk-width-1-1 product_searching" style="width: 100%">
                                    <option value="">Select Product</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Store Product Code <span class="red" >*</span></label>
                                <input type="text" name="storeid" class="md-input md-input-success label-fixed" required>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Warehouse <span class="red" >*</span></label><br>
                                <select name="warehouseid" class="uk-width-1-1 select2">
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
                        <input type="hidden" name="stocktype" value="single">
                        <input type="hidden" name="supplier" value="0">
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Update Type <span class="red" >*</span></label><br>
                                <select name="updatetype" class="uk-width-1-1 select2">
                                    <option value="qty">Ony Quantity</option>
                                    <option value="price">Ony Price</option>
                                    <option value="priceqty">Price and Quantity</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Update Price <span class="red" >*</span></label><br>
                                <input type="hidden" name="discount" value="no" >
                                <select name="pricetype" class="uk-width-1-1 select2">
                                    <option value="mrp">MRP</option>
                                    <option value="cost">Cost</option>
                                </select>
                            </div>
                        </div>



                    </div>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-large-1-1" style="padding-top: 20px;">
                            <button type="submit" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light" id="submitbtn" >Submit</button>
                            <a href="<?php echo base_url('admin/stores/products?id='.$store->id); ?>" class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light" >Cancel</a>
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
        $(".product_searching").select2({
            minimumInputLength: 2,
            tags: [],
            ajax: {
                url: "<?php echo base_url('admin/general/searching_products2'); ?>",
                dataType: 'json',
                type: "GET",
                quietMillis: 50,
                data: function (term) {
                    return {
                        term: term,
                        supplier_id: 0
                    };
                },
                results: function (data) {
                    console.log(data);
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.completeName,
                                slug: item.slug,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });
        $('#addFrom').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/stores/addproduct'); ?>',
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
                        window.location.href = "<?php echo base_url('admin/stores/products?id='.$store->id); ?>";
                    }
                    else{
                        toastr.error(obj.message);
                    }
                    $('#submitbtn').prop('disabled', false);
                }
            });
        });
    });

</script>

