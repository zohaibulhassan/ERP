<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i
                class="fa-fw fa fa-star"></i>Purchase Orders
        </h2>

        <div class="box-icon">
            <ul class="btn-tasks">
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?=lang('list_results');?></p>

                <div class="table-responsive">
                    <table id="POData" class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr class="active">
                            <th>Date</th>
                            <th>Purchase Reference No</th>
                            <th>Reference No</th>
                            <th>Supplier</th>
                            <th>Total</th>
                            <th>Surcharge</th>
                            <th>Grand Total</th>
                            <th class="nosort" style="width:100px;"><?= lang("actions"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(count($pr_rows)>0){

                                foreach($pr_rows as $row){
                            ?>
                            <tr>
                                <td><?php echo $row->return_date ?></td>
                                <td><?php echo $row->p_reference_no ?></td>
                                <td><?php echo $row->reference_no ?></td>
                                <td><?php echo $row->supplier_name ?></td>
                                <td><?php echo $row->total ?></td>
                                <td><?php echo $row->surecharge ?></td>
                                <td><?php echo $row->grand_total ?></td>
                                <td>
                                    <div class="text-center">
                                        <div class="btn-group text-left">
                                            <button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">
                                                <?php echo lang('actions'); ?>
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu pull-right" role="menu">
                                                <li><?php echo anchor('admin/purchases/return_view/'.$row->id, '<i class="fa fa-file-text-o"></i> Detail'); ?></li>
                                                <li><a href="" class="deletebtn" data-id="<?php echo $row->id; ?>" ><i class="fa fa-trash"></i> Delete</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                        <tfoot class="dtFilter">
                            <tr class="active">
                                <th>Date</th>
                                <th>Purchase Reference No</th>
                                <th>Reference No</th>
                                <th>Supplier</th>
                                <th>Total</th>
                                <th>Surcharge</th>
                                <th>Grand Total</th>
                                <th style="width:100px;"><?= lang("actions"); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
$(document).ready(function(){
    $('#POData').DataTable({
        "aaSorting": [[ 3, "desc" ]],
        'aoColumnDefs': [{
            'bSortable': false,
            'aTargets': ['nosort']
        }]
    });

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


});

</script>
