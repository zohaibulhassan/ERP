<style>
    .table thead tr th {
        text-align: left !important;
    } 
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i>Creadit Limits Report</h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12" id="headerdiv">
                <p class="introtext">
                    <?= lang('customize_report'); ?>
                </p>
                <div>
                    <form action="<?= admin_url('reports/creadits') ?>" method="get">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("suppliers", "suppliers"); ?>
                                    <?php
                                    $bl["all"] = "All";
                                    foreach ($suppliers as $supplier) {
                                        $bl[$supplier->id] = $supplier->name;
                                    }
                                    echo form_dropdown('supplier', $bl, $ssupplier, 'id="supplier" data-placeholder="' . lang("select") . ' ' . lang("supplier") . '" required="required" class="form-control input-tip select2" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("customers", "customers"); ?>
                                    <?php
                                    $bl["all"] = "All";
                                    foreach ($customers as $customer) {
                                        $bl[$customer->id] = $customer->name;
                                    }
                                    echo form_dropdown('customer', $bl, $scustomer, 'id="customer" data-placeholder="' . lang("select") . ' ' . lang("cuctomer") . '" required="required" class="form-control input-tip select2" style="width:100%;"');
                                    ?>
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
                <table class="table tabledb">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Supplier</th>
                            <th>Creadit Limit</th>
                            <th>Due Amount</th>
                            <th>Available Limit</th>
                            <th>Testing</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($rows as $row){
                        ?>
                        <tr>
                            <td><?= $row->cname ?></td>
                            <td><?= $row->sname ?></td>
                            <td><?= $this->sma->formatMoney($row->creadit_limit) ?></td>
                            <td>
                                <?= $this->sma->formatMoney($row->due_amount) ?>
                                <a href="<?= admin_url('reports/due_invoices?supplier='.$row->supplier_id.'&customer='.$row->customer_id) ?>" style="display: block;width: 29px;float: right;">
                                    <i class="fa fa-list-ul tip" data-placement="left" style="margin-left: 6px;" title="Due Invoices"></i>
                                </a>
                            </td>
                            <td><?php
                                $available = $row->creadit_limit-$row->due_amount;
                                if($available < 0){
                                    echo '<span style="color:red;" >'.$this->sma->formatMoney($available).'</span>';
                                }
                                else {
                                    echo $this->sma->formatMoney($available);
                                }
                            ?></td>
                            <td><?= $row->testing_date ?></td>
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
    $(document).ready(function(e){
        $('.select2').select2();
    });
</script>


