<style>
    .registertable{}
    .registertable tr{}
    .registertable tr th{}
    .registertable tr td{}
    .registertable tr td h4{
        margin: 0;
    }
</style>
<?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
echo admin_form_open_multipart('pos/close_register_submit/' . $user_id, $attrib);
?>

<div class="uk-modal-header">
    <h3 class="uk-modal-title"><?= lang('sales') . ' (' . $this->sma->hrld($register_open_time) . ' - ' . $this->sma->hrld(date('Y-m-d H:i:s')) . ')'; ?></h3>
</div>
<div class="uk-modal-body">
    <table width="100%" class="uk-table registertable">
        <tr>
            <td style="border-bottom: 1px solid #EEE;"><h4>Cash in hand:</h4></td>
            <td style="text-align:right; border-bottom: 1px solid #EEE;"><h4>
                    <span><?= $this->sma->formatMoney($cash_in_hand); ?></span></h4>
            </td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #EEE;"><h4>Cash Sale:</h4></td>
            <td style="text-align:right; border-bottom: 1px solid #EEE;"><h4>
                    <span><?= $this->sma->formatMoney($cashsales->paid ? $cashsales->paid : '0.00') . ' (' . $this->sma->formatMoney($cashsales->total ? $cashsales->total : '0.00') . ')'; ?></span>
                </h4></td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #EEE;"><h4>Cheque Sales:</h4></td>
            <td style="text-align:right;border-bottom: 1px solid #EEE;"><h4>
                    <span><?= $this->sma->formatMoney($chsales->paid ? $chsales->paid : '0.00') . ' (' . $this->sma->formatMoney($chsales->total ? $chsales->total : '0.00') . ')'; ?></span>
                </h4></td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #EEE;"><h4>Credit Card Sales:</h4></td>
            <td style="text-align:right;border-bottom: 1px solid #EEE;"><h4>
                    <span><?= $this->sma->formatMoney($ccsales->paid ? $ccsales->paid : '0.00') . ' (' . $this->sma->formatMoney($ccsales->total ? $ccsales->total : '0.00') . ')'; ?></span>
                </h4></td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #DDD;"><h4>Gift Card Sales:</h4></td>
            <td style="text-align:right;border-bottom: 1px solid #DDD;"><h4>
                    <span><?= $this->sma->formatMoney($gcsales->paid ? $gcsales->paid : '0.00') . ' (' . $this->sma->formatMoney($gcsales->total ? $gcsales->total : '0.00') . ')'; ?></span>
                </h4></td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #EEE;"><h4>Others:</h4></td>
            <td style="text-align:right;border-bottom: 1px solid #EEE;"><h4>
                    <span><?= $this->sma->formatMoney($othersales->paid ? $othersales->paid : '0.00') . ' (' . $this->sma->formatMoney($othersales->total ? $othersales->total : '0.00') . ')'; ?></span>
                </h4></td>
        </tr>
        <tr>
            <td width="300px;" style="font-weight:bold;"><h4>Total Sales:</h4></td>
            <td width="200px;" style="font-weight:bold;text-align:right;"><h4>
                    <span><?= $this->sma->formatMoney($totalsales->paid ? $totalsales->paid : '0.00') . ' (' . $this->sma->formatMoney($totalsales->total ? $totalsales->total : '0.00') . ')'; ?></span>
                </h4></td>
        </tr>
        <tr>
            <td style="border-top: 1px solid #DDD;"><h4>Refunds:</h4></td>
            <td style="text-align:right;border-top: 1px solid #DDD;"><h4>
                    <span><?= $this->sma->formatMoney($refunds->returned ? $refunds->returned : '0.00') . ' (' . $this->sma->formatMoney($refunds->total ? $refunds->total : '0.00') . ')'; ?></span>
                </h4></td>
        </tr>
        <tr>
            <td style="border-top: 1px solid #DDD;"><h4><?= lang('returns'); ?>:</h4></td>
            <td style="text-align:right;border-top: 1px solid #DDD;"><h4>
                    <span><?= $this->sma->formatMoney($returns ? '-' . $returns : '0.00'); ?></span>
                </h4></td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #DDD;"><h4><?= lang('expenses'); ?>:</h4></td>
            <td style="text-align:right;border-bottom: 1px solid #DDD;"><h4>
                    <span><?php $expense = $expenses ? $expenses->total : 0; echo $this->sma->formatMoney($expense) . ' (' . $this->sma->formatMoney($expense) . ')'; ?></span>
                </h4></td>
        </tr>
        <tr>
            <td width="300px;" style="font-weight:bold;"><h4><strong>Total Cash</strong>:</h4>
            </td>
            <td style="text-align:right;"><h4>
            <?php $total_cash_amount = $cashsales->paid ? (($cashsales->paid + ($cash_in_hand)) + ($refunds->returned ? $refunds->returned : 0) - ($returns ? $returns : 0) - $expense) : ($cash_in_hand - $expense - ($returns ? $returns : 0)); ?>
                            <span><strong><?= $this->sma->formatMoney($total_cash_amount); ?></strong></span>
                </h4></td>
        </tr>
    </table>
    <?php
    if ($suspended_bills) {
        echo '<hr><h3>Open Bill</h3><table class="uk-table"><thead><tr><th>' . lang('customer') . '</th><th>' . lang('date') . '</th><th>' . lang('total_items') . '</th><th>' . lang('amount') . '</th></tr></thead><tbody>';
        foreach ($suspended_bills as $bill) {
            echo '<tr><td>' . $bill->customer . '</td><td>' . $this->sma->hrld($bill->date) . '</td><td class="text-center">' . $bill->count . '</td><td class="text-right">' . $bill->total . '</td></tr>';
        }
        echo '</tbody></table>';
    }
    ?>
    <div class="uk-grid">
        <div class="uk-width-large-1-2">
            <div class="md-input-wrapper md-input-filled">
                <label>Total Cash</label>
                <?= form_hidden('total_cash', $total_cash_amount); ?>
                <?= form_input('total_cash_submitted', ($_POST['total_cash_submitted'] ?? $total_cash_amount ?? 0 ), 'class="md-input md-input-success label-fixed" id="total_cash_submitted" required="required"'); ?>

            </div>
        </div>
        <div class="uk-width-large-1-2">
            <div class="md-input-wrapper md-input-filled">
                <label>Total Cheques</label>
                <?= form_hidden('total_cheques', $chsales->total_cheques ?? 0); ?>
                <?= form_input('total_cheques_submitted', ($_POST['total_cheques_submitted'] ?? $chsales->total_cheques ?? 0), 'class="md-input md-input-success label-fixed" required="required"'); ?>
            </div>
        </div>
        <div class="uk-width-large-1-2">
            <div class="md-input-wrapper md-input-filled">
                <label>Total Credit Card Slips</label>
                <?= form_hidden('total_cc_slips', $ccsales->total_cc_slips); ?>
                <?= form_input('total_cc_slips_submitted', ($_POST['total_cc_slips_submitted'] ?? $ccsales->total_cc_slips ?? 0), 'class="md-input md-input-success label-fixed" id="total_cc_slips_submitted" required="required"'); ?>
            </div>
        </div>
        <div class="uk-width-large-1-2">
            <div class="md-input-wrapper md-input-filled">
                <label>Total Available Cash</label>
                <?= form_input('total_available_cash_submitted', 0, 'class="md-input md-input-success label-fixed" id="total_available_cash_submitted" required="required"'); ?>
            </div>
        </div>
        <div class="uk-width-large-1-1">
            <div class="md-input-wrapper md-input-filled">
                <label>Note</label>
                <?= form_textarea('note', ($_POST['note'] ?? ''), 'class="md-input md-input-success label-fixed" id="note" style="margin-top: 10px; height: 100px;"'); ?> 

            </div>
        </div>
    </div>

    
    

</div>
<div class="uk-modal-footer uk-text-right">
    <button type="button" class="md-btn md-btn-danger md-btn-flat uk-modal-close" >Close</button>
    <button type="submit" class="md-btn md-btn-primary md-btn-flat" >Register Close</button>
</div>
<?= form_close(); ?>
