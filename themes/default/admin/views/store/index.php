<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i
                class="fa-fw fa fa-star"></i>Stores
        </h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?=lang("actions")?>"></i></a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <?php 
                            if($Owner || $GP['store_add']){
                        ?>
                            <li>
                                <a href="<?=admin_url('stores/add')?>">
                                    <i class="fa fa-plus-circle"></i> Add Store
                                </a>
                            </li>
                        <?php
                            }
                        ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?=lang('list_results');?></p>
                <div class="table-responsive">
                    <table id="POData" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr class="active">
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Warehouse</th>
                                <th>Update Qty In</th>
                                <th>Update Price</th>
                                <th>Create Date</th>
                                <th>Create By</th>
                                <th>Status</th>
                                <th style="width:100px;"><?= lang("actions"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(count($stores)>0){
                                foreach($stores as $store){
                            ?>
                            <tr>
                                <td><?php echo $store->id ?></td>
                                <td><?php echo $store->name ?></td>
                                <td><?php echo $store->types ?></td>
                                <td><?php echo $store->warehouse_name.'('.$store->warehouse_code.')' ?></td>
                                <td><?php echo ucfirst($store->update_qty_in) ?></td>
                                <td><?php echo ucfirst($store->update_price) ?></td>
                                <td><?php echo $store->created_at ?></td>
                                <td><?php echo $store->created_by ?></td>
                                <td><?php echo ucfirst($store->status) ?></td>
                                <td>
                                    <div class="text-center">
                                        <div class="btn-group text-left">
                                            <button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">
                                                <?php echo lang('actions'); ?>
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu pull-right" role="menu">
                                            <?php 
                                                if($Owner || $GP['store_edit']){
                                            ?>
                                                <li><?php echo anchor('admin/stores/edit?id='.$store->id, '<i class="fa fa-edit"></i> Edit'); ?></li>
                                            <?php
                                                }
                                            ?>
                                            <?php 
                                                if($Owner || $GP['store_product_integration']){
                                            ?>
                                                <li><?php echo anchor('admin/stores/products?id='.$store->id, '<i class="fa fa-list"></i> Products Integrate'); ?></li>
                                            <?php
                                                }
                                            ?>
                                            <?php 
                                                if($Owner || $GP['store_product_integration_report']){
                                            ?>
                                                <li>
                                                    <a href="<?=admin_url('stores/report3?sid='.$store->id)?>"><i class="fa fa-file-excel-o"></i> Check Rhocom360 Products</a>
                                                </li>
                                                <li>
                                                    <a href="<?=admin_url('stores/report4?sid='.$store->id)?>"><i class="fa fa-file-excel-o"></i> Check Rhocom360 Products By Size</a>
                                                </li>
                                                <?php
                                                    if($store->types == "Daraz"){
                                                    ?>
                                                    <li>
                                                        <a href="<?=admin_url('stores/report5?sid='.$store->id)?>"><i class="fa fa-file-excel-o"></i> Check Daraz Occupy Stock Products</a>
                                                    </li>
                                                    <?php
                                                    }
                                                ?>

                                                <li>
                                                    <a href="<?=admin_url('stores/report6?sid='.$store->id)?>"><i class="fa fa-file-excel-o"></i> Check Store Products</a>
                                                </li>
                                                <?php
                                                    }
                                                ?>
                                                <?php 
                                                    if($Owner){
                                                ?>
                                                    <li><?php echo anchor('admin/stores/createstorewebhook?id='.$store->id, '<i class="fa fa-plus"></i> Create Webhook in Store'); ?></li>
                                                <?php
                                                    }
                                                ?>
                                                <li>
                                                    <a href="<?=admin_url('logs?store_id='.$store->id)?>"><i class="fa fa-file-excel-o"></i> Show Logs</a>
                                                </li>
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
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Warehouse</th>
                                <th>Update Qty In</th>
                                <th>Update Price</th>
                                <th>Create Date</th>
                                <th>Create By</th>
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



<script>
$(document).ready(function(){
    $('#POData').DataTable({
        "aaSorting": [[ 1, "asc" ]],
    });
});

</script>
