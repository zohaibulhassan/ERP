<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD ?>
<link href="https://cdn.jsdelivr.net/sweetalert2/4.2.4/sweetalert2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/sweetalert2/4.2.4/sweetalert2.min.js"></script>
<style>
    .tasks-menus li a {
        cursor: pointer;
    }
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i
                class="fa-fw fa fa-star"></i>Products Integrate (<?php echo $store->name; ?>)
        </h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?=lang("actions")?>"></i></a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                    <?php 
                        if($Owner || $GP['store_product_integration_add']){
                    ?>
                        <li>
                            <a  data-toggle="modal" data-target="#addProcuct">
                                <i class="fa fa-plus"></i> Add Product
                            </a>
                        </li>
                        <li>
                            <a  data-toggle="modal" data-target="#addbulkModel">
                                <i class="fa fa-file-o"></i> Add Product in Bulk
                            </a>
                        </li>
                    <?php
                        }
                    ?>
                    <?php 
                        if($Owner || $GP['store_product_integration_edit']){
                    ?>
                        <li>
                            <a  data-toggle="modal" data-target="#updatebulkModel">
                                <i class="fa fa-file-o"></i> Update Product in Bulk
                            </a>
                        </li>
                    <?php
                        }
                    ?>
                    <?php 
                        if($Owner || $GP['store_product_integration_report']){
                    ?>
                        <!-- <li>
                            <a href="<?=admin_url('stores/report1?sid='.$store->id)?>"><i class="fa fa-file-excel-o"></i> Report 1</a>
                        </li>
                        <li>
                            <a href="<?=admin_url('stores/report2?sid='.$store->id)?>"><i class="fa fa-file-excel-o"></i> Report 2</a>
                        </li> -->
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
                    </ul>
                </li>
                <?php 
                    if($Owner || $GP['store_product_integration_recycle']){
                ?>
                    <li>
                        <a id="UpdateStore" ><i class="icon fa fa-recycle" data-placement="left" style="cursor: pointer;" title="Update Store"></i></a>
                    </li>
                <?php
                    }
                ?>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">


                <div class="table-responsive">
                    <table id="integrateproducttb" class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr class="active">
                            <th>Product ID</th>
                            <th>Name</th>
                            <th>Store Product ID</th>
                            <th>Type</th>
                            <th>Warehouse ID</th>
                            <th>Quantity Update In</th>
                            <th>Price Update In</th>
                            <th>Apply Discount</th>
                            <th>Status</th>
                            <th>Edit</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(count($products)>0){

                                foreach($products as $product){
                            ?>
                            <tr>
                                <td><?php echo $product->pid ?></td>
                                <td><?php echo $product->pname ?></td>
                                <td><?php echo $product->spid ?></td>
                                <td><?php echo ucfirst($product->update_in) ?></td>
                                <td><?php echo ucfirst($product->warehouse_name) ?></td>
                                <td><?php echo ucfirst($product->update_qty_in) ?></td>
                                <td><?php echo ucfirst($product->price_type) ?></td>
                                <td><?php echo $product->discountname ?></td>
                                <td><?php echo ucfirst($product->status) ?></td>
                                <td>
                                    <?php 
                                        if($Owner || $GP['store_product_integration_edit']){
                                    ?>
                                        <a data-toggle="modal" class="updatebtn" data-target="#updatekModel" data-id="<?php echo $product->id ?>" data-pid="<?php echo $product->pid ?>" data-spid="<?php echo $product->spid ?>" data-status="<?php echo $product->status ?>" ><i class="fa fa-edit"></i></a>
                                    <?php
                                        }
                                    ?>
                                    <?php 
                                        if($Owner || $GP['store_product_integration_delete']){
                                    ?>
                                        <a data-toggle="modal" class="deletebtn" data-spid="<?php echo $product->spid ?>" data-id="<?php echo $product->id ?>" data-pid="<?php echo $product->pid ?>"><i class="fa fa-trash"></i></a>
                                    <?php
                                        }
                                    ?>
                                    <a href="<?=admin_url('logs?store_id='.$store->id.'&product_id='.$product->pid)?>" class="showlogs" ><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                        <tfoot class="dtFilter">
                            <tr class="active">
                                <th>Product ID</th>
                                <th>Name</th>
                                <th>Store Product ID</th>
                                <th>Type</th>
                                <th>Warehouse ID</th>
                                <th>Quantity Update In</th>
                                <th>Price Update In</th>
                                <th>Apply Discount</th>
                                <th>Status</th>
                                <th>Edit</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade in" id="updatebulkModel" tabindex="-1" role="dialog" aria-labelledby="updatebulkModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i></button>
                <h4 class="modal-title" id="updatebulkModelLabel">Update Products in bulk</h4>
            </div>
            <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'updatebulk-form');
            echo admin_form_open_multipart("stores/updatebulk_submit", $attrib); ?>

            <div class="modal-body">
                <p><?= lang('enter_info'); ?></p>
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" name="storeid" value="<?php echo $store->id; ?>" >
                        <div class="form-group" style="margin: 7.5px 0;">
                            <input id="itesfile" type="file" 
                                data-browse-label="Browse" 
                                data-upload-label="Update Now"
                                name="products" 
                                data-show-upload="false" 
                                data-show-preview="false"
                                class="form-control file" >
                            <span><a  href="<?=admin_url('stores/download_update_product_csv?sid='.$store->id) ?>" >Download Update Data</a></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
            <div class="modal-footer">
                <input type="button" id="updatebulksumit-btn" class="btn btn-primary" value="Submit">
            </div>
        </div>
    </div>
</div>
<div class="modal fade in" id="addbulkModel" tabindex="-1" role="dialog" aria-labelledby="addbulkModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i></button>
                <h4 class="modal-title" id="addbulkModelLabel">Add Products in bulk</h4>
            </div>
            <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'addbulk-form');
            echo admin_form_open_multipart("stores/addbulk_submit", $attrib); ?>

            <div class="modal-body">
                <p><?= lang('enter_info'); ?></p>
                <div class="row">
                    <div class="col-md-12">
                    <!--<form action="#" method="post" id="bulk-form">
                        
                        </form> -->
                        <input type="hidden" name="storeid" value="<?php echo $store->id; ?>" >
                        <div class="form-group" style="margin: 7.5px 0;">
                            <input id="itesfile" type="file" 
                                data-browse-label="Browse" 
                                data-upload-label="Update Now"
                                name="products" 
                                data-show-upload="false" 
                                data-show-preview="false"
                                class="form-control file" >
                            <span><a  href="<?=admin_url('stores/download_add_product_csv')?>" >Download Sample</a></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
            <div class="modal-footer">
                <input type="button" id="addbulksumit-btn" class="btn btn-primary" value="Submit">
            </div>
        </div>
    </div>
</div>
<div class="modal fade in" id="addProcuct" tabindex="-1" role="dialog" aria-labelledby="addProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i></button>
                <h4 class="modal-title" id="addProductLabel">Add Product</h4>
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
                                <input type="hidden" name="store_id" value="<?= $store->id?>">
                                <input type="hidden" name="stock_margin" value="<?= $store->stock_margin?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Product Store Title</label>
                            <div class="input-group" style="width: 100%;">
                                <input type="text" name="title" id="producttitle" class="form-control" style="width:100%;" required >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Product ID</label>
                            <div class="input-group" style="width: 100%;">
                                <input type="text" name="product_id_show" id="product_id" class="form-control" style="width:100%;" value="" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Default Auto SO Create Supplier</label>
                            <div class="input-group" style="width: 100%;">
                                <select name="supplier" id="addsupplier" class="form-control">
                                    <option value="">Select Supplier</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Product Store ID</label>
                            <div class="input-group" style="width: 100%;">
                                <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                    <input type="checkbox" name="createproduct" id="createproduct" value="1" >
                                </div>
                                <input type="text" name="storeid" id="storeid" class="form-control" style="width:100%;" required >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Update Type</label>
                            <div class="input-group" style="width: 100%;">
                                <select name="updatetype" id="updatetype" class="form-control">
                                    <option value="qty" <?php if($store->integration_type == "qty"){ echo 'selected'; } ?> >Ony Quantity</option>
                                    <option value="price" <?php if($store->integration_type == "price"){ echo 'selected'; } ?> >Ony Price</option>
                                    <option value="detail" <?php if($store->integration_type == "detail"){ echo 'selected'; } ?> >Ony Detail</option>
                                    <option value="priceqty" <?php if($store->integration_type == "priceqty"){ echo 'selected'; } ?> >Price and Quantity</option>
                                    <option value="detailnqty" <?php if($store->integration_type == "detailnqty"){ echo 'selected'; } ?> >Product Detail and Quantity</option>
                                    <option value="detailnprice" <?php if($store->integration_type == "detailnprice"){ echo 'selected'; } ?> >Product Detail and Price</option>
                                    <option value="full" <?php if($store->integration_type == "full"){ echo 'selected'; } ?> >Full Integration</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Update Stock In</label>
                            <div class="input-group" style="width: 100%;">
                                <select name="stocktype" id="stocktype" class="form-control">
                                    <option value="single" <?php if($store->update_qty_in == "single"){ echo 'selected'; } ?> >Single</option>
                                    <option value="pack" <?php if($store->update_qty_in == "pack"){ echo 'selected'; } ?> >Pack</option>
                                    <option value="carton" <?php if($store->update_qty_in == "carton"){ echo 'selected'; } ?> >Carton</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Update Price In</label>
                            <div class="input-group" style="width: 100%;">
                                <select name="pricetype" id="pricetype" class="form-control">
                                    <option value="mrp" <?php if($store->update_price == "mrp"){ echo 'selected'; } ?> >MRP</option>
                                    <option value="consiment" <?php if($store->update_price == "consiment"){ echo 'selected'; } ?> >Consiment</option>
                                    <option value="dropship" <?php if($store->update_price == "dropship"){ echo 'selected'; } ?> >Dropship</option>
                                    <option value="crossdock" <?php if($store->update_price == "crossdock"){ echo 'selected'; } ?> >Cross Dock</option>
                                    <option value="cost" <?php if($store->update_price == "cost"){ echo 'selected'; } ?> >Cost</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Warehouse</label>
                            <div class="input-group" style="width: 100%;">
                                <select name="warehouseid" id="swarehouseid" class="form-control">
                                    <?php
                                        foreach($warehouses as $warehouse){
                                        ?>
                                        <option value="<?php echo $warehouse->id; ?>" <?php if($store->warehouse_id == $warehouse->id){ echo 'selected'; } ?> ><?php echo $warehouse->name; ?></option>
                                        <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Discount Apply</label>
                            <div class="input-group" style="width: 100%;">
                                <select name="discount" id="discount" class="form-control">
                                    <option value="no">No Discount</option>
                                    <option value="mrp" <?php if($store->discount == "mrp"){ echo 'selected'; } ?> >MRP Discount</option>
                                    <option value="d1" <?php if($store->discount == "d1"){ echo 'selected'; } ?> >Discount 1</option>
                                    <option value="d2" <?php if($store->discount == "d2"){ echo 'selected'; } ?> >Discount 2</option>
                                    <option value="d3" <?php if($store->discount == "d3"){ echo 'selected'; } ?> >Discount 3</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <h4 class="modal-title" style="text-align:center;color:red" >Price Calculate</h4>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Price (W/O Tax)</label>
                            <input type="text" id="priceTxt" class="form-control" readonly >
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tax</label>
                            <input type="text" id="taxtTxt" class="form-control" readonly >
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Discount</label>
                            <input type="text" id="discountTxt" class="form-control" readonly >
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Total</label>
                            <input type="text" id="total" class="form-control" readonly >
                        </div>
                    </div>
                    <div class="col-md-12">
                        <h4 class="modal-title" style="text-align:center;color:red" >Store Update Price and Quantity</h4>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Reqular Price</label>
                            <input type="text" id="rprice" class="form-control" readonly >
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Sale Price</label>
                            <input type="text" id="sprice" class="form-control" readonly >
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Stock Qty</label>
                            <input type="text" id="stock" class="form-control" readonly >
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
            <div class="modal-footer">
                <input type="button" id="addproduct-btn" class="btn btn-primary" value="Submit">
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<div class="modal fade in" id="updatekModel" tabindex="-1" role="dialog" aria-labelledby="updateModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i></button>
                <h4 class="modal-title" id="updateModelLabel">Update</h4>
            </div>
            <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'update-form');
            echo admin_form_open_multipart("#", $attrib); ?>

            <div class="modal-body">
                <p><?= lang('enter_info'); ?></p>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group" style="margin: 7.5px 0;">
                            <label>Store ID</label>
                            <input type="text" class="form-control" name="sid" id="update_sid" value="<?php echo $store->id; ?>" readonly>
                            <input type="hidden" name="updateid" id="updateid">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" style="margin: 7.5px 0;">
                            <label>Product ID</label>
                            <input class="form-control" type="text" name="pid" id="update_pid" value="" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" style="margin: 7.5px 0;">
                            <label>Store Product ID</label>
                            <input class="form-control" type="text" name="spid" id="update_spid" value="" readonly>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>System Product Name</label>
                            <div class="input-group" style="width: 100%;">
                                <input type="text" name="systemname" id="updatesystemName" class="form-control" style="width:100%;" readonly required >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Product Store Title</label>
                            <div class="input-group" style="width: 100%;">
                                <input type="text" name="title" id="updateproducttitle" class="form-control" style="width:100%;" required >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Default Auto SO Create Supplier</label>
                            <div class="input-group" style="width: 100%;">
                                <select name="supplier" id="editsupplier" class="form-control">
                                    <option value="">Select Supplier</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Update Type</label>
                            <div class="input-group" style="width: 100%;">
                                <select name="editupdatetype" id="editupdatetype" class="form-control">
                                    <option value="qty">Ony Quantity</option>
                                    <option value="price">Ony Price</option>
                                    <option value="detail">Ony Detail</option>
                                    <option value="priceqty">Price and Quantity</option>
                                    <option value="detailnqty">Product Detail and Quantity</option>
                                    <option value="detailnprice">Product Detail and Price</option>
                                    <option value="full">Full Integration</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Update Stock In</label>
                            <div class="input-group" style="width: 100%;">
                                <select name="stocktype" id="updatestocktype" class="form-control">
                                    <option value="single">Single</option>
                                    <option value="pack">Pack</option>
                                    <option value="carton">Carton</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Update Price In</label>
                            <div class="input-group" style="width: 100%;">
                                <select name="pricetype" id="updatepricetype" class="form-control">
                                    <option value="mrp">MRP</option>
                                    <option value="consiment">Consiment</option>
                                    <option value="dropship">Dropship</option>
                                    <option value="crossdock">Cross Dock</option>
                                    <option value="cost">Cost</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Warehouse</label>
                            <div class="input-group" style="width: 100%;">
                                <select name="warehouseid" id="editswarehouseid" class="form-control">
                                    <?php
                                        foreach($warehouses as $warehouse){
                                        ?>
                                        <option value="<?php echo $warehouse->id; ?>" <?php if($store->warehouse_id == $warehouse->id){ echo 'selected'; } ?> ><?php echo $warehouse->name; ?></option>
                                        <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Discount Apply</label>
                            <div class="input-group" style="width: 100%;">
                                <select name="discount" id="updatediscount" class="form-control">
                                    <option value="no">No Discount</option>
                                    <option value="mrp">MRP Discount</option>
                                    <option value="d1">Discount 1</option>
                                    <option value="d2">Discount 2</option>
                                    <option value="d3">Discount 3</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" style="margin: 7.5px 0;">
                            <label>Status</label>
                            <select class="form-control" name="update_status" id="update_status" >
                                <option value="active">Active</option>
                                <option value="deactive">Dective</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <h4 class="modal-title" style="text-align:center;color:red" >Price Calculate</h4>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Price (W/O Tax)</label>
                            <input type="text" id="editpriceTxt" class="form-control" readonly >
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tax</label>
                            <input type="text" id="edittaxtTxt" class="form-control" readonly >
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Discount</label>
                            <input type="text" id="editdiscountTxt" class="form-control" readonly >
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Total</label>
                            <input type="text" id="edittotal" class="form-control" readonly >
                        </div>
                    </div>
                    <div class="col-md-12">
                        <h4 class="modal-title" style="text-align:center;color:red" >Store Update Price and Quantity</h4>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Reqular Price</label>
                            <input type="text" id="editrprice" class="form-control" readonly >
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Sale Price</label>
                            <input type="text" id="editsprice" class="form-control" readonly >
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Stock Qty</label>
                            <input type="text" id="editstock" class="form-control" readonly >
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" id="update-btn" class="btn btn-primary" value="Update">
            </div>
            <?php echo form_close(); ?>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<style>
    #UpdateStoreLoader {
        position: fixed;
        top: 0;
        left: 0;
        background: #00000094;
        z-index: 10;
        width: 100%;
        height: 100vh;        
    }
    #UpdateStoreLoader .content {
        width: 500px;
        background: white;
        margin: 0 auto;
        top: 200px;
        position: relative;
        padding: 15px 0px 20px;
        text-align: center;
        box-shadow: 0px 0px 44px 2px black;
    }
    #UpdateStoreLoader .content h1{
        font-size:24px;
        color:#428BCA;
    }
    #UpdateStoreLoader .content p{
        margin: 0;
        font-size: 16px;
        font-weight: bold;
        color: red;
    }
    #UpdateStoreLoader .content #prograss{
        width: 82%;
        margin-top: 8px;
        height: 50px;
    }
    #UpdateStoreLoader .content .progressbarstatsu{
        font-size: 15px;
        text-align: right;
        width: 80%;
        margin: 10px auto 0;
        font-weight: bold;
    }
</style>
<div id="UpdateStoreLoader" style="display:none" >
    <div class="content" >
        <h1>Product Finding......</h1>
        <p></p>
        <progress id="prograss" value="0" max="100"> </progress>
        <div class="progressbarstatsu"><span id="currentr" >0</span>/<span id="totalr">0</span></div>
    </div>

</div>

<div id="modal-loading" style="display: none;">
    <div class="blackbg"></div>
    <div class="loader"></div>
</div>
<div id="ajaxCall"><i class="fa fa-spinner fa-pulse"></i></div>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-flash-1.6.4/b-html5-1.6.4/b-print-1.6.4/sb-1.0.0/datatables.min.js"></script>

<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>

<script>
    $(document).ready(function(){
        $(document).on('ifChanged','#createproduct', function(event) {
            if($("#createproduct").prop("checked")){
                $('#storeid').attr('readonly','readonly');
            }
            else{
                $('#storeid').removeAttr('readonly');
            }
        });
        $('#product').select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function(element, callback) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: '<?= admin_url('stores/discountlist'); ?>' + $(element).val(),
                    success: function(data) {
                        $('#discount').html(data);
                    }
                });
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
            var pid = $(this).val();
            var selecteddata = $(this).select2('data');
            $.ajax({
                type: "get",
                data: {pid:pid,storediscount:'<?= $store->discount; ?>'},
                async: false,
                url: '<?= admin_url('stores/discountlist'); ?>',
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    $('#discount').html(obj.discount);
                    $('#addsupplier').html(obj.suppliers);
                    $('#producttitle').val(selecteddata.text);
                    calProductPrice()
                }
            });

        });
        $('#stocktype').change(function(){calProductPrice();});
        $('#pricetype').change(function(){calProductPrice();});
        $('#discount').change(function(){calProductPrice();});
        $('#swarehouseid').change(function(){calProductPrice();});
        function calProductPrice($edit = ""){
            if($edit == ""){
                var pid = $('#product').val();
                var stocktype = $('#stocktype').val();
                var pricetype = $('#pricetype').val();
                var discount = $('#discount').val();
                var swarehouse_id = $('#swarehouseid').val();
            }
            else{
                var pid = $('#update_pid').val();
                var stocktype = $('#updatestocktype').val();
                var pricetype = $('#updatepricetype').val();
                var discount = $('#updatediscount').val();
                var swarehouse_id = $('#editswarehouseid').val();
            }
            console.log(swarehouse_id);
            var stock_margin = <?= $store->stock_margin ?>;
            
            $.ajax({
                type: "get",
                data: {
                    pid:pid,
                    stocktype:stocktype,
                    pricetype:pricetype,
                    discount:discount,
                    warehouse_id:swarehouse_id,
                    stock_margin:stock_margin
                },
                url: '<?= admin_url('stores/calPrice'); ?>',
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    if($edit == ""){
                        $('#priceTxt').val(obj.price);
                        $('#taxtTxt').val(obj.tax);
                        $('#discountTxt').val(obj.discount);
                        $('#total').val(obj.total);
                        $('#rprice').val(obj.mrp);
                        $('#sprice').val(obj.total);
                        $('#stock').val(obj.stock);
                        $('#product_id').val(pid);
                    }
                    else{
                        $('#editpriceTxt').val(obj.price);
                        $('#edittaxtTxt').val(obj.tax);
                        $('#editdiscountTxt').val(obj.discount);
                        $('#edittotal').val(obj.total);
                        $('#editrprice').val(obj.mrp);
                        $('#editsprice').val(obj.total);
                        $('#editstock').val(obj.stock);
                    }
                }
            });
        }
        $('#integrateproducttb').DataTable({
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
        $('#updatebulksumit-btn').click(function(){
            $('#updatebulk-form').submit();
        });
        $('#updatebulk-form').submit(function(e){
            e.preventDefault();
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('stores/updatebulk_submit'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    if(obj.codestatus == "Product Integrate Update Successfully"){
                        // location.reload();
                        $('#ajaxCall').hide();
                    }
                    alert(obj.codestatus);
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
        $('#addbulksumit-btn').click(function(){
            $('#addbulk-form').submit();
        });
        $('#addbulk-form').submit(function(e){
            e.preventDefault();
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('stores/addbulk_submit'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    if(obj.codestatus == "Product Integrate Successfully"){
                        // location.reload();
                        $('#ajaxCall').hide();
                    }
                    alert(obj.codestatus);
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
        $('#update-btn').click(function(){
            $('#update-form').submit();
        });
        $('#update-form').submit(function(e){
            e.preventDefault();
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('stores/update_submit'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    var obj = jQuery.parseJSON(data);
                    if(obj.codestatus == "Store Setting and Store Qty Update Successfully"){
                        // location.reload();
                    }
                    alert(obj.codestatus);
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
        $('#addproduct-btn').click(function(){
            $('#add-form').submit();
        });
        $('#add-form').submit(function(e){
            e.preventDefault();
            $('#ajaxCall').show();
            $.ajax({
                url: '<?= admin_url('stores/addproduct'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data){
                    alert(data);
                    if(data == "Product Integrate Successfully"){
                        // location.reload();
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
        $('.updatebtn').click(function(){
            var id = $(this).data('id');
            var pid = $(this).data('pid');
            var spid = $(this).data('spid');
            var status = $(this).data('status');
            $.ajax({
                type: "get",
                data: {id:id},
                url: '<?= admin_url('stores/updateDetail'); ?>',
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    console.log(obj);
                    if(obj.codestatus == "ok"){
                        $('#updateid').val(obj.data.id);
                        $('#update_pid').val(obj.data.product_id);
                        $('#updatesystemName').val(obj.data.system_pname);
                        $('#updateproducttitle').val(obj.data.product_name);
                        $('#editupdatetype').val(obj.data.update_in);
                        $('#update_spid').val(obj.data.store_product_id);
                        $('#updatestocktype').val(obj.data.update_qty_in);
                        if(obj.data.warehouse_id == 0){
                            $('#editswarehouseid').val(<?= $store->warehouse_id ?>);
                        }
                        else{
                            $('#editswarehouseid').val(obj.data.warehouse_id);
                        }
                        $('#updatepricetype').val(obj.data.price_type);
                        $('#editsupplier').html(obj.suppliers);
                        $('#updatediscount').html(obj.discount);
                        $('#update_status').val(obj.data.status);
                        calProductPrice("getedit");
                    }
                    else{
                        alert(obj.codestatus);
                    }
                }
            });
        });
        $('#updatestocktype,#updatepricetype,#updatediscount,#editswarehouseid').change(function(){
            calProductPrice("getedit");
        });
        $( "body" ).on( "click", ".deletebtn", function() {
            var id = $(this).data('id');
            var spid = $(this).data('spid');
            var pid = $(this).data('pid');
            <?php 
                if($Owner || $GP['store_product_integration_delete_both']){
            ?>
                var buttons = $('<div>')
                .append("<p style='padding: 0 50px;line-height: 25px;margin-bottom: 15px;' >If you want to delete this product in store. click on <b>'Delete Both Side'</b> or if you want to delete only sofware to click on <b>'Only Delete Software'</b></p>")
                .append(createButton('Only Delete Software', function() {
                    window.location.href = "<?= admin_url('stores/productdelete') ?>?storeside=no&sid=<?= $store->id?>&id="+id+"&spid="+spid+"&pid="+pid;
                }))
                .append(createButton('Delete Both Side', function() {
                    window.location.href = "<?= admin_url('stores/productdelete') ?>?storeside=yes&sid=<?= $store->id?>&id="+id+"&spid="+spid+"&pid="+pid;
                }));
            <?php
                }
                else{
            ?>
                var buttons = $('<div>')
                .append("<p style='padding: 0 50px;line-height: 25px;margin-bottom: 15px;' >If you want to delete this product in store. click on <b>'Delete Both Side'</b> or if you want to delete only sofware to click on <b>'Only Delete Software'</b></p>")
                .append(createButton('Only Delete Software', function() {
                    window.location.href = "<?= admin_url('stores/productdelete') ?>?storeside=no&sid=<?= $store->id?>&id="+id+"&spid="+spid+"&pid="+pid;
                }));
            <?php
                }
            ?>
            swal({
                html: buttons,
                type: "warning",
                showConfirmButton: false,
                showCancelButton: false
            });

        });
        function createButton(text, cb) {
            if(text == "Only Delete Software"){
                return $('<button class="btn btn-warning" style="margin-right:10px" >' + text + '</button>').on('click', cb);
            }
            else{
                return $('<button class="btn btn-danger">' + text + '</button>').on('click', cb);
            }
        }
        var updateclick = 0;
        var productsList = [];
        $('#UpdateStore').click(function(){
            $('#currentr').html(0);
            $('#UpdateStoreLoader .content h1').html('Product Finding......');
            $("#UpdateStoreLoader .content #prograss").val(0);
            updateclick++;
            if(updateclick == 1){
                console.log('click Recycle');
                $('#UpdateStoreLoader').show();
                var sid = <?= $store->id ?>;
                $.ajax({
                    type: "get",
                    data: {sid:sid},
                    url: '<?= admin_url('stores/count_products'); ?>',
                    success: function(data) {
                        var obj = jQuery.parseJSON(data);
                        if(obj.count > 0){
                            $('#totalr').html(obj.count);
                            $("#UpdateStoreLoader .content #prograss").attr("max",obj.count);
                            $("#UpdateStoreLoader .content #prograss").val(0);
                            if(obj.count > 0){
                                productsList = obj.products;
                                updateProductInStore();
                            }
                            else{
                                $('#UpdateStoreLoader').hide();
                            }
                        }
                        else{
                            $('#UpdateStoreLoader').hide();
                        }
                    }
                });
            }
        });
        var psno = 0;
        function updateProductInStore(){
            $('#UpdateStoreLoader .content h1').html('Product Updating......');
            if(productsList.length > 0){
                if(psno != (productsList.length)){
                    $('#UpdateStoreLoader .content p').html(productsList[psno].pname);
                    var wid = productsList[psno].wid;
                    var sid = <?= $store->id ?>;
                    $.ajax({
                        type: "get",
                        data: {pid:productsList[psno].pid,wid:wid,sid:sid},
                        url: '<?= admin_url('stores/updateProductStore'); ?>',
                        success: function(data) {
                            psno++;
                            $('#currentr').html(psno);
                            $("#UpdateStoreLoader .content #prograss").val(psno);
                            updateProductInStore();
                        }
                    });
                }
                else{
                    $('#currentr').html(psno);
                    $("#UpdateStoreLoader .content #prograss").val(psno);
                    alert("Porducts Update Successfully");
                    updateclick = 0;
                    psno = 0;
                    $('#UpdateStoreLoader').hide();
                }
            }
            else{
                alert("Products not found");
                updateclick = 0;
                psno = 0;
                $('#UpdateStoreLoader').hide();
            }
        }
    });

</script>
