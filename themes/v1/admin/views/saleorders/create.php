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
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'createform');
                    echo admin_form_open_multipart("#", $attrib);
                ?>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Invoice No <span class="red" >*</span></label>
                                <input type="text" name="reference_no" class="md-input md-input-success label-fixed" required value="">
                                <input type="hidden" name="so_id" value="<?php echo $so['id'] ?>" >
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
                                <label>Warehouse <span class="red" >*</span></label>
                                <input type="text" name="warehouse" id="warehouse" class="md-input md-input-success label-fixed" required readonly value="<?php echo $so['warehouse_name'].'('.$so['warehouse_code'].')'; ?>">
                                <input type="hidden" name="warehouse_id" id="warehouse_id" value="<?php echo $so['warehouse_id']; ?>" readonly>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Customer <span class="red" >*</span></label>
                                <input type="text" name="customer" id="customername" class="md-input md-input-success label-fixed" required readonly value="<?php echo $so['customer_name']; ?>">
                                <input type="hidden" name="customer_id" id="customer_id" value="<?php echo $so['customer_id']; ?>" readonly>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>P.O Number <span class="red" >*</span></label>
                                <input type="text" name="po_number" class="md-input md-input-success label-fixed" id="po_number" required value="<?php echo $so['po_number']; ?>">
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>P.O Date</label>
                                <input class="md-input  label-fixed" type="text" name="po_date" data-uk-datepicker="{format:'YYYY-MM-DD'}" autocomplete="off" value="<?php echo $so['po_date']?>" readonly required >
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:50px">
                        <div class="dt_colVis_buttons"></div>
                        <table class="uk-table"  style="width:100%" id="dt_tableExport">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Net Unit Price</th>
                                    <th>MRP</th>
                                    <th>Quantity</th>
                                    <th>Batch</th>
                                    <th>Expiry</th>
                                    <th>Discount One</th>
                                    <th>Discount Two</th>
                                    <th>Discount Three</th>
                                    <th>Discount Three Code</th>
                                    <th>FED Tax</th>
                                    <th>Discount</th>
                                    <th>Prodcut Tax</th>
                                    <th>Further Tax</th>
                                    <th>Advance Tax</th>
                                    <th>Subtotal</th>
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
                                                <select name="discountselect[]" class="form-control discountselect" data-no="<?= $no ?>" style="width: 100%;padding: 5px 5px;">
                                                <option value="<?php echo $item->discount_three; ?>">Select Bulk Discount</option>
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
                                                <?php
                                                    if($so['customer_sales_type'] != "mrp"){
                                                        ?>
                                                        (<?= $item->tax_rate_rate ?>) 
                                                        <?php
                                                            $tax = 0;
                                                            if($item->tax_rate_type == 2){
                                                                $tax = $item->tax_rate_rate*$item->quantity;
                                                            }
                                                            else{
                                                                $tax = (($item->selling_price/100)*$item->tax_rate_rate)*$item->quantity;
                                                            }
                                                        ?>
                                                        <?php
                                                    }
                                                    else{
                                                        $tax = 0;
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
                                                            $furtheritem = (($item->selling_price/100)*$item->settingfurther)*$item->quantity;
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
                                <label>payment term </label>
                                <input type="text" name="payment_term" class="md-input md-input-success label-fixed"  value="<?php echo $so['payment_terms']; ?>">
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
        $('#dt_tableExport').DataTable({
            fixedColumns:   {left: 0,right: 0},
            scrollX: false,
            searching:false,
            paging :false
        });
    });
</script>
<script>
    $(document).ready(function(){
        totalcalculate();
        $(document).on('click','.discount_one,.discount_two,.discount_three', function(event) {
            var no = $(this).data('no');
            var val = $(this).val();
            totalcalculate();
        });
        // $(document).on('ifChanged','.discount_one,.discount_two,.discount_three', function(event) {
        //     var no = $(this).data('no');
        //     var val = $(this).val();
        //     totalcalculate();
        // });
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
            console.log(total);
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


