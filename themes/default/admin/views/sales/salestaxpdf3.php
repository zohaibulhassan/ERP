<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->lang->line('sale') . ' ' . $inv->reference_no; ?></title>
    <link href="<?= $assets ?>styles/pdf/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $assets ?>styles/pdf/pdf.css" rel="stylesheet">
</head>
<body>
    <div id="wrap">
        <div class="row">
            <div class="col-lg-12">
                <?php if ($logo) {
                ?>
                    <div class="text-center" style="margin-bottom:20px;">
                        <!-- <img src="<?= $base64; ?>" alt="<?= $customer->company != '-' ? $customer->company : $customer->name; ?>"> -->
                        <h1 style="font-size: 20px;font-weight: bolder;font-family: 'Arial' !important;">SALES TAX INVOICE</h1>
                    </div>
                <?php }
                ?>
                <div class="clearfix"></div>
                <div class="padding10">
                    <?php if ($Settings->invoice_view == 1) { ?>
                        <div class="col-xs-12 text-center">
                            <h1><?= lang('tax_invoice'); ?></h1>
                        </div>
                    <?php } ?>

                    <div class="col-xs-4">
                        <strong><?php echo $this->lang->line("to"); ?>:</strong>
                        <?= $customer->company != '-' ? $customer->company : $customer->name; ?><br>
                        <?= $customer->company ? '' : 'Attn: ' . $customer->name; ?>
                        <?php
                        echo $customer->address /* . ', ' . $customer->city . ' , ' . $customer->postal_code . ' , ' . $customer->state . ' , ' . $customer->country*/;
                        echo '<p>';
                        echo lang('tel') . ': ' . $customer->phone . '<br />' . lang('email') . ': ' . $customer->email;
                        if ($customer->vat_no != "-" && $customer->vat_no != "") {
                            echo "<br>" . lang("NTN") . ": " . $customer->vat_no;
                        }
                        if ($customer->gst_no != "-" && $customer->gst_no != "") {
                            echo "<br>" . lang("gst_no") . ": " . $customer->gst_no;
                        }
                        if ($customer->cf1 != '-' && $customer->cf1 != '') {
                            echo '<br>' . lang('bcf1') . ': ' . $customer->cf1;
                        }
                        if ($customer->cf2 != '-' && $customer->cf2 != '') {
                            echo '<br>' . lang('bcf2') . ': ' . $customer->cf2;
                        }
                        if ($customer->cf3 != '-' && $customer->cf3 != '') {
                            echo '<br>' . lang('bcf3') . ': ' . $customer->cf3;
                        }
                        if ($customer->cf4 != '-' && $customer->cf4 != '') {
                            echo '<br>' . lang('bcf4') . ': ' . $customer->cf4;
                        }
                        if ($customer->cf5 != '-' && $customer->cf5 != '') {
                            echo '<br>' . lang('bcf5') . ': ' . $customer->cf5;
                        }
                        if ($customer->cf6 != '-' && $customer->cf6 != '') {
                            echo '<br>' . lang('bcf6') . ': ' . $customer->cf6;
                        }
                        echo '</p>';
                        ?>
                        <div class="clearfix"></div>
                    </div>

                    <div class="col-xs-4">
                        <strong><?php echo $this->lang->line("from"); ?>:</strong>
                        <?= $own_company->companyname; ?><br>
                        <?= $own_company->registeraddress; ?><br>
                        <?php echo lang('Contact Person') . ': ' . $own_company->registerperson . '<br />' . lang('Mobile') . ': ' . $own_company->mobile; ?>
                        <?php
                        echo '';
                        if ($own_company->ntn != "-" && $own_company->ntn != "") {
                            echo "<br>" . lang("NTN") . ": " . $own_company->ntn;
                        }
                        if ($own_company->strn != "-" && $own_company->strn != "") {
                            echo "<br>" . lang("STRN") . ": " . $own_company->strn;
                        }
                        if ($own_company->srb != "-" && $own_company->srb != "") {
                            echo "<br>" . lang("SRB") . ": " . $own_company->srb;
                        }
                        echo '';
                        ?>
                    </div>

                    <div class="col-xs-4">
                        <strong>Bill # : </strong> SI-<?php echo $inv->reference_no; ?><br>
                        <strong> <?= lang('date'); ?>: </strong> <?= $this->sma->hrld($inv->date); ?><br>
                        <strong>Payment Status : </strong> <?= lang($inv->payment_status); ?><br>
                        <strong>P.O Number : </strong> <?= lang($inv->po_number); ?><br>
                        <p style="width:70%" ><strong>Delivery Address : </strong> <?php
                        if($inv->customer_address_id == 0){
                            echo $customer->address;
                        }
                        else{
                            echo $caddress['address'];
                        }
                        ?></p>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="padding10">
                    <div class="col-xs-5">
                        <div class="bold">
                            <!-- <?= lang('date'); ?>: <?= $this->sma->hrld($inv->date); ?><br> -->
                            <!-- <?= lang('ref'); ?>: <?= $inv->reference_no; ?><br> -->
                            <!-- <?= lang("payment_status"); ?>: <?= lang($inv->payment_status); ?> -->
                            <!-- <?php if ($inv->payment_status != 'paid') {
                                        echo '<br>' . lang('due_date') . ': ' . $this->sma->hrsd($inv->due_date);
                                    } ?>
                        <?php if (!empty($inv->return_sale_ref)) {
                            echo lang("return_ref") . ': ' . $inv->return_sale_ref . '<br>';
                        } ?> -->
                            <!-- <div class="order_barcodes barcode">
                            <?php
                            $path = admin_url('misc/barcode/' . $this->sma->base64url_encode($inv->reference_no) . '/code128/74/0/1');
                            $type = $Settings->barcode_img ? 'png' : 'svg+xml';
                            $data = file_get_contents($path);
                            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            ?>
                            <img src="<?= $base64; ?>" alt="<?= $inv->reference_no; ?>" class="bcimg" />
                            <?php /*echo $this->sma->qrcode('link', urlencode(admin_url('sales/view/' . $inv->id)), 2);*/ ?>
                        </div> -->
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <div class="clearfix"></div>
                <?php
                $col = $Settings->indian_gst ? 9 : 8;
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
                <div class="col-xs-12" style="margin-top: 15px;">
                    <div class="table-responsive">

                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th><?= lang('no'); ?></th>
                                    <th>SKU</th>
                                    <th>Company Code - <?= lang('description'); ?></th>
                                    <?php if ($Settings->indian_gst) { ?>
                                        <th><?= lang("hsn_code"); ?></th>
                                    <?php } ?>
                                    <th><?= lang('quantity'); ?></th>
                                    <th><?= lang('unit_price'); ?></th>
                                    <th>MRP</th>
                                    <!-- <th>Tax type</th> -->
                                    <?php
                                    if ($Settings->tax1 && $inv->product_tax > 0) {
                                        echo '<th>' . lang('tax') . '</th>';
                                    }
                                    ?>
                                    <?php if ($customer->gst_no == "") { ?>
                                        <th>Further Tax</th>
                                    <?php } ?>
                                    <?php
                                    echo '<th>Discount 1</th>';
                                    echo '<th>Discount 2</th>';
                                    echo '<th>Discount 3</th>';
                                    ?>
                                    <th>Advance Income Tax</th>
                                    <th><?= lang('subtotal'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $r = 1;
                                $total_adv_tax = 0;
                                $total_further_tax = 0;
                                $my_subtotal = 0;
                                $total_discount_one = 0;
                                $total_discount_two = 0;
                                $total_discount_three = 0;
                                $total_discount = 0;
                                $excluding_amount_price = 0;
                                $total_tax_amount = 0;
                                $General_sales_tax_total = 0;
                                $thired_schedule_tax_total = 0;
                                foreach ($rows as $row) :

                                    $unit_price_show = ($row->sma_companies_sales_type == "consignment" || $row->sma_companies_sales_type == "cost") ? ($row->unit_price) : (($row->sma_companies_sales_type == "dropship") ? ($row->dropship) : (($row->sma_companies_sales_type == "crossdock") ? ($row->crossdock) : (($row->sma_companies_sales_type == "services") ? ($row->services) :
                                        '0')));

                                    // $two = $this->sma->formatDecimal($unit_price_show) + ($this->sma->formatDecimal((($row->item_tax) / $row->unit_quantity)));
                                    $two = $this->sma->formatDecimal($unit_price_show + (($row->item_tax) / $row->quantity));

                                    //$three = $this->sma->formatDecimal($two - ($row->discount_one/100)*$unit_price_show);
                                    $four = $this->sma->formatDecimal($two - ($row->discount_two / 100) * $unit_price_show);
                                    $five = $this->sma->formatDecimal($four - ($row->discount_three / 100) * $unit_price_show);


                                    $further_tax = (($row->tax_type == 2) ? "" : (($unit_price_show * 3) / 100) * $row->unit_quantity);


                                    // echo $further_tax * $row->unit_quantity . "<br><br><br>";


                                    $total_further_tax += $this->sma->formatDecimal($further_tax);


                                    $my_row_subtotal = $this->sma->formatDecimal(($five * $row->unit_quantity)+$row->adv_tax);


                                    $my_subtotal += $this->sma->formatDecimal($five * $row->unit_quantity);


                                    $total_discount_one += $this->sma->formatDecimal((($row->discount_one / 100) * $unit_price_show) * $row->unit_quantity);


                                    $total_discount_two += ($this->sma->formatDecimal($this->sma->formatDecimal($this->sma->formatDecimal($row->discount_two) / 100) * $unit_price_show) *  $row->unit_quantity);


                                    // $total_discount_two += $this->sma->formatDecimal((($row->discount_two/100)*$unit_price_show) * $row->unit_quantity);

                                    // echo $total_discount_two . "<br>";


                                    $total_discount_three += $this->sma->formatDecimal((($row->discount_three / 100) * $unit_price_show) * $row->unit_quantity);

                                    $total_discount += $this->sma->formatDecimal(((($row->discount_two / 100) * $unit_price_show) + (($row->discount_three / 100) * $unit_price_show) + (($row->discount_one / 100) * $unit_price_show))  * $row->unit_quantity);


                                    $excluding_amount_price += $this->sma->formatDecimal(($unit_price_show * $row->unit_quantity));

                                    $total_tax_amount += $this->sma->formatDecimal((($row->item_tax) / $row->unit_quantity) * $row->unit_quantity);


                                    // var_dump( $row );

                                    $General_sales_tax = ($row->tax_type == "1") ? ((($row->item_tax) / $row->quantity) * $row->unit_quantity) : 'a';

                                    $General_sales_tax_total += $this->sma->formatDecimal($General_sales_tax);

                                    $thired_schedule_tax = ($row->tax_type == "2") ? ((($row->item_tax) / $row->quantity) * $row->unit_quantity) : 'b';

                                    $thired_schedule_tax_total += $this->sma->formatDecimal($thired_schedule_tax);

                                    // echo "<br><br>General_sales_tax_total : " . $General_sales_tax_total . "<br><br>";

                                    // echo "<br><br>thired_schedule_tax_total : " . $thired_schedule_tax_total . "<br><br>";

                                    // echo "<br><br>total_further_tax : " . $total_further_tax . "<br><br>";

                                    // echo "<br><br>total_sales_tax_amount : " . ($General_sales_tax_total + $thired_schedule_tax_total) . "<br><br>";

                                    $total_sales_tax_amount = $this->sma->formatDecimal($General_sales_tax_total + $thired_schedule_tax_total);

                                    $total_adv_tax = $total_adv_tax+$row->adv_tax;

                                    // echo "<br><br> my_subtotal : " . $my_subtotal . "<br><br>";

                                    // $net_amount = $this->sma->formatDecimal($my_subtotal + $total_sales_tax_amount + $total_further_tax);
                                    // if ($customer->vat_no == "") {
                                    //     $net_amount = $this->sma->formatDecimal($my_subtotal + $total_further_tax);
                                    // } else {
                                    //     $net_amount = $this->sma->formatDecimal($my_subtotal);
                                    // }


                                    if ($customer->gst_no == "") {
                                        $net_amount = $this->sma->formatDecimal($my_subtotal + $total_further_tax);
                                    } else {
                                        $net_amount = $this->sma->formatDecimal($my_subtotal);
                                    }

                                    if ($inv->order_discount !== "0.0000") {
                                        $net_amount = $this->sma->formatDecimal($net_amount - $inv->order_discount);
                                    }

                                    // $net_amount = $this->sma->formatDecimal($total_sales_tax_amount);
                                    // $net_amount = $this->sma->formatDecimal($total_further_tax);

                                ?>
                                    <tr>
                                        <td style="text-align:center; width:20px; vertical-align:middle;"><?= $r; ?></td>
                                        <td style="text-align:center; width:20px; vertical-align:middle;"><?= $row->product_code; ?></td>
                                        <td style="vertical-align:middle; width:160px; ">
                                            <?= $row->company_code . '-' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                            <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                            <?= $row->details ? '<br>' . $row->details : ''; ?>
                                            <?= $row->serial_no ? '<br>' . $row->serial_no : ''; ?>
                                            <?= $row->hsn_code ? '<br>' . "HS-Code : " . $row->hsn_code : ''; ?>

                                        </td>
                                        <td style="width: 80px; text-align:center; vertical-align:middle; width:20px; "><?= $this->sma->formatQuantity($row->unit_quantity) . ' ' . $row->product_unit_code; ?></td>
                                        <td style="text-align:center; width:90px;vertical-align:middle; width:20px; "><?= $this->sma->formatMoney($row->unit_price); ?></td>
                                        <td style="text-align:center; width:90px;vertical-align:middle; width:20px; "><?= $this->sma->formatMoney($row->mrp); ?></td>
                                        <!-- <td style="text-align:center; width:90px;"><?= ($row->tax_type == 2) ? "3rd schedule" : $row->tax_name; ?></td> -->
                                        <?php
                                        if ($Settings->tax1 && $inv->product_tax > 0) {
                                            echo '<td style="width: 90px; text-align:center; vertical-align:middle; width:80px; ">' . ($row->item_tax != 0 ? '(' . (($row->tax_type == 2) ? "3rd schedule" : $row->tax_name) . ')<br>' : '') . $this->sma->formatMoney(($row->item_tax / $row->quantity)) . '</td>';
                                        }
                                        // if ( $Settings->product_discount && $inv->product_discount != 0) {
                                        //     echo '<td style="width: 90px; text-align:center; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                                        // }
                                        echo '<td style="text-align:center; vertical-align:middle; width:60px; ">' . $this->sma->formatQuantity(($row->discount_one == "") ? "0" : $row->discount_one) . '% <br> ' . ($row->discount_one / 100) * $unit_price_show . '</td>';
                                        echo '<td style="text-align:center; vertical-align:middle; width:60px; ">' . $this->sma->formatQuantity(($row->discount_two == "") ? "0" : $row->discount_two) . '% <br> ' . $this->sma->formatQuantity(($row->discount_two / 100) * $unit_price_show) . '</td>';
                                        echo '<td style="text-align:center; vertical-align:middle; width:60px; ">' . $this->sma->formatQuantity(($row->discount_three == "") ? "0" : $row->discount_three) . '% <br> ' . $this->sma->formatQuantity(($row->discount_three / 100) * $unit_price_show) . '</td>';
                                        ?>
                                        <?php if ($customer->gst_no == "") { ?>
                                            <td style="text-align:center; width:90px;vertical-align:middle; width:20px; "><?php echo $further_tax; ?></td>
                                        <?php } ?>
                                        <td style="vertical-align:middle; text-align:center; width:80px;"><?= $this->sma->formatMoney($row->adv_tax); ?></td>
                                        <td style="vertical-align:middle; text-align:center; width:80px;"><?= $this->sma->formatMoney($my_row_subtotal); ?></td>
                                    </tr>
                                    <?php
                                    $r++;
                                endforeach;
                                    ?>
                            </tbody>
                            <tfoot>
                        </table>

                        <style>
                            /* DivTable.com */
                            .divTable {
                                display: table;
                                width: 100%;
                            }

                            .divTableRow {
                                display: table-row;
                            }

                            .divTableHeading {
                                background-color: #EEE;
                                display: table-header-group;
                            }

                            .divTableCell,
                            .divTableHead {
                                /* border: 1px solid #efefef; */
                                display: table-cell;
                                padding: 3px 10px;
                            }

                            .divTable .divTable .divTableCell {
                                border: 1px solid #efefef;
                            }

                            .divTableHeading {
                                background-color: #EEE;
                                display: table-header-group;
                                font-weight: bold;
                            }

                            .divTableFoot {
                                background-color: #EEE;
                                display: table-footer-group;
                                font-weight: bold;
                            }

                            .divTableBody {
                                display: table-row-group;
                            }

                            .pl-0 {
                                padding-left: 0px;
                            }

                            .pr-0 {
                                padding-right: 0px;
                            }

                            .bold {
                                font-weight: bold;
                            }
                            .page-number { font-size: 10px;position: absolute;bottom: 60px;right: 80px; }
                            .page-number:after { content: counter(page); }
                        </style>

                        <div class="divTable">
                            <div class="divTableBody">
                                <div class="divTableRow">

                                    <div class="divTableCell pl-0 pr-0">


                                        <div class="divTable">
                                            <div class="divTableBody">
                                                <div class="divTableRow">
                                                    <div class="divTableCell bold">Amount Exculding S/Tax</div>
                                                    <div class="divTableCell"><?php echo $this->sma->formatMoney($excluding_amount_price); ?></div>
                                                </div>
                                                <div class="divTableRow">
                                                    <div class="divTableCell bold">3rd Schedule</div>
                                                    <div class="divTableCell"><?php echo $this->sma->formatMoney($thired_schedule_tax_total); ?></div>
                                                </div>
                                                <div class="divTableRow">
                                                    <div class="divTableCell bold">General Sales Tax Amount</div>
                                                    <div class="divTableCell"><?php echo $this->sma->formatMoney($General_sales_tax_total); ?></div>
                                                </div>
                                                <div class="divTableRow">
                                                    <div class="divTableCell bold">Total Sales Tax Amount</div>
                                                    <div class="divTableCell"><?php echo $this->sma->formatMoney($total_sales_tax_amount); ?></div>
                                                </div>
                                                <div class="divTableRow">
                                                    <div class="divTableCell bold">Total Advance Income Tax</div>
                                                    <div class="divTableCell"><?php echo $this->sma->formatMoney2($total_adv_tax); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- DivTable.com -->


                                    </div>
                                    <div class="divTableCell">



                                        <div class="divTable">
                                            <div class="divTableBody">
                                                <div class="divTableRow">
                                                    <div class="divTableCell bold">Total Discount 1</div>
                                                    <div class="divTableCell"><?php echo $this->sma->formatMoney($total_discount_one); ?></div>
                                                </div>
                                                <div class="divTableRow">
                                                    <div class="divTableCell bold">Total Discount 2 </div>
                                                    <div class="divTableCell"><?php echo $this->sma->formatMoney($total_discount_two); ?></div>
                                                </div>
                                                <div class="divTableRow">
                                                    <div class="divTableCell bold">Total Discount 3</div>
                                                    <div class="divTableCell"><?php echo $this->sma->formatMoney($total_discount_three); ?></div>
                                                </div>
                                                <div class="divTableRow">
                                                    <div class="divTableCell bold">Total Discount</div>
                                                    <!-- <div class="divTableCell"><?php echo $this->sma->formatMoney($total_discount); ?></div> -->
                                                    <div class="divTableCell"><?php echo $this->sma->formatMoney($total_discount_one + $total_discount_two + $total_discount_three); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- DivTable.com -->



                                    </div>
                                    <div class="divTableCell pl-0 pr-0">



                                        <div class="divTable">
                                            <div class="divTableBody">

                                                <?php if ($inv->order_discount !== "0.0000") { ?>
                                                    <div class="divTableRow">
                                                        <div class="divTableCell bold">Extra Discount</div>
                                                        <div class="divTableCell"><?php echo $this->sma->formatMoney($inv->order_discount); ?></div>
                                                    </div>
                                                <?php } ?>


                                                <?php if ($customer->gst_no == "") { ?>
                                                    <div class="divTableRow">
                                                        <div class="divTableCell bold">Total Further Tax</div>
                                                        <div class="divTableCell"><?php echo $this->sma->formatMoney($total_further_tax); ?></div>
                                                    </div>
                                                <?php } ?>
                                                <div class="divTableRow">
                                                    <div class="divTableCell bold">Net Amount</div>
                                                    <div class="divTableCell"><?php echo $this->sma->formatMoney(($net_amount+$total_adv_tax) - $total_discount_one); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- DivTable.com -->




                                    </div>
                                </div>
                            </div>
                        </div>


                        </tr>
                        </table>


                        <!-- <?php if ($inv->grand_total != $inv->total) {
                                ?>
                        <tr>
                            <td colspan="<?= $tcol; ?>" style="text-align:center;"><?= lang('total'); ?>
                                (<?= $default_currency->code; ?>)
                            </td>
                            <?php
                                    if ($Settings->tax1 && $inv->product_tax > 0) {
                                        echo '<td style="text-align:center;">' . $this->sma->formatMoney($return_sale ? ($inv->product_tax + $return_sale->product_tax) : $inv->product_tax) . '</td>';
                                    }
                                    if ($Settings->product_discount && $inv->product_discount != 0) {
                                        echo '<td style="text-align:center;">' . $this->sma->formatMoney($return_sale ? ($inv->product_discount + $return_sale->product_discount) : $inv->product_discount) . '</td>';
                                    }
                            ?>
                            <td style="text-align:center;"><?= $this->sma->formatMoney($return_sale ? (($inv->total + $inv->product_tax) + ($return_sale->total + $return_sale->product_tax)) : ($inv->total + $inv->product_tax)); ?></td>
                        </tr>
                    <?php }
                    ?>
                    <?php
                    if ($return_sale) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:center;">' . lang("return_total") . ' (' . $default_currency->code . ')</td><td style="text-align:center;">' . $this->sma->formatMoney($return_sale->grand_total) . '</td></tr>';
                    }
                    if ($inv->surcharge != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:center;">' . lang("return_surcharge") . ' (' . $default_currency->code . ')</td><td style="text-align:center;">' . $this->sma->formatMoney($inv->surcharge) . '</td></tr>';
                    }
                    ?>

                    <?php if ($Settings->indian_gst) {
                        if ($inv->cgst > 0) {
                            $cgst = $return_sale ? $inv->cgst + $return_sale->cgst : $inv->cgst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('cgst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ($Settings->format_gst ? $this->sma->formatMoney($cgst) : $cgst) . '</td></tr>';
                        }
                        if ($inv->sgst > 0) {
                            $sgst = $return_sale ? $inv->sgst + $return_sale->sgst : $inv->sgst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('sgst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ($Settings->format_gst ? $this->sma->formatMoney($sgst) : $sgst) . '</td></tr>';
                        }
                        if ($inv->igst > 0) {
                            $igst = $return_sale ? $inv->igst + $return_sale->igst : $inv->igst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('igst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ($Settings->format_gst ? $this->sma->formatMoney($igst) : $igst) . '</td></tr>';
                        }
                    } ?>

                    <?php if ($inv->order_discount != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:center;">' . lang('order_discount') . ' (' . $default_currency->code . ')</td><td style="text-align:center;">' . ($inv->order_discount_id ? '<small>(' . $inv->order_discount_id . ')</small> ' : '') . $this->sma->formatMoney($return_sale ? ($inv->order_discount + $return_sale->order_discount) : $inv->order_discount) . '</td></tr>';
                    }
                    ?>
                    <?php if ($Settings->tax2 && $inv->order_tax != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:center;">' . lang('order_tax') . ' (' . $default_currency->code . ')</td><td style="text-align:center;">' . $this->sma->formatMoney($return_sale ? ($inv->order_tax + $return_sale->order_tax) : $inv->order_tax) . '</td></tr>';
                    }
                    ?>
                    <?php if ($inv->shipping != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:center;">' . lang('shipping') . ' (' . $default_currency->code . ')</td><td style="text-align:center;">' . $this->sma->formatMoney($inv->shipping) . '</td></tr>';
                    }
                    ?>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:center; font-weight:bold;"><?= lang('total_amount'); ?>
                            (<?= $default_currency->code; ?>)
                        </td>
                        <td style="text-align:center; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : $inv->grand_total); ?></td>
                    </tr>

                    <tr>
                        <td colspan="<?= $col; ?>" style="text-align:center; font-weight:bold;"><?= lang('paid'); ?>
                            (<?= $default_currency->code; ?>)
                        </td>
                        <td style="text-align:center; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid); ?></td>
                    </tr>
                    <tr>
                        <td colspan="<?= $col; ?>" style="text-align:center; font-weight:bold;"><?= lang('balance'); ?>
                            (<?= $default_currency->code; ?>)
                        </td>
                        <td style="text-align:center; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->grand_total + $return_sale->grand_total) : $inv->grand_total) - ($return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid)); ?></td>
                    </tr>

                    </tfoot>
                </table> -->
                    </div>
                    <?= $Settings->invoice_view > 0 ? $this->gst->summary($rows, $return_rows, ($return_sale ? $inv->product_tax + $return_sale->product_tax : $inv->product_tax)) : ''; ?>
                </div>
                <div class="clearfix"></div>
                <div class="col-xs-12">
                    <?php if ($inv->note || $inv->note != '') { ?>
                        <div class="well well-sm">
                            <p class="bold"><?= lang('note'); ?>:</p>

                            <div><?= $this->sma->decode_html($inv->note); ?></div>
                        </div>
                    <?php }
                    ?>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</body>
</html>