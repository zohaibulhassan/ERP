<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Payment Collections</h3>
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
                            <th>Date</th>
                            <th>Collect By</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Pay By</th>
                            <th>Cheque No</th>
                            <th>Note</th>
                            <th>Received Date</th>
                            <th>Received By</th>
                            <th>Status</th>
                            <th class="dt-no-export" >Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- <div class="md-fab-wrapper md-fab-in-card" style="position: fixed;bottom: 20px;">
    <a class="md-fab md-fab-success md-fab-wave waves-effect waves-button" href="<?php echo base_url('admin/paymentcollections/add'); ?>"><i class="fa-solid fa-plus"></i></a>
</div> -->
<div class="uk-modal" id="modal_resolved">
    <?php
        $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'resolvedForm');
        echo admin_form_open_multipart("#", $attrib);
    ?>
        <div class="uk-modal-dialog">
            <div class="uk-modal-header">
                <h3 class="uk-modal-title">Resolved</h3>
            </div>
            <div class="uk-modal-body">
                <div class="uk-form-row">
                    <div class="md-input-wrapper md-input-filled" >
                        <input type="hidden" value="0" name="paymentcollect_id" id="paymentcollect_id">
                        <label>Remarks</label>
                    </div>
                    <div class="md-input-wrapper md-input-filled" >
                        <textarea name="remarks" class="md-input no_autosize" id="editor" style="min-height:250px" >dfsdfwesf3w4</textarea>
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
<div class="uk-modal" id="modal_detail">
    <div class="uk-modal-dialog uk-modal-dialog-large">
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Detail</h3>
        </div>
        <div class="uk-modal-body">
        </div>
        <div class="uk-modal-footer uk-text-right">
            <button type="button" class="md-btn md-btn-flat uk-modal-close">Close</button>
        </div>
    </div>
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
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.colVis.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-fixedcolumns/dataTables.fixedColumns.min.js"></script>
<!-- datatables custom integration -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/custom/datatables/datatables.uikit.min.js"></script>
<!-- CK Editor 5 -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/ckeditor5/ckeditor.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/datatable.js"></script>
<script>
    var csrfName = "<?php echo $this->security->get_csrf_token_name(); ?>",
        csrfHash = "<?php echo $this->security->get_csrf_hash(); ?>";
    var data = {
        [csrfName]:csrfHash,
        customer:'<?php echo $scustomer ?>',
        status:'<?php echo $sstatus ?>',
        to:'<?php echo $sto ?>',
        from:'<?php echo $sfrom ?>'
    }
    $.DataTableInit({
        selector:'#dt_tableExport',
        url:"<?= admin_url('paymentcollections/get_list'); ?>",
        data:data,
        aaSorting: [[0, "desc"]],
        columnDefs: [
            { 
                "targets": 11,
                "orderable": false
            }
        ],
        fixedColumns:   {left: 0,right: 1},
        scrollX: true
    });
</script>



<script>
    ClassicEditor
    .create( document.querySelector( '#editor' ),{
        toolbar: {
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ],
            shouldNotGroupWhenFull: true
        }
    })
    .then( editor => {
        window.editor = editor;
    })
    .catch( error => {
        console.error( error );
    });

</script>

<script>
    $(document).ready(function(){
        $(document).on('click','.paymentcollectdetail',function(){
            var paymentcollect = $(this).data('paymentcollect');
            console.log(paymentcollect);
            var html = '';
            html += '<div class="uk-overflow-container">';
                html += '<div class="uk-grid uk-grid-divider uk-grid-medium">';
                    html += '<div class="uk-width-large-1-2">';
                        html += '<div class="uk-grid uk-grid-small">';
                            html += '<div class="uk-width-large-1-3">';
                                html += '<span class="uk-text-muted uk-text-small">ID</span>';
                            html += '</div>';
                            html += '<div class="uk-width-large-2-3">';
                                html += '<span class="uk-text-large uk-text-middle">'+paymentcollect.id+'</span>';
                            html += '</div>';
                        html += '</div>';
                        html += '<hr class="uk-grid-divider">';
                        html += '<div class="uk-grid uk-grid-small">';
                            html += '<div class="uk-width-large-1-3">';
                                html += '<span class="uk-text-muted uk-text-small">Customer</span>';
                            html += '</div>';
                            html += '<div class="uk-width-large-2-3">';
                                html += '<span class="uk-text-large uk-text-middle">'+paymentcollect.customer+'</span>';
                            html += '</div>';
                        html += '</div>';
                        html += '<hr class="uk-grid-divider">';
                        html += '<div class="uk-grid uk-grid-small">';
                            html += '<div class="uk-width-large-1-3">';
                                html += '<span class="uk-text-muted uk-text-small">Create By</span>';
                            html += '</div>';
                            html += '<div class="uk-width-large-2-3">';
                                html += '<span class="uk-text-large uk-text-middle">'+paymentcollect.created_by+'</span>';
                            html += '</div>';
                        html += '</div>';
                        html += '<hr class="uk-grid-divider">';
                        html += '<div class="uk-grid uk-grid-small">';
                            html += '<div class="uk-width-large-1-3">';
                                html += '<span class="uk-text-muted uk-text-small">Created Date</span>';
                            html += '</div>';
                            html += '<div class="uk-width-large-2-3">';
                                html += '<span class="uk-text-large uk-text-middle">'+paymentcollect.created_at+'</span>';
                            html += '</div>';
                        html += '</div>';
                        html += '<hr class="uk-grid-divider">';
                        html += '<div class="uk-grid uk-grid-small">';
                            html += '<div class="uk-width-large-1-3">';
                                html += '<span class="uk-text-muted uk-text-small">Priority</span>';
                            html += '</div>';
                            html += '<div class="uk-width-large-2-3">';
                                html += '<span class="uk-text-large uk-text-middle">'+paymentcollect.priority+'</span>';
                            html += '</div>';
                        html += '</div>';
                        html += '<hr class="uk-grid-divider">';
                        html += '<div class="uk-grid uk-grid-small">';
                            html += '<div class="uk-width-large-1-3">';
                                html += '<span class="uk-text-muted uk-text-small">Resolved By</span>';
                            html += '</div>';
                            html += '<div class="uk-width-large-2-3">';
                                html += '<span class="uk-text-large uk-text-middle">'+paymentcollect.resolved_by+'</span>';
                            html += '</div>';
                        html += '</div>';
                        html += '<hr class="uk-grid-divider">';
                        html += '<div class="uk-grid uk-grid-small">';
                            html += '<div class="uk-width-large-1-3">';
                                html += '<span class="uk-text-muted uk-text-small">Resolved Date</span>';
                            html += '</div>';
                            html += '<div class="uk-width-large-2-3">';
                                html += '<span class="uk-text-large uk-text-middle">'+paymentcollect.resolved_date+'</span>';
                            html += '</div>';
                        html += '</div>';
                        html += '<hr class="uk-grid-divider">';
                        html += '<div class="uk-grid uk-grid-small">';
                            html += '<div class="uk-width-large-1-3">';
                                html += '<span class="uk-text-muted uk-text-small">Status</span>';
                            html += '</div>';
                            html += '<div class="uk-width-large-2-3">';
                                html += '<span class="uk-text-large uk-text-middle">'; 
                                    if(paymentcollect.status == 0){
                                        html += 'Not Resolved';
                                    }
                                    else{
                                        html += 'Resolved';
                                    }
                                html += '</span>';
                            html += '</div>';
                        html += '</div>';
                        html += '<hr class="uk-grid-divider">';
                    html += '</div>';
                    html += '<div class="uk-width-large-1-2">';
                        html += '<p>';
                            html += '<span class="uk-text-muted uk-text-small uk-display-block uk-margin-small-bottom">Subject</span>';
                            html += paymentcollect.subject;
                        html += '</p>';
                        html += '<hr class="uk-grid-divider">';
                        html += '<p>';
                            html += '<span class="uk-text-muted uk-text-small uk-display-block uk-margin-small-bottom">Payment Collection Detail</span>';
                            html += paymentcollect.message;
                        html += '</p>';
                        html += '<hr class="uk-grid-divider">';
                        html += '<p>';
                            html += '<span class="uk-text-muted uk-text-small uk-display-block uk-margin-small-bottom">Resolved Detail</span>';
                            html += paymentcollect.remarks;
                        html += '</p>';
                        html += '<hr class="uk-grid-divider">';
                    html += '</div>';
                html += '</div>';
            html += '</div>';

            $('#modal_detail .uk-modal-body').html(html);


            UIkit.modal('#modal_detail').show();
        });

        $(document).on('click','.deletebtn',function(){
            var id = $(this).data('id');
            Swal.fire({
                title: "Do you want to delete this payment collection",
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
                                url: '<?php echo base_url("admin/paymentcollections/delete"); ?>',
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
        $(document).on('click','.receivedbtn',function(){
            var id = $(this).data('id');
            Swal.fire({
                title: "Do you receive this payment collection",
                input: "text",
                showCancelButton: true,
                confirmButtonColor: "#7cb342",
                confirmButtonText: "Received",
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
                                url: '<?php echo base_url("admin/paymentcollections/received"); ?>',
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