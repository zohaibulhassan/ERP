<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i>Create Sale</h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'createform');
                echo admin_form_open_multipart("salesorders/created", $attrib);
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="col-md-4">
                            <div class="form-group">
                            <label for="slref">Invoice No</label>
                                <?php echo form_input('reference_no', '', 'class="form-control input-tip" id="slref"'); ?>
                                <input type="hidden" name="so_id" value="<?php echo $so['id'] ?>" >
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("biller", "slbiller"); ?>
                                <?php
                                foreach ($billers as $biller) {
                                    $bl[$biller->id] = $biller->company != '-' ? $biller->company : $biller->name;
                                }
                                echo form_dropdown('biller', $bl, '', 'id="slbiller" data-placeholder="' . lang("select") . ' ' . lang("biller") . '" required="required" class="form-control input-tip select" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("own_companies", "poown_companies"); ?>
                                <?php
                                    $oc[''] = '';
                                    foreach ($own_company as $own_companies) {
                                        $oc[$own_companies->id] = $own_companies->companyname;
                                    }
                                    echo form_dropdown('own_company', $oc, '', 'id="poown_companies" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("own_companies") . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("warehouse", "slwarehouse"); ?>
                                <input type="text" class="form-control" name="warehouse" id="warehouse" value="<?php echo $so['warehouse_name'].'('.$so['warehouse_code'].')'; ?>" readonly>
                                <input type="hidden" name="warehouse_id" id="warehouse_id" value="<?php echo $so['warehouse_id']; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="slcustomer">Customer Type</label>
                                <input type="text" class="form-control" name="customer" id="customername" value="<?php echo $so['customer_name']?>" readonly>
                                <input type="hidden" name="customer_id" id="customer_id" value="<?php echo $so['customer_id']?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="slcustomer">E-taliers</label>
                                <input type="text" class="form-control" name="etalier" id="etaliername" value="<?php echo $so['etalier_name']?>" readonly>
                                <input type="hidden" name="etalier_id" id="etalier_id" value="<?php echo $so['etalier_id']?>" readonly>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="po_number">Delivery Address </label>
                                <?php echo form_input('customer_addres', $so['customer_addres'] == "" ? "Default Address" : $so['customer_addres'], 'class="form-control input-tip" readonly'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="po_number">P.O Number </label>
                                <?php echo form_input('po_number', $so['po_number'], 'class="form-control input-tip" id="po_number"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="po_number">P.O Date </label>
                                <?php echo form_input('po_date', $so['po_date'], 'class="form-control date2" id="podate" required="required" autocomplete="off"'); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("order_items"); ?> *</label>
                                <div class="controls table-controls">
                                    <table id="slTable" class="table items table-striped table-bordered table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th><?= lang('product') . ' (' . lang('code') .' - '.lang('name') . ')'; ?></th>
                                            <th><?= lang("net_unit_price"); ?></th>
                                            <th><?= lang("MRP"); ?></th>
                                            <th><?= lang("quantity"); ?></th>
                                            <th><?= lang("Batch"); ?></th>
                                            <th><?= lang("expiry"); ?></th>
                                            <th><?= lang("Discount One"); ?></th>
                                            <th><?= lang("Discount Two"); ?></th>
                                            <th><?= lang("Discount Three"); ?></th>
                                            <th><?= lang("Discount Three Code"); ?></th>
                                            <th><?= lang("FED TAX"); ?></th>
                                            <th><?= lang("discount"); ?></th>
                                            <th><?= lang("product_tax"); ?></th>
                                            <th>
                                                <?= lang("Further Tax"); ?>
                                                (<span class="currency"><?= $default_currency->code ?></span>)
                                            </th>
                                            <th><?= lang("Adv Tax"); ?></th>
                                            <th>
                                                <?= lang("subtotal"); ?>
                                                (<span class="currency"><?= $default_currency->code ?></span>)
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $totalqty = 0;
                                                $totalfed = 0;
                                                $totalproducttax = 0;
                                                $totalfurthertax = 0;
                                                $totaladvancetax = 0;
                                                $total = 0;
                                                $no = 0;
                                                foreach($so['items'] as $item){
                                                    $totalqty += $item->quantity;
                                                ?>
                                            <tr>
                                                <?php $tax = 0; $d1 = 0; $d2 = 0; $d3 = 0; $d = 0; ?>
                                                <td>
                                                    <?= $item->product_name ?>
                                                    <input type="hidden" id="productid<?= $no; ?>" name="product_id[]" value="<?= $item->product_id ?>" >
                                                    <input type="hidden" id="soc_id<?= $no; ?>" name="soc_id[]" value="<?= $item->id ?>" >
                                                </td>
                                                <td>
                                                    <input type="hidden" id="productprice<?= $no; ?>" name="product_price[]" value="<?= $item->selling_price ?>" >
                                                    <?= $item->selling_price ?>
                                                </td>
                                                <td><?= $item->mrp ?></td>
                                                <td>
                                                    <input type="hidden" id="productquantity<?= $no; ?>" name="productquantity[]" value="<?= $item->quantity ?>" >
                                                    <?= $item->quantity ?>
                                                </td>
                                                <td><?= $item->batch ?></td>
                                                <td><?= $item->expiry ?></td>
                                                <td>
                                                    <input class="discount_one" id="discount_one_chk<?= $no; ?>" name="discount_one[]" type="checkbox" value="<?= $item->discount_one ?>" data-no="<?= $no; ?>" >
                                                    ( <?= $item->discount_one ?>% ) <br> 
                                                    <span id="discount_one_cal<?= $no ?>" >
                                                        <?php
                                                        $d1 = (($item->selling_price/100)*$item->discount_one)*$item->quantity;
                                                        echo decimalallow($d1);
                                                        ?>
                                                    </span>
                                                    <input type="hidden" id="discount_one_amount<?= $no ?>" name="discount_one_amount[]" value="<?= $d1; ?>" >
                                                    <input type="hidden" id="discount_one_rate<?= $no ?>" name="discount_one_rate[]" value="0" >
                                                </td>
                                                <td>
                                                    <input class="discount_two" id="discount_two_chk<?= $no; ?>" name="discount_two[]" type="checkbox" value="<?= $item->discount_two ?>" data-no="<?= $no; ?>" >
                                                    ( <?= $item->discount_two ?>% ) <br> 
                                                    <span id="discount_two_cal<?= $no ?>" >
                                                        <?php
                                                            $d2 = (($item->selling_price/100)*$item->discount_two)*$item->quantity;
                                                            echo decimalallow($d2);
                                                        ?>
                                                    </span>
                                                    <input type="hidden" id="discount_two_amount<?= $no ?>" name="discount_two_amount[]" value="<?= $d2; ?>" >
                                                    <input type="hidden" id="discount_two_rate<?= $no ?>" name="discount_two_rate[]" value="0" >
                                                </td>
                                                <td>
                                                    <input class="discount_three" id="discount_three_chk<?= $no; ?>" name="discount_three[]" type="checkbox" data-value="<?= $item->discount_three; ?>" value="<?= $item->discount_three ?>" data-no="<?= $no; ?>" >
                                                    <span id="discount_three_cal<?= $no ?>" >
                                                        ( <?= $item->discount_three ?>% ) <br> 
                                                        <?php
                                                            $d3 = (($item->selling_price/100)*$item->discount_three)*$item->quantity;
                                                            echo decimalallow($d3);
                                                        ?>
                                                    </span>
                                                    <input type="hidden" id="discount_three_amount<?= $no ?>" name="discount_three_amount[]" value="<?= $d3; ?>" >
                                                    <input type="hidden" id="discount_three_rate<?= $no ?>" name="discount_three_rate[]" value="0" >
                                                </td>
                                                <td>
                                                    <select name="discountselect[]" class="form-control discountselect" data-no="<?= $no ?>">
                                                        <?php
                                                            foreach($item->discounts as $discount){
                                                        ?>
                                                        <option value="<?= $discount->percentage ?>" ><?= $discount->name ?></option>
                                                        <?php
                                                            }
                                                        ?>

                                                    </select>
                                                </td>
                                                <td>
                                                    <?= $item->fed_tax ?>
                                                    <input type="hidden" name="productfed[]" id="productfed<?= $no ?>" value="<?= $item->fed_tax ?>" >
                                                </td>
                                                <td>
                                                    <span id="totaldiscount<?= $no; ?>">
                                                    0.0000
                                                    </span>
                                                </td>
                                                <td>
                                                    (<?= $item->tax_rate_rate ?>) 
                                                    <?php
                                                        $tax = 0;
                                                        if($item->tax_rate_type == 2){
                                                            $tax = $item->tax_rate_rate*$item->quantity;
                                                        }
                                                        else{
                                                            $tax = (($item->selling_price/100)*$item->tax_rate_rate)*$item->quantity;
                                                        }
                                                            echo decimalallow($tax);
                                                           $totalproducttax += $tax;
                                                        ?>
                                                    <input type="hidden" name="producttax[]" id="producttax<?= $no ?>" value="<?= $tax; ?>" >
                                                </td>
                                                <td>
                                                    <?php
                                                        $showtax = $item->settingfurther;
                                                        if($item->customer_gst_no == ""){
                                                            if($tax>0){
                                                                $furtheritem = ($item->selling_price/100)*$item->settingfurther;
                                                            }
                                                            else{
                                                                $furtheritem = 0;
                                                                $showtax = 0;
                                                            }
                                                        }
                                                        else{
                                                            $furtheritem = 0;
                                                            $showtax = 0;
                                                        }
                                                    ?>
                                                    <?= '('.$showtax.')<br>'.$furtheritem ?>
                                                    <?php $totalfurthertax += $furtheritem; ?>
                                                    <input type="hidden" name="productfuthertax[]" id="productfuthertax<?= $no ?>" value="<?= $furtheritem ?>" >
                                                </td>
                                                <td>
                                                    <?php
                                                        if($item->customer_gst_no == ""){
                                                            $advtax = decimalallow(((($item->selling_price*$item->quantity)+$tax)/100)*$item->adv_tax_nonreg,2 );
                                                        }
                                                        else{
                                                            // $advtax = ((($item->selling_price+$item->tax_rate_rate)/100)*$item->adv_tax_reg)*$item->quantity;
                                                            $advtax = decimalallow(((($item->selling_price*$item->quantity)+$tax)/100)*$item->adv_tax_reg,2 );
                                                        }
                                                        echo $advtax;
                                                        $totaladvancetax += $advtax;
                                                    ?>
                                                    <input type="hidden" name="productadvtax[]" id="productadvtax<?= $no ?>" value="<?= $advtax ?>" >
                                                </td>
                                                <td>
                                                    <span id="producttotal<?= $no ?>" >
                                                        <?php 
                                                            //echo $item->selling_price.','.$item->quantity.', '.$tax.', '.$d;
                                                            $subtotal = (($item->selling_price*$item->quantity)+($tax+$furtheritem+$advtax))-$d;
                                                            $total += $subtotal;
                                                        //    echo decimalallow($subtotal);
                                                        ?>
                                                    </span>
                                                    <input type="hidden" name="producttotaltxt[]" id="producttotaltxt<?= $no ?>" value="<?= $subtotal ?>" >
                                                </td>
                                            </tr>
                                            <?php
                                            $no++;
                                                }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" >Total</th>
                                                <th><?= $totalqty; ?></th>
                                                <th colspan="6" ></th>
                                                <th><?= $totalfed; ?></th>
                                                <th id="totaldiscount" >0.0000</th>
                                                <th><?= $totalproducttax; ?></th>
                                                <th><?= $totalfurthertax; ?></th>
                                                <th><?= $totaladvancetax ?></th>
                                                <th id="totalval" ><?= $total; ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("order_discount", "sldiscount"); ?>
                                <?php echo form_input('order_discount', '', 'class="form-control input-tip" id="sodiscounttxt"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("shipping", "slshipping"); ?>
                                <?php echo form_input('shipping', '', 'class="form-control input-tip" id="soshippingtxt"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("payment_term", "slpayment_term"); ?>
                                <?php echo form_input('payment_term', $so['payment_terms'], 'class="form-control " '); ?>
                            </div>
                        </div>


                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="po_number">D.C Number </label>
                                <?php echo form_input('dc_number', '', 'class="form-control input-tip" id="dc_number"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="po_number">Cartdiage </label>
                                <?php echo form_input('cartidiage', '', 'class="form-control input-tip" id="cartidiage"'); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <input type="hidden" name="total_items" value="" id="total_items" required="required"/>
                        <div class="row" id="bt">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang("sale_note", "slnote"); ?>
                                        <?php echo form_textarea('note', '', 'class="form-control" id="slnote" style="margin-top: 10px; height: 100px;"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang("staff_note", "slinnote"); ?>
                                        <?php echo form_textarea('staff_note', '', 'class="form-control" id="slinnote" style="margin-top: 10px; height: 100px;"'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="fprom-group">
                                <button type="button" class="btn btn-primary" id="sobth" style="padding: 6px 15px; margin:15px 0;" ><?= lang('submit') ?></button>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="bottom-total" class="well well-sm" style="margin-bottom: 0;">
                    <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                        <tr class="warning">
                            <td><?= lang('items') ?> <span class="totals_val pull-right" id="soitems">0</span></td>
                            <td><?= lang('total') ?> <span class="totals_val pull-right" id="sototal">0.00</span></td>
                            <td><?= lang('order_discount') ?> <span class="totals_val pull-right" id="sodiscount">0.00</span></td>
                            <td><?= lang('shipping') ?> <span class="totals_val pull-right" id="soshipping">0.00</span></td>
                            <td><?= lang('grand_total') ?> <span class="totals_val pull-right" id="sogtotal">0.00</span></td>
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
        totalcalculate();
        $(document).on('ifChanged','.discount_one,.discount_two,.discount_three', function(event) {
            var no = $(this).data('no');
            var val = $(this).val();
            totalcalculate();
        });
        $(document).on('change','.discountselect',function(){
            var no = $(this).data('no');
            var val = $(this).val();
            var price = $('#productprice'+no).val();
            var qty = $('#productquantity'+no).val();
            var calculatevalue = ((price/100)*val)*qty;
            $('#discount_three_chk'+no).val(val);
            $('#discount_three_cal'+no).html('('+val+')<br>'+calculatevalue);
            totalcalculate();
        });
        function calulate(no){
            var id = null, price = 0, qty = 0, d1 = 0, d2 = 0, d3 = 0, d1_amount = 0, d2_amount = 0, d3_amount = 0, d = 0, total = 0, tax = 0, fedtax = 0, futhertax = 0, total_tax = 0;
            id = $('#productid'+no).val();
            price = $('#productprice'+no).val();
            qty = $('#productquantity'+no).val();
            if($("#discount_one_chk"+no).prop("checked")){
                d1 = $('#discount_one_chk'+no).val();
                $('#discount_one_rate'+no).val(d1);
            }
            else{
                $('#discount_one_rate'+no).val('');
            }
            if($("#discount_two_chk"+no).prop("checked")){
                d2 = $('#discount_two_chk'+no).val();
                $('#discount_two_rate'+no).val(d2);
            }
            else{
                $('#discount_two_rate'+no).val('');
            }
            if($("#discount_three_chk"+no).prop("checked")){
                d3 = $('#discount_three_chk'+no).val();
                $('#discount_three_rate'+no).val(d3);
            }
            else{
                $('#discount_three_rate'+no).val('');
            }
            tax = parseFloat($('#producttax'+no).val());
            fedtax = parseFloat($('#productfed'+no).val());
            futhertax = parseFloat($('#productfuthertax'+no).val());
            advtax = parseFloat($('#productadvtax'+no).val());
            total_tax = tax+fedtax+futhertax+advtax;
            d1_amount = ((price/100)*d1)*qty;
            d2_amount = ((price/100)*d2)*qty;
            d3_amount = ((price/100)*d3)*qty;
            d = d1_amount+d2_amount+d3_amount;
            $('#totaldiscount'+no).html((d == 0) ? '0.0000' : d.toFixed(4) );
            total = ((price*qty)+total_tax)-d;
            $('#producttotal'+no).html((total == 0) ? '0.0000' : total.toFixed(4) );
            $('#producttotaltxt'+no).val((total == 0) ? '0.0000' : total.toFixed(4) );
        }
        $('#sodiscounttxt, #soshippingtxt').change(function(){
            totalcalculate();
        });
        function totalcalculate(){
            var total = 0;
            var discount = 0;
            var length = <?= $no ?>;
            var i = 0;
            for(i=0;i<length;i++){
                calulate(i)
                var pt = parseFloat($('#producttotaltxt'+i).val());
                total += pt;
            }
            $('#totalval').html(total.toFixed(4));
            var discount = $('#sodiscounttxt').val();
            discount = (discount == "") ? parseFloat('0') : parseFloat(discount);
            var shipping = $('#soshippingtxt').val();
            shipping = (shipping == "") ? parseFloat('0') : parseFloat(shipping);
            var gtotal = total-discount+shipping
            $('#soitems').html('9(14)');
            $('#sototal').html(total.toFixed(4));
            $('#sodiscount').html(discount.toFixed(4));
            $('#soshipping').html(shipping.toFixed(4));
            $('#sogtotal').html(gtotal.toFixed(4));
        }
        $('#sobth').click(function(){
            $('#createform').submit();
        });
        $('#createform').submit(function(e){
            e.preventDefault();
            $('#sobth').prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('salesorders/created'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    $('#sobth').prop('disabled', false);
                    $('#ajaxCall').hide();
                    alert(obj.codestatus);
                    if(obj.codestatus == "Sale Create Successfully"){
                        // location.reload();
                        // window.top.location.href = '<?= admin_url('salesorders/view/'); ?>'+obj.purchase_id;
                        window.top.location.href = '<?= admin_url('sales/detail/'); ?>'+obj.sid;
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
                    $('#sobth').prop('disabled', false);
                    $('#ajaxCall').hide();
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
        <?php
            $messgaealert = 'This sale calculate on Consignment (TP) Price!';
            if($so['customer_sales_type'] == "cost"){
                $messgaealert = 'This sale calculate on Cost (DP) Price!';
            }
            else if($so['customer_sales_type'] == "mrp"){
                $messgaealert = 'This sale calculate on Cost (MRP) Price!';
            }
            echo 'alert("'.$messgaealert.'");';
        ?>
    });
</script>