<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD ?>
<style>
    .dataTables_length {
        margin-right:15px;
    }
</style>    
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i
                class="fa-fw fa fa-star"></i>Activity Logs
        </h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table id="myTable" class="table table-bordered">
                        <thead>
                            <tr class="active">
                                <th style="width:150px" >Date</th>
                                <th style="width:150px">User</th>
                                <th style="width:60px">Product ID</th>
                                <th style="width:60x">PO ID</th>
                                <th style="width:60x">Purchase ID</th>
                                <th style="width:60x">SO ID</th>
                                <th style="width:60x">Sale ID</th>
                                <th style="width:60x">Transfer ID</th>
                                <th style="width:60x">Store ID</th>
                                <th style="width:600px" >Note</th>
                                <th style="width:300px" >Note</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr class="active">
                                <th style="width:150px" >Date</th>
                                <th style="width:150px">User</th>
                                <th style="width:60px">Product ID</th>
                                <th style="width:60px">PO ID</th>
                                <th style="width:60px">Purchase ID</th>
                                <th style="width:60px">SO ID</th>
                                <th style="width:60px">Sale ID</th>
                                <th style="width:60px">Transfer ID</th>
                                <th style="width:60px">Store ID</th>
                                <th style="width:600px" >Note</th>
                                <th style="width:300px" >Note</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-flash-1.6.4/b-html5-1.6.4/b-print-1.6.4/sb-1.0.0/datatables.min.js"></script>
<script>
$(document).ready(function(){
    var csrfName = "<?php echo $this->security->get_csrf_token_name(); ?>",
    csrfHash = "<?php echo $this->security->get_csrf_hash(); ?>";
    // $("#myTable tfoot th").each( function () {
    //     var title = $(this).text();
    //     $(this).html( "<input type='text' class='' name='"+title+"' placeholder='Search "+title+"' />" );
    // });
    $("#myTable").DataTable({
        "scrollX": true,
        "responsive": true,
        // Processing indicator
        "processing": true,
        // DataTables server-side processing mode
        "serverSide": true,
        // Load data from an Ajax source
        "ajax": {
            "url": '<?= admin_url('logs/get_logs'); ?>',
            "type": "POST",
            "data": {
                [csrfName]:csrfHash,
                product_id:'<?php echo $product_id ?>',
                po_id:'<?php echo $po_id ?>',
                purchase_id:'<?php echo $purchase_id ?>',
                so_id:'<?php echo $so_id ?>',
                sale_id:'<?php echo $sale_id ?>',
                transfer_id:'<?php echo $transfer_id ?>',
                store_id:'<?php echo $store_id ?>',
            }
        },
        //Set column definition initialisation properties
        "columnDefs": [
            { 
                "targets": 0,
                "orderable": false,
            },
            { 
                "targets": 1,
                "orderable": false,
            },
            { 
                "targets": 9,
                "orderable": false,
                "width": "200px"
            },
            { 
                "targets": 10,
                "orderable": false
            }
        ],
        "dom": 'Blfrtip',
        "buttons": [
            {
                "extend": 'copy',
                "exportOptions": {
                    "columns": [0,1,2,3,4,5,6,7,8,9,10]
                }
            },
            {
                "extend": 'csv',
                "exportOptions": {
                    "columns": [0,1,2,3,4,5,6,7,8,9,10]
                }
            },
            {
                "extend": 'excel',
                "exportOptions": {
                    "columns": [0,1,2,3,4,5,6,7,8,9,10]
                }
            },
            {
                "extend": 'pdf',
                "exportOptions": {
                    "columns": [0,1,2,3,4,5,6,7,8,9,10]
                }
            },
            {
                "extend": 'print',
                "exportOptions": {
                    "columns": [0,1,2,3,4,5,6,7,8,9,10]
                }
            },
        ],
        "lengthMenu": [
            [ 10, 25, 50, 100, -1 ],
            [ '10', '25', '50', '100', 'Show all' ]
        ],
        "paging": true,
        "pageLength": 25,
        "initComplete": function () {
            // this.api().columns().every(function(){
            //     var that = this;
            //     $( "input", this.footer() ).on( "keyup change clear", function () {
            //         if ( that.search() !== this.value ) {
            //             that
            //             .search(this.value)
            //             .draw();
            //         }
            //     });
            // });
        }
    });
    setTimeout(function(){
        var windowwidth = $(window ).width();
        var sidewidth = $('.sidebar-con').width();
        var width = windowwidth-sidewidth-30;
        console.log(windowwidth+"px");
        console.log(sidewidth+"px");
        console.log(width+"px");
        $('.dataTables_scrollHeadInner').css('width',width+'px');
        $('.dataTables_scrollHeadInner table').css('width',width+'px');
        $('#myTable').css('width',width+'px');
        width = width-40;
        $('.dataTables_scroll').css('width',width+'px');
    }, 500);
});
</script>
