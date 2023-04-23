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
<body style="position: relative;">
    <div id="wrap">
        <div class="row">
            <div class="col-lg-12">
                <?php 
                    if ($logo) {
                        // $path = base_url() . 'assets/uploads/logos/' . $customer->logo;
                        // $type = pathinfo($path, PATHINFO_EXTENSION);
                        // $data = file_get_contents($path);
                        // $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                ?>
                <div class="text-center" style="margin-bottom:20px;position: relative;">
                    <h1 style="font-size: 20px;font-weight: bolder;font-family: 'Arial' !important;">SALES TAX INVOICE</h1>
                    <h2 style="text-align: center;background: #d0d0d0;padding: 7px 17px;position: absolute;top: 0;right: 50px;">
                        <?php
                            if($invoicestatus == "original"){
                                echo 'Original';
                            }
                            else{
                                echo 'Duplicate';
                            }
                        ?>
                    </h2>
                </div>
                <?php 
                    }
                ?>
                <div class="clearfix"></div>
                <div class="padding10">
                    <?php if ($Settings->invoice_view == 1) { ?>
                    <div class="col-xs-12 text-center">
                        <h1><?= lang('tax_invoice'); ?></h1>
                    </div>
                    <?php } ?>
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
                        <strong><?php echo $this->lang->line("to"); ?>:</strong>
                        <?= $customer->company != '-' ? $customer->company : $customer->name; ?><br>
                        <?= $customer->company ? '' : 'Attn: ' . $customer->name; ?>
                        <?php
                            echo $customer->address;
                            echo '<p>';
                            echo lang('tel') . ': ' . $customer->phone . '<br />' . lang('email') . ': ' . $customer->email;
                            if ($customer->cf1 != "-" && $customer->cf1 != "") {
                                echo "<br>NTN Number: " . $customer->cf1;
                            }
                            if ($customer->gst_no != "-" && $customer->gst_no != "") {
                                echo "<br>" . lang("gst_no") . ": " . $customer->gst_no;
                            }
                            if ($customer->vat_no != "-" && $customer->vat_no != "") {
                                echo "<br>VAT Number: " . $customer->vat_no;
                            }
                            echo '</p>';
                        ?>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-xs-4">
                        <strong>Bill # : </strong> <?php echo $inv->reference_no; ?><br>
                        <strong> <?= lang('date'); ?>: </strong>  <?= date_format(date_create($inv->date),"Y-m-d"); ?><br>
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
                        <div class="bold"></div>
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
                                <th>Value for 3rd-Schd GST</th>
                                <th>Value Ex. Sales tax Rs.</th>
                                <th>MRP</th>
                                <th>Rate of S.Tax%</th>
                                <?php
                                if ($Settings->tax1 && $inv->product_tax > 0) {
                                    echo '<th>' . lang('tax') . '</th>';
                                }
                                ?>
                                <?php if ($customer->gst_no == "") { ?>
                                    <th>Further Tax</th>
                                <?php } ?>
                                <th>Tax Value</th>
                                <th>Advance Income Tax</th>
                                <th><?= lang('subtotal'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $r = 1;
                                $total_adv_tax = 0;
                                $excluding_amount_price = 0;
                                $total_discount_two = 0;
                                $total_discount_three = 0; 
                                $total_discount = 0;
                                $my_subtotal = 0;
                                $total_tax_amount = 0;
                                $net_amount = 0;
                                $total_further_tax= 0;
                                $total_sales_tax_amount = 0;
                                $thired_schedule_tax_total= 0;
                                $General_sales_tax_total = 0;
                                foreach ($rows as $row):
                                    $unit_price_show = ($row->sma_companies_sales_type == "consignment" || $row->sma_companies_sales_type == "cost") ? ($row->unit_price) : (($row->sma_companies_sales_type == "dropship") ? ($row->dropship) : (($row->sma_companies_sales_type == "crossdock") ? ($row->crossdock) : (($row->sma_companies_sales_type == "services") ? ($row->services) : '0')));
                                    $two = $this->sma->formatDecimal($unit_price_show + (($row->item_tax) / $row->quantity));
                                    $further_tax = (($row->tax_type == 2) ? "" : (($unit_price_show * 3) / 100) * $row->unit_quantity);
                                    $total_further_tax += $this->sma->formatDecimal($further_tax) ;
                                    $my_row_subtotal = $this->sma->formatDecimal(($two * $row->unit_quantity)+$row->adv_tax);
                                    $my_subtotal += $this->sma->formatDecimal($two * $row->unit_quantity);
                                    $total_discount_two += ( $this->sma->formatDecimal ($this->sma->formatDecimal($this->sma->formatDecimal($row->discount_two)/100) * $unit_price_show ) *  $row->unit_quantity );
                                    $total_discount_three += $this->sma->formatDecimal((($row->discount_three/100)*$unit_price_show) * $row->unit_quantity);
                                    $total_discount += $this->sma->formatDecimal(((($row->discount_two/100)*$unit_price_show) + (($row->discount_three/100)*$unit_price_show)) * $row->unit_quantity);
                                    $excluding_amount_price += $this->sma->formatDecimal(($unit_price_show * $row->unit_quantity));
                                    $total_tax_amount += $this->sma->formatDecimal((($row->item_tax) / $row->quantity )* $row->unit_quantity);
                                    $General_sales_tax = ($row->tax_type == "1") ? ($row->item_tax / $row->quantity*$row->unit_quantity) : 'a';
                                    $General_sales_tax_total += $this->sma->formatDecimal($General_sales_tax);
                                    $thired_schedule_tax = ($row->tax_type == "2") ? ($row->item_tax / $row->quantity*$row->unit_quantity) : 'b';
                                    $thired_schedule_tax_total += $this->sma->formatDecimal($thired_schedule_tax);
                                    $total_sales_tax_amount = $this->sma->formatDecimal ($General_sales_tax_total + $thired_schedule_tax_total);
                                    $total_adv_tax = $total_adv_tax+$row->adv_tax;
                                    if ($customer->gst_no == "") {
                                        $net_amount = $this->sma->formatDecimal($my_subtotal + $total_further_tax);
                                    } else {
                                        $net_amount = $this->sma->formatDecimal($my_subtotal);
                                    }
                            ?>
                            <tr>
                                <td style="text-align:center; width:15px; vertical-align:middle;"><?= $r; ?></td>
                                <td style="text-align:center; width:20px; vertical-align:middle;"><?= $row->product_code; ?></td>
                                <td style="vertical-align:middle; width:140px; ">
                                <?= $row->company_code . '-'. $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                <?= $row->details ? '<br>' . $row->details : ''; ?>
                                <?= $row->serial_no ? '<br>' . $row->serial_no : ''; ?>
                                <?= $row->hsn_code ? '<br>' . "HS-Code : ".$row->hsn_code : ''; ?>
                                </td>
                                <td style="width: 80px; text-align:center; vertical-align:middle; width:20px; "><?= $this->sma->formatQuantity2($row->unit_quantity).' '.$row->product_unit_code; ?></td>
                                <td style="text-align:center; width:90px;vertical-align:middle; width:20px; "><?= $this->sma->formatMoney2($row->unit_price); ?></td>

                                <td style="text-align:center; width:90px;vertical-align:middle; width:20px; ">
                                <?php 
                                    if($row->tax_type == 2 && $row->item_tax != 0){
                                        echo $this->sma->formatMoney2(($row->mrp/1.17)*$row->unit_quantity); 
                                    }
                                
                                ?></td>

                                <td style="text-align:center; width:90px;vertical-align:middle; width:20px; "><?= $this->sma->formatMoney2($row->unit_quantity*$row->unit_price); ?></td>
                                <td style="text-align:center; width:90px;vertical-align:middle; width:20px; "><?= $this->sma->formatMoney2($row->mrp); ?></td>
                                <td style="text-align:center; width:90px;vertical-align:middle; width:30px; ">
                                    <?php
                                        echo $aa =  $row->item_tax != 0 ?  (($row->tax_type == 2) ? "17.00%" : $this->sma->formatQuantityDecimal($row->tax_rate,2).'%')  : '0%'
                                    ?>
                                </td>
                                <?php
                                    $taxval = 0;
                                    if ($Settings->tax1 && $inv->product_tax > 0) {
                                        ?>
                                        <td style="width: 90px; text-align:center; vertical-align:middle; width:80px; " >
                                            <?php
                                                echo ($row->item_tax != 0 ? '(' . (($row->tax_type == 2) ? "3rd schedule" : $row->tax_name) . ')<br>' : '');
                                                echo $this->sma->formatMoney2(($row->item_tax / $row->quantity));
                                                $taxval = $row->item_tax / $row->quantity*$row->unit_quantity;

                                            ?>
                                        </td>
                                    <?php
                                    }
                                    if ($customer->gst_no == "") { ?>
                                        <td style="text-align:center; width:90px;vertical-align:middle; width:20px; "><?php echo $further_tax;?></td>
                                        
                                <?php
                                    }
                                ?>
                                <td style="vertical-align:middle; text-align:center; width:80px;"><?= $this->sma->formatMoney2($taxval); ?></td>
                                <td style="vertical-align:middle; text-align:center; width:80px;"><?= $this->sma->formatMoney2($row->adv_tax); ?></td>
                                <td style="vertical-align:middle; text-align:center; width:80px;"><?= $this->sma->formatMoney2($my_row_subtotal); ?></td>
                            </tr>
                            <?php
                                $r++;
                                endforeach;
                            ?>
                        </tbody>
                    </table>
                    <style>
                        /* DivTable.com */
                        .divTable{
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
                        .divTableCell, .divTableHead {
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
                            padding-left:0px;
                        }
                        .pr-0 {
                            padding-right:0px;
                        }
                        .bold {
                            font-weight:bold;
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
                                                <div class="divTableCell"><?php echo $this->sma->formatMoney2($excluding_amount_price); ?></div>
                                            </div>
                                            <div class="divTableRow">
                                                <div class="divTableCell bold">3rd Schedule</div>
                                                <div class="divTableCell"><?php echo $this->sma->formatMoney2($thired_schedule_tax_total); ?></div>
                                            </div>
                                            <div class="divTableRow">
                                                <div class="divTableCell bold">General Sales Tax Amount</div>
                                                <div class="divTableCell"><?php echo $this->sma->formatMoney2($General_sales_tax_total); ?></div>
                                            </div>
                                            <div class="divTableRow">
                                                <div class="divTableCell bold">Total Sales Tax Amount</div>
                                                <div class="divTableCell"><?php echo $this->sma->formatMoney2($total_sales_tax_amount); ?></div>
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
                                </div>
                                <div class="divTableCell pl-0 pr-0">
                                    <div class="divTable">
                                        <div class="divTableBody">
                                            <?php if ($customer->gst_no == "") { ?>
                                                <div class="divTableRow">
                                                    <div class="divTableCell bold">Total Further Tax</div>
                                                    <div class="divTableCell"><?php echo $this->sma->formatMoney2($total_further_tax); ?></div>
                                                </div>
                                            <?php } ?>
                                            <div class="divTableRow">
                                                <div class="divTableCell bold">Net Amount</div>
                                                <div class="divTableCell"><?php echo $this->sma->formatMoney2($net_amount+$total_adv_tax); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- DivTable.com -->
                                </div>
                            </div>
                        </div>
                    </div>   
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
                <?php } ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    
</body>
</html>