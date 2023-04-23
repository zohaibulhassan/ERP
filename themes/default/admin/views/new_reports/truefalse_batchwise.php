<style>
    .table thead tr th {
        text-align: left !important;
    } 
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i>Batch Wise True & False </h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12" id="headerdiv">
                <p class="introtext">
                    <?= lang('customize_report'); ?>
                </p>
                <div>
                    <form action="<?= admin_url('reports/batch_wise_true_false') ?>" method="get">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <?= lang("product", "suggest_product"); ?>
                                    <?php echo form_input('sproduct', (isset($_GET['sproduct']) ? $_GET['sproduct'] : ""), 'class="form-control" id="suggest_product"'); ?>
                                    <input type="hidden" name="product" value="<?= isset($_GET['product']) ? $_GET['product'] : "" ?>" id="report_product_id" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="controls">
                                        <input type="submit" value="Submit" class="btn btn-primary" id="submitbtn" style="margin-top: 30px;" />
                                    </div>
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
                            <th>Batch</th>
                            <th>Type</th>
                            <th>Warehouse</th>
                            <th>Av.Quantity</th>
                            <th>Purchase Quantity</th>
                            <th>Purchase Return Quantity</th>
                            <th>Sale Quantity</th>
                            <th>Sale Return Quantity</th>
                            <th>SO Hold Quantity</th>
                            <th>Actual Quantity</th>
                            <th>Status</th>
                            <!-- <th>Note</th> -->
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

        function loadTable(req = ""){
            <?php
                $req = "product_id=".$sproduct;
                if($sbrandid == ""){
                    $req .= "&ssid=".$ssid;
                }
            ?>
            $('.tabledb').DataTable({
                dom: 'Bfrtip',
                scrollX: true,
                autoWidth: true,
                responsive: true,
                ajax: "<?= admin_url('reports/batch_wise_true_false_ajax?'.$req) ?>",
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