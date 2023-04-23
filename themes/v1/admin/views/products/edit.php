<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Edit Product </h3>
            </div>
            <div class="md-card-content" >
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'productFrom');
                    echo admin_form_open_multipart("#", $attrib);
                ?>
                    <div class="uk-grid">
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Product Image</label><br>
                                <input type="file" name="image" class="md-input md-input-success label-fixed" style="width:100%">
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Name <span class="red" >*</span></label>
                                <input type="hidden" name="product_id" value="<?php echo $product->id ?>" >
                                <input type="text" name="name" class="md-input md-input-success label-fixed"  value="<?php echo $product->name ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Product Group <span class="red" >*</span></label>
                                <select name="group" class="uk-width-1-1 select2">
                                    <option value="">Select Group</option>
                                    <?php
                                        foreach($groups as $row){
                                            echo '<option value="'.$row->id.'" ';
                                            if($row->id == $product->group_id){
                                                echo 'selected';
                                            }
                                            echo ' >'.$row->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Barcode <span class="red" >*</span></label>
                                <input type="text" name="barcode" class="md-input md-input-success label-fixed"  value="<?php echo $product->code ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Compnay Code <span class="red" >*</span></label>
                                <input type="text" name="companycode" class="md-input md-input-success label-fixed"  value="<?php echo $product->company_code ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>HSN Code</label>
                                <input type="text" name="hsncode" class="md-input md-input-success label-fixed" value="<?php echo $product->hsn_code ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Brand <span class="red" >*</span></label>
                                <select name="brnad" class="uk-width-1-1 select2" >
                                    <option value="">Select Brand</option>
                                    <?php
                                        foreach($brands as $row){
                                            echo '<option value="'.$row->id.'" ';
                                            if($row->id == $product->brand){
                                                echo 'selected';
                                            }
                                            echo ' >'.$row->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Category <span class="red" >*</span></label>
                                <select name="category" class="uk-width-1-1 select2" >
                                    <option value="">Select Category</option>
                                    <?php
                                        foreach($categories as $row){
                                            echo '<option value="'.$row->id.'" ';
                                            if($row->id == $product->category_id){
                                                echo 'selected';
                                            }
                                            echo ' >'.$row->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Sub Category</label>
                                <select name="subcategory" class="uk-width-1-1 select2">
                                    <option value="0">Select Sub Category</option>
                                    <?php
                                        foreach($subcategories as $row){
                                            echo '<option value="'.$row->id.'" ';
                                            if($row->id == $product->subcategory_id){
                                                echo 'selected';
                                            }
                                            echo ' >'.$row->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Status <span class="red" >*</span></label>
                                <select name="status" class="uk-width-1-1 select2"  >
                                    <option value="1" <?php if($product->status == 1 ){ echo 'selected'; } ?> >Active</option>
                                    <option value="0" <?php if($product->status == 0 ){ echo 'selected'; } ?> >Deactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Formula <span class="red" >*</span></label>
                                <select name="formula" class="uk-width-1-1 select2" >
                                    <option value="">Select Formula</option>
                                    <?php
                                        foreach($formulas as $row){
                                            echo '<option value="'.$row->id.'" ';
                                            if($product->formulas == $row->id){
                                                echo 'selected';
    
                                            }
                                            echo ' >'.$row->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Prescription Required <span class="red" >*</span></label>
                                <select name="prescription" class="uk-width-1-1 select2" >
                                    <option value="0" <?php if($product->prescription == "0"){ echo 'selected'; } ?> >No</option>
                                    <option value="1" <?php if($product->prescription == "1"){ echo 'selected'; } ?> >Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Weight <span class="red" >*</span></label>
                                <input type="number" name="weight" class="md-input md-input-success label-fixed"   value="<?php echo $product->weight ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Product Unit <span class="red" >*</span></label>
                                <select name="unit" class="uk-width-1-1 select2" >
                                    <option value="">Select Unit</option>
                                    <?php
                                        foreach($units as $row){
                                            echo '<option value="'.$row->id.'" ';
                                            if($row->id == $product->unit){
                                                echo 'selected';
                                            }
                                            echo ' >'.$row->text.'</option>';
                                        }
                                    ?>
    
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled" >
                                <label>Pack Size <span class="red" >*</span></label>
                                <input type="number" name="packsize" class="md-input md-input-success label-fixed" value="<?php echo $product->pack_size ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Carton Size <span class="red" >*</span></label>
                                <input type="number" name="cartonsize" class="md-input md-input-success label-fixed"  value="<?php echo $product->carton_size ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Manufacturer <span class="red" >*</span></label>
                                <select name="manufacturer" class="uk-width-1-1 select2" >
                                    <option value="">Select Manufacturer</option>
                                    <?php
                                        foreach($manufacturers as $row){
                                            echo '<option value="'.$row->id.'" ';
                                            if($product->manufacturer == $row->id){
                                                echo 'selected';
    
                                            }
                                            echo ' >'.$row->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Supplier 1 <span class="red" >*</span></label>
                                <select name="supplier1" class="uk-width-1-1 select2" >
                                    <option value="">Select Supplier</option>
                                    <?php
                                        foreach($suppliers as $row){
                                            echo '<option value="'.$row->id.'" ';
                                            if($row->id == $product->supplier1){
                                                echo 'selected';
                                            }
                                            echo ' >'.$row->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Supplier 2</label>
                                <select name="supplier2" class="uk-width-1-1 select2">
                                    <option value="">Select Supplier</option>
                                    <?php
                                        foreach($suppliers as $row){
                                            echo '<option value="'.$row->id.'" ';
                                            if($row->id == $product->supplier2){
                                                echo 'selected';
                                            }
                                            echo ' >'.$row->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Supplier 3</label>
                                <select name="supplier3" class="uk-width-1-1 select2">
                                    <option value="">Select Supplier</option>
                                    <?php
                                        foreach($suppliers as $row){
                                            echo '<option value="'.$row->id.'" ';
                                            if($row->id == $product->supplier3){
                                                echo 'selected';
                                            }
                                            echo ' >'.$row->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Supplier 4</label>
                                <select name="supplier4" class="uk-width-1-1 select2">
                                    <option value="">Select Supplier</option>
                                    <?php
                                        foreach($suppliers as $row){
                                            echo '<option value="'.$row->id.'" ';
                                            if($row->id == $product->supplier4){
                                                echo 'selected';
                                            }
                                            echo ' >'.$row->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Supplier 5</label>
                                <select name="supplier5" class="uk-width-1-1 select2">
                                    <option value="">Select Supplier</option>
                                    <?php
                                        foreach($suppliers as $row){
                                            echo '<option value="'.$row->id.'" ';
                                            if($row->id == $product->supplier5){
                                                echo 'selected';
                                            }
                                            echo ' >'.$row->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid">
                        <div class="uk-width-large-1-5">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Cost <small>(With Out Tax)</small><span class="red" >*</span></label>
                                <input type="text" name="cost" class="md-input md-input-success label-fixed"  value="<?php echo $product->cost ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-5">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Selling 1 <small>(With Out Tax)</small><span class="red" >*</span></label>
                                <input type="text" name="consignment" class="md-input md-input-success label-fixed"  value="<?php echo $product->price ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-5">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Selling 2 <small>(With Out Tax)</small><span class="red" >*</span></label>
                                <input type="text" name="dropship" class="md-input md-input-success label-fixed"  value="<?php echo $product->dropship ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-5">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Selling 3 <small>(With Out Tax)</small><span class="red" >*</span></label>
                                <input type="text" name="crossdock" class="md-input md-input-success label-fixed"  value="<?php echo $product->crossdock ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-5">
                            <div class="md-input-wrapper md-input-filled">
                                <label>MRP <small>(With Tax)</small><span class="red" >*</span></label>
                                <input type="text" name="mrp" class="md-input md-input-success label-fixed"  value="<?php echo $product->mrp ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Tax Method <span class="red" >*</span></label>
                                <select name="texmethod" class="uk-width-1-1 select2"  >
                                    <option value="1" <?php if($product->tax_method == 1 ){ echo 'selected'; } ?> >Exclusive</option>
                                    <option value="0" <?php if($product->tax_method == 0 ){ echo 'selected'; } ?> >Inclusive</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Product Tax <span class="red" >*</span></label>
                                <select name="producttax" class="uk-width-1-1 select2" >
                                    <option value="">Select Product Tax</option>
                                    <?php
                                        foreach($taxs as $row){
                                            echo '<option value="'.$row->id.'" ';
                                            if($row->id == $product->tax_rate){
                                                echo 'selected';
                                            }
                                            echo ' >'.$row->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>FED Tax <span class="red" >*</span></label>
                                <input type="text" name="fed_tax" class="md-input md-input-success label-fixed"   value="<?php echo $product->fed_tax ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Register Advance Tax For Sale <span class="red" >*</span></label>
                                <input type="text" name="ratax_sale" class="md-input md-input-success label-fixed"   value="<?php echo $product->adv_tax_reg ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Non-Register Advance Tax For Sale <span class="red" >*</span></label>
                                <input type="text" name="nratax_sale" class="md-input md-input-success label-fixed"   value="<?php echo $product->adv_tax_nonreg ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Register Advance Tax For Purchase <span class="red" >*</span></label>
                                <input type="text" name="ratax_purchase" class="md-input md-input-success label-fixed"   value="<?php echo $product->adv_tax_for_purchase ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Sales Incentive <span class="red" >*</span></label>
                                <input type="text" name="si_dicount" class="md-input md-input-success label-fixed"   value="<?php echo $product->discount_one ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Trade Discount <span class="red" >*</span></label>
                                <input type="text" name="t_discount" class="md-input md-input-success label-fixed"   value="<?php echo $product->discount_two ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Consumer Discount <span class="red" >*</span></label>
                                <input type="text" name="c_discount" class="md-input md-input-success label-fixed"   value="<?php echo $product->discount_three ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Alert Quantity <span class="red" >*</span></label>
                                <input type="number" name="alertqty" class="md-input md-input-success label-fixed"   value="<?php echo $product->alert_quantity ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Stock Hold Quantity <span class="red" >*</span></label>
                                <input type="number" name="hold_qty" class="md-input md-input-success label-fixed"   value="<?php echo $product->hold_stock ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Expected Sold Out Days <span class="red" >*</span></label>
                                <input type="number" name="sold_days" class="md-input md-input-success label-fixed"   value="<?php echo $product->es_durration ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Short Expiry Days <span class="red" >*</span></label>
                                <input type="number" name="se_expiry" class="md-input md-input-success label-fixed"   value="<?php echo $product->short_expiry_duration ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-1">
                            <label>Product Detail</label>
                        </div>
                        <div class="uk-width-large-1-1">
                            <textarea name="detail" class="md-input no_autosize" id="editor" style="min-height:250px" ><?php echo $product->product_details ?></textarea>
                        </div>
                        <div class="uk-width-large-1-1" style="padding-top: 20px;">
                            <button type="submit" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light" id="submitbtn" >Submit</button>
                            <a href="<?php echo base_url('admin/products'); ?>" class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light" >Cancel</a>
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
    ClassicEditor
    .create( document.querySelector( '#editor' ),{
        toolbar: {
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ],
            shouldNotGroupWhenFull: true
        }
    })
    .then( editor => {
        window.editor = editor;
    })
    .catch( error => {
        console.error( error );
    });

</script>

<script>
    $(document).ready(function(){
        $('.select2').select2();
    });
    $('#productFrom').submit(function(e){
        e.preventDefault();
        $('#submitbtn').prop('disabled', true);
        altair_helpers.content_preloader_show('md');
        $.ajax({
            url: '<?php echo base_url('admin/products/update_submit'); ?>',
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
                    $('#productFrom')[0].reset();
                    window.location.href = "<?php echo base_url('admin/products'); ?>";
                }
                else{
                    toastr.error(obj.message);
                }
                $('#submitbtn').prop('disabled', false);
                altair_helpers.content_preloader_hide();
            },
            error: function (request, status, error) {
                toastr.error(request.responseText);
                altair_helpers.content_preloader_hide();
                $('#submitbtn').prop('disabled', false);
            }
        });
    });

</script>

