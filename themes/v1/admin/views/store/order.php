<style>
    .md-card-toolbar-actions ul {
        margin-right:10px;
    }
    .md-card-toolbar-actions ul li i {
        font-size: 14px;
    }
    .uk-nav li>a {
        white-space: pre-wrap;
    }
    .uk-input-group-addon {
        padding: 0 5px !important;
    }
</style>
<div id="page_content">
    <div id="page_content_inner">
    <div class="md-card">
            <div class="md-card-toolbar">
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate">&#xE5D0;</i>
                    <div class="md-card-dropdown" data-uk-dropdown="{pos:'bottom-right',mode:'click'}">
                        <i class="md-icon material-icons">&#xE5D4;</i>
                        <div class="uk-dropdown uk-dropdown-small">
                            <ul class="uk-nav">
                                <li><a href="#" id="createSale" >Create Sale</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <h3 class="md-card-toolbar-heading-text"><?php echo $order->name?> </h3>
            </div>
            <div class="md-card-content" >
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                            <span class="uk-text-muted uk-text-small uk-text-italic"><b>Customer:</b></span>
                            <address>
                                <p><strong><?php echo $order->customer->first_name.' '.$order->customer->last_name  ?></strong></p>
                                <p><?php echo $order->customer->default_address->address1.', ' ?></p>
                                <p><?php echo $order->customer->default_address->address2.', ' ?></p>
                                <p><?php echo $order->customer->default_address->city.', '.$order->customer->default_address->province.', '.$order->customer->default_address->country ?></p>
                            </address>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                            <span class="uk-text-muted uk-text-small uk-text-italic"><b>Shipping Address:</b></span>
                            <address>
                                <p><strong><?php echo $order->shipping_address->first_name.' '.$order->shipping_address->last_name  ?></strong></p>
                                <p><?php echo $order->shipping_address->address1.', ' ?></p>
                                <p><?php echo $order->shipping_address->address2.', ' ?></p>
                                <p><?php echo $order->shipping_address->city.', '.$order->shipping_address->province.', '.$order->shipping_address->country ?></p>
                            </address>
                        </div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="uk-margin-bottom">
                            <span class="uk-text-muted uk-text-small uk-text-italic"><b>Detials:</b></span>
                            <address>
                                <p><b>Order ID:</b> <?php echo $order->id ?></p>
                                <p><b>Order No:</b> <?php echo $order->name ?></p>
                                <p><b>Date:</b> <?php echo dateformate($order->created_at,'Y-m-d H:i:s'); ?></p>
                                <p><b>Sale Status:</b> <?php echo $order->financial_status ?></p>
                            </address>
                        </div>
                    </div>
                </div>
                <h4 class="table_heading" >Items List</h4>
                <div class="dt_colVis_buttons"></div>
                <table id="itemsTable" class="uk-table">
                    <thead>
                        <tr>
                            <th style="width:150px">Name</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th>Fulfillable Quantity</th>
                            <th>Discount</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $sale_item = array();
                            foreach($order->line_items as $item){
                                $sale_item[] = array(
                                    'id' => $item->id,
                                    'quantity' => $item->quantity
                                );
                                ?>
                                <tr>
                                    <td><?php echo $item->name?></td>
                                    <td><?php echo $item->price?></td>
                                    <td><?php echo $item->quantity?></td>
                                    <td><?php echo $item->fulfillable_quantity?></td>
                                    <td><?php echo $item->total_discount?></td>
                                    <td><?php echo $item->quantity*$item->price ?></td>

                                </tr>
                                
                                <?php
                            }
                        ?>
                    </tbody>
                </table>
                <table class="uk-table" style="max-width: 300px;float: right;">
                    <tfoot>
                        <tr>
                            <th style="text-align:right" >Subtotal</th>
                            <td><?php echo $order->current_subtotal_price; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align:right" >Total Discount</th>
                            <td><?php echo $order->current_total_discounts; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align:right" >Total Tax</th>
                            <td><?php echo $order->current_total_tax; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align:right" >Total</th>
                            <td><?php echo $order->current_total_price; ?></td>
                        </tr>
                    </tfoot>
                </table>
                <div style="clear:both"></div>
                <h4 class="table_heading" >Trackings</h4>
                <div class="dt_colVis_buttons"></div>
                <table id="itemsTable" class="uk-table">
                    <thead>
                        <tr>
                            <th style="width:150px">S.No</th>
                            <th>Tracking Code</th>
                            <th>Tracker Company</th>
                            <th>Tracker Status</th>
                            <th>COD Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $tno = 0;
                            foreach($order->fulfillments as $fulfillment){
                                foreach($fulfillment->tracking_numbers as $tracking_number){
                                    $tno++;
                                    $trackingdata = get_traking_data($tracking_number);
                                    ?>
                                    <tr>
                                        <td><?php echo $tno?></td>
                                        <td><?php echo $tracking_number ?></td>
                                        <td><?php echo $fulfillment->tracking_company ?></td>
                                        <td><?php echo $trackingdata['status'] ?></td>
                                        <td><?php echo $trackingdata['cod_amount'] ?></td>
                                        <td>
                                            <button type="button" data-json='<?php echo get_traking($tracking_number) ?>' class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light md-btn-mini trakingdata" >Tracking</button>
                                        </td>

                                    </tr>
                                    
                                    <?php
                                }
                            }
                        ?>
                    </tbody>
                </table>
                <div style="clear:both"></div>
            </div>
        </div>
    </div>
</div>
<div class="uk-modal" id="modal_tracking">
    <div class="uk-modal-dialog">
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Tracking History</h3>
        </div>
        <div class="uk-modal-body">
            <div class="uk-width-large-1-1">
                <div class="uk-grid">
                    <table class="uk-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <button type="button" class="md-btn md-btn-flat uk-modal-close" >Close</button>
        </div>
    </div>
</div>




<!-- datatables -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<!-- datatables buttons-->
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/dataTables.buttons.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/custom/datatables/buttons.uikit.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/jszip/dist/jszip.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/pdfmake/build/pdfmake.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/pdfmake/build/vfs_fonts.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.colVis.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.html5.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-buttons/js/buttons.print.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/datatables-fixedcolumns/dataTables.fixedColumns.min.js"></script>
<!-- datatables custom integration -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/custom/datatables/datatables.uikit.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>js/datatable.js"></script>
<script>
    $(document).ready(function(){
        $(document).on('click','.trakingdata',function(){
            var rows = $(this).data('json');
            var html = "";
            $.each(rows, function(index, item) {
                var d = new Date(item.TransactionDate);
                var date = d.getFullYear()+"-"+setdecimal(d.getMonth())+"-"+setdecimal(d.getDate())+" "+setdecimal(d.getHours())+":"+setdecimal(d.getMinutes())+":"+setdecimal(d.getSeconds());
                html +="<tr>";
                    html += "<td>"+date+"</td>";
                    html += "<td>"+item.ProcessDescForPortal+"</td>";
                    html += "<td>"+item.OriginCity+"</td>";
                html +="</tr>";
            });
            $('#modal_tracking tbody').html(html);
            UIkit.modal('#modal_tracking').show();
        });
        function setdecimal(no){
            if(no < 10 && no > -10){
                no = "0"+no;

            }
            return no;
        } 
        $('#createSale').click(function(){
            var items =  '<?php echo json_encode($sale_item); ?>';
            var warehosue_id = <?php echo $store->warehouse_id?>;
            $.ajax({
                type: "get",
                url: '<?= admin_url('stores/get_products_for_sale'); ?>',
                data: {items: items,warehosue_id:warehosue_id},
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    if(obj.codestatus){
                        localStorage.removeItem("so_items");
                        localStorage.setItem('so_items',JSON.stringify(obj.products));
                        window.location.href = "<?php echo base_url('admin/salesorders/add?code='.$order_code); ?>";
                        
                    }
                    else{
                        alert(obj.message);
                    }

                }
            });
        });


    });
</script>



