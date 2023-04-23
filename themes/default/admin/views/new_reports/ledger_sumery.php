<style>
    .table thead tr th {
        text-align: left !important;
    } 
    .table>thead:first-child>tr:first-child>th{
        text-align: center !important;
        font-size: 15px;
    }
    .table>thead:first-child>tr:first-child>th, .table>thead:first-child>tr:first-child>td, .table-striped thead tr.primary:nth-child(odd) th{
        background-color: #fcfdff !important;
        color: black !important;
        border-top: 1px solid #000000 !important;
    }
    .table>thead>tr>th, .table>thead>tr>td, .table-striped thead tr.primary:nth-child(odd) th {
        background-color: #428BCA !important;
        color: white !important;
        border-color: #357EBD !important;
        border-top: 1px solid #357EBD !important;
        text-align: center;
    }
    .searching_select{
        height: auto;
    }
    table.dataTable tfoot th, table.dataTable tfoot td{
        border-bottom: 1px solid #111;
        padding: 8px !important;
    }
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i>Ledger Summary</h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext">
                    <?= lang('customize_report'); ?>
                </p>
                <div>
                    <form action="<?= admin_url('reports/ledger_summery') ?>" method="get">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label" for="warehouse">
                                        <?= lang("warehouse"); ?>
                                    </label>
                                    <?php
                                    foreach ($warehouses as $warehouse) {
                                        $whl[$warehouse->id] = $warehouse->name;
                                    }
                                    echo form_dropdown('warehouse[]', $whl, $swarehouse, 'class="form-control searching_select" id="warehouse" multiple="multiple" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("warehouse") . '"');
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label" >Customer *</label>
                                    <?php
                                        foreach ($customers as $customer) {
                                            $cul[$customer->id] = $customer->name;
                                        }
                                        echo form_dropdown('customer[]', $cul, $customer_id, 'class="form-control searching_select" multiple="multiple" id="customerID" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("Customer") . '"');
                                    
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="own_companies">Companies *</label>
                                    <?php
                                        foreach ($companies as $company) {
                                            $col[$company->id] = $company->name;
                                        }
                                        echo form_dropdown('company[]', $col, $company_id, 'class="form-control searching_select" multiple="multiple" id="companyID" data-placeholder="' . $this->lang->line("Companies") . " " . $this->lang->line("Customer") . '"');
                                    
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="text" name="start_date" value="<?php echo $start?>" class="form-control date2" id="start_date" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="text" name="end_date" value="<?php echo $end?>" class="form-control date2" id="end_date" autocomplete="off"/>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="controls">
                                        <input type="submit" name="submit_report" value="Submit" class="btn btn-primary" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
            if($swarehouse != "" && count($swarehouse) > 0){
            ?>
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <th colspan="<?php echo count($recivable_rows['thead'])+2; ?>" >Customer - 
                                <?php 
                                    foreach($showwarehouses as $showwarehouse){
                                        echo $showwarehouse->name.' ('.$showwarehouse->code.'), ';
                                    }
                                ?> - <span style="color:red;" >Recivable</span></th>
                            </tr>
                            <tr>
                                <th style="width:400px" >Customer Name</th>
                                <?php
                                    foreach($recivable_rows['thead'] as $row){
                                        echo '<th>'.$row['name'].'</th>';
                                    }
                                ?>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach($recivable_rows['tbody'] as $row){
                            ?>
                            <tr>
                                <td><?= $row['customer_name'] ?></td>
                                <?php
                                    foreach($row['companies'] as $crow){
                                        echo '<td>'.amountformate($crow['value'],4,'PKR').'</td>';
                                    }
                                ?>
                                <td></td>
                            </tr>
                            <?php
                                }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <?php
                                    foreach($recivable_rows['thead'] as $row){
                                        echo '<th>'.$row['name'].'</th>';
                                    }
                                ?>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-md-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <th colspan="<?php echo count($recivable_rows['thead'])+2; ?>" >Customer - 
                                <?php 
                                    foreach($showwarehouses as $showwarehouse){
                                        echo $showwarehouse->name.' ('.$showwarehouse->code.'), ';
                                    }
                                ?> - <span style="color:red;" >Due</span></th>
                            </tr>

                            <tr>
                                <th style="width:400px" >Customer Name</th>
                                <?php
                                    foreach($due_rows['thead'] as $row){
                                        echo '<th>'.$row['name'].'</th>';
                                    }
                                ?>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach($due_rows['tbody'] as $row){
                            ?>
                            <tr>
                                <td><?= $row['customer_name'] ?></td>
                                <?php
                                    foreach($row['companies'] as $crow){
                                        echo '<td>'.amountformate($crow['value'],4,'PKR').'</td>';
                                    }
                                ?>
                                <td></td>
                            </tr>
                            <?php
                                }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <?php
                                    foreach($due_rows['thead'] as $row){
                                        echo '<th>'.$row['name'].'</th>';
                                    }
                                ?>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php
            }
        ?>
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

        $('.table').DataTable({
            dom: 'Bfrtip',
            paging: true,
            info: true,
            // ordering:false,
            pageLength: -1,
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
            ],
            footerCallback: function ( row, data, start, end, display ) {
                var api = this.api();
                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                <?php
                $no = 0;
                    foreach($recivable_rows['thead'] as $row){
                        $no++;
                    ?>
                var t<?= $no ?> = api.column(<?= $no ?>).data().reduce(function (a, b) {
                    return intVal(a)+intVal(b);
                },0);
                $(api.column(<?= $no ?>).footer()).html(t<?= $no ?>);
                    <?php
                    }
                ?>





            }
        });


    });
</script>
