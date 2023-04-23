<style>
    .uk-open>.uk-dropdown, .uk-open>.uk-dropdown-blank{

    }
</style>
<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Purchase Return</h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                </div>
            </div>
            <div class="md-card-content">
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form','id'=>'returForm');
                    echo admin_form_open("#", $attrib)
                    ?>
                <input type="hidden" name="purchase_id" value="<?= $purchase_id; ?>" >
                <table class="uk-table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Received Qty</th>
                            <th>Balance Qty</th>
                            <th>Returned Quantity</th>
                            <th>Reason</th>
                       </tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach($items as $item){
                        ?>
                            <tr>
                                <td><?php echo $item->product_name; ?></td>
                                <td><?php echo $item->quantity; ?></td>
                                <td><?php echo $item->quantity_balance; ?></td>
                                <td>
                                    <input class="md-input md-input-success label-fixed rquantity" min="0" max="<?php echo $item->quantity_balance; ?>" name="return_qty[]" type="number" value="0">
                                    <input class="md-input md-input-success label-fixed" name="item_id[]" id="itemid-<?php echo $item->id; ?>" type="hidden" value="<?php echo $item->id; ?>">
                                </td>
                                <td>
                                    <input class="md-input md-input-success label-fixed reason" name="reason[]" type="text" value="">
                                </td>
                            </tr>
                        <?php
                            }
                            if(count($items)==0){
                        ?>
                                <tr>
                                    <td colspan="5" style="text-align:center;color:red;font-size:18px;font-weight:bold">All items sold</td>
                                </tr>                                                    
                        <?php
                            }
                        ?>
                    </tbody>
                </table>
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-large-1-1">
                        <button class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light" id="addReturnBtn" type="submit" >Submit</button>
                        <a href="<?php echo base_url('admin/purchases/view/'.$purchase_id); ?>" class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light" type="submit" >Back To Purchase</a>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<!-- datatables -->
<script>
    $(document).ready(function(){
        $('.select2').select2();
        $('#returForm').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/purchases/purchase_return_submit'); ?>',
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
                    }
                    $('#submitbtn').prop('disabled', false);
                }
            });
        });
    });
</script>