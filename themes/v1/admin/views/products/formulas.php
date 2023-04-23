<style>
    .select2-container{
    }
    .select2-container .select2-selection--multiple{
        /* border:none; */
    }
</style>
<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Product Formulas</h3>
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
                            <th>Code</th>
                            <th>Name</th>
                            <th>Form</th>
                            <th>Strength</th>
                            <th>Disease</th>
                            <th>Description</th>
                            <th>No of Products</th>
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
                <h3 class="uk-modal-title">Create Formula</h3>
            </div>
            <div class="uk-modal-body">
                <div class="uk-grid">
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Name <span class="red" >*</span></label>
                            <input type="text" name="name" class="md-input md-input-success label-fixed" required id="fname">
                        </div>
                    </div>
                    <div class="uk-width-large-1-1" style="margin-top: 15px;">
                        <div class="md-input-wrapper md-input-filled">
                            <label> Form </label>
                            <select name="form" class="uk-width-1-1 select2" style="width: 100%"  id="formname">
                                <?php
                                    foreach($forms as $form){
                                        echo '<option value="'.$form->id.'">'.$form->text.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-1" style="margin-top: 15px;">
                        <div class="md-input-wrapper md-input-filled">
                            <label> Strength </label>
                            <select name="strength" class="uk-width-1-1 select2" style="width: 100%"  id="strengthname">
                                <?php
                                    foreach($strengths as $strength){
                                        echo '<option value="'.$strength->id.'">'.$strength->text.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="uk-width-large-1-1" style="margin-top: 15px;">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Diseases </label>
                            <select name="diseases[]" class="uk-width-1-1 select2" style="width: 100%" multiple  id="desesesname">
                                <?php
                                    foreach($diseases as $disease){
                                        echo '<option value="'.$disease->id.'">'.$disease->text.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Code </label>
                            <input type="text" name="code" class="md-input md-input-success label-fixed" id="newgencode" readonly>
                        </div>
                    </div>
                    <br>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Description</label>
                            <input type="text" name="description" class="md-input md-input-success label-fixed" >
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
                <h3 class="uk-modal-title">Edit Formula</h3>
            </div>
            <div class="uk-modal-body">
                <div class="uk-grid">
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Name <span class="red" >*</span></label>
                            <input type="hidden" name="id" id="formula_id">
                            <input type="text" name="name" class="md-input md-input-success label-fixed" required id="name" >
                        </div>
                    </div>
                    <br>
                    <div class="uk-width-large-1-1" style="margin-top: 15px;">
                        <div class="md-input-wrapper md-input-filled">
                            <label> Form </label>
                            <select name="form" id="form" class="uk-width-1-1 select2" style="width: 100%" >
                                <?php
                                    foreach($forms as $form){
                                        echo '<option value="'.$form->id.'">'.$form->text.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-1" style="margin-top: 15px;">
                        <div class="md-input-wrapper md-input-filled">
                            <label> Strength </label>
                            <select name="strength" id="strength" class="uk-width-1-1 select2" style="width: 100%" >
                                <?php
                                    foreach($strengths as $strength){
                                        echo '<option value="'.$strength->id.'">'.$strength->text.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="uk-width-large-1-1" style="margin-top: 15px;">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Diseases </label>
                            <select name="diseases[]" id="diseases" class="uk-width-1-1 select2" style="width: 100%" multiple >
                                <?php
                                    foreach($diseases as $disease){
                                        echo '<option value="'.$disease->id.'">'.$disease->text.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Code </label>
                            <input type="text" name="code" class="md-input md-input-success label-fixed" id="editgencode" readonly >
                        </div>
                    </div>
                    <br>
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Description </label>
                            <input type="text" name="description" class="md-input md-input-success label-fixed" id="description" >
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
        url:"<?= admin_url('products/get_formulas'); ?>",
        data:data,
        aaSorting: [[1, "desc"]],
        columnDefs: [
            { 
                "targets": 8,
                "orderable": false
            }
        ],
        fixedColumns:   {left: 0,right: 1},
        scrollX: false
    });
</script>
<script>
    $(document).ready(function(){
        function CodeGenerate(){
            var fname = $('#fname').val().replace(" ", "-");
            var formname = $('#formname option:selected').text();
            var strengthname = $('#strengthname option:selected').text();
            $('#newgencode').val(fname+"-"+formname+"-"+strengthname);
        }
        $('#fname,#formname,#strengthname').change(function(){
            CodeGenerate();
        });
        function EditCodeGenerate(){
            var name = $('#name').val().replace(" ", "-");
            var form = $('#form option:selected').text();
            var strength = $('#strength option:selected').text();
            $('#editgencode').val(name+"-"+form+"-"+strength);
        }
        $('#name,#form,#strength').change(function(){
            EditCodeGenerate();
        });
        $('.select2').select2();
        $(document).on('click','.addbtn',function(){
            UIkit.modal('#modal_add').show();
        });
        $(document).on('click','.editbtn',function(){
            $('#formula_id').val($(this).data('id'));
            $('#name').val($(this).data('name'));
            $('#editgencode').val($(this).data('code'));
            $('#form').val($(this).data('form')).trigger('change');
            $('#strength').val($(this).data('strength')).trigger('change');

            var diseses = $(this).data('diseases');
            if(typeof(diseses) == "string"){
                diseses = diseses.split(",");
            }
            $('#diseases').val(diseses).trigger('change');
            $('#description').val($(this).data('description'));
            UIkit.modal('#modal_edit').show();
        });
        $('#addFrom').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/products/insert_formula'); ?>',
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
                url: '<?php echo base_url('admin/products/update_formula'); ?>',
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
                title: "Do you want to delete this formulas",
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
                        title: 'Deleting Formula!',
                        showCancelButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            $.ajax({
                                url: '<?php echo base_url("admin/products/delete_formula"); ?>',
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