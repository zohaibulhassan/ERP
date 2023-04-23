<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Deposit in Wallet'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'stForm');
                echo admin_form_open_multipart("system_settings/wallet_depoit", $attrib);
                ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" name="amount" class="form-control" >
                            <input type="hidden" name="wid" value="<?php echo $wid; ?>" >
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="fprom-group">
                            <button type="submit" class="btn btn-primary">Deposit</button>
                            <a href="<?php echo base_url('admin/system_settings/wallets'); ?>" class="btn btn-danger"><?= lang('Cancel') ?></a>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
