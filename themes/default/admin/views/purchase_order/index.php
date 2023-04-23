<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i
                class="fa-fw fa fa-star"></i>Purchase Orders
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
                                <a href="<?=admin_url('purchaseorder/add')?>">
                                    <i class="fa fa-plus-circle"></i> Add Purchase Order
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
        <form action="<?= admin_url('purchaseorder') ?>" method="get">
            <div class="col-lg-3">
                <div class="form-group">
                    <select name="wid" class="form-control">
                        <option value="">All Warehouse</option>
                        <?php
                            foreach($warehouses as $warehouse){
                                echo '<option value="'.$warehouse->id.'" '; if($warehouse->id == $wid){ echo 'selected'; } echo ' >'.$warehouse->name.'</option>';
                            }
                        ?>
                    </select>

                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <select name="sid" class="form-control">
                        <option value="">All Supplier</option>
                        <?php
                            foreach($suppliers as $supplier){
                                echo '<option value="'.$supplier->id.'" '; if($supplier->id == $sid){ echo 'selected'; } echo ' >'.$supplier->name.'</option>';
                            }
                        ?>
                    </select>

                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select name="status" class="form-control">
                        <option value="">All</option>
                        <option value="pending" <?php if($status == "pending" ){ echo 'selected'; }?> >Pending</option>
                        <option value="partial" <?php if($status == "partial" ){ echo 'selected'; }?> >Partial</option>
                        <option value="received" <?php if($status == "received" ){ echo 'selected'; }?> >Received</option>
                        <option value="closed" <?php if($status == "closed" ){ echo 'selected'; }?> >Closed</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
        <div class="row">
            <div class="col-lg-12">

                
                <div class="table-responsive">
                    <table id="POData" class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr class="active">
                            <th>Date</th>
                            <th>Ref No</th>
                            <th>Supplier</th>
                            <th>Warehouse</th>
                            <th>Create By</th>
                            <th>Complete Percentage</th>
                            <th>Status</th>
                            <th style="width:100px;"><?= lang("actions"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(count($po_rows)>0){

                                foreach($po_rows as $row){
                            ?>
                            <tr>
                                <td><?php echo $row->created_at ?></td>
                                <td><?php echo $row->reference_no ?></td>
                                <td><?php echo $row->supplier_name ?></td>
                                <td><?php echo $row->warehouse_code ?></td>
                                <td><?php echo $row->first_name.' '.$row->last_name; ?></td>
                                <td><?php echo $row->persentage; ?>%</td>
                                <td><?php echo $row->status ?></td>
                                <td>
                                    <div class="text-center">
                                        <div class="btn-group text-left">
                                            <button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">
                                                <?php echo lang('actions'); ?>
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu pull-right" role="menu">
                                                <li><?php echo anchor('admin/purchaseorder/view/'.$row->id, '<i class="fa fa-file-text-o"></i> Detail'); ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th>Ref No</th>
                            <th>Supplier</th>
                            <th>Warehouse</th>
                            <th>Receiving Date</th>
                            <th>Received Date</th>
                            <th>Status</th>
                            <th style="width:100px; text-align: center;"><?= lang("actions"); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-flash-1.6.4/b-html5-1.6.4/b-print-1.6.4/sb-1.0.0/datatables.min.js"></script>



<script>
$(document).ready(function(){
    $('#POData').DataTable({
        "aaSorting": [[ 0, "desc" ]],
        "dom": 'Blfrtip',
        "buttons": [
            {
                "extend": 'copy',
                "exportOptions": {
                    "columns": [0,1,2,3,4,5,6]
                }
            },
            {
                "extend": 'csv',
                "exportOptions": {
                    "columns": [0,1,2,3,4,5,6]
                }
            },
            {
                "extend": 'excel',
                "exportOptions": {
                    "columns": [0,1,2,3,4,5,6]
                }
            },
            {
                "extend": 'pdf',
                "exportOptions": {
                    "columns": [0,1,2,3,4,5,6]
                }
            },
            {
                "extend": 'print',
                "exportOptions": {
                    "columns": [0,1,2,3,4,5,6]
                }
            },
        ],
    });
});

</script>
