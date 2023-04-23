<style>
    .uk-open>.uk-dropdown, .uk-open>.uk-dropdown-blank{
    }
</style>
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
                <form action="<?php echo base_url('admin/reports/stock_report'); ?>" method="get">
                    <input type="hidden" name="show_type" value="2">
                    <div class="uk-grid">
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Form Date</label>
                                <input class="md-input  label-fixed" type="text" name="date_from" data-uk-datepicker="{format:'YYYY-MM-DD'}" autocomplete="off" value="<?php echo $date_from ?>" readonly >
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>To Date</label>
                                <input class="md-input  label-fixed" type="text" name="date_to" data-uk-datepicker="{format:'YYYY-MM-DD'}" autocomplete="off" value="<?php echo $date_to ?>" readonly >
                            </div>
                        </div>
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
                        <div class="uk-width-large-1-4" style="padding-top: 20px;">
                            <button type="submit" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light" >Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Batch Wise Report</h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                </div>
            </div>
            <div class="md-card-content">
                <div class="dt_colVis_buttons"></div>
                <table id="dt_tableExport" class="uk-table">
                    <thead>
                        <tr>
                            <th>SKU Code</th>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Month</th>
                            <th>Foical Year</th>
                            <th>Date</th>
                            <th>City</th>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>Opening</th>
                            <th>Primary</th>
                            <th>Secondary</th>
                            <th>Closing</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($rows as $row){
                                ?>

                        <tr>
                            <td><?php echo $row['sku_code']; ?></td>
                            <td><?php echo $row['product_id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['month']; ?></td>
                            <td><?php echo $row['fical_year']; ?></td>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo $row['city']; ?></td>
                            <td><?php echo $row['brand']; ?></td>
                            <td><?php echo $row['category']; ?></td>
                            <td><?php echo $row['opening']; ?></td>
                            <td><?php echo $row['primary']; ?></td>
                            <td><?php echo $row['secondary']; ?></td>
                            <td><?php echo $row['closing']; ?></td>
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
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-fixedcolumns/dataTables.fixedColumns.min.js"></script>
<!-- datatables custom integration -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/custom/datatables/datatables.uikit.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/datatable.js"></script>
<script>
    var csrfName = "<?php echo $this->security->get_csrf_token_name(); ?>",
        csrfHash = "<?php echo $this->security->get_csrf_hash(); ?>";
    var data = [];
    data[csrfName] = csrfHash;

    $.DataTableInit2({
        selector:'#dt_tableExport',
        aaSorting: [[1, "desc"]],
        fixedColumns:   {left: 0,right: 1},
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
    });
</script>
