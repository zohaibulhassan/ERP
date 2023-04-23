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
                <h3 class="md-card-toolbar-heading-text">Create Sale</h3>
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
                                <label>Sale Date</label>
                                <input class="md-input  label-fixed" type="text" name="date" data-uk-datepicker="{format:'YYYY-MM-DD'}" autocomplete="off" value="<?php echo date('Y-m-d'); ?>" readonly required >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Invoice No <span class="red" >*</span></label>
                                <input type="text" name="reference_no" class="md-input md-input-success label-fixed" required value="" id="slref">
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Warehouse</label>
                                <select name="warehouse" id="slwarehouse" class="uk-width-1-1 select2" required >
                                    <?php
                                        foreach($warehouses as $row){
                                            echo '<option value="'.$row->id.'" ';
                                            echo ' >'.$row->name.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Own Company</label>
                                <select name="own_company" id="poown_companies" class="uk-width-1-1 select2" required >
                                    <?php
                                        foreach($own_company as $row){
                                            echo '<option value="'.$row->id.'" ';
                                            echo ' >'.$row->companyname.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Customer <span class="red" >*</span></label>
                                <select name="customer" class="uk-width-1-1 select2" required id="customer_select">
                                    <option value="">Select Customer</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Delivery Address <span class="red" >*</span></label>
                                <select name="deliveryaddress" class="uk-width-1-1" required id="deliveryaddressid">
                                    <option value="">Select Customer</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Supplier</label>
                                <select name="supplier" id="supplier_id" class="uk-width-1-1 select2" required >
                                    <?php
                                        foreach($suppliers as $row){
                                            echo '<option value="'.$row->id.'" ';
                                            echo ' >'.$row->text.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-large-1-1">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Select Products </label>
                                <input type="text" name="products" id="searchproduct" class="md-input md-input-success label-fixed" placeholder="Enter Product Name or Barcode">
                                <div id="suggesstion-box"></div>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:50px;overflow-x: scroll;">
                        <div class="dt_colVis_buttons"></div>
                        <table class="uk-table"  style="width:100%" id="slTable">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Net Unit Price</th>
                                    <th>MRP</th>
                                    <th>Quantity</th>
                                    <th>Remain Quantity</th>
                                    <th>Batch</th>
                                    <th style="display:none;" ></th>
                                    <th style="display:none;" ></th>
                                    <th style="display:none;" ></th>
                                    <th style="display:none;" ></th>
                                    <th>Expiry</th>
                                    <th>Discount One</th>
                                    <th>Discount Two</th>
                                    <th>Discount Three</th>
                                    <th>Discount Three Code</th>
                                    <th>FED Tax</th>
                                    <th>Discount</th>
                                    <th>Prodcut Tax</th>
                                    <th>Advance Tax</th>
                                    <th>Further Tax</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>

                            </tfoot>
                        </table>
                    </div>

                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Order Discount </label>
                                <input type="text" name="order_discount" class="md-input md-input-success label-fixed"  value="" id="sodiscounttxt">
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Shipping </label>
                                <input type="text" name="shipping" class="md-input md-input-success label-fixed"  value="" id="soshippingtxt">
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Payment Term </label>
                                <input type="text" name="payment_term" class="md-input md-input-success label-fixed"  value="">
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>P.O Number</label>
                                <input type="text" name="po_number" class="md-input md-input-success label-fixed" id="po_number" value="">
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>P.O Date</label>
                                <input class="md-input  label-fixed" type="text" name="po_date" data-uk-datepicker="{format:'YYYY-MM-DD'}" autocomplete="off"  readonly >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>D.C Number </label>
                                <input type="text" name="dc_number" class="md-input md-input-success label-fixed"  value="" id="dc_number">
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Cartdiage </label>
                                <input type="text" name="cartidiage" class="md-input md-input-success label-fixed"  value="" id="cartidiage">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="total_items" value="" id="total_items" required="required"/>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Sale Note </label><br>
                                <textarea class="md-input md-input-success label-fixed" style="border: 1px solid #bec0bc !important;" name="note" id="slnote" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Staff Note </label><br>
                                <textarea  class="md-input md-input-success label-fixed" style="border: 1px solid #bec0bc !important;" name="staff_note" id="slinnote" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-large-1-1">
                            <button class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light" id="submitbtn" type="submit" >Submit</button>
                            <button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light" type="button" id="reset" >Reset</button>
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
    var site = <?=json_encode(array('url' => base_url(), 'base_url' => admin_url('/'), 'assets' => $assets, 'settings' => $Settings, 'dateFormats' => $dateFormats))?>;
</script>

<script src="<?php echo base_url('themes/default/admin/assets/js/custom.js'); ?>"></script>
<script src="<?php echo base_url('themes/default/admin/assets/js/core.js'); ?>"></script>
<script src="<?php echo base_url('themes/default/admin/assets/js/sales.js'); ?>"></script>
<script>
    var count = 1, an = 1, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>,
        product_tax = 0, invoice_tax = 0, product_discount = 0, order_discount = 0, total_discount = 0, total = 0, allow_discount = <?= ($Owner || $Admin || $this->session->userdata('allow_discount')) ? 1 : 0; ?>,
        tax_rates = <?php echo json_encode($tax_rates); ?>;
        var lang = {paid: '<?=lang('paid');?>', pending: '<?=lang('pending');?>', completed: '<?=lang('completed');?>', ordered: '<?=lang('ordered');?>', received: '<?=lang('received');?>', partial: '<?=lang('partial');?>', sent: '<?=lang('sent');?>', r_u_sure: '<?=lang('r_u_sure');?>', due: '<?=lang('due');?>', returned: '<?=lang('returned');?>', transferring: '<?=lang('transferring');?>', active: '<?=lang('active');?>', inactive: '<?=lang('inactive');?>', unexpected_value: '<?=lang('unexpected_value');?>', select_above: '<?=lang('select_above');?>', download: '<?=lang('download');?>'};


    $(document).ready(function(){
        $('.select2').select2();
        $('#customer_select').select2({
            ajax: {
                url: '<?php echo base_url("admin/general/customers"); ?>',
                dataType: 'json',
            },
            formatResult: function (data, term) {
                return data;
            },
        });
        $("#searchproduct").autocomplete({
            source: function (request, response) {
                var customer_id = $('#customer_select').val();
                var warehouse_id = $('#slwarehouse').val();
                var supplier_id = $('#supplier_id').val();
                $.ajax({
                    type: 'get',
                    url: '<?php echo base_url('admin/sales/suggestions'); ?>',
                    // url: '<?php echo base_url('admin/general/searching_products'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        customer_id:customer_id,
                        warehouse_id:warehouse_id,
                        supplier_id:supplier_id,
                    },
                    success: function (data) {
                        $(this).removeClass('ui-autocomplete-loading');
                        response(data);
                    }
                });
            },
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).val('');
                }
                else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    $(this).removeClass('ui-autocomplete-loading');
                }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).val('');
                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_invoice_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    alert('<?= lang('no_match_found') ?>');
                }
            }
        });
        $('#customer_select').change(function(){
            getAddressLis();
        });
        function getAddressLis(palert = false){
            var customerID = $('#customer_select').val();
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            $.ajax({
                url: '<?= admin_url('sales/getaddress'); ?>',
                type: 'POST',
                data: {customerID:customerID,[csrfName]:csrfHash},
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    $('#deliveryaddressid').html(obj.html);
                    if(palert){
                        alert(obj.pricemessage);
                    }
                },
                error: function(jqXHR, textStatus){
                    var errorStatus = jqXHR.status;
                }
            });
        }
        getAddressLis();
        $('#submitFrom').submit(function(e){
            e.preventDefault();
            // $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/sales/submit'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    if(obj.status){
                        toastr.success(obj.message);
                        // $('#submitFrom')[0].reset();
                        if (localStorage.getItem("slitems")) {
                            localStorage.removeItem("slitems");
                        }
                        if (localStorage.getItem("sldiscount")) {
                            localStorage.removeItem("sldiscount");
                        }
                        if (localStorage.getItem("sltax2")) {
                            localStorage.removeItem("sltax2");
                        }
                        if (localStorage.getItem("slshipping")) {
                            localStorage.removeItem("slshipping");
                        }
                        if (localStorage.getItem("slref")) {
                            localStorage.removeItem("slref");
                        }
                        if (localStorage.getItem("slwarehouse")) {
                            localStorage.removeItem("slwarehouse");
                        }
                        if (localStorage.getItem("slnote")) {
                            localStorage.removeItem("slnote");
                        }
                        if (localStorage.getItem("slinnote")) {
                            localStorage.removeItem("slinnote");
                        }
                        if (localStorage.getItem("slcustomer")) {
                            localStorage.removeItem("slcustomer");
                        }
                        if (localStorage.getItem("slcurrency")) {
                            localStorage.removeItem("slcurrency");
                        }
                        if (localStorage.getItem("sldate")) {
                            localStorage.removeItem("sldate");
                        }
                        if (localStorage.getItem("slstatus")) {
                            localStorage.removeItem("slstatus");
                        }
                        if (localStorage.getItem("slbiller")) {
                            localStorage.removeItem("slbiller");
                        }
                        if (localStorage.getItem("gift_card_no")) {
                            localStorage.removeItem("gift_card_no");
                        }
                        window.location.href = "<?php echo base_url('admin/sales'); ?>";
                    }
                    else{
                        toastr.error(obj.message);
                    }
                    // $('#submitbtn').prop('disabled', false);
                }
            });
        });
        $('#poown_companies').change(function(){
            var customerID = $('#slcustomer').val();
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            var owncom = $(this).val();
            $.ajax({
                url: '<?= admin_url('salesorders/autoinvoicecheck'); ?>',
                type: 'POST',
                data: {owncom:owncom,[csrfName]:csrfHash},
                success: function(data){
                    if(data == "true"){
                        $('#slref').val('Auto Generate After Create');
                        $('#slref').attr("readonly","readonly");
                    }
                    else{
                        $('#slref').val('');
                        $('#slref').removeAttr("readonly");
                    }
                },
                error: function(jqXHR, textStatus){
                    var errorStatus = jqXHR.status;
                }
            });
        });

        $('#supplier_id').change(function(){
            localStorage.setItem("supplier_id", $(this).val());
        });
        if (localStorage.getItem("supplier_id")) {
          $('#supplier_id').val(localStorage.getItem("supplier_id")).trigger('change');;
        }



    });
</script>
<script>
</script>


