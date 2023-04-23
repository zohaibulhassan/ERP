<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_purchase'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form','method'=>'post');
                echo admin_form_open_multipart("purchaseorder/created2", $attrib)
                ?>
                <div class="row">
                    <div class="col-lg-12">

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("reference_no", "poref"); ?>
                                <?php echo form_input('reference_no', '', 'class="form-control input-tip" id="poref"'); ?>
                                <input type="hidden" name="recid" value='<?= $po_re ?>'>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("order_items"); ?></label>

                                <div class="controls table-controls">
                                    <table id="poTable" class="table items table-striped table-bordered table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th class="col-md-4"><?= lang('product') . ' (' . lang('code') .' - '.lang('name') . ')'; ?></th>
                                            <?php
                                            if ($Settings->product_expiry) {
                                                echo '<th class="col-md-2">' . $this->lang->line("expiry_date") . '</th>';
                                            }
                                            ?>
                                            <th class="col-md-1"><?= lang("net_unit_cost"); ?></th>
                                            <th class="col-md-1"><?= lang("MRP"); ?></th>
                                            <th class="col-md-1"><?= lang("quantity"); ?></th>
                                            <th class="col-md-1"><?= lang("batch#"); ?></th>
                                            <th class="col-md-1"><?= lang("expiry"); ?></th>
                                            <th class="col-md-1"><?= lang("Discount One"); ?></th>
                                            <th class="col-md-1"><?= lang("Discount Two"); ?></th>
                                            <th class="col-md-1"><?= lang("Discount Three"); ?></th>
                                            <th class="col-md-1"><?= lang("FED TAX"); ?></th>
                                            <th class="col-md-1"><?= lang("Further TAX"); ?></th>
                                            <th class="col-md-1"><?= lang("Advance Income TAX"); ?></th>
                                            <?php
                                            if ($Settings->product_discount) {
                                                echo '<th class="col-md-1">' . $this->lang->line("discount") . '</th>';
                                            }
                                            ?>
                                            <?php
                                            if ($Settings->tax1) {
                                                echo '<th class="col-md-1">' . $this->lang->line("product_tax") . '</th>';
                                            }
                                            ?>
                                            <th><?= lang("subtotal"); ?> (<span
                                                    class="currency"><?= $default_currency->code ?></span>)
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                foreach($finaldata['pts'] as $pt){
                                            ?>
                                                <tr>
                                                    <td><?= $pt['product_name'] ?></td>
                                                    <td><?= $pt['expiry'] ?></td>
                                                    <td><?= $pt['net_unit_cost'] ?></td>
                                                    <td><?= $pt['mrp'] ?></td>
                                                    <td><?= $pt['quantity'] ?></td>
                                                    <td><?= $pt['batch'] ?></td>
                                                    <td><?= $pt['expiry'] ?></td>
                                                    <td><?= $pt['discount_one'] ?></td>
                                                    <td><?= $pt['discount_two'] ?></td>
                                                    <td><?= $pt['discount_three'] ?></td>
                                                    <td><?= $pt['fed_tax'] ?></td>
                                                    <td><?= $pt['further_tax'] ?></td>
                                                    <td><?= $pt['adv_tax']*$pt['quantity'] ?></td>
                                                    <td><?= $pt['discount'] ?></td>
                                                    <td>(<?= $pt['tax_rate'] ?>)<br><?= $pt['item_tax']*$pt['quantity'] ?></td>
                                                    <td><?= $pt['subtotal'] ?></td>
                                                </tr>
                                            <?php

                                                }
                                            
                                            ?>
                                            
                                        </tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <input type="hidden" name="total_items" value="" id="total_items" required="required"/>

                        <div class="col-md-12">
                            <div
                                class="from-group"><?php echo form_submit('add_pruchase', $this->lang->line("submit"), 'id="add_pruchase" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="bottom-total" class="well well-sm" style="margin-bottom: 0;">
                    <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                        <tr class="warning">
                            <td><?= lang('items') ?> <span class="totals_val pull-right" id="titems"><?= $finaldata['p']['total_item']; ?></span></td>
                            <td><?= lang('total') ?> <span class="totals_val pull-right" id="total"><?= $finaldata['p']['total']; ?></span></td>
                            <td><?= lang('order_discount') ?> <span class="totals_val pull-right" id="tds"><?= $finaldata['p']['order_discount']; ?></span></td>
                            <td><?= lang('order_tax') ?> <span class="totals_val pull-right" id="tds"><?= $finaldata['p']['order_tax']; ?></span></td>
                            <td><?= lang('shipping') ?> <span class="totals_val pull-right" id="tship"><?= $finaldata['p']['shipping']; ?></span></td>
                            <td><?= lang('grand_total') ?> <span class="totals_val pull-right" id="gtotal"><?= $finaldata['p']['grand_total']; ?></span></td>
                        </tr>
                    </table>
                </div>

                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>


