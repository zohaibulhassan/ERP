$(document).ready(function() {
    /*****************Supplier************************/
    $('#suppliers').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "suppliers/suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function(term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function(data, page) {
                if (data.results != null) {
                    return { results: data.results };
                } else {
                    return { results: [{ id: '', text: 'No Match Found' }] };
                }
            }
        }
    });
    $('#suppliers').change(function(){
        localStorage.setItem('purchaseorder_supplier_id', $(this).val());      
    });
    $('#suppliers').val(localStorage.getItem('purchaseorder_supplier_id')).select2({
        minimumInputLength: 1,
        data: [],
        initSelection: function(element, callback) {
            $.ajax({
                type: "get",
                async: false,
                url: site.base_url + "suppliers/getSupplier/" + $(element).val(),
                dataType: "json",
                success: function(data) {
                    callback(data[0]);
                }
            });
        },
        ajax: {
            url: site.base_url + "suppliers/suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function(term, page) {
                return {
                    term: term,
                    limit: 15
                };
            },
            results: function(data, page) {
                if (data.results != null) {
                    return { results: data.results };
                } else {
                    return { results: [{ id: '', text: 'No Match Found' }] };
                }
            }
        }
    });
    /*****************Load Item and Data************************/
    loaddata();
    loadItems();
    /*****************Remove Item************************/
    $(document).on('click', '.podel', function() {
        var row = $(this).closest('tr');
        var item_id = row.attr('data-item-id');
        var items = JSON.parse(localStorage.getItem('purchaseorder_items'));
        delete items[item_id];
        row.remove();
        if (items.hasOwnProperty(item_id)) {} else {
            localStorage.setItem('purchaseorder_items', JSON.stringify(items));
            loadItems();
            return;
        }
    });
    /********************Discount One Check Box*****************************/
    $(document).on('ifChanged','.discount_one', function(event) {
        var items = JSON.parse(localStorage.getItem('purchaseorder_items'));
        var item_id = $(this).data('id');
        items[item_id].row.discount_one_checked = event.target.checked;
        localStorage.setItem('purchaseorder_items', JSON.stringify(items));
        loadItems();
    });
    /********************Discount Two Check Box*****************************/
    $(document).on('ifChanged','.discount_two', function(event) {
        var items = JSON.parse(localStorage.getItem('purchaseorder_items'));
        var item_id = $(this).data('id');
        items[item_id].row.discount_two_checked = event.target.checked;
        localStorage.setItem('purchaseorder_items', JSON.stringify(items));
        loadItems();
    });
    /********************Discount Three Check Box*****************************/
    $(document).on('ifChanged','.discount_three', function(event) {
        var items = JSON.parse(localStorage.getItem('purchaseorder_items'));
        var item_id = $(this).data('id');
        items[item_id].row.discount_three_checked = event.target.checked;
        localStorage.setItem('purchaseorder_items', JSON.stringify(items));
        loadItems();
    });
    /*****************************Reset****************************/
    $('#resetbtn').click(function(){
        resetrecord();
        location.reload();
    });
    /**************Update Qty********************/
    $(document).on('change','.rquantity', function(event) {
        var qty = $(this).val();
        var item_id = $(this).data('item_id');
        var items = JSON.parse(localStorage.getItem('purchaseorder_items'));
        items[item_id].row.qty = qty;
        localStorage.setItem('purchaseorder_items', JSON.stringify(items));
        loadItems();
    });
    /**************Update Receving Date********************/
    $('#recevingdate').change(function(){
        localStorage.setItem('purchaseorder_recevingdate', $(this).val());      
    });
    /**************Update Ref No********************/
    // $('#refno').change(function(){
    //     localStorage.setItem('purchaseorder_refno', $(this).val());      
    // });
    /**************Update Warehouse ID********************/
    $('#warehouseid').change(function(){
        localStorage.setItem('purchaseorder_warehouseid', $(this).val());      
    });
    /**************Update Own Company********************/
    $('#owncompanies').change(function(){
        localStorage.setItem('purchaseorder_owncompanies', $(this).val());      
    });
    /**************Update Extra Checkbox********************/
    $('#extras').on('ifChecked', function() {
        localStorage.setItem('purchaseorder_extra', 1);
        $('#extras-con').slideDown();
    });
    $('#extras').on('ifUnchecked', function() {
        localStorage.removeItem("purchaseorder_extra");
        $('#extras-con').slideUp();
    });
    /**************Update Order Discount********************/
    $('#order_discount').change(function(){
        localStorage.setItem('purchaseorder_order_discount', $(this).val());
        loadItems();
    });
    /**************Update Order Shipping********************/
    $('#order_shipping').change(function(){
        localStorage.setItem('purchaseorder_order_shipping', $(this).val());
        loadItems();   
    });
    /**************Update Payment Term********************/
    $('#payment_term').change(function(){
        localStorage.setItem('purchaseorder_payment_term', $(this).val());      
    });
    /**************Update Order Note********************/
    // $('#order_note').redactor('destroy');
    // $('#order_note').redactor({
    //     buttons: ['formatting', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'bold', 'italic', 'underline', '|', 'unorderedlist', 'orderedlist', '|', 'link', '|', 'html'],
    //     formattingTags: ['p', 'pre', 'h3', 'h4'],
    //     minHeight: 100,
    //     changeCallback: function(e) {
    //         var v = this.get();
    //         localStorage.setItem('purchaseorder_order_note', v);
    //     }
    // });
});
var poitems = {};
function loadItems() {
    $("#po_tb tbody").empty();
    if (localStorage.getItem('purchaseorder_items')) {
        $('#itemsdata').val(localStorage.getItem('purchaseorder_items'));
        poitems = JSON.parse(localStorage.getItem('purchaseorder_items'));
        var total_items_qty = 0;
        var total_items_discount = 0
        var total_items_fed_tax = 0;
        var total_items_product_tax = 0;
        var total_subtotal = 0;
        var total_row = 0;
        var checkno = 0;
        $.each(poitems, function() {
            var item = this;
            var item_id = item.id;
            var item_code = item.row.code;
            var item_name = item.row.name.replace(/"/g, "&#034;").replace(/'/g, "&#039;");
            var item_supplier_part_no = item.row.supplier_part_no ? item.row.supplier_part_no : '';
            var item_real_unit_code = item.row.real_unit_cost;
            var item_mrp = item.row.mrp;
            var item_qty = item.row.qty;
            var warehouse_qty = item.row.warehouse_qty;
            var alert_quantity = item.row.alert_quantity;
            var item_d1_check = item.row.discount_one_checked;
            var item_d2_check = item.row.discount_two_checked;
            var item_d3_check = item.row.discount_three_checked;

            var item_d1 = item.row.discount_one;
            var item_d2 = item.row.discount_two;
            var item_d3 = item.row.discount_three;

            var item_consiment = item.row.consiment;
            var item_d1_amount = ((item_consiment*item_d1) / 100) * item_qty;
            var item_d2_amount = ((item_consiment*item_d2) / 100) * item_qty;
            var item_d3_amount = ((item_consiment*item_d3) / 100) * item_qty;

            var total_discount = 0;
            if(item_d1_check == true){
                total_discount += item_d1_amount;
            }
            if(item_d2_check == true){
                total_discount += item_d2_amount;
            }
            if(item_d3_check == true){
                total_discount += item_d3_amount;
            }
            var item_fed_tax_rate = item.row.fed_tax_rate ? item.row.fed_tax_rate : 0
            var item_further_tax = item.row.further_tax_rate ? item.row.further_tax_rate : 0
            var item_tax_method = item.row.tax_method;

            var pr_tax = item.tax_rate;
            var pr_tax_val = pr_tax_rate = 0;

            var item_tax = 0;
            var taxshowrate = '0%'; 

            if(pr_tax.type == 2){
                item_tax = pr_tax.rate*item_qty;
                taxshowrate = pr_tax.rate;
            }
            else{
                item_tax = ((item_real_unit_code/100)*pr_tax.rate)*item_qty;
                taxshowrate = pr_tax.rate+'%'; 
            }
            item_tax = parseFloat(item_tax);
            
            var subtotal = item_real_unit_code*item_qty;
            subtotal = subtotal+item_tax;
            subtotal = subtotal-total_discount;
            subtotal = subtotal+(parseFloat(item_fed_tax_rate)*item_qty);
            
            total_items_qty += parseFloat(item_qty);
            total_items_discount += parseFloat(total_discount);
            total_items_fed_tax += parseFloat(item_fed_tax_rate);
            total_items_product_tax += parseFloat(item_tax);
            total_subtotal += parseFloat(subtotal);

            item_tax = formatMoney(item_tax);
            item_d1_amount = formatMoney(item_d1_amount);
            item_d2_amount = formatMoney(item_d2_amount);
            item_d3_amount = formatMoney(item_d3_amount);
            total_discount = formatMoney(total_discount);
            subtotal = formatMoney(subtotal);
            var html = '<tr id="row_'+ item_id +'" class="row_'+ item_id +'" data-item-id="'+ item_id +'">';
                html += '<td>';
                    html += '<span>'+item_code+' - '+item_name+'<span class="label label-default">'+item_supplier_part_no+'</span></span>';
                html += '</td>';
                html += '<td class="text-right">';
                    html += '<span class="text-right scost" id="scost_'+ item_id +'">' + formatMoney(item_real_unit_code) + '</span>';
                html += '</td>';
                html += '<td class="text-right">';
                    html += '<span class="text-right scost" id="scost_'+ item_id +'">' + formatMoney(item_mrp) + '</span>';
                html += '</td>';
                html += '<td class="text-right" >';
                    html += formatQuantity2(warehouse_qty);
                html += '</td>';
                html += '<td class="text-right" >';
                    html += formatQuantity2(alert_quantity);
                html += '</td>';
                html += '<td>';
                    html += '<input class="form-control text-center rquantity" name="quantity[]" type="text" autocomplete="off" tabindex="3" value="' + formatQuantity2(item_qty) + '" id="quantity_'+ item_id +'" data-item_id="'+ item_id +'" >';
                html += '</td>';
                html += '<td>';
                    html += '<input id="discount_one_' + item_id + '"  class="discount_one" name="discount_one[]"' + (((item_d1_check == true) ? "checked" : "")) + ' type="checkbox" value="'+item_d1+'" onClick="this.select();"  data-id="' + item_id + '" data-item="' + item_id + '" > ( '+item_d1+'% ) <br> ' + item_d1_amount;
                html += '</td>';

                html += '<td>';
                    html += '<input id="discount_two_'+ item_id +'" class="discount_two" name="discount_two[]"' + (((item_d2_check == true) ? "checked" : "")) + ' type="checkbox" value="'+item_d2+'" onclick="this.select();" data-id="'+ item_id +'" data-item="'+ item_id +'"> ( '+item_d2+'% ) <br> ' + item_d2_amount;
                html += '</td>';
                html += '<td>';
                    html += '<input id="discount_three_'+ item_id +'" class="discount_three" name="discount_three[]"' + (((item_d3_check == true) ? "checked" : "")) + ' type="checkbox" value="'+item_d3+'" onclick="this.select();" data-id="'+ item_id +'" data-item="'+ item_id +'"> ( '+item_d3+'% ) <br> ' + item_d3_amount;
                html += '</td>';
                html += '<td>';
                    html += '<input id="fed_tax_'+ item_id +'" class="form-control input-sm text-right fed_tax" name="fed_tax[]" type="text" autocomplete="off" value="'+item_fed_tax_rate+'" onclick="this.select();" data-id="'+ item_id +'" data-item="'+ item_id +'" readonly="">';
                html += '</td>';
                html += '<td class="text-right">';
                    html += '<span class="text-right sdiscount text-danger" id="sdiscount_'+ item_id +'">-'+total_discount+'</span>';
                html += '</td>';
                html += '<td class="text-right">';
                    html += '<span class="text-right sproduct_tax" id="sproduct_tax_'+ item_id +'">('+taxshowrate+')<br>'+item_tax+'</span>';
                html += '</td>';
                html += '<td class="text-right">';
                    html += '<span class="text-right ssubtotal" id="subtotal_'+ item_id +'">'+subtotal+'</span>';
                html += '</td>';
                html += '<td class="text-center">';
                html += '<i class="fa fa-times tip podel" id="'+ item_id +'" title="Remove" style="cursor:pointer;"></i>';
                html += '</td>';
                html += '<td class="text-center">';
                html += '<i class="fa fa-ban tip podeactiva" id="'+ item_id +'" data-pid="'+ item_id +'" title="Deactive" style="cursor:pointer;"></i>';
                html += '</td>';
            html += '</tr>';
            $('#po_tb tbody').append(html);
            $(".discount_one, .discount_two, .discount_three").iCheck({
                checkboxClass: 'icheckbox_square-blue',
                increaseArea: '20%' // optional
            });
            $("#warehouseid").select2("readonly", true);
            $("#suppliers").select2("readonly", true);
            total_row++;
            checkno++;
        });
        if(checkno==0){
            $("#warehouseid").select2("readonly", false);
            $("#suppliers").select2("readonly", false);
        }
        var order_discount = parseFloat(localStorage.getItem('purchaseorder_order_discount'));
        var order_shipping = parseFloat(localStorage.getItem('purchaseorder_order_shipping'));
        var order_tax = 0;
        if(localStorage.getItem('purchaseorder_order_tax')){
            var order_tax_detail =  JSON.parse(localStorage.getItem('purchaseorder_order_tax'));
            if(order_tax_detail.type == 2){
                order_tax = order_tax_detail.rate;
            }
            else if(order_tax_detail.type == 1){
                order_tax = (total_subtotal/100)*order_tax_detail.rate;
            }
        }
        order_tax = parseFloat(order_tax);
        totalpaidivoice = total_subtotal-order_discount+order_shipping+order_tax;

        total_items_qty = formatMoney(total_items_qty);
        total_items_discount = formatMoney(total_items_discount);
        total_items_fed_tax = formatMoney(total_items_fed_tax);
        total_items_product_tax = formatMoney(total_items_product_tax);
        total_subtotal = formatMoney(total_subtotal);
        order_shipping = formatMoney(order_shipping);
        totalpaidivoice = formatMoney(totalpaidivoice);


        var fhtml = "<tr>";
            fhtml += "<th>Total</th>";
            fhtml += "<th></th>";
            fhtml += "<th></th>";
            fhtml += "<th></th>";
            fhtml += "<th></th>";
            fhtml += "<th>"+total_items_qty+"</th>";
            fhtml += "<th></th>";
            fhtml += "<th></th>";
            fhtml += "<th></th>";
            fhtml += "<th>"+total_items_fed_tax+"</th>";
            fhtml += "<th>"+total_items_discount+"</th>";
            fhtml += "<th>"+total_items_product_tax+"</th>";
            fhtml += "<th>"+total_subtotal+"</th>";
            fhtml += "<th></th>";
        fhtml += "</tr>";
        $('#po_tb tfoot').html(fhtml);
        $('#total_order_items').html(total_row+'('+total_items_qty+')');
        $('#total_order_price').html(total_subtotal);
        $('#total_order_discount').html(order_discount);
        $('#order_shipping_div').html(order_shipping);
        $('#order_total_grand').html(totalpaidivoice);

    }
}
function add_purchase_item(item) {
    if(item != null){
        var item_id = item.item_id;
        // var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
        poitems[item_id] = item;
        // poitems[item_id].order = new Date().getTime();
        localStorage.setItem('purchaseorder_items', JSON.stringify(poitems));
        loadItems();
        return true;
    }
}
function loaddata(){
    $('#recevingdate').val(localStorage.getItem('purchaseorder_recevingdate'));
    // $('#refno').val(localStorage.getItem('purchaseorder_refno'));
    $('#warehouseid').val(localStorage.getItem('purchaseorder_warehouseid'));
    $('#warehouseid').select2().trigger('change');
    $('#owncompanies').val(localStorage.getItem('purchaseorder_owncompanies'));
    var extra = localStorage.getItem('purchaseorder_extra');
    if(extra == 1){
        $('#extras-con').slideDown();
        $('#extras').iCheck('check');
    }
    else{
        $('#extras-con').slideUp();
    }
    if(localStorage.getItem('purchaseorder_order_tax')){
        $ordertax = JSON.parse(localStorage.getItem('purchaseorder_order_tax'));
        $('#order_tax').val($ordertax['id']);
        $('#order_tax').select2().trigger('change');
    }
    if(localStorage.getItem('purchaseorder_order_discount')){
        $('#order_discount').val(localStorage.getItem('purchaseorder_order_discount'));
    }
    else{
        $('#order_discount').val(0);
    }

    if(localStorage.getItem('purchaseorder_order_shipping')){
        $('#order_shipping').val(localStorage.getItem('purchaseorder_order_shipping'));
    }
    else{
        $('#order_shipping').val(0);
    }

    $('#payment_term').val(localStorage.getItem('purchaseorder_payment_term'));
    $('#order_note').val(localStorage.getItem('purchaseorder_order_note'));
    if (localStorage.getItem('purchaseorder_order_note')) {
        $('#order_note').redactor('set', localStorage.getItem('purchaseorder_order_note'));
    }
    if (localStorage.getItem('purchaseorder_items')) {
        $('#itemsdata').val(localStorage.getItem('purchaseorder_items'));
    }
}
function resetrecord(){
    localStorage.removeItem('purchaseorder_extra');
    // localStorage.removeItem('purchaseorder_refno');
    localStorage.removeItem('purchaseorder_payment_term');
    localStorage.removeItem('purchaseorder_order_discount');
    localStorage.removeItem('purchaseorder_items');
    localStorage.removeItem('purchaseorder_order_tax');
    localStorage.removeItem('purchaseorder_order_note');
    localStorage.removeItem('purchaseorder_supplier_id');
    localStorage.removeItem('purchaseorder_warehouseid');
    localStorage.removeItem('purchaseorder_order_shipping');
    localStorage.removeItem('purchaseorder_owncompanies');
    localStorage.removeItem('purchaseorder_recevingdate');
}