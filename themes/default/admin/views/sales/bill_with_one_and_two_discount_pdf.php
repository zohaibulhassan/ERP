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

<body>

<div id="wrap">
    <div class="row">
        <div class="col-lg-12">
            <?php if ($logo) {
                ?>
                <div class="text-center" style="margin-bottom:20px;">
                    <h1>Bill</h1>
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
                    <p class=""><?= $customer->company != '-' ? $customer->company : $customer->name; ?></p>
                    <?= $customer->company ? '' : 'Attn: ' . $customer->name; ?>
                    <?php
                        echo $customer->address . '<br />' . $customer->city . ' ' . $customer->postal_code . ' ' . $customer->state . '<br />' . $customer->country;
                        echo '<p>';
                        echo lang('tel') . ': ' . $customer->phone . '<br />' . lang('email') . ': ' . $customer->email;
                        if ($customer->vat_no != "-" && $customer->vat_no != "") {
                            echo "<br>" . lang("vat_no") . ": " . $customer->vat_no;
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
                    <p class=""><?= $own_company->companyname; ?></p>
                    <?= $own_company->registeraddress; ?><br>
                    <?php echo lang('Contact Person') . ': ' . $own_company->registerperson . '<br />' . lang('Mobile') . ': ' . $own_company->mobile; ?>
                    <?php
                        echo '<p>';
                        if ($own_company->ntn != "-" && $own_company->ntn != "") {
                            echo "<br>" . lang("NTN") . ": " . $own_company->ntn;
                        }
                        if ($own_company->strn != "-" && $own_company->strn != "") {
                            echo "<br>" . lang("STRN") . ": " . $own_company->strn;
                        }
                        if ($own_company->srb != "-" && $own_company->srb != "") {
                            echo "<br>" . lang("SRB") . ": " . $own_company->srb;
                        }
                        echo '</p>';
                    ?>
                </div>
                
                

                


                <div class="col-xs-4">
                <strong>Bill # : </strong> B-<?php echo $inv->reference_no; ?><br>
                <strong> <?= lang('date'); ?>: </strong>  <?= $this->sma->hrld($inv->date); ?><br>
                <strong>Delivery Address : </strong> <?php
                    if($inv->customer_address_id == 0){
                        echo $customer->address;
                    }
                    else{
                        echo $caddress['address'];
                    }
                ?>

                </div>


            </div>
            <div class="clearfix"></div>
            <div class="padding10">
                <div class="col-xs-5">
                    <div class="bold">
                        
                        <!-- <?= lang('ref'); ?>: <?= $inv->reference_no; ?><br> -->
                        <!-- <?= lang("payment_status"); ?>: <?= lang($inv->payment_status); ?>
                        <?php if ($inv->payment_status != 'paid') {
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
                $col = $Settings->indian_gst ? 6 : 5;
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
                        <th>Company Code - <?= lang('description'); ?></th>
                        <?php if ($Settings->indian_gst) { ?>
                            <th><?= lang("hsn_code"); ?></th>
                        <?php } ?>

                        <th>MRP <br> Per Piece</th>
                        <th><?= lang('unit_price'); ?> <br> Per Piece</th>
                        <?php
                            if ($Settings->tax1 && $inv->product_tax > 0) {
                                echo '<th> Tax  <br> Per Piece</th>';
                            }
                        ?>
                        <th>Advance Income Tax</th>
                        <?php
                            echo '<th>Discount 1 <br> Per Piece</th>';
                            echo '<th>Discount 2 <br> Per Piece</th>';
                        ?>
                        <th><?= lang('quantity'); ?></th>
                        <th><?= lang('subtotal'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $r = 1;
                        $total_adv_tax = 0;
                        $total_further_tax = 0;
                        $total_discount_one = 0;
                        $total_discount_two = 0;
                        $total_discount_three = 0;
                        $total_discount = 0;
                        $excluding_amount_price = 0;
                        $total_tax_amount = 0;
                        $net_amount = 0;


                        foreach ($rows as $row):

                            $unit_price_show = ($row->sma_companies_sales_type == "consignment" || $row->sma_companies_sales_type == "cost") ? ($row->unit_price) : 
                            (($row->sma_companies_sales_type == "dropship") ? ($row->dropship) : 
                            (($row->sma_companies_sales_type == "crossdock") ? ($row->crossdock) : 
                            (($row->sma_companies_sales_type == "services") ? ($row->services) : 
                            '0')));

                            $two = $unit_price_show + (($row->item_tax) / $row->quantity);
                            $three = $two - ($row->discount_one/100)*$unit_price_show;
                            $four = $three - ($row->discount_two/100)*$unit_price_show;

                            $further_tax = (($row->tax_type == 2) ? "" : (($unit_price_show * 3) / 100) * $row->unit_quantity);
                            $total_further_tax += $this->sma->formatDecimal($further_tax) ;


                            $total_adv_tax = $total_adv_tax+$row->adv_tax;
                            $my_subtotal = $four * $row->unit_quantity;
                            $my_subtotal = $my_subtotal+$row->adv_tax;


                            $total_discount_one += (($row->discount_one/100)*$unit_price_show) * $row->unit_quantity;
                            $total_discount_two += (($row->discount_two/100)*$unit_price_show) * $row->unit_quantity;
                            $total_discount_three += (($row->discount_three/100)*$unit_price_show) * $row->unit_quantity;

                            $total_discount += ((($row->discount_one/100)*$unit_price_show) + (($row->discount_two/100)*$unit_price_show) + (($row->discount_three/100)*$unit_price_show)) * $row->unit_quantity;

                            $excluding_amount_price += ($unit_price_show * $row->unit_quantity);

                            $total_tax_amount += (($row->item_tax) / $row->quantity )* $row->unit_quantity;

                            $net_amount += $my_subtotal ;

                            ?>
                            <tr>
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
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= round($row->mrp , 4) ; ?> </td>
                                <td style="text-align:center; width:90px;"><?= $this->sma->formatMoney($unit_price_show); ?></td>
                                <?php if ($Settings->tax1 && $inv->product_tax > 0) { ?>
                                <td style="text-align:center; width:90px;"><?= $this->sma->formatMoney(($row->item_tax) / $row->quantity); ?></td>
                                <?php } ?>
                                <td style="text-align:center; width:80px;"><?= $this->sma->formatMoney($row->adv_tax); ?></td>
                                <?php

                                        echo '<td style="text-align:center; vertical-align:middle;">'.$this->sma->formatQuantity(($row->discount_one == "") ? "0" : $row->discount_one).'% <br> '.($row->discount_one/100)*$unit_price_show.'</td>';
                                        echo '<td style="text-align:center; vertical-align:middle;">'.$this->sma->formatQuantity(($row->discount_two == "") ? "0" : $row->discount_two).'% <br> '.($row->discount_two/100)*$unit_price_show.'</td>';

                                        
                                            echo '<td style="width: 90px; text-align:center; vertical-align:middle;">' . $this->sma->formatQuantity($row->unit_quantity).' '.$row->product_unit_code   . '</td>';
                                ?>                                
                                <td style="vertical-align:middle; text-align:center; width:110px;"> <?= $this->sma->formatMoney($my_subtotal); ?></td>
                            </tr>
                            <?php
                            $r++;
                        endforeach;

                        if ($customer->gst_no == "") {
                            $net_amount = $this->sma->formatDecimal($net_amount + $total_further_tax);
                        } else {
                            $net_amount = $this->sma->formatDecimal($net_amount);
                        }

                        if ($inv->order_discount !== "0.0000") {
                            $net_amount = $this->sma->formatDecimal($net_amount - $inv->order_discount);
                        } 
                        
                            ?>
                    </tbody>
                    
                </table>

                <table class="table" style="width:100%;">

                    <tr>
                        <td style="font-weight: bold; width:60%; border:none"></td>
                        <td class="table-bordered" style="font-weight: bold; width:20%;">Amount Exculding S/Tax</td>
                        <td class="table-bordered" style="width:20%; text-align:right;"><?php echo $this->sma->formatMoney($excluding_amount_price); ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; width:60%; border:none"></td>
                        <td class="table-bordered" style="font-weight: bold; width:20%;">Total Discount One</td>
                        <td class="table-bordered" style="width:20%; text-align:right;"><?php echo $this->sma->formatMoney($total_discount_one); ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; width:60%; border:none"></td>
                        <td class="table-bordered" style="font-weight: bold; width:20%;">Total Discount Two</td>
                        <td class="table-bordered" style="width:20%; text-align:right;"><?php echo $this->sma->formatMoney($total_discount_two); ?></td>
                    </tr>


                    <tr>
                        <td style="font-weight: bold; width:60%; border:none"></td>
                        <td class="table-bordered" style="font-weight: bold; width:20%;">Sales Tax Amount</td>
                        <td class="table-bordered" style="width:20%; text-align:right;"><?php echo $this->sma->formatMoney($total_tax_amount); ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; width:60%; border:none"></td>
                        <td class="table-bordered" style="font-weight: bold; width:20%;">Total Advance Income Tax</td>
                        <td class="table-bordered" style="width:20%; text-align:right;"><?php echo $this->sma->formatMoney2($total_adv_tax); ?></td>
                    </tr>
                    <?php if ($customer->gst_no == "") { ?>
                    <tr>
                        <td style="font-weight: bold; width:60%; border:none"></td>
                        <td class="table-bordered" style="font-weight: bold; width:20%;">Total Further Tax</td>
                        <td class="table-bordered" style="width:20%; text-align:right;"><?php echo $this->sma->formatMoney($total_further_tax); ?></td>
                    </tr>
                    <?php } ?>

                    <?php if ($inv->order_discount !== "0.0000") { ?>
                        <tr>
                            <td style="font-weight: bold; width:60%; border:none"></td>
                            <td class="table-bordered" style="font-weight: bold; width:20%;">Extra Discount</td>
                            <td class="table-bordered" style="width:20%; text-align:right;"><?php echo $this->sma->formatMoney($inv->order_discount); ?></td>
                        </tr>
                    <?php } ?>

                    <tr>
                        <td style="font-weight: bold; width:60%; border:none"></td>
                        <td class="table-bordered" style="font-weight: bold; width:20%;">Net Amount</td>
                        <td class="table-bordered" style="font-weight: bold; width:20%; text-align:right;"><?php echo $this->sma->formatMoney($net_amount); ?></td>
                    </tr>
                </table>

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

<div style="padding-top: 50px;">
Stamp and signature is not required because it's system generated invoice.
</div>
<div>
Please lets us know within seven days if there is any issue in this invoice.
</div>
</body>
</html>