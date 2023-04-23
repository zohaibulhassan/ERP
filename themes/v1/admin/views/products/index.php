<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-toggle" style="opacity: 1; transform: scale(1);"></i>
                </div>
                <h3 class="md-card-toolbar-heading-text">Filters </h3>
            </div>
            <div class="md-card-content" >
                <form action="<?php echo base_url('admin/products'); ?>" method="get">
                    <div class="uk-grid">
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Warehouse</label>
                                <select name="warehouse" class="uk-width-1-1" id="warehouse_select" >
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Supplier</label>
                                <select name="supplier" class="uk-width-1-1" id="supplier_select">
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Brand</label>
                                <select name="brand" class="uk-width-1-1" id="brand_select">
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Category/Sub Category</label>
                                <select name="category" class="uk-width-1-1" id="category_select">
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Product Group</label>
                                <select name="group" class="uk-width-1-1" id="group_select">
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Tax Type</label>
                                <select name="taxtype" class="uk-width-1-1 select2"  >
                                    <option value="">ALL</option>
                                    <option value="1">GST</option>
                                    <option value="2">3rd Schedule</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Formulas</label>
                                <select name="formula" class="uk-width-1-1 select2"  >
                                    <option value="">ALL</option>
                                    <?php 
                                        foreach($formulas as $f){
                                            echo '<option value="'.$f->id.'">'.$f->text.'</option>';
                                        }
                                    
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Status</label>
                                <select name="status" class="uk-width-1-1 select2"  >
                                    <option value="">ALL</option>
                                    <option value="1">Active</option>
                                    <option value="0">Deactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-4" style="padding-top: 20px;">
                            <button type="submit" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light" >Submit</button>
                            <a href="<?php echo base_url('admin/products'); ?>" class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light" >Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Products</h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon fa-solid fa-expand md-card-fullscreen-activate toolbar_fixed"></i>
                </div>
            </div>
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-large-1-1">
                        <div class="dt_colVis_buttons"></div>
                        <table id="productsTable" class="uk-table" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>ID</th>
                                    <th>Barcode</th>
                                    <th>Name</th>
                                    <th>Available Stock</th>
                                    <th>Unit</th>
                                    <th>Cost</th>
                                    <th>Tax Value</th>
                                    <th>Cost With Tax</th>
                                    <th>MRP</th>
                                    <th>Alert Quantity</th>
                                    <th>Brand</th>
                                    <th>Category</th>
                                    <th>Sub Category</th>
                                    <th>Formula</th>
                                    <th>Prescription</th>
                                    <th>Disease</th>
                                    <th>Tax Type</th>
                                    <th>Tax Rate</th>
                                    <th>FED Rate</th>
                                    <th>Company Code</th>
                                    <th>Suppliers</th>
                                    <th>Manufacturer</th>
                                    <th>Carton Size</th>
                                    <th>Pack Size</th>
                                    <th>Short Expiry Durraction</th>
                                    <th>Expected Sold-Out Durration</th>
                                    <th>Total Soldout</th>
                                    <th>Group ID</th>
                                    <th>Group Name</th>
                                    <th>Status</th>
                                    <th class="dt-no-export" >Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- datatables -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<!-- datatables buttons-->
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/dataTables.buttons.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/custom/datatables/buttons.uikit.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/jszip/dist/jszip.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/pdfmake/build/pdfmake.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/pdfmake/build/vfs_fonts.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.colVis.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.html5.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.print.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.colVis.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-fixedcolumns/dataTables.fixedColumns.min.js"></script>

<!-- datatables custom integration -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/custom/datatables/datatables.uikit.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/datatable.js"></script>
<script>
    var csrfName = "<?php echo $this->security->get_csrf_token_name(); ?>",
        csrfHash = "<?php echo $this->security->get_csrf_hash(); ?>";
    var data = {
        [csrfName]:csrfHash,
        warehouse: "<?php echo $warehouse_id ?>",
        supplier: "<?php echo $supplier ?>",
        brand: "<?php echo $brand ?>",
        category: "<?php echo $category ?>",
        group: "<?php echo $group ?>",
        taxtype: "<?php echo $taxtype ?>",
        formula: "<?php echo $formula ?>",
        status: "<?php echo $status ?>"
    };
    $.DataTableInit({
        selector:'#productsTable',
        url:"<?= admin_url('products/get_list'); ?>",
        data:data,
        aaSorting: [[1, "desc"]],
        columnDefs: [
            { 
                "targets": 0,
                "orderable": false
            },
            { 
                "targets": 30,
                "orderable": false
            }
        ],
        fixedColumns:   {left: 0,right: 2},
        scrollX: true
    });
</script>

<script>
    $(document).ready(function(){
        $('.select2').select2();
        $('#warehouse_select').select2({
            ajax: {
                url: '<?php echo base_url("admin/general/warehouses"); ?>',
                dataType: 'json',
            },
            formatResult: function (data, term) {
                console.log(data);
                console.log(term);
                return data;
            },
        });
        $('#supplier_select').select2({
            ajax: {
                url: '<?php echo base_url("admin/general/suppliers"); ?>',
                dataType: 'json',
            },
            formatResult: function (data, term) {
                console.log(data);
                console.log(term);
                return data;
            },
        });
        $('#brand_select').select2({
            ajax: {
                url: '<?php echo base_url("admin/general/brands"); ?>',
                dataType: 'json',
            },
            formatResult: function (data, term) {
                console.log(data);
                console.log(term);
                return data;
            },
        });
        $('#category_select').select2({
            ajax: {
                url: '<?php echo base_url("admin/general/categories"); ?>',
                dataType: 'json',
            },
            formatResult: function (data, term) {
                console.log(data);
                console.log(term);
                return data;
            },
        });
        $('#group_select').select2({
            ajax: {
                url: '<?php echo base_url("admin/general/groups"); ?>',
                dataType: 'json',
            },
            formatResult: function (data, term) {
                console.log(data);
                console.log(term);
                return data;
            },
        });
        $(document).on('click','.deleteproduct',function(){
            var id = $(this).data('id');
            Swal.fire({
                title: "Do you want to delete this product",
                input: "text",
                showCancelButton: true,
                confirmButtonColor: "#e53935",
                confirmButtonText: "Delete",
                cancelButtonText: "Cancel",
                buttonsStyling: true
            }).then(function (res) {
                if(res.isConfirmed){
                    Swal.fire({
                        title: 'Deleting Product!',
                        showCancelButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            $.ajax({
                                url: '<?php echo base_url("admin/products/delete"); ?>',
                                type: 'POST',
                                data: {[csrfName]:csrfHash,id:id,reason:res.value},
                                success: function(data) {
                                    var obj = jQuery.parseJSON(data);
                                    swal.close()
                                    if(obj.status){
                                        toastr.success(obj.message);
                                        $('#productsTable').DataTable().ajax.reload()
                                    }
                                    else{
                                        toastr.error(obj.message);
                                    }
                                    
                                }
                            });
                        }
                    });
                }
            })
        });
        $(document).on('click','.statuschangeproduct',function(){
            var id = $(this).data('id');
            var status = $(this).data('status');
            var message = "Do you want to deactive this product";
            var buttonname = "Deactive";
            if(status == 0){
                message = "Do you want to active this product";
                buttonname = "Active";
            }
            Swal.fire({
                title: message,
                input: "text",
                showCancelButton: true,
                confirmButtonColor: "#e53935",
                confirmButtonText: buttonname,
                cancelButtonText: "Cancel",
                buttonsStyling: true
            }).then(function (res) {
                if(res.isConfirmed){
                    Swal.fire({
                        title: 'Status changing!',
                        showCancelButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            $.ajax({
                                url: '<?php echo base_url("admin/products/statuschangeproduct"); ?>',
                                type: 'POST',
                                data: {[csrfName]:csrfHash,id:id,status:status,reason:res.value},
                                success: function(data) {
                                    var obj = jQuery.parseJSON(data);
                                    swal.close()
                                    if(obj.status){
                                        toastr.success(obj.message);
                                        $('#productsTable').DataTable().ajax.reload()
                                    }
                                    else{
                                        toastr.error(obj.message);
                                    }
                                    
                                }
                            });
                        }
                    });
                }
            })
        });
    });
</script>
