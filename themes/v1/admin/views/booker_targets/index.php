<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Target Settings</h3>
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
                            <th>Month</th>
                            <th>Year</th>
                            <th>Order Targets</th>
                            <th>Order Targets Amount</th>
                            <th>Targets Shops</th>
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
            <h3 class="uk-modal-title">Create Target</h3>
        </div>
        <div class="uk-modal-body">
            <div class="uk-grid">
                <div class="uk-width-large-1-1">
                    <div class="md-input-wrapper md-input-filled">
                        <label>Month <span class="red" >*</span></label>
                        <select name="month" class="md-input md-input-success label-fixed">
                            <option value="jan">Januaray</option>
                            <option value="feb">Febrauray</option>
                            <option value="mar">March</option>
                            <option value="apr">April</option>
                            <option value="may">May</option>
                            <option value="jun">June</option>
                            <option value="jul">July</option>
                            <option value="aug">Augest</option>
                            <option value="sep">September</option>
                            <option value="oct">October</option>
                            <option value="nov">November</option>
                            <option value="dec">December</option>
                        </select>
                    </div>
                </div>
                <input type="hidden" name="user_id" value="<?php echo $user_id;?>">
                <input type="hidden" name="year" value="<?php echo date('Y-m-d');?>">
                <div class="uk-width-large-1-1" style="margin-top: 12px;" >
                    <label>Targetted Orders</label>
                    <input type="number" name="target_orders" class="md-input no_autosize">
                </div>

                <div class="uk-width-large-1-1" style="margin-top: 12px;" >
                    <label>Targetted Orders Amount</label>
                    <input type="number" name="target_amount" class="md-input no_autosize">
                </div>

                <div class="uk-width-large-1-1" style="margin-top: 12px;" >
                    <label>Targetted Shops</label>
                    <input type="number" name="target_shops" class="md-input no_autosize">
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
            <h3 class="uk-modal-title">Edit Target</h3>
        </div>
        <div class="uk-modal-body">
            <div class="uk-grid">
                <div class="uk-width-large-1-1">
                    <div class="md-input-wrapper md-input-filled">
                        <label>Month <span class="red" >*</span></label>
                        <input type="hidden" name="id" id="target_id">
                        <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id;?>">
                        <input type="hidden" name="year" id="year" value="<?php echo date('Y-m-d');?>">
                        <select name="month" id="month" class="md-input md-input-success label-fixed">
                            <option value="jan">Januaray</option>
                            <option value="feb">Febrauray</option>
                            <option value="mar">March</option>
                            <option value="apr">April</option>
                            <option value="may">May</option>
                            <option value="jun">June</option>
                            <option value="jul">July</option>
                            <option value="aug">Augest</option>
                            <option value="sep">September</option>
                            <option value="oct">October</option>
                            <option value="nov">November</option>
                            <option value="dec">December</option>
                        </select>
                    </div>
                </div>

                <div class="uk-width-large-1-1" style="margin-top: 12px;" >
                    <label>Targetted Orders</label>
                    <input type="number" id="target_orders" name="target_orders" class="md-input no_autosize">
                </div>

                <div class="uk-width-large-1-1" style="margin-top: 12px;" >
                    <label>Targetted Orders Amount</label>
                    <input type="number" id="target_amount" name="target_amount" class="md-input no_autosize">
                </div>

                <div class="uk-width-large-1-1" style="margin-top: 12px;" >
                    <label>Targetted Shops</label>
                    <input type="number" id="target_shop" name="target_shops" class="md-input no_autosize">
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
<!-- CK Editor 5 -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>ckeditor5/ckeditor.js"></script>


<script>
    var csrfName = "<?php echo $this->security->get_csrf_token_name(); ?>",
    csrfHash = "<?php echo $this->security->get_csrf_hash(); ?>";
    var data = [];
    data[csrfName] = csrfHash;
    $.DataTableInit({
        selector:'#dt_tableExport',
        url:"<?= admin_url('booker_targets/get_targets'); ?>",
        data:data,
        aaSorting: [[1, "desc"]],
        columnDefs: [
        { 
            "targets": 0,
            "orderable": false
        },
        { 
            "targets": 5,
            "orderable": false
        }
        ],
        fixedColumns:   {left: 0,right: 1},
        scrollX: false
    });
</script>
<script>
    $(document).ready(function(){
        $('.select2').select2();
        $(document).on('click','.addbtn',function(){
            UIkit.modal('#modal_add').show();
        });
        $(document).on('click','.editbtn',function(){
            $('#target_id').val($(this).data('id'));
            $('#month').val($(this).data('month'));
            $('#target_orders').val($(this).data('target_orders'));
            $('#target_amount').val($(this).data('target_amount'));
            $('#target_shop').val($(this).data('target_shop'));
            UIkit.modal('#modal_edit').show();
        });
        $('#addFrom').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/booker_targets/insert_target'); ?>',
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
                url: '<?php echo base_url('admin/booker_targets/update_target'); ?>',
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
                title: "Do you want to delete this Target",
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
                        title: 'Deleting Own Disease!',
                        showCancelButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            $.ajax({
                                url: '<?php echo base_url("admin/booker_targets/delete_target"); ?>',
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