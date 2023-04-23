<div id="page_content">
    <div id="page_content_inner">

    <div class="md-card">
            <div class="md-card-toolbar">
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-toggle" style="opacity: 1; transform: scale(1);"></i>
                </div>
                <h3 class="md-card-toolbar-heading-text">Filters </h3>
            </div>
            <div class="md-card-content" >
                <form action="<?php echo base_url('admin/products/groups'); ?>" method="get">
                    <div class="uk-grid">
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Warehouse</label>
                                <select name="warehouse" class="uk-width-1-1 select2" >
                                    <option value="0">All Warehouses</option>
                                    <?php
                                        foreach($warehouses as $row){
                                        ?>
                                        <option value="<?php echo $row->id?>" <?php if($row->id == $wid){ echo 'selected'; } ?> ><?php echo $row->text?></option>
                                        <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4" style="padding-top: 20px;">
                            <button type="submit" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light" >Submit</button>
                            <a href="<?php echo base_url('admin/products/groups'); ?>" class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light" >Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Product Groups</h3>
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
                            <th>Name</th>
                            <th>Brnad</th>
                            <th>Active</th>
                            <th>Deactive</th>
                            <th>Total</th>
                            <th>Available Quantity</th>
                            <th class="dt-no-export" >Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="md-fab-wrapper md-fab-in-card" style="position: fixed;bottom: 20px;">
    <button class="md-fab md-fab-success md-fab-wave waves-effect waves-button addbtn" type="button"><i class="fa-solid fa-plus"></i></button>
</div>
<div class="uk-modal" id="modal_addgroup">
    <?php
        $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'addFrom');
        echo admin_form_open_multipart("#", $attrib);
    ?>
        <div class="uk-modal-dialog">
            <div class="uk-modal-header">
                <h3 class="uk-modal-title">Create Product Group</h3>
            </div>
            <div class="uk-modal-body">
                <div class="uk-grid">
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Name <span class="red" >*</span></label>
                            <input type="text" name="name" class="md-input md-input-success label-fixed" required >
                        </div>
                    </div>
                    <br>
                    <div class="uk-width-large-1-1" style="margin-top: 15px;">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Brand <span class="red" >*</span></label>
                            <select name="brand" class="uk-width-1-1 select2" style="width: 100%">
                                <?php
                                    foreach($brands as $row){
                                        echo '<option value="'.$row->id.'" ';
                                        echo ' >'.$row->text.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
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
<div class="uk-modal" id="modal_editgroup">
    <?php
        $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'editFrom');
        echo admin_form_open_multipart("#", $attrib);
    ?>
        <div class="uk-modal-dialog">
            <div class="uk-modal-header">
                <h3 class="uk-modal-title">Edit Product Group</h3>
            </div>
            <div class="uk-modal-body">
                <div class="uk-grid">
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Name <span class="red" >*</span></label>
                            <input type="hidden" name="id" id="group_id">
                            <input type="text" name="name" class="md-input md-input-success label-fixed" required id="group_name">
                        </div>
                    </div>
                    <br>
                    <div class="uk-width-large-1-1" style="margin-top: 15px;">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Brand <span class="red" >*</span></label>
                            <select name="brand" class="uk-width-1-1 select2" style="width: 100%" id="brand_id">
                                <?php
                                    foreach($brands as $row){
                                        echo '<option value="'.$row->id.'" ';
                                        echo ' >'.$row->text.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
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
<!-- datatables custom integration -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/custom/datatables/datatables.uikit.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/datatable.js"></script>
<script>
    var csrfName = "<?php echo $this->security->get_csrf_token_name(); ?>",
        csrfHash = "<?php echo $this->security->get_csrf_hash(); ?>";
    var data = {
        [csrfName]:csrfHash,
        wid:'<?php echo $wid; ?>'
    }

    $.DataTableInit({
        selector:'#dt_tableExport',
        url:"<?= admin_url('products/get_group'); ?>",
        data:data,
        aaSorting: [[0, "desc"]],
        columnDefs: [
            { 
                "targets": 7,
                "orderable": false
            }
        ],
        fixedColumns:{left: 0,right: 1},
        scrollX: false
    });
</script>


<script>
    $(document).ready(function(){
        $('.select2').select2();
        $(document).on('click','.addbtn',function(){
            UIkit.modal('#modal_addgroup').show();
        });
        $(document).on('click','.editbtn',function(){
            $('#group_id').val($(this).data('id'));
            $('#brand_id').val($(this).data('bid')).trigger('change');
            $('#group_name').val($(this).data('name'));
            UIkit.modal('#modal_editgroup').show();
        });

        $('#addFrom').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/products/insert_group'); ?>',
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
                        UIkit.modal('#modal_addgroup').hide();
                        $('#addFrom')[0].reset();
                        $('#dt_tableExport').DataTable().ajax.reload()
                    }
                    else{
                        toastr.error(obj.message);
                        $('#submitbtn').prop('disabled', false);
                    }
                }
            });
        });
        $('#editFrom').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/products/update_group'); ?>',
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
                        UIkit.modal('#modal_editgroup').hide();
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
                title: "Do you want to delete this product group",
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
                        title: 'Deleting Product!',
                        showCancelButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            $.ajax({
                                url: '<?php echo base_url("admin/products/delete_group"); ?>',
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