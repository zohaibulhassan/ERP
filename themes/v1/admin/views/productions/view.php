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
                                <li><a href="#" class="addpayment"> Add Payment</a></li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
            <div class="md-card-content" >
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                            <span class="uk-text-muted uk-text-small uk-text-italic"><b>Production Detail:</b></span>
                            <address>
                                <p><b>ID:</b> <?php echo $production->id ?></p>
                                <p><b>Date:</b> <?php echo $production->created_at ?></p>
                                <p><b>Product ID:</b> <?php echo $production->product_id ?></p>
                                <p><b>Product Name:</b> <?php echo $production->product_name ?></p>
                                <p><b>Production Quantity:</b> <?php echo $production->quantity ?></p>
                                <p><b>Warehouse:</b> <?php echo $production->warehosue_name ?></p>
                            </address>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                    <div class="uk-margin-bottom">
                            <span class="uk-text-muted uk-text-small uk-text-italic"><b>Manufacture Detail:</b></span>
                            <address>
                                <p><b>Name:</b> <?php echo $production->m_name ?></p>
                                <p><b>Email:</b> <?php echo $production->m_email ?></p>
                                <p><b>Phone:</b> <?php echo $production->m_phone ?></p>
                                <p><b>Address:</b> <?php echo $production->m_address ?></p>
                            </address>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                        </div>
                    </div>
                </div>
                <h4 class="table_heading" >Items List</h4>
                <div class="dt_colVis_buttons"></div>
                <table id="itemsTable" class="uk-table">
                    <thead>
                        <tr>
                            <th>Material ID</th>
                            <th>Material Name</th>
                            <th>Barcode</th>
                            <th>Unit Cost</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($items as $item){
                                ?>
                                <tr>
                                    <td><?php echo $item->material_id ?></td>
                                    <td><?php echo $item->product_name ?></td>
                                    <td><?php echo $item->product_code ?></td>
                                    <td><?php echo $item->rate ?></td>
                                    <td><?php echo $item->quantity ?></td>
                                    <td><?php echo $item->total ?></td>
                                </tr>
                                
                                <?php
                            }
                        
                        ?>
                    </tbody>
                </table>
                <table class="uk-table" style="max-width: 300px;float: right;">
                    <tfoot>
                        <tr>
                            <th style="text-align:right" >Total Material Cost</th>
                            <td><?php echo $production->material_cost ?></td>
                        </tr>
                        <tr>
                            <th style="text-align:right" >Labour Cost</th>
                            <td><?php echo $production->labour_cost ?></td>
                        </tr>
                        <tr>
                            <th style="text-align:right" >Factory Cost</th>
                            <td><?php echo $production->factory_cost ?></td>
                        </tr>
                        <tr>
                            <th style="text-align:right" >Total Cost</th>
                            <td><?php echo $production->total_cost ?></td>
                        </tr>
                        <tr>
                            <th style="text-align:right" >Paid Amount</th>
                            <td><?php echo $production->paid ?></td>
                        </tr>
                        <tr>
                            <th style="text-align:right" >Balance Amount</th>
                            <td><?php echo $production->total_cost-$production->paid ?></td>
                        </tr>
                    </tfoot>
                </table>
                <div style="clear:both"></div>
                <h4 class="table_heading" >Payments List</h4>
                <table class="uk-table" >
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reference No</th>
                            <th>Amount</th>
                            <th>Paid By</th>
                            <th>Note</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($payments as $payment){
                                ?>
                                <tr>
                                    <td><?php echo $payment->date; ?></td>
                                    <td><?php echo $payment->reference_no; ?></td>
                                    <td><?php echo $payment->amount; ?></td>
                                    <td><?php echo $payment->paid_by; ?></td>
                                    <td><?php echo $payment->note; ?></td>
                                    <td>
                                        <button type="button" class="md-btn md-btn-warning md-btn-flat paymentedit" data-id="<?php echo $payment->id; ?>" >Edit</button>
                                        <button type="button" class="md-btn md-btn-danger md-btn-flat paymentdelete" data-id="<?php echo $payment->id; ?>" >Delete</button>
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
    var data = [];
    data[csrfName] = csrfHash;
    data['id'] = '<?php echo $production->id; ?>';
    // New Items
    $(document).ready(function(){
        $(document).on('click','.addpayment',function(){
            $('#modal_ajax .uk-modal-dialog').html("");
            $.ajax({
                url: '<?php echo base_url('admin/productions/add_payment'); ?>',
                type: 'POST',
                data: {[csrfName]:csrfHash,id:<?php echo $production->id; ?>},
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    if(obj.status){
                        $('#modal_ajax .uk-modal-dialog').html(obj.html);
                        UIkit.modal('#modal_ajax').show();
                    }
                    else{
                        toastr.error(obj.message);
                    }
                }
            });
        });
        $(document).on('click','.paymentedit',function(){
            $('#modal_ajax .uk-modal-dialog').html("");
            var id = $(this).data('id');
            $.ajax({
                url: '<?php echo base_url('admin/productions/edit_payment'); ?>',
                type: 'POST',
                data: {[csrfName]:csrfHash,id:id},
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    if(obj.status){
                        $('#modal_ajax .uk-modal-dialog').html(obj.html);
                        UIkit.modal('#modal_ajax').show();
                    }
                    else{
                        toastr.error(obj.message);
                    }
                }
            });
        });
        $(document).on('click','.paymentdelete',function(){
            var id = $(this).data('id');
            Swal.fire({
                title: "Do you want to delete this payment",
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
                        title: 'Deleting payment!',
                        showCancelButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            $.ajax({
                                url: '<?php echo base_url('admin/productions/delete_payment/'); ?>'+id,
                                type: 'POST',
                                data: {[csrfName]:csrfHash,id:id,reason:res.value},
                                success: function(data) {
                                    location.reload();
                                }
                            });
                        }
                    });
                }
            })
        });
    });
                        


</script>



