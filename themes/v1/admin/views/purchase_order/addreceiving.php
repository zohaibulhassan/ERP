<style>
    .uk-open>.uk-dropdown, .uk-open>.uk-dropdown-blank{

    }
</style>
<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Add Receiving Items</h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                </div>
            </div>
            <div class="md-card-content">
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form','id'=>'addrecived_form');
                    echo admin_form_open("#", $attrib)
                ?>
                <table class="uk-table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Barcode</th>
                            <th>Order Qty</th>
                            <th>Received Qty</th>
                            <th>Unreceived Qty</th>
                            <th>Batch</th>
                            <th style="width:10px" >Receiving Qty</th>
                            <th>Expiry Date</th>
                       </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($items as $item){
                                $checkre = $item->qty-$item->count_receving;
                                if($checkre != 0){
                                ?>
                                    <tr>
                                        <td>
                                            <input name="po_item[]" type="hidden" value="<?php echo $item->id; ?>">
                                            <input name="po_id[]" type="hidden" value="<?php echo $item->purchase_id; ?>">
                                            <input name="product_id[]" type="hidden" value="<?php echo $item->product_id; ?>">
                                            <?php echo $item->product_name; ?> 
                                        </td>
                                        <td>
                                            <?php echo $item->code; ?>
                                        </td>
                                        <td>
                                            <?php echo $item->qty; ?>
                                        </td>
                                        <td>
                                            <?php echo $item->count_receving; ?>
                                        </td>
                                        <td>
                                            <?php echo $item->qty-$item->count_receving; ?>
                                        </td>
                                        <td>
                                            <input class="md-input md-input-success label-fixed" name="batch[]" type="text" autocomplete="off">
                                        </td>
                                        <td>
                                            <input class="md-input md-input-success label-fixed" name="rqty[]" type="number" autocomplete="off" tabindex="2" value="0" data-id="" data-item="" id="" min="0">
                                        </td>
                                        <td>
                                            <input class="md-input  label-fixed" type="text" name="expirydate[]" data-uk-datepicker="{format:'YYYY-MM-DD'}" autocomplete="off" value="" readonly >

                                        </td>
                                    </tr>                                        
                                
                                <?php
                                }
                            }
                        ?>
                        
                    </tbody>
                </table>
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-large-1-1">
                        <button class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light" id="submitbtn" type="submit" >Submit</button>
                        <a href="<?php echo base_url('admin/purchaseorder/view/'.$po_id); ?>" class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light" type="submit" >Back To PO</a>
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
        $('#addrecived_form').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/purchaseorder/submit_recived'); ?>',
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