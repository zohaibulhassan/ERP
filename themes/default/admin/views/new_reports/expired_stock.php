<style>
    .table thead tr th {
        text-align: left !important;
    } 
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i>Expired Stock</h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12" id="headerdiv">
                <p class="introtext">
                    <?= lang('customize_report'); ?>
                </p>
                <div>
                    <form action="<?= admin_url('reports/expired_stock') ?>" method="get">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang("suppliers", "suppliers"); ?>
                                    <?php
                                    $bl["all"] = "All";
                                    foreach ($suppliers as $supplier) {
                                        $bl[$supplier->id] = $supplier->name;
                                    }
                                    echo form_dropdown('supplier', $bl, $csupplier, 'id="suppliers" data-placeholder="' . lang("select") . ' ' . lang("supplier") . '" required="required" class="form-control input-tip searching_select" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                            <div class="col-sm-3">
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
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label" for="brand">
                                        <?= lang("brand"); ?>
                                    </label>
                                    <?php
                                    $brandlist[""] = lang('select') . ' ' . lang('brand');
                                    foreach ($brands as $brand) {
                                        $brandlist[$brand->id] = $brand->name;
                                    }
                                    echo form_dropdown('brand', $brandlist, $sbrand, 'class="form-control searching_select" id="brand" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("brand") . '"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="controls">
                                    <input type="submit" value="Submit" class="btn btn-primary" id="submitbtn" />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="tabledb">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Purchase Date</th>
                            <th>Supplier</th>
                            <th>Warehouse</th>
                            <th>Brand</th>
                            <th>Batch</th>
                            <th>Expiry Date</th>
                            <th>Product Expired</th>
                            <th>Balance Qty</th>
                            <th>Carton Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($rows as $row){
                            $fexpiry = date('Y-m-d', strtotime(str_replace('/', '-', $row->expire)));;
                            if($fexpiry<=date('Y-m-d')){
                                $days = abs(strtotime(date('Y-m-d')) - strtotime($fexpiry));
                                $days = $days/(24*60*60);
                       ?>
                        <tr>
                            <td><?= $row->pid?></td>
                            <td><?= $row->pname?></td>
                            <td><?= $row->date?></td>
                            <td><?= $row->supplier_name?></td>
                            <td><?= $row->warehouse_name?></td>
                            <td><?= $row->brand_name?></td>
                            <td><?= $row->batch?></td>
                            <td><?= $row->expire?></td>
                            <td><?= $days ?> Ago</td>
                            <td><?= $row->qty?></td>
                            <td><?= decimalallow($row->qty/$row->carton_size,2) ?></td>
                        </tr>
                        <?php
                            }
                        }
                        ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-flash-1.6.4/b-html5-1.6.4/b-print-1.6.4/sb-1.0.0/datatables.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>

<script>
    $(document).ready(function(){

        setTimeout(function(){
            var windowwidth = $(window ).width();
            var sidewidth = $('.sidebar-con').width();
            console.log(windowwidth+"px");
            console.log(sidewidth+"px");
            var width = windowwidth-sidewidth-30;
            $('#headerdiv').css('width',width+'px');
            width = width-40;
            $('.dataTables_scroll').css('width',width+'px');
        }, 500);


        $('.date2').datetimepicker({
            format: 'yyyy-mm-dd', 
            fontAwesome: true, 
            language: 'sma', 
            todayBtn: 1, 
            autoclose: 1, 
            minView: 2 
        });

        $('.tabledb').DataTable({
            dom: 'Bfrtip',
            "pageLength": 50,
            buttons: [
                {
                    extend: 'copy',
                },
                {
                    extend: 'csv',
                },
                {
                    extend: 'excel',
                },
            ]
        });
    });
</script>