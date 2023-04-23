var net_unit_price;
var customer_type;
var gst_no;
var batch_expiry_date;
var check_select_batch;
var selected_item_id;
var select_purchase_id;
var select_net_unit_price;
var select_index = 0;
var further_tax;


$(document).ready(function(e) {
    $('body a, body button').attr('tabindex', -1);
    check_add_item_val();
    if (site.settings.set_focus != 1) {
        $('#add_item').focus();
    }
    var $customer = $('#slcustomer');
    $customer.change(function(e) {
        localStorage.setItem('slcustomer', $(this).val());
        //$('#slcustomer_id').val($(this).val());
    });
    if (slcustomer = localStorage.getItem('slcustomer')) {
        $customer.val(slcustomer).select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function(element, callback) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: site.base_url + "customers/getCustomer/" + $(element).val(),
                    dataType: "json",
                    success: function(data) {
                        callback(data[0]);
                    }
                });
            },
            ajax: {
                url: site.base_url + "customers/suggestions",
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
    } else {
        nsCustomer();
    }

    // Order level shipping and discount localStorage
    if (sldiscount = localStorage.getItem('sldiscount')) {
        $('#sldiscount').val(sldiscount);
    }
    $('#sltax2').change(function(e) {
        localStorage.setItem('sltax2', $(this).val());
        $('#sltax2').val($(this).val());
    });
    if (sltax2 = localStorage.getItem('sltax2')) {
        $('#sltax2').select2("val", sltax2);
    }
    $('#slsale_status').change(function(e) {
        localStorage.setItem('slsale_status', $(this).val());
    });
    if (slsale_status = localStorage.getItem('slsale_status')) {
        $('#slsale_status').select2("val", slsale_status);
    }
    $('#slpayment_status').change(function(e) {
        var ps = $(this).val();
        localStorage.setItem('slpayment_status', ps);
        if (ps == 'partial' || ps == 'paid') {
            if (ps == 'paid') {
                $('#amount_1').val(formatDecimal(parseFloat(((total + invoice_tax) - order_discount) + shipping)));
            }
            $('#payments').slideDown();
            $('#pcc_no_1').focus();
        } else {
            $('#payments').slideUp();
        }
    });
    if (slpayment_status = localStorage.getItem('slpayment_status')) {
        $('#slpayment_status').select2("val", slpayment_status);
        var ps = slpayment_status;
        if (ps == 'partial' || ps == 'paid') {
            $('#payments').slideDown();
            $('#pcc_no_1').focus();
        } else {
            $('#payments').slideUp();
        }
    }

    $(document).on('change', '.paid_by', function() {
        var p_val = $(this).val();
        localStorage.setItem('paid_by', p_val);
        $('#rpaidby').val(p_val);
        if (p_val == 'cash' || p_val == 'other') {
            $('.pcheque_1').hide();
            $('.pcc_1').hide();
            $('.pcash_1').show();
            $('#payment_note_1').focus();
        } else if (p_val == 'CC') {
            $('.pcheque_1').hide();
            $('.pcash_1').hide();
            $('.pcc_1').show();
            $('#pcc_no_1').focus();
        } else if (p_val == 'Cheque') {
            $('.pcc_1').hide();
            $('.pcash_1').hide();
            $('.pcheque_1').show();
            $('#cheque_no_1').focus();
        } else {
            $('.pcheque_1').hide();
            $('.pcc_1').hide();
            $('.pcash_1').hide();
        }
        if (p_val == 'gift_card') {
            $('.gc').show();
            $('.ngc').hide();
            $('#gift_card_no').focus();
        } else {
            $('.ngc').show();
            $('.gc').hide();
            $('#gc_details').html('');
        }
    });

    if (paid_by = localStorage.getItem('paid_by')) {
        var p_val = paid_by;
        $('.paid_by').select2("val", paid_by);
        $('#rpaidby').val(p_val);
        if (p_val == 'cash' || p_val == 'other') {
            $('.pcheque_1').hide();
            $('.pcc_1').hide();
            $('.pcash_1').show();
            $('#payment_note_1').focus();
        } else if (p_val == 'CC') {
            $('.pcheque_1').hide();
            $('.pcash_1').hide();
            $('.pcc_1').show();
            $('#pcc_no_1').focus();
        } else if (p_val == 'Cheque') {
            $('.pcc_1').hide();
            $('.pcash_1').hide();
            $('.pcheque_1').show();
            $('#cheque_no_1').focus();
        } else {
            $('.pcheque_1').hide();
            $('.pcc_1').hide();
            $('.pcash_1').hide();
        }
        if (p_val == 'gift_card') {
            $('.gc').show();
            $('.ngc').hide();
            $('#gift_card_no').focus();
        } else {
            $('.ngc').show();
            $('.gc').hide();
            $('#gc_details').html('');
        }
    }

    if (gift_card_no = localStorage.getItem('gift_card_no')) {
        $('#gift_card_no').val(gift_card_no);
    }
    $('#gift_card_no').change(function(e) {
        localStorage.setItem('gift_card_no', $(this).val());
    });

    if (amount_1 = localStorage.getItem('amount_1')) {
        $('#amount_1').val(amount_1);
    }
    $('#amount_1').change(function(e) {
        localStorage.setItem('amount_1', $(this).val());
    });

    if (paid_by_1 = localStorage.getItem('paid_by_1')) {
        $('#paid_by_1').val(paid_by_1);
    }
    $('#paid_by_1').change(function(e) {
        localStorage.setItem('paid_by_1', $(this).val());
    });

    if (pcc_holder_1 = localStorage.getItem('pcc_holder_1')) {
        $('#pcc_holder_1').val(pcc_holder_1);
    }
    $('#pcc_holder_1').change(function(e) {
        localStorage.setItem('pcc_holder_1', $(this).val());
    });

    if (pcc_type_1 = localStorage.getItem('pcc_type_1')) {
        $('#pcc_type_1').select2("val", pcc_type_1);
    }
    $('#pcc_type_1').change(function(e) {
        localStorage.setItem('pcc_type_1', $(this).val());
    });

    if (pcc_month_1 = localStorage.getItem('pcc_month_1')) {
        $('#pcc_month_1').val(pcc_month_1);
    }
    $('#pcc_month_1').change(function(e) {
        localStorage.setItem('pcc_month_1', $(this).val());
    });

    if (pcc_year_1 = localStorage.getItem('pcc_year_1')) {
        $('#pcc_year_1').val(pcc_year_1);
    }
    $('#pcc_year_1').change(function(e) {
        localStorage.setItem('pcc_year_1', $(this).val());
    });

    if (pcc_no_1 = localStorage.getItem('pcc_no_1')) {
        $('#pcc_no_1').val(pcc_no_1);
    }
    $('#pcc_no_1').change(function(e) {
        var pcc_no = $(this).val();
        localStorage.setItem('pcc_no_1', pcc_no);
        var CardType = null;
        var ccn1 = pcc_no.charAt(0);
        if (ccn1 == 4)
            CardType = 'Visa';
        else if (ccn1 == 5)
            CardType = 'MasterCard';
        else if (ccn1 == 3)
            CardType = 'Amex';
        else if (ccn1 == 6)
            CardType = 'Discover';
        else
            CardType = 'Visa';

        $('#pcc_type_1').select2("val", CardType);
    });

    if (cheque_no_1 = localStorage.getItem('cheque_no_1')) {
        $('#cheque_no_1').val(cheque_no_1);
    }
    $('#cheque_no_1').change(function(e) {
        localStorage.setItem('cheque_no_1', $(this).val());
    });

    if (payment_note_1 = localStorage.getItem('payment_note_1')) {
        $('#payment_note_1').redactor('set', payment_note_1);
    }
    $('#payment_note_1').redactor('destroy');
    $('#payment_note_1').redactor({
        buttons: ['formatting', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'bold', 'italic', 'underline', '|', 'unorderedlist', 'orderedlist', '|', 'link', '|', 'html'],
        formattingTags: ['p', 'pre', 'h3', 'h4'],
        minHeight: 100,
        changeCallback: function(e) {
            var v = this.get();
            localStorage.setItem('payment_note_1', v);
        }
    });

    var old_payment_term;
    $('#slpayment_term').focus(function() {
        old_payment_term = $(this).val();
    }).change(function(e) {
        var new_payment_term = $(this).val() ? parseFloat($(this).val()) : 0;
        if (!is_numeric($(this).val())) {
            $(this).val(old_payment_term);
            bootbox.alert(lang.unexpected_value);
            return;
        } else {
            localStorage.setItem('slpayment_term', new_payment_term);
            $('#slpayment_term').val(new_payment_term);
        }
    });
    if (slpayment_term = localStorage.getItem('slpayment_term')) {
        $('#slpayment_term').val(slpayment_term);
    }

    var old_shipping;
    $('#slshipping').focus(function() {
        old_shipping = $(this).val();
    }).change(function() {
        var slsh = $(this).val() ? $(this).val() : 0;
        if (!is_numeric(slsh)) {
            $(this).val(old_shipping);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        shipping = parseFloat(slsh);
        localStorage.setItem('slshipping', shipping);
        var gtotal = ((total + invoice_tax) - order_discount) + shipping;
        $('#gtotal').text(formatMoney(gtotal));
        $('#tship').text(formatMoney(shipping));
    });
    if (slshipping = localStorage.getItem('slshipping')) {
        shipping = parseFloat(slshipping);
        $('#slshipping').val(shipping);
    } else {
        shipping = 0;
    }
    $('#add_sale, #edit_sale').attr('disabled', true);
    $(document).on('change', '.rserial', function() {
        var item_id = $(this).closest('tr').attr('data-item-id');
        slitems[item_id].row.serial = $(this).val();
        localStorage.setItem('slitems', JSON.stringify(slitems));
    });

    // If there is any item in localStorage
    if (localStorage.getItem('slitems')) {
        loadItems();
    }

    // clear localStorage and reload
    $('#reset').click(function(e) {
        bootbox.confirm(lang.r_u_sure, function(result) {
            if (result) {
                if (localStorage.getItem('slitems')) {
                    localStorage.removeItem('slitems');
                }
                if (localStorage.getItem('sldiscount')) {
                    localStorage.removeItem('sldiscount');
                }
                if (localStorage.getItem('sltax2')) {
                    localStorage.removeItem('sltax2');
                }
                if (localStorage.getItem('slshipping')) {
                    localStorage.removeItem('slshipping');
                }
                if (localStorage.getItem('slref')) {
                    localStorage.removeItem('slref');
                }
                if (localStorage.getItem('slwarehouse')) {
                    localStorage.removeItem('slwarehouse');
                }
                if (localStorage.getItem('slnote')) {
                    localStorage.removeItem('slnote');
                }
                if (localStorage.getItem('slinnote')) {
                    localStorage.removeItem('slinnote');
                }
                if (localStorage.getItem('slcustomer')) {
                    localStorage.removeItem('slcustomer');
                }
                if (localStorage.getItem('slcurrency')) {
                    localStorage.removeItem('slcurrency');
                }
                if (localStorage.getItem('sldate')) {
                    localStorage.removeItem('sldate');
                }
                if (localStorage.getItem('slstatus')) {
                    localStorage.removeItem('slstatus');
                }
                if (localStorage.getItem('slbiller')) {
                    localStorage.removeItem('slbiller');
                }
                if (localStorage.getItem('gift_card_no')) {
                    localStorage.removeItem('gift_card_no');
                }

                $('#modal-loading').show();
                location.reload();
            }
        });
    });

    // save and load the fields in and/or from localStorage

    $('#slref').change(function(e) {
        localStorage.setItem('slref', $(this).val());
    });
    if (slref = localStorage.getItem('slref')) {
        $('#slref').val(slref);
    }

    $('#slwarehouse').change(function(e) {
        localStorage.setItem('slwarehouse', $(this).val());
    });
    if (slwarehouse = localStorage.getItem('slwarehouse')) {
        $('#slwarehouse').select2("val", slwarehouse);
    }

    $('#slnote').redactor('destroy');
    $('#slnote').redactor({
        buttons: ['formatting', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'bold', 'italic', 'underline', '|', 'unorderedlist', 'orderedlist', '|', 'link', '|', 'html'],
        formattingTags: ['p', 'pre', 'h3', 'h4'],
        minHeight: 100,
        changeCallback: function(e) {
            var v = this.get();
            localStorage.setItem('slnote', v);
        }
    });
    if (slnote = localStorage.getItem('slnote')) {
        $('#slnote').redactor('set', slnote);
    }
    $('#slinnote').redactor('destroy');
    $('#slinnote').redactor({
        buttons: ['formatting', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'bold', 'italic', 'underline', '|', 'unorderedlist', 'orderedlist', '|', 'link', '|', 'html'],
        formattingTags: ['p', 'pre', 'h3', 'h4'],
        minHeight: 100,
        changeCallback: function(e) {
            var v = this.get();
            localStorage.setItem('slinnote', v);
        }
    });
    if (slinnote = localStorage.getItem('slinnote')) {
        $('#slinnote').redactor('set', slinnote);
    }

    // prevent default action usln enter
    $('body').bind('keypress', function(e) {
        if ($(e.target).hasClass('redactor_editor')) {
            return true;
        }
        if (e.keyCode == 13) {
            e.preventDefault();
            return false;
        }
    });

    // Order tax calculation
    if (site.settings.tax2 != 0) {
        $('#sltax2').change(function() {
            localStorage.setItem('sltax2', $(this).val());
            loadItems();
            return;
        });
    }

    // Order discount calculation
    var old_sldiscount;
    $('#sldiscount').focus(function() {
        old_sldiscount = $(this).val();
    }).change(function() {
        var new_discount = $(this).val() ? $(this).val() : '0';
        if (is_valid_discount(new_discount)) {
            localStorage.removeItem('sldiscount');
            localStorage.setItem('sldiscount', new_discount);
            loadItems();
            return;
        } else {
            $(this).val(old_sldiscount);
            bootbox.alert(lang.unexpected_value);
            return;
        }

    });


    /* ----------------------
     * Delete Row Method
     * ---------------------- */
    $(document).on('click', '.sldel', function() {
        var row = $(this).closest('tr');
        var item_id = row.attr('data-item-id');
        delete slitems[item_id];
        row.remove();
        net_unit_price = 0;
        price_change = 0;
        if (slitems.hasOwnProperty(item_id)) {} else {
            localStorage.setItem('slitems', JSON.stringify(slitems));
            loadItems();
            return;
        }
    });


    /* -----------------------
     * Edit Row Modal Hanlder
     ----------------------- */
    $(document).on('click', '.edit', function() {
        var row = $(this).closest('tr');
        var row_id = row.attr('id');
        item_id = row.attr('data-item-id');
        item = slitems[item_id];
        var qty = row.children().children('.rquantity').val(),
            product_option = row.children().children('.roption').val(),
            unit_price = formatDecimal(row.children().children('.ruprice').val()),
            discount = row.children().children('.rdiscount').val();
        if (item.options !== false) {
            $.each(item.options, function() {
                if (this.id == item.row.option && this.price != 0 && this.price != '' && this.price != null) {
                    unit_price = parseFloat(item.row.real_unit_price) + parseFloat(this.price);
                }
            });
        }
        var real_unit_price = item.row.real_unit_price;
        var net_price = unit_price;
        $('#prModalLabel').text(item.row.name + ' (' + item.row.code + ')');
        if (site.settings.tax1) {
            $('#ptax').select2('val', item.row.tax_rate);
            $('#old_tax').val(item.row.tax_rate);
            var item_discount = 0,
                ds = discount ? discount : '0';
            if (ds.indexOf("%") !== -1) {
                var pds = ds.split("%");
                if (!isNaN(pds[0])) {
                    item_discount = formatDecimal(parseFloat(((unit_price) * parseFloat(pds[0])) / 100), 4);
                } else {
                    item_discount = parseFloat(ds);
                }
            } else {
                item_discount = parseFloat(ds);
            }
            net_price -= item_discount;
            var pr_tax = item.row.tax_rate,
                pr_tax_val = 0;
            if (pr_tax !== null && pr_tax != 0) {
                $.each(tax_rates, function() {
                    if (this.id == pr_tax) {
                        if (this.type == 1) {

                            if (slitems[item_id].row.tax_method == 0) {
                                pr_tax_val = formatDecimal((((net_price) * parseFloat(this.rate)) / (100 + parseFloat(this.rate))), 4);
                                pr_tax_rate = formatDecimal(this.rate) + '%';
                                net_price -= pr_tax_val;
                            } else {
                                pr_tax_val = formatDecimal((((net_price) * parseFloat(this.rate)) / 100), 4);
                                pr_tax_rate = formatDecimal(this.rate) + '%';
                            }

                        } else if (this.type == 2) {

                            pr_tax_val = parseFloat(this.rate);
                            pr_tax_rate = this.rate;

                        }
                    }
                });
            }
        }
        if (site.settings.product_serial !== 0) {
            $('#pserial').val(row.children().children('.rserial').val());
        }
        var opt = '<p style="margin: 12px 0 0 0;">n/a</p>';
        if (item.options !== false) {
            var o = 1;
            opt = $("<select id=\"poption\" name=\"poption\" class=\"form-control select\" />");
            $.each(item.options, function() {
                if (o == 1) {
                    if (product_option == '') { product_variant = this.id; } else { product_variant = product_option; }
                }
                $("<option />", { value: this.id, text: this.name }).appendTo(opt);
                o++;
            });
        } else {
            product_variant = 0;
        }

        uopt = '<p style="margin: 12px 0 0 0;">n/a</p>';
        if (item.units) {
            uopt = $("<select id=\"punit\" name=\"punit\" class=\"form-control select\" />");
            $.each(item.units, function() {
                if (this.id == item.row.unit) {
                    $("<option />", { value: this.id, text: this.name, selected: true }).appendTo(uopt);
                } else {
                    $("<option />", { value: this.id, text: this.name }).appendTo(uopt);
                }
            });
        }

        $('#poptions-div').html(opt);
        $('#punits-div').html(uopt);
        $('select.select').select2({ minimumResultsForSearch: 7 });
        $('#pquantity').val(qty);
        $('#old_qty').val(qty);
        $('#pprice').val(unit_price);
        $('#punit_price').val(formatDecimal(parseFloat(unit_price) + parseFloat(pr_tax_val)));
        $('#poption').select2('val', item.row.option);
        $('#old_price').val(unit_price);
        $('#row_id').val(row_id);
        $('#item_id').val(item_id);
        $('#pserial').val(row.children().children('.rserial').val());
        $('#pdiscount').val(discount);
        $('#net_price').text(formatMoney(net_price));
        $('#pro_tax').text(formatMoney(pr_tax_val));
        $('#prModal').appendTo("body").modal('show');

    });

    $('#prModal').on('shown.bs.modal', function(e) {
        if ($('#poption').select2('val') != '') {
            $('#poption').select2('val', product_variant);
            product_variant = 0;
        }
    });

    $(document).on('change', '#pprice, #ptax, #pdiscount', function() {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var unit_price = parseFloat($('#pprice').val());
        var item = slitems[item_id];
        var ds = $('#pdiscount').val() ? $('#pdiscount').val() : '0';
        if (ds.indexOf('%') !== -1) {
            var pds = ds.split('%');
            if (!isNaN(pds[0])) {
                item_discount = parseFloat((unit_price * parseFloat(pds[0])) / 100);
            } else {
                item_discount = parseFloat(ds);
            }
        } else {
            item_discount = parseFloat(ds);
        }
        unit_price -= item_discount;
        var pr_tax = $('#ptax').val(),
            item_tax_method = item.row.tax_method;
        var pr_tax_val = 0,
            pr_tax_rate = 0;
        if (pr_tax !== null && pr_tax != 0) {
            $.each(tax_rates, function() {
                if (this.id == pr_tax) {
                    if (this.type == 1) {
                        if (item_tax_method == 0) {
                            pr_tax_val = formatDecimal((unit_price * parseFloat(this.rate)) / (100 + parseFloat(this.rate)), 4);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                            unit_price -= pr_tax_val;
                        } else {
                            pr_tax_val = formatDecimal((unit_price * parseFloat(this.rate)) / 100, 4);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                        }
                    } else if (this.type == 2) {
                        pr_tax_val = parseFloat(this.rate);
                        pr_tax_rate = this.rate;
                    }
                }
            });
        }

        $('#net_price').text(formatMoney(unit_price));
        $('#pro_tax').text(formatMoney(pr_tax_val));

        // var row = $('#' + $('#row_id').val());
        // var item_id = row.attr('data-item-id');
        // var unit_price = parseFloat($('#pprice').val());
        // var item = slitems[item_id];
        // var ds = $('#pdiscount').val() ? $('#pdiscount').val() : '0';
        // if (ds.indexOf("%") !== -1) {
        //     var pds = ds.split("%");
        //     if (!isNaN(pds[0])) {
        //         item_discount = parseFloat(((unit_price) * parseFloat(pds[0])) / 100);
        //     } else {
        //         item_discount = parseFloat(ds);
        //     }
        // } else {
        //     item_discount = parseFloat(ds);
        // }
        // unit_price -= item_discount;
        // var pr_tax = $('#ptax').val(),
        //     item_tax_method = item.row.tax_method;
        // var pr_tax_val = 0,
        //     pr_tax_rate = 0;
        // if (pr_tax !== null && pr_tax != 0) {
        //     $.each(tax_rates, function() {
        //         if (this.id == pr_tax) {
        //             if (this.type == 1) {

        //                 if (item_tax_method == 0) {
        //                     pr_tax_val = formatDecimal(((unit_price) * parseFloat(this.rate)) / (100 + parseFloat(this.rate)), 4);
        //                     pr_tax_rate = formatDecimal(this.rate) + '%';
        //                     unit_price -= pr_tax_val;
        //                 } else {
        //                     pr_tax_val = formatDecimal((((unit_price) * parseFloat(this.rate)) / 100), 4);
        //                     pr_tax_rate = formatDecimal(this.rate) + '%';
        //                 }

        //             } else if (this.type == 2) {

        //                 pr_tax_val = parseFloat(this.rate);
        //                 pr_tax_rate = this.rate;

        //             }
        //         }
        //     });
        // }

        // $('#net_price').text(formatMoney(unit_price));
        // $('#pro_tax').text(formatMoney(pr_tax_val));
    });

    $(document).on('change', '#punit', function() {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var item = slitems[item_id];
        if (!is_numeric($('#pquantity').val())) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var opt = $('#poption').val(),
            unit = $('#punit').val(),
            base_quantity = $('#pquantity').val(),
            aprice = 0;
        if (item.options !== false) {
            $.each(item.options, function() {
                if (this.id == opt && this.price != 0 && this.price != '' && this.price != null) {
                    aprice = parseFloat(this.price);
                }
            });
        }
        if (item.units && unit != slitems[item_id].row.base_unit) {
            $.each(item.units, function() {
                if (this.id == unit) {
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                    $('#pprice').val(formatDecimal(((parseFloat(item.row.base_unit_price + aprice)) * unitToBaseQty(1, this)), 4)).change();
                }
            });
        } else {
            $('#pprice').val(formatDecimal(item.row.base_unit_price + aprice)).change();
        }
    });





    /* -----------------------
     * Edit Price Row Modal Hanlder
     ----------------------- */
    $(document).on('click', '.load_amount', function() {
        var row = $(this).closest('tr');
        var row_id = row.attr('id');
        item_id = row.attr('data-item-id');
        item = slitems[item_id];
        var qty = row.children().children('.rquantity').val(),
            product_option = row.children().children('.roption').val(),
            unit_price = formatDecimal(row.children().children('.ruprice').val()),
            discount = row.children().children('.rdiscount').val();
        if (item.options !== false) {
            $.each(item.options, function() {
                if (this.id == item.row.option && this.price != 0 && this.price != '' && this.price != null) {
                    unit_price = parseFloat(item.row.real_unit_price) + parseFloat(this.price);
                }
            });
        }
        var real_unit_price = item.row.real_unit_price;
        var net_price = unit_price;

        // alert(real_unit_price + "-----" + net_price);



        // $.ajax({
        //     type: 'get',
        //     url: site.base_url + 'sales/get_price',
        //     dataType: "json",
        //     data: { id: id },
        //     success: function (data) {
        //         if (data.result === 'success') {
        //             slitems[mid] = { "id": mid, "item_id": mid, "label": gcname + ' (' + gccode + ')', "row": { "id": mid, "code": gccode, "name": gcname, "quantity": 1, "base_quantity": 1, "price": gcprice, "real_unit_price": gcprice, "tax_rate": 0, "qty": 1, "type": "manual", "discount": "0", "serial": "", "option": "" }, "tax_rate": false, "options": false, "units": false };
        //             localStorage.setItem('slitems', JSON.stringify(slitems));
        //             loadItems();
        //             $('#gcModal').modal('hide');
        //             $('#gccard_no').val('');
        //             $('#gcvalue').val('');
        //             $('#gcexpiry').val('');
        //             $('#gcprice').val('');
        //         } else {
        //             $('#gcerror').text(data.message);
        //             $('.gcerror-con').show();
        //         }
        //     }
        // });





        // $('#select_pr_ModalLabel').text(item.row.name + ' (' + item.row.code + ')');
        // if (site.settings.tax1) {
        //     $('#ptax').select2('val', item.row.tax_rate);
        //     $('#old_tax').val(item.row.tax_rate);
        //     var item_discount = 0, ds = discount ? discount : '0';
        //     if (ds.indexOf("%") !== -1) {
        //         var pds = ds.split("%");
        //         if (!isNaN(pds[0])) {
        //             item_discount = formatDecimal(parseFloat(((unit_price) * parseFloat(pds[0])) / 100), 4);
        //         } else {
        //             item_discount = parseFloat(ds);
        //         }
        //     } else {
        //         item_discount = parseFloat(ds);
        //     }
        //     net_price -= item_discount;
        //     var pr_tax = item.row.tax_rate, pr_tax_val = 0;
        //     if (pr_tax !== null && pr_tax != 0) {
        //         $.each(tax_rates, function () {
        //             if (this.id == pr_tax) {
        //                 if (this.type == 1) {

        //                     if (slitems[item_id].row.tax_method == 0) {
        //                         pr_tax_val = formatDecimal((((net_price) * parseFloat(this.rate)) / (100 + parseFloat(this.rate))), 4);
        //                         pr_tax_rate = formatDecimal(this.rate) + '%';
        //                         net_price -= pr_tax_val;
        //                     } else {
        //                         pr_tax_val = formatDecimal((((net_price) * parseFloat(this.rate)) / 100), 4);
        //                         pr_tax_rate = formatDecimal(this.rate) + '%';
        //                     }

        //                 } else if (this.type == 2) {

        //                     pr_tax_val = parseFloat(this.rate);
        //                     pr_tax_rate = this.rate;

        //                 }
        //             }
        //         });
        //     }
        // }
        // if (site.settings.product_serial !== 0) {
        //     $('#pserial').val(row.children().children('.rserial').val());
        // }
        // var opt = '<p style="margin: 12px 0 0 0;">n/a</p>';
        // if (item.options !== false) {
        //     var o = 1;
        //     opt = $("<select id=\"poption\" name=\"poption\" class=\"form-control select\" />");
        //     $.each(item.options, function () {
        //         if (o == 1) {
        //             if (product_option == '') { product_variant = this.id; } else { product_variant = product_option; }
        //         }
        //         $("<option />", { value: this.id, text: this.name }).appendTo(opt);
        //         o++;
        //     });
        // } else {
        //     product_variant = 0;
        // }

        // uopt = '<p style="margin: 12px 0 0 0;">n/a</p>';
        // if (item.units) {
        //     uopt = $("<select id=\"punit\" name=\"punit\" class=\"form-control select\" />");
        //     $.each(item.units, function () {
        //         if (this.id == item.row.unit) {
        //             $("<option />", { value: this.id, text: this.name, selected: true }).appendTo(uopt);
        //         } else {
        //             $("<option />", { value: this.id, text: this.name }).appendTo(uopt);
        //         }
        //     });
        // }

        // $('#poptions-div').html(opt);
        // $('#punits-div').html(uopt);
        // $('select.select').select2({ minimumResultsForSearch: 7 });
        // $('#pquantity').val(qty);
        // $('#old_qty').val(qty);
        // $('#pprice').val(unit_price);
        // $('#punit_price').val(formatDecimal(parseFloat(unit_price) + parseFloat(pr_tax_val)));
        // $('#poption').select2('val', item.row.option);
        // $('#old_price').val(unit_price);
        // $('#row_id').val(row_id);
        // $('#item_id').val(item_id);
        // $('#pserial').val(row.children().children('.rserial').val());
        // $('#pdiscount').val(discount);
        // $('#net_price').text(formatMoney(net_price));
        // $('#pro_tax').text(formatMoney(pr_tax_val));
        // $('#select_pr_Modal').appendTo("body").modal('show');

    });








    $('#select_pr_Modal').on('shown.bs.modal', function(e) {
        if ($('#poption').select2('val') != '') {
            $('#poption').select2('val', product_variant);
            product_variant = 0;
        }
    });

    $(document).on('change', '#pprice, #ptax, #pdiscount', function() {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var unit_price = parseFloat($('#pprice').val());
        var item = slitems[item_id];
        var ds = $('#pdiscount').val() ? $('#pdiscount').val() : '0';
        if (ds.indexOf("%") !== -1) {
            var pds = ds.split("%");
            if (!isNaN(pds[0])) {
                item_discount = parseFloat(((unit_price) * parseFloat(pds[0])) / 100);
            } else {
                item_discount = parseFloat(ds);
            }
        } else {
            item_discount = parseFloat(ds);
        }
        unit_price -= item_discount;
        var pr_tax = $('#ptax').val(),
            item_tax_method = item.row.tax_method;
        var pr_tax_val = 0,
            pr_tax_rate = 0;
        if (pr_tax !== null && pr_tax != 0) {
            $.each(tax_rates, function() {
                if (this.id == pr_tax) {
                    if (this.type == 1) {

                        if (item_tax_method == 0) {
                            pr_tax_val = formatDecimal(((unit_price) * parseFloat(this.rate)) / (100 + parseFloat(this.rate)), 4);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                            unit_price -= pr_tax_val;
                        } else {
                            pr_tax_val = formatDecimal((((unit_price) * parseFloat(this.rate)) / 100), 4);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                        }

                    } else if (this.type == 2) {

                        pr_tax_val = parseFloat(this.rate);
                        pr_tax_rate = this.rate;

                    }
                }
            });
        }

        $('#net_price').text(formatMoney(unit_price));
        $('#pro_tax').text(formatMoney(pr_tax_val));
    });

    $(document).on('change', '#punit', function() {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var item = slitems[item_id];
        if (!is_numeric($('#pquantity').val())) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var opt = $('#poption').val(),
            unit = $('#punit').val(),
            base_quantity = $('#pquantity').val(),
            aprice = 0;
        if (item.options !== false) {
            $.each(item.options, function() {
                if (this.id == opt && this.price != 0 && this.price != '' && this.price != null) {
                    aprice = parseFloat(this.price);
                }
            });
        }
        if (item.units && unit != slitems[item_id].row.base_unit) {
            $.each(item.units, function() {
                if (this.id == unit) {
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                    $('#pprice').val(formatDecimal(((parseFloat(item.row.base_unit_price + aprice)) * unitToBaseQty(1, this)), 4)).change();
                }
            });
        } else {
            $('#pprice').val(formatDecimal(item.row.base_unit_price + aprice)).change();
        }
    });


    /* -----------------------
     * Edit Row Method
     ----------------------- */
    $(document).on('click', '#editItem', function() {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id'),
            new_pr_tax = $('#ptax').val(),
            new_pr_tax_rate = false;
        if (new_pr_tax) {
            $.each(tax_rates, function() {
                if (this.id == new_pr_tax) {
                    new_pr_tax_rate = this;
                }
            });
        }
        var price = parseFloat($('#pprice').val());
        if (item.options !== false) {
            var opt = $('#poption').val();
            $.each(item.options, function() {
                if (this.id == opt && this.price != 0 && this.price != '' && this.price != null) {
                    price = price - parseFloat(this.price);
                }
            });
        }
        if (site.settings.product_discount == 1 && $('#pdiscount').val()) {
            if (!is_valid_discount($('#pdiscount').val()) /* || $('#pdiscount').val() > price */ ) {
                bootbox.alert(lang.unexpected_value);
                return false;
            }
        }
        if (!is_numeric($('#pquantity').val())) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var unit = $('#punit').val();
        var base_quantity = parseFloat($('#pquantity').val());
        if (unit != slitems[item_id].row.base_unit) {
            $.each(slitems[item_id].units, function() {
                if (this.id == unit) {
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                }
            });
        }
        slitems[item_id].row.fup = 1,
            slitems[item_id].row.qty = parseFloat($('#pquantity').val()),
            slitems[item_id].row.base_quantity = parseFloat(base_quantity),
            slitems[item_id].row.real_unit_price = price,

            ((customer_type == "consignment") ? (slitems[item_id].row.get_selected_product_price = price, slitems[item_id].row.consignment = parseFloat(price)) : (customer_type == "dropship") ? (slitems[item_id].row.get_selected_product_dropship = price, slitems[item_id].row.dropship = parseFloat(price)) : (customer_type == "crossdock") ? (slitems[item_id].row.get_selected_product_crossdock = price, slitems[item_id].row.crossdock = parseFloat(price)) : (customer_type == "services") ? "0" : "0")

        // if(customer_type == "consignment") {
        //     slitems[item_id].row.get_selected_product_dropship = price,
        //     slitems[item_id].row.dropship = parseFloat(price),
        // } else if(customer_type == "dropship") {

        // } else if(customer_type == "crossdock") {

        // } else if(customer_type == "services") {

        // }


        // slitems[item_id].row.get_selected_product_price = price,
        // slitems[item_id].row.consiment = parseFloat(price),

        slitems[item_id].row.unit = unit,
            slitems[item_id].row.tax_rate = new_pr_tax,
            slitems[item_id].tax_rate = new_pr_tax_rate,
            slitems[item_id].row.discount = $('#pdiscount').val() ? $('#pdiscount').val() : '',
            slitems[item_id].row.option = $('#poption').val() ? $('#poption').val() : '',
            slitems[item_id].row.serial = $('#pserial').val();
        localStorage.setItem('slitems', JSON.stringify(slitems));
        $('#prModal').modal('hide');

        loadItems();

        return;
    });

    /* -----------------------
     * Product option change
     ----------------------- */
    $(document).on('change', '#poption', function() {
        var row = $('#' + $('#row_id').val()),
            opt = $(this).val();
        var item_id = row.attr('data-item-id');
        var item = slitems[item_id];
        var unit = $('#punit').val(),
            base_quantity = parseFloat($('#pquantity').val()),
            base_unit_price = item.row.base_unit_price;
        if (unit != slitems[item_id].row.base_unit) {
            $.each(slitems[item_id].units, function() {
                if (this.id == unit) {
                    base_unit_price = formatDecimal((parseFloat(item.row.base_unit_price) * (unitToBaseQty(1, this))), 4)
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                }
            });
        }
        $('#pprice').val(parseFloat(base_unit_price)).trigger('change');
        if (item.options !== false) {
            $.each(item.options, function() {
                if (this.id == opt && this.price != 0 && this.price != '' && this.price != null) {
                    $('#pprice').val(parseFloat(base_unit_price) + (parseFloat(this.price))).trigger('change');
                }
            });
        }
    });

    /* ------------------------------
    * Sell Gift Card modal
    ------------------------------- */
    $(document).on('click', '#sellGiftCard', function(e) {
        if (count == 1) {
            slitems = {};
            if ($('#slwarehouse').val() && $('#slcustomer').val()) {
                $('#slcustomer').select2("readonly", true);
                $('#slwarehouse').select2("readonly", true);
            } else {
                bootbox.alert(lang.select_above);
                item = null;
                return false;
            }
        }
        $('#gcModal').appendTo("body").modal('show');
        return false;
    });

    $(document).on('click', '#addGiftCard', function(e) {
        var mid = (new Date).getTime(),
            gccode = $('#gccard_no').val(),
            gcname = $('#gcname').val(),
            gcvalue = $('#gcvalue').val(),
            gccustomer = $('#gccustomer').val(),
            gcexpiry = $('#gcexpiry').val() ? $('#gcexpiry').val() : '',
            gcprice = parseFloat($('#gcprice').val());
        if (gccode == '' || gcvalue == '' || gcprice == '' || gcvalue == 0 || gcprice == 0) {
            $('#gcerror').text('Please fill the required fields');
            $('.gcerror-con').show();
            return false;
        }

        var gc_data = new Array();
        gc_data[0] = gccode;
        gc_data[1] = gcvalue;
        gc_data[2] = gccustomer;
        gc_data[3] = gcexpiry;
        //if (typeof slitems === "undefined") {
        //    var slitems = {};
        //}

        $.ajax({
            type: 'get',
            url: site.base_url + 'sales/sell_gift_card',
            dataType: "json",
            data: { gcdata: gc_data },
            success: function(data) {
                if (data.result === 'success') {
                    slitems[mid] = { "id": mid, "item_id": mid, "label": gcname + ' (' + gccode + ')', "row": { "id": mid, "code": gccode, "name": gcname, "quantity": 1, "base_quantity": 1, "price": gcprice, "real_unit_price": gcprice, "tax_rate": 0, "qty": 1, "type": "manual", "discount": "0", "serial": "", "option": "" }, "tax_rate": false, "options": false, "units": false };
                    localStorage.setItem('slitems', JSON.stringify(slitems));
                    loadItems();
                    $('#gcModal').modal('hide');
                    $('#gccard_no').val('');
                    $('#gcvalue').val('');
                    $('#gcexpiry').val('');
                    $('#gcprice').val('');
                } else {
                    $('#gcerror').text(data.message);
                    $('.gcerror-con').show();
                }
            }
        });
        return false;
    });

    /* ------------------------------
     * Show manual item addition modal
     ------------------------------- */
    $(document).on('click', '#addManually', function(e) {
        if (count == 1) {
            slitems = {};
            if ($('#slwarehouse').val() && $('#slcustomer').val()) {
                $('#slcustomer').select2("readonly", true);
                $('#slwarehouse').select2("readonly", true);
            } else {
                bootbox.alert(lang.select_above);
                item = null;
                return false;
            }
        }
        $('#mnet_price').text('0.00');
        $('#mpro_tax').text('0.00');
        $('#mModal').appendTo("body").modal('show');
        return false;
    });

    $(document).on('click', '#addItemManually', function(e) {
        var mid = (new Date).getTime(),
            mcode = $('#mcode').val(),
            mname = $('#mname').val(),
            mtax = parseInt($('#mtax').val()),
            munit = parseInt($('#munit').val()),
            mqty = parseFloat($('#mquantity').val()),
            mdiscount = $('#mdiscount').val() ? $('#mdiscount').val() : '0',
            unit_price = parseFloat($('#mprice').val()),
            mtax_rate = {};
        if (mcode && mname && mqty && unit_price) {
            $.each(tax_rates, function() {
                if (this.id == mtax) {
                    mtax_rate = this;
                }
            });

            slitems[mid] = { "id": mid, "item_id": mid, "label": mname + ' (' + mcode + ')', "row": { "id": mid, "code": mcode, "name": mname, "quantity": mqty, "base_quantity": mqty, "price": unit_price, "unit_price": unit_price, "real_unit_price": unit_price, "tax_rate": mtax, "unit": munit, "tax_method": 0, "qty": mqty, "type": "manual", "discount": mdiscount, "serial": "", "option": "" }, "tax_rate": mtax_rate, 'units': false, "options": false };
            localStorage.setItem('slitems', JSON.stringify(slitems));
            loadItems();
        }
        $('#mModal').modal('hide');
        $('#mcode').val('');
        $('#mname').val('');
        $('#mtax').val('');
        $('#munit').val('');
        $('#mquantity').val('');
        $('#mdiscount').val('');
        $('#mprice').val('');
        return false;
    });

    $(document).on('change', '#mprice, #mtax, #mdiscount', function() {
        var unit_price = parseFloat($('#mprice').val());
        var ds = $('#mdiscount').val() ? $('#mdiscount').val() : '0';
        if (ds.indexOf("%") !== -1) {
            var pds = ds.split("%");
            if (!isNaN(pds[0])) {
                item_discount = parseFloat(((unit_price) * parseFloat(pds[0])) / 100);
            } else {
                item_discount = parseFloat(ds);
            }
        } else {
            item_discount = parseFloat(ds);
        }
        unit_price -= item_discount;
        var pr_tax = $('#mtax').val(),
            item_tax_method = 0;
        var pr_tax_val = 0,
            pr_tax_rate = 0;
        if (pr_tax !== null && pr_tax != 0) {
            $.each(tax_rates, function() {
                if (this.id == pr_tax) {
                    if (this.type == 1) {

                        if (item_tax_method == 0) {
                            pr_tax_val = formatDecimal((((unit_price) * parseFloat(this.rate)) / (100 + parseFloat(this.rate))), 4);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                            unit_price -= pr_tax_val;
                        } else {
                            pr_tax_val = formatDecimal((((unit_price) * parseFloat(this.rate)) / 100), 4);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                        }

                    } else if (this.type == 2) {

                        pr_tax_val = parseFloat(this.rate);
                        pr_tax_rate = this.rate;

                    }
                }
            });
        }
        $('#mnet_price').text(formatMoney(unit_price));
        $('#mpro_tax').text(formatMoney(pr_tax_val));
    });










    /* --------------------------
        * Edit Row Discount Method
        -------------------------- */
    var old_row_qty;
    var consiment_cost;
    var get_class;
    var discount_with_qty = 0;

    $(document).on("change", '.discount_one, .discount_two, .discount_three', function() {
        var row = $(this).closest('tr');
        item_id = row.attr('data-item-id');

        get_class = $(this)[0].classList.value + "_checked";

        if ($(this)[0].checked == true) {

            discount_one_checked = "true";
            // consiment_cost = slitems[item_id].row.consiment;
            consiment_cost = slitems[item_id].row.get_selected_product_consiment;

            old_discount = JSON.parse(slitems[item_id].row.discount);
            discount_one = $(this).val();
            total_discount_one = (consiment_cost * discount_one) / 100;
            tax_value = formatMoney(((parseFloat(slitems[item_id].row.cost)) * parseFloat(slitems[item_id].row.base_quantity) * parseFloat(slitems[item_id].tax_rate.rate)) / 100);
            total_cost = formatMoney(((parseFloat(slitems[item_id].row.cost) * parseFloat(slitems[item_id].row.base_quantity)) + (parseFloat(tax_value))));
            discount_with_qty += total_discount_one * parseFloat(slitems[item_id].row.base_quantity);
            slitems[item_id].row.discount = JSON.stringify(discount_with_qty + old_discount);
        } else {
            discount_one_checked = "false";
            // consiment_cost = slitems[item_id].row.consiment;
            consiment_cost = slitems[item_id].row.get_selected_product_consiment;
            old_discount = JSON.parse(slitems[item_id].row.discount);
            discount_one = $(this).val();
            total_discount_one = (consiment_cost * discount_one) / 100;
            tax_value = formatMoney(((parseFloat(slitems[item_id].row.cost)) * parseFloat(slitems[item_id].row.base_quantity) * parseFloat(slitems[item_id].tax_rate.rate)) / 100);
            total_cost = formatMoney(((parseFloat(slitems[item_id].row.cost) * parseFloat(slitems[item_id].row.base_quantity)) + (parseFloat(tax_value))));
            discount_with_qty += total_discount_one * parseFloat(slitems[item_id].row.base_quantity);
            slitems[item_id].row.discount = JSON.stringify(old_discount - discount_with_qty);
        }


        if (get_class == 'discount_one_checked') {
            slitems[item_id].row.discount_one_checked = discount_one_checked;
        } else if (get_class == 'discount_two_checked') {
            slitems[item_id].row.discount_two_checked = discount_one_checked;
        } else if (get_class == 'discount_three_checked') {
            slitems[item_id].row.discount_three_checked = discount_one_checked;
        }

        localStorage.setItem('slitems', JSON.stringify(slitems));

        loadItems();

        consiment_cost = 0;
        discount_one = 0;
        total_discount_one = 0;
        tax_value = 0;
        total_cost = 0;
        discount_with_qty = 0;

    });



    /* --------------------------
     * Edit Row Batch Number Method
     -------------------------- */
    $(document).on("focus", '.rbatch_number', function() {}).on("change", '.rbatch_number', function() {
        var row = $(this).closest('tr');

        var new_batch_number = $(this).val();
        item_id = row.attr('data-item-id');

        slitems[item_id].row.batch = new_batch_number;
        localStorage.setItem('slitems', JSON.stringify(slitems));

        loadItems();
    });


    /* --------------------------
     * Edit Row Expiry Number Method
     -------------------------- */
    var old_row_qty;
    $(document).on("focus", '.rprod_expiry', function() {
        old_row_qty = $(this).val();
    }).on("change", '.rprod_expiry', function() {

        var row = $(this).closest('tr');

        var new_prod_expiry = $(this).val();
        item_id = row.attr('data-item-id');

        slitems[item_id].row.expiry = new_prod_expiry;
        localStorage.setItem('slitems', JSON.stringify(slitems));

        loadItems();



    });


    /* --------------------------
     * Edit Row Quantity Method
    --------------------------- */
    var old_row_qty;
    $(document).on("focus", '.rquantity', function() {
        old_row_qty = $(this).val();
    }).on("change", '.rquantity', function() {
        var row = $(this).closest('tr');
        item_id = row.attr('data-item-id');
        // console.log(is_numeric($(this).val()));

        let get_row_id = "#row_" + item_id;

        var new_qty = parseFloat($(this).val());
        let get_selected_product_quantities = Math.trunc($("#product_batch_quantity_" + item_id + "_" + select_index).val());




        if (!is_numeric($(this).val())) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        // var new_qty = parseFloat($(this).val()),

        slitems[item_id].row.base_quantity = new_qty;
        if (slitems[item_id].row.unit != slitems[item_id].row.base_unit) {
            $.each(slitems[item_id].units, function() {
                if (this.id == slitems[item_id].row.unit) {
                    slitems[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }
        slitems[item_id].row.qty = new_qty;
        localStorage.setItem('slitems', JSON.stringify(slitems));
        loadItems();

        if (get_selected_product_quantities < new_qty) {
            $(get_row_id).addClass('danger');
            if (site.settings.overselling != 1) { $('#add_sale, #edit_sale').attr('disabled', true); }
        } else {}

    });

    /* --------------------------
     * Edit Row Price Method
     -------------------------- */
    var old_price;
    $(document).on("focus", '.rprice', function() {
        old_price = $(this).val();
    }).on("change", '.rprice', function() {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val())) {
            $(this).val(old_price);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_price = parseFloat($(this).val()),
            item_id = row.attr('data-item-id');
        slitems[item_id].row.price = new_price;
        localStorage.setItem('slitems', JSON.stringify(slitems));
        loadItems();
    });

    $(document).on("click", '#removeReadonly', function() {
        $('#slcustomer').select2('readonly', false);
        //$('#slwarehouse').select2('readonly', false);
        return false;
    });


});
/* -----------------------
* Misc Actions
----------------------- */

// hellper function for customer if no localStorage value
function nsCustomer() {
    $('#slcustomer').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "customers/suggestions",
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
}
//localStorage.clear();
function loadItems() {

    if (localStorage.getItem('slitems')) {

        check_further_tax = false;
        total = 0;
        count = 1;
        an = 1;
        product_tax = 0;
        invoice_tax = 0;
        product_discount = 0;
        order_discount = 0;
        total_discount = 0;
        my_count = 1;
        price_change = 0;

        $furhter_tax_value_total = 0;
        $furhter_tax_value = 0;

        $("#slTable tbody").empty();
        slitems = JSON.parse(localStorage.getItem('slitems'));
        get_row_id = JSON.parse(localStorage.getItem('row_id'));
        sortedItems = (site.settings.item_addition == 1) ? _.sortBy(slitems, function(o) { return [parseInt(o.order)]; }) : slitems;

        $('#add_sale, #edit_sale').attr('disabled', false);
        $.each(sortedItems, function() {
                further_tax = 0;
                var item = this;
                var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
                item.order = item.order ? item.order : new Date().getTime();
                var product_id = item.row.id,
                    further_tax = item.row.further_tax,
                    item_type = item.row.type,
                    combo_items = item.combo_items,
                    item_price = item.row.price,
                    item_qty = item.row.qty,
                    item_aqty = item.row.quantity,
                    item_tax_method = item.row.tax_method,
                    item_ds = item.row.discount,
                    item_discount = 0,
                    item_option = item.row.option,
                    company_code = item.row.company_code,
                    item_code = item.row.code,
                    item_serial = item.row.serial,
                    item_name = item.row.name.replace(/"/g, "&#034;").replace(/'/g, "&#039;");
                var product_unit = item.row.unit,
                    base_quantity = item.row.base_quantity;
                var unit_price = item.row.real_unit_price;

                customer_type = item.customer_type;
                gst_no = item.gst_no;

                fed_tax_rate = item.row.fed_tax_rate;



                // alert(item.row.batch);

                var select_batch = ((item.row.batch == null) ? "" : item.row.batch.split(","));
                var product_price = ((item.row.product_price == null) ? "" : item.row.product_price.split(","));

                var fed_tax = ((item.row.fed_tax_rate == null) ? "" : item.row.fed_tax_rate.split(","));

                var product_consiment = ((item.row.product_price == null) ? "" : item.row.product_price.split(","));
                var product_mrp = ((item.row.product_mrp == null) ? "" : item.row.product_mrp.split(","));
                var product_dropship = ((item.row.product_dropship == null) ? "" : item.row.product_dropship.split(","));
                var product_crossdock = ((item.row.product_crossdock == null) ? "" : item.row.product_crossdock.split(","));
                var expiry = ((item.row.expiry == null) ? "" : item.row.expiry.split(","));
                var product_purchase_id = ((item.row.purchase_item_id == null) ? "" : item.row.purchase_item_id.split(","));
                var product_batch_quantity = ((item.row.product_batch_quantity == null) ? "" : item.row.product_batch_quantity.split(","));



                // console.log(select_batch);
                // console.log(product_price);
                // console.log(product_mrp);
                // console.log(product_dropship);
                // console.log(product_crossdock);
                // console.log(expiry);
                // console.log(product_purchase_id);
                // console.log("26,28,28".split(","));

                // alert("selected_item_id : " + selected_item_id);
                // alert("item.id : " + item.id);

                // console.log(get_row_id);

                // alert(select_purchase_id);

                // alert("net_unit_price : " + net_unit_price);
                // alert("price_change : " + price_change);
                // alert(JSON.stringify(item.row));

                if (item.units && item.row.fup != 1 && product_unit != item.row.base_unit) {
                    $.each(item.units, function() {
                        if (this.id == product_unit) {
                            base_quantity = formatDecimal(unitToBaseQty(item.row.qty, this), 4);
                            unit_price = formatDecimal((parseFloat(item.row.base_unit_price) * (unitToBaseQty(1, this))), 4);
                        }
                    });
                }
                if (item.options !== false) {
                    $.each(item.options, function() {
                        if (this.id == item.row.option && this.price != 0 && this.price != '' && this.price != null) {
                            item_price = unit_price + (parseFloat(this.price));
                            unit_price = item_price;
                        }
                    });
                }

                var ds = item_ds ? item_ds : '0';
                if (ds.indexOf("%") !== -1) {
                    var pds = ds.split("%");
                    if (!isNaN(pds[0])) {
                        item_discount = formatDecimal((((unit_price) * parseFloat(pds[0])) / 100), 4);
                    } else {
                        item_discount = formatDecimal(ds);
                    }
                } else {
                    item_discount = formatDecimal(ds);
                }
                product_discount += parseFloat(item_discount);
                // product_discount += parseFloat(item_discount * item_qty);

                // alert("item_discount " + item_discount);

                // unit_price = formatDecimal(price_change-item_discount);

                // unit_price = formatDecimal(((customer_type == "consignment") ? formatMoney(item.row.get_selected_product_price) : (customer_type == "dropship") ? formatMoney(item.row.get_selected_product_dropship) : (customer_type == "crossdock") ? formatMoney(item.row.get_selected_product_crossdock) : (customer_type == "services") ? "services" : "0")-item_discount);

                unit_price = formatDecimal(((customer_type == "consignment") ? formatMoney(item.row.get_selected_product_price) : (customer_type == "dropship") ? formatMoney(item.row.get_selected_product_dropship) : (customer_type == "crossdock") ? formatMoney(item.row.get_selected_product_crossdock) : (customer_type == "services") ? "services" : "0"));


                // alert("customer_type " + customer_type);


                // alert("unit_price " + unit_price);



                var pr_tax = item.tax_rate;
                var pr_tax_val = 0,
                    pr_tax_rate = 0;
                if (site.settings.tax1 == 1) {
                    if (pr_tax !== false && pr_tax != 0) {
                        if (pr_tax.type == 1) {

                            if (item_tax_method == '0') {
                                pr_tax_val = formatDecimal((((unit_price) * parseFloat(pr_tax.rate)) / (100 + parseFloat(pr_tax.rate))), 4);
                                pr_tax_rate = formatDecimal(pr_tax.rate) + '%';
                            } else {
                                pr_tax_val = formatDecimal((((unit_price) * parseFloat(pr_tax.rate)) / 100), 4);
                                pr_tax_rate = formatDecimal(pr_tax.rate) + '%';
                            }

                        } else if (pr_tax.type == 2) {

                            pr_tax_val = parseFloat(pr_tax.rate);
                            pr_tax_rate = pr_tax.rate;

                        }
                        product_tax += pr_tax_val * item_qty;
                    }
                }
                item_price = item_tax_method == 0 ? formatDecimal(price_change - pr_tax_val, 4) : formatDecimal(unit_price);

                // console.log("item_tax_method " + item_tax_method);
                // console.log("price_change " + price_change);
                // console.log("pr_tax_val " + pr_tax_val);
                // console.log("unit_price " + unit_price);

                // unit_price = formatDecimal(unit_price+item_discount, 4);
                unit_price = formatDecimal(unit_price, 4);
                var sel_opt = '';
                $.each(item.options, function() {
                    if (this.id == item_option) {
                        sel_opt = this.name;
                    }
                });

                var row_no = item.id;
                var newTr = $('<tr id="row_' + row_no + '" class="row_' + item_id + '" data-item-id="' + item_id + '"></tr>');
                tr_html = '<td><input name="product_id[]" type="hidden" class="rid" value="' + product_id + '"><input name="product_type[]" type="hidden" class="rtype" value="' + item_type + '"><input name="company_code[]" type="hidden" class="rcode" value="' + company_code + '"><input name="product_code[]" type="hidden" class="rcode" value="' + item_code + '"><input name="product_name[]" type="hidden" class="rname" value="' + item_name + '"><input name="product_option[]" type="hidden" class="roption" value="' + item_option + '"><span class="sname" id="name_' + row_no + '">' + item_code + ' - ' + item_name + (sel_opt != '' ? ' (' + sel_opt + ')' : '') + '</span> <i class="pull-right fa fa-edit tip pointer edit" id="' + row_no + '" data-item="' + item_id + '" title="Edit" style="cursor:pointer;"></i></td>';

                if (site.settings.product_serial == 1) {
                    tr_html += '<td class="text-right"><input class="form-control input-sm rserial" name="serial[]" type="text" autocomplete="off" id="serial_' + row_no + '" value="' + item_serial + '"></td>';
                }

                // console.log(customer_type);
                // console.log("Price :" + (item.row.get_selected_product_dropship));

                tr_html += '<td class="text-right"><input class="form-control input-sm text-right rprice" name="net_price[]" type="hidden" id="price_' + row_no + '" value="' + ((customer_type == "consignment") ? (item.row.get_selected_product_price) : (customer_type == "dropship") ? (item.row.get_selected_product_dropship) : (customer_type == "crossdock") ? (item.row.get_selected_product_crossdock) : (customer_type == "services") ? "services" : "0") + '"><input id="ruprice_' + row_no + '" class="ruprice" name="unit_price[]" type="hidden" value="' + ((customer_type == "consignment") ? (item.row.get_selected_product_price) : (customer_type == "dropship") ? (item.row.get_selected_product_dropship) : (customer_type == "crossdock") ? (item.row.get_selected_product_crossdock) : (customer_type == "services") ? "services" : "0") + '"><input id="realuprice_' + row_no + '" class="realuprice" name="real_unit_price[]" type="hidden" value="' + ((customer_type == "consignment") ? (item.row.get_selected_product_price) : (customer_type == "dropship") ? (item.row.get_selected_product_dropship) : (customer_type == "crossdock") ? (item.row.get_selected_product_crossdock) : (customer_type == "services") ? "services" : "0") + '"><span class="text-right sprice" id="sprice_' + row_no + '">' + formatMoney((customer_type == "consignment") ? formatMoney(item.row.get_selected_product_price) : (customer_type == "dropship") ? formatMoney(item.row.get_selected_product_dropship) : (customer_type == "crossdock") ? formatMoney(item.row.get_selected_product_crossdock) : (customer_type == "services") ? "services" : "0") + '</span> </td>';

                tr_html += '<td><input class="form-control text-center rquantity" tabindex="' + ((site.settings.set_focus == 1) ? an : (an + 1)) + '" name="quantity[]" type="text" autocomplete="off" value="' + formatQuantity2(item_qty) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="quantity_' + row_no + '" onClick="this.select();"><input name="product_unit[]" type="hidden" class="runit" value="' + product_unit + '"><input name="product_base_quantity[]" type="hidden" class="rbase_quantity" value="' + base_quantity + '"><input name="product_base_quantity_old[]" type="hidden" class="rbase_quantity" value="' + base_quantity + '"></td>';

                if ((select_batch == "empty") || (select_batch == null)) {
                    tr_html += '<td class="text-center"> - </td>';
                    tr_html += '<td class="text-center"> - </td>';
                    tr_html += '<td class="text-center"> - </td>';
                } else {
                    tr_html += '<td><input class="form-control text-center batch_remain_quantity" name="batch_remain_quantity[]" type="text" autocomplete="off" value="' + Math.trunc(item.row.get_selected_product_quantities) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="batch_remain_quantity_' + row_no + '" disabled>  <input name="remain_quantity[]" type="hidden" value="' + Math.trunc(item.row.get_selected_product_quantities) + '" > </td>';
                    // alert(" 123456 " + select_purchase_id);
                    let counter = 0;
                    tr_html += '<td><select id="batch_number_' + row_no + '" name="batch_number[]" class="batch_number form-control" data-item-id="' + row_no + '">';
                    tr_html += '<option value="">Select</option>';
                    for (let index_select = 0; index_select < select_batch.length; index_select++) {
                        tr_html += '<option ' + ((item.row.get_selected_batch_code == select_batch[index_select]) ? "selected" : " ") + ' value="' + select_batch[index_select] + '">' + select_batch[index_select] + '</option>';

                        // tr_html += '<option ' + ((item.row.get_selected_purchase_id == select_batch[index_select]) ? "selected" : ((item.row.selected_batch == select_batch[index_select]) ? "selected" : "")) + ' value="'+select_batch[index_select]+'">'+select_batch[index_select]+'</option>';
                        // tr_html += '<option ' + ((item.row.get_selected_purchase_id == product_purchase_id[index_select]) ? "selected" : " ") + ' value="'+product_purchase_id[index_select]+'">'+select_batch[index_select]+'</option>';
                        // tr_html += '<option  value="'+product_purchase_id[index_select]+'">'+select_batch[index_select]+'</option>';
                    }
                    tr_html += '</select>';
                    for (let index_select = 0; index_select < select_batch.length; index_select++) {
                        tr_html += '<input id="product_price_' + row_no + '_' + index_select + '" name="product_price" type="hidden" class="runit" value="' + product_price[index_select] + '">';
                        tr_html += '<input id="product_consiment_' + row_no + '_' + index_select + '" name="product_price" type="hidden" class="runit" value="' + product_consiment[index_select] + '">';
                        tr_html += '<input id="product_mrp_' + row_no + '_' + index_select + '"  name="product_mrp" type="hidden" class="runit" value="' + product_mrp[index_select] + '">'
                        tr_html += '<input id="product_dropship_' + row_no + '_' + index_select + '"  name="product_dropship" type="hidden" class="runit" value="' + product_dropship[index_select] + '">';
                        tr_html += '<input id="product_crossdock_' + row_no + '_' + index_select + '"  name="product_crossdock" type="hidden" class="runit" value="' + product_crossdock[index_select] + '">';
                        tr_html += '<input id="expiry_' + row_no + '_' + index_select + '"  name="product_expiry" type="hidden" class="runit" value="' + expiry[index_select] + '">';
                        tr_html += '<input id="product_purchase_id_' + row_no + '_' + index_select + '"  name="product_purchase_id" type="hidden" class="runit" value="' + product_purchase_id[index_select] + '">';
                        tr_html += '<input id="product_batch_quantity_' + row_no + '_' + index_select + '"  name="product_batch_quantity" type="hidden" class="runit" value="' + product_batch_quantity[index_select] + '">';

                        tr_html += '<input id="fed_tax_' + row_no + '_' + index_select + '" name="fed_tax" type="hidden" class="runit" value="' + fed_tax[index_select] + '">';

                        // tr_html += '<input id="purchase_items_id_' + row_no + '"  name="purchase_items_id" type="hidden" class="runit" value="' + purchase_item_id[counter] + '">';
                    }
                    tr_html += '</td>';
                    // tr_html += '<td class="text-right" style="display:none" ><input class="rureal_price" name="purchase_price[]" type="hidden" value="' + (item.row.consiment) + '"></td>';
                    // tr_html += '<td class="text-right" style="display:none"><input class="rureal_price" name="purchase_dropship[]" type="hidden" value="' + (item.row.dropship) + '"></td>';
                    // tr_html += '<td class="text-right" style="display:none" ><input class="rureal_price" name="purchase_crossdock[]" type="hidden" value="' + (item.row.crossdock) + '"></td>';
                    // tr_html += '<td class="text-right" style="display:none" ><input class="rureal_price" name="purchase_mrp[]" type="hidden" value="' + (item.row.mrp) + '"></td>';



                    tr_html += '<td class="text-right" style="display:none" ><input class="rureal_price" name="purchase_price[]" type="hidden" value="' + (item.row.get_selected_product_consiment) + '"></td>';
                    tr_html += '<td class="text-right" style="display:none"><input class="rureal_price" name="purchase_dropship[]" type="hidden" value="' + (item.row.get_selected_product_dropship) + '"></td>';
                    tr_html += '<td class="text-right" style="display:none" ><input class="rureal_price" name="purchase_crossdock[]" type="hidden" value="' + (item.row.get_selected_product_crossdock) + '"></td>';
                    tr_html += '<td class="text-right" style="display:none" ><input class="rureal_price" name="purchase_mrp[]" type="hidden" value="' + (item.row.get_selected_product_mrp) + '"></td>';


                    // counter++;
                    tr_html += '<td><input class="form-control text-center date rprod_expiry" id="rprod_expiry_' + item_id + '" value="' + (((batch_expiry_date !== null && batch_expiry_date !== '')) ? item.row.get_selected_expiry : ((item.row.selected_expiry) ? "11" : "22")) + '" name="expiry[]" type="text" autocomplete="off" tabindex="' + ((site.settings.set_focus == 1) ? an : (an + 1)) + '" value="" data-id="' + row_no + '" data-item="' + item_id + '" id="podate" onClick=""></td>';
                    // tr_html += '<td><input class="form-control text-center datetime rprod_expiry" value="' + ((item.row.expiry !== null && item.row.expiry !== '') ? item.row.expiry : ""  ) + '" name="expiry[]" type="text" autocomplete="off" tabindex="'+((site.settings.set_focus == 1) ? an : (an+1))+'" value="" data-id="' + row_no + '" data-item="' + item_id + '" id="podate" onClick=""></td>';
                }


                // tr_html += '<td> <input id="discount_one_' + row_no + '"  class="discount_one" name="discount_one_' + my_count + '" ' + (((JSON.parse(item.row.discount_one_checked) == true) ? "checked" : "")) + '  type="checkbox" value="' + (((item.row.discount_one)) ? (item.row.discount_one).slice(0, 5) : 0) + '" onClick="this.select();"  data-id="' + row_no + '" data-item="' + item_id + '" > ( ' + (((item.row.discount_one)) ? (item.row.discount_one).slice(0, 5) : 0) + '% ) <br> ' + formatMoney(((item.row.get_selected_product_consiment * (item.row.discount_one)) / 100) * /* get_selected_product_price */ item.row.base_quantity) + '  </td>';


                tr_html += '<td> <input id="discount_one_' + row_no + '"  class="discount_one" name="discount_one_' + my_count + '" ' + (((JSON.parse(item.row.discount_one_checked) == true) ? "checked" : "")) + '  type="checkbox" value="' + (((item.row.discount_one)) ? (item.row.discount_one).slice(0, 5) : 0) + '" onClick="this.select();"  data-id="' + row_no + '" data-item="' + item_id + '" > ( ' + (((item.row.discount_one)) ? (item.row.discount_one).slice(0, 5) : 0) + '% ) <br> ' + formatMoney(((item.row.get_selected_product_price * (item.row.discount_one)) / 100) * /* get_selected_product_price */ item.row.base_quantity) + '  </td>';

                // tr_html += '<td><input id="discount_two_' + row_no + '"  class="discount_two" name="discount_two_' + my_count + '" ' + ((JSON.parse(item.row.discount_two_checked) == true) ? "checked" : "") + ' type="checkbox" value="' + (((item.row.discount_two)) ? (item.row.discount_two).slice(0, 5) : 0) + '" onClick="this.select();"  data-id="' + row_no + '" data-item="' + item_id + '" > ( ' + (((item.row.discount_two)) ? (item.row.discount_two).slice(0, 5) : 0) + '% ) <br> ' + formatMoney(((item.row.get_selected_product_consiment * (item.row.discount_two)) / 100) * /* get_selected_product_price */ item.row.base_quantity) + '  </td>';


                tr_html += '<td><input id="discount_two_' + row_no + '"  class="discount_two" name="discount_two_' + my_count + '" ' + ((JSON.parse(item.row.discount_two_checked) == true) ? "checked" : "") + ' type="checkbox" value="' + (((item.row.discount_two)) ? (item.row.discount_two).slice(0, 5) : 0) + '" onClick="this.select();"  data-id="' + row_no + '" data-item="' + item_id + '" > ( ' + (((item.row.discount_two)) ? (item.row.discount_two).slice(0, 5) : 0) + '% ) <br> ' + formatMoney(((item.row.get_selected_product_price * (item.row.discount_two)) / 100) * /* get_selected_product_price */ item.row.base_quantity) + '  </td>';

                // alert(item.row.consiment);
                // alert(item.row.discount_two);
                // alert(item.row.base_quantity);
                // get_selected_product_price

                tr_html += '<td><input id="discount_three_' + row_no + '"  class="discount_three" name="discount_three_' + my_count + '" ' + ((JSON.parse(item.row.discount_three_checked) == true) ? "checked" : "") + ' type="checkbox" value="' + (((item.row.discount_three)) ? (item.row.discount_three).slice(0, 5) : 0) + '" onClick="this.select();"  data-id="' + row_no + '" data-item="' + item_id + '" > ( ' + (((item.row.discount_three)) ? (item.row.discount_three).slice(0, 5) : 0) + '% ) <br> ' + formatMoney(((item.row.get_selected_product_price * (item.row.discount_three)) / 100) * /* get_selected_product_price */ item.row.base_quantity) + '  </td>';


                // tr_html += '<td><input id="discount_three_' + row_no + '"  class="discount_three" name="discount_three_' + my_count + '" ' + ((JSON.parse(item.row.discount_three_checked) == true) ? "checked" : "") + ' type="checkbox" value="' + (((item.row.discount_three)) ? (item.row.discount_three).slice(0, 5) : 0) + '" onClick="this.select();"  data-id="' + row_no + '" data-item="' + item_id + '" > ( ' + (((item.row.discount_three)) ? (item.row.discount_three).slice(0, 5) : 0) + '% ) <br> ' + formatMoney(((item.row.get_selected_product_consiment * (item.row.discount_three)) / 100) * /* get_selected_product_price */ item.row.base_quantity) + '  </td>';


                tr_html += '<td><input id="fed_tax_' + row_no + '"  class="form-control input-sm text-right fed_tax" name="fed_tax_' + my_count + '" type="text" autocomplete="off" value="' + (((item.row.get_selected_fed_tax_rate)) ? (item.row.get_selected_fed_tax_rate) : 0) + '" onClick="this.select();"  data-id="' + row_no + '" data-item="' + item_id + '" readonly> </td>';


                // ((customer_type == "consignment") ? formatMoney(item.row.get_selected_product_price) : (customer_type == "dropship") ? formatMoney(item.row.get_selected_product_dropship) : (customer_type == "crossdock") ? formatMoney(item.row.get_selected_product_crossdock) : (customer_type == "services") ? "services" : "0")

                if ((site.settings.product_discount == 1 && allow_discount == 1) || item_discount) {
                    tr_html += '<td class="text-right"><input class="form-control input-sm rdiscount" name="product_discount[]" type="hidden" id="discount_' + row_no + '" value="' + item_ds + '"><span class="text-right sdiscount text-danger" id="sdiscount_' + row_no + '">' + /* formatMoney(0 - (item_discount * item_qty))*/ formatMoney(0 - (item_ds)) + '</span></td>';
                }


                // console.log(site.settings.tax1);

                // if (site.settings.tax1 == 1) {
                //     tr_html += '<td class="text-right"><input class="form-control input-sm text-right rproduct_tax" name="product_tax[]" type="hidden" id="product_tax_' + row_no + '" value="' + pr_tax.id + '"><span class="text-right sproduct_tax" id="sproduct_tax_' + row_no + '">' + (parseFloat(pr_tax_rate) != 0 ? '(' + formatDecimal(pr_tax_rate) + ')' : '') + ' ' + formatMoney(pr_tax_val * item_qty) + '</span></td>';
                // }


                // Consignment Condition
                if (customer_type == "consignment") {
                    if (site.settings.tax1 == 1) {
                        tr_html += '<td class="text-right"><input class="form-control input-sm text-right rproduct_tax" name="product_tax[]" type="hidden" id="product_tax_' + row_no + '" value="' + pr_tax.id + '"><span class="text-right sproduct_tax" id="sproduct_tax_' + row_no + '">' + (parseFloat(pr_tax_rate) != 0 ? '(' + formatDecimal(pr_tax_rate) + ')' : '') + ' ' + formatMoney(pr_tax_val * item_qty) + '<br>' + pr_tax_val + '</span></td>';
                    }
                } else {
                    if (site.settings.tax1 == 1) {
                        tr_html += '<td class="text-right"><input class="form-control input-sm text-right rproduct_tax" name="product_tax[]" type="hidden" id="product_tax_' + row_no + '" value="0"><span class="text-center sproduct_tax" id="sproduct_tax_' + row_no + '" style="display: block;"> 0 </span></td>';
                    }
                }


                // Check edit page and reverse calculate
                if (item.edit_function) {
                    if (item.gst_no == "") {
                        if (customer_type == "consignment") {
                            if (item.tax_rate.type == 1) {
                                check_further_tax = true;
                                $furhter_tax_value = item.row.further_tax;
                                $furhter_tax_value_total += $furhter_tax_value;
                                tr_html += '<td class="text-right"><input class="form-control input-sm text-right rproduct_tax" name="further_tax[]" type="hidden" id="further_tax_' + row_no + '" value="' + $furhter_tax_value + '"><span class="text-right sproduct_tax" id="sproduct_tax_' + row_no + '">' + '(' + formatDecimal((item.row.further_tax * 100) / ((parseFloat(item_price) + ((customer_type == "consignment") ? parseFloat(pr_tax_val) : 0)) * parseFloat(item_qty) - item_ds)) + ')' + '<br> ' + formatMoney($furhter_tax_value) + '</span></td>';
                            } else {
                                tr_html += '<td class="text-right"><span class="text-right sproduct_tax" > 0 </span></td>';
                            }
                        } else {
                            tr_html += '<td class="text-right"><span class="text-right sproduct_tax" > 0 </span></td>';
                        }
                    } else {
                        tr_html += '<td class="text-right"><span class="text-right sproduct_tax" > 0 </span></td>';
                    }
                } else {
                    if (item.gst_no == "") {
                        if (customer_type == "consignment") {
                            if (item.tax_rate.type == 1) {
                                check_further_tax = true;
                                $furhter_tax_value = (((item.row.get_selected_product_price * (item.row.further_tax)) / 100) * item.row.base_quantity);
                                $furhter_tax_value_total += $furhter_tax_value;
                                tr_html += '<td class="text-right"><input class="form-control input-sm text-right rproduct_tax" name="further_tax[]" type="hidden" id="further_tax_' + row_no + '" value="' + $furhter_tax_value + '"><span class="text-right sproduct_tax" id="sproduct_tax_' + row_no + '">' + '(' + formatDecimal(item.row.further_tax) + ')' + '<br> ' + formatMoney($furhter_tax_value) + '</span></td>';
                            } else {
                                tr_html += '<td class="text-right"><span class="text-right sproduct_tax" > 0 </span></td>';
                            }
                        } else {
                            tr_html += '<td class="text-right"><span class="text-right sproduct_tax" > 0 </span></td>';
                        }
                    } else {
                        tr_html += '<td class="text-right"><span class="text-right sproduct_tax" > 0 </span></td>';
                    }
                }






                // if (site.settings.tax1 == 1) {
                //     tr_html += '<td class="text-right"><input class="form-control input-sm text-right rproduct_tax" name="product_tax[]" type="hidden" id="product_tax_' + row_no + '" value="' + pr_tax.id + '"><span class="text-right sproduct_tax" id="sproduct_tax_' + row_no + '">' + (parseFloat(pr_tax_rate) != 0 ? '(' + formatDecimal(pr_tax_rate) + ')' : '') + ' ' + formatMoney(pr_tax_val * item_qty) + '</span></td>';
                // }

                // tr_html += '<td class="text-right"><span class="text-right ssubtotal" id="subtotal_' + row_no + '">' + formatMoney(((parseFloat(item_price) + parseFloat(pr_tax_val) ) * parseFloat(item_qty) - item_ds)) + '</span></td>';

                // console.log($furhter_tax_value);


                console.log("row_no " + row_no);
                console.log("item_price " + parseFloat(item_price));
                console.log("customer_type " + customer_type);
                console.log("pr_tax_val " + parseFloat(pr_tax_val));
                console.log("item_qty " + parseFloat(item_qty));
                console.log("item_ds " + item_ds);
                console.log("item.gst " + item.gst);
                console.log("item.tax_rate.type " + item.tax_rate.type);
                console.log("item.tax_rate.rate " + item.tax_rate.rate);
                console.log("furhter_tax_value " + $furhter_tax_value);
                console.log("get_selected_fed_tax_rate " + item.row.get_selected_fed_tax_rate);


                formatMoney(
                    (
                        (parseFloat(item_price) + ((customer_type == "consignment") ? parseFloat(pr_tax_val) : 0)) *
                        parseFloat(item_qty) - item_ds) +
                    (((item.gst_no == "") && (customer_type == "consignment") && (item.tax_rate.type == 1)) ?
                        parseFloat($furhter_tax_value) :
                        0) tr_html += '<td class="text-right"><span class="text-right ssubtotal" id="subtotal_' + row_no + '">' + formatMoney(((parseFloat(item_price) + ((customer_type == "consignment") ? parseFloat(pr_tax_val) : 0)) * parseFloat(item_qty) - item_ds) + (((item.gst_no == "") && (customer_type == "consignment") && (item.tax_rate.type == 1)) ? parseFloat($furhter_tax_value) : 0)) + '</span></td>';


                    tr_html += '<td class="text-center"><i class="fa fa-times tip pointer sldel" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>'; newTr.html(tr_html); newTr.prependTo("#slTable");
                    // total += formatDecimal(((parseFloat(item_price) + parseFloat(pr_tax_val)) * parseFloat(item_qty) - item_ds), 4);
                    total += formatDecimal(((parseFloat(item_price) + ((customer_type == "consignment") ? parseFloat(pr_tax_val) : 0)) * parseFloat(item_qty) - item_ds + (((item.gst_no == "") && (customer_type == "consignment") && (item.tax_rate.type == 1)) ? parseFloat($furhter_tax_value) : 0)), 4); count += parseFloat(item_qty); an++; my_count++;

                    // if (item_type == 'standard' && item.options !== false) {
                    //     $.each(item.options, function() {
                    //         if (this.id == item_option && base_quantity > this.quantity) {
                    //             $('#row_' + row_no).addClass('danger');
                    //             if (site.settings.overselling != 1) { $('#add_sale, #edit_sale').attr('disabled', true); }
                    //         }
                    //     });
                    // } else if (item_type == 'standard' && base_quantity > item_aqty) {
                    //     $('#row_' + row_no).addClass('danger');
                    //     if (site.settings.overselling != 1) { $('#add_sale, #edit_sale').attr('disabled', true); }
                    // } else if (item_type == 'combo') {
                    //     if (combo_items === false) {
                    //         $('#row_' + row_no).addClass('danger');
                    //         if (site.settings.overselling != 1) { $('#add_sale, #edit_sale').attr('disabled', true); }
                    //     } else {
                    //         $.each(combo_items, function() {
                    //             if (parseFloat(this.quantity) < (parseFloat(this.qty) * base_quantity) && this.type == 'standard') {
                    //                 $('#row_' + row_no).addClass('danger');
                    //                 if (site.settings.overselling != 1) { $('#add_sale, #edit_sale').attr('disabled', true); }
                    //             }
                    //         });
                    //     }
                    // }

                    // $('#rprod_expiry_'+selected_item_id).val(item.row.get_selected_expiry);
                    // $('#rprod_expiry_'+selected_item_id).val(item.row.get_selected_expiry);

                    // // // // alert(selected_item_id);

                    // // // // if((customer_type == "consignment") && (selected_item_id == item.id)) {

                    // // // //     alert("consignment");
                    // // // //     // select_purchase_id = item.row.get_selected_purchase_id;
                    // // // //     select_net_unit_price = item.row.get_selected_product_price;

                    // // // //     $("#rprice_"+selected_item_id).val(select_net_unit_price);
                    // // // //     $("#ruprice_"+selected_item_id).val(select_net_unit_price);
                    // // // //     $("#realuprice_"+selected_item_id).val(select_net_unit_price);
                    // // // //     $('#sprice_'+selected_item_id).html(select_net_unit_price);

                    // // // // } else if((customer_type == "dropship") && (selected_item_id == item.id)) {

                    // // // //     alert("dropship");

                    // // // //     // select_purchase_id = item.row.get_selected_purchase_id;
                    // // // //     select_net_unit_price = item.row.get_selected_product_dropship;

                    // // // //     alert(select_net_unit_price);

                    // // // //     $("#rprice_"+selected_item_id).val(select_net_unit_price);
                    // // // //     $("#ruprice_"+selected_item_id).val(select_net_unit_price);
                    // // // //     $("#realuprice_"+selected_item_id).val(select_net_unit_price);
                    // // // //     $('#sprice_'+selected_item_id).html(select_net_unit_price);


                    // // // // } else if((customer_type == "crossdock") && (selected_item_id == item.id)) {

                    // // // //     alert("crossdock");

                    // // // //     // select_purchase_id = item.row.get_selected_purchase_id;
                    // // // //     select_net_unit_price = item.row.get_selected_product_crossdock;

                    // // // //     $("#rprice_"+selected_item_id).val(select_net_unit_price);
                    // // // //     $("#ruprice_"+selected_item_id).val(select_net_unit_price);
                    // // // //     $("#realuprice_"+selected_item_id).val(select_net_unit_price);
                    // // // //     $('#sprice_'+selected_item_id).html(select_net_unit_price);

                    // // // // } else if((customer_type == "services") && (selected_item_id == item.id)) {
                    // // // //     // select_purchase_id = get_row_id[selected_item_id].purchase_id;
                    // // // //     select_net_unit_price =  "1";
                    // // // //     // $("#print_"+selected_item_id).val(get_row_id.selected_item_id.product_price);
                    // // // // }

                });

            var col = 2;
            if (site.settings.product_serial == 1) { col++; }
            var tfoot = '<tr id="tfoot" class="tfoot active"><th colspan="' + col + '">Total</th><th class="text-center">' + formatQty(parseFloat(count) - 1) + '</th><th class="text-center"></th><th class="text-center"></th><th class="text-center"></th><th class="text-center"></th><th class="text-center"></th><th class="text-center"></th><th class="text-center"></th>';
            if ((site.settings.product_discount == 1 && allow_discount == 1) || product_discount) {
                tfoot += '<th class="text-right">' + formatMoney(product_discount) + '</th>';
            }


            // if (site.settings.tax1 == 1) {
            //     tfoot += '<th class="text-right">' + formatMoney(product_tax) + '</th>';
            // }

            if (customer_type == "consignment") {
                if (site.settings.tax1 == 1) {
                    tfoot += '<th class="text-right">' + formatMoney(product_tax) + '</th>';
                }
            } else {
                tfoot += '<th class="text-center"> 0 </th>';
            }



            // alert(check_further_tax);
            if (check_further_tax == true) {

                tfoot += '<th class="text-center"> ' + formatMoney($furhter_tax_value_total) + ' </th>';
                //             tfoot += '<th class="text-right">' + formatMoney(((item.row.get_selected_product_price * (item.further_tax)) / 100) * item.row.base_quantity) + '</span></th>';
                //         } else {
                //             // tfoot += '<th class="text-right"><input class="form-control input-sm text-right rproduct_tax" name="product_tax[]" type="hidden" id="product_tax_' + row_no + '" value="' + pr_tax.id + '"><span class="text-right sproduct_tax" id="sproduct_tax_' + row_no + '"> 0 </span></th>';
            } else {
                tfoot += '<th class="text-center"> 0 </th>';
            }


            tfoot += '<th class="text-right">' + formatMoney(total) + '</th><th class="text-center"><i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i></th></tr>'; $('#slTable tfoot').html(tfoot);

            // Order level discount calculations
            if (sldiscount = localStorage.getItem('sldiscount')) {
                var ds = sldiscount;
                if (ds.indexOf("%") !== -1) {
                    var pds = ds.split("%");
                    if (!isNaN(pds[0])) {
                        order_discount = formatDecimal((((total) * parseFloat(pds[0])) / 100), 4);
                    } else {
                        order_discount = formatDecimal(ds);
                    }
                } else {
                    order_discount = formatDecimal(ds);
                }

                //total_discount += parseFloat(order_discount);
            }

            // Order level tax calculations
            if (site.settings.tax2 != 0) {
                if (sltax2 = localStorage.getItem('sltax2')) {
                    $.each(tax_rates, function() {
                        if (this.id == sltax2) {
                            if (this.type == 2) {
                                invoice_tax = formatDecimal(this.rate);
                            } else if (this.type == 1) {
                                invoice_tax = formatDecimal((((total - order_discount) * this.rate) / 100), 4);
                            }
                        }
                    });
                }
            }

            // alert(product_discount);
            // alert(order_discount);

            total_discount = parseFloat(order_discount + product_discount);
            // Totals calculations after item addition
            var gtotal = parseFloat(((total + invoice_tax) - order_discount) + shipping); $('#total').text(formatMoney(total)); $('#titems').text((an - 1) + ' (' + formatQty(parseFloat(count) - 1) + ')'); $('#total_items').val((parseFloat(count) - 1));
            //$('#tds').text('('+formatMoney(product_discount)+'+'+formatMoney(order_discount)+')'+formatMoney(total_discount));
            $('#tds').text(formatMoney(order_discount));
            if (site.settings.tax2 != 0) {
                $('#ttax2').text(formatMoney(invoice_tax));
            }
            $('#tship').text(formatMoney(shipping)); $('#gtotal').text(formatMoney(gtotal));
            if (an > parseInt(site.settings.bc_fix) && parseInt(site.settings.bc_fix) > 0) {
                $("html, body").animate({ scrollTop: $('#sticker').offset().top }, 500);
                $(window).scrollTop($(window).scrollTop() + 1);
            }
            if (count > 1) {
                $('#slcustomer').select2("readonly", true);
                $('#slwarehouse').select2("readonly", true);
            }
            set_page_focus();
        }
    }


    /* --------------------------
        * Change Batch and Show Price Method
        -------------------------- */
    // var old_row_qty;
    // var consiment_cost;
    // var get_class;
    // var discount_with_qty = 0;

    $(document).on("change", '.batch_number', function() {


        var row = $(this).closest('tr');
        item_id = row.attr('data-item-id');

        get_row_item_from_localstorage = JSON.parse(localStorage.getItem('slitems'));

        get_product_bar_code = get_row_item_from_localstorage[item_id].row.code;

        select_index = ($("#batch_number_" + item_id).prop('selectedIndex') - 1);
        // id = "#product_price_"+item_id+"_"+select_index;
        // console.log($(id).val());

        get_selected_batch_code = $("#batch_number_" + item_id + " option:selected").text();
        // alert(get_selected_batch_code);
        get_selected_purchase_id = $("#batch_number_" + item_id).val();
        // alert(get_selected_purchase_id);
        get_selected_product_price = $("#product_price_" + item_id + "_" + select_index).val();
        // alert(get_selected_product_price);
        get_selected_product_consiment = $("#product_consiment_" + item_id + "_" + select_index).val();
        // alert(get_selected_product_consiment);
        get_selected_product_mrp = $("#product_mrp_" + item_id + "_" + select_index).val();
        // alert(get_selected_product_mrp);
        get_selected_product_dropship = $("#product_dropship_" + item_id + "_" + select_index).val();
        // alert(get_selected_product_dropship);
        get_selected_product_crossdock = $("#product_crossdock_" + item_id + "_" + select_index).val();
        // alert(get_selected_product_crossdock);
        get_selected_expiry = $("#expiry_" + item_id + "_" + select_index).val();
        // alert(get_selected_expiry);
        get_selected_product_quantities = $("#product_batch_quantity_" + item_id + "_" + select_index).val();

        // alert(get_selected_expiry);
        get_selected_fed_tax_rate = $("#fed_tax_" + item_id + "_" + select_index).val();


        $.ajax({
            type: "get",
            async: false,
            url: site.base_url + "sales/get_remain_quantity",
            dataType: "json",
            data: { "get_product_bar_code": get_product_bar_code, "get_selected_batch_code": get_selected_batch_code },
            success: function(data) {
                // alert("already");
                get_selected_product_quantities = data.quantity_balance;
                get_selected_fed_tax_rate = data.get_selected_fed_tax_rate;




                get_selected_purchase_id = data.get_selected_purchase_id;
                get_selected_product_price = data.get_selected_product_price;
                get_selected_product_consiment = data.get_selected_product_consiment
                get_selected_product_mrp = data.get_selected_product_mrp;
                get_selected_product_dropship = data.get_selected_product_dropship;
                get_selected_product_crossdock = data.get_selected_product_crossdock;
                // callback(data[0]);
            }
        });



        slitems[item_id].row.get_selected_batch_code = get_selected_batch_code;
        slitems[item_id].row.get_selected_purchase_id = get_selected_purchase_id;
        slitems[item_id].row.get_selected_product_price = get_selected_product_price;
        slitems[item_id].row.get_selected_product_consiment = get_selected_product_consiment;
        slitems[item_id].row.get_selected_product_mrp = get_selected_product_mrp;
        slitems[item_id].row.get_selected_product_dropship = get_selected_product_dropship;
        slitems[item_id].row.get_selected_product_crossdock = get_selected_product_crossdock;
        slitems[item_id].row.get_selected_expiry = get_selected_expiry;
        slitems[item_id].row.get_selected_product_quantities = get_selected_product_quantities;
        slitems[item_id].row.get_selected_fed_tax_rate = get_selected_fed_tax_rate;





        // row_id[selected_row_id] = "0123";
        // slitems[item_id].row = { "batch_number":get_selected_batch_code, "purchase_id":get_selected_purchase_id, "product_price": get_selected_product_price, "product_mrp": get_selected_product_mrp, "product_dropship": get_selected_product_dropship, "product_crossdock": get_selected_product_crossdock, "expiry": get_selected_expiry};

        // console.log(row_id[selected_row_id]);                                

        localStorage.setItem('slitems', JSON.stringify(slitems));

        // get_selected_batch_code = $("#batch_number_"+selected_row_id+" option:selected").text();
        // alert(selected_row_id);


        // $.ajax({
        //     type: "get", async: false,
        //     url: site.base_url + "sales/get_purchase_list/",
        //     dataType: "json",
        //     data: { "purchase_id": get_selected_purchase_id, "batch_code": get_selected_batch_code },
        //     success: function (data) {

        //         if(customer_type == "consignment") {

        //             net_unit_price = data[0]['price'];

        //         } else if(customer_type == "dropship") {

        //             // price_change = item.row.product_dropship;
        //             net_unit_price = data[0]['dropship'];

        //         } else if(customer_type == "crossdock") {

        //             // price_change = item.row.product_crossdock;
        //             net_unit_price = data[0]['crossdock'];

        //         } else if(customer_type == "services") {
        //             price_change = "1";
        //         }

        //         batch_expiry_date = data[0]['expiry'];
        //         alert(JSON.stringify(data));

        //         // alert(net_unit_price);

        //         check_select_batch = get_selected_purchase_id;



        //alert(selected_item_id);



        selected_item_id = item_id;
        loadItems();

        //     }
        // });

    });

    /* -----------------------------
    * Add Sale Order Item Function
    * @param {json} item
    * @returns {Boolean}
    ---------------------------- */
    function add_invoice_item(item) {

        if (count == 1) {
            slitems = {};
            if ($('#slwarehouse').val() && $('#slcustomer').val()) {
                $('#slcustomer').select2("readonly", true);
                $('#slwarehouse').select2("readonly", true);
            } else {
                bootbox.alert(lang.select_above);
                item = null;
                return;
            }
        }
        if (item == null)
            return;

        var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
        if (slitems[item_id]) {

            var new_qty = parseFloat(slitems[item_id].row.qty) + 1;
            slitems[item_id].row.base_quantity = new_qty;
            if (slitems[item_id].row.unit != slitems[item_id].row.base_unit) {
                $.each(slitems[item_id].units, function() {
                    if (this.id == slitems[item_id].row.unit) {
                        slitems[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                    }
                });
            }
            slitems[item_id].row.qty = new_qty;

        } else {
            slitems[item_id] = item;
        }
        slitems[item_id].order = new Date().getTime();
        localStorage.setItem('slitems', JSON.stringify(slitems));
        loadItems();
        return true;
    }

    if (typeof(Storage) === "undefined") {
        $(window).bind('beforeunload', function(e) {
            if (count > 1) {
                var message = "You will loss data!";
                return message;
            }
        });
    }