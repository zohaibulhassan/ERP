<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-file"></i><?= lang("sale_no") . ' ' . $inv->id; ?></h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>">
                        </i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <?php 
                            if ($Owner || $Admin || $GP['sales-edit']) {
                        ?>
                            <li><a href="#addItemSBtn"  data-toggle="modal" data-target="#addItemSBtn"><i class="fa fa-cart-plus"></i> Add Item</a></li>
                            <!-- <li><a href="#editPOModel" id="editPOBtn" data-toggle="modal" data-target="#editPOModel"><i class="fa fa-edit"></i> Edit Sale Detail</a></li> -->
                            <li><a href="<?= admin_url('sales/editinfo/' . $inv->id) ?>"  data-target="#myModal" data-toggle="modal"><i class="fa fa-edit"></i> Edit Sale Detail</a></li>
                            <?php
                            }
                            if(!$return_sale){

                        ?>

                            <li>
                                <a href="<?= admin_url('sales/return_sale/' . $inv->id) ?>">
                                    <i class="fa fa-retweet"></i> Sale Return
                                </a>
                            </li>
                        <?php 
                            }
                            if ($Owner || $Admin || $GP['sales-delete']) {
                        ?>
                            <li>
                                <a id="deleteSale">
                                    <i class="fa fa-trash-o"></i>
                                    <span class="hidden-sm hidden-xs"><?= lang('delete') ?></span>
                                </a>
                            </li>
                        <?php
                            }
                        ?>
                        <li>
                            <a href="<?= admin_url('sales/payments/' . $inv->id) ?>" data-target="#myModal" data-toggle="modal">
                                <i class="fa fa-money"></i> <?= lang('view_payments') ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= admin_url('sales/add_payment/' . $inv->id) ?>" data-target="#myModal" data-toggle="modal">
                                <i class="fa fa-dollar"></i> <?= lang('add_payment') ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= admin_url('sales/pdf/' . $inv->id) ?>">
                                <i class="fa fa-file-pdf-o"></i> <?= lang('export_to_pdf') ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= admin_url('logs?sale_id='.$inv->id) ?>">
                                <i class="fa fa-eye"></i> Show Logs 
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="print-only col-xs-12">
                    <img src="<?= base_url() . 'assets/uploads/logos/' . $biller->logo; ?>" alt="<?= $biller->company != '-' ? $biller->company : $biller->name; ?>">
                </div>
                <div class="well well-sm">
                    <?php
                        $allowcol = 4;
                        if($inv->etalier_id != 0){
                            $allowcol = 3;
                        }
                    ?>
                    <div class="col-xs-<?= $allowcol ?> border-right">

                        <div class="col-xs-2"><i class="fa fa-3x fa-user padding010 text-muted"></i></div>
                        <div class="col-xs-10">
                            <h2 class=""><?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></h2>
                            <?= $customer->company && $customer->company != '-' ? "" : "Attn: " . $customer->name ?>

                            <?php
                            echo $customer->address . "<br>" . $customer->city . " " . $customer->postal_code . " " . $customer->state . "<br>" . $customer->country;

                            echo "<p>";

                            if ($customer->vat_no != "-" && $customer->vat_no != "") {
                                echo "<br>" . lang("vat_no") . ": " . $customer->vat_no;
                            }
                            if ($customer->gst_no != "-" && $customer->gst_no != "") {
                                echo "<br>" . lang("gst_no") . ": " . $customer->gst_no;
                            }
                            if ($customer->cf1 != "-" && $customer->cf1 != "") {
                                echo "<br>" . lang("ccf1") . ": " . $customer->cf1;
                            }
                            if ($customer->cf2 != "-" && $customer->cf2 != "") {
                                echo "<br>" . lang("ccf2") . ": " . $customer->cf2;
                            }
                            if ($customer->cf3 != "-" && $customer->cf3 != "") {
                                echo "<br>" . lang("ccf3") . ": " . $customer->cf3;
                            }
                            if ($customer->cf4 != "-" && $customer->cf4 != "") {
                                echo "<br>" . lang("ccf4") . ": " . $customer->cf4;
                            }
                            if ($customer->cf5 != "-" && $customer->cf5 != "") {
                                echo "<br>" . lang("ccf5") . ": " . $customer->cf5;
                            }
                            if ($customer->cf6 != "-" && $customer->cf6 != "") {
                                echo "<br>" . lang("ccf6") . ": " . $customer->cf6;
                            }

                            echo "</p>";
                            echo lang("tel") . ": " . $customer->phone . "<br>" . lang("email") . ": " . $customer->email;
                            ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <?php if($inv->etalier_id != 0){ ?>
                    <div class="col-xs-<?= $allowcol ?> border-right">

                        <div class="col-xs-2"><i class="fa fa-3x fa-user padding010 text-muted"></i></div>
                        <div class="col-xs-10">
                            <h2 class=""><?= $etalier->company && $etalier->company != '-' ? $etalier->company : $etalier->name; ?></h2>
                            <?= $etalier->company && $etalier->company != '-' ? "" : "Attn: " . $etalier->name ?>

                            <?php
                            echo $etalier->address . "<br>" . $etalier->city . " " . $etalier->postal_code . " " . $etalier->state . "<br>" . $etalier->country;

                            echo "<p>";

                            if ($etalier->vat_no != "-" && $etalier->vat_no != "") {
                                echo "<br>" . lang("vat_no") . ": " . $etalier->vat_no;
                            }
                            if ($etalier->gst_no != "-" && $etalier->gst_no != "") {
                                echo "<br>" . lang("gst_no") . ": " . $etalier->gst_no;
                            }
                            if ($etalier->cf1 != "-" && $etalier->cf1 != "") {
                                echo "<br>" . lang("ccf1") . ": " . $etalier->cf1;
                            }
                            if ($etalier->cf2 != "-" && $etalier->cf2 != "") {
                                echo "<br>" . lang("ccf2") . ": " . $etalier->cf2;
                            }
                            if ($etalier->cf3 != "-" && $etalier->cf3 != "") {
                                echo "<br>" . lang("ccf3") . ": " . $etalier->cf3;
                            }
                            if ($etalier->cf4 != "-" && $etalier->cf4 != "") {
                                echo "<br>" . lang("ccf4") . ": " . $etalier->cf4;
                            }
                            if ($etalier->cf5 != "-" && $etalier->cf5 != "") {
                                echo "<br>" . lang("ccf5") . ": " . $etalier->cf5;
                            }
                            if ($etalier->cf6 != "-" && $etalier->cf6 != "") {
                                echo "<br>" . lang("ccf6") . ": " . $etalier->cf6;
                            }

                            echo "</p>";
                            echo lang("tel") . ": " . $etalier->phone . "<br>" . lang("email") . ": " . $etalier->email;
                            ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <?php }?>
                    <div class="col-xs-<?= $allowcol ?> border-right">
                        <div class="col-xs-2"><i class="fa fa-3x fa-building padding010 text-muted"></i></div>
                        <div class="col-xs-10">
                            <h2 class=""><?= $biller->company != '-' ? $biller->company : $biller->name; ?></h2>
                            <?= $biller->company ? "" : "Attn: " . $biller->name ?>
                            <?php
                                echo $biller->address . "<br>" . $biller->city . " " . $biller->postal_code . " " . $biller->state . "<br>" . $biller->country;
                                echo "<p>";
                                if ($biller->vat_no != "-" && $biller->vat_no != "") {
                                    echo "<br>" . lang("vat_no") . ": " . $biller->vat_no;
                                }
                                if ($biller->gst_no != "-" && $biller->gst_no != "") {
                                    echo "<br>" . lang("gst_no") . ": " . $biller->gst_no;
                                }
                                if ($biller->cf1 != "-" && $biller->cf1 != "") {
                                    echo "<br>" . lang("bcf1") . ": " . $biller->cf1;
                                }
                                if ($biller->cf2 != "-" && $biller->cf2 != "") {
                                    echo "<br>" . lang("bcf2") . ": " . $biller->cf2;
                                }
                                if ($biller->cf3 != "-" && $biller->cf3 != "") {
                                    echo "<br>" . lang("bcf3") . ": " . $biller->cf3;
                                }
                                if ($biller->cf4 != "-" && $biller->cf4 != "") {
                                    echo "<br>" . lang("bcf4") . ": " . $biller->cf4;
                                }
                                if ($biller->cf5 != "-" && $biller->cf5 != "") {
                                    echo "<br>" . lang("bcf5") . ": " . $biller->cf5;
                                }
                                if ($biller->cf6 != "-" && $biller->cf6 != "") {
                                    echo "<br>" . lang("bcf6") . ": " . $biller->cf6;
                                }
                                echo "</p>";
                                echo lang("tel") . ": " . $biller->phone . "<br>" . lang("email") . ": " . $biller->email;
                            ?>
                            <br>
                            <?= $warehouse->name ?>
                            <?php
                                echo $warehouse->address . "<br>";
                                echo ($warehouse->phone ? lang("tel") . ": " . $warehouse->phone . "<br>" : '') . ($warehouse->email ? lang("email") . ": " . $warehouse->email : '');
                            ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-xs-<?= $allowcol ?>">
                        <div class="col-xs-2"><i class="fa fa-3x fa-file-text-o padding010 text-muted"></i></div>
                        <div class="col-xs-10">
                            <h2 class=""><?= lang("ref"); ?>: <?= $inv->reference_no; ?></h2>
                            <p style="font-weight:bold;"><?= lang("date"); ?>: <?= $this->sma->hrld($inv->date); ?></p>
                            <p style="font-weight:bold;"><?= lang("sale_status"); ?>: <?= lang($inv->sale_status); ?></p>
                            <p style="font-weight:bold;"><?= lang("payment_status"); ?>
                                : <?= lang($inv->payment_status); ?></p>
                            <?php if ($inv->payment_status != 'paid') {
                                echo '<p>'.lang('due_date').': '.$this->sma->hrsd($inv->due_date).'</p>';
                            } ?>
                            <strong>Delivery Address : </strong> <?php
                            if($inv->customer_address_id == 0){
                                echo $customer->address;
                            }
                            else{
                                echo $caddress['address'];
                            }
                            ?>
                            <p>&nbsp;</p>

                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="clearfix"></div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped print-table order-table">
                        <thead>
                            <tr>
                                <th><?= lang("no."); ?></th>
                                <th><?= lang("description"); ?> (<?= lang("code"); ?>)</th>
                                <?php if ($Settings->indian_gst) { ?>
                                    <th><?= lang("hsn_code"); ?></th>
                                <?php } ?>
                                <th><?= lang("quantity"); ?></th>
                                <?php
                                if ($Settings->product_serial) {
                                    echo '<th style="text-align:center; vertical-align:middle;">' . lang("serial_no") . '</th>';
                                }
                                ?>
                                <th style="padding-right:20px;"><?= lang("unit_price"); ?></th>
                                <?php
                                if ($Settings->tax1 && $inv->product_tax > 0) {
                                    echo '<th style="padding-right:20px; text-align:center; vertical-align:middle;">' . lang("tax") . '</th>';
                                }
                                echo '<th style="padding-right:20px; text-align:center; vertical-align:middle;">Advance Income Tax</th>';
                                echo '<th style="padding-right:20px; text-align:center; vertical-align:middle;">Further Tax</th>';
                                if ($Settings->product_discount && $inv->product_discount != 0) {
                                    echo '<th style="padding-right:20px; text-align:center; vertical-align:middle;">' . lang("discount") . '</th>';
                                }
                                ?>
                                <th style="padding-right:20px;"><?= lang("subtotal"); ?></th>
                                <?php 
                                    if ($Owner || $Admin || $GP['sales-edit']) {
                                ?>                         
                                <th style="padding-right:20px;"></th>
                                <?php
                                    }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $r = 1;
                        foreach ($rows as $row):
                            ?>
                            <tr>
                                <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                                <td style="vertical-align:middle;">
                                    <?= $row->product_code.' - '.$row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                    <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                    <?= $row->details ? '<br>' . $row->details : ''; ?>
                                </td>
                                <?php if ($Settings->indian_gst) { ?>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                                <?php } ?>
                                <td style="width: 100px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->unit_quantity).' '.$row->product_unit_code; ?></td>
                                <?php
                                if ($Settings->product_serial) {
                                    echo '<td>' . $row->serial_no . '</td>';
                                }
                                ?>
                                <td style="text-align:right; width:120px; padding-right:10px;"><?= $this->sma->formatMoney($row->unit_price); ?></td>
                                <?php
                                if ($Settings->tax1 && $inv->product_tax > 0) {
                                    echo '<td style="width: 120px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>('.($Settings->indian_gst ? $row->tax : $row->tax_code).')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                }
                                echo '<td style="width: 120px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney($row->adv_tax) . '</td>';
                                echo '<td style="width: 120px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney($row->further_tax) . '</td>';
                                if ($Settings->product_discount && $inv->product_discount != 0) {
                                    echo '<td style="width: 120px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                                }
                                ?>
                                <td style="text-align:right; width:120px; padding-right:10px;"><?= $this->sma->formatMoney($row->subtotal); ?></td>
                                <?php 
                                    if ($Owner || $Admin || $GP['sales-edit']) {
                                ?>                         
                                <td>

                                    <ul class="btn-tasks">
                                        <li class="dropdown">
                                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                                <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i>
                                            </a>
                                            <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                                                <?php 
                                                    if(!$return_sale && ($Owner || $Admin || $GP['sales-edit'])){
                                                ?>
                                                    <li><a data-id="<?php echo $row->id; ?>" style="cursor: pointer;" class="si_editbtn"><i class="fa fa-pencil-square-o"></i> Edit</a></li>
                                                    <li><a data-id="<?php echo $row->id; ?>" style="cursor: pointer;" class="si_deletebtn"><i class="fa fa-trash"></i> Delete </a></li>
                                                <?php
                                                    }
                                                ?>
                                                <li><a href="<?= admin_url('logs?sale_id='.$inv->id.'&product_id='.$row->product_id) ?>" style="cursor: pointer;"><i class="fa fa-eye"></i> Show Logs </a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </td>
                                <?php
                                    }
                                ?>
                            </tr>
                            <?php
                            $r++;
                        endforeach;


                        ?>
                        <?php
                        $col = $Settings->indian_gst ? 7 : 6;
                        if ($Settings->product_serial) {
                            $col++;
                        }
                        if ($Settings->product_discount && $inv->product_discount != 0) {
                            $col++;
                        }
                        if ($Settings->tax1 && $inv->product_tax > 0) {
                            $col++;
                        }
                        if ($Settings->product_discount && $inv->product_discount != 0 && $Settings->tax1 && $inv->product_tax > 0) {
                            $tcol = $col - 2;
                        } elseif ($Settings->product_discount && $inv->product_discount != 0) {
                            $tcol = $col - 1;
                        } elseif ($Settings->tax1 && $inv->product_tax > 0) {
                            $tcol = $col - 1;
                        } else {
                            $tcol = $col;
                        }
                        ?>
                        <?php if ($inv->grand_total != $inv->total) { ?>
                            <tr>
                                <td colspan="<?= $tcol; ?>"
                                    style="text-align:right; padding-right:10px;"><?= lang("total"); ?>
                                    (<?= $default_currency->code; ?>)
                                </td>
                                <?php
                                if ($Settings->tax1 && $inv->product_tax > 0) {
                                    echo '<td style="text-align:right;">' . $this->sma->formatMoney($inv->product_tax) . '</td>';
                                }
                                echo '<td style="text-align:right;">' . $this->sma->formatMoney($inv->adv_tax) . '</td>';
                                if ($Settings->product_discount && $inv->product_discount != 0) {
                                    echo '<td style="text-align:right;">' . $this->sma->formatMoney($inv->product_discount) . '</td>';
                                }
                                ?>
                                <td style="text-align:right; padding-right:10px;"><?= $this->sma->formatMoney($inv->total); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>
                        <?php 
                            if($return_sale){
                                ?>
                            <tr>
                                <td  colspan="<?php echo $col+1; ?>"><p style="color:red;text-align:center;font-weight: bold;font-size: 18px;" >Return Items</p></td>
                                <td>
                                    <ul class="btn-tasks">
                                        <li class="dropdown">
                                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                                <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i>
                                            </a>
                                            <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                                                <li><a data-id="<?php echo $return_sale->id; ?>" style="cursor: pointer;" class="si_deletereturn"><i class="fa fa-trash"></i> Delete </a></li>
                                            </ul>
                                        </li>
                                    </ul>

                                </td>
                            </tr>
                                <?php
                            }
                        ?>
                        
                        <?php
                        // Return Items
                        $r = 1;
                        foreach ($return_rows as $row):
                            ?>
                            <tr>
                                <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                                <td style="vertical-align:middle;">
                                    <?= $row->product_code.' - '.$row->product_name ?>
                                    <?= '<br> <p style="color:red">Reason: ' . $row->reason.'</p>'; ?>
                                </td>
                                <?php if ($Settings->indian_gst) { ?>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->product_hsn_code; ?></td>
                                <?php } ?>
                                <td style="width: 100px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->quantity).' pcs'; ?></td>
                                <?php
                                if ($Settings->product_serial) {
                                    echo '<td></td>';
                                }
                                ?>
                                <td style="text-align:right; width:120px; padding-right:10px;"><?= $this->sma->formatMoney($row->net_unit_price); ?></td>
                                <?php
                                if ($Settings->tax1 && $inv->product_tax > 0) {
                                    echo '<td style="width: 120px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                }
                                if ($Settings->product_discount && $inv->product_discount != 0) {
                                    echo '<td style="width: 120px; text-align:right; vertical-align:middle;">' . $this->sma->formatMoney($row->total_discount) . '</td>';
                                }
                                ?>
                                <td style="text-align:right; width:120px; padding-right:10px;"><?= $this->sma->formatMoney($row->subtotal); ?></td>
                                <?php 
                                    if ($Owner || $Admin || $GP['sales-edit']) {
                                ?>                         
                                <td>

                                </td>
                                <?php
                                    }
                                ?>
                            </tr>
                            <?php
                            $r++;
                        endforeach;
                        ?>
                    </tbody>
                    <tfoot>
                            <?php if ($inv->order_discount != 0) {
                                echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang("order_discount") . ' (' . $default_currency->code . ')</td><td style="text-align:right; padding-right:10px;">'.($inv->order_discount_id ? '<small>('.$inv->order_discount_id.')</small> ' : '') . $this->sma->formatMoney($inv->order_discount) . '</td><td></td></tr>';
                            }
                            ?>
                            <?php if ($Settings->tax2 && $inv->order_tax != 0) {
                                echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;">' . lang("order_tax") . ' (' . $default_currency->code . ')</td><td style="text-align:right; padding-right:10px;">' . $this->sma->formatMoney($inv->order_tax) . '</td><td></td></tr>';
                            }
                            ?>
                            <?php if ($inv->shipping != 0) {
                                echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang("shipping") . ' (' . $default_currency->code . ')</td><td style="text-align:right; padding-right:10px;">' . $this->sma->formatMoney($inv->shipping) . '</td><td></td></tr>';
                            }
                            ?>
                            <tr>
                                <td colspan="<?= $col; ?>"
                                    style="text-align:right; font-weight:bold;"><?= lang("total_amount"); ?>
                                    (<?= $default_currency->code; ?>)
                                </td>
                                <td style="text-align:right; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($inv->grand_total); ?></td>
                                <?php 
                                    if ($Owner || $Admin || $GP['sales-edit']) {
                                ?>
                                <td></td>
                                <?php
                                    }
                                ?>
                            </tr>
                            <?php 
                                if($return_sale){
                            ?>
                            <tr>
                                <td colspan="<?= $col; ?>"
                                    style="text-align:right; font-weight:bold;"><?= lang("Return Amount"); ?>
                                    (<?= $default_currency->code; ?>)
                                </td>
                                <td style="text-align:right; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale->grand_total); ?></td>
                                <?php 
                                    if ($Owner || $Admin || $GP['sales-edit']) {
                                ?>
                                <td></td>
                                <?php
                                    }
                                ?>
                            </tr>
                            <?php
                                }
                            ?>
                            <tr>
                                <td colspan="<?= $col; ?>"
                                    style="text-align:right; font-weight:bold;"><?= lang("Recovery"); ?>
                                    (<?= $default_currency->code; ?>)
                                </td>
                                <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($inv->paid); ?></td>
                                <?php 
                                    if ($Owner || $Admin || $GP['sales-edit']) {
                                ?>
                                <td></td>
                                <?php
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td colspan="<?= $col; ?>"
                                    style="text-align:right; font-weight:bold;"><?= lang("balance"); ?>
                                    (<?= $default_currency->code; ?>)
                                </td>
                                <td style="text-align:right; font-weight:bold;">
                                    <?= $this->sma->formatMoney($inv->grand_total - $inv->paid); ?></td>
                                <?php 
                                    if ($Owner || $Admin || $GP['sales-edit']) {
                                ?>
                                <td></td>
                                <?php
                                    }
                                ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row">
                    <div class="col-xs-6">
                        <?php
                        if ($inv->note || $inv->note != "") { ?>
                            <div class="well well-sm">
                                <p class="bold"><?= lang("note"); ?>:</p>

                                <div><?= $this->sma->decode_html($inv->note); ?></div>
                            </div>
                        <?php
                        }
                        if ($inv->staff_note || $inv->staff_note != "") { ?>
                            <div class="well well-sm staff_note">
                                <p class="bold"><?= lang("staff_note"); ?>:</p>

                                <div><?= $this->sma->decode_html($inv->staff_note); ?></div>
                            </div>
                        <?php } ?>

                        <?php if ($customer->award_points != 0 && $Settings->each_spent > 0) { ?>
                        <div class="col-xs-12 col-sm-10 col-md-8 col-lg-6">
                            <div class="well well-sm">
                                <?=
                                '<p>'.lang('this_sale').': '.floor(($inv->grand_total/$Settings->each_spent)*$Settings->ca_point)
                                .'<br>'.
                                lang('total').' '.lang('award_points').': '. $customer->award_points . '</p>';?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="col-xs-6">
                        <div class="well well-sm">
                            <p><?= lang("created_by"); ?>: <?= $inv->created_by ? $created_by->first_name . ' ' . $created_by->last_name : $customer->name; ?> </p>
                            <p><?= lang("date"); ?>: <?= $this->sma->hrld($inv->date); ?></p>
                            <?php if ($inv->updated_by) { ?>
                                <p><?= lang("updated_by"); ?>
                                    : <?= $updated_by->first_name . ' ' . $updated_by->last_name;; ?></p>
                                <p><?= lang("update_at"); ?>: <?= $this->sma->hrld($inv->updated_at); ?></p>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <?php if ($payments) { ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-condensed print-table">
                                    <thead>
                                    <tr>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('payment_reference') ?></th>
                                        <th><?= lang('paid_by') ?></th>
                                        <th><?= lang('amount') ?></th>
                                        <th><?= lang('created_by') ?></th>
                                        <th><?= lang('type') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($payments as $payment) { ?>
                                        <tr <?= $payment->type == 'returned' ? 'class="warning"' : ''; ?>>
                                            <td><?= $this->sma->hrld($payment->date) ?></td>
                                            <td><?= $payment->reference_no; ?></td>
                                            <td><?= lang($payment->paid_by);
                                                if ($payment->paid_by == 'gift_card' || $payment->paid_by == 'CC') {
                                                    echo ' (' . $payment->cc_no . ')';
                                                } elseif ($payment->paid_by == 'Cheque') {
                                                    echo ' (' . $payment->cheque_no . ')';
                                                }
                                                ?></td>
                                            <td><?= $this->sma->formatMoney($payment->amount); ?></td>
                                            <td><?= $payment->first_name . ' ' . $payment->last_name; ?></td>
                                            <td><?= lang($payment->type); ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editPOModel" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
</div>
<div class="modal fade" id="editItemModel" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Edit Sale Item</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('#', 'id="editItemForm"'); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Price</label>
                                <input type="text" name="price" id="ediItemPrice" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Qty</label>
                                <input type="number" name="qty" id="editItemQtyTxt" value="0" class="form-control" >
                                <input type="hidden" name="id" id="ediItemId">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Batch</label>
                                <select name="batch" id="editsi_batch" class="form-control" > 

                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Discount One</label>
                                <div class="input-group" style="width: 100%;">
                                    <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                        <input type="checkbox" name="done_chk" class="editItemchk" id="editItemdone_chk" value="0">
                                    </div>
                                    <input type="text" name="done_txt" id="editItemdone_txt" class="form-control" style="width:100%;" readonly value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Discount Two</label>
                                <div class="input-group" style="width: 100%;">
                                    <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                        <input type="checkbox" name="dtwo_chk" class="editItemchk" id="editItemdtwo_chk" value="0">
                                    </div>
                                    <input type="text" name="dtwo_txt" id="editItemdtwo_txt" class="form-control" style="width:100%;" readonly value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Discount Three</label>
                                <div class="input-group" style="width: 100%;">
                                    <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                        <input type="checkbox" name="dth_chk" class="editItemchk" id="editItemdth_chk" value="0">
                                    </div>
                                    <input type="text" name="dth_txt" id="editItemdth_txt" class="form-control" style="width:100%;" readonly value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Bulk Discount</label>
                                <select name="editbulkdiscount" id="editsi_bulkdiscount" class="form-control" > 

                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Reason <span style="color:red">*</span></label>
                                <input type="text" name="reason" class="form-control">
                            </div>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateItemBtn">Update</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addItemSBtn" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Add New Item</h4>
            </div>

            <?php echo form_open('#', 'id="addItemForm"'); ?>
                <input type="hidden" name="sid" value="<?= $inv->id ?>" >
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Product</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="hidden" name="product" id="product" class="form-control" style="width:100%;" placeholder="<?= lang("select") . ' Product' ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Batch</label>
                                <select name="batch" id="addsi_batch" class="form-control" > 

                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Net Unit Cost</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="text" name="net_unit_cost" value="0" id="net_unit_cost" class="form-control" style="width:100%;" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>MRP</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="text" name="mrp" id="mrp" value="0" class="form-control" style="width:100%;" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Quantity</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="number" min="1" name="qty" id="qty" class="form-control" autocomplete="off" value="1" style="width:100%;" >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Discount One</label>
                                <div class="input-group" style="width: 100%;">
                                    <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                        <input type="checkbox" name="done_chk" class="addItemchk" id="done_chk" value="0">
                                    </div>
                                    <input type="text" name="done_txt" id="done_txt" class="form-control" style="width:100%;" readonly value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Discount Two</label>
                                <div class="input-group" style="width: 100%;">
                                    <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                        <input type="checkbox" name="dtwo_chk" class="addItemchk" id="dtwo_chk" value="0">
                                    </div>
                                    <input type="text" name="dtwo_txt" id="dtwo_txt" class="form-control" style="width:100%;" readonly value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Discount Three</label>
                                <div class="input-group" style="width: 100%;">
                                    <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                        <input type="checkbox" name="dth_chk" class="addItemchk" id="dth_chk" value="0">
                                    </div>
                                    <input type="text" name="dth_txt" id="dth_txt" class="form-control" style="width:100%;" readonly value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Bulk Discount</label>
                                <select name="bulkdiscount" id="addsi_bulkdiscount" class="form-control" > 

                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Total Discount</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="text" name="td" id="td" class="form-control" style="width:100%;" readonly value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>FED Tax</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="text" name="fed" id="fed" class="form-control" style="width:100%;" readonly value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Product Tax</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="text" name="producttax" id="producttax" class="form-control" style="width:100%;" readonly value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Sub Total</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="text" name="total" id="addtotal" class="form-control" style="width:100%;" readonly value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="addItemBtn">Add</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>



<!-- <script src="<?php echo $assets; ?>plugins/sweetalert/sweetalert.js"></script> -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function(e){
        $('.date2').datetimepicker({
            format: 'yyyy-mm-dd', 
            fontAwesome: true, 
            language: 'sma', 
            todayBtn: 1, 
            autoclose: 1, 
            minView: 2 
        });
        $('#updateBtn').click(function(){
            $('#editForm').submit();    
        });
        $('#editForm').submit(function(e){
            e.preventDefault();
            $('#updateBtn').prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('sales/salesedit'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    $('#updateBtn').prop('disabled', false);
                    var obj = jQuery.parseJSON(data);
                    $('#ajaxCall').hide();
                    
                    if(obj.codestatus == 'ok'){
                        alert('Update Successfully');
                        location.reload();
                    }
                    else{
                        alert(obj.codestatus);
                    }
                },
                error: function(jqXHR, textStatus){
                    var errorStatus = jqXHR.status;
                    if(errorStatus==0){ 
                        alert("Internet Connection Problem");
                    }
                    else{
                        alert('Try Again. Error Code '+errorStatus);
                    }
                    $('#updateBtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                }
            });

        });
        function editcol(){
            var price = $('#ediItemPrice').val();
            var qty = $('#editItemQtyTxt').val();
            var pd1 = $('#editItemdone_chk').val();
            var pd2 = $('#editItemdtwo_chk').val();
            var pd3 = $('#editItemdth_chk').val();
            $('#editItemdone_txt').val((((price/100)*pd1)*qty).toFixed(4));
            $('#editItemdtwo_txt').val((((price/100)*pd2)*qty).toFixed(4));
            $('#editItemdth_txt').val((((price/100)*pd3)*qty).toFixed(4));
        }
        $('#ediItemPrice').change(function(){
            editcol();
        });
        $('#editItemQtyTxt').change(function(){
            editcol();
        });
        $('#editsi_batch').change(function(){
            var price = $(this).find(':selected').data('price');
            console.log(price);
            $('#ediItemPrice').val(price);
            editcol();
        });
        $('.si_editbtn').click(function(){
            $('#ajaxCall').show();
            var id = $(this).data('id');
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            $.ajax({
                url: '<?= admin_url('sales/itemdetail'); ?>',
                data: {
                    'id':id,
                    [csrfName]:csrfHash,
                },
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    if(obj.codestatus == "ok"){
                        $('#editItemQtyTxt').val(obj.detail.quantity);
                        $('#editsi_batch').html(obj.htmlbatchs);
                        $('#editsi_bulkdiscount').html(obj.htmldiscount);
                        $('#ediItemPrice').val(obj.detail.net_unit_price);
                        $('#ediItemId').val(obj.detail.id);
                        $('#editItemdone_chk').val(obj.detail.pd1 == null ? 0 : obj.detail.pd1);
                        $('#editItemdtwo_chk').val(obj.detail.pd2 == '' ? 0 : obj.detail.pd2);
                        $('#editItemdth_chk').val(obj.detail.pd3 == '' ? 0 : obj.detail.pd3);
                        if(obj.detail.discount_one!="" && obj.detail.discount_one!= 0 && obj.detail.discount_one!= null){
                            $('#editItemdone_chk').iCheck('check');
                        }
                        else{
                            $('#editItemdone_chk').iCheck('uncheck');
                        }
                        if(obj.detail.discount_two!="" && obj.detail.discount_two!= 0 && obj.detail.discount_two!= null){
                            $('#editItemdtwo_chk').iCheck('check');
                        }
                        else{
                            $('#editItemdtwo_chk').iCheck('uncheck');
                        }
                        if(obj.detail.discount_three!="" && obj.detail.discount_three!= 0 && obj.detail.discount_three!= null){
                            $('#editItemdth_chk').iCheck('check');
                        }
                        else{
                            $('#editItemdth_chk').iCheck('uncheck');
                        }
                        // $('#editItemdone_txt').val(obj.detail.pd1 == null ? 0 : obj.detail.pd1);
                        // $('#editItemdtwo_txt').val(obj.detail.pd2 == '' ? 0 : obj.detail.pd2);
                        // $('#editItemdth_txt').val(obj.detail.pd3 == '' ? 0 : obj.detail.pd3);
                        editcol();
                        $('#editItemModel').modal('show');
                    }
                    else{
                        alert(obj.codestatus);
                        $('#editItemModel').modal('hide');
                    }
                    $('#ajaxCall').hide();
                },
                error: function(){
                    alert('Try Again!');
                    $('#ajaxCall').hide();
                }

            });
        });
        $('#updateItemBtn').click(function(){
            $('#editItemForm').submit();    
        });
        $('#editItemForm').submit(function(e){
            e.preventDefault();
            $('#updateItemBtn').prop('disabled', true);
            $('#updateItemBtn').prop('disabled', false);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('sales/updateitem'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    $('#updateItemBtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                    if(obj.codestatus == 'ok'){
                        location.reload();
                        alert('Item Update Successfuly');
                    }
                    else{
                        alert(obj.codestatus);
                    }
                },
                error: function(jqXHR, textStatus){
                    var errorStatus = jqXHR.status;
                    if(errorStatus==0){ 
                        alert("Internet Connection Problem");
                    }
                    else{
                        alert('Try Again. Error Code '+errorStatus);
                    }
                    $('#updateItemBtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                }
            });
        });
        $('.si_deletebtn').click(function(){
            var iid = $(this).data('id');
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to delete this item!",
                icon: "warning",
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Delete',
                showLoaderOnConfirm: true,
                preConfirm: (reason) => {
                    return fetch(`<?= base_url('admin/sales/itemdelete?id=') ?>${iid}&reason=${reason}&[${csrfName}]=${csrfHash}`)
                    .then(response => {
                        console.log(response);
                        if (!response.ok) {
                            console.log('Error');
                            throw new Error(response.statusText)
                        }
                        else if(reason == ""){
                            throw new Error('Enter Reason')
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        )
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            })
            .then((result) => {
                console.log('CR: '+result);
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Item Delete Successfully',
                        showConfirmButton: false,
                        timer: 10000
                    });
                    setTimeout(function(){ 
                        location.reload();
                    }, 1000);
                }
            });
        });
        $('#product').change(function(){
            var pid = $(this).val();
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            $.ajax({
                type: "post",
                url: '<?= admin_url('sales/batches'); ?>',
                data: {
                    'pid': pid,
                    'wid':<?php echo $inv->warehouse_id; ?>,
                    'sid':<?php echo $inv->supplier_id; ?>,
                    [csrfName]:csrfHash,
                },
                success: function(data) {
                    var obj = jQuery.parseJSON(data);

                    $('#addsi_batch').html(obj.htmlbatchs);
                    $('#addsi_bulkdiscount').html(obj.htmldiscount);
                }
            });
        });
        $('#product').select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function(element, callback) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: '<?= admin_url('sales/pgetSuppliers'); ?>' + $(element).val(),
                    dataType: "json",
                    success: function(data) {
                        callback(data[0]);
                    }
                });
            },
            ajax: {
                url: '<?= admin_url('sales/productslist'); ?>',
                dataType: 'json',
                quietMillis: 15,
                data: function(term, page) {
                    return {
                        term: term,
                        supplier_id: <?php echo $inv->supplier_id; ?>,
                        warehouse_id: <?php echo $inv->warehouse_id; ?>,
                        own_company: 0,
                        limit: 15
                    };
                },
                results: function(data, page) {
                    if (data.results != null) {
                        return { results: data.results };
                    } else {
                        return { results: [{ id: '', text: 'No Match Found' }] };
                    }
                }
            }
        });
        $('#addsi_batch').change(function(){
            filladdform();
        });
        $('#qty').change(function(){
            filladdform();
        });
        $(document).on('ifChanged','#done_chk,#dtwo_chk,#dth_chk', function(event) {
            filladdform();
        });

        function filladdform(){
            var pid = $('#product').val();
            var qty = $('#qty').val();
            var wid = <?php echo $inv->warehouse_id; ?>;
            var cid = <?php echo $inv->customer_id; ?>;
            var batch = $('#addsi_batch').val();
            var bulkdiscount = $('#addsi_bulkdiscount').val();
            var cd1 = 'no';
            var cd2 = 'no';
            var cd3 = 'no';
            if($("#done_chk").prop("checked")){
                cd1 = 'yes';
            }
            if($("#dtwo_chk").prop("checked")){
                cd2 = 'yes';
            }
            if($("#dth_chk").prop("checked")){
                cd3 = 'yes';
            }
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            if(batch != ""){
                $.ajax({
                    type: "post",
                    url: '<?= admin_url('sales/productdetail'); ?>',
                    data: {
                        'pid': pid,
                        'qty': qty,
                        'cd1': cd1,
                        'cd2': cd2,
                        'cd3': cd3,
                        'wid': wid,
                        'cid': cid,
                        'batch': batch,
                        'bulkdiscount': bulkdiscount,
                        [csrfName]:csrfHash,
                    },
                    success: function(data) {
                        var obj = jQuery.parseJSON(data);
                        $("#net_unit_cost").val(obj.detail.sellingprice);
                        $("#mrp").val(obj.detail.mrp);
                        $("#done_chk").val(obj.detail.d1);
                        $("#dtwo_chk").val(obj.detail.d2);
                        $("#done_txt").val(obj.detail.d1a.toFixed(4));
                        $("#dtwo_txt").val(obj.detail.d2a.toFixed(4));
                        $("#dth_chk").val(obj.detail.d3);
                        $("#dth_txt").val(obj.detail.d3a.toFixed(4));
                        $("#td").val(obj.detail.d.toFixed(4));
                        $("#fed").val(obj.detail.fedtax);
                        $("#producttax").val(obj.detail.tax.toFixed(4));
                        $("#addtotal").val(obj.detail.subtotal.toFixed(4));
                    }
                });
            }
            else{
                $("#net_unit_cost").val(0);
                $("#mrp").val(0);
                $("#qty").val(0);

                $("#done_chk").val(0);
                $("#dtwo_chk").val(0);
                $("#dth_chk").val(0);

                $("#done_txt").val(0);
                $("#dtwo_txt").val(0);
                $("#dth_txt").val(0);

                $("#td").val(0);
                $("#fed").val(0);
                $("#producttax").val(0);
                $("#addtotal").val(0);

            }

        }
        $("#addItemForm").submit(function(e){
             e.preventDefault();
             $(':input[type="submit"]').prop('disabled', true);
             $.ajax({
                url: '<?= admin_url('sales/additem'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    if(obj.codestatus == "Item Add Successfully"){
                        location.reload();
                    }
                    alert(obj.codestatus);
                    $(':input[type="submit"]').prop('disabled', false);
                },
                error: function(jqXHR, textStatus){
                    var errorStatus = jqXHR.status;
                    $(':input[type="submit"]').prop('disabled', false);
                }
            });
            
        });
        $('#addsi_bulkdiscount').change(function(){
            filladdform();
        });
        $('#editsi_bulkdiscount').change(function(){
            var edidiscount3 = $(this).val();
            $('#editItemdth_chk').val(edidiscount3);
            console.log(edidiscount3);
            editcol();
        });
        $('#deleteSale').click(function(){
            // var iid = $(this).data('id');
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to delete this sale!",
                icon: "warning",
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Delete',
                showLoaderOnConfirm: true,
                preConfirm: (reason) => {
                    return fetch(`<?= base_url('admin/sales/delete/'.$inv->id.'?reason=') ?>${reason}&[${csrfName}]=${csrfHash}`)
                    .then(response => {
                        console.log(response);
                        if (!response.ok) {
                            console.log('Error');
                            throw new Error(response.statusText)
                        }
                        else if(reason == ""){
                            throw new Error('Enter Reason')
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        )
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            })
            .then((result) => {
                console.log('CR: '+result);
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sale  Delete Successfully',
                        showConfirmButton: false,
                        timer: 10000
                    });
                    setTimeout(function(){ 
                        window.location.href = "<?= base_url('admin/sales') ?>";
                        // location.reload();
                    }, 1000);
                }
            });
        });
        $('.si_deletereturn').click(function(){
            var iid = $(this).data('id');
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to delete this return!",
                icon: "warning",
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Delete',
                showLoaderOnConfirm: true,
                preConfirm: (reason) => {
                    console.log(reason);
                    $.ajax({
                        type: "get",
                        url: '<?= admin_url('sales/returndelete'); ?>',
                        data: {
                            'id': iid,
                            'reason': reason,
                            [csrfName]:csrfHash
                        },
                        success: function(data) {
                            var obj = jQuery.parseJSON(data);
                            if(obj.codestatus == "Return sale deleted"){
                                Swal.fire({
                                    title: obj.codestatus ,
                                    icon: "success",
                                });
                                location.reload();
                            }
                            else{
                                Swal.fire({
                                    title: obj.codestatus,
                                    icon: "error",
                                });
                            }
                            throw new Error(obj.codestatus);
                        },
                        error: function(jqXHR, textStatus){
                            var errorStatus = jqXHR.status;
                            Swal.fire({
                                title: errorStatus,
                                icon: "error",
                            });
                            throw new Error(errorStatus);
                        }
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            })
            .then((result) => {
                console.log('CR: '+JSON.stringify(result));
                if (result.isConfirmed) {
                    // Swal.fire({
                    //     icon: 'success',
                    //     title: 'Return Delete Successfully',
                    //     showConfirmButton: false,
                    //     timer: 10000
                    // });
                    // setTimeout(function(){ 
                    //     location.reload();
                    // }, 1000);
                }
            });
        });

    });
</script>