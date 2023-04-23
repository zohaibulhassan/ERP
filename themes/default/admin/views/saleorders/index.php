<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD ?>
<style>
    .dataTables_length {
        margin-right:15px;
    }
    #myTable tr, #myTable tr td{
        cursor: pointer;
    }
</style>    

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i
                class="fa-fw fa fa-star"></i>Sale Orders
        </h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?=lang("actions")?>"></i></a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <?php 
                            if($Owner){
                        ?>
                            <li>
                                <a href="<?=admin_url('salesorders/add')?>">
                                    <i class="fa fa-plus-circle"></i> Add Sale Order
                                </a>
                            </li>
                        <?php
                            }
                        ?>
                    </ul>
                </li>
                <?php if (!empty($warehouses)) {
                    ?>
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-building-o tip" data-placement="left" title="<?=lang("warehouses")?>"></i></a>
                        <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                            <li><a href="<?=admin_url('purchases')?>"><i class="fa fa-building-o"></i> <?=lang('all_warehouses')?></a></li>
                            <li class="divider"></li>
                            <?php
                            	foreach ($warehouses as $warehouse) {
                                    echo '<li ' . ($warehouse_id && $warehouse_id == $warehouse->id ? 'class="active"' : '') . '><a href="' . admin_url('purchases/' . $warehouse->id) . '"><i class="fa fa-building"></i>' . $warehouse->name . '</a></li>';
                                }
                            ?>
                        </ul>
                    </li>
                <?php }
                ?>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <form action="<?= admin_url('salesorders') ?>" method="get">
            <div class="col-lg-2">
                <div class="form-group">
                    <?= lang("suppliers", "suppliers"); ?>
                    <?php
                    $bl["all"] = "All";
                    foreach ($suppliers as $supplier) {
                        $bl[$supplier->id] = $supplier->name;
                    }
                    echo form_dropdown('supplier', $bl, $ssupplier, 'id="suppliers" data-placeholder="' . lang("select") . ' ' . lang("supplier") . '" required="required" class="form-control input-tip searching_select" style="width:100%;"');
                    ?>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <?= lang("customers", "customers"); ?>
                    <?php
                    $bl["all"] = "All";
                    foreach ($customers as $customer) {
                        $bl[$customer->id] = $customer->name;
                    }
                    echo form_dropdown('customers', $bl, $scustomer, 'id="customers" data-placeholder="' . lang("select") . ' ' . lang("cuctomer") . '" required="required" class="form-control input-tip searching_select" style="width:100%;"');
                    ?>
                </div>
            </div>
            <?php
                if($user_warehouses == ""){
            ?>
            <div class="col-lg-2">
                <div class="form-group">
                    <label class="control-label" for="warehouse">
                        <?= lang("warehouse"); ?>
                    </label>
                    <?php
                    $whl[""] = lang('select') . ' ' . lang('warehouse');
                    foreach ($warehouses as $warehouse) {
                        $whl[$warehouse->id] = $warehouse->name;
                    }
                    echo form_dropdown('warehouse', $whl, $swarehouse, 'class="form-control searching_select" id="warehouse" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("warehouse") . '"');
                    ?>
                </div>
            </div>
            <?php
                }
            ?>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="text" name="start_date" value="<?php echo $start?>" class="form-control date2" id="start_date" autocomplete="off" />
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="text" name="end_date" value="<?php echo $end?>" class="form-control date2" id="end_date" autocomplete="off"/>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <label for="">Operation Team Status</label>
                    <select name="otstatus" class="form-control">
                        <option value="all">All</option>
                        <option value="pending" <?php if($otstatus == "pending" ){ echo 'selected'; }?> >Pending</option>
                        <option value="partial dispatch" <?php if($otstatus == "partial dispatch" ){ echo 'selected'; }?> >Partial Dispatch</option>
                        <option value="complete dispatch" <?php if($otstatus == "complete dispatch" ){ echo 'selected'; }?> >Complete Dispatch</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <label for="">Account Team Status</label>
                    <select name="atstatus" class="form-control">
                        <option value="all">All</option>
                        <option value="pending" <?php if($atstatus == "pending" ){ echo 'selected'; }?> >Pending</option>
                        <option value="partial invoiced" <?php if($atstatus == "partial invoiced" ){ echo 'selected'; }?> >Partial Invoiced</option>
                        <option value="completed invoiced" <?php if($atstatus == "completed invoiced" ){ echo 'selected'; }?> >Completed Invoiced</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <label for="">SO Status</label>
                    <select name="status" class="form-control">
                        <option value="all">All</option>
                        <option value="pending" <?php if($status == "pending" ){ echo 'selected'; }?> >Pending</option>
                        <option value="partial" <?php if($status == "partial" ){ echo 'selected'; }?> >Partial</option>
                        <option value="completed" <?php if($status == "completed" ){ echo 'selected'; }?> >Completed</option>
                        <option value="close" <?php if($status == "close" ){ echo 'selected'; }?> >Close</option>
                        <option value="cancel" <?php if($status == "cancel" ){ echo 'selected'; }?> >Cancel</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <label for="">SO Type</label>
                    <select name="sot" class="form-control">
                        <option value="all">All</option>
                        <option value="a" <?php if($sot == "a" ){ echo 'selected'; }?> >Auto SO</option>
                        <option value="m" <?php if($sot == "m" ){ echo 'selected'; }?> >Menual SO</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2">
                <br>
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
        <div class="row">
            <div class="col-lg-12">
                <form action="<?= admin_url('salesorders/merged') ?>" method="get">
                    <div class="table-responsive">
                        <table id="myTable" class="table table-bordered table-hover table-striped">
                            <thead>
                            <tr class="active">
                                <th style="min-width:30px; width: 30px; text-align: center;">
                                    <input class="checkbox checkft" type="checkbox" name="check"/>
                                </th>
                                <th>Date</th>
                                <th>Ref No</th>
                                <th>PO No</th>
                                <th>Supplier</th>
                                <th>Custumer</th>
                                <th>Warehouse</th>
                                <th>Demand Quantity</th>
                                <th>Demand Value</th>
                                <th>Complete Quantity</th>
                                <th>Complete Value</th>
                                <th>Quantity Percentage</th>
                                <th>Value Percentage</th>
                                <th>Create By</th>
                                <th>Account Status</th>
                                <th>Operation Status</th>
                                <th>SO Status</th>
                                <th style="width:100px;"><?= lang("actions"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot class="dtFilter">
                            <tr class="active">
                                <th style="min-width:30px; width: 30px; text-align: center;">
                                    <input class="checkbox checkft" type="checkbox" name="check"/>
                                </th>
                                <th>Date</th>
                                <th>Ref No</th>
                                <th>PO No</th>
                                <th>Supplier</th>
                                <th>Custumer</th>
                                <th>Warehouse</th>
                                <th>Demand Quantity</th>
                                <th>Demand Value</th>
                                <th>Complete Quantity</th>
                                <th>Complete Value</th>
                                <th>Quantity Percentage</th>
                                <th>Value Percentage</th>
                                <th>Create By</th>
                                <th>Account Status</th>
                                <th>Operation Status</th>
                                <th>SO Status</th>
                                <th style="width:100px;"><?= lang("actions"); ?></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-info" >Merged Detail</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="summaryModel" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Sale Order Summary</h4>
            </div>
            <div class="modal-body">
                <table class="table table=bordered" id="summaryTable">
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $assets; ?>plugins/sweetalert/sweetalert.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-flash-1.6.4/b-html5-1.6.4/b-print-1.6.4/sb-1.0.0/datatables.min.js"></script>

<script>
$(document).ready(function(){
    var csrfName = "<?php echo $this->security->get_csrf_token_name(); ?>",
    csrfHash = "<?php echo $this->security->get_csrf_hash(); ?>";
    $("#myTable").DataTable({
        // Processing indicator
        "processing": true,
        // DataTables server-side processing mode
        "serverSide": true,
        // Load data from an Ajax source
        "ajax": {
            "url": '<?= admin_url('salesorders/get_so'); ?>',
            "type": "POST",
            "data": {
                [csrfName]:csrfHash,
                start_date:'<?php echo $start ?>',
                end_date:'<?php echo $end ?>',
                supplier:'<?php echo $ssupplier ?>',
                warehouse:'<?php echo $swarehouse ?>',
                customer:'<?php echo $scustomer ?>',
                otstatus:'<?php echo $otstatus ?>',
                atstatus:'<?php echo $atstatus ?>',
                status:'<?php echo $status ?>',
                sot:'<?php echo $sot ?>'
            }
        },
        "aaSorting": [[1, "desc"]],
        //Set column definition initialisation properties
        "columnDefs": [
            { 
                "targets": 0,
                "orderable": false
            },
            { 
                "targets": 14,
                "orderable": false
            }
        ],
        "dom": 'Blfrtip',
        "buttons": [
            {
                "extend": 'copy',
                "exportOptions": {
                    "columns": [0,1,2,3,4,5,6,7,8,9,10,11,12]
                }
            },
            {
                "extend": 'csv',
                "exportOptions": {
                    "columns": [0,1,2,3,4,5,6,7,8,9,10,11,12]
                }
            },
            {
                "extend": 'excel',
                "exportOptions": {
                    "columns": [0,1,2,3,4,5,6,7,8,9,10,11,12]
                }
            },
            {
                "extend": 'pdf',
                "exportOptions": {
                    "columns": [0,1,2,3,4,5,6,7,8,9,10,11,12]
                }
            },
            {
                "extend": 'print',
                "exportOptions": {
                    "columns": [0,1,2,3,4,5,6,7,8,9,10,11,12]
                }
            },
        ],
        "lengthMenu": [
            [ 10, 25, 50, 100, -1 ],
            [ '10', '25', '50', '100', 'Show all' ]
        ],
        "createdRow": function( row, data, dataIndex ) {
            $(row).attr('data-so_no',$(row).find('td:eq(2)').html());
            $(row).attr('data-po_no',$(row).find('td:eq(3)').html());
            $(row).attr('data-supplier',$(row).find('td:eq(4)').html());
            $(row).attr('data-customer',$(row).find('td:eq(5)').html());
            $(row).attr('data-warehouse',$(row).find('td:eq(6)').html());
            $(row).attr('data-qty',$(row).find('td:eq(7)').html());
            $(row).attr('data-cqty',$(row).find('td:eq(8)').html());
            $(row).attr('data-cpre',$(row).find('td:eq(9)').html());
        }
    });
    $("#myTable tbody").on("dblclick", "tr", function() {
        $('#ajaxCall').show();
        var html = "";
        var so_no = $(this).data('so_no');
        var po_no = $(this).data('po_no');
        var supplier = $(this).data('supplier');
        var customer = $(this).data('customer');
        var warehouse = $(this).data('warehouse');
        var demand_qty  = $(this).data('qty');
        var dispatch_qty  = $(this).data('cqty');
        var pending_qty  = demand_qty-dispatch_qty;
        var dispatch_per  = $(this).data('cpre');
        var expacted_per = '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i>';
        $('#summaryModel').modal('show');
        $('#summaryTable').html("<tr><td style='text-align: center;' ><i class='fa fa-spinner fa-spin fa-3x fa-fw'></i></td></tr>");

        $.ajax({
            url: '<?= admin_url('salesorders/so_summary'); ?>',
            type: "POST",
            data: {[csrfName]:csrfHash,so_no:so_no},
            success: function(data){
                data = jQuery.parseJSON(data);
                $('#ajaxCall').hide();
                if(data.codestatus == "ok"){
                    console.log(data);
                    html += "<tr>";
                        html += "<th>Sale Order No</th>";
                        html += "<td>"+data.so_no+"</td>";
                    html += "</tr>";
                    html += "<tr>";
                        html += "<th>PO Number</th>";
                        html += "<td>"+data.po_no+"</td>";
                    html += "</tr>";
                    html += "<tr>";
                        html += "<th>Supplier</th>";
                        html += "<td>"+supplier+"</td>";
                    html += "</tr>";
                    html += "<tr>";
                        html += "<th>Customer</th>";
                        html += "<td>"+customer+"</td>";
                    html += "</tr>";
                    html += "<tr>";
                        html += "<th>Warehouse</th>";
                        html += "<td>"+warehouse+"</td>";
                    html += "</tr>";
                    html += "<tr>";
                        html += "<th>Demand Quantity</th>";
                        html += "<td>"+data.demand_qty+"</td>";
                    html += "</tr>";
                    html += "<tr>";
                        html += "<th>Dispatch Quantity</th>";
                        html += "<td>"+data.dispatch_qty+"</td>";
                    html += "</tr>";
                    html += "<tr>";
                        html += "<th>Pending Quantity</th>";
                        html += "<td>"+data.pending_qty+"</td>";
                    html += "</tr>";
                    html += "<tr>";
                        html += "<th>Dispatch Percentage</th>";
                        html += "<td>"+data.dispatch_pre+"%</td>";
                    html += "</tr>";
                    html += "<tr>";
                        html += "<th>Expacted Complete Quantity</th>";
                        html += "<td id='expPerTxt' >"+data.expacted_qty+"</td>";
                    html += "</tr>";
                    html += "<tr>";
                        html += "<th>Expacted Complete Percentage</th>";
                        html += "<td id='expPerTxt' >"+data.expacted_pre+"%</td>";
                    html += "</tr>";
                    html += "<tr>";
                        html += "<th>Total Deliveries (DC)</th>";
                        html += "<td id='expPerTxt' >"+data.no_of_dc+"</td>";
                    html += "</tr>";
                    html += "<tr>";
                        html += "<th>Pending Deliveries (DC)</th>";
                        html += "<td id='expPerTxt' >"+data.no_of_pdc+"</td>";
                    html += "</tr>";
                    $('#summaryTable').html(html);
                }
                else{
                    alert(data.codestatus);
                    $('#summaryModel').modal('hide');
                }
            },
            error: function(){
                alert('Try Again!');
                $('#ajaxCall').hide();
                $('#summaryModel').modal('hide');
            }
        });

        

    });

    <?php if ($Owner || $Admin || $GP['so_cancel']) { ?>
        $('#myTable').on('click','#mergedBtn', function(){
            var mergeIDs = $('.soIDCheck').val();
            console.log(mergeIDs);
        });

    <?php } ?>
});

</script>
