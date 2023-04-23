<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i>Product Ledger Report<h2>
        <div class="box-icon">
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('customize_report'); ?></p>
                <div id="">
                    <form action="<?= admin_url("reports/products_ledger"); ?>" method="get" >
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("product", "suggest_product"); ?>
                                <?php echo form_input('sproduct', (isset($_GET['sproduct']) ? $_GET['sproduct'] : ""), 'class="form-control" id="suggest_product"'); ?>
                                <input type="hidden" name="product" value="<?= isset($_GET['product']) ? $_GET['product'] : "" ?>" id="report_product_id" />
                            </div>
                        </div>
                        <?php
                            if($user_warehouses == "" || $user_warehouses == 0){
                        ?>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang("warehouse"); ?></label>
                                <?php
                                $whl[""] = lang('select') . ' ' . lang('warehouse');
                                foreach ($warehouses as $warehouse) {
                                    $whl[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $whl, (isset($_GET['warehouse']) ? $_GET['warehouse'] : ""), 'class="form-control searching_select" id="warehouse" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("warehouse") . '"');
                                ?>
                            </div>
                        </div>
                        <?php
                            }
                        ?>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', (isset($_GET['start_date']) ? $_GET['start_date'] : ""), 'class="form-control date" id="start_date" autocomplete="off"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', (isset($_GET['end_date']) ? $_GET['end_date'] : ""), 'class="form-control date" id="end_date" autocomplete="off"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    </form>

                </div>

                <div class="clearfix"></div>

                <div class="container">

                    <table id="table_id" class="table" style=" margin-top: 2%; ">
                        <thead>
                            <tr>
                                <th>Sr #</th>
                                <th>Type</th>
                                <th>Reference #</th>
                                <th>PO #</th>
                                <th>Date</th>
                                <th>Product ID</th>
                                <th>Batch #</th>
                                <th>Customer / Supplier / Warehouse</th>
                                <th>Quantity</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $data = array();
                            foreach ($rows as $val) {
                            ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <td><?= $val['type'] ?></td>
                                    <td><?= $val['ref'] ?></td>
                                    <td><?= $val['po'] ?></td>
                                    <td><?= $val['date'] ?></td>
                                    <td><?= $val['product_id'] ?></td>
                                    <td><?= $val['batch'] ?></td>
                                    <td><?= $val['customer_supplier'] ?></td>
                                    <td><?= $val['qty'] ?></td>
                                    <td><?= $val['balance'] ?></td>
                                </tr>
                            <?php $i++;
                            } ?>
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


<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#table_id').DataTable({
            pageLength: -1,
            order: [
                [0, "asc"]
            ],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>