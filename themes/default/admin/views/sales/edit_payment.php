<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_payment'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open_multipart("sales/edit_payment/" . $payment->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
                <?php //if ($Owner || $Admin) { ?>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?= lang("date", "date"); ?>
                            <?= form_input('date', (isset($_POST['date']) ? $_POST['date'] : $this->sma->hrld($payment->date)), 'class="form-control date" id="date" required="required"'); ?>
                        </div>
                    </div>
                <?php //} ?>
                <div class="col-sm-6">
                    <div class="form-group">
                        <?= lang("reference_no", "reference_no"); ?>
                        <?= form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $payment->reference_no), 'class="form-control tip" id="reference_no" required="required"'); ?>
                    </div>
                </div>

                <input type="hidden" value="<?php echo $payment->sale_id; ?>" name="sale_id"/>
            </div>
            <div class="clearfix"></div>
            <div id="payments">
                <?php
                    $showamount = $payment->amount;

                ?>

                <div class="well well-sm well_1">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="payment">
                                    <div class="form-group">
                                        <?= lang("amount", "amount_1"); ?>
                                        <input name="amount-paid"
                                               value="<?= $this->sma->formatDecimal($showamount); ?>" type="text"
                                               id="amount_1" class="pa form-control kb-pad amount"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <?= lang("paying_by", "paid_by_1"); ?>
                                    <select name="paid_by" id="paid_by_1" class="form-control paid_by">
                                        <?= $this->sma->paid_opts($payment->paid_by); ?>
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
                                    <input name="cprno" value="<?= $payment->cpr_no; ?>" type="text" id="cprno" class="pa form-control kb-pad"/>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="pcc_1" style="display:none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input name="pcc_no" value="<?= $payment->cc_no; ?>" type="text" id="pcc_no_1"
                                               class="form-control" placeholder="<?= lang('cc_no') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <input name="pcc_holder" value="<?= $payment->cc_holder; ?>" type="text"
                                               id="pcc_holder_1" class="form-control"
                                               placeholder="<?= lang('cc_holder') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select name="pcc_type" id="pcc_type_1" class="form-control pcc_type"
                                                placeholder="<?= lang('card_type') ?>">
                                            <option
                                                value="Visa"<?= $payment->cc_type == 'Visa' ? ' checked="checcked"' : '' ?>><?= lang("Visa"); ?></option>
                                            <option
                                                value="MasterCard"<?= $payment->cc_type == 'MasterCard' ? ' checked="checcked"' : '' ?>><?= lang("MasterCard"); ?></option>
                                            <option
                                                value="Amex"<?= $payment->cc_type == 'Amex' ? ' checked="checcked"' : '' ?>><?= lang("Amex"); ?></option>
                                            <option
                                                value="Discover"<?= $payment->cc_type == 'Discover' ? ' checked="checcked"' : '' ?>><?= lang("Discover"); ?></option>
                                        </select>
                                        <!-- <input type="text" id="pcc_type_1" class="form-control" placeholder="<?= lang('card_type') ?>" />-->
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input name="pcc_month" value="<?= $payment->cc_month; ?>" type="text"
                                               id="pcc_month_1" class="form-control"
                                               placeholder="<?= lang('month') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <input name="pcc_year" value="<?= $payment->cc_year; ?>" type="text"
                                               id="pcc_year_1" class="form-control" placeholder="<?= lang('year') ?>"/>
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
                                <input name="cheque_no" value="<?= $payment->cheque_no; ?>" type="text" id="cheque_no_1"
                                       class="form-control cheque_no"/>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>

            </div>

            <div class="form-group">
                <?= lang("attachment", "attachment") ?>
                <input id="attachment" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false"
                       class="form-control file">
            </div>

            <div class="form-group">
                <?= lang("note", "note"); ?>
                <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : $payment->note), 'class="form-control" id="note"'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_payment', lang('edit_payment'), 'class="btn btn-primary"'); ?>
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
        $.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;
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
            $('#cprno').val('');
            $('.pwhtcpr_1').show();

        });
        $(document).on('change', '.paid_by', function () {
            var p_cpr_status = $('#pcc_status_1').val();
            var p_val = $(this).val();
            console.log(p_val);
            $('#rpaidby').val(p_val);
            if (p_val == 'cash') {
                $('.pcheque_1').hide();
                $('.pcc_1').hide();
                $('.pcash_1').show();
                $('.pwhtcpr_1').hide();
                $('.pwhtcprno_1').hide();
                $('#amount_1').focus();
                $('#cprno').val('');
            } else if (p_val == 'CC') {
                $('.pcheque_1').hide();
                $('.pcash_1').hide();
                $('.pcc_1').show();
                $('.pwhtcpr_1').hide();
                $('.pwhtcprno_1').hide();
                $('#pcc_no_1').focus();
                $('#cprno').val('');
            }
            else if (p_val == 'Cheque') {
                $('.pcc_1').hide();
                $('.pcash_1').hide();
                $('.pcheque_1').show();
                $('.pwhtcpr_1').hide();
                $('.pwhtcprno_1').hide();
                $('#cheque_no_1').focus();
                $('#cprno').val('');
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
            } 
            else {
                $('.pcheque_1').hide();
                $('.pcc_1').hide();
                $('.pcash_1').hide();
                $('.pwhtcpr_1').hide();
                $('.pwhtcprno_1').hide();
                $('#cprno').val('');
            }
            if (p_val == 'gift_card') {
                $('.gc').show();
                $('#gift_card_no').focus();
            } else {
                $('.gc').hide();
            }
        });
        var p_cpr_status = '<?=$payment->status?>';
        var p_val = '<?=$payment->paid_by?>';
        localStorage.setItem('paid_by', p_val);
        if (p_val == 'cash') {
            $('.pcheque_1').hide();
            $('.pcc_1').hide();
            $('.pcash_1').show();
            $('#amount_1').focus();
            $('.pwhtcpr_1').hide();
            $('.pwhtcprno_1').hide();
        } else if (p_val == 'CC') {
            $('.pcheque_1').hide();
            $('.pcash_1').hide();
            $('.pcc_1').show();
            $('#pcc_no_1').focus();
            $('.pwhtcpr_1').hide();
            $('.pwhtcprno_1').hide();
        } else if (p_val == 'Cheque') {
            $('.pcc_1').hide();
            $('.pcash_1').hide();
            $('.pcheque_1').show();
            $('#cheque_no_1').focus();
            $('.pwhtcpr_1').hide();
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
            } 
        else {
            $('.pcheque_1').hide();
            $('.pcc_1').hide();
            $('.pcash_1').hide();
            $('.pwhtcpr_1').hide();
            $('.pwhtcprno_1').hide();
        }
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
        $('#paid_by_1').select2("val", '<?=$payment->paid_by?>');
        $('#pcc_status_1').select2("val", '<?=$payment->status?>');
    });
</script>
