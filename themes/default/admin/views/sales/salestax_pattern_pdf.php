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

<!-- <pre><?php print_r($own_company); ?></pre>
<br><br><br>
<pre><?php print_r($inv); ?></pre>
<br><br><br>
<pre><?php print_r($rows); ?></pre> -->



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
                    <p class=""><?= $biller->company != '-' ? $biller->company : $biller->name; ?></p>
                    <?= $biller->company ? '' : 'Attn: ' . $biller->name; ?>
                    <?php
                        echo $biller->address . '<br />' . $biller->city . ' ' . $biller->postal_code . ' ' . $biller->state . '<br />' . $biller->country;
                        echo '<p>';
                        echo lang('tel') . ': ' . $biller->phone . '<br />' . lang('email') . ': ' . $biller->email;
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
                    <strong> <?= lang('date'); ?>: </strong>  <?= $this->sma->hrld($inv->date); ?>
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

                        foreach ($rows as $row):

                            $unit_price_show = ($row->sma_companies_sales_type == "consignment" || $row->sma_companies_sales_type == "cost") ? ($row->unit_price) : 
                            (($row->sma_companies_sales_type == "dropship") ? ($row->dropship) : 
                            (($row->sma_companies_sales_type == "crossdock") ? ($row->crossdock) : 
                            (($row->sma_companies_sales_type == "services") ? ($row->services) : 
                            '0')));

                            $two = $unit_price_show + (($row->item_tax) / $row->unit_quantity);
                            $three = $two - ($row->discount_one/100)*$unit_price_show;
                            $four = $three - ($row->discount_two/100)*$unit_price_show;
                            $my_subtotal = $four * $row->unit_quantity;

                            $total_discount += ((($row->discount_one/100)*$unit_price_show) + (($row->discount_two/100)*$unit_price_show)) * $row->unit_quantity;

                            $excluding_amount_price += ($unit_price_show * $row->unit_quantity);

                            $total_tax_amount += (($row->item_tax) / $row->unit_quantity);

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
                                <td style="text-align:center; width:90px;"><?= $this->sma->formatMoney(($row->item_tax) / $row->unit_quantity); ?></td>
                                <!-- <td style="text-align:right; width:90px;"><?= ($row->tax_type == 2) ? "3rd schedule" : $row->tax_name; ?></td> -->
                                <?php
                                    // if ( $Settings->product_discount && $inv->product_discount != 0) {
                                    //     echo '<td style="width: 90px; text-align:center; vertical-align:middle;"> ' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                                    // }

                                        echo '<td style="text-align:center; vertical-align:middle;">'.$this->sma->formatQuantity(($row->discount_one == "") ? "0" : $row->discount_one).'% <br> '.($row->discount_one/100)*$unit_price_show.'</td>';
                                        echo '<td style="text-align:center; vertical-align:middle;">'.$this->sma->formatQuantity(($row->discount_two == "") ? "0" : $row->discount_two).'% <br> '.($row->discount_two/100)*$unit_price_show.'</td>';

                                        if ($Settings->tax1 && $inv->product_tax > 0) {
                                            echo '<td style="width: 90px; text-align:center; vertical-align:middle;">' . $this->sma->formatQuantity($row->unit_quantity).' '.$row->product_unit_code  /* . ($row->item_tax != 0 ? '<small>(' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ')</small> ' : '') . $this->sma->formatMoney($row->item_tax) */ . '</td>';
                                        }
                                ?>                                
                                <td style="vertical-align:middle; text-align:center; width:110px;"> <?= $this->sma->formatMoney($my_subtotal); ?></td>
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
                                    <td style="text-align:right; width:90px;"><?= $this->sma->formatMoney($row->unit_price); ?></td>
                                    <td style="text-align:right; width:90px;"></td>
                                    <?php
                                    // if ($Settings->tax1 && $inv->product_tax > 0) {
                                    //     echo '<td style="text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>('.($Settings->indian_gst ? $row->tax : $row->tax_code).')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                    // }
                                    // if ($Settings->product_discount && $inv->product_discount != 0) {
                                    //     echo '<td style="text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                                    // }
                                    ?>
                                    <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->quantity).' '.$row->product_unit_code; ?></td>

                                    <td style="text-align:right; width:110px;"><?= $this->sma->formatMoney($row->subtotal); ?></td>
                                </tr>
                                <?php
                                $r++;
                            endforeach;
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
                        <td class="table-bordered" style="font-weight: bold; width:20%;">Total Discount</td>
                        <td class="table-bordered" style="width:20%; text-align:right;"><?php echo $this->sma->formatMoney($total_discount); ?></td>
                    </tr>

                    <tr>
                        <td style="font-weight: bold; width:60%; border:none"></td>
                        <td class="table-bordered" style="font-weight: bold; width:20%;">Sales Tax Amount</td>
                        <td class="table-bordered" style="width:20%; text-align:right;"><?php echo $this->sma->formatMoney($total_tax_amount); ?></td>
                    </tr>

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
</body>
</html>