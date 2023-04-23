<style>
    .uk-open>.uk-dropdown, .uk-open>.uk-dropdown-blank{

    }
    .md-btn:active, .md-btn:focus, .md-btn:hover, .uk-button-dropdown.uk-open>.md-btn {
        background: #69b54a;
        color: white;

    }
    .md-btn>i.material-icons{
        margin-top:0px;
    }
    .uk-dropdown, .uk-dropdown-blank{
        width: auto;
    }
    #dt_tableExport .dtfc-fixed-right{
        /* position: absolute !important; */
    }
</style>
<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Sales Opened</h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                </div>
            </div>
            <div class="md-card-content">
                <div class="dt_colVis_buttons"></div>
                <table id="dt_tableExport2" class="uk-table">
                    <thead>
                        <tr>
                            <th style="width:120px">Date</th>
                            <th>Customer</th>
                            <th>Reference</th>
                            <th>Total Items</th>
                            <th>Grand Total</th>
                            <th class="dt-no-export" >Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($sales as $sale){
                                ?>
                                <tr>
                                    <td><?php echo $sale->date; ?></td>
                                    <td><?php echo $sale->customer; ?></td>
                                    <td><?php echo $sale->reference_no; ?></td>
                                    <td><?php echo $sale->count; ?></td>
                                    <td><?php echo $sale->total; ?></td>
                                    <td>
                                        <a class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini itemedit" href="<?php echo base_url('admin/pos?hold='.$sale->id); ?>" >Open Bill on POS</a>
                                        <button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" type="button" data-id="<?php echo $sale->id; ?>" >Delete</button>
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
    $.DataTableInit2({
        selector:'#dt_tableExport2',
        aaSorting: [[1, "desc"]],
        fixedColumns:   {left: 0,right: 0},
        scrollX: false,
    });
</script>
<script>
    $(document).ready(function(){
        $('.select2').select2();
    });

    $(document).on('click','.deletebtn',function(){
        var id = $(this).data('id');
        Swal.fire({
            title: "Do you want to delete this open sale",
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
                    title: 'Deleting open sale!',
                    showCancelButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                        $.ajax({
                            url: '<?php echo base_url("admin/sales/open_delete"); ?>',
                            type: 'POST',
                            data: {[csrfName]:csrfHash,id:id,reason:res.value},
                            success: function(data) {
                                var obj = jQuery.parseJSON(data);
                                swal.close()
                                if(obj.status){
                                    toastr.success(obj.message);
                                    location.reload();
                                    // $('#dt_tableExport').DataTable().ajax.reload()
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
</script>