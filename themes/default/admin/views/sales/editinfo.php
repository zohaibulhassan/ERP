<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title">Edit Sale Detail</h4>
        </div>

        <div class="modal-body">
            <?php echo form_open('#', 'id="editForm"'); ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Invoice Number</label>
                            <input type="text" class="form-control" name="reference_no" value="<?= $inv->reference_no ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Sale Date </label>
                            <?php echo form_input('date', date_format(date_create($inv->date),"Y-m-d"), 'class="form-control date2" required="required" autocomplete="off"'); ?>
                            <input type="hidden" name="id" value="<?= $inv->id ?>" >
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>PO Date <?= $inv->po_date ?> </label>
                            <?php echo form_input('po_date', date_format(date_create($inv->po_date),"Y-m-d"), 'class="form-control date2" required="required" autocomplete="off"'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>DC Number</label>
                            <input type="text" class="form-control" name="dc_number" value="<?= $inv->dc_num ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>PO Number</label>
                            <input type="text" class="form-control" name="po_number" value="<?= $inv->po_number ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Own Company</label>
                            <?php
                            $oc[''] = '';
                            foreach ($own_company as $own_companies) {
                                $oc[$own_companies->id] = $own_companies->companyname;
                            }
                            echo form_dropdown('own_company', $oc, $inv->own_company, 'class="form-control input-tip select" id="owncompanies" data-placeholder="' . lang("select") . ' ' . lang("own_companies") . '" required="required" style="width:100%;" ');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Biller</label>
                            <?php
                                $billerc[''] = '';
                                foreach ($billerslist as $billerlist) {
                                    $billerc[$billerlist->id] = $billerlist->name;
                                }
                                echo form_dropdown('biller_id', $billerc, $inv->biller_id, 'class="form-control input-tip select" id="biller" data-placeholder="' . lang("select") . ' ' . lang("biller") . '" required="required" style="width:100%;" ');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sletaliers">E-taliers</label>
                            <?php
                                $lcu[''] = '';
                                foreach ($lcustomers as $lcustomer) {
                                    $lcu[$lcustomer->id] = $lcustomer->company;
                                }
                                echo form_dropdown('etaliers', $lcu, $inv->etalier_id, 'id="etaliers" class="form-control input-tip searching_select" data-placeholder="' . lang("select") . ' ' . lang("E-taliers") . '" required="required" style="width:100%;" ');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Order Discount</label>
                            <input type="text" class="form-control" name="discount" value="<?= $inv->order_discount ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Shipping</label>
                            <input type="text" class="form-control" name="shipping" value="<?= $inv->shipping ?>">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Delivery Address</label>
                            <select name="deliveryaddress" class="form-control">
                                <option value="0">Default Address</option>
                                <?php
                                    foreach($addresslist as $address){
                                        echo '<option value="'.$address->id.'" '; if($address->id == $inv->customer_address_id){ echo 'selected'; } echo ' >'.$address->line1.' '.$address->line2.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Payment Term</label>
                            <input type="text" class="form-control" name="payment_term" value="<?= $inv->payment_terms ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Sale Note</label>
                            <?php echo form_textarea('note', $inv->note, 'class="form-control" id="order_note" style="margin-top: 10px; height: 100px;"'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Staff Note</label>
                            <?php echo form_textarea('staff_note', $inv->staff_note, 'class="form-control" id="order_note" style="margin-top: 10px; height: 100px;"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Reason <span style="color:red">*</span></label>
                            <input type="text" name="reason" class="form-control">
                        </div>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="updateBtn">Update</button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(e){
        $('.date2').datetimepicker({
            format: 'yyyy-mm-dd', 
            fontAwesome: true, 
            language: 'sma', 
            todayBtn: 1, 
            autoclose: 1, 
            minView: 2 
        });
        $('#updateBtn').click(function(){
            $('#editForm').submit();    
        });
        $('#editForm').submit(function(e){
            e.preventDefault();
            $('#updateBtn').prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('sales/salesedit'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    $('#updateBtn').prop('disabled', false);
                    var obj = jQuery.parseJSON(data);
                    $('#ajaxCall').hide();
                    
                    if(obj.codestatus == 'ok'){
                        alert('Update Successfully');
                        location.reload();
                    }
                    else{
                        alert(obj.codestatus);
                    }
                },
                error: function(jqXHR, textStatus){
                    var errorStatus = jqXHR.status;
                    if(errorStatus==0){ 
                        alert("Internet Connection Problem");
                    }
                    else{
                        alert('Try Again. Error Code '+errorStatus);
                    }
                    $('#updateBtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                }
            });

        });

    });
</script>







