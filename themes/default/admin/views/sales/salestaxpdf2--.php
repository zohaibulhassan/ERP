<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->lang->line('sale') . ' ' . $inv->reference_no; ?></title>
    <link href="<?= $assets ?>styles/pdf/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $assets ?>styles/pdf/pdf.css" rel="stylesheet">
</head>

<!-- 
Oen Company : <pre><?php print_r($own_company); ?></pre>
<br><br><br>
Inventory :<pre><?php print_r($inv); ?></pre>
<br><br><br>
Rows :<pre><?php print_r($rows); ?></pre> -->

<body>
<div id="wrap">
    <div class="row">
        <div class="col-lg-12">
            <?php if ($logo) {
                $path = base_url() . 'assets/uploads/logos/' . $biller->logo;
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                ?>
                <div class="text-center" style="margin-bottom:20px;">
                    <!-- <img src="<?= $base64; ?>" alt="<?= $biller->company != '-' ? $biller->company : $biller->name; ?>"> -->
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
                    <?= $biller->company != '-' ? $biller->company : $biller->name; ?><br>
                    <?= $biller->company ? '' : 'Attn: ' . $biller->name; ?>
                    <?php
                        echo $biller->address /* . ', ' . $biller->city . ' , ' . $biller->postal_code . ' , ' . $biller->state . ' , ' . $biller->country*/;
                        echo '<p>';
                        echo lang('tel') . ': ' . $biller->phone . '<br />' . lang('email') . ': ' . $biller->email;
                        if ($biller->vat_no != "-" && $biller->vat_no != "") {
                            echo "<br>" . lang("NTN") . ": " . $biller->vat_no;
                        }
                        if ($biller->gst_no != "-" && $biller->gst_no != "") {
                            echo "<br>" . lang("gst_no") . ": " . $biller->gst_no;
                        }
                        if ($biller->cf1 != '-' && $biller->cf1 != '') {
                            echo '<br>' . lang('bcf1') . ': ' . $biller->cf1;
                        }
                        if ($biller->cf2 != '-' && $biller->cf2 != '') {
                            echo '<br>' . lang('bcf2') . ': ' . $biller->cf2;
                        }
                        if ($biller->cf3 != '-' && $biller->cf3 != '') {
                            echo '<br>' . lang('bcf3') . ': ' . $biller->cf3;
                        }
                        if ($biller->cf4 != '-' && $biller->cf4 != '') {
                            echo '<br>' . lang('bcf4') . ': ' . $biller->cf4;
                        }
                        if ($biller->cf5 != '-' && $biller->cf5 != '') {
                            echo '<br>' . lang('bcf5') . ': ' . $biller->cf5;
                        }
                        if ($biller->cf6 != '-' && $biller->cf6 != '') {
                            echo '<br>' . lang('bcf6') . ': ' . $biller->cf6;
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
                    <strong> <?= lang('date'); ?>: </strong>  <?= $this->sma->hrld($inv->date); ?><br>
                    <strong>Payment Status : </strong> <?= lang($inv->payment_status); ?>
                </div>


                <!-- <div class="col-xs-5">
                    <?php echo $this->lang->line("to"); ?>:
                    <h2 class=""><?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></h2>
                    <?= $customer->company && $customer->company != '-' ? '' : 'Attn: ' . $customer->name; ?>
                    <?php
                        echo $customer->address . '<br />' . $customer->city . ' ' . $customer->postal_code . ' ' . $customer->state . '<br />' . $customer->country;
                        echo '<p>';
                        if ($customer->vat_no != "-" && $customer->vat_no != "") {
                            echo "<br>" . lang("vat_no") . ": " . $customer->vat_no;
                        }
                        if ($customer->gst_no != "-" && $customer->gst_no != "") {
                            echo "<br>" . lang("gst_no") . ": " . $customer->gst_no;
                        }
                        if ($customer->cf1 != '-' && $customer->cf1 != '') {
                            echo '<br>' . lang('ccf1') . ': ' . $customer->cf1;
                        }
                        if ($customer->cf2 != '-' && $customer->cf2 != '') {
                            echo '<br>' . lang('ccf2') . ': ' . $customer->cf2;
                        }
                        if ($customer->cf3 != '-' && $customer->cf3 != '') {
                            echo '<br>' . lang('ccf3') . ': ' . $customer->cf3;
                        }
                        if ($customer->cf4 != '-' && $customer->cf4 != '') {
                            echo '<br>' . lang('ccf4') . ': ' . $customer->cf4;
                        }
                        if ($customer->cf5 != '-' && $customer->cf5 != '') {
                            echo '<br>' . lang('ccf5') . ': ' . $customer->cf5;
                        }
                        if ($customer->cf6 != '-' && $customer->cf6 != '') {
                            echo '<br>' . lang('ccf6') . ': ' . $customer->cf6;
                        }
                        echo '</p>';
                        echo lang('tel') . ': ' . $customer->phone . '<br />' . lang('email') . ': ' . $customer->email;
                    ?>
                </div>

                <div class="col-xs-5">
                    <?php echo $this->lang->line("from"); ?>:
                    <h2 class=""><?= $biller->company != '-' ? $biller->company : $biller->name; ?></h2>
                    <?= $biller->company ? '' : 'Attn: ' . $biller->name; ?>
                    <?php
                        echo $biller->address . '<br />' . $biller->city . ' ' . $biller->postal_code . ' ' . $biller->state . '<br />' . $biller->country;
                        echo '<p>';
                        if ($biller->vat_no != "-" && $biller->vat_no != "") {
                            echo "<br>" . lang("vat_no") . ": " . $biller->vat_no;
                        }
                        if ($biller->gst_no != "-" && $biller->gst_no != "") {
                            echo "<br>" . lang("gst_no") . ": " . $biller->gst_no;
                        }
                        if ($biller->cf1 != '-' && $biller->cf1 != '') {
                            echo '<br>' . lang('bcf1') . ': ' . $biller->cf1;
                        }
                        if ($biller->cf2 != '-' && $biller->cf2 != '') {
                            echo '<br>' . lang('bcf2') . ': ' . $biller->cf2;
                        }
                        if ($biller->cf3 != '-' && $biller->cf3 != '') {
                            echo '<br>' . lang('bcf3') . ': ' . $biller->cf3;
                        }
                        if ($biller->cf4 != '-' && $biller->cf4 != '') {
                            echo '<br>' . lang('bcf4') . ': ' . $biller->cf4;
                        }
                        if ($biller->cf5 != '-' && $biller->cf5 != '') {
                            echo '<br>' . lang('bcf5') . ': ' . $biller->cf5;
                        }
                        if ($biller->cf6 != '-' && $biller->cf6 != '') {
                            echo '<br>' . lang('bcf6') . ': ' . $biller->cf6;
                        }
                        echo '</p>';
                        echo lang('tel') . ': ' . $biller->phone . '<br />' . lang('email') . ': ' . $biller->email;
                    ?>
                    <div class="clearfix"></div>
                </div> -->

            </div>
            <div class="clearfix"></div>
            <div class="padding10">
                <div class="col-xs-5">
                    <div class="bold">
                        <!-- <?= lang('date'); ?>: <?= $this->sma->hrld($inv->date); ?><br> -->
                        <!-- <?= lang('ref'); ?>: <?= $inv->reference_no; ?><br> -->
                        <!-- <?= lang("payment_status"); ?>: <?= lang($inv->payment_status); ?> -->
                        <!-- <?php if ($inv->payment_status != 'paid') {
                            echo '<br>'.lang('due_date').': '.$this->sma->hrsd($inv->due_date);
                        } ?>
                        <?php if (!empty($inv->return_sale_ref)) {
                            echo lang("return_ref").': '.$inv->return_sale_ref.'<br>';
                        } ?> -->
                        <!-- <div class="order_barcodes barcode">
                            <?php
                            $path = admin_url('misc/barcode/'.$this->sma->base64url_encode($inv->reference_no).'/code128/74/0/1');
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
                if ( $Settings->product_discount && $inv->product_discount != 0) {
                    $col++;
                }
                if ($Settings->tax1 && $inv->product_tax > 0) {
                    $col++;
                }
                if ( $Settings->product_discount && $inv->product_discount != 0 && $Settings->tax1 && $inv->product_tax > 0) {
                    $tcol = $col - 2;
                } elseif ( $Settings->product_discount && $inv->product_discount != 0) {
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
                        <th >Company Code - <?= lang('description'); ?></th>
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
                        <?php
                            // echo '<th>Discount 1</th>';
                            // echo '<th>Discount 2</th>';
                            // echo '<th>Discount 3</th>';
                        ?>
                        <th><?= lang('subtotal'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $r = 1;
                        foreach ($rows as $row):

                            $unit_price_show = $this->sma->formatDecimal(($row->sma_companies_sales_type == "consignment" || $row->sma_companies_sales_type == "cost") ? ($row->unit_price) : 
                            (($row->sma_companies_sales_type == "dropship") ? ($row->dropship) : 
                            (($row->sma_companies_sales_type == "crossdock") ? ($row->crossdock) : 
                            (($row->sma_companies_sales_type == "services") ? ($row->services) : 
                            '0'))));

                            $two = $this->sma->formatDecimal($unit_price_show + (($row->item_tax) / $row->unit_quantity));
                            // $three = $this->sma->formatDecimal($two - ($row->discount_one/100)*$unit_price_show);
                            $four = $this->sma->formatDecimal($two - ($row->discount_two/100)*$unit_price_show);
                            // $five = $this->sma->formatDecimal($four - ($row->discount_three/100)*$unit_price_show);
                            $my_subtotal = $this->sma->formatDecimal($four * $row->unit_quantity);


                            // $total_discount_one += $this->sma->formatDecimal((($row->discount_one/100)*$unit_price_show) * $row->unit_quantity);
                            $total_discount_two += $this->sma->formatDecimal((($row->discount_two/100)*$unit_price_show) * $row->unit_quantity);
                            // $total_discount_three += $this->sma->formatDecimal((($row->discount_three/100)*$unit_price_show) * $row->unit_quantity);

                            $total_discount += $this->sma->formatDecimal(((($row->discount_two/100)*$unit_price_show)) * $row->unit_quantity);

                            $excluding_amount_price += $this->sma->formatDecimal(($unit_price_show * $row->unit_quantity));

                            $total_tax_amount += $this->sma->formatDecimal((($row->item_tax) / $row->unit_quantity )* $row->unit_quantity);

                            $net_amount += $this->sma->formatDecimal($my_subtotal);


                             $General_sales_tax_total += ($row->tax_type == 1) ? ($row->item_tax) : '';

                             $thired_schedule_tax_total += ($row->tax_type == 2) ? ($row->item_tax) : '';
                            
                             $total_sales_tax_amount += $this->sma->formatDecimal($General_sales_tax_total + $thired_schedule_tax_total);
                             
                             $further_tax = (($row->tax_type == 2) ? "" : (($row->item_tax * 3) / 100));

                             $total_further_tax += $further_tax ;

                            ?>
                            <tr>
                            <td style="text-align:center; width:20px; vertical-align:middle;"><?= $r; ?></td>
                            <td style="text-align:center; width:20px; vertical-align:middle;"><?= $row->product_code; ?></td>
                                <td style="vertical-align:middle; width:280px; ">
                                    <?= $row->company_code . '-'. $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                    <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                    <?= $row->details ? '<br>' . $row->details : ''; ?>
                                    <?= $row->serial_no ? '<br>' . $row->serial_no : ''; ?>
                                    <?= $row->hsn_code ? '<br>' . "HS-Code : ".$row->hsn_code : ''; ?>
                                    
                                </td>
                                <td style="width: 80px; text-align:center; vertical-align:middle; width:20px; "><?= $this->sma->formatQuantity($row->unit_quantity).' '.$row->product_unit_code; ?></td>
                                <td style="text-align:center; width:90px;vertical-align:middle; width:20px; "><?= $this->sma->formatMoney($row->unit_price); ?></td>
                                <td style="text-align:center; width:90px;vertical-align:middle; width:20px; "><?= $this->sma->formatMoney($row->mrp); ?></td>
                                <!-- <td style="text-align:center; width:90px;"><?= ($row->tax_type == 2) ? "3rd schedule" : $row->tax_name; ?></td> -->
                                <?php
                                    if ($Settings->tax1 && $inv->product_tax > 0) {
                                        echo '<td style="width: 90px; text-align:center; vertical-align:middle; width:80px; ">' . ($row->item_tax != 0 ? '(' . (($row->tax_type == 2) ? "3rd schedule" : $row->tax_name) . ')<br>' : '') . ($row->item_tax / $row->unit_quantity) . '</td>';
                                    }
                                    // if ( $Settings->product_discount && $inv->product_discount != 0) {
                                    //     echo '<td style="width: 90px; text-align:center; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                                    // }
                                    // echo '<td style="text-align:center; vertical-align:middle; width:60px; ">'.$this->sma->formatQuantity(($row->discount_one == "") ? "0" : $row->discount_one).'% <br> '.($row->discount_one/100)*$unit_price_show.'</td>';
                                    // echo '<td style="text-align:center; vertical-align:middle; width:60px; ">'.$this->sma->formatQuantity(($row->discount_two == "") ? "0" : $row->discount_two).'% <br> '.($row->discount_two/100)*$unit_price_show.'</td>';
                                    // echo '<td style="text-align:center; vertical-align:middle; width:60px; ">'.$this->sma->formatQuantity(($row->discount_three == "") ? "0" : $row->discount_three).'% <br> '.($row->discount_three/100)*$unit_price_show.'</td>';
                                ?>
                                <td style="vertical-align:middle; text-align:center; width:80px;"><?= $this->sma->formatMoney($my_subtotal); ?></td>
                            </tr>
                            <?php
                            $r++;
                        endforeach;
                        if ($return_rows) {
                            echo '<tr class="warning"><td colspan="'.($col+1).'" class="no-border"><strong>'.lang('returned_items').'</strong></td></tr>';
                            foreach ($return_rows as $row):
                            ?>
                                <tr class="warning">
                                    <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                                    <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                                    <td style="text-align:center; width:40px; vertical-align:middle;"><?= $row->product_code; ?></td>
                                    <td style="vertical-align:middle;">
                                        <?= $row->company_code . '-'. $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                        <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                        <?= $row->details ? '<br>' . $row->details : ''; ?>
                                        <?= $row->serial_no ? '<br>' . $row->serial_no : ''; ?>
                                    </td>
                                    <?php if ($Settings->indian_gst) { ?>
                                    <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                                    <?php } ?>
                                    <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->quantity).' '.$row->product_unit_code; ?></td>
                                    <td style="text-align:center; width:90px;"><?= $this->sma->formatMoney($row->unit_price); ?></td>
                                    <td style="text-align:center; width:90px;"><?= $this->sma->formatMoney($row->mrp); ?></td>
                                    <td style="text-align:center; width:90px;"><?= $this->sma->formatMoney($row->tax_type); ?></td>
                                    <?php
                                    if ($Settings->tax1 && $inv->product_tax > 0) {
                                        echo '<td style="text-align:center; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>('.($Settings->indian_gst ? $row->tax : $row->tax_code).')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                    }
                                    if ($Settings->product_discount && $inv->product_discount != 0) {
                                        echo '<td style="text-align:center; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                                    }
                                    ?>
                                    <td style="text-align:center; width:110px;"><?= $this->sma->formatMoney($row->subtotal); ?></td>
                                </tr>
                                <?php
                                $r++;
                            endforeach;
                        }
                    ?>
                    </tbody>
                    <tfoot>
                    </table>



                        <table style="width:100%;">
                        <tr>
                        <td width="30%">
                        
                        <table class="table" style="width:100%;">

                            <tr>
                                <td class="table-bordered" style="font-weight: bold; width:60%;">Amount Exculding S/Tax</td>
                                <td class="table-bordered" style="width:30%; text-align:center;"><?php echo $this->sma->formatMoney($excluding_amount_price); ?></td>
                            </tr>

                            <tr>
                                <td class="table-bordered" style="font-weight: bold; width:60%;">3rd Schedule</td>
                                <td class="table-bordered" style="width:30%; text-align:center;"><?php echo $this->sma->formatMoney($thired_schedule_tax_total); ?></td>
                            </tr>

                            <tr>
                                <td class="table-bordered" style="font-weight: bold; width:60%;">General Sales Tax Amount</td>
                                <td class="table-bordered" style="width:30%; text-align:center;"><?php echo $this->sma->formatMoney($General_sales_tax_total); ?></td>
                            </tr>

                            <tr>
                                <td class="table-bordered" style="font-weight: bold; width:60%;">Total Sales Tax Amount</td>
                                <td class="table-bordered" style="width:30%; text-align:center;"><?php echo $this->sma->formatMoney($total_sales_tax_amount); ?></td>
                            </tr>

                            <?php if(!$biller->gst_no) : ?>
                                <tr>
                                    <td class="table-bordered" style="font-weight: bold; width:60%;">Total Further Tax</td>
                                    <td class="table-bordered" style="width:30%; text-align:center;"><?php echo $this->sma->formatMoney($total_further_tax); ?></td>
                                </tr>
                            <?php endif; ?>

                            <tr>
                                <td class="table-bordered" style="font-weight: bold; width:60%;">Net Amount</td>
                                <td class="table-bordered" style="font-weight: bold; width:30%; text-align:center;"><?php echo $this->sma->formatMoney($net_amount); ?></td>
                            </tr>
                        </table>



                            </td>

                            
                            <td width="40%">
                            </td>

                            <td width="30%">
                            <table class="table" style="width:90%;">
                                <!-- <tr>
                                    <td class="table-bordered" style="font-weight: bold; width:20%;">Total Discount 1</td>
                                    <td class="table-bordered" style="width:20%; text-align:center;"><?php echo $this->sma->formatMoney($total_discount_one); ?></td>
                                </tr> -->


                                <tr>
                                    <td class="table-bordered" style="font-weight: bold; width:20%;">Total Discount 2</td>
                                    <td class="table-bordered" style="width:20%; text-align:center;"><?php echo $this->sma->formatMoney($total_discount_two); ?></td>
                                </tr>


                                <!-- <tr>
                                    <td class="table-bordered" style="font-weight: bold; width:20%;">Total Discount 3</td>
                                    <td class="table-bordered" style="width:20%; text-align:center;"><?php echo $this->sma->formatMoney($total_discount_three); ?></td>
                                </tr> -->

                                <tr>
                                    <td class="table-bordered" style="font-weight: bold; width:20%;">Total Discount</td>
                                    <td class="table-bordered" style="width:20%; text-align:center;"><?php echo $this->sma->formatMoney($total_discount); ?></td>
                                </tr>

                                <tr>
                                    <td class="" style="font-weight: bold; width:20%;"> &nbsp; </td>
                                    <td class="" style="width:20%; text-align:center;"> &nbsp;  </td>
                                </tr>

                                <tr>
                                    <td class="" style="font-weight: bold; width:20%;"> &nbsp; </td>
                                    <td class="" style="width:20%; text-align:center;"> &nbsp;  </td>
                                </tr>

                                <tr>
                                    <td class="" style="font-weight: bold; width:20%;"> &nbsp; </td>
                                    <td class="" style="width:20%; text-align:center;"> &nbsp;  </td>
                                </tr>

                            </table>
                            </td>
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
                                    echo '<td style="text-align:center;">' . $this->sma->formatMoney($return_sale ? ($inv->product_tax+$return_sale->product_tax) : $inv->product_tax) . '</td>';
                                }
                                if ( $Settings->product_discount && $inv->product_discount != 0) {
                                    echo '<td style="text-align:center;">' . $this->sma->formatMoney($return_sale ? ($inv->product_discount+$return_sale->product_discount) : $inv->product_discount) . '</td>';
                                }
                            ?>
                            <td style="text-align:center;"><?= $this->sma->formatMoney($return_sale ? (($inv->total + $inv->product_tax)+($return_sale->total + $return_sale->product_tax)) : ($inv->total + $inv->product_tax)); ?></td>
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
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('cgst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($cgst) : $cgst) . '</td></tr>';
                        }
                        if ($inv->sgst > 0) {
                            $sgst = $return_sale ? $inv->sgst + $return_sale->sgst : $inv->sgst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('sgst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($sgst) : $sgst) . '</td></tr>';
                        }
                        if ($inv->igst > 0) {
                            $igst = $return_sale ? $inv->igst + $return_sale->igst : $inv->igst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('igst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($igst) : $igst) . '</td></tr>';
                        }
                    } ?>

                    <?php if ($inv->order_discount != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:center;">' . lang('order_discount') . ' (' . $default_currency->code . ')</td><td style="text-align:center;">'.($inv->order_discount_id ? '<small>('.$inv->order_discount_id.')</small> ' : '') . $this->sma->formatMoney($return_sale ? ($inv->order_discount+$return_sale->order_discount) : $inv->order_discount) . '</td></tr>';
                    }
                    ?>
                    <?php if ($Settings->tax2 && $inv->order_tax != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:center;">' . lang('order_tax') . ' (' . $default_currency->code . ')</td><td style="text-align:center;">' . $this->sma->formatMoney($return_sale ? ($inv->order_tax+$return_sale->order_tax) : $inv->order_tax) . '</td></tr>';
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
                        <td style="text-align:center; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total+$return_sale->grand_total) : $inv->grand_total); ?></td>
                    </tr>

                    <tr>
                        <td colspan="<?= $col; ?>" style="text-align:center; font-weight:bold;"><?= lang('paid'); ?>
                            (<?= $default_currency->code; ?>)
                        </td>
                        <td style="text-align:center; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->paid+$return_sale->paid) : $inv->paid); ?></td>
                    </tr>
                    <tr>
                        <td colspan="<?= $col; ?>" style="text-align:center; font-weight:bold;"><?= lang('balance'); ?>
                            (<?= $default_currency->code; ?>)
                        </td>
                        <td style="text-align:center; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->grand_total+$return_sale->grand_total) : $inv->grand_total) - ($return_sale ? ($inv->paid+$return_sale->paid) : $inv->paid)); ?></td>
                    </tr>

                    </tfoot>
                </table> -->
            </div>
            <?= $Settings->invoice_view > 0 ? $this->gst->summary($rows, $return_rows, ($return_sale ? $inv->product_tax+$return_sale->product_tax : $inv->product_tax)) : ''; ?>
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