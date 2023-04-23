<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>

</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-dollar"></i><?= lang('expenses'); ?></h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li>
                            <a href="<?= admin_url('purchases/add_expense') ?>">
                                <i class="fa fa-plus-circle"></i> <?= lang('add_expense') ?>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="EXPData" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr class="active">
                                <th class="col-xs-2"><?= lang("date"); ?></th>
                                <th class="col-xs-2"><?= lang("reference"); ?></th>
                                <th class="col-xs-2"><?= lang("Type"); ?></th>
                                <th class="col-xs-2"><?= lang("Location"); ?></th>
                                <th class="col-xs-2"><?= lang("Category"); ?></th>
                                <th class="col-xs-2"><?= lang("Own Compnay"); ?></th>
                                <th class="col-xs-2"><?= lang("Payment Method"); ?></th>
                                <th class="col-xs-1"><?= lang("amount"); ?></th>
                                <th class="col-xs-3"><?= lang("note"); ?></th>
                                <th class="col-xs-2"><?= lang("created_by"); ?></th>
                                <th style="width:100px;"><?= lang("actions"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach($expenses as $expense){
                            ?>
                            <tr class="active">
                                <td><?php echo $expense->date; ?></td>
                                <td><?php echo $expense->reference; ?></td>
                                <td><?php echo $expense->etype; ?></td>
                                <td><?php echo $expense->warehouse_name; ?></td>
                                <td><?php echo $expense->category; ?></td>
                                <td><?php echo $expense->own_compnay; ?></td>
                                <td><?php echo $expense->pay_method; ?></td>
                                <td><?php echo $expense->amount; ?></td>
                                <td><?php echo $expense->note; ?></td>
                                <td><?php echo $expense->user; ?></td>
                                <td>
                                    <?php
                                        $detail_link = anchor('admin/purchases/expense_note/'.$expense->id, '<i class="fa fa-file-text-o"></i> ' . lang('expense_note'), 'data-toggle="modal" data-target="#myModal2"');
                                        $delete_link = anchor('admin/purchases/delete_expense/'.$expense->id, '<i class="fa fa-trash-o"></i> ' . lang('delete_expense'), 'data-toggle="modal" data-target="#myModal2"');
                                        $action = '<div class="text-center"><div class="btn-group text-left">'
                                        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
                                        . lang('actions') . ' <span class="caret"></span></button>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                        <li>' . $detail_link . '</li>
                                        <li>' . $delete_link . '</li>
                                        </ul>
                                        </div></div>';
                                        echo $action;
                                    ?>
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
        $("#EXPData").DataTable({
            "dom": 'Blfrtip',
            "buttons": [
                {
                    "extend": 'copy',
                    "exportOptions": {
                        "columns": [0,1,2,3,4,5,6,7,8,9]
                    }
                },
                {
                    "extend": 'csv',
                    "exportOptions": {
                        "columns": [0,1,2,3,4,5,6,7,8,9]
                    }
                },
                {
                    "extend": 'excel',
                    "exportOptions": {
                        "columns": [0,1,2,3,4,5,6,7,8,9]
                    }
                },
                {
                    "extend": 'pdf',
                    "exportOptions": {
                        "columns": [0,1,2,3,4,5,6,7,8,9]
                    }
                },
                {
                    "extend": 'print',
                    "exportOptions": {
                        "columns": [0,1,2,3,4,5,6,7,8,9]
                    }
                },
            ],
            "aaSorting": [[0, "desc"]],
            "lengthMenu": [
                [ 10, 25, 50, 100, -1 ],
                [ '10', '25', '50', '100', 'Show all' ]
            ]
        });
        $("#EXPData2").DataTable({
        });
    });
</script>
<?php if ($Owner) { ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>
<?php } ?>
