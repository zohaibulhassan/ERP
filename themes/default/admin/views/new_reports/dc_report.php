<style>
    .table thead tr th {
        text-align: left !important;
    } 
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i>Customer/Sales Ledger</h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext">
                    <?= lang('customize_report'); ?>
                </p>
                <div>
                    <form action="<?= admin_url('reports/dc_report') ?>" method="get">
                        <div class="row">
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
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="controls">
                                    <input type="submit" name="submit_report" value="Submit" class="btn btn-primary" />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sale ID</th>
                            <th>Date</th>
                            <th>Ref No</th>
                            <th>Product Name</th>
                            <th>Product Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($rows as $row){
                        ?>
                        <tr>
                            <td><?php echo $row->id; ?></td>
                            <td><?php echo date_format(date_create($row->date),"Y-m-d"); ?></td>
                            <td><?php echo $row->ref_no; ?></td>
                            <td><?php echo $row->product_name; ?></td>
                            <td><?php echo $row->quantity; ?></td>
                            <td><?php echo $row->total; ?></td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="<?= admin_url('sales/view/'.$row->id) ?>" >View</a>
                            </td>
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
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-flash-1.6.4/b-html5-1.6.4/b-print-1.6.4/sb-1.0.0/datatables.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script>
    $(document).ready(function(){
        $('.date2').datetimepicker({
            format: 'yyyy-mm-dd', 
            fontAwesome: true, 
            language: 'sma', 
            todayBtn: 1, 
            autoclose: 1, 
            minView: 2 
        });

        $('.table').DataTable({
            dom: 'Bfrtip',
            paging: false,
            info: false,
            buttons: [
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [0,1,2,3,4,5]
                    }
                },
                {
                    extend: 'csv',
                    exportOptions: {
                        columns: [0,1,2,3,4,5]
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: [0,1,2,3,4,5]
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: [0,1,2,3,4,5]
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: [0,1,2,3,4,5]
                    }
                },
            ]
        });


    });
</script>