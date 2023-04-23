<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD ?>
<style>
    table span.pull-right {
        padding:0 !important;
    }
</style>
<div id="ajaxCall" style="display: none;"><i class="fa fa-spinner fa-pulse"></i></div>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_purchase'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form','id'=>'po_form2');
                    echo admin_form_open_multipart("purchaseorder/add", $attrib)
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Expected Receiving Date</label>
                                <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : ""), 'class="form-control date" id="recevingdate" required="required" autocomplete="off"'); ?>
                            </div>
                        </div>
                        <!-- <div class="col-md-4">
                            <div class="form-group">
                                <label>Reference No</label>
                                <?php echo form_input('reference_no','', 'class="form-control input-tip" id="refno" readonly'); ?>
                            </div>
                        </div> -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("own_companies", "poown_companies"); ?>
                                <?php
                                $oc[''] = '';
                                foreach ($own_company as $own_companies) {
                                    $oc[$own_companies->id] = $own_companies->companyname;
                                }
                                echo form_dropdown('own_company', $oc, (isset($_POST['own_companies']) ? $_POST['own_companies'] : $Settings->default_warehouse), 'class="form-control input-tip select" id="owncompanies" data-placeholder="' . lang("select") . ' ' . lang("own_companies") . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="document">Attach Document</label>
                                <input id="document" type="file" data-browse-label="Browse ..." name="document" data-show-upload="false" data-show-preview="false" class="form-control file">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="panel panel-warning">
                                <div class="panel-heading"><?= lang('please_select_these_before_adding_product') ?></div>
                                <div class="panel-body" style="padding: 5px;">
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
                                    <div class="col-md-4">
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
                        <div class="col-md-5" id="sticker">
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
                                                <a href="<?= admin_url('products/add') ?>" id="addManually1"><i class="fa fa-2x fa-plus-circle addIcon" id="addIcon"></i></a>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <p style="font-size: 18px;font-weight: bold;line-height: 60px;text-align: center;" >OR</p>
                        </div>
                        <div class="col-md-6">
                                <div class="form-group" style="margin: 7.5px 0;">
                                    <input id="itesfile" type="file" 
                                        data-browse-label="Browse" 
                                        data-upload-label="Get Items" 
                                        name="itemsfile" 
                                        data-show-upload="true" 
                                        data-show-preview="false" 
                                        class="form-control file">
                                    <span><a href="<?php echo $this->config->base_url(); ?>assets/csv/sample_po_items_list.csv">Download Sample</a></span>
                                </div>
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
                                            <th class="col-md-4"><?= lang('product') . ' (' . lang('code') .' - '.lang('name') . ')'; ?></th>
                                            <th class="col-md-1"><?= lang("net_unit_cost"); ?></th>
                                            <th class="col-md-1"><?= lang("MRP"); ?></th>
                                            <th class="col-md-1">Balance Qty</th>
                                            <th class="col-md-1">Alert Qty</th>
                                            <th class="col-md-1"><?= lang("quantity"); ?></th>
                                            <th class="col-md-1"><?= lang("Discount One"); ?></th>
                                            <th class="col-md-1"><?= lang("Discount Two"); ?></th>
                                            <th class="col-md-1"><?= lang("Discount Three"); ?></th>
                                            <th class="col-md-1"><?= lang("FED TAX"); ?></th>
                                            <th class="col-md-1"><?= lang("Discount"); ?></th>
                                            <th class="col-md-1"><?= $this->lang->line("product_tax"); ?></th>
                                            <th><?= lang("subtotal"); ?> (<span class="currency"><?= $default_currency->code ?></span>)</th>
                                            <th style="width: 30px !important; text-align: center;"><i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i></th>
                                            <th style="width: 30px !important; text-align: center;"><i class="fa fa-ban" style="opacity:0.5; filter:alpha(opacity=50);"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="checkbox" class="checkbox" id="extras" value=""/>
                                <label for="extras" class="padding05"><?= lang('more_options') ?></label>
                            </div>
                            <div class="row" id="extras-con" style="display: none;">
                                <?php if ($Settings->tax1) { ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang('order_tax', 'order_tax') ?>
                                            <?php
                                            $tr[""] = "";
                                            foreach ($tax_rates as $tax) {
                                                $tr[$tax->id] = $tax->name;
                                            }
                                            echo form_dropdown('order_tax', $tr, "", 'id="order_tax" class="form-control input-tip select" style="width:100%;" ');
                                            ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang("discount_label", "order_discount"); ?>
                                        <?php echo form_input('discount', '', 'class="form-control input-tip" id="order_discount"'); ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang("shipping", "order_shipping"); ?>
                                        <?php echo form_input('shipping', '', 'class="form-control input-tip" id="order_shipping"'); ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang("payment_term", "payment_term"); ?>
                                        <?php echo form_input('payment_term', '', 'class="form-control tip" data-trigger="focus" data-placement="top" title="' . lang('payment_term_tip') . '" id="payment_term"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group">
                                <?= lang("note", "order_note"); ?>
                                <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control" id="order_note" style="margin-top: 10px; height: 100px;"'); ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="from-group">
                                <button type="submit" class="btn btn-primary" id="pobtn2">Submit</button>
                                <button type="button" class="btn btn-danger" id="resetbtn"><?= lang('reset') ?></button>
                                <button type="button" class="btn btn-warning" id="alertQtybtn">Get Alert Qty Items</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="well well-sm" style="margin-top: 15px;">
                    <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                        <tr class="warning">
                            <td>Items <span class="pull-right" id="total_order_items" >0</span></td>
                            <td>Total <span class="pull-right" id="total_order_price" >0.00</span></td>
                            <td>Order Discount<span class="pull-right" id="total_order_discount" >0.00</span></td>
                            <td>Shippng <span class="pull-right" id="order_shipping_div" >0.00</span></td>
                            <td>Grand Total <span class="pull-right" id="order_total_grand">0.00</span></td>
                        </tr>
                    </table>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
    $(document).ready(function(){

        // localStorage.setItem('purchaseorder_refno', '<?= $generate_ref ?>');

        $(document).on('click', '.podeactiva', function() {
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            var row = $(this).closest('tr');
            var item_id = row.attr('data-item-id');


            swal({
                title: "Are you sure?",
                text: "Do you want to deactivate this product",
                icon: "warning",
                buttons: true,
                successMode: true,
                confirmButtonText: 'Deactivate',
            })
            .then(id => {
                if (!id) throw null;
                <?php 
                    $url = base_url('admin/purchaseorder/deactivateproduct?pid=');
                 ?>
                return fetch("<?php echo $url; ?>"+item_id+"&"+[csrfName]+"="+csrfHash);
            })
            .then(results => {
                return results.json();
            })
            .then(json => {
                if(json.codestatus ==  "ok"){
                    swal("Product Deactivate Successfully", {
                        icon: "success",
                    });
                    var items = JSON.parse(localStorage.getItem('purchaseorder_items'));
                    delete items[item_id];
                    row.remove();
                    if (items.hasOwnProperty(item_id)) {} else {
                        localStorage.setItem('purchaseorder_items', JSON.stringify(items));
                        loadItems();
                        return;
                    }
                }
                else{
                    swal("Error!", json.codestatus, "error");
                }
            })
            .catch(err => {
                if (err) {
                    console.log(err);
                    swal("Oh noes!", "The AJAX request failed!", "error");
                }
                else {
                    swal.stopLoading();
                    swal.close();
                }
            });




        });



        $('#alertQtybtn').click(function(){
            $("#alertQtybtn").prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                type: 'get',
                url: '<?= admin_url('purchaseorder/alertqty'); ?>',
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
                    url: '<?= admin_url('purchaseorder/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        supplier_id: $("#suppliers").val(),
                        warehouse_id: $("#warehouseid").val(),
                        own_company: $("#owncompanies").val()
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
                url: '<?= admin_url('purchaseorder/gettex'); ?>',
                data: {[csrfName]: csrfHash,'id':$(this).val()},
                success: function (data) {
                    localStorage.setItem('purchaseorder_order_tax', data);    
                    loadItems();  
                }
            });
        });
        /***********Submit Purchase Order************/
        $('#po_form').submit(function(e){
            e.preventDefault();

            var file_data = $('#document').prop('files')[0];   
            var form_data = new FormData(this);                  
            form_data.append('document', file_data);

            $('#pobtn').prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('purchaseorder/submit'); ?>',
                type: 'POST',
                data: form_data,
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    resetrecord();
                    $('#pobtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                    alert(obj.codestatus);
                    if(obj.codestatus == "Purchase Order Create Successfully"){
                        // location.reload();
                        window.top.location.href = '<?= admin_url('purchaseorder/view/'); ?>'+obj.purchase_id;
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
        $('#pobtn').click(function(e){
            $('#po_form').submit();
        });
        $('#po_form2').on('click','.kv-fileinput-upload',function(){
            $.ajax({
                url: '<?= admin_url('purchaseorder/bulkuploaditem'); ?>',
                type: 'POST',
                data: new FormData(document.getElementById("po_form2")),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    if(obj.codestatus == "ok"){
                        var i = 0;
                        for(i=0;i<obj.products.length;i++){
                            if(obj.products[i].id != 0){
                                add_purchase_item(obj.products[i]);
                            }
                            else{
                                // alert(obj.products[i].label);
                            }
                        }
                        if(obj.errors.prducts.length > 0){
                            var text = "Prodcut ID: "
                            obj.errors.prducts.forEach(function(item, index){
                                text += item+", "
                            });
                            text += "Not add in PO list"
                            alert(text);
                        }
                        $("#alertQtybtn").prop('disabled', false);
                        $('#ajaxCall').hide();
                    }
                    else{
                        alert(obj.codestatus);
                    }
                    // $('.fileinput-remove-button').click();
                    // $(this).fileinput('clear');
                    $('#ajaxCall').hide();
                },
                error: function(jqXHR, textStatus){
                    var errorStatus = jqXHR.status;
                    if(errorStatus==0){ 
                        console.log('Internet Connection Problem');
                    }
                    else{
                        console.log('Try Again. Error Code '+errorStatus);
                    }
                    $('#ajaxCall').hide();
                    // $('.fileinput-remove-button').click();
                    // $(this).fileinput('clear');
                }
            });
        });

    });
</script>




