<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Add New Wallet'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'stForm');
                echo admin_form_open_multipart("system_settings/insert_wallet", $attrib);
                ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" >
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Location</label>
                            <select name="localtion" class="form-control">
                                <?php
                                    foreach($warehouses as $w){
                                    ?>
                                    <option value="<?php echo $w->id; ?>"><?php echo $w->name; ?></option>
                                    <?php
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>First User</label>
                            <select name="fname" class="form-control">
                                <option value="0">Not Allow</option>
                                <?php
                                    foreach($userslist as $u){
                                    ?>
                                    <option value="<?php echo $u->id; ?>"><?php echo $u->first_name.' '.$u->last_name; ?></option>
                                    <?php
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Second User</label>
                            <select name="sname" class="form-control">
                                <option value="0">Not Allow</option>
                                <?php
                                    foreach($userslist as $u){
                                    ?>
                                    <option value="<?php echo $u->id; ?>"><?php echo $u->first_name.' '.$u->last_name; ?></option>
                                    <?php
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="fprom-group">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="<?php echo base_url('admin/system_settings/wallets'); ?>" class="btn btn-danger"><?= lang('Cancel') ?></a>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
