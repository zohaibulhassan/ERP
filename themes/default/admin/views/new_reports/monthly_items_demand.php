<style>
    .table thead tr th {
        text-align: left !important;
    } 
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i>Monthly Items Demand</h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12" id="headerdiv">
                <p class="introtext">
                    <?= lang('customize_report'); ?>
                </p>
                <div>
                    <form action="<?= admin_url('reports/monthly_items_demand') ?>" method="get">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label" for="warehouse">
                                        <?= lang("warehouse"); ?>
                                    </label>
                                    <?php
                                    // $whl[""] = lang('select') . ' ' . lang('warehouse');
                                    foreach ($warehouses as $warehouse) {
                                        $whl[$warehouse->id] = $warehouse->name;
                                    }
                                    echo form_dropdown('warehouse[]', $whl, $swarehouse, 'class="form-control searching_select" multiple="multiple" id="warehouse" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("warehouse") . '"');
                                    ?>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label" for="brand">
                                        <?= lang("brand"); ?>
                                    </label>
                                    <?php
                                    // $brandlist[""] = lang('select') . ' ' . lang('brand');
                                    foreach ($brands as $brand) {
                                        $brandlist[$brand->id] = $brand->name;
                                    }
                                    echo form_dropdown('brand[]', $brandlist, $sbrand, 'class="form-control searching_select" id="brand" multiple="multiple" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("brand") . '"');
                                    ?>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label" for="customers">
                                        <?= lang("customers"); ?>
                                    </label>
                                    <?php
                                    // $customerlist[""] = lang('select') . ' ' . lang('customer');
                                    foreach ($customers as $customer) {
                                        $customerlist[$customer->id] = $customer->name;
                                    }
                                    echo form_dropdown('cutomer[]', $customerlist, $scutomer, 'class="form-control searching_select" id="customer"  multiple="multiple" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("customer") . '"');
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
                            <th>Month</th>
                            <th>Group ID</th>
                            <th>Group Name</th>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Warehouse</th>
                            <th>Cutomer</th>
                            <th>Brand</th>
                            <th>Carton Size</th>
                            <th>MRP</th>
                            <th>Cost (With Tax)</th>
                            <th>Demand Quantity</th>
                            <th>Demand Master Carton</th>
                            <!-- <th>Sold Quantity</th>
                            <th>Sold Master Carton</th>
                            <th>Remaining Quantity</th>
                            <th>Remaining Master Carton</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($rows as $row){
                         $month = DateTime::createFromFormat('!m', $row->d_month);
                       ?>
                        <tr>
                            <td><?= $month->format('M') ?>-<?= $row->d_year ?></td>
                            <td><?= $row->group_id?></td>
                            <td><?= $row->group_name?></td>
                            <td><?= $row->pid?></td>
                            <td><?= $row->pname?></td>
                            <td><?= $row->wid?></td>
                            <td><?= $row->customer?></td>
                            <td><?= $row->brand?></td>
                            <td><?= $row->carton_size?></td>
                            <td><?= $row->pmrp?></td>
                            <td>
                                <?php
                                    if($row->tax_type == 2){
                                        echo $row->tax_rate+$row->cost;
                                    }
                                    else{
                                        echo ($row->cost/100*$row->tax_rate)+$row->cost;
                                    }
                                ?>


                            </td>
                            <td><?= $row->quantity?></td>
                            <td><?= $dc = decimalallow($row->quantity/$row->carton_size,2) ?></td>

                        </tr>
                        <?php
                        }
                        ?>

                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Month</th>
                            <th>Group ID</th>
                            <th>Group Name</th>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Warehouse</th>
                            <th>Cutomer</th>
                            <th>Brand</th>
                            <th>Carton Size</th>
                            <th>MRP</th>
                            <th>Cost (With Tax)</th>
                            <th>Demand Quantity</th>
                            <th>Demand Master Carton</th>
                            <!-- <th>Sold Quantity</th>
                            <th>Sold Master Carton</th>
                            <th>Remaining Quantity</th>
                            <th>Remaining Master Carton</th> -->
                        </tr>
                    </tfoot>
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
            var width = windowwidth-sidewidth-30;
            console.log(windowwidth+"px");
            console.log(sidewidth+"px");
            console.log(width+"px");
            $('.dataTables_scrollHeadInner').css('width',width+'px');
            $('.dataTables_scrollHeadInner table').css('width',width+'px');
            $('.tabledb').css('width',width+'px');
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

        $(".tabledb tfoot th").each( function () {
            var title = $(this).text();
            $(this).html( "<input type='text' class='' placeholder='Search "+title+"' />" );
        });
        $('.tabledb').DataTable({
            dom: 'Bfrtip',
            "pageLength": 25,
            scrollX: true,
            responsive: true,
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
            ],
            initComplete: function () {
                this.api().columns().every(function(){
                    var that = this;
                    $( "input", this.footer() ).on( "keyup change clear", function () {
                        if ( that.search() !== this.value ) {
                            that
                            .search(this.value)
                            .draw();
                        }
                    });
                });
            }
        });
    });
</script>