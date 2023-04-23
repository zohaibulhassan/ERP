<style>
    .uk-open>.uk-dropdown, .uk-open>.uk-dropdown-blank{

    }
</style>
<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Register Detail (<?php echo $register->date.' TO '.$register->closed_at; ?>)</h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                </div>
            </div>
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin="">
                    <div class="uk-width-medium-2-10 uk-row-first">
                        <span class="uk-display-block uk-margin-small-top uk-text-large"><b>Register Detail</b></span>
                    </div>
                    <div class="uk-width-medium-8-10">
                        <table class="uk-table">
                            <tbody>
                                <tr>
                                    <td><b>Start Date</b></td>
                                    <td class="uk-text-right"><?php echo $register->date; ?></td>
                                    <td><b>Close Date</b></td>
                                    <td class="uk-text-right"><?php echo $register->closed_at; ?></td>
                                </tr>
                                <tr>
                                    <td><b>Cash Hand</b></td>
                                    <td class="uk-text-right"><?php echo $register->cash_in_hand; ?></td>
                                    <td><b>Received Payment</b></td>
                                    <td class="uk-text-right"><?php echo $register->total_cash_submitted; ?></td>
                                </tr>
                                <tr>
                                    <td><b>Cardit Card Payment</b></td>
                                    <td class="uk-text-right"><?php echo $register->total_cc_slips_submitted; ?></td>
                                    <td><b>Cheque Payment</b></td>
                                    <td class="uk-text-right"><?php echo $register->total_cheques_submitted; ?></td>
                                </tr>
                                <tr>
                                    <td><b>Total Sales</b></td>
                                    <td class="uk-text-right"><?php echo $register->total_cash; ?></td>
                                    <td><b>Status</b></td>
                                    <td class="uk-text-right"><?php echo $register->status; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="uk-grid" data-uk-grid-margin="">
                    <div class="uk-width-medium-2-10 uk-row-first">
                        <span class="uk-display-block uk-margin-small-top uk-text-large"><b>Sale Detail</b></span>
                    </div>
                    <div class="uk-width-medium-8-10">
                        <table class="uk-table db_table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice No</th>
                                    <th>Customer</th>
                                    <th>Grand Total</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $totalsale = 0;
                                    $totalpaidsale = 0;
                                    $totalbalancesale = 0;
                                    foreach($sales as $sale){
                                        ?>
                                        <tr>
                                            <td><?php echo $sale->date ?></td>
                                            <td><a href="<?php echo base_url('admin/sales/detail/'.$sale->id); ?>" target="_blank" ><?php echo $sale->reference_no ?></a></td>
                                            <td><?php echo $sale->customer ?></td>
                                            <td><?php echo decimalallow($sale->grand_total) ?></td>
                                            <td><?php echo decimalallow($sale->paid) ?></td>
                                            <td><?php echo decimalallow($sale->grand_total-$sale->paid) ?></td>
                                        </tr>
                                        <?php
                                        $totalsale += decimalallow($sale->grand_total);
                                        $totalpaidsale += decimalallow($sale->paid);
                                        $totalbalancesale += decimalallow($sale->grand_total-$sale->paid);
                                    }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" >Total</th>
                                    <th><?php echo $totalsale; ?></th>
                                    <th><?php echo $totalpaidsale; ?></th>
                                    <th><?php echo $totalbalancesale; ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="uk-grid" data-uk-grid-margin="">
                    <div class="uk-width-medium-2-10 uk-row-first">
                        <span class="uk-display-block uk-margin-small-top uk-text-large"><b>Payment Detail</b></span>
                    </div>
                    <div class="uk-width-medium-8-10">
                        <table class="uk-table db_table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Payment Ref</th>
                                    <th>Invoice No</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                    $paymentmethods = array();

                                    $totalspayment = 0;
                                    foreach($payments as $payment){
                                        ?>
                                        <tr>
                                            <td><?php echo $payment->date ?></td>
                                            <td><?php echo $payment->reference_no ?></td>
                                            <td><a href="<?php echo base_url('admin/sales/detail/'.$payment->invoice_id); ?>" target="_blank" ><?php echo $payment->invoice_no ?></a></td>
                                            <td><?php echo decimalallow($payment->amount) ?></td>
                                            <td><?php echo $payment->paid_by ?></td>
                                            <td><?php echo $payment->note ?></td>
                                        </tr>
                                        <?php
                                        if(!isset($paymentmethods[$payment->paid_by])){ $paymentmethods[$payment->paid_by] = 0; }
                                        $paymentmethods[$payment->paid_by] = $paymentmethods[$payment->paid_by]+$payment->amount;
                                        $totalspayment += $payment->amount;
                                    }
                                ?>
                            </tbody>
                            <tfoot>
                                <?php
                                    foreach($paymentmethods as $key => $pm){
                                    ?>
                                    <tr>
                                        <th colspan="3" ><?php echo $key; ?></th>
                                        <td><?php echo $pm; ?></td>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    <?php
                                    }
                                ?>
                                <tr>
                                    <th colspan="3" >Total</th>
                                    <th><?php echo $totalspayment; ?></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="uk-grid" data-uk-grid-margin="">
                    <div class="uk-width-medium-2-10 uk-row-first">
                        <span class="uk-display-block uk-margin-small-top uk-text-large"><b>Sale Return Detail</b></span>
                    </div>
                    <div class="uk-width-medium-8-10">
                        <table class="uk-table db_table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice No</th>
                                    <th>Customer</th>
                                    <th>Grand Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $totalreturnsale = 0;
                                    foreach($returns as $return){
                                        ?>
                                        <tr>
                                            <td><?php echo $return->date ?></td>
                                            <td><a href="<?php echo base_url('admin/sales/detail/'.$return->id); ?>" target="_blank" ><?php echo $return->reference_no ?></a></td>
                                            <td><?php echo $return->customer ?></td>
                                            <td><?php echo decimalallow($return->grand_total) ?></td>
                                        </tr>
                                        <?php
                                        $totalreturnsale += decimalallow($return->grand_total);
                                    }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" >Total</th>
                                    <th><?php echo $totalreturnsale; ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                
            </div>
        </div>
    </div>
</div>
<!-- datatables -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<!-- datatables buttons-->
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/dataTables.buttons.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/custom/datatables/buttons.uikit.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/jszip/dist/jszip.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/pdfmake/build/pdfmake.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/pdfmake/build/vfs_fonts.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.colVis.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.html5.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.print.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-fixedcolumns/dataTables.fixedColumns.min.js"></script>
<!-- datatables custom integration -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/custom/datatables/datatables.uikit.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/datatable.js"></script>
<script>
    // $('.db_table').DataTable();
</script>