<style>
    .md-card-toolbar-actions ul {
        margin-right:10px;
    }
    .md-card-toolbar-actions ul li i {
        font-size: 14px;
    }
    .uk-nav li>a {
        white-space: pre-wrap;
    }
    .uk-input-group-addon {
        padding: 0 5px !important;
    }
</style>
<div id="page_content">
    <div id="page_content_inner">
    <div class="md-card">
            <div class="md-card-toolbar">
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate">&#xE5D0;</i>
                    <div class="md-card-dropdown" data-uk-dropdown="{pos:'bottom-right',mode:'click'}">
                        <i class="md-icon material-icons">&#xE5D4;</i>
                        <div class="uk-dropdown uk-dropdown-small">
                            <ul class="uk-nav">
                                <li><a href="#" class="addnewitem"> Add Item</a></li>
                                <li><a href="#" class="editsale"> Edit Sale Detail</a></li>
                                <li><a href="#" class="addpayment"> Add Payment</a></li>
                                <?php
                                    if($return_sale){
                                        ?>
                                <li><a href="#" class="si_deletereturn" data-id="<?php echo $return_sale->id; ?>" >Sale Return Delete</a></li>
                                        <?php
                                    }
                                    else{
                                        ?>
                                <li><a href="<?php echo base_url('admin/sales/return_sale/'.$inv->id); ?>">Sale Return</a></li>
                                        <?php
                                    }
                                ?>



                                <!-- <li class="uk-nav-divider"></li> -->
                                <!-- <li><a href="#">View Payments</a></li> -->
                                <!-- <li><a href="#">Add Payments</a></li> -->
                                <!-- <li class="uk-nav-divider"></li> -->
                                <!-- <li><a href="<?php echo base_url('admin/sales/Delivery_challan/'.$inv->id); ?>">DeliveryChallen</a></li> -->
                                <!-- <li><a href="<?php echo base_url('admin/sales/Delivery_challan2/'.$inv->id); ?>">DeliveryChallen (New)</a></li> -->
                                <!-- <li class="uk-nav-divider"></li> -->
                                <!-- <li><a href="#">Sale Tax Invoice 1 (New Trading)</a></li> -->
                                <!-- <li><a href="#">Sale Tax Invoice 1 (New Trading)</a></li> -->
                                <!-- <li><a href="<?php echo base_url('admin/sales/salestaxpdf1/'.$inv->id); ?>">Sale Tax Invoice 1</a></li> -->
                                <!-- <li><a href="<?php echo base_url('admin/sales/salestaxpdf2/'.$inv->id); ?>">Sale Tax Invoice 2</a></li> -->
                                <!-- <li><a href="#">Sale Tax Invoice 2 (New)</a></li> -->
                                <!-- <li><a href="<?php echo base_url('admin/sales/salestaxpdf3/'.$inv->id); ?>">Sale Tax Invoice 3</a></li> -->
                                <!-- <li class="uk-nav-divider"></li> -->
                                <!-- <li><a href="<?php echo base_url('admin/sales/bill_trading/'.$inv->id); ?>">Billing Trading</a></li> -->
                                <!-- <li><a href="<?php echo base_url('admin/sales/bill_with_one_and_two_discount_pdf/'.$inv->id); ?>">Download Bill With 2 Discount</a></li> -->
                                <!-- <li><a href="<?php echo base_url('admin/sales/bill_with_one_two_and_three_discount_pdf/'.$inv->id); ?>">Download Bill With 3 Discount</a></li> -->
                                <!-- <li class="uk-nav-divider"></li> -->
                                <!-- <li><a href="#">Delete Sale</a></li> -->

                            </ul>
                        </div>
                    </div>
                </div>
                <h3 class="md-card-toolbar-heading-text"><?php echo $inv->reference_no?> </h3>
            </div>
            <div class="md-card-content" >
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                            <span class="uk-text-muted uk-text-small uk-text-italic"><b>Customer:</b></span>
                            <address>
                                <p><strong><?php echo $customer->company ?></strong></p>
                                <p><?php echo $customer->address ?></p>
                                <p><?php echo $customer->city.', '.$customer->state.', '.$customer->country ?></p>
                                <?php
                                    if($customer->phone != ""){
                                        echo '<p><b>Phone:</b> '.$customer->phone.'</p>';
                                    }
                                    if($customer->email != ""){
                                        echo '<p><b>Email:</b> '.$customer->email.'</p>';
                                    }
                                    if($customer->cnic != ""){
                                        echo '<p><b>CNIC:</b> '.$customer->cnic.'</p>';
                                    }
                                    if($customer->vat_no != ""){
                                        echo '<p><b>VAT No:</b> '.$customer->vat_no.'</p>';
                                    }
                                    if($customer->cf1 != ""){
                                        echo '<p><b>NtN No:</b> '.$customer->cf1.'</p>';
                                    }
                                    if($customer->gst_no != ""){
                                        echo '<p><b>GST No:</b> '.$customer->gst_no.'</p>';
                                    }
                                ?>
                            </address>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                            <span class="uk-text-muted uk-text-small uk-text-italic"><b>Warehosue:</b></span>
                            <address>
                                <p><strong><?php echo $warehouse->name ?></strong></p>
                                <p><b>Phone:</b> <?php echo $warehouse->phone ?></p>
                                <p><b>Email:</b> <?php echo $warehouse->email ?></p>
                                <p><b>address:</b> <?php echo $warehouse->address ?></p>
                            </address>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                            <span class="uk-text-muted uk-text-small uk-text-italic"><b>Detials:</b></span>
                            <address>
                                <p><b>Reference:</b> <?php echo $inv->reference_no ?></p>
                                <p><b>Date:</b> <?php echo $inv->date ?></p>
                                <p><b>Sale Status:</b> <?php echo $inv->sale_status ?></p>
                                <p><b>Payment Status:</b> <?php echo $inv->payment_status ?></p>
                                <p><b>Due Date:</b> <?php echo $inv->due_date ?></p>
                                <p><b>Created By:</b> <?php echo $created_by->first_name.' '.$created_by->last_name ?></p>
                                <p><b>Created At:</b> <?php echo $inv->created_at ?></p>
                            </address>
                        </div>
                    </div>
                </div>
                <h4 class="table_heading" >Items List</h4>
                <div class="dt_colVis_buttons"></div>
                <table id="itemsTable" class="uk-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Product ID</th>
                            <th>Barcode</th>
                            <th style="width:150px">Name</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Tax</th>
                            <th>Advance Income Tax</th>
                            <th>Further Tax</th>
                            <th>Discount</th>
                            <th>Subtotal</th>
                            <th class="dt-no-export" >Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($rows as $key => $row){
                                ?>
                                <tr>
                                    <td><?php echo $key+1 ?></td>
                                    <td><?php echo $row->product_id?></td>
                                    <td><?php echo $row->product_code?></td>
                                    <td><?php echo $row->product_name?></td>
                                    <td><?php echo $row->quantity?></td>
                                    <td><?php echo $row->unit_price?></td>
                                    <td><?php echo $row->item_tax != "" ? '<small>('.($Settings->indian_gst ? $row->tax : $row->tax_code).')</small>' : '' ?></td>
                                    <td><?php echo $row->adv_tax?></td>
                                    <td><?php echo $row->further_tax?></td>
                                    <td><?php echo ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) ?></td>
                                    <td><?php echo $row->subtotal?></td>
                                    <td>
                                        <button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini si_editbtn" type="button" data-id="<?php echo $row->id; ?>" data-product="<?php echo $row->product_id; ?>" data-qty="<?php echo $row->quantity; ?>" data-panem="<?php echo $row->product_name; ?>" >Edit</button>
                                        <button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini si_deletebtn" type="button" data-id="<?php echo $row->id; ?>" >Delete</button>
                                    </td>
                                </tr>
                                <?php
                            }
                        ?>
                    </tbody>
                </table>
                <?php
                    if($return_sale){
                        ?>
                        
                        <h4 class="table_heading" >Return Items List</h4>
                        <div class="dt_colVis_buttons"></div>
                        <table id="itemsTable2" class="uk-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Product ID</th>
                                    <th>Barcode</th>
                                    <th style="width:150px">Name</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Tax</th>
                                    <th>Advance Income Tax</th>
                                    <th>Further Tax</th>
                                    <th>Discount</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach($return_rows as $key => $row){
                                        ?>
                                        <tr>
                                            <td><?php echo $key+1 ?></td>
                                            <td><?php echo $row->product_id?></td>
                                            <td><?php echo $row->product_code?></td>
                                            <td><?php echo $row->product_name?></td>
                                            <td><?php echo $row->quantity?></td>
                                            <td><?php echo $row->net_unit_price?></td>
                                            <td><?php echo $row->item_tax ?></td>
                                            <td><?php echo 0 ?></td>
                                            <td><?php echo $row->further_tax?></td>
                                            <td><?php echo $row->total_discount?></td>
                                            <td><?php echo $row->subtotal?></td>
                                        </tr>
                                        <?php
                                    }
                                ?>
                            </tbody>
                        </table>
                        <?php
                    }
                ?>
                <?php
                ?>
                <table class="uk-table" style="max-width: 300px;float: right;">
                    <tfoot>
                        <tr>
                            <th style="text-align:right" >Total Amount</th>
                            <td><?php echo $inv->total ?></td>
                        </tr>
                        <tr>
                            <th style="text-align:right" >Order Discount</th>
                            <td><?php echo $inv->order_discount ?></td>
                        </tr>
                        <tr>
                            <th style="text-align:right" >Charges</th>
                            <td><?php echo $inv->shipping ?></td>
                        </tr>
                        <?php
                            $return_amount = 0;
                            if(isset($return_sale->total)){
                                $return_amount = $return_sale->total;
                                ?>
                                <tr>
                                    <th style="text-align:right" >Sale Return</th>
                                    <td><?php echo $return_sale->total ?></td>
                                </tr>
                                <?php
                                
                            }
                        ?>
                        <tr>
                            <th style="text-align:right" >Net Amount </th>
                            <td><?php echo $inv->grand_total-$return_amount ?></td>
                        </tr>
                        <tr>
                            <th style="text-align:right" >Paid Amount </th>
                            <td><?php echo $inv->paid ?></td>
                        </tr>
                        <tr>
                            <th style="text-align:right" >Balance  </th>
                            <td><?php echo $inv->grand_total-$inv->paid-$return_amount ?></td>
                        </tr>
                    </tfoot>
                </table>
                <div style="clear:both"></div>
                <h4 class="table_heading" >Payments</h4>
                <div class="dt_colVis_buttons"></div>
                <table id="itemsTable2" class="uk-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Payment Reference</th>
                            <th>Paid By</th>
                            <th>Amount</th>
                            <th>Created By</th>
                            <th>Type</th>
                            <th class="dt-no-export" >Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if($payments == ""){
                                $payments =array(); 
                            }
                            foreach($payments as $key => $payment){
                                ?>
                                <tr>

                                    <td><?= $this->sma->hrld($payment->date) ?></td>
                                    <td><?= $payment->reference_no; ?></td>
                                    <td><?= lang($payment->paid_by);
                                        if ($payment->paid_by == 'gift_card' || $payment->paid_by == 'CC') {
                                            echo ' (' . $payment->cc_no . ')';
                                        } elseif ($payment->paid_by == 'Cheque') {
                                            echo ' (' . $payment->cheque_no . ')';
                                        }
                                        ?></td>
                                    <td><?= $this->sma->formatMoney($payment->amount); ?></td>
                                    <td><?= $payment->first_name . ' ' . $payment->last_name; ?></td>
                                    <td><?= lang($payment->type); ?></td>
                                    <td>
                                        <button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini paymentedit" type="button" data-id="<?php echo $payment->id; ?>" >Edit</button>
                                        <button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini paymentdelete" type="button" data-id="<?php echo $payment->id; ?>" >Delete</button>
                                    </td>
                                </tr>
                                <?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="uk-modal" id="modal_newitem">
    <?php
        $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'addItemForm');
        echo admin_form_open_multipart("#", $attrib);
    ?>
        <div class="uk-modal-dialog">
            <div class="uk-modal-header">
                <h3 class="uk-modal-title">Add New Item</h3>
            </div>
            <div class="uk-modal-body">
                <div class="uk-grid">
                    <div class="uk-width-large-1-1" style="margin-top: 0px;">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Product <span class="red" >*</span></label>
                            <input type="hidden" name="sid" value="<?= $inv->id ?>" >
                            <select name="product" class="uk-width-1-1 product_searching" style="width: 100%" id="product">
                                <option value="">Select Product</option>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Batch <span class="red" >*</span></label>
                            <select name="batch" class="uk-width-1-1" style="width: 100%" id="addsi_batch">
                                <option value="">Select Batch</option>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Quantity</label>
                            <input type="number" name="qty" value="1" id="qty" class="md-input md-input-success label-fixed">
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Net Unit Cost</label>
                            <input type="text" name="net_unit_cost" value="0" id="net_unit_cost" class="md-input md-input-success label-fixed" readonly>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="md-input-wrapper md-input-filled">
                            <label>MRP</label>
                            <input type="text" name="mrp" value="0" id="mrp" class="md-input md-input-success label-fixed" readonly>
                        </div>
                    </div>
                    <div class="uk-width-large-1-4 uk-width-medium-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Discount 1</label>
                            <div class="uk-input-group">
                                <span class="uk-input-group-addon"><input type="checkbox" data-md-icheck name="done_chk" class="addItemchk" id="done_chk" value="0"/></span>
                                <input type="text" class="md-input md-input-success label-fixed" name="done_txt" id="done_txt" readonly value="0" />
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-large-1-4 uk-width-medium-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Discount 2</label>
                            <div class="uk-input-group">
                                <span class="uk-input-group-addon"><input type="checkbox" data-md-icheck name="dtwo_chk" class="addItemchk" id="dtwo_chk" value="0"/></span>
                                <input type="text" class="md-input md-input-success label-fixed" name="dtwo_txt" id="dtwo_txt" readonly value="0" />
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-large-1-4 uk-width-medium-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Discount 3</label>
                            <div class="uk-input-group">
                                <span class="uk-input-group-addon"><input type="checkbox" data-md-icheck name="dth_chk" class="addItemchk" id="dth_chk" value="0"/></span>
                                <input type="text" class="md-input md-input-success label-fixed" name="dth_txt" id="dth_txt" readonly value="0" />
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-large-1-4">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Bulk Discount <span class="red" >*</span></label>
                            <select name="bulkdiscount" class="uk-width-1-1" style="width: 100%" id="addsi_bulkdiscount">
                            </select>
                        </div>
                    </div>

                    <div class="uk-width-large-1-3">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Product Tax</label>
                            <input type="number" name="producttax" value="0" id="producttax" class="md-input md-input-success label-fixed" readonly>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Further Tax</label>
                            <input type="number" name="further_tax" value="0" id="further_tax" class="md-input md-input-success label-fixed" readonly>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="md-input-wrapper md-input-filled">
                            <label>FED Tax</label>
                            <input type="number" name="fed" value="0" id="fed" class="md-input md-input-success label-fixed" readonly>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Advance Tax</label>
                            <input type="number" name="advtax" value="0" id="advtax" class="md-input md-input-success label-fixed" readonly>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Total Discount</label>
                            <input type="number" name="td" value="0" id="td" class="md-input md-input-success label-fixed" readonly>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Sub Total</label>
                            <input type="number" name="total" value="0" id="addtotal" class="md-input md-input-success label-fixed" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-modal-footer uk-text-right">
                <button type="submit" class="md-btn md-btn-success md-btn-flat submitbtn" >Submit</button>
                <button type="button" class="md-btn md-btn-flat uk-modal-close" >Close</button>
            </div>
        </div>
    <?php echo form_close(); ?>
</div>
<div class="uk-modal" id="modal_edititem">
    <?php
        $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'editItemForm');
        echo admin_form_open_multipart("#", $attrib);
    ?>
        <div class="uk-modal-dialog">
            <div class="uk-modal-header">
                <h3 class="uk-modal-title">Edit Sale Item</h3>
                <input type="hidden" name="id" id="ediItemId">
            </div>
            <div class="uk-modal-body">
                <div class="uk-grid">
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Batch <span class="red" >*</span></label>
                            <select name="batch" class="uk-width-1-1" style="width: 100%" id="editsi_batch">
                                <option value="">Select Batch</option>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Quantity</label>
                            <input type="number" name="qty" value="0" id="editItemQtyTxt" class="md-input md-input-success label-fixed">
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Price</label>
                            <input type="number" name="price" value="0" id="ediItemPrice" class="md-input md-input-success label-fixed" readonly>
                        </div>
                    </div>
                    <div class="uk-width-large-1-2 uk-width-medium-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Discount 1</label>
                            <div class="uk-input-group">
                                <span class="uk-input-group-addon"><input type="checkbox" data-md-icheck name="done_chk" class="editItemchk" id="editItemdone_chk" value="0"/></span>
                                <input type="text" class="md-input md-input-success label-fixed" name="done_txt" id="editItemdone_txt" readonly value="0" />
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-large-1-2 uk-width-medium-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Discount 2</label>
                            <div class="uk-input-group">
                                <span class="uk-input-group-addon"><input type="checkbox" data-md-icheck name="dtwo_chk" class="editItemchk" id="editItemdtwo_chk" value="0"/></span>
                                <input type="text" class="md-input md-input-success label-fixed" name="dtwo_txt" id="editItemdtwo_txt" readonly value="0" />
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-large-1-2 uk-width-medium-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Discount 3</label>
                            <div class="uk-input-group">
                                <span class="uk-input-group-addon"><input type="checkbox" data-md-icheck name="dth_chk" class="editItemchk" id="editItemdth_chk" value="0"/></span>
                                <input type="text" class="md-input md-input-success label-fixed" name="editItemdth_txt" id="dth_txt" readonly value="0" />
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Bulk Discount <span class="red" >*</span></label>
                            <select name="editbulkdiscount" class="uk-width-1-1" style="width: 100%" id="editsi_bulkdiscount">
                            </select>
                        </div>
                    </div>

                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Reason <span style="color:red">*</span></label>
                            <input type="text" name="reason" class="md-input md-input-success label-fixed" >
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-modal-footer uk-text-right">
                <button type="submit" class="md-btn md-btn-success md-btn-flat" id="updateItemBtn" >Submit</button>
                <button type="button" class="md-btn md-btn-flat uk-modal-close" >Close</button>
            </div>
        </div>
    <?php echo form_close(); ?>
</div>
<div class="uk-modal" id="modal_editsale">
    <?php
        $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'editForm');
        echo admin_form_open_multipart("#", $attrib);
    ?>
        <div class="uk-modal-dialog">
            <div class="uk-modal-header">
                <h3 class="uk-modal-title">Edit Sale Detial</h3>
            </div>
            <div class="uk-modal-body">
                <div class="uk-grid">
                    <input type="hidden" name="id" value="<?= $inv->id ?>" >
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Invoice Number <span class="red" >*</span></label>
                            <input type="text" name="reference_no" class="md-input md-input-success label-fixed" required value="<?= $inv->reference_no ?>">
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label for="uk_dp_1">Sale Date </label>
                            <input class="md-input  label-fixed" type="text" name="date" id="uk_dp_1" data-uk-datepicker="{format:'YYYY-MM-DD'}" autocomplete="off" readonly value="<?php echo $inv->date; ?>" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label for="uk_dp_1">PO Date </label>
                            <input class="md-input  label-fixed" type="text" name="po_date" id="uk_dp_1" data-uk-datepicker="{format:'YYYY-MM-DD'}" autocomplete="off" readonly value="<?php echo $inv->po_date; ?>" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>DC Number </label>
                            <input type="text" name="dc_number" class="md-input md-input-success label-fixed" value="<?= $inv->dc_num ?>">
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>PO Number </label>
                            <input type="text" name="po_number" class="md-input md-input-success label-fixed" value="<?= $inv->po_number ?>">
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Own Company <span class="red" >*</span></label>
                            <select name="own_company" class="uk-width-1-1 select2" id="owncompanies">
                                <?php
                                foreach ($own_company as $own_companies) {
                                    echo '<option value="'.$own_companies->id.'" '; if($inv->own_company == $own_companies->id){ echo 'selected'; } echo' >'.$own_companies->companyname.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Biller <span class="red" >*</span></label>
                            <select name="biller_id" class="uk-width-1-1 select2" id="owncompanies">
                                <?php
                                foreach ($billerslist as $billerlist) {
                                    echo '<option value="'.$billerlist->id.'" '; if($inv->biller_id == $billerlist->id){ echo 'selected'; } echo' >'.$billerlist->name.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>E-taliers <span class="red" >*</span></label>
                            <select name="etaliers" class="uk-width-1-1 select2" id="etaliers">
                                <?php
                                foreach ($lcustomers as $lcustomer) {
                                    echo '<option value="'.$lcustomer->id.'" '; if($inv->etalier_id == $lcustomer->id){ echo 'selected'; } echo' >'.$lcustomer->company.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Order Discount <span class="red" >*</span></label>
                            <input type="text" name="discount" class="md-input md-input-success label-fixed" required value="<?= $inv->order_discount ?>">
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Shipping <span class="red" >*</span></label>
                            <input type="text" name="shipping" class="md-input md-input-success label-fixed" required value="<?= $inv->shipping ?>">
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Delivery Address <span class="red" >*</span></label>
                            <select name="deliveryaddress" class="uk-width-1-1 select2" id="deliveryaddress">
                                <option value="0">Default Address</option>
                                <?php
                                foreach ($addresslist as $address) {
                                    echo '<option value="'.$address->id.'" '; if($inv->customer_address_id == $address->id){ echo 'selected'; } echo' >'.$address->line1.' '.$address->line2.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Payment Term</label>
                            <input type="text" name="payment_term" class="md-input md-input-success label-fixed" value="<?= $inv->payment_terms ?>">
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Sale Note </label>
                            <textarea rows="6" class="md-input autosized" style="overflow-x: auto; overflow-wrap: break-word; max-height: 150px;" name="note" ><?php echo $inv->note; ?></textarea>
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Staff Note </label>
                            <textarea rows="6" class="md-input autosized" style="overflow-x: auto; overflow-wrap: break-word; max-height: 150px;" name="staff_note" ><?php echo $inv->staff_note; ?></textarea>
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Reason <span class="red" >*</span></label>
                            <input type="text" name="reason" class="md-input md-input-success label-fixed" required>
                        </div>
                    </div>






                </div>
            </div>
            <div class="uk-modal-footer uk-text-right">
                <button type="submit" class="md-btn md-btn-success md-btn-flat" >Submit</button>
                <button type="button" class="md-btn md-btn-flat uk-modal-close" >Close</button>
            </div>
        </div>
    <?php echo form_close(); ?>
</div>



<!-- datatables -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<!-- datatables buttons-->
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/dataTables.buttons.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/custom/datatables/buttons.uikit.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/jszip/dist/jszip.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/pdfmake/build/pdfmake.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/pdfmake/build/vfs_fonts.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.colVis.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.html5.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.print.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-fixedcolumns/dataTables.fixedColumns.min.js"></script>
<!-- datatables custom integration -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/custom/datatables/datatables.uikit.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/datatable.js"></script>

<script>
    $.DataTableInit2({
        selector:'#itemsTable',
        aaSorting: [[0, "asc"]],
        columnDefs: [
            { 
                "targets": 11,
                "orderable": false
            }
        ],
        fixedColumns:   {left: 0,right: 1},
        scrollX: true,
        paging: false,
        info: true,
        searching: false,

    });
</script>
<script>
    var csrfName = "<?php echo $this->security->get_csrf_token_name(); ?>",
        csrfHash = "<?php echo $this->security->get_csrf_hash(); ?>";
    var data = [];
    data[csrfName] = csrfHash;
    data['id'] = '<?php echo $inv->id; ?>';
    $(document).ready(function(){
        $(document).on('click','.addnewitem',function(){
            UIkit.modal('#modal_newitem').show();
        });
        $(document).on('click','.editsale',function(){
            UIkit.modal('#modal_editsale').show();
        });
        $('#product').change(function(){
            var pid = $(this).val();
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            $.ajax({
                type: "post",
                url: '<?= admin_url('sales/batches'); ?>',
                data: {
                    'pid': pid,
                    'wid':<?php echo $inv->warehouse_id; ?>,
                    'sid':<?php echo $inv->supplier_id; ?>,
                    [csrfName]:csrfHash,
                },
                success: function(data) {
                    var obj = jQuery.parseJSON(data);

                    $('#addsi_batch').html(obj.htmlbatchs);
                    $('#addsi_bulkdiscount').html(obj.htmldiscount);
                }
            });
        });
        $(".product_searching").select2({
            minimumInputLength: 2,
            tags: [],
            ajax: {
                url: "<?php echo base_url('admin/general/searching_products2'); ?>",
                dataType: 'json',
                type: "GET",
                quietMillis: 50,
                data: function (term) {
                    return {
                        term: term,
                        supplier_id: <?php echo $inv->supplier_id; ?>,
                        warehouse_id: <?php echo $inv->warehouse_id; ?>,
                        own_company: 0,
                        limit: 15
                    };
                },
                results: function (data) {
                    console.log(data);
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.completeName,
                                slug: item.slug,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });
        $('#addsi_batch').change(function(){
            filladdform();
        });
        $('#qty').change(function(){
            filladdform();
        });
        $(document).on('ifChanged','#done_chk,#dtwo_chk,#dth_chk', function(event) {
            filladdform();
        });

        function filladdform(){
            var pid = $('#product').val();
            var qty = $('#qty').val();
            var wid = <?php echo $inv->warehouse_id; ?>;
            var cid = <?php echo $inv->customer_id; ?>;
            var batch = $('#addsi_batch').val();
            var bulkdiscount = $('#addsi_bulkdiscount').val();
            var cd1 = 'no';
            var cd2 = 'no';
            var cd3 = 'no';
            if($("#done_chk").prop("checked")){
                cd1 = 'yes';
            }
            if($("#dtwo_chk").prop("checked")){
                cd2 = 'yes';
            }
            if($("#dth_chk").prop("checked")){
                cd3 = 'yes';
            }
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            if(batch != ""){
                $.ajax({
                    type: "post",
                    url: '<?= admin_url('sales/productdetail'); ?>',
                    data: {
                        'pid': pid,
                        'qty': qty,
                        'cd1': cd1,
                        'cd2': cd2,
                        'cd3': cd3,
                        'wid': wid,
                        'cid': cid,
                        'batch': batch,
                        'bulkdiscount': bulkdiscount,
                        [csrfName]:csrfHash,
                    },
                    success: function(data) {
                        var obj = jQuery.parseJSON(data);
                        $("#net_unit_cost").val(obj.detail.sellingprice);
                        $("#mrp").val(obj.detail.mrp);
                        $("#done_chk").val(obj.detail.d1);
                        $("#dtwo_chk").val(obj.detail.d2);
                        $("#done_txt").val(obj.detail.d1a.toFixed(4));
                        $("#dtwo_txt").val(obj.detail.d2a.toFixed(4));
                        $("#dth_chk").val(obj.detail.d3);
                        $("#dth_txt").val(obj.detail.d3a.toFixed(4));
                        $("#td").val(obj.detail.d.toFixed(4));
                        $("#fed").val(obj.detail.fedtax);
                        $("#further_tax").val(obj.detail.further_tax);
                        $("#advtax").val(obj.detail.adv_tax);
                        $("#producttax").val(obj.detail.tax.toFixed(4));
                        $("#addtotal").val(obj.detail.subtotal.toFixed(4));
                    }
                });
            }
            else{
                $("#net_unit_cost").val(0);
                $("#mrp").val(0);
                $("#qty").val(0);

                $("#done_chk").val(0);
                $("#dtwo_chk").val(0);
                $("#dth_chk").val(0);

                $("#done_txt").val(0);
                $("#dtwo_txt").val(0);
                $("#dth_txt").val(0);

                $("#td").val(0);
                $("#fed").val(0);
                $("#producttax").val(0);
                $("#addtotal").val(0);

            }

        }
        $("#addItemForm").submit(function(e){
             e.preventDefault();
             $(':input[type="submit"]').prop('disabled', true);
             $.ajax({
                url: '<?= admin_url('sales/additem'); ?>',
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
        $('.si_deletebtn').click(function(){
            var iid = $(this).data('id');
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to delete this item!",
                icon: "warning",
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Delete',
                showLoaderOnConfirm: true,
                preConfirm: (reason) => {
                    return fetch(`<?= base_url('admin/sales/itemdelete?id=') ?>${iid}&reason=${reason}&[${csrfName}]=${csrfHash}`)
                    .then(response => {
                        console.log(response);
                        if (!response.ok) {
                            console.log('Error');
                            throw new Error(response.statusText)
                        }
                        else if(reason == ""){
                            throw new Error('Enter Reason')
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        )
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            })
            .then((result) => {
                console.log('CR: '+result);
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Item Delete Successfully',
                        showConfirmButton: false,
                        timer: 10000
                    });
                    setTimeout(function(){ 
                        location.reload();
                    }, 1000);
                }
            });
        });
        $('#addsi_bulkdiscount').change(function(){
            filladdform();
        });
        // Edit Sale Item
        function editcol(){
            var price = $('#ediItemPrice').val();
            var qty = $('#editItemQtyTxt').val();
            var pd1 = $('#editItemdone_chk').val();
            var pd2 = $('#editItemdtwo_chk').val();
            var pd3 = $('#editItemdth_chk').val();
            $('#editItemdone_txt').val((((price/100)*pd1)*qty).toFixed(4));
            $('#editItemdtwo_txt').val((((price/100)*pd2)*qty).toFixed(4));
            $('#editItemdth_txt').val((((price/100)*pd3)*qty).toFixed(4));
        }
        $('#ediItemPrice').change(function(){
            editcol();
        });
        $('#editItemQtyTxt').change(function(){
            editcol();
        });
        $('#editsi_batch').change(function(){
            var price = $(this).find(':selected').data('price');
            console.log(price);
            $('#ediItemPrice').val(price);
            editcol();
        });
        $('.si_editbtn').click(function(){
            var id = $(this).data('id');
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            $.ajax({
                url: '<?= admin_url('sales/itemdetail'); ?>',
                data: {
                    'id':id,
                    [csrfName]:csrfHash,
                },
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    if(obj.codestatus == "ok"){
                        $('#editItemQtyTxt').val(obj.detail.quantity);
                        $('#editsi_batch').html(obj.htmlbatchs);
                        $('#editsi_bulkdiscount').html(obj.htmldiscount);
                        $('#ediItemPrice').val(obj.detail.net_unit_price);
                        $('#ediItemId').val(obj.detail.id);
                        $('#editItemdone_chk').val(obj.detail.pd1 == null ? 0 : obj.detail.pd1);
                        $('#editItemdtwo_chk').val(obj.detail.pd2 == '' ? 0 : obj.detail.pd2);
                        $('#editItemdth_chk').val(obj.detail.pd3 == '' ? 0 : obj.detail.pd3);
                        if(obj.detail.discount_one!="" && obj.detail.discount_one!= 0 && obj.detail.discount_one!= null){
                            $('#editItemdone_chk').iCheck('check');
                        }
                        else{
                            $('#editItemdone_chk').iCheck('uncheck');
                        }
                        if(obj.detail.discount_two!="" && obj.detail.discount_two!= 0 && obj.detail.discount_two!= null){
                            $('#editItemdtwo_chk').iCheck('check');
                        }
                        else{
                            $('#editItemdtwo_chk').iCheck('uncheck');
                        }
                        if(obj.detail.discount_three!="" && obj.detail.discount_three!= 0 && obj.detail.discount_three!= null){
                            $('#editItemdth_chk').iCheck('check');
                        }
                        else{
                            $('#editItemdth_chk').iCheck('uncheck');
                        }
                        // $('#editItemdone_txt').val(obj.detail.pd1 == null ? 0 : obj.detail.pd1);
                        // $('#editItemdtwo_txt').val(obj.detail.pd2 == '' ? 0 : obj.detail.pd2);
                        // $('#editItemdth_txt').val(obj.detail.pd3 == '' ? 0 : obj.detail.pd3);
                        editcol();
                        UIkit.modal('#modal_edititem').show();
                    }
                    else{
                        alert(obj.codestatus);
                        UIkit.modal('#modal_edititem').hide();
                    }
                },
                error: function(){
                    alert('Try Again!');
                }

            });
        });
        $('#editItemForm').submit(function(e){
            e.preventDefault();
            $('#updateItemBtn').prop('disabled', true);
            $.ajax({
                url: '<?= admin_url('sales/updateitem'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    $('#updateItemBtn').prop('disabled', false);
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
                }
            });
        });
        $(document).on('click','.addpayment',function(){
            $('#modal_ajax .uk-modal-dialog').html("");
            $.ajax({
                url: '<?php echo base_url('admin/sales/add_payment'); ?>',
                type: 'POST',
                data: {[csrfName]:csrfHash,id:<?php echo $inv->id; ?>},
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    if(obj.status){
                        $('#modal_ajax .uk-modal-dialog').html(obj.html);
                        UIkit.modal('#modal_ajax').show();
                    }
                    else{
                        toastr.error(obj.message);
                    }
                }
            });
        });
        $(document).on('click','.paymentedit',function(){
            $('#modal_ajax .uk-modal-dialog').html("");
            var id = $(this).data('id');
            $.ajax({
                url: '<?php echo base_url('admin/sales/edit_payment'); ?>',
                type: 'POST',
                data: {[csrfName]:csrfHash,id:id},
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    if(obj.status){
                        $('#modal_ajax .uk-modal-dialog').html(obj.html);
                        UIkit.modal('#modal_ajax').show();
                    }
                    else{
                        toastr.error(obj.message);
                    }
                }
            });
        });
        $(document).on('click','.paymentdelete',function(){
            var id = $(this).data('id');
            Swal.fire({
                title: "Do you want to delete this payment",
                input: "text",
                showCancelButton: true,
                confirmButtonColor: "#e53935",
                confirmButtonText: "Delete",
                cancelButtonText: "Cancel",
                buttonsStyling: true
            }).then(function (res) {
                console.log(res);
                if(res.isConfirmed){
                    Swal.fire({
                        title: 'Deleting payment!',
                        showCancelButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            $.ajax({
                                url: '<?php echo base_url('admin/sales/delete_payment/'); ?>'+id,
                                type: 'POST',
                                data: {[csrfName]:csrfHash,id:id,reason:res.value},
                                success: function(data) {
                                    location.reload();
                                }
                            });
                        }
                    });
                }
            })
        });
        $('#updateBtn').click(function(){
            $('#editForm').submit();    
        });
        $('#editForm').submit(function(e){
            e.preventDefault();
            $('#updateBtn').prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('sales/salesedit'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    $('#updateBtn').prop('disabled', false);
                    var obj = jQuery.parseJSON(data);
                    $('#ajaxCall').hide();
                    
                    if(obj.codestatus == 'ok'){
                        alert('Update Successfully');
                        location.reload();
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
                    $('#updateBtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                }
            });

        });

        $('.si_deletereturn').click(function(){
            var iid = $(this).data('id');
            var csrfName = 'token',
            csrfHash = 'fb7e3b4410bdecef196f96ff088cf914';

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to delete this return!",
                icon: "warning",
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Delete',
                showLoaderOnConfirm: true,
                preConfirm: (reason) => {
                    console.log(reason);
                    $.ajax({
                        type: "get",
                        url: '<?php echo base_url("admin/sales/returndelete"); ?>',
                        data: {
                            'id': iid,
                            'reason': reason,
                            [csrfName]:csrfHash
                        },
                        success: function(data) {
                            var obj = jQuery.parseJSON(data);
                            if(obj.codestatus == "Return sale deleted"){
                                Swal.fire({
                                    title: obj.codestatus ,
                                    icon: "success",
                                });
                                location.reload();
                            }
                            else{
                                Swal.fire({
                                    title: obj.codestatus,
                                    icon: "error",
                                });
                            }
                            throw new Error(obj.codestatus);
                        },
                        error: function(jqXHR, textStatus){
                            var errorStatus = jqXHR.status;
                            Swal.fire({
                                title: errorStatus,
                                icon: "error",
                            });
                            throw new Error(errorStatus);
                        }
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            })
            .then((result) => {
                console.log('CR: '+JSON.stringify(result));
                if (result.isConfirmed) {
                    // Swal.fire({
                    //     icon: 'success',
                    //     title: 'Return Delete Successfully',
                    //     showConfirmButton: false,
                    //     timer: 10000
                    // });
                    // setTimeout(function(){ 
                    //     location.reload();
                    // }, 1000);
                }
            });
        });


    });
</script>



