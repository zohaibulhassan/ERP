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
                        <?php
                            if($invoicestatus == "original"){
                                ?>
                                <h1 style="font-size: 20px;font-weight: bolder;font-family: 'Arial' !important;">SALES TAX INVOICE</h1>
                                <?php
                            }
                            else{
                                ?>
                                    <h1 style="font-size: 20px;font-weight: bolder;font-family: 'Arial' !important;">PROFORMA INVOICE</h1>
                                <?php
                            }
                        ?>
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
                                    <th><?= lang('quantity'); ?></th>
                                    <th><?= lang('unit_price'); ?></th>
                                    <th>MRP</th>
                                    <?php
                                    if ($Settings->tax1 && $inv->product_tax > 0) {
                                        echo '<th>' . lang('tax') . '</th>';
                                    }
                                    ?>
                                    <?php if ($customer->gst_no == "") { ?>
                                        <th>Further Tax</th>
                                    <?php } ?>
                                    <th>Advance Income Tax</th>
                                    <th><?= lang('subtotal'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sno=0;
                                    $excluding_amount_price = 0;
                                    $thired_schedule_tax_total = 0;
                                    $General_sales_tax_total = 0;
                                    $total_d1 = 0;
                                    $total_d2 = 0;
                                    $total_d3 = 0;
                                    $income_tax = 0;
                                    $total_further_tax = 0;
                                    foreach($rows as $row){
                                        $sno++;
                                        $selling_price = ($row->sma_companies_sales_type == "consignment" || $row->sma_companies_sales_type == "cost") ? ($row->unit_price) : (($row->sma_companies_sales_type == "dropship") ? ($row->dropship) : (($row->sma_companies_sales_type == "crossdock") ? ($row->crossdock) : (($row->sma_companies_sales_type == "services") ? ($row->services) : '0')));
                                        $further_tax = 0;
                                        if ($customer->gst_no == "") {
                                            if($row->tax_type == 2){
                                                $further_tax = 0;
                                            }
                                            else{
                                                $further_tax = (($selling_price/ 100)*3) * $row->unit_quantity;
                                            }
                                        }
                                        if($row->tax_type == 2){
                                            $thired_schedule_tax_total += $row->item_tax;
                                        }
                                        else{
                                            $General_sales_tax_total += $row->item_tax;
                                        }
                                        $excluding_amount_price += $selling_price*$row->unit_quantity;
                                        $income_tax += $row->adv_tax;
                                        $total_further_tax += $further_tax;
                                        $total_d1 += (($selling_price/ 100)*$row->discount_one)*$row->unit_quantity;
                                        $total_d2 += (($selling_price/ 100)*$row->discount_two)*$row->unit_quantity;
                                        $total_d3 += (($selling_price/ 100)*$row->discount_three)*$row->unit_quantity;
                                        $total = ($selling_price*$row->unit_quantity)+$row->item_tax+$further_tax+$row->adv_tax;

                                ?>
                                <tr>
                                    <td style="text-align:center; width:20px; vertical-align:middle;"><?= $sno; ?></td>
                                    <td style="text-align:center; width:20px; vertical-align:middle;"><?= $row->product_code; ?></td>
                                    <td  style="vertical-align:middle; width:210px; ">
                                        <?= $row->company_code . '-' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                        <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                        <?= $row->details ? '<br>' . $row->details : ''; ?>
                                        <?= $row->serial_no ? '<br>' . $row->serial_no : ''; ?>
                                        <?= $row->hsn_code ? '<br>' . "HS-Code : " . $row->hsn_code : ''; ?>

                                    </td>
                                    <td style="width: 80px; text-align:center; vertical-align:middle; width:20px; "><?= $this->sma->formatQuantity($row->unit_quantity) . ' ' . $row->product_unit_code; ?></td>
                                    <td style="text-align:center; width:90px;vertical-align:middle; width:20px; "><?= $this->sma->formatMoney($selling_price); ?></td>
                                    <td style="text-align:center; width:90px;vertical-align:middle; width:20px; "><?= $this->sma->formatMoney($row->mrp); ?></td>
                                    <?php
                                        if ($Settings->tax1 && $inv->product_tax > 0) {
                                            echo '<td style="width: 90px; text-align:center; vertical-align:middle; width:80px; ">' . ($row->item_tax != 0 ? '(' . (($row->tax_type == 2) ? "3rd schedule" : $row->tax_name) . ')<br>' : '') . $this->sma->formatMoney(($row->item_tax / $row->quantity)) . '</td>';
                                        }
                                        ?>
                                    <?php if ($customer->gst_no == "") { ?>
                                        <td style="text-align:center; width:90px;vertical-align:middle; width:20px; "><?php echo $this->sma->formatMoney($further_tax); ?></td>
                                    <?php } ?>
                                    <td style="text-align:center; width:90px;vertical-align:middle; width:20px; "><?php echo $this->sma->formatMoney($row->adv_tax); ?></td>
                                    <td style="vertical-align:middle; text-align:center; width:80px;"><?php echo $this->sma->formatMoney($total); ?></td>
                                </tr>
                                
                                <?php
                                    }
                                
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
                                                    <div class="divTableCell"><?php echo $this->sma->formatMoney($thired_schedule_tax_total+$General_sales_tax_total); ?></div>
                                                </div>
                                                <div class="divTableRow">
                                                    <div class="divTableCell bold">Total Advance Income Tax</div>
                                                    <div class="divTableCell"><?php echo $this->sma->formatMoney($income_tax); ?></div>
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
                                                    <div class="divTableCell"><?php echo $this->sma->formatMoney($total_d1); ?></div>
                                                </div>
                                                <div class="divTableRow">
                                                    <div class="divTableCell bold">Total Discount 2 </div>
                                                    <div class="divTableCell"><?php echo $this->sma->formatMoney($total_d2); ?></div>
                                                </div>
                                                <div class="divTableRow">
                                                    <div class="divTableCell bold">Total Discount 3</div>
                                                    <div class="divTableCell"><?php echo $this->sma->formatMoney($total_d3); ?></div>
                                                </div>
                                                <div class="divTableRow">
                                                    <div class="divTableCell bold">Total Discount</div>
                                                    <div class="divTableCell"><?php echo $this->sma->formatMoney($total_d1+$total_d2+$total_d3); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- DivTable.com -->
                                    </div>
                                    <div class="divTableCell pl-0 pr-0">



                                        <div class="divTable">
                                            <div class="divTableBody">

                                                <?php if ($customer->gst_no == "") { ?>
                                                    <div class="divTableRow">
                                                        <div class="divTableCell bold">Total Further Tax</div>
                                                        <div class="divTableCell"><?php echo $this->sma->formatMoney($total_further_tax); ?></div>
                                                    </div>
                                                <?php } ?>
                                                <div class="divTableRow">
                                                    <div class="divTableCell bold">Net Amount</div>
                                                    <div class="divTableCell"><?php echo $this->sma->formatMoney($excluding_amount_price+($thired_schedule_tax_total+$General_sales_tax_total)+$income_tax+$total_further_tax-($total_d1+$total_d2+$total_d3)); ?></div>
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