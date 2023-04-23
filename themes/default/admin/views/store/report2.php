<style>
    .table thead tr th {
        text-align: left !important;
    }
    .tabledb input {

    }
</style>
                        
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i>Check Store's Products (<?= $store->name ?>)</h2>
        <span id="ajaxloading" style="float: right;line-height: 41px;padding-right: 38px;color: red;font-weight: bold;" ><b>0</b> Products Found. <strong style="display:none;" >Loading......</strong> </span>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-md-12">
                <table class="tabledb">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Product MRP</th>
                            <th>Store Product ID</th>
                            <th>Store Product Name</th>
                            <th>Reguler Price</th>
                            <th>Selling Price</th>
                            <th>Store Status</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Product MRP</th>
                            <th>Store Product ID</th>
                            <th>Store Product Name</th>
                            <th>Reguler Price</th>
                            <th>Selling Price</th>
                            <th>Store Status</th>
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
        var i = 0;
        var sid = <?= $store->id ?>;
        var limit = 100;
        var products = [];
        var totalproduct =0;

        get_products();
        //Data Load
        function get_products(){
            $('#ajaxloading strong').show();
            i++;
            console.log(i);
            
            $.ajax({
                type: "get",
                data: {sid:sid,page:i,limit:limit},
                url: '<?= admin_url('stores/report2_ajax'); ?>',
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    if(obj.codestatus == "ok"){
                        products = products.concat(obj.products);
                        totalproduct += obj.count;
                        $('#ajaxloading b').html(totalproduct);
                        if(obj.count>=limit){
                            get_products();
                            console.log('Send Again');
                        }
                        else{
                            console.log('Complete');
                            $('#ajaxloading strong').hide();
                            fullData();
                        }
                    }
                    else{
                        $('#ajaxloading strong').hide();
                        fullData();
                        alert(obj.codestatus);
                    }
                }
            });
        }
        function fullData(){
            products.forEach(function(product) {
                console.log(product);
                var html = '';
                html += '<tr>';
                    html += '<td>'+product.pid+'</td>';
                    html += '<td>'+product.pname+'</td>';
                    html += '<td>'+product.mrp+'</td>';
                    html += '<td>'+product.spid+'</td>';
                    html += '<td>'+product.spname+'</td>';
                    html += '<td>'+product.spregular+'</td>';
                    html += '<td>'+product.spsales+'</td>';
                    html += '<td>'+product.sstatus+'</td>';
                    html += '<td>'+product.status+'</td>';
                html += '</tr>';
                $('.tabledb tbody').append(html);
            });

            $(".tabledb tfoot th").each( function () {
                var title = $(this).text();
                $(this).html( "<input type='text' class='' placeholder='Search "+title+"' />" );
            });
            $('.tabledb').DataTable({
                dom: 'Bfrtip',
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
        }
    });
</script>

