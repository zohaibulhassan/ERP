<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD ?>
<?php 
    if($remove == 1){
?>
<script>
    localStorage.removeItem('purchaseorder_extra');
    // localStorage.removeItem('purchaseorder_refno');
    localStorage.removeItem('purchaseorder_payment_term');
    localStorage.removeItem('purchaseorder_order_discount');
    localStorage.removeItem('purchaseorder_items');
    localStorage.removeItem('purchaseorder_order_tax');
    localStorage.removeItem('purchaseorder_order_note');
    localStorage.removeItem('purchaseorder_supplier_id');
    localStorage.removeItem('purchaseorder_warehouseid');
    localStorage.removeItem('purchaseorder_order_shipping');
    localStorage.removeItem('purchaseorder_owncompanies');
    localStorage.removeItem('purchaseorder_recevingdate');
</script>
<?php
    }
?>
<style>
    table.dataTable tfoot th, table.dataTable tfoot td{
        border-top: 1px solid #ddd !important;
    }
    .dt-buttons {
        float:right;
    }
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-file"></i><?= lang("purchase_no") . '. ' . $detail->id; ?></h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <?php if($detail->status == "partial" || $detail->status == "pending"){ ?>
                            <?php if ($Owner || $Admin || $GP['po_add_receiving']) { ?>
                                <li><a href="<?php echo base_url('admin/purchaseorder/addreciveing').'/'.$detail->id; ?>"><i class="fa fa-plus"></i> Add Receiving</a></li>
                            <?php } ?>
                            <?php if ($Owner || $Admin || $GP['po_add_new_item']) { ?>
                                <li><a href="#addItemPOBtn"  data-toggle="modal" data-target="#addItemPOBtn"><i class="fa fa-cart-plus"></i> Add Item</a></li>
                            <?php } ?>
                            <?php if ($Owner || $Admin || $GP['po_edit_info']) { ?>
                                <li><a href="#editPOModel" id="editPOBtn" data-toggle="modal" data-target="#editPOModel"><i class="fa fa-edit"></i> Edit PO Detail</a></li>
                            <?php } ?>
                            <?php if ($Owner || $Admin || $GP['po_close']) { ?>
                                <li><a  id="closePOBtn" style="cursor: pointer;" ><i class="fa fa-close"></i> Close Purchase Order</a></li>
                            <?php } ?>
                            <?php if ($Owner || $Admin || $GP['po_delete']) { ?>
                                <li><a  id="deletePOBtn" style="cursor: pointer;" ><i class="fa fa-trash"></i> Delete Purchase Order</a></li>
                            <?php } ?>
                            <li><a href="<?php echo base_url('admin/purchaseorder/pdf').'/'.$detail->id; ?>"><i class="fa fa-file-pdf-o"></i> Download PDF</a></li>
                              
                        <?php
                            }
                        ?>
                            
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
                                echo "<br>" . lang("scf1") . ": " . $detail->supplier->cf1;
                            }
                            if ($detail->supplier->cf2 != "-" && $detail->supplier->cf2 != "") {
                                echo "<br>" . lang("scf2") . ": " . $supplier->cf2;
                            }
                            if ($detail->supplier->cf3 != "-" && $detail->supplier->cf3 != "") {
                                echo "<br>" . lang("scf3") . ": " . $detail->supplier->cf3;
                            }
                            if ($detail->supplier->cf4 != "-" && $detail->supplier->cf4 != "") {
                                echo "<br>" . lang("scf4") . ": " . $detail->supplier->cf4;
                            }
                            if ($detail->supplier->cf5 != "-" && $detail->supplier->cf5 != "") {
                                echo "<br>" . lang("scf5") . ": " . $supplier->cf5;
                            }
                            if ($detail->supplier->cf6 != "-" && $detail->supplier->cf6 != "") {
                                echo "<br>" . lang("scf6") . ": " . $detail->supplier->cf6;
                            }

                            echo "</p>";
                            echo lang("tel") . ": " . $detail->supplier->phone . "<br />" . lang("email") . ": " . $detail->supplier->email;
                            ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>

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
                            <h2 class=""><?= lang("ref"); ?>: <?= $detail->reference_no; ?></h2>
                            <?php if (!empty($detail->return_purchase_ref)) {
                                echo '<p>'.lang("return_ref").': '.$detail->return_purchase_ref;
                                    echo '</p>';
                            } ?>
                            <p style="font-weight:bold;">Receiving Date: <?= $this->sma->hrld($detail->receiving_date); ?></p>
                            <p style="font-weight:bold;">Received Date: <?= $this->sma->hrld($detail->received_date); ?></p>
                            <p style="font-weight:bold;">Close Date: <?= $this->sma->hrld($detail->close_date); ?></p>
                            <p style="font-weight:bold;"><?= lang("status"); ?>: <?= lang($detail->status); ?></p>
                            <p style="font-weight:bold;"><?= lang("payment_status"); ?>: <?= lang($detail->payment_status); ?></p>
                        </div>
                        <div class="col-xs-12 order_barcodes">
                            <img src="<?= admin_url('misc/barcode/'.$this->sma->base64url_encode($detail->reference_no).'/code128/74/0/1'); ?>" alt="<?= $detail->reference_no; ?>" class="bcimg" />
                            <?= $this->sma->qrcode('link', urlencode(admin_url('purchases/view/' . $detail->id)), 2); ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped print-table order-table">
                        <thead>
                            <tr>
                                <th >No.</th>
                                <th >P.ID.</th>
                                <th >Description</th>
                                <th style="width:100px;">Quantity</th>
                                <th style="width:120px;">Received Qty</th>
                                <th style="width:130px;">Unreceived Qty</th>
                                <th style="width:130px;">Complete Percentage</th>
                                <th style="width:100px;">Unit Cost</th>
                                <th style="width:100px;">Discount</th>
                                <th style="width:100px;">Tax</th>
                                <th style="width:100px;">Subtotal</th>
                                <th style="width:20px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  
                                $no = 1;
                                foreach($detail->items as $item){
                            ?>
                            <tr>
                                <td  style="text-align:center; width:40px; vertical-align:middle;"><?php echo $no; ?></td>
                                <td><?php echo $item->product_id; ?></td>
                                <td><?php echo $item->product_name; ?></td>
                                <td><?php echo $item->qty; ?></td>
                                <td><?php echo $item->count_receving; ?></td>
                                <td><?php echo $item->qty-$item->count_receving; ?></td>
                                <td><?=  decimalallow(($item->count_receving/$item->qty)*100,2) ?>%</td>
                                <td><?php echo $this->sma->formatMoney($item->purchase_price); ?></td>
                                <td style="color:#dc0b0b" ><?php echo $this->sma->formatMoney(' -'.$item->total_discount*$item->qty); ?></td>
                                <td><?php echo $this->sma->formatMoney($item->total_tax*$item->qty); ?></td>
                                <td><?php echo $this->sma->formatMoney($item->sub_total*$item->qty); ?></td>
                                <td>
                                    <ul class="btn-tasks">
                                        <li class="dropdown">
                                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                                <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i>
                                            </a>
                                            <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                                                <?php if($detail->status == "partial" || $detail->status == "pending"){ ?>
                                                    <?php if ($Owner || $Admin || $GP['po_edit_item']) { ?>
                                                        <li><a data-id="<?php echo $item->id; ?>" style="cursor: pointer;" class="poi_editbtn"><i class="fa fa-pencil-square-o"></i> Edit</a></li>
                                                    <?php } ?>
                                                    <?php if ($Owner || $Admin || $GP['po_delete_item']) { ?>
                                                        <li><a data-id="<?php echo $item->id; ?>" style="cursor: pointer;" class="poi_deletebtn"><i class="fa fa-trash"></i> Delete </a></li>
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
                                <td colspan="7" style="text-align:right"></td>
                                <td  style="text-align:right"><?= lang("total"); ?>(<?= $default_currency->code; ?>)</td>
                                <td><?php echo $this->sma->formatMoney($detail->items_discount); ?></td>
                                <td><?php echo $this->sma->formatMoney($detail->items_tax); ?></td>
                                <td colspan="2" ><?php echo $this->sma->formatMoney($detail->total); ?></td>
                            </tr>
                            <tr>
                                <td colspan="10" style="text-align:right; font-weight:bold;"><?= lang("order_discount"); ?>(<?= $default_currency->code; ?>)</td>
                                <td colspan="2" ><?php echo $this->sma->formatMoney($detail->order_discount); ?></td>
                            </tr>
                            <tr>
                                <td colspan="10" style="text-align:right; font-weight:bold;"><?= lang("order_tax"); ?>(<?= $default_currency->code; ?>)</td>
                                <td colspan="2" ><?php echo $this->sma->formatMoney($detail->order_tax); ?></td>
                            </tr>
                            <tr>
                                <td colspan="10" style="text-align:right; font-weight:bold;"><?= lang("shipping"); ?>(<?= $default_currency->code; ?>)</td>
                                <td colspan="2" ><?php echo $this->sma->formatMoney($detail->shipping); ?></td>
                            </tr>
                            <tr>
                                <td colspan="10" style="text-align:right; font-weight:bold;"><?= lang("total_amount"); ?>(<?= $default_currency->code; ?>)</td>
                                <td colspan="2" ><?php echo $this->sma->formatMoney($detail->grand_total); ?></td>
                            </tr>
                            <tr>
                                <td colspan="10" style="text-align:right; font-weight:bold;"><?= lang("paid"); ?>(<?= $default_currency->code; ?>)</td>
                                <td colspan="2" ><?php echo $this->sma->formatMoney($detail->paid_amount); ?></td>
                            </tr>
                            <tr>
                                <td colspan="10" style="text-align:right; font-weight:bold;"><?= lang("balance"); ?>(<?= $default_currency->code; ?>)</td>
                                <td colspan="2" ><?php echo $this->sma->formatMoney($detail->grand_total-$detail->paid_amount); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="row">
                    <div class="col-xs-7">
                        <?php if ($detail->note || $detail->note != "") { ?>
                            <div class="well well-sm">
                                <p class="bold"><?= lang("note"); ?>:</p>
                                <div><?= $this->sma->decode_html($detail->note);    ?></div>
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
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form','id'=>'createdPorchaseForm2','method'=>'get');
                    echo admin_form_open("purchaseorder/createdPurchase", $attrib);
                    if($purchase_create>0 && ($Owner || $Admin || $GP['po_create_invoice'])){
                ?>
                    <button data-rows="<?php echo $purchase_create; ?>" type="submit" class="btn btn-success createselectedpurchase_btn2" style="margin-bottom: 10px;" >Create Selected Purchase</button>
                <?php
                    }
                ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped print-table order-table">
                        <thead>
                            <tr>
                                <th style="width:50px" ></th>
                                <th style="width:150px" >Delivery Note#</th>
                                <th >Received By</th>
                                <th >Received Date</th>
                                <th style="width:200px;">Purchace Invoice</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $purchase_create = 'yes';
                                $no = 1;
                                foreach($deliveries as $delivery){
                            ?>
                            <tr>
                                <td>
                                    <?php
                                        if($delivery->purchase_create == 'no' && ($Owner || $Admin || $GP['po_create_invoice'])){
                                    ?>
                                        <input type="checkbox" class="devlieryChkbox" name="did[]" value="<?php echo $delivery->id; ?>"  id="pic_<?php echo $no; ?>" >
                                    <?php
                                        }
                                    ?>
                                </td>
                                <td><?php echo $delivery->id; ?></td>
                                <td><?php echo $delivery->first_name.' '.$delivery->last_name; ?></td>
                                <td><?php echo $delivery->created_at; ?></td>
                                <td><?php if($delivery->purchase_create == 'yes'){ echo 'Created'; } else{ echo 'No Create'; } ?></td>
                                <td>
                                    <ul class="btn-tasks">
                                        <li class="dropdown">
                                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                                <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i>
                                            </a>
                                            <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                                                <?php //if($detail->status == "partial" || $detail->status == "pending"){?>
                                                    <?php if ($Owner || $Admin || $GP['po_edit_receiving']) { ?>
                                                        <li><a  style="cursor: pointer;" href="<?php echo base_url('admin/purchaseorder/editreciveing').'?porid='.$delivery->id.'&poid='.$detail->id; ?>" ><i class="fa fa-pencil-square-o"></i></i> Edit </a></li>
                                                    <?php } ?>
                                                    <?php if ($Owner || $Admin || $GP['po_delete_receiving']) { ?>
                                                        <li><a data-id="<?php echo $delivery->id; ?>" style="cursor: pointer;" class="por_delbtn"><i class="fa fa-trash"></i></i> Delete </a></li>
                                                    <?php } ?>
                                                <?php
                                                    // }
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
                <?php echo form_close(); ?>
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
                                <input type="hidden" class="form-control" id="po_id" name="po_id">
                                <input type="hidden" class="form-control" id="po_item_id" name="po_item_id">
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
    // if($Owner){
?>
<div class="modal fade" id="addItemPOBtn" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Add New Item</h4>
            </div>

            <?php echo form_open('#', 'id="addItemForm"'); ?>
                <input type="hidden" name="poid" value="<?= $detail->id ?>" >
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Net Unit Cost</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="text" name="net_unit_cost" value="0" id="net_unit_cost" class="form-control" style="width:100%;" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>MRP</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="text" name="mrp" id="mrp" value="0" class="form-control" style="width:100%;" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Quantity</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="number" min="1" name="qty" id="qty" class="form-control" autocomplete="off" value="1" style="width:100%;" >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Discount One</label>
                                <div class="input-group" style="width: 100%;">
                                    <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                        <input type="checkbox" name="done_chk" class="addItemchk" id="done_chk" value="0">
                                    </div>
                                    <input type="text" name="done_txt" id="done_txt" class="form-control" style="width:100%;" readonly value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Discount Two</label>
                                <div class="input-group" style="width: 100%;">
                                    <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                        <input type="checkbox" name="dtwo_chk" class="addItemchk" id="dtwo_chk" value="0">
                                    </div>
                                    <input type="text" name="dtwo_txt" id="dtwo_txt" class="form-control" style="width:100%;" readonly value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Discount Three</label>
                                <div class="input-group" style="width: 100%;">
                                    <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                        <input type="checkbox" name="dth_chk" class="addItemchk" id="dth_chk" value="0">
                                    </div>
                                    <input type="text" name="dth_txt" id="dth_txt" class="form-control" style="width:100%;" readonly value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Total Discount</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="text" name="td" id="td" class="form-control" style="width:100%;" readonly value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>FED Tax</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="text" name="fed" id="fed" class="form-control" style="width:100%;" readonly value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Product Tax</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="text" name="producttax" id="producttax" class="form-control" style="width:100%;" readonly value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Sub Total</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="text" name="total" id="total" class="form-control" style="width:100%;" readonly value="0">
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
                <h4 class="modal-title">Edit Purchase Order Detail</h4>
            </div>

            <div class="modal-body">
                <?php echo form_open('#', 'id="editForm"'); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Expected Receiving Date </label>
                                <?php echo form_input('date', date_format(date_create($detail->receiving_date),"d/m/Y"), 'class="form-control date" id="recevingdate" required="required" autocomplete="off"'); ?>
                                <input type="hidden" name="id" value="<?= $detail->id ?>" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Warehouse</label>
                                <?php
                                $wh[''] = '';
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                $exAttri = '';
                                // if(count($deliveries)>0){$exAttri = ' data-readonly=true disable';}
                                echo form_dropdown('warehouse', $wh, $detail->warehouse->id, 'class="form-control input-tip" id="warehouseid" data-placeholder="' . lang("select") . ' ' . lang("warehouse") . '" required="required" style="width:100%;"'.$exAttri);

                               
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Own Company</label>
                                <?php
                                $oc[''] = '';
                                foreach ($own_company as $own_companies) {
                                    $oc[$own_companies->id] = $own_companies->companyname;
                                }
                                echo form_dropdown('own_company', $oc, $detail->own_company, 'class="form-control input-tip select" id="owncompanies" data-placeholder="' . lang("select") . ' ' . lang("own_companies") . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Order Tax</label>
                                <?php
                                    $tr[""] = "";
                                    foreach ($tax_rates as $tax) {
                                        $tr[$tax->id] = $tax->name;
                                    }
                                    echo form_dropdown('order_tax', $tr, $detail->order_tax_id, 'id="order_tax" class="form-control input-tip select" style="width:100%;" ');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Discount</label>
                                <input type="text" class="form-control" name="discount" value="<?= $detail->order_discount ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Shipping</label>
                                <input type="text" class="form-control" name="shipping" value="<?= $detail->shipping ?>">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Payment Term</label>
                                <input type="text" class="form-control" name="payment_term" value="<?= $detail->payemnt_terms ?>">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Note</label>
                                <?php echo form_textarea('note', $detail->note, 'class="form-control" id="order_note" style="margin-top: 10px; height: 100px;"'); ?>
                            </div>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateBtn">Update</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editItemModel" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Edit Purchase Order Item</h4>
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Discount One</label>
                                <div class="input-group" style="width: 100%;">
                                    <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                        <input type="checkbox" name="done_chk" class="editItemchk" id="editItemdone_chk" value="0">
                                    </div>
                                    <input type="text" name="done_txt" id="editItemdone_txt" class="form-control" style="width:100%;" readonly value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Discount Two</label>
                                <div class="input-group" style="width: 100%;">
                                    <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                        <input type="checkbox" name="dtwo_chk" class="editItemchk" id="editItemdtwo_chk" value="0">
                                    </div>
                                    <input type="text" name="dtwo_txt" id="editItemdtwo_txt" class="form-control" style="width:100%;" readonly value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Discount Three</label>
                                <div class="input-group" style="width: 100%;">
                                    <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                        <input type="checkbox" name="dth_chk" class="editItemchk" id="editItemdth_chk" value="0">
                                    </div>
                                    <input type="text" name="dth_txt" id="editItemdth_txt" class="form-control" style="width:100%;" readonly value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Total Discount</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="text" name="td" id="editItemtd" class="form-control" style="width:100%;" readonly value="0">
                                </div>
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
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-flash-1.6.4/b-html5-1.6.4/b-print-1.6.4/sb-1.0.0/datatables.min.js"></script>

<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.table').DataTable({
            dom: 'Bfrtip',
            searching: false,
            paging: false,
            info: false,
            buttons: [
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [1,2,3,4,5,6,7,8,9,10]
                    },
                    footer: true,
                    title: ''
                },
                {
                    extend: 'csv',
                    exportOptions: {
                        columns: [1,2,3,4,5,6,7,8,9,10]
                    },
                    footer: true,
                    title: ''
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: [1,2,3,4,5,6,7,8,9,10]
                    },
                    footer: true,
                    title: ''
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: [1,2,3,4,5,6,7,8,9,10]
                    },
                    footer: true,
                    title: '',
                    orientation: 'landscape',
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: [1,2,3,4,5,6,7,8,9,10]
                    },
                    footer: true,
                    title: ''
                },
            ]
        });
    });
</script>

<script>
    $(document).ready(function(){
        <?php 
            if(count($deliveries)>0){
            ?>
            // $("#warehouseid").select2("readonly", true);
            $('#warehouseid').prop("readonly",true);
            <?php 
            }
        ?>
        $("#createdPorchaseForm").keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
        $('#createdPorchaseForm').submit(function(e){
            e.preventDefault();
            $('.createselectedpurchase_btn').prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('purchaseorder/allcreate'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    $('.createselectedpurchase_btn').prop('disabled', false);
                    $('#ajaxCall').hide();
                    alert(obj.message);
                    if(obj.codestatus == 'ok'){
                        // location.reload();
                        window.top.location.href = '<?= admin_url('purchases/view/'); ?>'+obj.p_id;
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
                    $('.createselectedpurchase_btn').prop('disabled', false);
                    $('#ajaxCall').hide();
                }
            });

        });
        $('.editForm').click(function(){
            var rid = $(this).data('id');
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            swal({
                title: "Are you sure?",
                text: "Do you want to create purchase!",
                icon: "warning",
                buttons: true,
                successMode: true,
                confirmButtonText: 'Create',
            })
            .then(id => {
                if (!id) throw null;
                <?php 
                    $url = base_url('admin/purchaseorder/createpurchase?rid=');
                 ?>
                return fetch("<?php echo $url; ?>"+rid+"&"+[csrfName]+"="+csrfHash);
            })
            .then(results => {
                return results.json();
            })
            .then(json => {
                if(json.codestatus ==  "ok"){
                    swal("Purchase Create Successfully", {
                        icon: "success",
                    });
                    window.top.location.href = '<?= admin_url('purchases/view/'); ?>'+$json.p_id;
                }
                else{
                    swal("Error!", json.codestatus, "error");
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
    // if($Owner){
?>
        $('#updateBtn').click(function(){
            $('#editForm').submit();    
        });
        $('#editForm').submit(function(e){
            e.preventDefault();
            $('#updateBtn').prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('purchaseorder/update'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    $('#updateBtn').prop('disabled', false);
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
                    $('#updateBtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                }
            });

        });
        $('#closePOBtn').click(function(){
            var poid = <?= $detail->id ?>;
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            swal({
                title: "Are you sure?",
                text: "Do you want to Close purchase Order!",
                icon: "warning",
                buttons: true,
                successMode: true,
                confirmButtonText: 'Close',
            })
            .then(id => {
                if (!id) throw null;
                <?php 
                    $url = base_url('admin/purchaseorder/close?id=');
                 ?>
                return fetch("<?php echo $url; ?>"+poid+"&"+[csrfName]+"="+csrfHash);
            })
            .then(results => {
                return results.json();
            })
            .then(json => {
                if(json.codestatus ==  "ok"){
                    swal("Purchase Order Close Successfully", {
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
                    swal("Oh noes!", "Request failed!", "error");
                }
                else {
                    swal.stopLoading();
                    swal.close();
                }
            });
        });
        $('#deletePOBtn').click(function(){
            var rid = <?= $detail->id ?>;
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            swal({
                title: "Are you sure?",
                text: "Do you want to Delete purchase Order!",
                icon: "warning",
                buttons: true,
                successMode: true,
                confirmButtonText: 'Delete',
            })
            .then(id => {
                if (!id) throw null;
                <?php 
                    $url = base_url('admin/purchaseorder/delete?id=');
                 ?>
                return fetch("<?php echo $url; ?>"+rid+"&"+[csrfName]+"="+csrfHash);
            })
            .then(results => {
                return results.json();
            })
            .then(json => {
                if(json.codestatus ==  "ok"){
                    swal("Purchase Order Delete Successfully", {
                        icon: "success",
                    });
                    setTimeout(function(){ 
                        window.top.location.href = '<?= admin_url('purchaseorder'); ?>';
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
        $('#product').change(function(){
            var pid = $(this).val();
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            $.ajax({
                type: "post",
                url: '<?= admin_url('purchaseorder/productdetail'); ?>',
                data: {
                    'pid':pid,
                    [csrfName]:csrfHash,
                },
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    $('.addItemchk').iCheck('uncheck');
                    $("#net_unit_cost").val(obj.detail.cost);
                    $("#mrp").val(obj.detail.mrp);
                    $("#qty").val(1);

                    $("#done_chk").val(obj.detail.discount_one);
                    $("#dtwo_chk").val(obj.detail.discount_two);
                    $("#dth_chk").val(obj.detail.discount_three);

                    $("#done_txt").val(obj.detail.discount_one_amount);
                    $("#dtwo_txt").val(obj.detail.discount_two_amount);
                    $("#dth_txt").val(obj.detail.discount_three_amount);

                    // $("#done_txt").val((obj.detail.price/100)*obj.detail.discount_one);
                    // $("#dtwo_txt").val((obj.detail.price/100)*obj.detail.discount_two);
                    // $("#dth_txt").val((obj.detail.price/100)*obj.detail.discount_three);

                    // $("#td").val(0.0000);
                    $("#fed").val(obj.detail.fed_tax);
                    $("#producttax").val(obj.detail.product_tax);
                    // $("#total").val(parseFloat(obj.detail.cost)+parseFloat(obj.detail.fed_tax)+parseFloat(obj.detail.product_tax));
                    addItemCal();
                }
            });
        });
        $('#product').select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function(element, callback) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: '<?= admin_url('purchaseorder/pgetSuppliers'); ?>' + $(element).val(),
                    dataType: "json",
                    success: function(data) {
                        callback(data[0]);
                    }
                });
            },
            ajax: {
                url: '<?= admin_url('purchaseorder/productslist'); ?>',
                dataType: 'json',
                quietMillis: 15,
                data: function(term, page) {
                    return {
                        term: term,
                        supplier_id: <?php echo $detail->supplier->id; ?>,
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
        $(document).on('ifChanged','.addItemchk', function(event) {
            addItemCal();
        });
        $('#qty').change(function(){
            addItemCal();
        });
        function addItemCal(){
            var netprice = parseFloat($("#net_unit_cost").val());
            var qty = parseFloat($("#qty").val());
            var fed = parseFloat($("#fed").val());
            var producttax = parseFloat($("#producttax").val());
            var discount1 = 0;
            var discount2 = 0;
            var discount3 = 0;
            if($("#done_chk").prop("checked")){discount1 = parseFloat($('#done_txt').val());}
            if($("#dtwo_chk").prop("checked")){discount2 = parseFloat($('#dtwo_txt').val());}
            if($("#dth_chk").prop("checked")){discount3 = parseFloat($('#dth_txt').val());}
            var discount = discount1+discount2+discount3;
            $('#td').val(discount);
            $('#total').val(((netprice+fed+producttax)-discount)*qty);


        }
        addItemCal();
        $("#addItemForm").submit(function(e){
             e.preventDefault();
             $(':input[type="submit"]').prop('disabled', true);
             $.ajax({
                url: '<?= admin_url('purchaseorder/additem'); ?>',
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
        $('.poi_deletebtn').click(function(){
            var rid = $(this).data('id');
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
                    $url = base_url('admin/purchaseorder/itemdelete?id=');
                 ?>
                return fetch("<?php echo $url; ?>"+rid+"&poid=<?php echo $detail->id; ?>&"+[csrfName]+"="+csrfHash);
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
        $('.poi_editbtn').click(function(){
            $('#ajaxCall').show();
            var id = $(this).data('id');
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            $.ajax({
                url: '<?= admin_url('purchaseorder/itemdetail'); ?>',
                data: {
                    'id':id,
                    [csrfName]:csrfHash,
                },
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    if(obj.codestatus == "ok"){
                        $('#editItemQtyTxt').val(obj.detail.qty);
                        $('#ediItemId').val(obj.detail.id);
                        $('.editItemchk').iCheck('uncheck');
                        if(obj.detail.pd1=="yes"){
                            $('#editItemdone_chk').iCheck('check');
                        }
                        if(obj.detail.pd2=="yes"){
                            $('#editItemdtwo_chk').iCheck('check');
                        }
                        if(obj.detail.pd3=="yes"){
                            $('#editItemdth_chk').iCheck('check');
                        }
                        $('#editItemdone_chk').val(obj.detail.sales_incentive_discount_amount);
                        $('#editItemdtwo_chk').val(obj.detail.trade_discount_amount);
                        $('#editItemdth_chk').val(obj.detail.consumer_discount_amount);

                        $('#editItemdone_txt').attr('data-dis',obj.detail.sales_incentive_discount_amount);
                        $('#editItemdtwo_txt').attr('data-dis',obj.detail.trade_discount_amount);
                        $('#editItemdth_txt').attr('data-dis',obj.detail.consumer_discount_amount);

                        $('#editItemdone_txt').val(parseFloat(obj.detail.sales_incentive_discount_amount)*parseInt(obj.detail.qty));
                        $('#editItemdtwo_txt').val(parseFloat(obj.detail.trade_discount_amount)*parseInt(obj.detail.qty));
                        $('#editItemdth_txt').val(parseFloat(obj.detail.consumer_discount_amount)*parseInt(obj.detail.qty));

                        $('#editItemtd').val((parseFloat(obj.detail.sales_incentive_discount_amount)+parseFloat(obj.detail.trade_discount_amount)+parseFloat(obj.detail.consumer_discount_amount))*parseInt(obj.detail.qty));


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
        $(document).on('ifChanged','.editItemchk', function(event) {
            editItemCal();
        });
        $('#editItemQtyTxt').change(function(){
            editItemCal();
        });
        function editItemCal(){
            var qty = parseInt($("#editItemQtyTxt").val());
            var discount1 = parseFloat($('#editItemdone_txt').data('dis')).toFixed(4);
            var discount2 = parseFloat($('#editItemdtwo_txt').data('dis')).toFixed(4);
            var discount3 = parseFloat($('#editItemdth_txt').data('dis')).toFixed(4);
            $('#editItemdone_txt').val(discount1*qty);
            $('#editItemdtwo_txt').val(discount2*qty);
            $('#editItemdth_txt').val(discount3*qty);
            var td1 = 0;
            var td2 = 0;
            var td3 = 0;
            if($("#editItemdone_chk").prop("checked")){td1 = discount1*qty;}
            if($("#editItemdtwo_chk").prop("checked")){td2 = discount2*qty;}
            if($("#editItemdth_chk").prop("checked")){td3 = discount3*qty;}
            $('#editItemtd').val(td1+td2+td3);
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
                url: '<?= admin_url('purchaseorder/updateitem'); ?>',
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
        $('.por_delbtn').click(function(){
            var rid = $(this).data('id');
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            swal({
                title: "Are you sure?",
                text: "Do you want to Delete Reciving!",
                icon: "warning",
                buttons: true,
                successMode: true,
                confirmButtonText: 'Delete',
            })
            .then(id => {
                if (!id) throw null;
                <?php 
                    $url = base_url('admin/purchaseorder/pordelete?id=');
                 ?>
                return fetch("<?php echo $url; ?>"+rid+"&"+[csrfName]+"="+csrfHash);
            })
            .then(results => {
                return results.json();
            })
            .then(json => {
                if(json.codestatus ==  "ok"){
                    swal("Purchase Order Receiving Delete Successfully", {
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
    });
    function receviceitem(id){
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
            url: "<?php echo  base_url('admin/purchaseorder/itemdata'); ?>",
            method: 'post',
            data: { [csrfName]: csrfHash,id:id},
            success: function(data){
                var obj = jQuery.parseJSON(data);
                $('#product_name').val(obj.product_name);
                $('#po_id').val(obj.purchase_id);
                $('#po_id').val(obj.purchase_id);
                $('#po_item_id').val(obj.id);
                $('#order_qty').val(obj.qty);
                $('#receved_qty').val(obj.qty_received);
                $('#unreceived_qty').val(obj.qty-obj.qty_received);
                $('#current_receiving_qty').val(0);
                $('#batch').val('');
                $('#expiry_date').val('');
                $('.modal-backdrop').hide();
                $('#modal-loading').hide();

            }
        });
    }
</script>
