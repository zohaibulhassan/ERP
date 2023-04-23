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
        localStorage.setItem('salesorders_supplier_id', $(this).val());      
    });
    $('#suppliers').val(localStorage.getItem('salesorders_supplier_id')).select2({
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
        var items = JSON.parse(localStorage.getItem('salesorders_items'));
        delete items[item_id];
        row.remove();
        if (items.hasOwnProperty(item_id)) {} else {
            localStorage.setItem('salesorders_items', JSON.stringify(items));
            loadItems();
            return;
        }
    });
    /********************Discount One Check Box*****************************/
    $(document).on('ifChanged','.discount_one', function(event) {
        var items = JSON.parse(localStorage.getItem('salesorders_items'));
        var item_id = $(this).data('id');
        items[item_id].row.discount_one_checked = event.target.checked;
        localStorage.setItem('salesorders_items', JSON.stringify(items));
        loadItems();
    });
    /********************Discount Two Check Box*****************************/
    $(document).on('ifChanged','.discount_two', function(event) {
        var items = JSON.parse(localStorage.getItem('salesorders_items'));
        var item_id = $(this).data('id');
        items[item_id].row.discount_two_checked = event.target.checked;
        localStorage.setItem('salesorders_items', JSON.stringify(items));
        loadItems();
    });
    /********************Discount Three Check Box*****************************/
    $(document).on('ifChanged','.discount_three', function(event) {
        var items = JSON.parse(localStorage.getItem('salesorders_items'));
        var item_id = $(this).data('id');
        items[item_id].row.discount_three_checked = event.target.checked;
        localStorage.setItem('salesorders_items', JSON.stringify(items));
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
        var items = JSON.parse(localStorage.getItem('salesorders_items'));
        items[item_id].row.qty = qty;
        localStorage.setItem('salesorders_items', JSON.stringify(items));
        loadItems();
    });
    /**************Update Receving Date********************/
    $('#saledate').change(function(){
        localStorage.setItem('salesorders_saledate', $(this).val());      
    });
    /**************Update Delivery Date********************/
    $('#saledeliverydate').change(function(){
        localStorage.setItem('salesorders_deliverydate', $(this).val());      
    });
    /**************Update Ref No********************/
    $('#refno').change(function(){
        localStorage.setItem('salesorders_refno', $(this).val());      
    });
    /**************Update Warehouse ID********************/
    $('#warehouseid').change(function(){
        localStorage.setItem('salesorders_warehouseid', $(this).val());      
    });
    /**************Update Own Company********************/
    // $('#owncompanies').change(function(){
    //     localStorage.setItem('salesorders_owncompanies', $(this).val());      
    // });
    /**************Update Extra Checkbox********************/
    $('#extras').on('ifChecked', function() {
        localStorage.setItem('salesorders_extra', 1);
        $('#extras-con').slideDown();
    });
    $('#extras').on('ifUnchecked', function() {
        localStorage.removeItem("salesorders_extra");
        $('#extras-con').slideUp();
    });
    /**************Update Order Discount********************/
    $('#order_discount').change(function(){
        localStorage.setItem('salesorders_order_discount', $(this).val());
        loadItems();
    });
    $('#po_number').change(function(){
        localStorage.setItem('salesorders_po_number', $(this).val());
        loadItems();
    });
    /**************Update Order Shipping********************/
    $('#order_shipping').change(function(){
        localStorage.setItem('salesorders_order_shipping', $(this).val());
        loadItems();   
    });
    /**************Update Payment Term********************/
    $('#payment_term').change(function(){
        localStorage.setItem('salesorders_payment_term', $(this).val());      
    });
});
var poitems = {};
function loadItems() {
    $("#po_tb tbody").empty();
    if (localStorage.getItem('salesorders_items')) {
        $('#itemsdata').val(localStorage.getItem('salesorders_items'));
        poitems = JSON.parse(localStorage.getItem('salesorders_items'));
        var total_items_qty = 0;
        var total_items_discount = 0
        var total_items_fed_tax = 0;
        var total_items_product_tax = 0;
        var total_further_tax = 0;
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

            if(pr_tax.type == 2){
                item_tax = pr_tax.rate;
            }
            else{
                item_tax = ((item_real_unit_code/100)*pr_tax.rate)*item_qty;
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
            total_further_tax += parseFloat(item_tax);
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
                html += '<td>';
                    html += '<input class="form-control text-center rquantity" name="quantity[]" type="text" autocomplete="off" tabindex="3" value="' + formatQuantity2(item_qty) + '" id="quantity_'+ item_id +'" data-item_id="'+ item_id +'" >';
                html += '</td>';
                html += '<td class="text-center"><i class="fa fa-times tip podel" id="'+ item_id +'" title="Remove" style="cursor:pointer;"></i></td>';
            html += '</tr>';
            $('#po_tb tbody').append(html);
            $(".discount_one, .discount_two, .discount_three").iCheck({
                checkboxClass: 'icheckbox_square-blue',
                increaseArea: '20%' // optional
            });
            // $("#warehouseid").select2("readonly", true);
            // $("#suppliers").select2("readonly", true);
            total_row++;
            checkno++;
        });
        if(checkno==0){
            // $("#warehouseid").select2("readonly", false);
            // $("#suppliers").select2("readonly", false);
        }
        var order_discount = parseFloat(localStorage.getItem('salesorders_order_discount'));
        var order_shipping = parseFloat(localStorage.getItem('salesorders_order_shipping'));
        totalpaidivoice = total_subtotal-order_discount+order_shipping;

        total_items_qty = formatMoney(total_items_qty);
        total_items_discount = formatMoney(total_items_discount);
        total_items_fed_tax = formatMoney(total_items_fed_tax);
        total_items_product_tax = formatMoney(total_items_product_tax);
        total_further_tax = formatMoney(total_further_tax);
        total_subtotal = formatMoney(total_subtotal);
        order_shipping = formatMoney(order_shipping);
        totalpaidivoice = formatMoney(totalpaidivoice);


        $('#total_order_items').html(total_row+'('+total_items_qty+')');
        $('#total_order_price').html(total_subtotal);
        $('#total_order_discount').html(order_discount);
        $('#order_shipping_div').html(order_shipping);
        $('#order_total_grand').html(totalpaidivoice);

    }
}
function add_purchase_item(item) {
    if(item != null){
        var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
        poitems[item_id] = item;
        // poitems[item_id].order = new Date().getTime();
        localStorage.setItem('salesorders_items', JSON.stringify(poitems));
        loadItems();
        return true;
    }
}
function loaddata(){
    $('#saledate').val(localStorage.getItem('salesorders_saledate'));
    $('#saledeliverydate').val(localStorage.getItem('salesorders_deliverydate'));
    $('#refno').val(localStorage.getItem('salesorders_refno'));
    $('#warehouseid').val(localStorage.getItem('salesorders_warehouseid'));
    $('#warehouseid').select2().trigger('change');
    // $('#owncompanies').val(localStorage.getItem('salesorders_owncompanies'));
    var extra = localStorage.getItem('salesorders_extra');
    if(extra == 1){
        $('#extras-con').slideDown();
        $('#extras').iCheck('check');
    }
    else{
        $('#extras-con').slideUp();
    }
    if(localStorage.getItem('salesorders_order_discount')){
        $('#order_discount').val(localStorage.getItem('salesorders_order_discount'));
    }
    else{
        $('#order_discount').val(0);
    }
    if(localStorage.getItem('salesorders_po_number')){
        $('#po_number').val(localStorage.getItem('salesorders_po_number'));
    }
    else{
        $('#po_number').val('');
    }

    if(localStorage.getItem('salesorders_order_shipping')){
        $('#order_shipping').val(localStorage.getItem('salesorders_order_shipping'));
    }
    else{
        $('#order_shipping').val(0);
    }

    $('#payment_term').val(localStorage.getItem('salesorders_payment_term'));
    $('#order_note').val(localStorage.getItem('salesorders_order_note'));
    if (localStorage.getItem('salesorders_order_note')) {
        $('#order_note').redactor('set', localStorage.getItem('salesorders_order_note'));
    }
    if (localStorage.getItem('salesorders_items')) {
        $('#itemsdata').val(localStorage.getItem('salesorders_items'));
    }
}
function resetrecord(){
    localStorage.removeItem('salesorders_extra');
    localStorage.removeItem('salesorders_refno');
    localStorage.removeItem('salesorders_payment_term');
    localStorage.removeItem('salesorders_po_number');
    localStorage.removeItem('salesorders_items');
    localStorage.removeItem('salesorders_order_note');
    localStorage.removeItem('salesorders_supplier_id');
    localStorage.removeItem('salesorders_warehouseid');
    localStorage.removeItem('salesorders_order_shipping');
    // localStorage.removeItem('salesorders_owncompanies');
    localStorage.removeItem('salesorders_saledate');
}