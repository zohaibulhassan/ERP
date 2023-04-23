<div class="box">
    <div class="box-header">
        <h2 class="blue"> <i class="fa-fw fa fa-file"></i> Purchase Return Number. <?php echo $details->id ?> </h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="Actions"></i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <!-- <li>
                            <a href="#" data-target="#myModal" data-toggle="modal"><i class="fa fa-money"></i> View Payments </a>
                        </li> -->
                        <li><a href="" class="deletebtn" data-id="<?php echo $details->id; ?>" ><i class="fa fa-trash"></i> Delete</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="clearfix"></div>
                <div class="well well-sm">
                    <div class="col-xs-4 border-right">
                        <div class="col-xs-2"><i class="fa fa-3x fa-building padding010 text-muted"></i>
                        </div>
                        <div class="col-xs-10">
                            <h2 class=""><?php echo $details->supper_name ?></h2>
                            <?php echo $details->supper_address ?>
                            <p></p>
                            Tel: <?php echo $details->supper_phone ?><br />
                            Email: <?php echo $details->supper_email ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-xs-4">
                        <div class="col-xs-2">
                            <i class="fa fa-3x fa-truck padding010 text-muted"></i>
                        </div>
                        <div class="col-xs-10">
                            <h2 class=""><?= $Settings->site_name; ?></h2>
                            <?php echo $details->warehouse_name.'('.$details->warehouse_code.')' ?>
                            <p>Address<?php echo $details->warehouse_address ?></p><br>
                            Tel: <?php echo $details->warehouse_phone ?><br>
                            Email: <?php echo $details->warehouse_email ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-xs-4 border-left">
                        <div class="col-xs-2"><i class="fa fa-3x fa-file-text-o padding010 text-muted"></i>
                        </div>
                        <div class="col-xs-10">
                            <h2 class="">Purchase Reference: <?php echo $details->p_reference_no ?></h2>
                            <h2 class="">Purchase Return Reference: <?php echo $details->reference_no ?></h2>
                            <p style="font-weight:bold;">Return Date: <?php echo $details->return_date ?> </p>
                            <p style="font-weight:bold;">Status: <?php echo $details->status ?></p>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="table-responsive">
                    <table
                        class="table table-bordered table-hover table-striped print-table order-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th style="padding-right:20px;">Unit Cost</th>
                                <th style="padding-right:20px; text-align:center; vertical-align:middle;">Tax</th>
                                <th style="padding-right:20px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach($details->items as $item){
                            ?>
                            <tr>
                                <td style="text-align:center; width:40px; vertical-align:middle;">1</td>
                                <td style="vertical-align:middle;">
                                    <?php echo $item->code ?> - <?php echo $item->name ?> <br>Expiry: <?php echo $item->expiry ?>
                                </td>
                                <td style="width: 120px; text-align:center; vertical-align:middle;"><?php echo $item->quantity ?></td>
                                <td style="text-align:right; width:120px; padding-right:10px;"> <?php echo $item->net_unit_cost ?></td>
                                <td style="width: 120px; text-align:right; vertical-align:middle;">
                                    <?php echo $item->item_tax ?> (<small><?php echo $item->item_tax_rate ?></small>)
                                </td>
                                <td style="text-align:right; width:120px; padding-right:10px;"><?php echo $item->total ?></td>
                            </tr>
                            
                            <?php
                                }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" style="text-align:right; font-weight:bold;">Total Amount (PKR)</td>
                                <td style="text-align:right; padding-right:10px; font-weight:bold;">PKR <?php echo $details->subtotal ?></td>
                            </tr>
                            <tr>
                                <td colspan="5" style="text-align:right; font-weight:bold;">Surcharge (PKR)</td>
                                <td style="text-align:right; font-weight:bold;">PKR <?php echo $details->surcharge ?></td>
                            </tr>
                            <tr>
                                <td colspan="5" style="text-align:right; font-weight:bold;">Grand Total (PKR)</td>
                                <td style="text-align:right; font-weight:bold;">PKR <?php echo $details->grand_total ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="row">
                    <div class="col-xs-7">
                    <?php 
                        if($details->note != ""){
                    ?>
                        <div class="well well-sm">
                            <p class="bold">Note:</p>
                            <div><?php echo $details->note ?></div>
                        </div>
                    <?php
                        }
                    ?>
                    </div>
                    <div class="col-xs-4 col-xs-offset-1">
                        <div class="well well-sm">
                            <p>Created by : <?php echo $details->creater_fname.' '.$details->creater_lname ?> </p>
                            <p>Date: <?php echo $details->created_at ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
    $('.deletebtn').click(function(e){
        e.preventDefault();
        var rid = $(this).data('id');
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        swal({
            title: "Are you sure?",
            text: "Do you want to Delete purchase return!",
            icon: "warning",
            buttons: true,
            successMode: true,
            confirmButtonText: 'Delete',
        })
        .then(id => {
            if (!id) throw null;
            <?php 
                $url = base_url('admin/purchases/return_delete?id=');
                ?>
            return fetch("<?php echo $url; ?>"+rid+"&"+[csrfName]+"="+csrfHash);
        })
        .then(results => {
            return results.json();
        })
        .then(json => {
            if(json.codestatus ==  "Purchase Return Delete Successfully"){
                swal("Purchase Return Delete Successfully", {
                    icon: "success",
                });
                setTimeout(function(){ 
                    window.top.location.href = '<?= admin_url('purchases/returns'); ?>';
                }, 1500);
            }
            else{
                swal("Erro!", json.codestatus, "error");
            }
        })
        .catch(err => {
            if (err) {
                console.log(err);
                swal("Oh noes!", "The AJAX request failed!", "error");
            }
            else {
                swal.stopLoading();
                swal.close();
            }
        });
    });

</script>
