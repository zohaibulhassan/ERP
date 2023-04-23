<style>
    .table thead tr th {
        text-align: left !important;
    } 
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i>Sale Return Items Report With Remarks</h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext">
                    <?= lang('customize_report'); ?>
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sale Date</th>
                            <th>Refrence No</th>
                            <th>PO Number</th>
                            <th>Cutomer</th>
                            <th>Own Company</th>
                            <th>Warehouse</th>
                            <th>Product ID</th>
                            <th>Company Code</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Expiry</th>
                            <th>Price With-Out Tax</th>
                            <th>MRP</th>
                            <th>Tax</th>
                            <th>Sub Total</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($rows as $row){
                        ?>
                            <tr>
                                <td><?php echo date_format(date_create($row->sale_date),"Y-m-d"); ?></td>
                                <td><?php echo $row->reference_no; ?></td>
                                <td><?php echo $row->po_number; ?></td>
                                <td><?php echo $row->customer; ?></td>
                                <td><?php echo $row->own_company; ?></td>
                                <td><?php echo $row->warehouse_name; ?></td>
                                <td><?php echo $row->product_id; ?></td>
                                <td><?php echo $row->company_code; ?></td>
                                <td><?php echo $row->product_name; ?></td>
                                <td><?php echo $row->quantity; ?></td>
                                <td><?php echo $row->expiry; ?></td>
                                <td><?php echo $row->net_unit_price; ?></td>
                                <td><?php echo $row->mrp; ?></td>
                                <td><?php echo $row->tax; ?></td>
                                <td><?php echo $row->subtotal; ?></td>
                                <td><?php echo $row->note; ?></td>
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

        $('.table').DataTable({
            dom: 'Bfrtip',
            paging: true,
            info: true,
            ordering:false,
            pageLength: -1,
            buttons: [
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]
                    }
                },
                {
                    extend: 'csv',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]
                    }
                },
            ]
        });


    });
</script>
