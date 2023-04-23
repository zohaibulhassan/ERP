<style>
    .table thead tr th {
        text-align: left !important;
    }
    .tabledb input {

    }
</style>
                        
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i>Product Groups</h2>

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
        <form action="<?= admin_url('products/groups') ?>" method="get">
            <div class="col-lg-3">
                <div class="form-group">
                    <label for="">Warehosue</label>
                    <select name="warehosue" class="form-control">
                        <option value="all">All</option>
                        <?php
                        foreach($warehouses as $w){
                        ?>
                        <option value="<?= $w->id ?>" <?php if($swarehosue == $w->id ){ echo 'selected'; }?> ><?= $w->name ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-lg-2">
                <br>
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
        <div class="row">
            <div class="col-md-12">
                <table class="table tabledb">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Brand</th>
                            <th>Active Products</th>
                            <th>Deactive Products</th>
                            <th>Total Products</th>
                            <th>Available Qty</th>
                            <th style="width:100px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($rows as $row){
                        ?>
                        <tr>
                            <td><?= $row->id?></td>
                            <td><?= $row->name?></td>
                            <td><?= $row->brand?></td>
                            <td><?= $row->active?></td>
                            <td><?= $row->inactive?></td>
                            <td><?= $row->active+$row->inactive?></td>
                            <td><?= $row->qty?></td>
                            <td>
                                <?php 
                                if($row->id != 0){
                                ?>
                                <?php
                                $total = $row->active+$row->inactive;
                                if($total == 0){
                                ?>
                                <a style="margin-left:5px;cursor: pointer;" data-id="<?= $row->id ?>" class="deletebtn" ><i class="fa fa-trash-o" ></i></a>
                                <?php
                                }
                                else{
                                ?>
                                <a style="margin-left:5px;cursor: pointer;" data-toggle="modal" data-target="#listModel" data-products='<?= $row->products ?>' class="productsListBtn" ><i class="fa fa-list" ></i></a>
                                <?php
                                }
                                ?>
                                <a style="margin-left:5px;cursor: pointer;" data-toggle="modal" class="editbtn" data-target="#editModel" data-id="<?php echo $row->id ?>" data-name="<?php echo $row->name ?>" data-brand_id="<?php echo $row->brand_id ?>" ><i class="fa fa-edit"></i></a>
                                <?php
                                }
                                ?>
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
<div class="modal fade" id="editModel" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Edit Group</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('#', 'id="editForm"'); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Select Brand</label>
                                <select  class="form-control select2" name="brand" id="editBrand" >
                                    <option value="">Select Brand</option>
                                    <?php
                                        foreach($brands as $brand){
                                            echo '<option value="'.$brand->id.'" >'.$brand->name.'</option>';
                                        }
                                    ?>
                                </select>
                                <input type="hidden" name="id" id="editID">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" class="form-control" name="name" id="editName">
                            </div>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editBtn">Edit</button>
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
                                <label>Select Brand</label>
                                <select  class="form-control select2" name="brand">
                                    <option value="">Select Brand</option>
                                    <?php
                                        foreach($brands as $brand){
                                            echo '<option value="'.$brand->id.'" >'.$brand->name.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" class="form-control" name="name">
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
<div class="modal fade" id="listModel" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Add New</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="addNewBtn">Add</button>
            </div>
        </div>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/sweetalert2/4.2.4/sweetalert2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/sweetalert2/4.2.4/sweetalert2.min.js"></script>
<script>

    $(document).ready(function(e){
        $('.select2').select2();
        $('.tabledb').DataTable();
        $('#addNewBtn').click(function(){
            $('#addNewBtn').prop('disabled', true);
            $('#addNewForm').submit();    
        });
        $('#editBtn').click(function(){
            $('#editBtn').prop('disabled', true);
            $('#editForm').submit();    
        });
        $('#editForm').submit(function(e){
            e.preventDefault();
            $('#editBtn').prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('products/editgroups'); ?>',
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
                        $('#editBtn').prop('disabled', false);
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
                    $('#editBtn').prop('disabled', false);
                    $('#ajaxCall').hide();
                }
            });

        });
        $('#addNewForm').submit(function(e){
            e.preventDefault();
            $('#addNewBtn').prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('products/addgroups'); ?>',
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
        $(document).on('click','.editbtn',function(){
            var id = $(this).data('id');
            var name = $(this).data('name');
            var brand_id = $(this).data('brand_id');
            $('#editID').val(id);
            $('#editBrand').val(brand_id);
            $('#editBrand').trigger('change');
            $('#editName').val(name);
        });
        $(document).on('click','.deletebtn',function(){
            var id = $(this).data('id');
            $(this).prop('disabled', true);
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('products/deletegroups'); ?>',
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
        $(document).on('click','.productsListBtn',function(){
            var products = $(this).data('products');
            var html = '<table class="table" >';
                html += '<thead>';
                    html += '<tr>';
                        html += '<th>ID</th>';
                        html += '<th>Name</th>';
                        html += '<th>MRP</th>';
                        html += '<th>Qty</th>';
                        html += '<th>Status</th>';
                    html += '</tr>';
                html += '</thead>';
                html += '<tbody>';
                products.forEach(function(p) {
                    html += '<tr>';
                        html += '<td>'+p.id+'</td>';
                        html += '<td>'+p.name+'</td>';
                        html += '<td>'+p.mrp+'</td>';
                        html += '<td>'+p.qty+'</td>';
                        html += '<td>';
                        if(p.status == 1){
                            html += '<span style="color:green" >Active</span>';
                        }
                        else{
                            html += '<span style="color:red" >Deactive</span>';
                        }
                        html += '</td>';
                    html += '</tr>';
                });
                html += '</tbody>';
            html += '</table>';
            $('#listModel .modal-body').html(html);
            // swal.fire({
            //     title: 'Products List',
            //     html: html,
            //     width: 700
            // })
            console.log(products);
        });

    });
</script>