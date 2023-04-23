<style>
    .uk-open>.uk-dropdown, .uk-open>.uk-dropdown-blank{

    }
    .dt_colVis_buttons {
        display:none;
    }
    .summarytable {}
    .summarytable table{
        width: 30%;
        float: right;
    }
    .summarytable tr{}
    .summarytable th{}
    .summarytable td{}
</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">New Production</h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                </div>
            </div>
            <div class="md-card-content">
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'submitFrom');
                    echo admin_form_open_multipart("#", $attrib);
                ?>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Product <span class="red" >*</span></label>
                                <select name="product" class="uk-width-1-1" required id="product_select">
                                    <option value="">Select Product</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Production Quantity <span class="red" >*</span></label>
                                <input type="number" class="uk-width-1-1" name="quantity" id="quantity" min="1">
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Warehouse</label>
                                <select name="warehouse" id="warehosue_id" class="uk-width-1-1 select2" required >
                                    <?php
                                        foreach($warehouses as $row){
                                            echo '<option value="'.$row->id.'" ';
                                            echo ' >'.$row->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Manufacturer</label>
                                <select name="manufacturer" id="manufacturer_id" class="uk-width-1-1 select2" required >
                                    <?php
                                        foreach($manufacturers as $row){
                                            echo '<option value="'.$row->id.'" ';
                                            echo ' >'.$row->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Batch Code <span class="red" >*</span></label>
                                <input type="text" class="uk-width-1-1" name="batch" id="batch">
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-large-1-1">
                            <div class="md-input-wrapper md-input-filled">
                                <button class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light" id="importmaterial" type="button" >Import Material</button>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:50px">
                        <div class="dt_colVis_buttons"></div>
                        <table class="uk-table"  style="width:100%" id="dt_tableExport">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Product Name</th>
                                    <th>Rate</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="summarytable" >
                        <table class="uk-table uk-table-striped ">
                            <tbody>
                                <tr>
                                    <td><b>Total Cost Material</b></td>
                                    <td id="totalnetamount">0</td>
                                </tr>
                                <tr>
                                    <td><b>Total Labour Cost</b></td>
                                    <td><input type="number" name="elc_amount" id="elc_amount" value="0" ></td>
                                </tr>
                                <tr>
                                    <td><b>Total Factory Overheads</b></td>
                                    <td><input type="number" name="efo_ammount" id="efo_ammount" value="0" ></td>
                                </tr>
                                <tr>
                                    <td><b>Total Cost</b></td>
                                    <td id="totalcostamount">0</td>
                                </tr>
                            </tbody>
                        </table>
                        <div style="clear:both" ></div>
                    </div>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-large-1-1">
                            <button class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light" id="submitbtn" type="submit" >Submit</button>
                            <button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light" type="button" >Reset</button>
                        </div>
                    </div>
                
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="">
<!-- datatables -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<!-- datatables buttons-->
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/dataTables.buttons.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/custom/datatables/buttons.uikit.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/jszip/dist/jszip.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/pdfmake/build/pdfmake.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/pdfmake/build/vfs_fonts.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.colVis.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.html5.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.print.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-fixedcolumns/dataTables.fixedColumns.min.js"></script>
<!-- datatables custom integration -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/custom/datatables/datatables.uikit.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/datatable.js"></script>

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function(){
        $('.select2').select2();
        $('#product_select').select2({
            ajax: {
                url: '<?php echo base_url("admin/general/products"); ?>',
                dataType: 'json'
            },
            formatResult: function (data, term) {
                return data;
            },
        });
        $('#importmaterial').click(function(){
            var fg_product = $('#product_select').val();
            var fg_quantity = $('#quantity').val();
            var fg_warehouse = $('#warehosue_id').val();
            $.ajax({
                url: '<?php echo base_url('admin/productions/importmaterial'); ?>',
                type: 'GET',
                data: {fg_product:fg_product,fg_quantity:fg_quantity,fg_warehouse:fg_warehouse},
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    if(obj.status){
                        var totalmc = 0;
                        var totallc = 0;
                        var totalfc = 0;
                        var totalcost = 0;
                        var html = "";
                        $.each(obj.items, function(index) {
                            var item = this;
                            html += "<tr>";
                                html += "<td>"+(index+1);
                                html += "<input type='hidden' name='product_id[]' value='"+item.product_id+"' >";
                                html += "<input type='hidden' name='bom_item_id[]' value='"+item.bom_item_id+"' >";
                                html += "</td>";
                                html += "<td>"+item.product_name+"</td>";
                                html += "<td>"+item.rate+"</td>";
                                html += "<td>"+item.material_qty+"</td>";
                                html += "<td>"+item.total+"</td>";
                            html += "</tr>";
                        });
                        $('#dt_tableExport tbody').html(html);
                        $('#totalnetamount').html(obj.material_cost);
                        $('#elc_amount').html(obj.labour_cost);
                        $('#efo_ammount').html(obj.factory_cost);
                        $('#totalcostamount').html(obj.total_cost);
                    }
                    else{
                        toastr.error(obj.message);
                    }
                }
            });
        });
        $('#submitFrom').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/productions/submit'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    if(obj.status){
                        toastr.success(obj.message);
                        $('#submitFrom')[0].reset();
                        window.location.href = "<?php echo base_url('admin/productions'); ?>";
                    }
                    else{
                        toastr.error(obj.message);
                    }
                    $('#submitbtn').prop('disabled', false);
                }
            });
        });
        function loadTotal(){
            var tma = $('#totalnetamount').html();
            var elc = $('#elc_amount').val();
            var efo = $('#efo_ammount').val();
            console.log(tma);
            console.log(elc);
            console.log(efo);
            tma = parseFloat(tma);
            elc = parseFloat(elc);
            efo = parseFloat(efo);
            $('#totalcostamount').html(tma+elc+efo);
        }

        $('#elc_amount').change(function(){
            loadTotal();
        });
        $('#efo_ammount').change(function(){
            loadTotal();
        });

        $('#submitFrom').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) { 
                e.preventDefault();
                return false;
            }
        });


    });

</script>