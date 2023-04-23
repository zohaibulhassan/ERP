<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Own Companies</h3>
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
                            <th>Company Name</th>
                            <th>Mobile</th>
                            <th>Register Person</th>
                            <th>NTN</th>
                            <th>STRN</th>
                            <th>STB</th>
                            <th>Register Address</th>
                            <th>Warehouse Address</th>
                            <th>Auto Invoice Generate</th>
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
                <h3 class="uk-modal-title">Create Own Company</h3>
            </div>
            <div class="uk-modal-body">
                <div class="uk-grid">
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Company Name <span class="red" >*</span></label>
                            <input type="text" name="companyname" class="md-input md-input-success label-fixed" required >
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Mobile <span class="red" >*</span></label>
                            <input type="text" name="mobile" class="md-input md-input-success label-fixed" required >
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Register Person </label>
                            <input type="text" name="rperson" class="md-input md-input-success label-fixed" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="md-input-wrapper md-input-filled">
                            <label>NTN </label>
                            <input type="text" name="ntn" class="md-input md-input-success label-fixed" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="md-input-wrapper md-input-filled">
                            <label>STRN </label>
                            <input type="text" name="strn" class="md-input md-input-success label-fixed" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="md-input-wrapper md-input-filled">
                            <label>SRB </label>
                            <input type="text" name="srb" class="md-input md-input-success label-fixed" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Register Address </label>
                            <input type="text" name="raddress" class="md-input md-input-success label-fixed">
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Warehouse Address </label>
                            <input type="text" name="waddress" class="md-input md-input-success label-fixed" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Slip Header </label>
                            <input type="text" name="slipheder" class="md-input md-input-success label-fixed" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Slip Footer </label>
                            <input type="text" name="slipfooter" class="md-input md-input-success label-fixed" >
                        </div>
                    </div>
                    <br>
                    <div class="uk-width-large-1-1" style="margin-top: 15px;">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Auto Invoice Generate </label>
                            <select name="autoinvoice" class="uk-width-1-1 select2" style="width: 100%">
                                <option value="0">Off</option>
                                <option value="1">On</option>
                            </select>
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
                <h3 class="uk-modal-title">Edit Own Company</h3>
            </div>
            <div class="uk-modal-body">
                <div class="uk-grid">
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Company Name <span class="red" >*</span></label>
                            <input type="hidden" name="id" id="owncompany_id">
                            <input type="text" name="companyname" class="md-input md-input-success label-fixed" required id="companyname" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Mobile <span class="red" >*</span></label>
                            <input type="text" name="mobile" class="md-input md-input-success label-fixed" required id="mobile" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Register Person </label>
                            <input type="text" name="rperson" class="md-input md-input-success label-fixed"  id="rperson" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="md-input-wrapper md-input-filled">
                            <label>NTN </label>
                            <input type="text" name="ntn" class="md-input md-input-success label-fixed"  id="ntn">
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="md-input-wrapper md-input-filled">
                            <label>STRN </label>
                            <input type="text" name="strn" class="md-input md-input-success label-fixed"  id="strn">
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="md-input-wrapper md-input-filled">
                            <label>SRB </label>
                            <input type="text" name="srb" class="md-input md-input-success label-fixed"  id="srb" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Register Address </label>
                            <input type="text" name="raddress" class="md-input md-input-success label-fixed"  id="raddress" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Warehouse Address </label>
                            <input type="text" name="waddress" class="md-input md-input-success label-fixed"  id="waddress" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Slip Header </label>
                            <input type="text" name="slipheder" id="slipheder" class="md-input md-input-success label-fixed" >
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Slip Footer </label>
                            <input type="text" name="slipfooter" id="slipfooter" class="md-input md-input-success label-fixed" >
                        </div>
                    </div>
                    <br>
                    <div class="uk-width-large-1-1" style="margin-top: 15px;">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Auto Invoice Generate </label>
                            <select name="autoinvoice" class="uk-width-1-1 select2" style="width: 100%" id="autoinvoice" >
                                <option value="0">Off</option>
                                <option value="1">On</option>
                            </select>
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
    $.DataTableInit({
        selector:'#dt_tableExport',
        url:"<?= admin_url('system_settings/get_own_companies'); ?>",
        data:data,
        aaSorting: [[0, "desc"]],
        columnDefs: [
            { 
                "targets": 10,
                "orderable": false
            }
        ],
        fixedColumns:   {left: 0,right: 1},
        scrollX: true
    });
</script>

<script>
    $(document).ready(function(){
        $('.select2').select2();
        $(document).on('click','.addbtn',function(){
            UIkit.modal('#modal_add').show();
        });
        $(document).on('click','.editbtn',function(){
            $('#owncompany_id').val($(this).data('id'));
            $('#companyname').val($(this).data('companyname'));
            $('#mobile').val($(this).data('mobile'));
            $('#rperson').val($(this).data('rperson'));
            $('#ntn').val($(this).data('ntn'));
            $('#strn').val($(this).data('strn'));
            $('#srb').val($(this).data('srb'));
            $('#raddress').val($(this).data('raddress'));
            $('#waddress').val($(this).data('waddress'));
            $('#slipheder').val($(this).data('slip_header'));
            $('#slipfooter').val($(this).data('slip_footer'));
            $('#autoinvoice').val($(this).data('autoinvoice')).trigger('change');
            UIkit.modal('#modal_edit').show();
        });
        $('#addFrom').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/system_settings/insert_own_company'); ?>',
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
                url: '<?php echo base_url('admin/system_settings/update_own_company'); ?>',
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
                title: "Do you want to delete this own company",
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
                        title: 'Deleting Own Company!',
                        showCancelButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            $.ajax({
                                url: '<?php echo base_url("admin/system_settings/delete_own_company"); ?>',
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