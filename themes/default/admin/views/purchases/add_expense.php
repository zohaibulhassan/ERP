<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Deposit in Wallet'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("purchases/add_expense", $attrib); ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= lang("date", "date"); ?>
                            <?= form_input('date', (isset($_POST['date']) ? $_POST['date'] : ""), 'class="form-control date" id="date" required="required"'); ?>
                        </div>
                        <div class="form-group">
                            <?= lang("reference", "reference"); ?>
                            <?= form_input('reference', (isset($_POST['reference']) ? $_POST['reference'] : $exnumber), 'class="form-control tip" id="reference" required="required"'); ?>
                        </div>

                        <div class="form-group">
                            <label>Expence Type</label>
                            <select name="type" class="form-control" id="extype">
                                <option value="general" selected >General</option>
                                <option value="inbound" >Inbound</option>
                                <option value="outbound">Outbound</option>
                            </select>
                        </div>
                        <div class="form-group inboundfield">
                            <label>Purchase</label>
                            <select class="form-control" id="purchaseselect" multiple name="pinovice[]"></select>
                        </div>
                        <div class="form-group outboundfield" >
                            <label>Sale Inovice</label>
                            <select class="form-control" id="invoiceselect" multiple name="sinovices[]"></select>
                        </div>
                        <div class="form-group supplierfield">
                            <label>Supplier Name</label>
                            <select class="form-control" id="suplierselect" multiple name="supplier[]"></select>
                        </div>
                        <div class="form-group outboundfield">
                            <label>Customer Name</label>
                            <select class="form-control" id="customerselect" multiple name="customer[]"></select>
                        </div>
                        <div class="form-group">
                            <label>Own Company</label>
                            <select name="owncompany" class="form-control"  required="required">
                                <option value="">Select Option</option>
                                <?php
                                    foreach($own_company as $oc){
                                        echo '<option value="'.$oc->id.'">'.$oc->companyname.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="paymethod" id="paymentmethod" class="form-control" required="required">
                                <option value="cash">Cash</option>
                                <option value="cheque">Cheque</option>
                                <option value="onlinetransfer">Online Transfer</option>
                                <option value="payorder">Pay Order</option>
                            </select>
                        </div>
                        <div class="form-group" id="chequediv" style="display:none" >
                            <label>Cheque No</label>
                            <input type="text" name="ex_chequeno" id="ex_chequeno" class="form-control">
                        </div>
                        <div class="form-group" id="cashdiv">
                            <label>Wallet</label>
                            <select name="wallet" class="form-control" >
                                <option value="">Select Wallet</option>
                                <?php
                                    foreach($wallets as $w){
                                ?>
                                    <option value="<?php echo $w->id; ?>"><?php echo $w->title; ?></option>
                                <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" id="otdiv" style="display:none" >
                            <label>Transfer No</label>
                            <input type="text" name="ex_transfer" id="ex_transfer" class="form-control">
                        </div>
                        <div class="form-group" id="podiv" style="display:none" >
                            <label>Pay Order</label>
                            <input type="text" name="ex_payorder" id="ex_payorder" class="form-control">
                        </div>
                        <div class="form-group">
                            <?= lang('category', 'category'); ?>
                            <?php
                            $ct[''] = lang('select').' '.lang('category');
                            if ($categories) {
                                foreach ($categories as $category) {
                                    $ct[$category->id] = $category->name;
                                }
                            }
                            ?>
                            <?= form_dropdown('category', $ct, set_value('category'), 'class="form-control tip" id="category" required="required"'); ?>
                        </div>

                        <div class="form-group">
                            <?= lang("location", "location"); ?>
                            <?php
                            $wh[''] = lang("select") . ' ' . lang("location");
                            foreach ($warehouses as $warehouse) {
                                $wh[$warehouse->id] = $warehouse->name;
                            }
                            echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'id="warehouse" class="form-control input-tip select" style="width:100%;" required="required"');
                            ?>
                        </div>

                        <div class="form-group">
                            <?= lang("amount", "amount"); ?>
                            <input name="amount" type="text" id="amount" value="" class="pa form-control kb-pad amount"
                                required="required"/>
                        </div>

                        <div class="form-group">
                            <?= lang("attachment", "attachment") ?>
                            <input id="attachment" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false"
                                class="form-control file">
                        </div>

                        <div class="form-group">
                            <?= lang("note", "note"); ?>
                            <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control" id="note"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="fprom-group">
                            <?php echo form_submit('add_expense', lang('add_expense'), 'class="btn btn-primary"'); ?>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" charset="UTF-8">
    $.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;
</script>
    <script type="text/javascript" charset="UTF-8">
    $(document).ready(function () {
        $.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;
        $("#date").datetimepicker({
            format: site.dateFormats.js_ldate,
            fontAwesome: true,
            language: 'sma',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0
        }).datetimepicker('update', new Date());
        $('#extype').change(function(){
            var txt = $(this).val();
            changeextype(txt);
        });
        function changeextype(txt){
            console.log(txt);
            if(txt=="inbound"){
                $('.outboundfield').hide();
                $('.inboundfield').show()
                $('.supplierfield').show()
            }
            else if(txt=="outbound"){
                $('.outboundfield').show();
                $('.inboundfield').hide()
                $('.supplierfield').show()
            }
            else{
                $('.outboundfield').hide();
                $('.inboundfield').hide()
                $('.supplierfield').hide()
            }
        }
        $('#paymentmethod').change(function(){
            var txt = $(this).val();
            console.log(txt);
            if(txt=="cash"){
                $('#chequediv').hide();
                $('#cashdiv').show();
                $('#otdiv').hide();
                $('#podiv').hide();
            }
            else if(txt=="cheque"){
                $('#chequediv').show();
                $('#cashdiv').hide();
                $('#otdiv').hide();
                $('#podiv').hide();
            }
            else if(txt=="onlinetransfer"){
                $('#chequediv').hide();
                $('#cashdiv').hide();
                $('#otdiv').show();
                $('#podiv').hide();
            }
            else if(txt=="payorder"){
                $('#chequediv').hide();
                $('#cashdiv').hide();
                $('#otdiv').hide();
                $('#podiv').show();
            }
        });
        $('#purchaseselect').select2({
            ajax: {
                url: '<?php echo base_url("admin/purchases/searching_purchase"); ?>',
                dataType: 'json'
            }
        });
        $('#invoiceselect').select2({
            ajax: {
                url: '<?php echo base_url("admin/purchases/searching_sales"); ?>',
                dataType: 'json'
            }
        });
        $('#suplierselect').select2({
            ajax: {
                url: '<?php echo base_url("admin/purchases/searching_supplier"); ?>',
                dataType: 'json'
            }
        });
        $('#customerselect').select2({
            ajax: {
                url: '<?php echo base_url("admin/purchases/searching_customer"); ?>',
                dataType: 'json'
            }
        });
        changeextype('general')
    });
</script>
