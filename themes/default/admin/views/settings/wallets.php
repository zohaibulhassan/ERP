<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue">
            <i class="fa-fw fa fa-barcode"></i>
            <?= lang('Wallets'); ?>
        </h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <?php
                    if($Owner || $Admin || $GP['wallet_add']){
                    ?>
                    <li class="dropdown">
                        <a href="<?= admin_url('system_settings/add_wallet') ?>" class="tip" data-placement="top" title="<?= lang("Add New Wallet") ?>">
                            <i class="icon fa fa-plus tip"></i>
                        </a>
                    </li>
                    <?php
                    }
                ?>

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
                                <th>ID</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Location</th>
                                <th>Create Date</th>
                                <th>Status</th>
                                <th style="max-width:65px; text-align:center;"><?= lang("actions") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach($wallets as $w){
                                    ?>
                                    <tr>
                                        <td><?php echo $w->id ?></td>
                                        <td><?php echo $w->title ?></td>
                                        <td><?php echo $w->amount ?></td>
                                        <td><?php echo $w->wname ?></td>
                                        <td><?php echo $w->created_at ?></td>
                                        <td><?php echo $w->status ?></td>
                                        <td>
                                            <?php
                                                if($Owner || $Admin || $GP['wallet_edit']){
                                                    if($w->status == "active"){
                                                    ?>
                                                        <a href="<?php echo base_url('admin/system_settings/wallet_deactive?wid='.$w->id); ?>" title="Deactive" style="margin-left: 5px;" ><i class="fa fa-ban"></i></a>
                                                    <?php
                                                    }
                                                    else{
                                                    ?>
                                                        <a href="<?php echo base_url('admin/system_settings/wallet_active?wid='.$w->id); ?>" title="Active" style="margin-left: 5px;" ><i class="fa fa-check-square-o"></i></a>
                                                    <?php
                                                    }
                                                }
                                                if($Owner || $Admin || $GP['wallet_transations']){
                                                ?>
                                                <a href="<?php echo base_url('admin/system_settings/wallet_transations?wid='.$w->id); ?>" title="Transation List" style="margin-left: 5px;" ><i class="fa fa-list"></i></a>
                                                <a href="<?php echo base_url('admin/system_settings/wallets_add_transation?wid='.$w->id); ?>" title="Deposit Amount" style="margin-left: 5px;" ><i class="fa fa-credit-card"></i></a>
                                                <?php
                                                }
                                            ?>
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
