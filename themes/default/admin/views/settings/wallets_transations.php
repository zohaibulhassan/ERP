<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue">
            <i class="fa-fw fa fa-barcode"></i>
            <?= lang('Wallet Transactions'); ?>
        </h2>

        <div class="box-icon">
            <ul class="btn-tasks">
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="STData" class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                            <tr class="primary">
                                <th>Transaction ID</th>
                                <th>Transaction Type</th>
                                <th>Transaction Date</th>
                                <th>Transaction Amount</th>
                                <th>Transaction By</th>
                                <th style="max-width:65px; text-align:center;"><?= lang("actions") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach($transactions as $t){
                                    ?>
                                    <tr>
                                        <td><?php echo $t->id ?></td>
                                        <td><?php echo $t->type == 0 ? 'Deposit' : 'Credit'; ?></td>
                                        <td><?php echo $t->created_at ?></td>
                                        <td><?php echo $t->amount ?></td>
                                        <td><?php echo $t->fname.' '.$t->lname ?></td>
                                        <td>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
