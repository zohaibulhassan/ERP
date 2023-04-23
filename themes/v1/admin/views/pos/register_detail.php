<style>
    .registertable{}
    .registertable tr{}
    .registertable tr th{}
    .registertable tr td{}
    .registertable tr td h4{
        margin: 0;
    }
</style>
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
                    <span><strong><?= $cashsales->paid ? $this->sma->formatMoney(($cashsales->paid + ($cash_in_hand)) + ($refunds->returned ? $refunds->returned : 0) - ($returns ? $returns : 0) - $expense) : $this->sma->formatMoney($cash_in_hand - $expense - ($returns ? $returns : 0)); ?></strong></span>
                </h4></td>
        </tr>
    </table>

</div>
<div class="uk-modal-footer uk-text-right">
    <button type="button" class="md-btn md-btn-danger md-btn-flat uk-modal-close" >Close</button>
</div>
