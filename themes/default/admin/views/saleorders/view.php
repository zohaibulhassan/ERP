<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD ?>

<style>
    .dt-buttons {
        float:right !important;
    }
</style>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-file"></i><?= 'Sale Order. ' . $detail->id; ?></h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <?php if($detail->status == "partial" || $detail->status == "pending"){ ?>
                            <?php if ($Owner || $Admin || $GP['so_add_new_item']) { ?>
                                <li><a href="#addItemSOBtn"  data-toggle="modal" data-target="#addItemSOBtn"><i class="fa fa-cart-plus"></i> Add Item</a></li>
                            <?php } ?>
                            <?php if ($Owner || $Admin || $GP['so_create_invoice']) { ?>
                                <li><a href="<?= admin_url('salesorders/create/') ?><?php echo $detail->id; ?>"><i class="fa fa-barcode"></i> Create Invoice</a></li>
                            <?php } ?>
                            <?php if ($Owner || $Admin || $GP['so_edit_info']) { ?>
                                <li><a href="#editPOModel" id="editPOBtn" data-toggle="modal" data-target="#editPOModel"><i class="fa fa-edit"></i> Edit SO Detail</a></li>
                            <?php } ?>
                            <?php if (($Owner || $Admin || $GP['so_delete']) && count($detail->cso) == 0) { ?>
                                <li><a  id="deleteSOBtn" style="cursor: pointer;" ><i class="fa fa-trash"></i> Delete Sale Order</a></li>
                            <?php } ?>
                            <?php if (($Owner || $Admin || $GP['so_cancel']) && count($detail->cso) == 0 && count($detail->citems) == 0) { ?>
                                <li><a  id="cancelSOBtn" style="cursor: pointer;" ><i class="fa fa-close"></i> Cancel Sale Order</a></li>
                            <?php } ?>
                            <?php if (($Owner || $Admin || $GP['so_cancel']) && $detail->status == "partial" && count($detail->citems) == 0) { ?>
                                <li><a  id="closeSOBtn" style="cursor: pointer;" ><i class="fa fa-ban"></i> Close Sale Order</a></li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="clearfix"></div>
                <div class="print-only col-xs-12">
                    <img src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>"
                         alt="<?= $Settings->site_name; ?>">
                </div>
                <div class="well well-sm">

                    <div class="col-xs-4 border-right">
                        <div class="col-xs-2"><i class="fa fa-3x fa-building padding010 text-muted"></i></div>
                        <div class="col-xs-10">
                            <h2 class=""><?= $detail->customer->company && $detail->customer->company != '-' ? $detail->customer->company : $detail->customer->company; ?></h2>
                            <?= $detail->customer->company && $detail->customer->company != '-' ? "" : "Attn: " . $detail->customer->name ?>

                            <?php
                            echo $detail->customer->address . "<br />" . $detail->customer->city . " " . $detail->customer->postal_code . " " . $detail->customer->state . "<br />" . $detail->customer->country;

                            echo "<p>";

                            if ($detail->customer->vat_no != "-" && $detail->customer->vat_no != "") {
                                echo "<br>" . lang("vat_no") . ": " . $detail->customer->vat_no;
                            }
                            if ($detail->customer->gst_no != "-" && $detail->customer->gst_no != "") {
                                echo "<br>" . lang("gst_no") . ": " . $detail->customer->gst_no;
                            }
                            if ($detail->customer->cf1 != "-" && $detail->customer->cf1 != "") {
                                echo "<br>" . lang("ccf1") . ": " . $detail->customer->cf1;
                            }
                            if ($detail->customer->cf2 != "-" && $detail->customer->cf2 != "") {
                                echo "<br>" . lang("ccf2") . ": " . $customer->cf2;
                            }
                            if ($detail->customer->cf3 != "-" && $detail->customer->cf3 != "") {
                                echo "<br>" . lang("ccf3") . ": " . $detail->customer->cf3;
                            }
                            if ($detail->customer->cf4 != "-" && $detail->customer->cf4 != "") {
                                echo "<br>" . lang("ccf4") . ": " . $detail->customer->cf4;
                            }
                            if ($detail->customer->cf5 != "-" && $detail->customer->cf5 != "") {
                                echo "<br>" . lang("ccf5") . ": " . $customer->cf5;
                            }
                            if ($detail->customer->cf6 != "-" && $detail->customer->cf6 != "") {
                                echo "<br>" . lang("ccf6") . ": " . $detail->customer->cf6;
                            }

                            echo "</p>";
                            echo lang("tel") . ": " . $detail->customer->phone . "<br />" . lang("email") . ": " . $detail->customer->email;
                            ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <!-- <div class="col-xs-3  border-right">
                        <div class="col-xs-2"><i class="fa fa-2x fa-opencart padding010 text-muted"></i></div>
                        <div class="col-xs-10">
                            <h2 class=""><?= $detail->supplier->company && $detail->supplier->company != '-' ? $detail->supplier->company : $detail->supplier->company; ?></h2>
                            <?= $detail->supplier->company && $detail->supplier->company != '-' ? "" : "Attn: " . $detail->supplier->name ?>

                            <?php
                            echo $detail->supplier->address . "<br />" . $detail->supplier->city . " " . $detail->supplier->postal_code . " " . $detail->supplier->state . "<br />" . $detail->supplier->country;

                            echo "<p>";

                            if ($detail->supplier->vat_no != "-" && $detail->supplier->vat_no != "") {
                                echo "<br>" . lang("vat_no") . ": " . $detail->supplier->vat_no;
                            }
                            if ($detail->supplier->gst_no != "-" && $detail->supplier->gst_no != "") {
                                echo "<br>" . lang("gst_no") . ": " . $detail->supplier->gst_no;
                            }
                            if ($detail->supplier->cf1 != "-" && $detail->supplier->cf1 != "") {
                                echo "<br>" . lang("ccf1") . ": " . $detail->supplier->cf1;
                            }
                            if ($detail->supplier->cf2 != "-" && $detail->supplier->cf2 != "") {
                                echo "<br>" . lang("ccf2") . ": " . $supplier->cf2;
                            }
                            if ($detail->supplier->cf3 != "-" && $detail->supplier->cf3 != "") {
                                echo "<br>" . lang("ccf3") . ": " . $detail->supplier->cf3;
                            }
                            if ($detail->supplier->cf4 != "-" && $detail->supplier->cf4 != "") {
                                echo "<br>" . lang("ccf4") . ": " . $detail->supplier->cf4;
                            }
                            if ($detail->supplier->cf5 != "-" && $detail->supplier->cf5 != "") {
                                echo "<br>" . lang("ccf5") . ": " . $supplier->cf5;
                            }
                            if ($detail->supplier->cf6 != "-" && $detail->supplier->cf6 != "") {
                                echo "<br>" . lang("ccf6") . ": " . $detail->supplier->cf6;
                            }

                            echo "</p>";
                            echo lang("tel") . ": " . $detail->supplier->phone . "<br />" . lang("email") . ": " . $detail->supplier->email;
                            ?>
                        </div>
                        <div class="clearfix"></div>

                    </div> -->
                    <div class="col-xs-4">
                        <div class="col-xs-2"><i class="fa fa-3x fa-truck padding010 text-muted"></i></div>
                        <div class="col-xs-10">
                            <h2 class=""><?= $Settings->site_name; ?></h2>
                            <?= $detail->warehouse->name ?>

                            <?php
                            echo $detail->warehouse->address . "<br>";
                            echo ($detail->warehouse->phone ? lang("tel") . ": " . $detail->warehouse->phone . "<br>" : '') . ($detail->warehouse->email ? lang("email") . ": " . $detail->warehouse->email : '');
                            ?>
                        </div>
                        <div class="clearfix"></div>

                    </div>

                    <div class="col-xs-4 border-left">

                        <div class="col-xs-2"><i class="fa fa-3x fa-file-text-o padding010 text-muted"></i></div>
                        <div class="col-xs-10">
                            <h2 class=""><?= lang("ref"); ?>: <?= $detail->ref_no; ?></h2>
                            <h2 class="">PO Number: <?= $detail->po_number; ?></h2>
                            <p style="font-weight:bold;">Supplier: <?= $detail->supplier->company; ?></p>
                            <p style="font-weight:bold;">Customer: <?= $detail->customer->company; ?></p>
                            <p style="font-weight:bold;">Sales Order Date: <?= $this->sma->hrld($detail->date); ?></p>
                            <p style="font-weight:bold;">Delivery Date: <?= $this->sma->hrld($detail->delivery_date); ?></p>
                            <p style="font-weight:bold;">Delivery Address :<br><?php
                            echo $detail->deliveryaddress;
                            ?></p>

                            <p style="font-weight:bold;">Cancel Date: <?= $this->sma->hrld($detail->cancel_date); ?></p>
                            <p style="font-weight:bold;">Account Team <?= lang("status"); ?>: <?= lang($detail->accounts_team_status); ?></p>
                            <p style="font-weight:bold;">Operation Team <?= lang("status"); ?>: <?= lang($detail->operation_team_stauts); ?></p>
                            <p style="font-weight:bold;">SO <?= lang("status"); ?>: <?= lang($detail->status); ?></p>
                        </div>
                        <div class=" order_barcodes">
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <?php
                    if(count($detail->items)>0){
                ?>
                <h2>Uncomplete Quantity</h2>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped print-table order-table table1">
                        <thead>
                            <tr>
                                <th >No.</th>
                                <th >P.ID.</th>
                                <th >Barcode</th>
                                <th >Description</th>
                                <th style="width:100px;">Quantity (pcs)</th>
                                <th style="width:100px;">Demand Value</th>
                                <th style="width:120px;">Completed Qty (pcs)</th>
                                <th style="width:100px;">Completed Value</th>
                                <th style="width:130px;">Uncomplete Qty (pcs)</th>
                                <th style="width:130px;">Uncomplete Value</th>
                                <th>Complete Percentage</th>
                                <th>Expected Complete Qty</th>
                                <th>Expected Complete</th>
                                <th>Group SKU Expected Complete</th>
                                <th style="width:20px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  
                                $no = 1;
                                $total_qty = 0;
                                $total_value = 0;
                                $total_cqty = 0;
                                $total_eqty = 0;
                                $total_groupskueqty = 0;
                                $total_cvalue = 0;
                                $total_ncqty = 0;
                                $total_ncvalue = 0;
                                foreach($detail->items as $item){
                                    $total_qty += $item->quantity;
                                    $total_value += $item->value_qty;
                                    $total_cqty += $item->completed_qty;
                                    $expected_complete_qty = $item->expected_complete_qty;
                                    $group_sku_expected_qty = $item->group_sku_expected_qty;
                                    $uncompleteQty = $item->quantity-$item->completed_qty;
                                    if($uncompleteQty < $item->expected_complete_qty){
                                        $expected_complete_qty = $uncompleteQty;
                                        $group_sku_expected_qty = $uncompleteQty;
                                    }
                                    $total_eqty += $expected_complete_qty;
                                    $total_groupskueqty += $group_sku_expected_qty;



                                    $total_cvalue += $item->value_cqty;
                            ?>
                            <tr>
                                <td  style="text-align:center; width:40px; vertical-align:middle;"><?php echo $no; ?></td>
                                <td><?php echo $item->product_id; ?></td>
                                <td><?php echo $item->product_code; ?></td>
                                <td><?php echo $item->product_name; ?></td>
                                <td><?php echo $item->quantity ?></td>
                                <td><?php echo $this->sma->formatMoney2($item->value_qty) ?></td>
                                <td><?php echo $item->completed_qty ?></td>
                                <td><?php echo $this->sma->formatMoney2($item->value_cqty) ?></td>
                                <td><?php echo $uncompleteQty ?> </td>
                                <td><?php echo $this->sma->formatMoney2($item->value_qty-$item->value_cqty) ?> </td>
                                <td>
                                    <?php 
                                        $completeper = ($item->completed_qty/$item->quantity)*100;
                                        echo (int)$completeper;
                                    ?>%
                                </td>
                                <td><?php 
                                    if($uncompleteQty == 0){
                                        echo 'Completed';
                                    }
                                    else{
                                        echo $expected_complete_qty;
                                    }
                                ?></td>
                                <td>
                                    <?php
                                        if($uncompleteQty == 0){
                                            echo 'Completed';
                                        }
                                        else{
                                            $exqty = ($expected_complete_qty/$item->quantity)*100;
                                            // $exqty = $exqty+$completeper;
                                            echo ($exqty > 100) ? 100 : (int)$exqty;
                                            echo '%';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        if($uncompleteQty == 0){
                                            echo 'Completed';
                                        }
                                        else{
                                            $gskuexqty = ($group_sku_expected_qty/$item->quantity)*100;
                                            echo ($gskuexqty > 100) ? 100 : (int)$gskuexqty;
                                            echo '%';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <ul class="btn-tasks">
                                        <li class="dropdown">
                                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                                <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i>
                                            </a>
                                            <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                                            <?php if($detail->status == "partial" || $detail->status == "pending" || $detail->status == "dispatch"){ ?>
                                                <?php
                                                    if ($Owner || $Admin || $GP['so_complete_item']) { 
                                                        if($item->completed_qty != $item->quantity){
                                                ?>
                                                    <li><a data-id="<?php echo $item->id; ?>" style="cursor: pointer;" class="soi_completeqty"><i class="fa fa-pencil-square-o"></i> Complete Qty</a></li>
                                                <?php
                                                        }
                                                    }
                                                ?>
                                                <?php if ($Owner || $Admin || $GP['so_edit_item']) { ?>
                                                    <li><a data-id="<?php echo $item->id; ?>" style="cursor: pointer;" class="soi_editbtn"><i class="fa fa-pencil-square-o"></i> Edit</a></li>
                                                <?php } ?>
                                                <?php if ($Owner || $Admin || $GP['so_delete_item']) { ?>
                                                    <li><a data-id="<?php echo $item->id; ?>" style="cursor: pointer;" class="soi_deletebtn"><i class="fa fa-trash"></i> Delete </a></li>
                                                <?php } ?>
                                                <?php
                                                }
                                                ?>
                                            </ul>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <?php
                                $no++;
                                }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" >Total</th>
                                <th><?= $total_qty ?></th>
                                <th><?= $this->sma->formatMoney2($total_value) ?></th>
                                <th><?= $total_cqty ?></th>
                                <th><?= $this->sma->formatMoney2($total_cvalue) ?></th>
                                <th><?= $total_qty-$total_cqty ?></th>
                                <th><?= $this->sma->formatMoney2($total_value-$total_cvalue) ?></th>
                                <th colspan="1" >
                                    <?php 
                                        $totalcompleteper = ($total_cqty/$total_qty)*100;
                                        echo (int)$totalcompleteper;
                                        ?>%
                                </th>
                                <th><?php
                                    if($total_qty == $total_cqty){
                                        echo 'Completed';
                                    }
                                    else{
                                        $total_eqty;
                                    }
                                ?></th>
                                <th colspan="1" >
                                    <?php 
                                        if($total_qty == $total_cqty){
                                            echo 'Completed';
                                        }
                                        else{
                                            echo (int)((($total_eqty)/$total_qty)*100);
                                            echo '%';
                                        }
                                    ?>
                                </th>
                                <th colspan="1" >
                                    <?php 
                                        if($total_qty == $total_cqty){
                                            echo 'Completed';
                                        }
                                        else{
                                            echo (int)((($total_groupskueqty)/$total_qty)*100);
                                            echo '%';
                                        }
                                    ?>
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php
                    }
                    if(count($detail->citems)>0){
                ?>
                <h2>Complete Quantity</h2>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped print-table order-table table2">
                        <thead>
                            <tr>
                                <th >No.</th>
                                <th >P.ID.</th>
                                <th >Barcode</th>
                                <th >Description</th>
                                <th style="width:150px;">Quantity (pcs)</th>
                                <th style="width:200px;">Batch</th>
                                <th style="width:200px;">Expiry Date</th>
                                <th style="width:20px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  
                                $no = 1;
                                foreach($detail->citems as $item){
                            ?>
                            <tr>
                                <td  style="text-align:center; width:40px; vertical-align:middle;"><?php echo $no; ?></td>
                                <td><?php echo $item->product_id; ?></td>
                                <td><?php echo $item->product_code; ?></td>
                                <td><?php echo $item->product_name; ?></td>
                                <td><?php echo $item->quantity ?></td>
                                <td><?php echo $item->batch; ?></td>
                                <td><?php echo $item->product_expiry; ?></td>
                                <td>
                                    <ul class="btn-tasks">
                                        <li class="dropdown">
                                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                                <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i>
                                            </a>
                                            <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                                            <?php if($detail->status == "partial" || $detail->status == "pending" || $detail->status == "dispatch"){ ?>
                                                <?php if ($Owner || $Admin || $GP['so_delete_complete_item']) { ?>
                                                    <li><a data-id="<?php echo $item->id; ?>" style="cursor: pointer;" class="soi_completeqty_delete"><i class="fa fa-pencil-square-o"></i> Delete</a></li>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <?php
                                $no++;
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
                    }
                 ?>
                <?php
                    if(count($detail->cso)>0){
                ?>
                <h2>Complete Invoice</h2>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped print-table order-table table2">
                        <thead>
                            <tr>
                                <th >No.</th>
                                <th >Invoice No</th>
                                <th >PO Number</th>
                                <th >PO Date</th>
                                <th style="width:20px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  
                                $no = 1;
                                foreach($detail->cso as $csorow){
                            ?>
                            <tr>
                                <td  style="text-align:center; width:40px; vertical-align:middle;"><?php echo $no; ?></td>
                                <td><?php echo $csorow->reference_no; ?></td>
                                <td><?php echo $csorow->po_number; ?></td>
                                <td><?php echo date_format(date_create($csorow->po_number),"d/m/Y"); ?></td>
                                <td>
                                    <a class="btn btn-success" target="_blank" style="cursor: pointer;" href="<?= admin_url('sales/detail/'.$csorow->dc_id); ?>" > Open Invoice</a>
                                </td>
                            </tr>
                            <?php
                                $no++;
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
                    }
                 ?>
                <div class="row">
                    <div class="col-xs-7">
                        <?php if ($detail->sale_note || $detail->sale_note != "") { ?>
                            <div class="well well-sm">
                                <p class="bold"><?= lang("note"); ?>:</p>
                                <div><?= $this->sma->decode_html($detail->sale_note);    ?></div>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="col-xs-4 col-xs-offset-1">
                        <div class="well well-sm">
                            <p><?= lang("created_by"); ?>
                                : <?= $detail->create_user->first_name . ' ' . $detail->create_user->last_name; ?> </p>

                            <p>Created <?= lang("date"); ?>: <?= $this->sma->hrld($detail->created_at); ?></p>
                            <?php if ($detail->updated_at) { ?>
                                <p><?= lang("update_at"); ?>: <?= $this->sma->hrld($detail->updated_at); ?></p>
                            <?php } ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Large modal -->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 200;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Recevied Item</h4>
            </div>

            <div class="modal-body">
                <?php echo form_open('#', 'id="addrecivedform"'); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Batch</label>
                                <input type="text" class="form-control" id="batch" name="batch" >
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Product Name</label>
                                <input type="text" class="form-control" id="product_name" readonly name="product_name">
                                <input type="hidden" class="form-control" id="so_id" name="so_id">
                                <input type="hidden" class="form-control" id="so_item_id" name="so_item_id">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Order Quantity</label>
                                <input type="text" class="form-control" id="order_qty" name="oder_qty" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Receved Quantity</label>
                                <input type="text" class="form-control" id="receved_qty" name="receved_qty" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Unreceived Quantity</label>
                                <input type="text" class="form-control" id="unreceived_qty" name="unreceived_qty" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Current Receiving Qty</label>
                                <input type="text" class="form-control" id="current_receiving_qty" name="current_receiving_qty">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="text" class="form-control" id="expiry_date" name="">
                            </div>
                        </div>
                    </div>
               <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>
<?php
    // if($Owner || $Admin){
?>

<div class="modal fade" id="addItemSOBtn" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Add New Item</h4>
            </div>

            <?php echo form_open('#', 'id="addItemForm"'); ?>
                <input type="hidden" name="soid" value="<?= $detail->id ?>" >
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Product</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="hidden" name="product" id="product" class="form-control" style="width:100%;" placeholder="<?= lang("select") . ' Product' ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Quantity</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="number" min="1" name="qty" id="qty" class="form-control" autocomplete="off" value="1" style="width:100%;" >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="addItemBtn">Add</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>


<div class="modal fade" id="editPOModel" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Edit Sales Order Detail</h4>
            </div>

            <div class="modal-body">
                <?php echo form_open('#', 'id="editForm"'); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sales Order Date </label>
                                <?php echo form_input('date', date_format(date_create($detail->date),"d/m/Y"), 'class="form-control date" id="recevingdate" required="required" autocomplete="off"'); ?>
                                <input type="hidden" name="id" value="<?= $detail->id ?>" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="slcustomer">Customer Type</label>
                                <input type="text" value="<?php echo $detail->customer_id; ?>" hidden class="hidden_customer_id" readonly/>
                                <div class="input-group">
                                    <?php
                                        echo form_input('customer', $detail->customer_id, 'id="slcustomer" data-placeholder="' . lang("select") . ' ' . lang("customer") . '" required="required" class="form-control input-tip" style="width:100%;"');
                                    ?>
                                    <div class="input-group-addon no-print" style="padding: 2px 8px; border-left: 0;">
                                        <a href="#" id="toogle-customer-read-attr" class="external">
                                            <i class="fa fa-pencil" id="addIcon" style="font-size: 1.2em;"></i>
                                        </a>
                                    </div>
                                    <div class="input-group-addon no-print" style="padding: 2px 7px; border-left: 0;">
                                        <a href="#" id="view-customer" class="external" data-toggle="modal" data-target="#myModal">
                                            <i class="fa fa-eye" id="addIcon" style="font-size: 1.2em;"></i>
                                        </a>
                                    </div>
                                    <?php if ($Owner || $Admin || $GP['customers-add']) { ?>
                                        <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                            <a href="<?= admin_url('customers/add'); ?>" id="add-customer"class="external" data-toggle="modal" data-target="#myModal">
                                                <i class="fa fa-plus-circle" id="addIcon"  style="font-size: 1.2em;"></i>
                                            </a>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sletaliers">E-taliers</label>
                                <?php
                                    $lcu[''] = '';
                                    foreach ($lcustomers as $lcustomer) {
                                        $lcu[$lcustomer->id] = $lcustomer->company;
                                    }
                                    echo form_dropdown('etaliers', $lcu, $detail->etalier_id, 'id="etaliers" class="form-control input-tip searching_select" data-placeholder="' . lang("select") . ' ' . lang("E-taliers") . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Delivery Address</label>
                                <select name="deliveryaddress" id="deliveryaddressid" class="form-control" >
                                    <option value="0">Default Address</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>PO Number</label>
                                <input type="text" class="form-control" name="ponumber" value="<?= $detail->po_number ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>PO Date </label>
                                <?php echo form_input('po_date', date_format(date_create($detail->po_date),"d/m/Y"), 'class="form-control date" id="recevingpodate" required="required" autocomplete="off"'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Delivery Date </label>
                                <?php echo form_input('ddate', date_format(date_create($detail->delivery_date),"d/m/Y"), 'class="form-control date" id="deliverydate" required="required" autocomplete="off"'); ?>
                            </div>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editFormBtn">Update</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="completeModel" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Add batch to complete order</h4>
            </div>

            <div class="modal-body">
                <?php echo form_open('#', 'id="completeForm"'); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Select Batch</label>
                                <select name="batch" id="selectbatch"  class="form-control">
                                    <option value="">Select Batch</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Complete Quantity</label>
                                <input type="number" name="qty" id="sobatchqty" value="0" class="form-control" >
                                <input type="hidden" name="itemid" id="soitemIdbatch">
                                <input type="hidden" name="soid" id="soIdbatch" value="<?php echo $detail->id; ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Remaining Quantity</label>
                                <input type="text" name="uncompleteqty" id="uncompleteqtyTxt" class="form-control" readonly >
                            </div>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="completeModulCloseBtn" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="completeFormBtn">Submit</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editItemModel" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Edit Sale Order Item Quantity</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('#', 'id="editItemForm"'); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Qty</label>
                                <input type="number" name="qty" id="editItemQtyTxt" value="0" class="form-control" >
                                <input type="hidden" name="id" id="ediItemId">
                            </div>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateItemBtn">Update</button>
            </div>
        </div>
    </div>
</div>
<?php
    // }
?>

<script src="<?php echo $assets; ?>plugins/sweetalert/sweetalert.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-flash-1.6.4/b-html5-1.6.4/b-print-1.6.4/sb-1.0.0/datatables.min.js"></script>

<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.table1').DataTable({
            dom: 'Bfrtip',
            searching: false,
            paging: false,
            info: false,
            buttons: [
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [1,2,3,4,5,6,7,8,9,10,11,12]
                    }
                },
                {
                    extend: 'csv',
                    exportOptions: {
                        columns: [1,2,3,4,5,6,7,8,9,10,11,12]
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: [1,2,3,4,5,6,7,8,9,10,11,12]
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: [1,2,3,4,5,6,7,8,9,10,11,12]
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: [1,2,3,4,5,6,7,8,9,10,11,12]
                    }
                },
            ]
        });
        $('.table2').DataTable({
            dom: 'Bfrtip',
            searching: false,
            paging: false,
            info: false,
            buttons: [
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [1,2,3,4,5,6]
                    }
                },
                {
                    extend: 'csv',
                    exportOptions: {
                        columns: [1,2,3,4,5,6]
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: [1,2,3,4,5,6]
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: [1,2,3,4,5,6]
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: [1,2,3,4,5,6]
                    }
                },
            ]
        });
    });
</script>
<script>
    var reloadstatus = 0;
    $(document).ready(function(){
        $("#completeFormBtn").click(function(){
            $('#completeForm').submit();    
        });
        $('#completeForm').submit(function(e){
            e.preventDefault();
            $('.completeFormBtn').prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('salesorders/addbatch'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    $('.completeFormBtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                    if(obj.codestatus == 'ok'){
                        window.top.location.href = '<?= admin_url('salesorders/view/'); ?>'+obj.soid;
                    }
                    else if(obj.codestatus == 'next'){
                        var r = confirm("Do you want to add other batch");
                        if (r == true) {
                            $('#ajaxCall').show();
                            completeqtymodel($("#soitemIdbatch").val());
                            reloadstatus = 1;
                        }
                        else {
                            window.top.location.href = '<?= admin_url('salesorders/view/'); ?>'+obj.soid;
                        }
                    }
                    else{
                        alert(obj.codestatus);
                    }
                },
                error: function(jqXHR, textStatus){
                    var errorStatus = jqXHR.status;
                    if(errorStatus==0){ 
                        alert("Internet Connection Problem");
                    }
                    else{
                        alert('Try Again. Error Code '+errorStatus);
                    }
                    $('.completeFormBtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                }
            });

        });
        $('.soi_editbtn').click(function(){
            $('#ajaxCall').show();
            var id = $(this).data('id');
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            $.ajax({
                url: '<?= admin_url('salesorders/itemdetail'); ?>',
                data: {
                    'id':id,
                    [csrfName]:csrfHash,
                },
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    if(obj.codestatus == "ok"){
                        $("#editItemQtyTxt").val(obj.detail.quantity);
                        $("#ediItemId").val(obj.detail.id);
                        $('#editItemModel').modal('show');
                    }
                    else{
                        alert(obj.codestatus);
                        $('#editItemModel').modal('hide');
                    }
                    $('#ajaxCall').hide();
                },
                error: function(){
                    alert('Try Again!');
                    $('#ajaxCall').hide();
                }

            });
        });
        $('#completeModulCloseBtn').click(function(){
            if(reloadstatus == 1){
                location.reload();
            }
        });
        $('#product').select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function(element, callback) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: '<?= admin_url('sales/pgetSuppliers'); ?>' + $(element).val(),
                    dataType: "json",
                    success: function(data) {
                        callback(data[0]);
                    }
                });
            },
            ajax: {
                url: '<?= admin_url('salesorders/productslist'); ?>',
                dataType: 'json',
                quietMillis: 15,
                data: function(term, page) {
                    return {
                        term: term,
                        supplier_id: <?php echo $detail->supplier_id; ?>,
                        warehouse_id: <?php echo $detail->warehouse_id; ?>,
                        own_company: 0,
                        limit: 15
                    };
                },
                results: function(data, page) {
                    if (data.results != null) {
                        return { results: data.results };
                    } else {
                        return { results: [{ id: '', text: 'No Match Found' }] };
                    }
                }
            }
        });



<?php
    // if($Owner || $Admin){
?>
        $('#editFormBtn').click(function(){
            $('#editForm').submit();    
        });
        $('#editForm').submit(function(e){
            e.preventDefault();
            $('#editFormBtn').prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('salesorders/update'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    $('#editFormBtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                    alert(obj.message);
                    if(obj.codestatus == 'ok'){
                        location.reload();
                    }
                },
                error: function(jqXHR, textStatus){
                    var errorStatus = jqXHR.status;
                    if(errorStatus==0){ 
                        alert("Internet Connection Problem");
                    }
                    else{
                        alert('Try Again. Error Code '+errorStatus);
                    }
                    $('#editFormBtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                }
            });

        });
        $('#cancelSOBtn').click(function(){
            var rid = <?= $detail->id ?>;
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            swal({
                title: "Are you sure?",
                text: "Do you want to Cancel Sale Order!",
                icon: "warning",
                buttons: true,
                successMode: true,
                confirmButtonText: 'Cancel',
            })
            .then(id => {
                if (!id) throw null;
                <?php 
                    $url = base_url('admin/salesorders/cancel?id=');
                 ?>
                return fetch("<?php echo $url; ?>"+rid+"&"+[csrfName]+"="+csrfHash);
            })
            .then(results => {
                return results.json();
            })
            .then(json => {
                if(json.codestatus ==  "ok"){
                    swal("Sale Cancel Successfully", {
                        icon: "success",
                    });
                    setTimeout(function(){ 
                        location.reload();
                    }, 1500);
                }
                else{
                    swal("Erro!", json.codestatus, "error");
                }
            })
            .catch(err => {
                if (err) {
                    console.log(err);
                    swal("Oh noes!", "The AJAX request failed!", "error");
                }
                else {
                    swal.stopLoading();
                    swal.close();
                }
            });
        });
        <?php
            if(count($detail->citems) == 0){
        ?>
            $('#closeSOBtn').click(function(){
                var rid = <?= $detail->id ?>;
                var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
                csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
                swal({
                    title: "Are you sure?",
                    text: "Do you want to Close Sale Order!",
                    icon: "warning",
                    buttons: true,
                    successMode: true,
                    confirmButtonText: 'Close',
                })
                .then(id => {
                    if (!id) throw null;
                    <?php 
                        $url = base_url('admin/salesorders/close?id=');
                    ?>
                    return fetch("<?php echo $url; ?>"+rid+"&"+[csrfName]+"="+csrfHash);
                })
                .then(results => {
                    return results.json();
                })
                .then(json => {
                    if(json.codestatus ==  "ok"){
                        swal("Sale Close Successfully", {
                            icon: "success",
                        });
                        setTimeout(function(){ 
                            location.reload();
                        }, 1500);
                    }
                    else{
                        swal("Erro!", json.codestatus, "error");
                    }
                })
                .catch(err => {
                    if (err) {
                        console.log(err);
                        swal("Oh noes!", "The AJAX request failed!", "error");
                    }
                    else {
                        swal.stopLoading();
                        swal.close();
                    }
                });
            });
        <?php
            }
        ?>
        $('#deleteSOBtn').click(function(){
            var rid = <?= $detail->id ?>;
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            swal({
                title: "Are you sure?",
                text: "Do you want to Delete Sale Order!",
                icon: "warning",
                buttons: true,
                successMode: true,
                confirmButtonText: 'Delete',
            })
            .then(id => {
                if (!id) throw null;
                <?php 
                    $url = base_url('admin/salesorders/delete?id=');
                 ?>
                return fetch("<?php echo $url; ?>"+rid+"&"+[csrfName]+"="+csrfHash);
            })
            .then(results => {
                return results.json();
            })
            .then(json => {
                if(json.codestatus ==  "ok"){
                    swal("Purchase Sale Delete Successfully", {
                        icon: "success",
                    });
                    setTimeout(function(){ 
                        window.top.location.href = '<?= admin_url('salesorders'); ?>';
                    }, 1500);
                }
                else{
                    swal("Erro!", json.codestatus, "error");
                }
            })
            .catch(err => {
                if (err) {
                    console.log(err);
                    swal("Oh noes!", "The AJAX request failed!", "error");
                }
                else {
                    swal.stopLoading();
                    swal.close();
                }
            });
        });
        $('.soi_deletebtn').click(function(){
            var iid = $(this).data('id');
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            swal({
                title: "Are you sure?",
                text: "Do you want to delete this item!",
                icon: "warning",
                buttons: true,
                successMode: true,
                confirmButtonText: 'Delete',
            })
            .then(id => {
                if (!id) throw null;
                <?php 
                    $url = base_url('admin/salesorders/itemdelete?id=');
                 ?>
                return fetch("<?php echo $url; ?>"+iid+"&"+[csrfName]+"="+csrfHash);
            })  
            .then(results => {
                return results.json();
            })
            .then(json => {
                if(json.codestatus ==  "ok"){
                    swal("Item Delete Successfully", {
                        icon: "success",
                    });
                    setTimeout(function(){ 
                        location.reload();
                    }, 1500);
                }
                else{
                    swal("Erro!", json.codestatus, "error");
                }
            })
            .catch(err => {
                if (err) {
                    console.log(err);
                    swal("Oh noes!", "The AJAX request failed!", "error");
                }
                else {
                    swal.stopLoading();
                    swal.close();
                }
            });
        });
        $('.soi_completeqty_delete').click(function(){
            $('#ajaxCall').show();
            var id = $(this).data('id');
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            $.ajax({
                url: '<?= admin_url('salesorders/citem_delete'); ?>',
                data: {
                    'id':id,
                    [csrfName]:csrfHash,
                },
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    location.reload();
                    $('#ajaxCall').hide();
                },
                error: function(){
                    alert('Try Again!');
                    $('#ajaxCall').hide();
                }

            });



        });
        $('.soi_completeqty').click(function(){
            $('#ajaxCall').show();
            var id = $(this).data('id');
            completeqtymodel(id);
        });
        function completeqtymodel(id){
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            $.ajax({
                url: '<?= admin_url('salesorders/batchdetail'); ?>',
                data: {
                    'id':id,
                    [csrfName]:csrfHash,
                },
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    if(obj.codestatus == "ok"){
                        var i = 0;
                        var html = "";
                        for(i = 0; i<obj.ebatchs.length; i++){
                            html += "<option value='"+obj.ebatchs[i].code+"' >"+obj.ebatchs[i].code+" (Expiry Date: "+obj.ebatchs[i].expiry+" & Available: "+obj.ebatchs[i].qb+")</option>";
                        }
                        $("#selectbatch").html(html);
                        $("#sobatchqty").val(0);
                        $("#soitemIdbatch").val(id);
                        $("#uncompleteqtyTxt").val(obj.uncompletedqty);
                        $("#selectbatch").select2("destroy").select2();
                        $('#completeModel').modal('show');
                    }
                    else{
                        alert(obj.codestatus);
                        $('#completeModel').modal('hide');
                    }
                    $('#ajaxCall').hide();
                },
                error: function(){
                    alert('Try Again!');
                    $('#ajaxCall').hide();
                }

            });
        }
        $('#updateItemBtn').click(function(){
            $('#editItemForm').submit();    
        });
        $('#editItemForm').submit(function(e){
            e.preventDefault();
            $('#updateItemBtn').prop('disabled', true);
            $('#updateItemBtn').prop('disabled', false);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('salesorders/updateitem'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    $('#updateItemBtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                    if(obj.codestatus == 'ok'){
                        location.reload();
                        alert('Item Update Successfuly');
                    }
                    else{
                        alert(obj.codestatus);
                    }
                },
                error: function(jqXHR, textStatus){
                    var errorStatus = jqXHR.status;
                    if(errorStatus==0){ 
                        alert("Internet Connection Problem");
                    }
                    else{
                        alert('Try Again. Error Code '+errorStatus);
                    }
                    $('#updateItemBtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                }
            });
        });
<?php
    // }
?>

        var $customer = $("#slcustomer");
        $(".hidden_customer_id").val(localStorage.getItem("slcustomer"));
        $customer.change(function (e) {
            localStorage.setItem("slcustomer", $(this).val());
            $(".hidden_customer_id").val($(this).val());
        });
        if ((slcustomer = localStorage.getItem("slcustomer"))) {
            $customer.val(slcustomer).select2({
                minimumInputLength: 1,
                data: [],
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: site.base_url + "customers/getCustomer/" + $(element).val(),
                        dataType: "json",
                        success: function (data) {
                            callback(data[0]);
                            console.log(data);
                        },
                    });
                },
                ajax: {
                    url: site.base_url + "customers/suggestions",
                    dataType: "json",
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            term: term,
                            limit: 10,
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return { results: data.results };
                        } else {
                            return { results: [{ id: "", text: "No Match Found" }] };
                        }
                    },
                },
            });
        } else {
            nsCustomer();
        }
        // hellper function for customer if no localStorage value
        function nsCustomer() {
            $("#slcustomer").select2({
                minimumInputLength: 1,
                ajax: {
                    url: site.base_url + "customers/suggestions",
                    dataType: "json",
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            term: term,
                            limit: 10,
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return { results: data.results };
                        } else {
                            return { results: [{ id: "", text: "No Match Found" }] };
                        }
                    },
                },
            });
        }
        $("#addItemForm").submit(function(e){
             e.preventDefault();
             $(':input[type="submit"]').prop('disabled', true);
             $.ajax({
                url: '<?= admin_url('salesorders/additem'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    if(obj.codestatus == "Item Add Successfully"){
                        location.reload();
                    }
                    alert(obj.codestatus);
                    $(':input[type="submit"]').prop('disabled', false);
                },
                error: function(jqXHR, textStatus){
                    var errorStatus = jqXHR.status;
                    $(':input[type="submit"]').prop('disabled', false);
                }
            });
            
        });
        $('#slcustomer').change(function(){
            getAddressLis();
        });
        function getAddressLis(){
            var customerID = $('#slcustomer').val();
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

            $.ajax({
                url: '<?= admin_url('sales/getaddress'); ?>',
                type: 'POST',
                data: {customerID:customerID,[csrfName]:csrfHash},
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    $('#deliveryaddressid').html(obj.html);
                    $('#deliveryaddressid').val(<?php echo $detail->customer_address_id; ?>);
                },
                error: function(jqXHR, textStatus){
                    var errorStatus = jqXHR.status;
                }
            });
        }
        getAddressLis();


    });
</script>
