<style>
    .select2 {
        width:100% !important;
    }
</style>
<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Credits</h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                </div>
            </div>
            <div class="md-card-content">
                <div class="dt_colVis_buttons"></div>
                <table id="dt_tableExport" class="uk-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Supplier</th>
                            <th>Credit Limit</th>
                            <th>Payment Terms</th>
                            <th>MRP Discount</th>
                            <th>Created at</th>
                            <th>Created by</th>
                            <th class="dt-no-export" >Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="md-fab-wrapper md-fab-in-card" style="position: fixed;bottom: 20px;">
    <button class="md-fab md-fab-success md-fab-wave waves-effect waves-button addbtn" type="button"><i class="fa-solid fa-plus"></i></button>
</div>
<div class="uk-modal" id="modal_add">
    <?php
        $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'addFrom');
        echo admin_form_open_multipart("#", $attrib);
    ?>
        <div class="uk-modal-dialog">
            <div class="uk-modal-header">
                <h3 class="uk-modal-title">Create Credit</h3>
            </div>
            <div class="uk-modal-body">
                <div class="uk-grid">
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Supplier<span class="red" >*</span></label>
                            <input type="hidden" name="customer" value="<?php echo $id; ?>" >
                            <select name="supplier" class="uk-width-1-1 select2" required>
                                <option value="">Select Supplier</option>
                                <?php
                                    foreach($suppliers as $row){
                                        echo '<option value="'.$row->id.'" ';
                                        echo ' >'.$row->text.'</option>';
                                    }
                                ?>
                            </select>
                        </div>

                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Limit <span class="red" >*</span></label>
                            <input type="number" name="limit" class="md-input md-input-success label-fixed" required >
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Payment Terms <span class="red" >*</span></label>
                            <input type="number" name="paymentterms" class="md-input md-input-success label-fixed" required >
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>MRP Discount (Only Crossdock Customers) <span class="red" >*</span></label>
                            <input type="number" name="mrpdiscount" class="md-input md-input-success label-fixed" required >
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-modal-footer uk-text-right">
                <button type="submit" class="md-btn md-btn-success md-btn-flat" id="submitbtn" >Submit</button>
                <button type="button" class="md-btn md-btn-flat uk-modal-close" >Close</button>
            </div>
        </div>
    <?php echo form_close(); ?>
</div>
<div class="uk-modal" id="modal_edit">
    <?php
        $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'editFrom');
        echo admin_form_open_multipart("#", $attrib);
    ?>
        <div class="uk-modal-dialog">
            <div class="uk-modal-header">
                <h3 class="uk-modal-title">Edit Credit</h3>
            </div>
            <div class="uk-modal-body">
                <div class="uk-grid">
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Supplier<span class="red" >*</span></label>
                            <input type="hidden" name="id" id="credit_id" >
                            <select name="supplier" class="uk-width-1-1 select2" required id="supplier" >
                                <option value="">Select Supplier</option>
                                <?php
                                    foreach($suppliers as $row){
                                        echo '<option value="'.$row->id.'" ';
                                        echo ' >'.$row->text.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Limit <span class="red" >*</span></label>
                            <input type="number" name="limit" class="md-input md-input-success label-fixed" required id="limit" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Payment Terms <span class="red" >*</span></label>
                            <input type="number" name="paymentterms" class="md-input md-input-success label-fixed" required id="durration" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>MRP Discount (Only Crossdock Customers) <span class="red" >*</span></label>
                            <input type="number" name="mrpdiscount" class="md-input md-input-success label-fixed" required id="mrpdiscoutn" >
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-modal-footer uk-text-right">
                <button type="submit" class="md-btn md-btn-success md-btn-flat" id="submitbtn2" >Submit</button>
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
    data['customer_id'] = <?php echo $id; ?>;
    $.DataTableInit({
        selector:'#dt_tableExport',
        url:"<?= admin_url('customers/get_credits'); ?>",
        data:data,
        aaSorting: [[0, "desc"]],
        columnDefs: [
            { 
                "targets": 7,
                "orderable": false
            }
        ]
    });
</script>
<script>
    $(document).ready(function(){
        $('.select2').select2();
        $(document).on('click','.addbtn',function(){
            UIkit.modal('#modal_add').show();
        });
        $(document).on('click','.editbtn',function(){
            $('#credit_id').val($(this).data('id'));
            $('#supplier').val($(this).data('supplier_id')).trigger('change');
            $('#limit').val($(this).data('creadit_limit'));
            $('#durration').val($(this).data('durration'));
            $('#mrpdiscoutn').val($(this).data('crossdock_discount'));
            UIkit.modal('#modal_edit').show();
        });
        $('#addFrom').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/customers/insert_credit'); ?>',
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
                        $('#dt_tableExport').DataTable().ajax.reload()
                        UIkit.modal('#modal_add').hide();
                        $('#addFrom')[0].reset();
                    }
                    else{
                        toastr.error(obj.message);
                    }
                    $('#submitbtn').prop('disabled', false);
                }
            });
        });
        $('#editFrom').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/customers/update_credit'); ?>',
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
                        $('#dt_tableExport').DataTable().ajax.reload()
                        UIkit.modal('#modal_edit').hide();
                        $('#editFrom')[0].reset();
                    }
                    else{
                        toastr.error(obj.message);
                        $('#submitbtn').prop('disabled', false);
                    }
                }
            });
        });

        $(document).on('click','.deletebtn',function(){
            var id = $(this).data('id');
            Swal.fire({
                title: "Do you want to delete this credit",
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
                        title: 'Deleting Credit!',
                        showCancelButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            $.ajax({
                                url: '<?php echo base_url("admin/customers/delete_credit"); ?>',
                                type: 'POST',
                                data: {[csrfName]:csrfHash,id:id,reason:res.value},
                                success: function(data) {
                                    var obj = jQuery.parseJSON(data);
                                    swal.close()
                                    if(obj.status){
                                        toastr.success(obj.message);
                                        $('#dt_tableExport').DataTable().ajax.reload()
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