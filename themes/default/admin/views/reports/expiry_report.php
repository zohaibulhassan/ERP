<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$v = "";

if ($this->input->post('own_company')) {
    $v .= "&own_company=" . $this->input->post('own_company');
}


if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}


?>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i>Expiry Report<?php
                                                                            if ($this->input->post('start_date')) {
                                                                                echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
                                                                            }
                                                                            ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>

                <div id="">

                    <?php // echo admin_form_open("reports/expiry_reports"); 
                    ?>
                    <?php echo admin_form_open("reports/expiry_reports"); ?>

                    <div class="row">

                        <?php if ($Owner || $Admin || !$this->session->userdata('own_companies_id')) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("own_companies", "poown_companies"); ?>
                                    <?php
                                    $oc["0"] = "All";
                                    foreach ($own_company as $own_companies) {
                                        $oc[$own_companies->id] = $own_companies->name;
                                    }
                                    echo form_dropdown('own_company', $oc, (isset($_POST['own_companies']) ? $_POST['own_companies'] : $oc["0"] /*$Settings->default_warehouse*/), 'id="poown_companies" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("own_companies") . '" required="required" style="width:100%;" ');
                                    ?>
                                </div>
                            </div>
                        <?php } else {
                            $own_companies_input = array(
                                'type' => 'hidden',
                                'name' => 'own_companies',
                                'id' => 'slown_companies',
                                'value' => $this->session->userdata('own_companies_id'),
                            );

                            echo form_input($own_companies_input);
                        } ?>


                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group" style=" position: relative; top: 30px; ">
                                <div class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
                            </div>
                        </div>
                    </div>

                    <?php echo form_close(); ?>

                </div>

                <div class="clearfix"></div>

                <?php

               // print_r($data);

                ?>

                <div class="container">

                    <table id="table_id" class="table" style=" margin-top: 2%; ">
                        <thead>
                            <tr>
                                <th> ID</th>
                                <th>Name</th>
                                <th>Batch</th>
                                <th>Qty Balance</th>
                                <th>MRP</th>
                                <th>Price</th>
                                <th>Expiry</th>
                                <th>Near to Expiry</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            
                            foreach ($data as $key => $val) { ?>
                        
                            <tr>
                                <td><?= $val['product_id'] ?></td>
                                <td><?= $val['product_name'] ?></td>
                                <td><?= $val['batch'] ?></td>
                                <td><?= $val['quantity_balance'] ?></td>
                                <td><?= $val['mrp'] ?></td>
                                <td><?= $val['price'] ?></td>
                                <td><?= $val['expiry_date'] ?></td>
                                <td><?= $val['expiry_in_days'] ?></td>
                        
                            </tr>

                            <?php } ?>
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
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>