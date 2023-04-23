<style>
    .table thead tr th {
        text-align: left !important;
    }
    .tabledb input {

    }
</style>
                        
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i>Check Rhocom's Products Intergration (<?= $store->name ?>)</h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12" id="headerdiv">
                <div>
                    <form action="<?= admin_url('stores/report3') ?>" method="get">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Supplier</label>
                                    <input type="hidden" name="sid" value="<?= $store->id ?>" >
                                    <select name="supplier"  class="form-control input-tip">
                                        <option value="">Select Supplier</option>
                                        <option value="all">All</option>
                                        <?php
                                            foreach($suppliers as $supplier){
                                                echo '<option ';
                                                if($supplier->id == $gsupplier){
                                                    echo 'selected';
                                                }
                                                echo ' value="'.$supplier->id.'">'.$supplier->name.'</option>';
                                            }
                                        ?>
                                    </select>
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
                            <th>Brand</th>
                            <th>Avaiable Qty</th>
                            <th>MRP</th>
                            <th>Store Product ID</th>
                            <th>Store Product Name</th>
                            <th>Type</th>
                            <th>Update Qty In</th>
                            <th>Update Price In</th>
                            <th>Apply Discount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($products as $product){
                        ?>
                        <tr>
                            <td><?=  $product['pid']; ?></td>
                            <td><?=  $product['pname']; ?></td>
                            <td><?=  $product['brand_name']; ?></td>
                            <td><?=  $product['qty']; ?></td>
                            <td><?=  $product['pmrp']; ?></td>
                            <td><?=  $product['spid']; ?></td>
                            <td><?=  $product['spname']; ?></td>
                            <td><?=  $product['update_in']; ?></td>
                            <td><?=  $product['update_qty_in']; ?></td>
                            <td><?=  $product['price_type']; ?></td>
                            <td><?=  $product['discount']; ?></td>
                            <td><?=  $product['status']; ?></td>
                        </tr>
                        <?php        
                            }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Brand</th>
                            <th>Avaiable Qty</th>
                            <th>MRP</th>
                            <th>Store Product ID</th>
                            <th>Store Product Name</th>
                            <th>Type</th>
                            <th>Update Qty In</th>
                            <th>Update Price In</th>
                            <th>Apply Discount</th>
                            <th>Status</th>
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
    $(document).ready( function () {
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
            $('.sorting_asc').click();
        }, 500);


        $(".tabledb tfoot th").each( function () {
            var title = $(this).text();
            $(this).html( "<input type='text' class='' placeholder='Search "+title+"' />" );
        });
        $('.tabledb').DataTable({
            dom: 'Bfrtip',
            scrollX: true,
            responsive: true,
            paging: true,
            pageLength: 25,
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

