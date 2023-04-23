<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->lang->line("purchase") . " " . $inv->reference_no; ?></title>
    <link href="<?= $assets ?>styles/pdf/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $assets ?>styles/pdf/pdf.css" rel="stylesheet">
</head>
<body>
    <div id="wrap">
        <div class="row">
            <div class="col-lg-12">
                <?php
                    echo "<h2 class='text-center'> Purchase Order</h2>";
                ?>
                <div class="clearfix"></div>
                <div class="row padding10  margin-top: 45px;">
                    <div class="col-xs-4">
                        <?= $this->lang->line("to"); ?>
                        <h6 class=""><?= $supplier->company && $supplier->company != '-' ? $supplier->company : $supplier->name; ?></h6>
                        <?= $supplier->company && $supplier->company != '-' ? "" : "Attn: " . $supplier->name; ?>
                        <?php
                        echo $supplier->address . "<br />" . $supplier->city . " " . $supplier->postal_code . " " . $supplier->state . "<br />" . $supplier->country . "<br>";
                        if ($supplier->cf1 != "-" && $supplier->cf1 != "") {
                            echo "<strong> NTN : </strong>" . $supplier->cf1 . "<br>";
                        }
                        if ($supplier->gst_no != "-" && $supplier->gst_no != "") {
                            echo "<strong> GST : </strong>" . $supplier->gst_no;
                        }
                        echo "<p>";
                        if ($supplier->vat_no != "-" && $supplier->vat_no != "") {
                            echo "<br>" . lang("vat_no") . ": " . $supplier->vat_no;
                        }

                        if ($supplier->cf2 != "-" && $supplier->cf2 != "") {
                            echo "<br>" . lang("scf2") . ": " . $supplier->cf2;
                        }
                        if ($supplier->cf3 != "-" && $supplier->cf3 != "") {
                            echo "<br>" . lang("scf3") . ": " . $supplier->cf3;
                        }
                        if ($supplier->cf4 != "-" && $supplier->cf4 != "") {
                            echo "<br>" . lang("scf4") . ": " . $supplier->cf4;
                        }
                        if ($supplier->cf5 != "-" && $supplier->cf5 != "") {
                            echo "<br>" . lang("scf5") . ": " . $supplier->cf5;
                        }
                        if ($supplier->cf6 != "-" && $supplier->cf6 != "") {
                            echo "<br>" . lang("scf6") . ": " . $supplier->cf6;
                        }
                        echo "</p>";
                        echo "<strong> Phone : </strong>" . $supplier->phone . "<br />";
                        echo "<strong> Email : </strong>" . $supplier->email;
                        ?>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-xs-4">
                        <?= $this->lang->line("from"); ?>
                        <?php
                        if ($own_company->companyname != "-" && $own_company->companyname != "") {
                            echo "<h6>" . $own_company->companyname . "</h6>";
                        }

                        if ($own_company->registeraddress != "-" && $own_company->registeraddress != "") {
                            echo "<strong> Register Address : </strong>" . $own_company->registeraddress . "<br>";
                        }

                        if ($own_company->warehouseaddress != "-" && $own_company->warehouseaddress != "") {
                            echo "<strong> Company Address : </strong>" . $own_company->warehouseaddress . "<br>";
                        }

                        if ($own_company->ntn != "-" && $own_company->ntn != "") {
                            echo "<strong> NTN : </strong>" . $own_company->ntn . "<br>";
                        }

                        if ($own_company->strn != "-" && $own_company->strn != "") {
                            echo "<strong> STRN : </strong>" . $own_company->strn . "<br>";
                        }

                        if ($own_company->srb != "-" && $own_company->srb != "") {
                            echo "<strong> SRB : </strong>" . $own_company->srb . "<br>";
                        }

                        if ($own_company->mobile != "-" && $own_company->mobile != "") {
                            echo "<strong> Phone : </strong>" . $own_company->mobile . "<br>";
                        }
                        ?>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-xs-4">
                        <span style="padding-left:10px; padding-bottom:20px; display: block;">
                            <strong>
                                <?= lang("date"); ?>: <?= $this->sma->hrld($inv->date); ?>
                            </strong>
                            <br>
                            <strong>
                                <?= lang("ref"); ?>: <?= $inv->reference_no; ?>
                            </strong>
                        </span>
                        <div class="pull-left order_barcodes">
                            <?php
                            $path = admin_url('misc/barcode/' . $this->sma->base64url_encode($inv->reference_no) . '/code128/74/0/1');
                            $type = $Settings->barcode_img ? 'png' : 'svg+xml';
                            $data = file_get_contents($path);
                            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            ?>
                            <img src="<?= $base64; ?>" alt="<?= $inv->reference_no; ?>" class="bcimg" />
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <?php
                $col = $Settings->indian_gst ? 8 : 7;
                if ($inv->status == 'partial') {
                    $col++;
                }
                if ($Settings->product_discount && $inv->product_discount != 0) {
                    $col++;
                }
                if ($Settings->tax1 && $inv->product_tax > 0) {
                    $col++;
                }
                if ($Settings->product_discount && $inv->product_discount != 0 && $Settings->tax1 && $inv->product_tax > 0) {
                    $tcol = $col - 1;
                } elseif ($Settings->product_discount && $inv->product_discount != 0) {
                    $tcol = $col - 1;
                } elseif ($Settings->tax1 && $inv->product_tax > 0) {
                    $tcol = $col - 1;
                } elseif (!empty($row->discount_one)) {
                    $tcol = $col - 1;
                } elseif (!empty($row->discount_two)) {
                    $tcol = $col - 1;
                } elseif (!empty($row->discount_three)) {
                    $tcol = $col - 1;
                } else {
                    $tcol = $col;
                }
                ?>
                <div style="margin-top: 45px; padding-top: 45px;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr class="active" style="font-weight: bold;">
                                    <th><?= lang("no."); ?></th>
                                    <th><?= lang("description"); ?></th>
                                    <th>MRP</th>
                                    <?php if ($Settings->indian_gst) { ?>
                                        <th><?= lang("hsn_code"); ?></th>
                                    <?php } ?>
                                    <th><?= lang("quantity"); ?></th>
                                    <?php
                                    if ($inv->status == 'partial') {
                                        echo '<th>' . lang("received") . '</th>';
                                    }
                                    ?>
                                    <th><?= lang("unit_cost"); ?></th>
                                    <th><?= lang("3rd Schedule Value"); ?></th>
                                    <th><?= lang("Batch"); ?></th>
                                    <th><?= lang("Expiry"); ?></th>
                                    <th><?= lang("Purchase Value"); ?></th>
                                    <?php
                                    if ($inv->product_discount > 0) {
                                        ?>
                                        <th><?= lang("discount_one"); ?></th>
                                        <th><?= lang("discount_two"); ?></th>
                                        <th><?= lang("discount_three"); ?></th>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                    if ($Settings->tax1 && $inv->product_tax > 0) {
                                        echo '<th>' . lang("tax") . '</th>';
                                    }
                                    echo '<th>Advance Income Tax</th>';
                                    ?>
                                    <?php
                                    if ($Settings->product_discount && $inv->product_discount != 0) {
                                        echo '<th>' . lang("discount") . '</th>';
                                    }
                                    ?>
                                    <th><?= lang("Net Amount"); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $r = 1;
                                $total_purchase_value = 0;
                                $total_purchase_value_tax = 0;
                                $total_consumer_discount = 0;
                                $total_sales_incentive = 0;
                                $total_trade_discount = 0;
                                $total_discounts = 0;
                                foreach ($rows as $row) :
                                ?>
                                    <tr>
                                        <td style="text-align:center; vertical-align:middle;"><?= $r; ?></td>
                                        <td style="vertical-align:middle; ">
                                            <?= $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                            <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                            <?= $row->supplier_part_no ? '<br>' . lang('supplier_part_no') . ': ' . $row->supplier_part_no : ''; ?>
                                            <?= $row->details ? '<br>' . $row->details : ''; ?>
                                        </td>
                                            <td style="text-align:center; vertical-align:middle;"><?= $row->mrp; ?></td>
                                        <?php if ($Settings->indian_gst) { ?>
                                            <td style="text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                                        <?php } ?>
                                        <td style="text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->quantity) . ' ' . $row->product_unit_code; ?></td>
                                        <?php
                                        if ($inv->status == 'partial') {
                                            echo '<td style="text-center:center;vertical-align:middle;">' . $this->sma->formatQuantity($row->quantity_received) . ' ' . $row->product_unit_code . '</td>';
                                        }
                                        ?>
                                        <td style="text-align:right; vertical-align:middle;"><?= $this->sma->formatMoney($row->net_unit_cost); ?></td>
                                        <td style="text-align:center; vertical-align:middle;"><?= (($row->tax_type == "2") && ($row->tax_code !== "exp")  ? $this->sma->formatMoney(($row->mrp / 1.17) * $row->quantity) : "Exempted"); ?></td>
                                        <td style="text-align:center; vertical-align:middle;"><?= $row->batch; ?></td>
                                        <td style="text-align:center; vertical-align:middle;"><?= $row->expiry; ?></td>
                                        <td style="text-align:center; vertical-align:middle;"><?= $this->sma->formatMoney($row->net_unit_cost * $row->quantity); ?></td>
                                        <?php
                                        if ($inv->product_discount > 0) {
                                            ?>
                                            <td style="text-align:center; vertical-align:middle;"><?= (empty($row->discount_one)) ? "0" : "" . $this->sma->formatMoney($row->discount_one) . "% <br>" . $this->sma->formatMoney((($row->price * ($row->discount_one)) / 100) * $row->quantity); ?></td>
                                            <td style="text-align:center; vertical-align:middle;"><?= (empty($row->discount_two)) ? "0" : "" . $this->sma->formatMoney($row->discount_two) . "% <br>" . $this->sma->formatMoney((($row->price * ($row->discount_two)) / 100) * $row->quantity); ?></td>
                                            <td style="text-align:center; vertical-align:middle;"><?= (empty($row->discount_three)) ? "0" : "" . $this->sma->formatMoney($row->discount_three) . "% <br>" . $this->sma->formatMoney((($row->price * ($row->discount_three)) / 100) * $row->quantity);  ?></td>
                                            <?php
                                        }
                                        ?>

                                        <?php
                                        if ($Settings->tax1 && $inv->product_tax > 0) {
                                            echo '<td style="text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '(' . ($Settings->indian_gst ? $row->tax : $row->tax_rate) . ') <br>' : '') . $this->sma->formatMoney($row->item_tax) . '</td>';
                                        }
                                        echo '<td style="text-align:right; vertical-align:middle;">'.$row->adv_tax.'</td>';
                                        ?>
                                        <?php
                                        if ($Settings->product_discount && $inv->product_discount != 0) {
                                            echo '<td style="text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '(' . $this->sma->formatMoney($row->discount) . ')' : '0') . /* $this->sma->formatMoney($row->item_discount) . */ '</td>';
                                        }
                                        ?>
                                        <td style="text-align:right; vertical-align:middle;"><?= $this->sma->formatMoney($row->subtotal - $row->item_discount); ?></td>
                                    </tr>
                                    <?php
                                    $total_purchase_value += ($row->net_unit_cost * $row->quantity);
                                    $total_purchase_value_tax += ($row->item_tax);
                                    $total_consumer_discount += (($row->price * ($row->discount_three)) / 100) * $row->quantity;
                                    $total_sales_incentive += (($row->price * ($row->discount_one)) / 100) * $row->quantity;
                                    $total_trade_discount += (($row->price * ($row->discount_two)) / 100) * $row->quantity;
                                    $total_discounts += ($row->item_discount);
                                    $r++;
                                endforeach;
                                if ($return_rows) {
                                    echo '<tr class="warning"><td colspan="' . ($col + 1) . '" class="no-border"><strong>' . lang('returned_items') . '</strong></td></tr>';
                                    foreach ($return_rows as $row) :
                                    ?>
                                        <tr>
                                            <td style="text-align:center; vertical-align:middle;"><?= $r; ?></td>
                                            <td style="vertical-align:middle;">
                                                <?= $row->product_code . ' - ' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                                <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                                <?= $row->supplier_part_no ? '<br>' . lang('supplier_part_no') . ': ' . $row->supplier_part_no : ''; ?>
                                                <?= $row->details ? '<br>' . $row->details : ''; ?>
                                                <?= ($row->expiry && $row->expiry != '0000-00-00') ? '<br>' . lang('expiry') . ': ' . $this->sma->hrsd($row->expiry) : ''; ?>
                                            </td>
                                            <?php if ($Settings->indian_gst) { ?>
                                                <td style="text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                                            <?php } ?>
                                            <td style="text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->quantity) . ' ' . $row->product_unit_code; ?></td>
                                            <?php
                                            if ($inv->status == 'partial') {
                                                echo '<td style="text-align:center;vertical-align:middle;">' . $this->sma->formatQuantity($row->quantity_received) . ' ' . $row->product_unit_code . '</td>';
                                            }
                                            ?>
                                            <td style="text-align:right;"><?= $this->sma->formatMoney($row->unit_cost); ?></td>

                                            <?php
                                            if ($Settings->tax1 && $inv->product_tax > 0) {
                                                echo '<td style="text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>(' . ($Settings->indian_gst ? $row->tax : $row->tax_rate) . ')</small> ' : '') . $this->sma->formatMoney($row->item_tax) . '</td>';
                                            }
                                            if ($Settings->product_discount && $inv->product_discount != 0) {
                                                echo '<td style="text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' .  $this->sma->formatMoney($row->discount) . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                                            }
                                            ?>
                                            <td style="text-align:right; "><?= $this->sma->formatMoney($row->subtotal); ?></td>
                                        </tr>
                                <?php
                                        $r++;
                                    endforeach;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?= $Settings->invoice_view > 0 ? $this->gst->summary($rows, $return_rows, ($return_purchase ? $inv->product_tax + $return_purchase->product_tax : $inv->product_tax), true) : ''; ?>
                </div>
                <div class="clearfix"></div>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-7">
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                            echo lang("total") . " Purchase Value : ";
                            ?>
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                            echo $this->sma->formatMoney($total_purchase_value);
                            ?>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-sm-7">
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                                echo lang("total") . " Product Tax : ";
                            ?>
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                                echo $this->sma->formatMoney($inv->product_tax);
                            ?>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-sm-7">
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                                echo lang("total") . " Advance Income Tax : ";
                            ?>
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                                echo $this->sma->formatMoney($inv->total_adv_tax);
                            ?>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-sm-7">
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                                echo lang("total") . " Tax : ";
                            ?>
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                                echo $this->sma->formatMoney($inv->product_tax+$inv->total_adv_tax);
                            ?>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-sm-7">
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                            echo lang("Value With") . " Tax : ";
                            ?>
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                            $suprememe_amount = $total_purchase_value + $inv->product_tax + $inv->total_adv_tax;
                            echo $this->sma->formatMoney($suprememe_amount);
                            ?>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-sm-7">
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                            echo "Consumer Discount : ";
                            ?>
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                            echo $this->sma->formatMoney($total_consumer_discount);
                            ?>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-sm-7">
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                            echo lang("Subtotal : ");
                            ?>
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                            echo $this->sma->formatMoney(($total_purchase_value + $inv->product_tax + $inv->total_adv_tax) - $total_consumer_discount);
                            ?>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-sm-7">
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                            echo "Sales Incentive : ";
                            ?>
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                            echo $this->sma->formatMoney($total_sales_incentive);
                            ?>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-sm-7">
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                            echo "Trade Discount : ";
                            ?>
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                            echo $this->sma->formatMoney($total_trade_discount);
                            ?>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-7">
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                            echo "Discount : ";
                            ?>
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                            echo $this->sma->formatMoney($total_discounts);
                            ?>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-sm-7">
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php
                            echo lang("Total Net Amount : ");
                            ?>
                        </div>
                        <div class="col-sm-2 font-weight-bold">
                            <?= $this->sma->formatMoney(($total_purchase_value + $inv->product_tax+$inv->total_adv_tax) - $total_sales_incentive) ?>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                </div>
                <p>* This is a computer generated slip and does not require signature</p>
                <div class="row">
                    <div class="col-xs-7 pull-left">
                        <?php if ($inv->note || $inv->note != "") { ?>
                            <div class="well well-sm">
                                <p class="bold"><?= lang("note"); ?>:</p>
                                <div><?= $this->sma->decode_html($inv->note); ?></div>
                            </div>
                        <?php }
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
