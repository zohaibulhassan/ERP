<style>
    .uk-open>.uk-dropdown, .uk-open>.uk-dropdown-blank{

    }
</style>
<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Sale Return</h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                </div>
            </div>
            <div class="md-card-content">
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form','id'=>'returnForm');
                    echo admin_form_open("#", $attrib)
                    ?>
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-large-1-3">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Payment Refund <span class="red" >*</span></label>
                            <select name="refund" class="uk-width-1-1" required id="refund">
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-large-1-1">
                        <input type="hidden" name="sale_id" value="<?= $sale_id; ?>" >
                        <table class="uk-table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Barcode</th>
                                    <th>Order Qty</th>
                                    <th>Batch</th>
                                    <th>Return Qty</th>
                                    <th>Reason</th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                foreach($items as $item){
                                ?>
                                    <tr>
                                        <td>
                                            <input name="saleitem_id[]" type="hidden" value="<?php echo $item->id; ?>">
                                            <?php echo $item->product_name; ?> 
                                        </td>
                                        <td>
                                            <span><?php echo $item->product_code; ?></span>
                                        </td>
                                        <td class="text-right">
                                            <span class="text-right scost" id=""><?php echo $item->quantity; ?></span>
                                        </td>
                                        <td class="text-right">
                                            <span class="text-right scost" id=""><?php echo $item->batch; ?></span>
                                        </td>
                                        <td>
                                            <input class="form-control text-center returnqtytxt" name="rqty[]" type="number" autocomplete="off" tabindex="2" value="0" data-id="" data-item="" id="" min="0" max="<?php echo (int)$item->quantity; ?>" data-max="<?php echo (int)$item->quantity; ?>">
                                        </td>
                                        <td>
                                            <input class="form-control " name="reason[]" type="text" autocomplete="off">
                                        </td>
                                    </tr>
                                <?php
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="uk-width-large-1-1">
                        <button class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light" id="submitbtn" type="submit" >Submit</button>
                        <a href="<?php echo base_url('admin/sales/detail/'.$sale_id); ?>" class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light" type="submit" >Back To Sale</a>
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
        $('#returnForm').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/sales/return_sale_submit'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    alert(data);
                    if(data == 'Return Successfully'){
                        window.top.location.href = '<?= admin_url('sales/detail/'.$sale_id); ?>';
                    }
                    $('#submitbtn').prop('disabled', false);
                }
            });
        });
    });
</script>