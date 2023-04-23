<?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
echo admin_form_open_multipart("purchases/edit_payment_submit/" . $payment->id, $attrib); ?>
<div class="uk-modal-header">
    <h3 class="uk-modal-title">Edit Payment</h3>
</div>
<div class="uk-modal-body">
    <div class="uk-grid">
        <div class="uk-width-large-1-2">
            <div class="md-input-wrapper md-input-filled">
                <label for="uk_dp_1">Date</label>
                <input type="hidden" value="<?php echo $payment->id; ?>" name="id"/>
                <input type="hidden" value="<?php echo $payment->purchase_id; ?>" name="purchase_id"/>
                <input class="md-input  label-fixed" type="text" name="date" id="uk_dp_1" data-uk-datepicker="{format:'YYYY-MM-DD'}" autocomplete="off" readonly value="<?php echo dateformate($payment->date); ?>" >
            </div>
        </div>
        <div class="uk-width-large-1-2">
            <div class="md-input-wrapper md-input-filled">
                <label>Reference No <span class="red" >*</span></label>
                <input type="text" name="reference_no" class="md-input md-input-success label-fixed" required value="<?php echo $payment->reference_no; ?>">
            </div>
        </div>
        <div class="uk-width-large-1-2">
            <div class="md-input-wrapper md-input-filled">
                <label>Amount <span class="red" >*</span></label>
                <input type="number" name="amount-paid" class="md-input md-input-success label-fixed" required autocomplete="off" value="<?= $this->sma->formatDecimal($payment->amount) ?>" >
            </div>
        </div>
        <div class="uk-width-large-1-2">
            <div class="md-input-wrapper md-input-filled">
                <label>Payment Method <span class="red" >*</span></label>
                <select name="paid_by" class="uk-width-1-1 select2" id="paid_by">
                    <option value="cash" <?php if($payment->paid_by == "cash"){ echo 'selected'; } ?> >Cash</option>
                    <option value="onlinetransfer" <?php if($payment->paid_by == "onlinetransfer"){ echo 'selected'; } ?> >Online Tansfer</option>
                    <option value="payorder" <?php if($payment->paid_by == "payorder"){ echo 'selected'; } ?> >Payorder</option>
                    <option value="withholdingtax" <?php if($payment->paid_by == "withholdingtax"){ echo 'selected'; } ?> >With Holding Tax</option>
                    <option value="retainer" <?php if($payment->paid_by == "retainer"){ echo 'selected'; } ?> >Retainer</option>
                    <option value="balance" <?php if($payment->paid_by == "balance"){ echo 'selected'; } ?> >Balance</option>
                    <option value="gift_card" <?php if($payment->paid_by == "gift_card"){ echo 'selected'; } ?> >Gift Card</option>
                    <option value="CC" <?php if($payment->paid_by == "CC"){ echo 'selected'; } ?> >Credit Card</option>
                    <option value="Cheque" <?php if($payment->paid_by == "Cheque"){ echo 'selected'; } ?> >Cheque</option>
                    <option value="creaditnote" <?php if($payment->paid_by == "creaditnote"){ echo 'selected'; } ?> >Creadit Note</option>
                    <option value="other" <?php if($payment->paid_by == "other"){ echo 'selected'; } ?> >Other</option>                                    
                </select>
            </div>
        </div>
        <div class="uk-width-large-1-2 ccn_div" >
            <div class="md-input-wrapper md-input-filled">
            <label>CPR Status <span class="red" >*</span></label>
                <select name="pcc_status" class="uk-width-1-1 select2" id="cpr_status">
                    <option value="0" <?php if($payment->cpr_no == ""){ echo 'selected'; } ?> >Not Recived</option>
                    <option value="1" <?php if($payment->cpr_no != ""){ echo 'selected'; } ?> >Recived</option>
                </select>
            </div>
        </div>
        <div class="uk-width-large-1-2 ccn_div" >
            <div class="md-input-wrapper md-input-filled">
                <label>CPR No <span class="red" >*</span></label>
                <input type="number" name="cprno" class="md-input md-input-success label-fixed" autocomplete="off" min="0" value="<?php echo $payment->cpr_no; ?>" >
            </div>
        </div>
        <div class="uk-width-large-1-2 cheaqueno_div" style="display:none;" >
            <div class="md-input-wrapper md-input-filled">
                <label>Cheque No <span class="red" >*</span></label>
                <input type="text" name="cheque_no" class="md-input md-input-success label-fixed" value="<?php echo $payment->cheque_no; ?>" >
            </div>
        </div>
        <div class="uk-width-large-1-1">
            <div class="md-input-wrapper md-input-filled">
                <label>Note </label>
                <textarea rows="4" class="md-input autosized" style="overflow-x: hidden; overflow-wrap: break-word; height: 121px;" name="note" ><?php echo $payment->note; ?></textarea>
            </div>
        </div>
    </div>
</div>
<div class="uk-modal-footer uk-text-right">
    <button type="submit" class="md-btn md-btn-success md-btn-flat" >Submit</button>
    <button type="button" class="md-btn md-btn-flat uk-modal-close" >Close</button>
</div>
<?php echo form_close(); ?>
    <script>
        $(document).ready(function(){
            $('.select2').select2();
            $('#paid_by').change(function(){
                changepay();
            });
            function changepay(){
                var paidby = $('#paid_by').val();
                $('.ccn_div').hide();
                $('.cheaqueno_div').hide();
                if(paidby == "Cheque"){
                    $('.cheaqueno_div').show();
                }
                if(paidby == "creaditnote"){
                    $('.ccn_div').show();
                }
            }
            changepay();
        });
    
    </script>
