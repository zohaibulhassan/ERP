<style>
    .table thead tr th {
        text-align: left !important;
    } 
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i>Sales Items Wise Report</h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12" id="headerdiv">
                <p class="introtext">
                    <?= lang('customize_report'); ?>
                </p>
                <div>
                    <form action="<?= admin_url('reports/salesreport') ?>" method="get">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("own_companies", "poown_companies"); ?>
                                    <?php
                                    foreach ($own_companies as $own_companies) {
                                        $oc[$own_companies->id] = $own_companies->companyname;
                                    }
                                    echo form_dropdown('own_company[]', $oc, $own_company , 'id="poown_companies" multiple="multiple" class="form-control input-tip searching_select" data-placeholder="' . lang("select") . ' ' . lang("own_companies") . '" style="width:100%;" ');
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("suppliers", "suppliers"); ?>
                                    <?php
                                    foreach ($suppliers as $supplier) {
                                        $bl[$supplier->id] = $supplier->name;
                                    }
                                    echo form_dropdown('supplier[]', $bl, $csupplier, 'id="suppliers" multiple="multiple" data-placeholder="' . lang("select") . ' ' . lang("supplier") . '" class="form-control input-tip searching_select" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("customers", "customers"); ?>
                                    <?php
                                    foreach ($customers as $customer) {
                                        $bl[$customer->id] = $customer->name;
                                    }
                                    echo form_dropdown('customers[]', $bl, $ccustomer, 'id="customers" multiple="multiple" data-placeholder="' . lang("select") . ' ' . lang("cuctomer") . '"  class="form-control input-tip searching_select" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                            <?php
                                if($user_warehouses == ""){
                            ?>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label" for="warehouse">
                                        <?= lang("warehouse"); ?>
                                    </label>
                                    <?php
                                    foreach ($warehouses as $warehouse) {
                                        $whl[$warehouse->id] = $warehouse->name;
                                    }
                                    echo form_dropdown('warehouse[]', $whl, $swarehouse, 'class="form-control searching_select" multiple="multiple" id="warehouse" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("warehouse") . '"');
                                    ?>
                                </div>
                            </div>
                            <?php
                                }
                            ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="text" name="start_date" value="<?php echo $start?>" class="form-control date2" id="start_date" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="text" name="end_date" value="<?php echo $end?>" class="form-control date2" id="end_date" autocomplete="off"/>
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
                            <th>Own Company</th>
                            <th>Customer NIC</th>
                            <th>Customer NTN</th>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th>P.O Number</th>
                            <th>Customer Name</th>
                            <th>E-talier Name</th>
                            <th>FBR Registeration Status</th>
                            <th>Product ID</th>
                            <th>Company Code</th>
                            <th>Barcode</th>
                            <th>Brand</th>
                            <th>HSN Code</th>
                            <th>Product Name</th>
                            <th>Carton Size</th>
                            <th>MRP</th>
                            <th>Qty Order</th>
                            <th>UOM</th>
                            <th>Carton Qty</th>
                            <th>Price Excluding Tax(TP)</th>
                            <th>Selling Price</th>
                            <th>Value Excluding Tax</th>
                            <th>Tax %</th>
                            <th>Item Tax</th>
                            <th>Advance Income Tax</th>
                            <th>Further Tax</th>
                            <th>FED Tax</th>
                            <th>Sales Value</th>
                            <th>Total Including All Taxes</th>
                            <th>Sales Incentive Value</th>
                            <th>Sales Incentive %</th>
                            <th>Trade Discount Value</th>
                            <th>Trade Discount %</th>
                            <th>Consumer Discount Value</th>
                            <th>Consumer Discount %</th>
                            <th>Total Discount</th>
                            <th>Net Amount</th>
                            <th>Expiry Date</th>
                            <th>Batch</th>
                            <th>Warehouse</th>
                            <th>Supplier Name</th>
                            <th>Remarks</th>
                            <th>M.R.P Excluding Tax</th>
                            <th>M.R.P Third Schedule</th>
                            <th>Group ID</th>
                            <th>Group Name</th>
                        </tr>
                    </thead>
                    <tbody>

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

        function loadTable(req = ""){
            <?php
                $req = "";
                foreach($own_company as $row){
                    $req .= "own_company%5B%5D=".$row."&";
                }
                foreach($swarehouse as $row){
                    $req .= "warehouse%5B%5D=".$row."&";
                }
                foreach($csupplier as $row){
                    $req .= "supplier%5B%5D=".$row."&";
                }
                foreach($ccustomer as $row){
                    $req .= "customer%5B%5D=".$row."&";
                }
                if($start != ""){
                    $req .= "start=".urlencode($start)."&";
                }
                if($end != ""){
                    $req .= "end=".urlencode($end)."&";
                }
            ?>
            $('.tabledb').DataTable({
                dom: 'Bfrtip',
                scrollX: true,
                autoWidth: true,
                responsive: true,
                ajax: "<?= admin_url('reports/salesreport_ajax?'.$req) ?>",
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
        }
        loadTable();
    });
</script>