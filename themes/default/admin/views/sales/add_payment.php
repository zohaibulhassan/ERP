<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_payment'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open_multipart("sales/add_payment/" . $inv->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
                <?php //if ($Owner || $Admin) { ?>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?= lang("date", "date"); ?>
                            <?= form_input('date', (isset($_POST['date']) ? $_POST['date'] : ""), 'class="form-control date" id="date" required="required"'); ?>
                        </div>
                    </div>
                <?php //} ?>
                <div class="col-sm-6">
                    <div class="form-group">
                        <?= lang("reference_no", "reference_no"); ?>
                        <?= form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $payment_ref), 'class="form-control tip" id="reference_no"'); ?>
                    </div>
                </div>

                <input type="hidden" value="<?php echo $inv->id; ?>" name="sale_id"/>
            </div>
            <div class="clearfix"></div>
            <div id="payments">

                <div class="well well-sm well_1">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="payment">
                                    <div class="form-group">
                                        <?= lang("amount", "amount_1"); ?>
                                        <input name="amount-paid" type="text" id="amount_1"
                                               value="<?= $this->sma->formatDecimal($inv->grand_total - $inv->paid); ?>"
                                               class="pa form-control kb-pad amount" required="required"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <?= lang("paying_by", "paid_by_1"); ?>
                                    <select name="paid_by" id="paid_by_1" class="form-control paid_by" required="required">
                                        <?= $this->sma->paid_opts(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 pwhtcpr_1"  style="display:none;">
                                <div class="form-group">
                                    <?= lang("CPR Status", "pcc_status_1"); ?>
                                    <select name="pcc_status" id="pcc_status_1" class="form-control pcc_status" placeholder="Status">
                                        <option value="0">Not Recived</option>
                                        <option value="1">Recived</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 pwhtcprno_1"  style="display:none;">
                                <div class="form-group">
                                    <?= lang("CPR No", "pcc_cprno_1"); ?>
                                    <input name="cprno" type="text" id="cprno" class="pa form-control kb-pad"/>
                                </div>
                            </div>
                            <div class="col-md-6 pcn_pre"  style="display:none;">
                                <div class="form-group">
                                    <?= lang("Creadit Note Percentage", "pcnpre_1"); ?>
                                    <input name="pcnpre" type="text" id="pcnpretxt" class="pa form-control kb-pad"/>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group gc" style="display: none;">
                            <?= lang("gift_card_no", "gift_card_no"); ?>
                            <input name="gift_card_no" type="text" id="gift_card_no" class="pa form-control kb-pad"/>

                            <div id="gc_details"></div>
                        </div>
                        <div class="pcc_1" style="display:none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input name="pcc_no" type="text" id="pcc_no_1" class="form-control"
                                               placeholder="<?= lang('cc_no') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <input name="pcc_holder" type="text" id="pcc_holder_1" class="form-control"
                                               placeholder="<?= lang('cc_holder') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select name="pcc_type" id="pcc_type_1" class="form-control pcc_type"
                                                placeholder="<?= lang('card_type') ?>">
                                            <option value="Visa"><?= lang("Visa"); ?></option>
                                            <option value="MasterCard"><?= lang("MasterCard"); ?></option>
                                            <option value="Amex"><?= lang("Amex"); ?></option>
                                            <option value="Discover"><?= lang("Discover"); ?></option>
                                        </select>
                                        <!-- <input type="text" id="pcc_type_1" class="form-control" placeholder="<?= lang('card_type') ?>" />-->
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input name="pcc_month" type="text" id="pcc_month_1" class="form-control"
                                               placeholder="<?= lang('month') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <input name="pcc_year" type="text" id="pcc_year_1" class="form-control"
                                               placeholder="<?= lang('year') ?>"/>
                                    </div>
                                </div>
                                <!--<div class="col-md-3">
                                    <div class="form-group">
                                        <input name="pcc_ccv" type="text" id="pcc_cvv2_1" class="form-control" placeholder="<?= lang('cvv2') ?>" />
                                    </div>
                                </div>-->
                            </div>
                        </div>
                        <div class="pcheque_1" style="display:none;">
                            <div class="form-group"><?= lang("cheque_no", "cheque_no_1"); ?>
                                <input name="cheque_no" type="text" id="cheque_no_1" class="form-control cheque_no"/>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>

            </div>

            <div class="form-group">
                <?= lang("attachment", "attachment") ?>
                <input id="attachment" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
            </div>

            <div class="form-group">
                <?= lang("note", "note"); ?>
                <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control" id="note"'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_payment', lang('add_payment'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<script type="text/javascript" charset="UTF-8">
    $.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;
</script>
<?= $modal_js ?>
<script type="text/javascript" charset="UTF-8">
    $(document).ready(function () {
        $(document).on('change', '#gift_card_no', function () {
            var cn = $(this).val() ? $(this).val() : '';
            if (cn != '') {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + "sales/validate_gift_card/" + cn,
                    dataType: "json",
                    success: function (data) {
                        if (data === false) {
                            $('#gift_card_no').parent('.form-group').addClass('has-error');
                            bootbox.alert('<?=lang('incorrect_gift_card')?>');
                        } else if (data.customer_id !== null && data.customer_id != <?=$inv->customer_id?>) {
                            $('#gift_card_no').parent('.form-group').addClass('has-error');
                            bootbox.alert('<?=lang('gift_card_not_for_customer')?>');

                        } else {
                            var due = <?=$inv->grand_total-$inv->paid?>;
                            if (due > data.balance) {
                                $('#amount_1').val(formatDecimal(data.balance));
                            }
                            $('#gc_details').html('<small>Card No: <span style="max-width:60%;float:right;">' + data.card_no + '</span><br>Value: <span style="max-width:60%;float:right;">' + currencyFormat(data.value) + '</span><br>Balance: <span style="max-width:60%;float:right;">' + currencyFormat(data.balance) + '</span></small>');
                            $('#gift_card_no').parent('.form-group').removeClass('has-error');
                        }
                    }
                });
            }
        });
        $('#pcc_status_1').change(function(){
            var p_cpr_status = $(this).val();
            console.log('A');
            console.log(p_cpr_status);
            if(p_cpr_status == 1){
                $('.pwhtcprno_1').show();
            }
            else{
                $('.pwhtcprno_1').hide();
            }
            $('.pwhtcpr_1').show();

        });
        $(document).on('change', '.paid_by', function () {
            var p_cpr_status = $('#pcc_status_1').val();
            console.log(p_cpr_status);
            var p_val = $(this).val();
            $('#rpaidby').val(p_val);
            console.log(p_val);
            if (p_val == 'cash') {
                $('.pcheque_1').hide();
                $('.pcc_1').hide();
                $('.pcash_1').show();
                $('.pwhtcpr_1').hide();
                $('.pwhtcprno_1').hide();
                $('#cprno').val('');
                $('#amount_1').focus();
                $('#pcnpretxt').val('');
                $('.pcn_pre').hide();
            } else if (p_val == 'CC') {
                $('.pcheque_1').hide();
                $('.pcash_1').hide();
                $('.pcc_1').show();
                $('.pwhtcpr_1').hide();
                $('.pwhtcprno_1').hide();
                $('#cprno').val('');
                $('#pcc_no_1').focus();
                $('#pcnpretxt').val('');
                $('.pcn_pre').hide();
            }
            else if (p_val == 'Cheque') {
                $('.pcc_1').hide();
                $('.pcash_1').hide();
                $('.pcheque_1').show();
                $('.pwhtcpr_1').hide();
                $('.pwhtcprno_1').hide();
                $('#cprno').val('');
                $('#cheque_no_1').focus();
                $('#pcnpretxt').val('');
                $('.pcn_pre').hide();
            } 
            else if (p_val == 'withholdingtax') {
                $('.pcc_1').hide();
                $('.pcash_1').hide();
                $('.pcheque_1').hide();
                $('.pwhtcpr_1').show();
                if(p_cpr_status == 1){
                    $('.pwhtcprno_1').show();
                }
                else{
                    $('.pwhtcprno_1').hide();
                }
                $('#cprno').val('');
                $('#pcnpretxt').val('');
                $('.pcn_pre').hide();
                
            } 
            else if (p_val == 'creaditnote') {
                $('.pcc_1').hide();
                $('.pcash_1').hide();
                $('.pcheque_1').hide();
                $('.pwhtcpr_1').hide();
                $('.pcn_pre').show();
                $('.pwhtcprno_1').hide();
                $('#cprno').val('');
                $('#pcnpretxt').val('');
                
            } 
            else {
                $('.pcheque_1').hide();
                $('.pcc_1').hide();
                $('.pcash_1').hide();
                $('.pwhtcpr_1').hide();
                $('#cprno').val('');
                $('.pwhtcprno_1').hide();
                $('#pcnpretxt').val('');
                $('.pcn_pre').hide();
            }
            if (p_val == 'gift_card') {
                $('.gc').show();
                $('#gift_card_no').focus();
            } else {
                $('.gc').hide();
            }
        });
        $('#pcc_no_1').change(function (e) {
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
    });
</script>
