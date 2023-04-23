<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
    #POData a{
        cursor:pointer;
    }
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i
                class="fa-fw fa fa-star"></i>Batch adjustments
        </h2>
        <?php
            if($Owner || $Admin || $GP['purchase_adj_add']){
            ?>
            <div class="box-icon">
                <ul class="btn-tasks">
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?=lang("actions")?>"></i></a>
                        <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                            <li>
                                <a  data-toggle="modal" data-target="#addProcuct">
                                    <i class="fa fa-plus"></i> Add Batch Adjustment
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <?php
            }
        
        ?>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?=lang('list_results');?></p>

                <div class="table-responsive">
                    <table id="POData" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr class="active">
                            <th>Date</th>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Old Batch</th>
                            <th>Batch</th>
                            <th>Qty</th>
                            <th>Expiry</th>
                            <th>Cost</th>
                            <th>Price</th>
                            <th>Dropship</th>
                            <th>Crossdock</th>
                            <th>MRP</th>
                            <th>Adjust By</th>
                            <th style="width:100px;"><?= lang("actions"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach($rows as $row){
                                    ?>
                                    <tr>
                                        <td><?= $row->adj_date ?></td>
                                        <td><?= $row->product_id ?></td>
                                        <td><?= $row->product_name ?></td>
                                        <td><?= $row->old_batch ?></td>
                                        <td><?= $row->batch ?></td>
                                        <td><?= $row->quantity_received ?></td>
                                        <td><?= $row->expiry ?></td>
                                        <td><?= $row->net_unit_cost ?></td>
                                        <td><?= $row->price ?></td>
                                        <td><?= $row->dropship ?></td>
                                        <td><?= $row->crossdock ?></td>
                                        <td><?= $row->mrp ?></td>
                                        <td><?= $row->first_name.' '.$row->last_name ?></td>
                                        <td>
                                            <?php
                                                if($Owner || $Admin || $GP['purchase_adj_edit']){
                                                    ?>
                                                    <a href="#" data-id="<?=  $row->id ?>" class="editbtn" ><i class="fa fa-edit"></i></a>
                                                    <?php
                                                }
                                            ?>                                            
                                            <?php
                                                if($Owner || $Admin || $GP['purchase_adj_delete']){
                                                ?>
                                                <a  data-id="<?=  $row->id ?>" class="deletebtn" ><i class="fa fa-trash"></i></a>
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
</div>
<?php
    if($Owner || $Admin || $GP['purchase_adj_add']){
    ?>
    <div class="modal fade in" id="addProcuct" tabindex="-1" role="dialog" aria-labelledby="addProductLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i></button>
                    <h4 class="modal-title" id="addProductLabel">Add Batch Adjustment</h4>
                </div>
                <?php echo form_open('#', 'id="add-form"'); ?>
                <div class="modal-body">
                    <p><?= lang('enter_info'); ?></p>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Product</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="hidden" name="product" id="product" class="form-control" style="width:100%;" placeholder="<?= lang("select") . ' Product' ?>" required >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Warehouse</label>
                                <div class="input-group" style="width: 100%;">
                                    <select name="warehouse" id="warehouse" class="form-control searching_select">
                                        <option value="all">All Warehouse</option>
                                        <?php
                                            foreach($warehouses as $warehouse){
                                                echo '<option value="'.$warehouse->id.'">'.$warehouse->name.'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Select Batch</label>
                                <div class="input-group" style="width: 100%;">
                                    <select name="batch" id="batch" class="form-control searching_select ">
                                        <option value="">No Discount</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Now Available Quantity</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="text" name="available_qty" id="available_qty" class="form-control" style="width:100%;" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Adjustment Quantity</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="number" min="0" name="adj_qty" id="adj_qty" class="form-control" style="width:100%;" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Now Expiry Date</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="text" name="expiry_date" id="expiry_date" class="form-control" style="width:100%;" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Adjustment Expiry Date</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="text" autocomplete="off" name="adj_expiry" id="adj_expiry" class="form-control expirydate" style="width:100%;" value="">
                                </div>
                            </div>
                        </div>
                        <?php 
                            if($Owner || $Admin || $GP['purchase_adj_price']){
                            ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Now Cost Price</label>
                                    <div class="input-group" style="width: 100%;">
                                        <input type="text" name="costprice" id="costprice" class="form-control" style="width:100%;" value="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Adjustment Cost Price</label>
                                    <div class="input-group" style="width: 100%;">
                                        <input type="number" min="0" name="adj_costprice" id="adj_costprice" class="form-control" style="width:100%;" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Now TP Price</label>
                                    <div class="input-group" style="width: 100%;">
                                        <input type="text" name="tp_price" id="tp_price" class="form-control" style="width:100%;" value="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Adjustment TP Price</label>
                                    <div class="input-group" style="width: 100%;">
                                        <input type="number" min="0" name="adj_tp_price" id="adj_tp_price" class="form-control" style="width:100%;" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Now Dropship Price</label>
                                    <div class="input-group" style="width: 100%;">
                                        <input type="text" name="dropship_price" id="dropship_price" class="form-control" style="width:100%;" value="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Adjustment Dropship Price</label>
                                    <div class="input-group" style="width: 100%;">
                                        <input type="number" min="0" name="adj_dropship_price" id="adj_dropship_price" class="form-control" style="width:100%;" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Now Crossdock Price</label>
                                    <div class="input-group" style="width: 100%;">
                                        <input type="text" name="crossdock_price" id="crossdock_price" class="form-control" style="width:100%;" value="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Adjustment Crossdock Price</label>
                                    <div class="input-group" style="width: 100%;">
                                        <input type="number" min="0" name="adj_crossdock_price" id="adj_crossdock_price" class="form-control" style="width:100%;" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Now MRP</label>
                                    <div class="input-group" style="width: 100%;">
                                        <input type="text" name="mrp_price" id="mrp_price" class="form-control" style="width:100%;" value="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Adjustment MRP</label>
                                    <div class="input-group" style="width: 100%;">
                                        <input type="number" min="0" name="adj_mrp_price" id="adj_mrp_price" class="form-control" style="width:100%;" value="">
                                    </div>
                                </div>
                            </div>
                            <?php
                            }
                        ?>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Adjustment Reason</label>
                                <div class="input-group" style="width: 100%;">
                                    <input type="text" name="reason" class="form-control" style="width:100%;" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" id="addproduct-btn" class="btn btn-primary" value="Submit">
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <?php 
    }
?>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-flash-1.6.4/b-html5-1.6.4/b-print-1.6.4/sb-1.0.0/datatables.min.js"></script>

<script>
    $(document).ready(function(){
        $('#POData').DataTable({
            dom: 'Bfrtip',
            paging: false,
            buttons: [
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7]
                    }
                },
                {
                    extend: 'csv',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7]
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7]
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7]
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7]
                    }
                },
            ]
        });
        <?php
            if($Owner || $Admin || $GP['purchase_adj_add']){
            ?>
                // Add Adjustment
                $('#product').select2({
                    minimumInputLength: 1,
                    data: [],
                    initSelection: function(element, callback) {
                        console.log(element);
                        // $.ajax({
                        //     type: "get",
                        //     async: false,
                        //     url: '<?= admin_url('stores/discountlist'); ?>' + $(element).val(),
                        //     success: function(data) {
                        //         $('#discount').html(data);
                        //     }
                        // });
                    },
                    ajax: {
                        url: '<?= admin_url('stores/productslist'); ?>',
                        dataType: 'json',
                        quietMillis: 15,
                        data: function(term, page) {
                            return {
                                term: term,
                                limit: 15
                            };
                        },
                        results: function(data, page) {
                            if (data.results != null) {
                                return { results: data.results };
                            } else {
                                return { results: [{ id: '', text: 'No Match Found' }] };
                            }
                        }
                    }
                });
                $('#product').change(function(){
                    var pid = $(this).val();
                    var wid = $('#warehouse').val();
                    setBatchs(pid,wid)
        
                });
                $('#warehouse').change(function(){
                    var wid = $(this).val();
                    var pid = $('#product').val();
                    setBatchs(pid,wid)
        
                });
                function setBatchs(pid,wid){
                    $.ajax({
                        type: "get",
                        data: {pid:pid,wid:wid},
                        async: false,
                        url: '<?= admin_url('purchases/batchslist'); ?>',
                        success: function(data) {
                            $('#batch').html(data);
                            $("#batch").select2("destroy").select2();
                            $('#batch').val($('#batch option:eq(1)').val()).trigger('change');
                            setValue()
                        }
                    });
        
                }
                $('#batch').change(function(){
                    setValue();
                });
                function setValue(){
                    var batchval = $('#batch').val();
                    if(batchval != ""){
                        $('#available_qty').val($('#batch option:selected').data('available'));
                        $('#adj_qty').val($('#batch option:selected').data('available'));
                        $('#expiry_date').val($('#batch option:selected').data('expiry'));
                        $('#adj_expiry').val($('#batch option:selected').data('expiry'));
                        <?php
                            if($Owner || $Admin || $GP['purchase_adj_price']){
                            ?>
                            $('#costprice').val($('#batch option:selected').data('cost'));
                            $('#adj_costprice').val($('#batch option:selected').data('cost'));
                            $('#tp_price').val($('#batch option:selected').data('tpprice'));
                            $('#adj_tp_price').val($('#batch option:selected').data('tpprice'));
                            $('#dropship_price').val($('#batch option:selected').data('dropship'));
                            $('#adj_dropship_price').val($('#batch option:selected').data('dropship'));
                            $('#crossdock_price').val($('#batch option:selected').data('crossdock'));
                            $('#adj_crossdock_price').val($('#batch option:selected').data('crossdock'));
                            $('#mrp_price').val($('#batch option:selected').data('mrp'));
                            $('#adj_mrp_price').val($('#batch option:selected').data('mrp'));
                            <?php
                            }
                        ?>
                    }
                    else{
                        $('#available_qty').val($('#batch option:selected').val(''));
                        $('#adj_qty').val($('#batch option:selected').val(''));
                        $('#expiry_date').val($('#batch option:selected').val(''));
                        $('#adj_expiry').val($('#batch option:selected').val(''));
                        <?php
                            if($Owner || $Admin || $GP['purchase_adj_price']){
                            ?>
                            $('#costprice').val($('#batch option:selected').val(''));
                            $('#adj_costprice').val($('#batch option:selected').val(''));
                            $('#tp_price').val($('#batch option:selected').val(''));
                            $('#adj_tp_price').val($('#batch option:selected').val(''));
                            $('#dropship_price').val($('#batch option:selected').val(''));
                            $('#adj_dropship_price').val($('#batch option:selected').val(''));
                            $('#crossdock_price').val($('#batch option:selected').val(''));
                            $('#adj_crossdock_price').val($('#batch option:selected').val(''));
                            $('#mrp_price').val($('#batch option:selected').val(''));
                            $('#adj_mrp_price').val($('#batch option:selected').val(''));
                            <?php
                            }
                        ?>
                    }
                }
                $('#addproduct-btn').click(function(){
                    $('#add-form').submit();
                });
                $('#add-form').submit(function(e){
                    e.preventDefault();
                    $('#ajaxCall').show();
                    $.ajax({
                        url: '<?= admin_url('purchases/addadjustment'); ?>',
                        type: 'POST',
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function(data){
                            var obj = jQuery.parseJSON(data);
                            alert(obj.message);
                            if(obj.codestatus){
                                location.reload();
                            }
                            $('#ajaxCall').hide();
                        },
                        error: function(jqXHR, textStatus){
                            var errorStatus = jqXHR.status;
                            if(errorStatus==0){ 
                                console.log('Internet Connection Problem');
                            }
                            else{
                                console.log('Try Again. Error Code '+errorStatus);
                            }
                            $('#ajaxCall').hide();
                        }
                    });
                });

            <?php
            }
        ?>
        $("input[type='number']").change(function(){
            var numberval = $(this).val();
            if(numberval == ""){
                $(this).val(0)
            }
        });
        <?php
            if($Owner || $Admin || $GP['purchase_adj_delete']){
            ?>
            $('#POData').on('click','.deletebtn',function(){
                var id = $(this).data('id');
                var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
                csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you want to delete this adjustment!",
                    icon: "warning",
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    showLoaderOnConfirm: true,
                    preConfirm: (reason) => {
                        console.log(reason);
                        $.ajax({
                            type: "get",
                            url: '<?= base_url('admin/purchases/deletebatchadj?id=') ?>',
                            data: {
                                'id': id,
                                'reason': reason,
                                [csrfName]:csrfHash
                            },
                            success: function(data) {
                                var obj = jQuery.parseJSON(data);
                                if(obj.codestatus == "Batch Adjustment Successfully"){
                                    Swal.fire({
                                        title: obj.codestatus ,
                                        icon: "success",
                                    });
                                    location.reload();
                                }
                                else{
                                    Swal.fire({
                                        title: obj.codestatus,
                                        icon: "error",
                                    });
                                }
                                throw new Error(obj.codestatus);
                            },
                            error: function(jqXHR, textStatus){
                                var errorStatus = jqXHR.status;
                                Swal.fire({
                                    title: errorStatus,
                                    icon: "error",
                                });
                                throw new Error(errorStatus);
                            }
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                })
                .then((result) => {
                    console.log('CR: '+JSON.stringify(result));
                    if (result.isConfirmed) {
                    }
                });
    
    
    
    
    
    
            });
            <?php
            }
        ?>
        $('.expirydate').datetimepicker({
            format: 'dd/mm/yyyy', 
            fontAwesome: true, 
            language: 'sma', 
            todayBtn: 1, 
            autoclose: 1, 
            minView: 2 
        });
    });
</script>