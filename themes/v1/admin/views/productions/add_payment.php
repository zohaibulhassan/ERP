<?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
echo admin_form_open_multipart("productions/add_payment_submit/" . $inv->id, $attrib); ?>
<div class="uk-modal-header">
    <h3 class="uk-modal-title">Add Payment</h3>
</div>
<div class="uk-modal-body">
    <div class="uk-grid">
        <div class="uk-width-large-1-2">
            <div class="md-input-wrapper md-input-filled">
                <label for="uk_dp_1">Date</label>
                <input type="hidden" value="<?php echo $inv->id; ?>" name="production_id"/>
                <input class="md-input  label-fixed" type="text" name="date" id="uk_dp_1" data-uk-datepicker="{format:'YYYY-MM-DD'}" autocomplete="off" readonly value="<?php echo date('Y-m-d'); ?>" >
            </div>
        </div>
        <div class="uk-width-large-1-2">
            <div class="md-input-wrapper md-input-filled">
                <label>Reference No <span class="red" >*</span></label>
                <input type="text" name="reference_no" class="md-input md-input-success label-fixed" required>
            </div>
        </div>
        <div class="uk-width-large-1-2">
            <div class="md-input-wrapper md-input-filled">
                <label>Amount <span class="red" >*</span></label>
                <input type="number" name="amount-paid" class="md-input md-input-success label-fixed" required autocomplete="off" value="<?= $this->sma->formatDecimal($inv->total_cost - $inv->paid) ?>" >
            </div>
        </div>
        <div class="uk-width-large-1-2">
            <div class="md-input-wrapper md-input-filled">
                <label>Payment Method <span class="red" >*</span></label>
                <select name="paid_by" class="uk-width-1-1 select2" id="paid_by">
                    <option value="cash" selected >Cash</option>
                    <option value="onlinetransfer">Online Tansfer</option>
                    <option value="payorder">Payorder</option>
                    <option value="withholdingtax">With Holding Tax</option>
                    <option value="retainer">Retainer</option>
                    <option value="balance">Balance</option>
                    <option value="gift_card">Gift Card</option>
                    <option value="CC">Credit Card</option>
                    <option value="Cheque">Cheque</option>
                    <option value="creaditnote">Creadit Note</option>
                    <option value="other">Other</option>                                    
                </select>
            </div>
        </div>
        <div class="uk-width-large-1-2 ccn_div" >
            <div class="md-input-wrapper md-input-filled">
            <label>CPR Status <span class="red" >*</span></label>
                <select name="pcc_status" class="uk-width-1-1 select2" id="cpr_status">
                    <option value="0">Not Recived</option>
                    <option value="1">Recived</option>
                </select>
            </div>
        </div>
        <div class="uk-width-large-1-2 ccn_div" >
            <div class="md-input-wrapper md-input-filled">
                <label>CPR No <span class="red" >*</span></label>
                <input type="number" name="cprno" class="md-input md-input-success label-fixed" autocomplete="off" min="0" >
            </div>
        </div>
        <div class="uk-width-large-1-2 cheaqueno_div" style="display:none;" >
            <div class="md-input-wrapper md-input-filled">
                <label>Cheque No <span class="red" >*</span></label>
                <input type="text" name="cheque_no" class="md-input md-input-success label-fixed" >
            </div>
        </div>
        <div class="uk-width-large-1-1">
            <div class="md-input-wrapper md-input-filled">
                <label>Note </label>
                <textarea rows="4" class="md-input autosized" style="overflow-x: hidden; overflow-wrap: break-word; height: 121px;" name="note" ></textarea>
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
