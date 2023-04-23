<style>
    .table thead tr th {
        text-align: left !important;
    }
    .tabledb input {

    }
</style>
                        
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i>Creadit Limits By Suppliers (<?= $customer->name?>)</h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="modal" data-target="#addNew" href="#addNew" style="display: block;">
                        <i class="icon fa fa-plus tip" data-placement="left" title="Add New"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-md-12">
                <table class="table tabledb">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Creadit Limit</th>
                            <th>Payment Terms</th>
                            <th>MRP Discount <span >(Only Crossdock Customers)</span></th>
                            <th style="width:100px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($rows as $row){
                        ?>
                        <tr>
                            <td><?= $row->name?></td>
                            <td><?= $this->sma->formatMoney($row->creadit_limit) ?></td>
                            <td><?= $row->durration?> Days</td>
                            <td><?= $row->crossdock_discount?></td>
                            <td>
                                <a style="margin-left:5px;cursor: pointer;" data-id="<?= $row->id ?>" class="deletebtn" ><i class="fa fa-trash-o" ></i></a>
                                <a data-toggle="modal" data-target="#editModel" href="#editModel" class="editBtn" style="margin-left:5px;cursor: pointer;" data-id="<?= $row->id ?>" data-creadit_limit="<?= $row->creadit_limit ?>" data-durration="<?= $row->durration ?>" data-crossdock_discount="<?= $row->crossdock_discount ?>" ><i class="fa fa-edit" ></i></a>
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
<div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Add New</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('#', 'id="addNewForm"'); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Select Supplier</label>
                                <select  class="form-control select2" name="supplier">
                                    <option value="">Select Supplier</option>
                                    <?php
                                        foreach($suppliers as $supplier){
                                            echo '<option value="'.$supplier->id.'" >'.$supplier->name.'</option>';
                                        }
                                    ?>
                                </select>
                                <input type="hidden" name="cid" value="<?= $customer->id; ?>">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Limit</label>
                                <input type="number" class="form-control" name="limit" value="0" >
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Payment Terms</label>
                                <input type="number" class="form-control" name="durration" value="0" >
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>MRP Discount <span >(Only Crossdock Customers)</span></label>
                                <input type="number" class="form-control" name="mrpdiscount" value="0" >
                            </div>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="addNewBtn">Add</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editModel" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Edit</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('#', 'id="editForm"'); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Limit</label>
                                <input type="number" class="form-control" id="editLimit" name="limit" value="0" >
                                <input type="hidden" id="editID" name="id" value="0">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Payment Terms</label>
                                <input type="number" class="form-control" name="durration" value="0" id="editPayterms" >
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>MRP Discount <span >(Only Crossdock Customers)</span></label>
                                <input type="number" class="form-control" name="mrpdiscount" value="0" id="editMRP" >
                            </div>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="edisubmitBtn">Update</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(e){
        $('.select2').select2();
        $('.tabledb').DataTable();
        $('#edisubmitBtn').click(function(){
            $('#edisubmitBtn').prop('disabled', true);
            $('#editForm').submit();    
        });
        $('#editForm').submit(function(e){
            e.preventDefault();
            $('#edisubmitBtn').prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('customers/editlimit'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    $('#ajaxCall').hide();
                    alert(obj.message);
                    if(obj.codestatus == 'ok'){
                        location.reload();
                    }else{
                        $('#edisubmitBtn').prop('disabled', false);
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
                    $('#edisubmitBtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                }
            });
        });
        $('#addNewBtn').click(function(){
            $('#addNewBtn').prop('disabled', true);
            $('#addNewForm').submit();    
        });
        $('#addNewForm').submit(function(e){
            e.preventDefault();
            $('#addNewBtn').prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('customers/addlimit'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    $('#ajaxCall').hide();
                    alert(obj.message);
                    if(obj.codestatus == 'ok'){
                        location.reload();
                    }else{
                        $('#addNewBtn').prop('disabled', false);
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
                    $('#addNewBtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                }
            });

        });
        $(document).on('click','.deletebtn',function(){
            var id = $(this).data('id');
            $(this).prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('customers/deletelimit'); ?>',
                type: 'GET',
                data: {id:id},
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    $('#ajaxCall').hide();
                    alert(obj.message);
                    if(obj.codestatus == 'ok'){
                        location.reload();
                    }else{
                        $(this).prop('disabled', false);
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
                    $(this).prop('disabled', false);
                    $('#ajaxCall').hide();
                }
            });
        });
        $(document).on('click','.editBtn',function(){
            var id = $(this).data('id');
            var creadit_limit = $(this).data('creadit_limit');
            var durration = $(this).data('durration');
            var crossdock_discount = $(this).data('crossdock_discount');
            $('#editID').val(id);
            $('#editPayterms').val(durration);
            $('#editLimit').val(creadit_limit);
            $('#editMRP').val(crossdock_discount);
        });
    });
</script>