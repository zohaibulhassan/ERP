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
                                <?php if($so->status == "partial" || $so->status == "pending"){ ?>
                                    <?php if ($Owner || $Admin || $GP['so_add_new_item']) { ?>
                                        <li><a href="#" class="addnewitem" ><i class="fa-solid fa-plus"></i> Add Items</a></li>
                                    <?php } ?>
                                    <?php if ($Owner || $Admin || $GP['so_create_invoice']) { ?>
                                        <li><a href="<?php echo base_url('admin/salesorders/create/'.$so->id); ?>"><i class="fa-solid fa-file-invoice"></i> Create Invoice</a></li>
                                    <?php } ?>
                                    <?php if ($Owner || $Admin || $GP['so_edit_info']) { ?>
                                    <?php } ?>
                                        <li><a href="#" id="SOdetailbtn" ><i class="fa-solid fa-pen-to-square"></i> Edit SO Detial</a></li>
                                    <?php if (($Owner || $Admin || $GP['so_delete']) && $so->operation_team_stauts == "pending") { ?>
                                        <li><a href="#" id="deleteSOBtn" ><i class="fa-solid fa-trash"></i> Delete</a></li>
                                    <?php } ?>
                                    <?php if (($Owner || $Admin || $GP['so_cancel']) && $so->operation_team_stauts == "pending") { ?>
                                        <li><a href="#" id="cancelSOBtn" ><i class="fa-solid fa-ban"></i> Cancel Sale Order</a></li>
                                    <?php } ?>
                                    <?php if (($Owner || $Admin || $GP['so_cancel']) && $so->status == "partial" && $so->operation_team_stauts == "pending") { ?>
                                        <li><a href="#"><i class="fa-regular fa-circle-stop"></i> Close Sale Order</a></li>
                                    <?php } ?>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <h3 class="md-card-toolbar-heading-text"><?php echo $so->ref_no?> </h3>
            </div>
            <div class="md-card-content" >
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                            <span class="uk-text-muted uk-text-small uk-text-italic"><b>Customer:</b></span>
                            <address>
                                <p><strong><?php echo $so->cusomer_company ?></strong></p>
                                <p><?php echo $so->cusomer_address ?></p>
                                <p><?php echo $so->cusomer_city.', '.$so->cusomer_state.', '.$so->cusomer_country ?></p>
                                <p><b>Phone:</b> <?php echo $so->cusomer_phone ?></p>
                                <p><b>Email:</b> <?php echo $so->cusomer_email ?></p>
                                <?php
                                    if($so->cusomer_cnic != ""){
                                        echo '<p><b>CNIC:</b> '.$so->cusomer_cnic.'</p>';
                                    }
                                    if($so->cusomer_vat_no != ""){
                                        echo '<p><b>VAT No:</b> '.$so->cusomer_vat_no.'</p>';
                                    }
                                    if($so->cusomer_ntn != ""){
                                        echo '<p><b>NtN No:</b> '.$so->cusomer_ntn.'</p>';
                                    }
                                    if($so->cusomer_gst_no != ""){
                                        echo '<p><b>GST No:</b> '.$so->cusomer_gst_no.'</p>';
                                    }
                                ?>
                            </address>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                            <span class="uk-text-muted uk-text-small uk-text-italic"><b>Supplier:</b></span>
                            <address>
                                <p><strong><?php echo $so->supplier_company ?></strong></p>
                                <p><?php echo $so->supplier_address ?></p>
                                <p><?php echo $so->supplier_city.', '.$so->supplier_state.', '.$so->supplier_country ?></p>
                                <p><b>Phone:</b> <?php echo $so->supplier_phone ?></p>
                                <p><b>Email:</b> <?php echo $so->supplier_email ?></p>
                                <?php
                                    if($so->supplier_cnic != ""){
                                        echo '<p><b>CNIC:</b> '.$so->supplier_cnic.'</p>';
                                    }
                                    if($so->supplier_vat_no != ""){
                                        echo '<p><b>VAT No:</b> '.$so->supplier_vat_no.'</p>';
                                    }
                                    if($so->supplier_ntn != ""){
                                        echo '<p><b>NtN No:</b> '.$so->supplier_ntn.'</p>';
                                    }
                                    if($so->supplier_gst_no != ""){
                                        echo '<p><b>GST No:</b> '.$so->supplier_gst_no.'</p>';
                                    }
                                ?>
                            </address>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                            <span class="uk-text-muted uk-text-small uk-text-italic"><b>Detials:</b></span>
                            <address>
                            <p><b>Reference No:</b> <?php echo $so->ref_no ?></p>
                                <p><b>Date:</b> <?php echo $so->date ?></p>
                                <p><b>Warehouse:</b> <?php echo $so->warehouse_name ?></p>
                                <p><b>PO Number:</b> <?php echo $so->po_number ?></p>
                                <p><b>PO Date:</b> <?php echo $so->po_date ?></p>
                                <p><b>Phone:</b> <?php echo $so->payment_status ?></p>
                                <!-- <p><b>Create Date:</b> <?php echo $so->created_at ?></p>
                                <p><b>Created By:</b> <?php echo $so->created_by ?></p> -->
                                <p><b>Complete/Closing Date:</b> <?php echo $so->complete_date ?></p>
                                <p><b>Cancel Date:</b> <?php echo $so->cancel_date ?></p>
                                <p><b>Account Status:</b> <?php echo $so->accounts_team_status ?></p>
                                <p><b>Operation Status:</b> <?php echo $so->operation_team_stauts ?></p>
                                <p><b>Status:</b> <?php echo $so->status ?></p>
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
                            <th>Demend Quantity</th>
                            <th>Dement Value</th>
                            <th>Complete Quantity</th>
                            <th>Complete Value</th>
                            <th>Complete Percentage</th>
                            <th>Uncomplete Quantity</th>
                            <th>Uncomplete Value</th>
                            <th>Expected Complete Quantitty</th>
                            <th>Expected Complete Percentage</th>
                            <th>Simpler Products Expected Complete</th>
                            <th class="dt-no-export" >Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <th colspan="4" >Total</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tfoot>
                </table>
                <h4 class="table_heading" >Complete Quantity</h4>
                <div class="dt_colVis_buttons2"></div>
                <table id="citemsTable" class="uk-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Product ID</th>
                            <th>Barcode</th>
                            <th style="width:150px">Name</th>
                            <th>Quantity</th>
                            <th>Batch</th>
                            <th>Expiry Date</th>
                            <th class="dt-no-export" >Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tfoot>
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
                            <input type="hidden" name="soid" value="<?php echo $so->id; ?>">
                            <select name="product" class="uk-width-1-1 product_searching" style="width: 100%">
                                <option value="">Select Product</option>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Quantity <span class="red" >*</span></label>
                            <input type="text" name="qty" class="md-input md-input-success label-fixed itemqty" required >
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
                <h3 class="uk-modal-title">Edit Item</h3>
            </div>
            <div class="uk-modal-body">
                <div class="uk-grid">
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Quantity <span class="red" >*</span></label>
                            <input type="hidden" name="id" id="ediItemId">
                            <input type="text" name="qty" id="editItemQtyTxt" class="md-input md-input-success label-fixed itemqty" required >
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
<div class="uk-modal" id="modal_editdetail">
    <?php
        $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'editForm');
        echo admin_form_open_multipart("#", $attrib);
    ?>
        <div class="uk-modal-dialog">
            <div class="uk-modal-header">
                <h3 class="uk-modal-title">Edit SO Detail</h3>
            </div>
            <div class="uk-modal-body">
                <div class="uk-grid">
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Sale Order Date</label>
                            <input type="hidden" name="id" value="<?php echo $so->id; ?>" >
                            <input class="md-input  label-fixed" type="text" name="date" data-uk-datepicker="{format:'YYYY-MM-DD'}" autocomplete="off" value="<?php echo $so->date; ?>" readonly required >
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Customer <span class="red" >*</span></label>
                            <br>
                            <select name="customer" class="uk-width-1-1 select2" required id="customer_select" style="width:100%;">
                                <option value="<?php echo $so->cusomer_id; ?>"><?php echo $so->cusomer_name; ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>P.O Number <span class="red" >*</span></label>
                            <input type="text" name="ponumber" class="md-input md-input-success label-fixed" required value="<?php echo $so->po_number; ?>">
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>PO Date</label>
                            <input class="md-input  label-fixed" type="text" name="po_date" data-uk-datepicker="{format:'YYYY-MM-DD'}" autocomplete="off" value="<?php echo $so->po_date; ?>" readonly required >
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Delivery Date</label>
                            <input class="md-input  label-fixed" type="text" name="saledeliverydate" data-uk-datepicker="{format:'YYYY-MM-DD'}" autocomplete="off" value="<?php echo $so->delivery_date; ?>" readonly required >
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-modal-footer uk-text-right">
                <button type="submit" class="md-btn md-btn-success md-btn-flat" id="editFormBtn" >Submit</button>
                <button type="button" class="md-btn md-btn-flat uk-modal-close" >Close</button>
            </div>
        </div>
    <?php echo form_close(); ?>
</div>
<div class="uk-modal" id="modal_completeqty">
    <?php
        $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'completeForm');
        echo admin_form_open_multipart("#", $attrib);
    ?>
        <div class="uk-modal-dialog">
            <div class="uk-modal-header">
                <h3 class="uk-modal-title">Complete Quantity</h3>
            </div>
            <div class="uk-modal-body">
                <div class="uk-grid">
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Batches <span class="red" >*</span></label>
                            <br>
                            <select name="batch" class="uk-width-1-1" required id="selectbatch" style="width:100%;">
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Complete Quantity <span class="red" >*</span></label>
                            <input type="number" name="qty" class="md-input md-input-success label-fixed" required value="0" id="sobatchqty" >
                            <input type="hidden" name="itemid" id="soitemIdbatch">
                            <input type="hidden" name="soid" id="soIdbatch" value="<?php echo $so->id; ?>">

                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Remaining Quantity <span class="red" >*</span></label>
                            <input type="number" name="uncompleteqty" class="md-input md-input-success label-fixed" required value="0" id="uncompleteqtyTxt" readonly>
                        </div>
                    </div>





                </div>
            </div>
            <div class="uk-modal-footer uk-text-right">
                <button type="submit" class="md-btn md-btn-success md-btn-flat" id="completeFormBtn" >Submit</button>
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
    data['id'] = '<?php echo $so->id; ?>';
    $.DataTableInit({
        selector:'#itemsTable',
        url:"<?= admin_url('salesorders/get_items'); ?>",
        data:data,
        aaSorting: [[0, "asc"]],
        columnDefs: [
            { 
                "targets": 14,
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
    $.DataTableInit({
        selector:'#citemsTable',
        url:"<?= admin_url('salesorders/get_citems'); ?>",
        data:data,
        aaSorting: [[0, "asc"]],
        columnDefs: [
            { 
                "targets": 7,
                "orderable": false
            }
        ],
        fixedColumns:   {left: 0,right: 1},
        scrollX: false,
        paging: false,
        info: true,
        searching: false,
        processing: false,
        serverSide: false
    });
    $(document).ready(function(){
        $(document).on('click','#deleteSOBtn',function(){
            var rid = <?= $so->id ?>;
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            Swal.fire({
                title: "Do you want to delete this sale order",
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
                        title: 'Deleting Sale order!',
                        showCancelButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            $.ajax({
                                url: '<?php echo base_url('admin/salesorders/delete'); ?>',
                                type: 'GET',
                                data: {[csrfName]:csrfHash,id:rid,reason:res.value},
                                success: function(data) {
                                    var obj = jQuery.parseJSON(data);
                                    swal.close()
                                    if(obj.status){
                                        toastr.success(obj.message);
                                        location.reload();
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
        $(document).on('click','#cancelSOBtn',function(){
            var rid = <?= $so->id ?>;
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            Swal.fire({
                title: "Do you want to cancel this sale order",
                input: "text",
                showCancelButton: true,
                confirmButtonColor: "#e53935",
                confirmButtonText: "Cancel SO",
                cancelButtonText: "Cancel",
                buttonsStyling: true
            }).then(function (res) {
                console.log(res);
                if(res.isConfirmed){
                    Swal.fire({
                        title: 'Canceling Sale order!',
                        showCancelButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            $.ajax({
                                url: '<?php echo base_url('admin/salesorders/cancel'); ?>',
                                type: 'GET',
                                data: {[csrfName]:csrfHash,id:rid,reason:res.value},
                                success: function(data) {
                                    var obj = jQuery.parseJSON(data);
                                    swal.close()
                                    if(obj.status){
                                        toastr.success(obj.message);
                                        location.reload();
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
                        supplier_id: <?php echo $so->supplier_id; ?>
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
        $('#customer_select').select2({
            ajax: {
                url: '<?php echo base_url("admin/general/customers"); ?>',
                dataType: 'json',
            },
            formatResult: function (data, term) {
                return data;
            },
        });
        // New Item
        $(document).on('click','.addnewitem',function(){
            UIkit.modal('#modal_newitem').show();
        });
        $('#additem').submit(function(e){
            e.preventDefault();
            $('.submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/salesorders/additem'); ?>',
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
        // Edit Item
        $(document).on('click','.soi_editbtn',function(){
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
        $(document).on('click','.soi_completeqty',function(){
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
                        // $("#selectbatch").select2("destroy").select2();
                        UIkit.modal('#modal_completeqty').show();
                    }
                    else{
                        alert(obj.codestatus);
                        UIkit.modal('#modal_completeqty').hide();
                    }
                },
                error: function(){
                    alert('Try Again!');
                    UIkit.modal('#modal_completeqty').hide();
                }
            });
        }
        $(document).on('click','.soi_completeqty_delete',function(){
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
                    // var obj = jQuery.parseJSON(data);
                    location.reload();
                },
                error: function(){
                    alert('Try Again!');
                }
            });
        });
    });
    $('#updateItemBtn').click(function(){
        $('#editItemForm').submit();
    });
    $('#editItemForm').submit(function(e){
        e.preventDefault();
        $('#updateItemBtn').prop('disabled', true);
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
    $(document).on('click','.soi_deletebtn',function(){
        var iid = $(this).data('id');
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
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
                            url: '<?php echo base_url('admin/salesorders/itemdelete'); ?>',
                            type: 'GET',
                            data: {[csrfName]:csrfHash,id:iid,reason:res.value},
                            success: function(data) {
                                var obj = jQuery.parseJSON(data);
                                swal.close()
                                if(obj.status){
                                    toastr.success(obj.message);
                                    location.reload();
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
    $(document).on('click','#SOdetailbtn',function(){
        UIkit.modal('#modal_editdetail').show();
    });
    $('#editFormBtn').click(function(){
        $('#editForm').submit();    
    });
    $('#editForm').submit(function(e){
        e.preventDefault();
        $('#editFormBtn').prop('disabled', true);
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
            }
        });

    });
    $('#completeForm').submit(function(e){
        e.preventDefault();
        $('.completeFormBtn').prop('disabled', true);
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
                if(obj.codestatus == 'ok'){
                    window.top.location.href = '<?= admin_url('salesorders/view/'); ?>'+obj.soid;
                }
                else if(obj.codestatus == 'next'){
                    var r = confirm("Do you want to add other batch");
                    if (r == true) {
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
</script>



