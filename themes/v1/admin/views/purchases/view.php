<style>
    .md-card-toolbar-actions ul {
        margin-right:10px;
    }
    .md-card-toolbar-actions ul li i {
        font-size: 14px;
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
                                <!-- <li><a href="#" class="addnewitem"> Edit Purchase Detail</a></li> -->
                                <li><a href="#" class="addnewitem"> Add Item</a></li>
                                <li><a href="#" class="addnewbulkitem"> Add Bulk Item</a></li>
                                <li><a href="#" class="addpayment"> Add Payment</a></li>
                                <li><a href="<?php echo base_url('admin/purchases/pdf/'.$purchase->id); ?>">  Purchase PDF</a></li>
                                <li><a href="<?php echo base_url('admin/purchases/purchase_return/'.$purchase->id); ?>"> Purchase Return</a></li>
                                <li><a href="<?php echo base_url('admin/purchases/pdf_return/'.$purchase->id); ?>">  Purchase Return PDF</a></li>
                                <li><a href="#" id="deletePurchase"> Delete Purchase</a></li>
                                <li><a href="#" id="revicedPurchase"> Revice Purchase Amount</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <h3 class="md-card-toolbar-heading-text"><?php echo $purchase->reference_no?> </h3>
            </div>
            <div class="md-card-content" >
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                            <span class="uk-text-muted uk-text-small uk-text-italic"><b>Supplier:</b></span>
                            <address>
                                <p><strong><?php echo $purchase->company ?></strong></p>
                                <p><?php echo $purchase->address ?></p>
                                <p><?php echo $purchase->city.', '.$purchase->state.', '.$purchase->country ?></p>
                                <p><b>Phone:</b> <?php echo $purchase->phone ?></p>
                                <p><b>Email:</b> <?php echo $purchase->email ?></p>
                                <?php
                                    if($purchase->cnic != ""){
                                        echo '<p><b>CNIC:</b> '.$purchase->cnic.'</p>';
                                    }
                                    if($purchase->vat_no != ""){
                                        echo '<p><b>VAT No:</b> '.$purchase->vat_no.'</p>';
                                    }
                                    if($purchase->cf1 != ""){
                                        echo '<p><b>NtN No:</b> '.$purchase->cf1.'</p>';
                                    }
                                    if($purchase->gst_no != ""){
                                        echo '<p><b>GST No:</b> '.$purchase->gst_no.'</p>';
                                    }
                                ?>
                            </address>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                            <span class="uk-text-muted uk-text-small uk-text-italic"><b>Warehosue:</b></span>
                            <address>
                                <p><strong><?php echo $purchase->warehosue_name ?></strong></p>
                                <p><?php echo $purchase->address ?></p>
                                <p><b>Phone:</b> <?php echo $purchase->warehosue_phone ?></p>
                                <p><b>Email:</b> <?php echo $purchase->warehosue_email ?></p>
                            </address>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                            <span class="uk-text-muted uk-text-small uk-text-italic"><b>Detials:</b></span>
                            <address>
                            <p><b>Reference No:</b> <?php echo $purchase->reference_no ?></p>
                                <p><b>Date:</b> <?php echo $purchase->date ?></p>
                                <p><b>Payment Status:</b> <?php echo $purchase->payment_status ?></p>
                                <p><b>Status:</b> <?php echo $purchase->status ?></p>
                                <p><b>Created By:</b> <?php echo $purchase->created_by ?></p>
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
                            <th>Name</th>
                            <th>Barcode</th>
                            <th>Quantity</th>
                            <th>Unit Cost</th>
                            <th>MRP</th>
                            <th>Expiry</th>
                            <th>Batch</th>
                            <th>Tax Type</th>
                            <th>Product Tax</th>
                            <th>Advance Income Tax</th>
                            <th>Discount</th>
                            <th>Subtotal</th>
                            <th class="dt-no-export" >Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <table class="uk-table" style="max-width: 300px;float: right;">
                    <tfoot>
                        <tr>
                            <th style="text-align:right" >Total Amount</th>
                            <td><?php echo $purchase->grand_total ?></td>
                        </tr>
                        <tr>
                            <th style="text-align:right" >Paid</th>
                            <td><?php echo $purchase->paid ?></td>
                        </tr>
                        <tr>
                            <th style="text-align:right" >Balance</th>
                            <td><?php echo $purchase->grand_total-$purchase->paid ?></td>
                        </tr>
                    </tfoot>
                </table>
                <div style="clear:both"></div>
                <?php
                if ($return_rows) {
                    ?>
                    <h4 class="table_heading" style="height: 40px;">Return Items List <button type="button" class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light" id="deletePurchaseReturn" data-id="<?php echo $purchase->id; ?>" style="float: right;">Delete Return Items</button></h4>
                    <div class="dt_colVis_buttons"></div>
                    <table id="itemsTable2" class="uk-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Product ID</th>
                                <th>Name</th>
                                <th>Barcode</th>
                                <th>Quantity</th>
                                <th>Unit Cost</th>
                                <th>MRP</th>
                                <th>Expiry</th>
                                <th>Batch</th>
                                <th>Tax Type</th>
                                <th>Product Tax</th>
                                <th>Advance Income Tax</th>
                                <th>Discount</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach ($return_rows as $key => $row){
                                    ?>
                                    <tr>
                                        <td><?php echo $key++; ?></td>
                                        <td><?php echo $row->product_id; ?></td>
                                        <td><?php echo $row->product_name; ?></td>
                                        <td><?php echo $row->product_code; ?></td>
                                        <td><?php echo $row->quantity; ?></td>
                                        <td><?php echo $row->net_unit_cost; ?></td>
                                        <td><?php echo $row->mrp; ?></td>
                                        <td><?php echo $row->expiry; ?></td>
                                        <td><?php echo $row->batch; ?></td>
                                        <td><?php if($row->tax_rate == 0){ echo 'Exempt'; }else{ if($row->tax_type == 1){ echo 'GST'; }else{ echo '3rd Schedule'; } } ?></td>
                                        <td><?php echo $row->item_tax; ?></td>
                                        <td><?php echo $row->adv_tax; ?></td>
                                        <td><?php echo $row->total_discount; ?></td>
                                        <td><?php echo $row->subtotal; ?></td>
                                    </tr>
                                    <?php
                                }
                            ?>
                        </tbody>
                    </table>
                    <?php
                }
                
                ?>
                <h4 class="table_heading" >Payments List</h4>
                <table class="uk-table" >
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reference No</th>
                            <th>Amount</th>
                            <th>Paid By</th>
                            <th class="dt-no-export" >Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($payments as $payment){
                                ?>
                                <tr>
                                    <td><?php echo $payment->date; ?></td>
                                    <td><?php echo $payment->reference_no; ?></td>
                                    <td><?php echo $payment->amount; ?></td>
                                    <td><?php echo $payment->paid_by; ?></td>
                                    <td>
                                        <button type="button" class="md-btn md-btn-warning md-btn-flat paymentedit" data-id="<?php echo $payment->id; ?>" >Edit</button>
                                        <button type="button" class="md-btn md-btn-danger md-btn-flat paymentdelete" data-id="<?php echo $payment->id; ?>" >Delete</button>
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
        $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'additem');
        echo admin_form_open_multipart("#", $attrib);
    ?>
        <div class="uk-modal-dialog">
            <div class="uk-modal-header">
                <h3 class="uk-modal-title">Add New Item</h3>
            </div>
            <div class="uk-modal-body">
                <div class="uk-grid">
                    <div class="uk-width-large-1-1" style="margin-top: 15px;">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Product <span class="red" >*</span></label>
                            <input type="hidden" name="pid" value="<?php echo $purchase->id; ?>">
                            <select name="product" class="uk-width-1-1 product_searching" style="width: 100%">
                                <option value="">Select Product</option>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Batch <span class="red" >*</span></label>
                            <input type="text" name="batch" class="md-input md-input-success label-fixed" required >
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Expiry Date <span class="red" >*</span></label>
                            <input type="text" name="expiry" class="md-input md-input-success label-fixed" required data-uk-datepicker="{format:'DD-MM-YYYY'}" autocomplete="off" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Quantity <span class="red" >*</span></label>
                            <input type="text" name="quanitty" class="md-input md-input-success label-fixed itemqty" required >
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Discount <span class="red" >*</span></label>
                            <input type="text" name="discount" class="md-input md-input-success label-fixed" required >
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
<div class="uk-modal" id="modal_newbulkitem">
    <?php
        $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'addbulkitem');
        echo admin_form_open_multipart(base_url('admin/purchases/add_item_bulk'), $attrib);
    ?>
        <div class="uk-modal-dialog">
            <div class="uk-modal-header">
                <h3 class="uk-modal-title">Add New Bulk Item</h3>
            </div>
            <div class="uk-modal-body">
                <div class="uk-grid">
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Bulk File <span class="red" >*</span></label>
                            <input type="hidden" name="pid" value="<?php echo $purchase->id; ?>">
                            <br>
                            <input type="file" name="itemsfile" class="md-input md-input-success label-fixed" required >
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
<div class="uk-modal" id="modal_edititem">
    <?php
        $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'edititem');
        echo admin_form_open_multipart("#", $attrib);
    ?>
        <div class="uk-modal-dialog">
            <div class="uk-modal-header">
                <h3 class="uk-modal-title">Add Edit Item</h3>
            </div>
            <div class="uk-modal-body">
                <div class="uk-grid">
                    <div class="uk-width-large-1-1" style="margin-top: 15px;">
                        <input type="hidden" name="piid" id="edit_pi_id">
                        <input type="hidden" name="pid" id="edit_product_id">
                        <label>Product <span class="red" >*</span></label>
                        <input type="text" class="md-input md-input-success label-fixed" id="edit_product_name" name="pname" readonly id="">
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Batch <span class="red" >*</span></label>
                            <input type="text" name="batch" id="edit_product_batch" class="md-input md-input-success label-fixed " required >
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Expiry Date <span class="red" >*</span></label>
                            <input type="text" name="expiry" id="edit_product_expiry" class="md-input md-input-success label-fixed" required >
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Quantity <span class="red" >*</span></label>
                            <input type="text" name="quanitty" id="edit_product_qty" class="md-input md-input-success label-fixed" required >
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

    var csrfName = "<?php echo $this->security->get_csrf_token_name(); ?>",
        csrfHash = "<?php echo $this->security->get_csrf_hash(); ?>";
    var data = [];
    data[csrfName] = csrfHash;
    data['id'] = '<?php echo $purchase->id; ?>';
    $.DataTableInit({
        selector:'#itemsTable',
        url:"<?= admin_url('purchases/get_items'); ?>",
        data:data,
        aaSorting: [[0, "asc"]],
        columnDefs: [
            { 
                "targets": 10,
                "orderable": false
            }
        ],
        fixedColumns:   {left: 0,right: 1},
        scrollX: true,
        paging: false,
        info: true,
        searching: false,
        processing: false,
        serverSide: false
    });
    // New Items
    $(document).on('click','.addnewitem',function(){
        UIkit.modal('#modal_newitem').show();
    });
    $(document).on('click','.addnewbulkitem',function(){
        UIkit.modal('#modal_newbulkitem').show();
    });


    $(document).ready(function(){
        $('.select2').select2();
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
                        supplier_id: <?php echo $purchase->supplier_id; ?>
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
        $('#additem').submit(function(e){
            e.preventDefault();
            $('.submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/purchases/insert_item'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    console.log(obj);
                    if(obj.status){
                        toastr.success(obj.message);
                        $('#itemsTable').DataTable().ajax.reload()
                        location.reload();
                        UIkit.modal('#modal_newitem').hide();
                        $('#additem')[0].reset();
                    }
                    else{
                        toastr.error(obj.message);
                    }
                    $('.submitbtn').prop('disabled', false);
                }
            });
        });
        $('#addbulkitem').submit(function(e){
            e.preventDefault();
            $('.submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/purchases/insert_bulk_item'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    console.log(obj);
                    if(obj.status){
                        toastr.success(obj.message);
                        $('#itemsTable').DataTable().ajax.reload()
                        location.reload();
                        UIkit.modal('#modal_newitem').hide();
                        $('#additem')[0].reset();
                    }
                    else{
                        toastr.error(obj.message);
                    }
                    $('.submitbtn').prop('disabled', false);
                }
            });
        });
        $(document).on('click','.itemedit',function(){
            $('#edit_pi_id').val($(this).data('id'));
            $('#edit_product_id').val($(this).data('product'));
            $('#edit_product_name').val($(this).data('panem'));
            $('#edit_product_batch').val($(this).data('batch'));
            $('#edit_product_expiry').val($(this).data('expiry'));
            $('#edit_product_qty').val($(this).data('qty'));
            UIkit.modal('#modal_edititem').show();
            
        });
        $('#edititem').submit(function(e){
            e.preventDefault();
            $('.submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/purchases/update_item'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    console.log(obj);
                    if(obj.status){
                        toastr.success(obj.message);
                        location.reload();
                        $('#itemsTable').DataTable().ajax.reload()
                        UIkit.modal('#modal_edititem').hide();
                        $('#additem')[0].reset();
                    }
                    else{
                        toastr.error(obj.message);
                    }
                    $('.submitbtn').prop('disabled', false);
                }
            });
        });
        $(document).on('click','.itemdelete',function(){
            var id = $(this).data('id');
            var pid = <?php echo $purchase->id; ?>;
            Swal.fire({
                title: "Do you want to delete this item",
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
                        title: 'Deleting item!',
                        showCancelButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            $.ajax({
                                url: '<?php echo base_url('admin/purchases/delete_item'); ?>',
                                type: 'POST',
                                data: {[csrfName]:csrfHash,id:id,pid:pid,reason:res.value},
                                success: function(data) {
                                    var obj = jQuery.parseJSON(data);
                                    swal.close()
                                    if(obj.status){
                                        toastr.success(obj.message);
                                        location.reload();
                                        $('#itemsTable').DataTable().ajax.reload()
                                    }
                                    else{
                                        toastr.error(obj.message);
                                    }
                                    
                                }
                            });
                        }
                    });
                }
            })
        });
        $('#deletePurchase').click(function(){
            var poid = <?php echo $purchase->id; ?>;
            Swal.fire({
                title: "Do you want to delete this purchase",
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
                        title: 'Deleting purchase!',
                        showCancelButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            location.href = "<?php echo base_url('admin/purchases/delete/'.$purchase->id.'?resason='); ?>"+res.value;
                        }
                    });
                }
            })
        });
        $('#deletePurchaseReturn').click(function(){
            var iid = $(this).data('id');
            Swal.fire({
                title: "Do you want to delete this purchase return",
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
                        title: 'Deleting purchase return!',
                        showCancelButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            $.ajax({
                                type: "get",
                                url: '<?= admin_url('purchases/return_delete'); ?>',
                                data: {
                                    'id': iid,
                                    'reason': res.value,
                                    [csrfName]:csrfHash
                                },
                                success: function(data) {
                                    var obj = jQuery.parseJSON(data);
                                    if(obj.codestatus == "Return purchase deleted"){
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
                        }
                    });
                }
            })
        });
        $('#revicedPurchase').click(function(){
            var id = <?php echo $purchase->id; ?>;
            $.ajax({
                url: '<?php echo base_url('admin/purchases/revise_amount'); ?>',
                type: 'GET',
                data: {id:id},
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    if(obj.status){
                        toastr.success(obj.message);
                        location.reload();
                    }
                    else{
                        toastr.error(obj.message);
                    }
                }
            });
        });
        $(document).on('click','.addpayment',function(){
            $('#modal_ajax .uk-modal-dialog').html("");
            $.ajax({
                url: '<?php echo base_url('admin/purchases/add_payment'); ?>',
                type: 'POST',
                data: {[csrfName]:csrfHash,id:<?php echo $purchase->id; ?>},
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
                url: '<?php echo base_url('admin/purchases/edit_payment'); ?>',
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
                                url: '<?php echo base_url('admin/purchases/delete_payment/'); ?>'+id,
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
        <?php
            if ($return_rows) {
                ?>
                $.DataTableInit2({
                    selector:'#itemsTable2',
                    aaSorting: [[0, "asc"]],
                    fixedColumns:   {left: 0,right: 0},
                    scrollX: true,
                    paging: false,
                    info: true,
                    searching: false,
                    processing: false,
                    serverSide: false
                });
                <?php
            }
        ?>



    });
</script>



