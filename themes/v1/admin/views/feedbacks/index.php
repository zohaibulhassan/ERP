<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Feedbacks</h3>
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
                            <th>Feedback Date</th>
                            <th>Customer</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Feedback By</th>
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
    <a class="md-fab md-fab-success md-fab-wave waves-effect waves-button" href="<?php echo base_url('admin/feedbacks/add'); ?>"><i class="fa-solid fa-plus"></i></a>
</div> -->
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
        url:"<?= admin_url('feedbacks/get_list'); ?>",
        data:data,
        aaSorting: [[0, "desc"]],
        columnDefs: [
            { 
                "targets": 6,
                "orderable": false
            }
        ],
        fixedColumns:   {left: 0,right: 1},
        scrollX: false
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
        $(document).on('click','.resolvedbtn',function(){
            var feedback_id = $(this).data('id');
            $('#feedback_id').val(feedback_id);
            window.editor.setData('')
            UIkit.modal('#modal_resolved').show();
        });
        $(document).on('click','.feedbackdetail',function(){
            var feedback = $(this).data('feedback');
            console.log(feedback);
            var html = '';
            html += '<div class="uk-overflow-container">';
                html += '<div class="uk-grid uk-grid-divider uk-grid-medium">';
                    html += '<div class="uk-width-large-1-2">';
                        html += '<div class="uk-grid uk-grid-small">';
                            html += '<div class="uk-width-large-1-3">';
                                html += '<span class="uk-text-muted uk-text-small">ID</span>';
                            html += '</div>';
                            html += '<div class="uk-width-large-2-3">';
                                html += '<span class="uk-text-large uk-text-middle">'+feedback.id+'</span>';
                            html += '</div>';
                        html += '</div>';
                        html += '<hr class="uk-grid-divider">';
                        html += '<div class="uk-grid uk-grid-small">';
                            html += '<div class="uk-width-large-1-3">';
                                html += '<span class="uk-text-muted uk-text-small">Customer</span>';
                            html += '</div>';
                            html += '<div class="uk-width-large-2-3">';
                                html += '<span class="uk-text-large uk-text-middle">'+feedback.customer+'</span>';
                            html += '</div>';
                        html += '</div>';
                        html += '<hr class="uk-grid-divider">';
                        html += '<div class="uk-grid uk-grid-small">';
                            html += '<div class="uk-width-large-1-3">';
                                html += '<span class="uk-text-muted uk-text-small">Create By</span>';
                            html += '</div>';
                            html += '<div class="uk-width-large-2-3">';
                                html += '<span class="uk-text-large uk-text-middle">'+feedback.created_by+'</span>';
                            html += '</div>';
                        html += '</div>';
                        html += '<hr class="uk-grid-divider">';
                        html += '<div class="uk-grid uk-grid-small">';
                            html += '<div class="uk-width-large-1-3">';
                                html += '<span class="uk-text-muted uk-text-small">Created Date</span>';
                            html += '</div>';
                            html += '<div class="uk-width-large-2-3">';
                                html += '<span class="uk-text-large uk-text-middle">'+feedback.created_at+'</span>';
                            html += '</div>';
                        html += '</div>';
                        html += '<hr class="uk-grid-divider">';
                    html += '</div>';
                    html += '<div class="uk-width-large-1-2">';
                        html += '<p>';
                            html += '<span class="uk-text-muted uk-text-small uk-display-block uk-margin-small-bottom">Subject</span>';
                            html += feedback.subject;
                        html += '</p>';
                        html += '<hr class="uk-grid-divider">';
                        html += '<p>';
                            html += '<span class="uk-text-muted uk-text-small uk-display-block uk-margin-small-bottom">Feedback Detail</span>';
                            html += feedback.message;
                        html += '</p>';
                        html += '<hr class="uk-grid-divider">';
                    html += '</div>';
                html += '</div>';
            html += '</div>';

            $('#modal_detail .uk-modal-body').html(html);


            UIkit.modal('#modal_detail').show();
        });
        $('#resolvedForm').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/feedbacks/resolved'); ?>',
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
                title: "Do you want to delete this feedback",
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
                                url: '<?php echo base_url("admin/feedbacks/delete"); ?>',
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