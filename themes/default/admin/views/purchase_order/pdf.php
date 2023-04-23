<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->lang->line("purchase") . " " . $detail->reference_no; ?></title>
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
                        <strong><?= $this->lang->line("to"); ?></strong>
                        <h6 class=""><?= $detail->supplier->company && $detail->supplier->company != '-' ? $detail->supplier->company : $detail->supplier->name; ?></h6>
                        <?= $detail->supplier->company && $detail->supplier->company != '-' ? "" : "Attn: " . $detail->supplier->name; ?>
                        <?php
                        echo $detail->supplier->address . "<br />" . $detail->supplier->city . " " . $detail->supplier->postal_code . " " . $detail->supplier->state . "<br />" . $detail->supplier->country . "<br>";
                        if ($detail->supplier->cf1 != "-" && $detail->supplier->cf1 != "") {
                            echo "<strong> NTN : </strong>" . $detail->supplier->cf1 . "<br>";
                        }
                        if ($detail->supplier->gst_no != "-" && $detail->supplier->gst_no != "") {
                            echo "<strong> GST : </strong>" . $detail->supplier->gst_no;
                        }
                        echo "<p>";
                        if ($detail->supplier->vat_no != "-" && $detail->supplier->vat_no != "") {
                            echo "<br>" . lang("vat_no") . ": " . $detail->supplier->vat_no;
                        }

                        if ($detail->supplier->cf2 != "-" && $detail->supplier->cf2 != "") {
                            echo "<br>" . lang("scf2") . ": " . $detail->supplier->cf2;
                        }
                        if ($detail->supplier->cf3 != "-" && $detail->supplier->cf3 != "") {
                            echo "<br>" . lang("scf3") . ": " . $detail->supplier->cf3;
                        }
                        if ($detail->supplier->cf4 != "-" && $detail->supplier->cf4 != "") {
                            echo "<br>" . lang("scf4") . ": " . $detail->supplier->cf4;
                        }
                        if ($detail->supplier->cf5 != "-" && $detail->supplier->cf5 != "") {
                            echo "<br>" . lang("scf5") . ": " . $detail->supplier->cf5;
                        }
                        if ($detail->supplier->cf6 != "-" && $detail->supplier->cf6 != "") {
                            echo "<br>" . lang("scf6") . ": " . $detail->supplier->cf6;
                        }
                        echo "</p>";
                        echo "<strong> Phone : </strong>" . $detail->supplier->phone . "<br />";
                        echo "<strong> Email : </strong>" . $detail->supplier->email;
                        ?>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-xs-4">
                        <strong><?= $this->lang->line("from"); ?></strong>
                        <?php
                        if ($detail->own_company_detail->companyname != "-" && $detail->own_company_detail->companyname != "") {
                            echo "<h6>" . $detail->own_company_detail->companyname . "</h6>";
                        }

                        if ($detail->own_company_detail->registeraddress != "-" && $detail->own_company_detail->registeraddress != "") {
                            echo "<strong> Register Address : </strong>" . $detail->own_company_detail->registeraddress . "<br>";
                        }

                        if ($detail->own_company_detail->warehouseaddress != "-" && $detail->own_company_detail->warehouseaddress != "") {
                            echo "<strong> Company Address : </strong>" . $detail->own_company_detail->warehouseaddress . "<br>";
                        }

                        if ($detail->own_company_detail->ntn != "-" && $detail->own_company_detail->ntn != "") {
                            echo "<strong> NTN : </strong>" . $detail->own_company_detail->ntn . "<br>";
                        }

                        if ($detail->own_company_detail->strn != "-" && $detail->own_company_detail->strn != "") {
                            echo "<strong> STRN : </strong>" . $detail->own_company_detail->strn . "<br>";
                        }

                        if ($detail->own_company_detail->srb != "-" && $detail->own_company_detail->srb != "") {
                            echo "<strong> SRB : </strong>" . $detail->own_company_detail->srb . "<br>";
                        }

                        if ($detail->own_company_detail->mobile != "-" && $detail->own_company_detail->mobile != "") {
                            echo "<strong> Phone : </strong>" . $detail->own_company_detail->mobile . "<br>";
                        }
                        ?>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-xs-4">
                        <span style="padding-left:10px; padding-bottom:20px; display: block;">
                            <strong><?= lang("Reference"); ?>:</strong> <?= $detail->reference_no; ?><br>
                            <strong><?= lang("Receiving Date"); ?>:</strong> <?= $this->sma->hrld($detail->receiving_date); ?><br>
                            <strong><?= lang("Received Date"); ?>:</strong> <?= $this->sma->hrld($detail->received_date); ?><br>
                            <strong><?= lang("Close Date"); ?>:</strong> <?= $this->sma->hrld($detail->close_date); ?><br>
                            <strong><?= lang("status"); ?>:</strong> <?= $this->sma->hrld($detail->status); ?><br>
                            <strong><?= lang("payment_status"); ?>:</strong> <?= $this->sma->hrld($detail->payment_status); ?><br>
                        </span>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div style="margin-top: 45px; padding-top: 45px;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr class="active" style="font-weight: bold;">
                                    <th>No.</th>
                                    <th>P.ID.</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Received Qty</th>
                                    <th>Unreceived Qty</th>
                                    <th>Complete Percentage</th>
                                    <th>Unit Cost</th>
                                    <th>Discount</th>
                                    <th>Tax</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 0;
                                foreach($detail->items as $item){
                                    $no++;
                                ?>
                                <tr>
                                    <td  style="text-align:center; width:40px; vertical-align:middle;"><?php echo $no; ?></td>
                                    <td><?php echo $item->product_id; ?></td>
                                    <td><?php echo $item->product_name; ?></td>
                                    <td><?php echo $item->qty.' '.$item->unit; ?></td>
                                    <td><?php echo $item->count_receving.' '.$item->unit; ?></td>
                                    <td><?php echo $item->qty-$item->count_receving.' '.$item->unit; ?></td>
                                    <td><?=  ($item->count_receving/$item->qty)*100 ?>%</td>
                                    <td><?php echo $this->sma->formatMoney($item->purchase_price); ?></td>
                                    <td style="color:#dc0b0b" ><?php echo $this->sma->formatMoney(' -'.$item->total_discount*$item->qty); ?></td>
                                    <td><?php echo $this->sma->formatMoney($item->total_tax*$item->qty); ?></td>
                                    <td><?php echo $this->sma->formatMoney($item->sub_total*$item->qty); ?></td>
                                </tr>

                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-8"></div>
                        <div class="col-sm-2 font-weight-bold">Total :</div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php echo $this->sma->formatMoney($detail->total); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-sm-8"></div>
                        <div class="col-sm-2 font-weight-bold">Order Discount :</div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php echo $this->sma->formatMoney($detail->order_discount); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-sm-8"></div>
                        <div class="col-sm-2 font-weight-bold">Order Tax :</div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php echo $this->sma->formatMoney($detail->order_tax); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-sm-8"></div>
                        <div class="col-sm-2 font-weight-bold">Shipping :</div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php echo $this->sma->formatMoney($detail->shipping); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-sm-8"></div>
                        <div class="col-sm-2 font-weight-bold">Grand Total :</div>
                        <div class="col-sm-2 font-weight-bold">
                            <?php echo $this->sma->formatMoney($detail->grand_total); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <p>* This is a computer generated slip and does not require signature</p>
                <div class="row">
                    <div class="col-xs-7 pull-left">
                        <?php if ($detail->note || $detail->note != "") { ?>
                            <div class="well well-sm">
                                <p class="bold"><?= lang("note"); ?>:</p>
                                <div><?= $this->sma->decode_html($detail->note); ?></div>
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
