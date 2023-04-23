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

                            <?php if($po->status == "partial" || $po->status == "pending"){ ?>
                                <?php if ($Owner || $Admin || $GP['po_add_new_item']) { ?>
                                    <li><a href="#" class="addnewitem"> Add Item</a></li>
                                <?php } ?>
                                <?php if ($Owner || $Admin || $GP['po_add_receiving']) { ?>
                                    <li><a href="<?php echo base_url('admin/purchaseorder/addreciveing').'/'.$po->po_id; ?>"> Add Receiving</a></li>
                                <?php } ?>
                                <?php if ($Owner || $Admin || $GP['po_edit_info']) { ?>
                                    <!-- <li><a href="#editPOModel" id="editPOBtn" data-toggle="modal" data-target="#editPOModel"> Edit PO Detail</a></li> -->
                                <?php } ?>
                                <?php if ($Owner || $Admin || $GP['po_close']) { ?>
                                    <li><a  id="closePOBtn" style="cursor: pointer;" > Close PO</a></li>
                                <?php } ?>
                                <?php if ($Owner || $Admin || $GP['po_delete']) { ?>
                                    <li><a  id="deletePOBtn" style="cursor: pointer;" > Delete PO</a></li>
                                <?php } ?>
                                <li><a href="<?php echo base_url('admin/purchaseorder/pdf').'/'.$po->po_id; ?>"> Download PDF</a></li>
                                
                            <?php
                                }
                            ?>

                            </ul>
                        </div>
                    </div>
                </div>
                <h3 class="md-card-toolbar-heading-text"><?php echo $po->reference_no?> </h3>
            </div>
            <div class="md-card-content" >
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                            <span class="uk-text-muted uk-text-small uk-text-italic"><b>Supplier:</b></span>
                            <address>
                                <p><strong><?php echo $po->company ?></strong></p>
                                <p><?php echo $po->address ?></p>
                                <p><?php echo $po->city.', '.$po->state.', '.$po->country ?></p>
                                <p><b>Phone:</b> <?php echo $po->phone ?></p>
                                <p><b>Email:</b> <?php echo $po->email ?></p>
                                <?php
                                    if($po->cnic != ""){
                                        echo '<p><b>CNIC:</b> '.$po->cnic.'</p>';
                                    }
                                    if($po->vat_no != ""){
                                        echo '<p><b>VAT No:</b> '.$po->vat_no.'</p>';
                                    }
                                    if($po->cf1 != ""){
                                        echo '<p><b>NtN No:</b> '.$po->cf1.'</p>';
                                    }
                                    if($po->gst_no != ""){
                                        echo '<p><b>GST No:</b> '.$po->gst_no.'</p>';
                                    }
                                ?>
                            </address>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                            <span class="uk-text-muted uk-text-small uk-text-italic"><b>Warehosue:</b></span>
                            <address>
                                <p><strong><?php echo $po->warehosue_name ?></strong></p>
                                <p><?php echo $po->address ?></p>
                                <p><b>Phone:</b> <?php echo $po->warehosue_phone ?></p>
                                <p><b>Email:</b> <?php echo $po->warehosue_email ?></p>
                            </address>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                            <span class="uk-text-muted uk-text-small uk-text-italic"><b>Detials:</b></span>
                            <address>
                            <p><b>Reference No:</b> <?php echo $po->reference_no ?></p>
                                <p><b>Reciving Date:</b> <?php echo $po->receiving_date ?></p>
                                <p><b>Warehouse:</b> <?php echo $po->warehosue_name ?></p>
                                <p><b>Status:</b> <?php echo $po->status ?></p>
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
                            <th>Received Quantity</th>
                            <th>Unreceived Quantity</th>
                            <th>Complete Percentage</th>
                            <th>Unit Cost</th>
                            <th>Tax</th>
                            <th>Subtotal</th>
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
                    </tfoot>
                </table>
                <h4 class="table_heading" >Receiving</h4>
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form','id'=>'createdPorchaseForm2','method'=>'get');
                    echo admin_form_open("purchaseorder/createdPurchase", $attrib);
                ?>
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-large-1-1">
                        <button class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light" id="createinvoice" >Create Invoice</button>
                    </div>
                </div>


                <table id="citemsTable" class="uk-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Delivery Note</th>
                            <th>Recived By</th>
                            <th>Recived Date</th>
                            <th>Purchase Invoice</th>
                            <th class="dt-no-export" >Actions</th>
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
                                <?php
                                    if($delivery->purchase_create == 'no' && ($Owner || $Admin || $GP['po_create_invoice'])){
                                ?>
                                    <a href="<?php echo base_url('admin/purchaseorder/editreciveing').'?porid='.$delivery->id.'&poid='.$po->po_id; ?>"  class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" >Edit</a>
                                    <button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini reciveingdelete"  data-id="<?php echo $delivery->id; ?>"  type="button" >Delete</button>
                                <?php
                                    }
                                ?>

                            </td>
                        </tr>
                        <?php
                        $no++;
                            }
                        ?>
                    </tbody>
                </table>
                <?php echo form_close(); ?>
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
                            <input type="hidden" name="poid" value="<?php echo $po->po_id; ?>">
                            <select name="product" class="uk-width-1-1 product_searching" style="width: 100%">
                                <option value="">Select Product</option>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Quantity <span class="red" >*</span></label>
                            <input type="text" name="quanitty" class="md-input md-input-success label-fixed itemqty" required >
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
                        <input type="hidden" name="poid" id="edit_poi_id">
                        <input type="hidden" name="pid" id="edit_product_id">
                        <label>Product <span class="red" >*</span></label>
                        <input type="text" class="md-input md-input-success label-fixed" id="edit_product_name" name="pname" readonly id="">
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
    data['id'] = '<?php echo $po->po_id; ?>';
    $.DataTableInit({
        selector:'#itemsTable',
        url:"<?= admin_url('purchaseorder/get_items'); ?>",
        data:data,
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
        processing: false,
        serverSide: false
    });
    // New Items
    $(document).on('click','.addnewitem',function(){
        UIkit.modal('#modal_newitem').show();
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
                        supplier_id: <?php echo $po->supplier_id; ?>
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
                url: '<?php echo base_url('admin/purchaseorder/insert_item'); ?>',
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
        $(document).on('click','.itemedit',function(){
            $('#edit_poi_id').val($(this).data('id'));
            $('#edit_product_id').val($(this).data('product'));
            $('#edit_product_name').val($(this).data('panem'));
            $('#edit_product_qty').val($(this).data('qty'));
            UIkit.modal('#modal_edititem').show();
            
        });
        $('#edititem').submit(function(e){
            e.preventDefault();
            $('.submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/purchaseorder/update_item'); ?>',
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
            var poid = <?php echo $po->po_id; ?>;
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
                                url: '<?php echo base_url('admin/purchaseorder/delete_item'); ?>',
                                type: 'POST',
                                data: {[csrfName]:csrfHash,id:id,poid:poid,reason:res.value},
                                success: function(data) {
                                    var obj = jQuery.parseJSON(data);
                                    swal.close()
                                    if(obj.status){
                                        toastr.success(obj.message);
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
        $('#closePOBtn').click(function(){
            var poid = <?php echo $po->po_id; ?>;
            Swal.fire({
                title: "Do you want to close this PO",
                input: "text",
                showCancelButton: true,
                confirmButtonColor: "#e53935",
                confirmButtonText: "Close",
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
                                url: '<?php echo base_url('admin/purchaseorder/close'); ?>',
                                type: 'POST',
                                data: {[csrfName]:csrfHash,id:poid,reason:res.value},
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
        $('#deletePOBtn').click(function(){
            var poid = <?php echo $po->po_id; ?>;
            Swal.fire({
                title: "Do you want to delete this PO",
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
                                url: '<?php echo base_url('admin/purchaseorder/delete'); ?>',
                                type: 'POST',
                                data: {[csrfName]:csrfHash,id:poid,reason:res.value},
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
        $(document).on('click','.reciveingdelete',function(){
            var id = $(this).data('id');
            var poid = <?php echo $po->po_id; ?>;
            Swal.fire({
                title: "Do you want to Delete Reciving!",
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
                        title: 'Deleting po reciveing!',
                        showCancelButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            $.ajax({
                                url: '<?php echo base_url('admin/purchaseorder/pordelete'); ?>',
                                type: 'POST',
                                data: {[csrfName]:csrfHash,id:id,poid:poid,reason:res.value},
                                success: function(data) {
                                    var obj = jQuery.parseJSON(data);
                                    swal.close()
                                    if(obj.status){
                                        toastr.success(obj.message);
                                        location.reload();
                                        // $('#itemsTable').DataTable().ajax.reload()
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



    });
</script>



