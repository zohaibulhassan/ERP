<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD ?>
<style>
    table span.pull-right {
        padding:0 !important;
    }
</style>
<div id="ajaxCall" style="display: none;"><i class="fa fa-spinner fa-pulse"></i></div>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i>Add Sale Order</h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form','id'=>'po_form');
                    echo admin_form_open("salesorders/submit", $attrib)
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Sale Order Date</label>
                                <?php echo form_input('date', date('d-m-Y'), 'class="form-control date" id="saledate" required="required" autocomplete="off"'); ?>
                            </div>
                        </div>
                        <!-- <div class="col-md-4">
                            <div class="form-group">
                                <label>Reference No</label>
                                <?php echo form_input('reference_no',$generate_ref, 'class="form-control input-tip" id="refno" readonly'); ?>
                            </div>
                        </div> -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Warehouse</label>
                                <?php
                                $wh[''] = '';
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $Settings->default_warehouse), 'class="form-control input-tip select" id="warehouseid" data-placeholder="' . lang("select") . ' ' . lang("warehouse") . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="po_number">P.O Number </label>
                                <?php echo form_input('po_number', '', 'class="form-control input-tip" id="po_number"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>PO Date</label>
                                <?php echo form_input('po_date', date('Y-m-d'), 'class="form-control date2" id="salepodate" required="required" autocomplete="off"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="slcustomer">Customer Type</label>
                                <input type="text" value="" hidden class="hidden_customer_id" readonly/>
                                <div class="input-group">
                                    <?php
                                        echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'id="slcustomer" data-placeholder="' . lang("select") . ' ' . lang("customer") . '" required="required" class="form-control input-tip" style="width:100%;"');
                                    ?>
                                    <div class="input-group-addon no-print" style="padding: 2px 8px; border-left: 0;">
                                        <a href="#" id="toogle-customer-read-attr" class="external">
                                            <i class="fa fa-pencil" id="addIcon" style="font-size: 1.2em;"></i>
                                        </a>
                                    </div>
                                    <div class="input-group-addon no-print" style="padding: 2px 7px; border-left: 0;">
                                        <a href="#" id="view-customer" class="external" data-toggle="modal" data-target="#myModal">
                                            <i class="fa fa-eye" id="addIcon" style="font-size: 1.2em;"></i>
                                        </a>
                                    </div>
                                    <?php if ($Owner || $Admin || $GP['customers-add']) { ?>
                                        <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                            <a href="<?= admin_url('customers/add'); ?>" id="add-customer"class="external" data-toggle="modal" data-target="#myModal">
                                                <i class="fa fa-plus-circle" id="addIcon"  style="font-size: 1.2em;"></i>
                                            </a>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Delivery Address</label>
                                <select name="deliveryaddress" id="deliveryaddressid" class="form-control" >
                                    <option value="0">Default Address</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sletaliers">E-taliers</label>
                                <?php
                                    $lcu[''] = '';
                                    foreach ($lcustomers as $lcustomer) {
                                        $lcu[$lcustomer->id] = $lcustomer->company;
                                    }
                                    echo form_dropdown('etaliers', $lcu, $_POST['etaliers'], 'id="etaliers" class="form-control input-tip searching_select" data-placeholder="' . lang("select") . ' ' . lang("E-taliers") . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Delivery Date</label>
                                <?php echo form_input('saledeliverydate', date('Y-m-d'), 'class="form-control date2" id="saledeliverydate" required="required" autocomplete="off"'); ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="panel panel-warning">
                                <div class="panel-heading"><?= lang('please_select_these_before_adding_product') ?></div>
                                <div class="panel-body" style="padding: 5px;">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Supplier</label>
                                            <div class="input-group">
                                                <input type="hidden" name="suppliers" value="" id="suppliers" class="form-control" style="width:100%;" placeholder="<?= lang("select") . ' ' . lang("supplier") ?>">
                                                <div class="input-group-addon no-print" style="padding: 2px 5px; border-left: 0;">
                                                    <a href="#" id="view-supplier" class="external" data-toggle="modal" data-target="#myModal">
                                                        <i class="fa fa-2x fa-user" id="addIcon"></i>
                                                    </a>
                                                </div>
                                                <div class="input-group-addon no-print" style="padding: 2px 5px;">
                                                    <a href="<?= admin_url('suppliers/add'); ?>" id="add-supplier" class="external" data-toggle="modal" data-target="#myModal">
                                                        <i class="fa fa-2x fa-plus-circle" id="addIcon"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                                <i class="fa fa-2x fa-barcode addIcon"></i>
                                            </a>
                                        </div>
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . $this->lang->line("add_product_to_order") . '"'); ?>
                                        <?php if ($Owner || $Admin || $GP['products-add']) { ?>
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <a href="<?= admin_url('products/add') ?>" id="addManually1"><i
                                                    class="fa fa-2x fa-plus-circle addIcon" id="addIcon"></i></a></div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("order_items"); ?></label>

                                <div class="controls table-controls">
                                    <input type="hidden" value="" id="itemsdata" name="items">
                                    <table id="po_tb" class="table items table-striped table-bordered table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th class="col-md-10" style="text-align:left"><?= lang('product') . ' (' . lang('code') .' - '.lang('name') . ')'; ?></th>
                                            <th class="col-md-1"><?= lang("quantity"); ?></th>
                                            <th class="col-md-1"><i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <?php if ($Settings->tax2) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("order_tax", "sltax2"); ?>
                                    <?php
                                    $tr[""] = "";
                                    foreach ($tax_rates as $tax) {
                                        $tr[$tax->id] = $tax->name;
                                    }
                                    echo form_dropdown('order_tax', $tr, (isset($_POST['order_tax']) ? $_POST['order_tax'] : $Settings->default_tax_rate2), 'id="sltax2" data-placeholder="' . lang("select") . ' ' . lang("order_tax") . '" class="form-control input-tip select" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        <?php } ?>
                        <!-- <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("payment_term", "payment_term"); ?>
                                <?php echo form_input('payment_term', '', 'class="form-control tip" data-trigger="focus" data-placement="top" title="' . lang('payment_term_tip') . '" id="payment_term"'); ?>
                            </div>
                        </div> -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang("note", "order_note"); ?>
                                <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control" id="order_note" style="margin-top: 10px; height: 100px;"'); ?>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="from-group">
                                <button type="button" class="btn btn-primary" id="pobtn">Submit</button>
                                <button type="button" class="btn btn-danger" id="resetbtn"><?= lang('reset') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="well well-sm" style="margin-top: 15px;">
                    <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                        <tr class="warning">
                            <td>Items <span class="pull-right" id="total_order_items" >0</span></td>
                        </tr>
                    </table>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        localStorage.setItem('salesorders_refno', '<?= $generate_ref ?>');

        $('#alertQtybtn').click(function(){
            $("#alertQtybtn").prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                type: 'get',
                url: '<?= admin_url('salesorders/alertqty'); ?>',
                dataType: "json",
                data: {
                    supplier_id: $("#suppliers").val(),
                    warehouse_id: $("#warehouseid").val(),
                },
                success: function (data) {
                    var i = 0;
                    for(i=0;i<data.length;i++){
                        if(data[i].id != 0){
                            add_purchase_item(data[i]);
                        }
                        else{
                            alert(data[i].label);
                        }
                    }
                    $("#alertQtybtn").prop('disabled', false);
                    $('#ajaxCall').hide();
                },
                error: function(jqXHR, textStatus){
                    var errorStatus = jqXHR.status;
                    $("#alertQtybtn").prop('disabled', false);
                    $('#ajaxCall').hide();
                }
            });
        });
        $("#po_form").keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
        /**************Add Item in Table***********************/
        $("#add_item").autocomplete({
            source: function (request, response) {
                $.ajax({
                    type: 'get',
                    url: '<?= admin_url('salesorders/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        supplier_id: $("#suppliers").val(),
                        warehouse_id: $("#warehouseid").val(),
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
                    //audio_error.play();
                    bootbox.alert('No Match Found', function () {
                        $('#add_item').focus();
                    });
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
                    //audio_error.play();
                    bootbox.alert('No Match Found', function () {
                        $('#add_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).val('');
                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_purchase_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    //audio_error.play();
                    bootbox.alert('No Match Found');
                }
            }
        });
        /**************Update Order Tax********************/
        $('#order_tax').change(function(){
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
                csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';            
            $.ajax({
                type: 'post',
                url: '<?= admin_url('salesorders/gettex'); ?>',
                data: {[csrfName]: csrfHash,'id':$(this).val()},
                success: function (data) {
                    localStorage.setItem('salesorders_order_tax', data);    
                    loadItems();  
                }
            });
        });
        /***********Submit Sales Order************/
        $('#po_form').submit(function(e){
            e.preventDefault();
            $('#pobtn').prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('salesorders/submit'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    resetrecord();
                    $('#pobtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                    alert(obj.codestatus);
                    if(obj.codestatus == "Sale Order Create Successfully"){
                        // location.reload();
                        window.top.location.href = '<?= admin_url('salesorders/view/'); ?>'+obj.so_id;
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
                    $('#pobtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                }
            });
        });
        $('#slcustomer').change(function(){
            getAddressLis();
        });
        function getAddressLis(){
            var customerID = $('#slcustomer').val();
            if(customerID == ""){
                customerID = localStorage.getItem("slcustomer");
            }
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

            $.ajax({
                url: '<?= admin_url('sales/getaddress'); ?>',
                type: 'POST',
                data: {customerID:customerID,[csrfName]:csrfHash},
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    $('#deliveryaddressid').html(obj.html);
                },
                error: function(jqXHR, textStatus){
                    var errorStatus = jqXHR.status;
                }
            });
        }
        getAddressLis();
        $('#pobtn').click(function(e){
            $('#po_form').submit();
        });


        var $customer = $("#slcustomer");
        $(".hidden_customer_id").val(localStorage.getItem("slcustomer"));
        $customer.change(function (e) {
            localStorage.setItem("slcustomer", $(this).val());
            $(".hidden_customer_id").val($(this).val());
        });
        if ((slcustomer = localStorage.getItem("slcustomer"))) {

            $customer.val(slcustomer).select2({
                minimumInputLength: 1,
                data: [],
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: site.base_url + "customers/getCustomer/" + $(element).val(),
                        dataType: "json",
                        success: function (data) {
                            callback(data[0]);
                            console.log(data);
                        },
                    });
                },
                ajax: {
                    url: site.base_url + "customers/suggestions",
                    dataType: "json",
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            term: term,
                            limit: 10,
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return { results: data.results };
                        } else {
                            return { results: [{ id: "", text: "No Match Found" }] };
                        }
                    },
                },
            });
        } else {
            nsCustomer();
        }
        // hellper function for customer if no localStorage value
        function nsCustomer() {
            $("#slcustomer").select2({
                minimumInputLength: 1,
                ajax: {
                    url: site.base_url + "customers/suggestions",
                    dataType: "json",
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            term: term,
                            limit: 10,
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return { results: data.results };
                        } else {
                            return { results: [{ id: "", text: "No Match Found" }] };
                        }
                    },
                },
            });
        }
        $('#po_number').change(function(){
            var po_number = $(this).val();
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
                csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            $.ajax({
                url: '<?= admin_url('salesorders/pocheck'); ?>',
                type: 'POST',
                data: {po_number:po_number,[csrfName]:csrfHash},
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    if(obj.status){
                        alert(obj.message);
                    }
                },
                error: function(jqXHR, textStatus){
                    var errorStatus = jqXHR.status;
                }
            });
        });


    });
</script>




