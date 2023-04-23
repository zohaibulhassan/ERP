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
                        <h1>Delivery Challan</h1>
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
                            echo "<br>" . lang("NTN #") . ": " . $customer->vat_no;
                        }
                        if ($customer->gst_no != "-" && $customer->gst_no != "") {
                            echo "<br>" . lang("gst_no") . ": " . $customer->gst_no;
                        }
                        echo '</p>';
                        ?>
                        <div class="clearfix"></div>
                    </div>

                    <div class="col-xs-4">
                        <strong><?php echo $this->lang->line("from"); ?>:</strong>
                        <p class=""><?= $own_company->companyname; ?></p>
                        <?= $own_company->registeraddress; ?><br>
                        <?php echo lang('Contact Person') . ': ' . $own_company->registerperson . '<br />' . lang('Mobile') . ': ' . $own_company->mobile;  ?>
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
                        <strong>Delivery Challan # : </strong> DC-<?php echo $inv->reference_no; ?><br>
                        <strong> <?= lang('date'); ?>: </strong> <?= $this->sma->hrld($inv->date); ?><br>
                        <strong> <?= lang('P.O #'); ?>: </strong> <?php echo $inv->po_number; ?><br>
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

                            <!-- <?= lang('ref'); ?>: <?= $inv->reference_no; ?><br> -->
                            <!-- <?= lang("payment_status"); ?>: <?= lang($inv->payment_status); ?>
                        <?php if ($inv->payment_status != 'paid') {
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
                $col = $Settings->indian_gst ? 6 : 5;
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
                        <table class="table table-bordered table-hover table-striped" >
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width:15pt" ><?= lang('no'); ?></th>
                                    <th rowspan="2" style="width:30pt" >SKU</th>
                                    <th rowspan="2" style="width:200pt" >Company Code - <?= lang('description'); ?></th>
                                    <?php if ($Settings->indian_gst) { ?>
                                        <th rowspan="2" ><?= lang("hsn_code"); ?></th>
                                    <?php } ?>
                                    <th rowspan="2" style="width:38pt" >MRP <br> Per Piece</th>
                                    <th rowspan="2" style="width:30pt" >Pack Size</th>
                                    <th rowspan="2" style="width:30pt" >Carton Size</th>
                                    <th rowspan="2" style="width:68pt" ><?= lang('Batch'); ?> </th>
                                    <th rowspan="2" style="width:45pt" ><?= lang('Expiry'); ?></th>
                                    <th rowspan="2" style="width:30pt" ><?= lang('Quantity In PCS'); ?></th>
                                    <th rowspan="2" style="width:45pt" >Total Weight</th>
                                    <th colspan="2" style="width:60pt" ><?= lang('quantity'); ?></th>
                                </tr>
                                <tr>
                                    <th style="width:30pt" >Carton</th>
                                    <th style="width:30pt" >Loose</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $r = 1;
                                $total_qty = 0;
                                $total_carton_qty = 0;
                                $total_loose_qty = 0;
                                $total_weight = 0;
                                $excluding_amount_price= 0;
                                $total_tax_amount = 0;
                                $net_amount = 0;
                                foreach ($rows as $row) :

                                    $unit_price_show = ($row->sma_companies_sales_type == "consignment" || $row->sma_companies_sales_type == "cost") ? ($row->unit_price) : (($row->sma_companies_sales_type == "dropship") ? ($row->dropship) : (($row->sma_companies_sales_type == "crossdock") ? ($row->crossdock) : (($row->sma_companies_sales_type == "services") ? ($row->services) :
                                        '0')));
                                    $two = $unit_price_show + (($row->item_tax) / $row->unit_quantity);
                                    $my_subtotal = $two * $row->unit_quantity;
                                    $excluding_amount_price += ($unit_price_show * $row->unit_quantity);
                                    $total_tax_amount += (($row->item_tax) / $row->unit_quantity) * $row->unit_quantity;
                                    $net_amount += $my_subtotal;
                                ?>
                                    <tr>
                                        <td style="text-align:center; vertical-align:middle;"><?= $r; ?></td>
                                        <td style="text-align:center;vertical-align:middle;"><?= $row->product_code; ?> </td>
                                        <td style="vertical-align:middle;">
                                            <?= $row->company_code . '-' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                            <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                            <?= $row->details ? '<br>' . $row->details : ''; ?>
                                            <?= $row->serial_no ? '<br>' . $row->serial_no : ''; ?>
                                        </td>
                                        <?php if ($Settings->indian_gst) { ?>
                                            <td style=" text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                                        <?php } ?>
                                        <td style="text-align:center; vertical-align:middle;"><?= decimalallow($row->mrp,0); ?> </td>
                                        <td style="text-align:center; vertical-align:middle;"><?= $row->pack_size; ?> </td>
                                        <td style="text-align:center; vertical-align:middle;"> <?= $row->carton_size; ?> </td>
                                        <td style="text-align:center;"><?= $row->sma_purchase_items_batch; ?></td>
                                        <td style="text-align:center;"><?= $row->selected_expiry; ?></td>
                                        <td style="text-align:center;"><?php echo decimalallow($row->unit_quantity,0); ?></td>
                                        <?php
                                            $total_qty += $row->unit_quantity;
                                            $carton_qty=$row->unit_quantity/$row->carton_size;
                                            $carton_qty = (int)$carton_qty;
                                            $loss_qty=$row->unit_quantity-($carton_qty*$row->carton_size);
                                            $weight = $row->weight*$row->unit_quantity;
                                            $total_carton_qty += $carton_qty;
                                            $total_loose_qty += $loss_qty;
                                            $total_weight += $weight;
                                        ?>
                                        <td style="text-align:center; vertical-align:middle;"> <?= $weight; ?> </td>
                                        <td style="text-align:center;"><?php echo $carton_qty; ?></td>
                                        <td style="text-align:center;"><?php echo $loss_qty; ?></td>
                                    </tr>
                                    <?php
                                    $r++;
                                endforeach;
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="8" style="text-align:right; ">Total</th>
                                    <th><?= $total_qty ?></th>
                                    <th><?= $total_weight ?></th>
                                    <th><?= $total_carton_qty ?></th>
                                    <th><?= $total_loose_qty ?></th>
                                </tr>
                            </tfoot>

                        </table>
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
    <div style="padding-top: 50px;">
        Stamp and signature is not required because it's system generated invoice.
    </div>
    <div>
        Please lets us know within seven days if there is any issue in this invoice.
    </div>
</body>

</html>