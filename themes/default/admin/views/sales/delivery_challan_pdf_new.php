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
            <div class="col-xs-12">
                <div class="text-center" style="margin-bottom:20px;">
                    <h1>Delivery Challan</h1>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <strong><?php echo $this->lang->line("to"); ?>:</strong>
                <p class=""><?= $inv->customer_company != '-' ? $inv->customer_company : $inv->customer_name; ?></p>
                <?= $inv->customer_company ? '' : 'Attn: ' . $inv->customer_name; ?>
                <?php
                    echo $inv->customer_address . '<br />' . $inv->customer_city . ' ' . $inv->customer_postal_code . ' ' . $inv->customer_state . '<br />' . $inv->customer_country;
                    echo '<p>';
                    echo lang('tel') . ': ' . $inv->customer_phone . '<br />' . lang('email') . ': ' . $inv->customer_email;
                    if ($inv->customer_vat_no != "-" && $inv->customer_vat_no != "") {
                        echo "<br>" . lang("NTN #") . ": " . $inv->customer_vat_no;
                    }
                    if ($inv->cutomer_gst != "-" && $inv->cutomer_gst != "") {
                        echo "<br>" . lang("gst_no") . ": " . $inv->cutomer_gst;
                    }
                    echo '</p>';
                ?>
            </div>
            <div class="col-xs-4">
                <strong><?php echo $this->lang->line("from"); ?>:</strong>
                <p class=""><?= $inv->oc_companyname; ?></p>
                <?= $inv->oc_registeraddress; ?><br>
                <?php echo lang('Contact Person') . ': ' . $inv->oc_regesteperson . '<br />' . lang('Mobile') . ': ' . $inv->oc_mobile;  ?>
                <?php
                    echo '<p>';
                    if ($inv->oc_ntn != "-" && $inv->oc_ntn != "") {
                        echo "<br>" . lang("NTN") . ": " . $inv->oc_ntn;
                    }
                    if ($inv->oc_strn != "-" && $inv->oc_strn != "") {
                        echo "<br>" . lang("STRN") . ": " . $inv->oc_strn;
                    }
                    if ($inv->oc_srb != "-" && $inv->oc_srb != "") {
                        echo "<br>" . lang("SRB") . ": " . $inv->oc_srb;
                    }
                    echo '</p>';
                ?>
            </div>
            <div class="col-xs-4">
                <strong>Delivery Challan # : </strong> DC-<?php echo $inv->reference_no; ?><br>
                <strong> <?= lang('date'); ?>: </strong> <?= $this->sma->hrld($inv->date); ?><br>
                <strong> <?= lang('P.O #'); ?>: </strong> <?php echo $inv->po_number; ?><br>
                <p style="width:70%" >
                    <strong>Delivery Address : </strong>
                    <?php
                        if($inv->customer_address_id == 0){
                            echo $inv->customer_address;
                        }
                        else{
                            echo $caddress['address'];
                        }
                    ?>
                </p>
            </div>



        </div>
        <div class="row">
            <div class="col-xs-12">
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
                            foreach ($rows as $row) :
                            ?>
                                <tr>
                                    <td style="text-align:center; vertical-align:middle;"><?= $r; ?></td>
                                    <td style="text-align:center;vertical-align:middle;"><?= $row->sku; ?> </td>
                                    <td style="vertical-align:middle;">
                                        <?= $row->company_code . '-' . $row->product_name; ?>
                                        <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                        <?= $row->details ? '<br>' . $row->details : ''; ?>
                                    </td>
                                    <?php if ($Settings->indian_gst) { ?>
                                        <td style=" text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                                    <?php } ?>
                                    <td style="text-align:center; vertical-align:middle;"><?= decimalallow($row->mrp,0); ?> </td>
                                    <td style="text-align:center; vertical-align:middle;"><?= $row->pack_size; ?> </td>
                                    <td style="text-align:center; vertical-align:middle;"> <?= $row->carton_size; ?> </td>
                                    <td style="text-align:center;"><?= $row->batch; ?></td>
                                    <td style="text-align:center;"><?= $row->expiry; ?></td>
                                    <td style="text-align:center;"><?php echo decimalallow($row->quantity,0); ?></td>
                                    <?php
                                        $total_qty += $row->quantity;
                                        $carton_qty=$row->quantity/$row->carton_size;
                                        $carton_qty = (int)$carton_qty;
                                        $loss_qty=$row->quantity-($carton_qty*$row->carton_size);
                                        $weight = $row->weight*$row->quantity;
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