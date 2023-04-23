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
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i>FBR Sale Report<?php
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

                    <?php echo admin_form_open("reports/fbr_sale_report"); ?>

                    <div class="row">




                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="form-control date" id="end_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group" style=" position: relative; top: 30px; ">
                                <div class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
                            </div>
                        </div>
                    </div>

                    <?php echo form_close(); ?>

                </div>

                <div class="clearfix"></div>

                <?php
                // echo '<pre>';
                // print_r($data);
                // echo '</pre>';

                ?>

                <div class="container">

                    <table id="table_id" class="table" style=" margin-top: 2%; ">
                        <thead>
                            <tr>
                                <th>Sr</th>
                                <th>Buyer NTN</th>
                                <th>Buyer CNIC</th>

                                <th>Buyer Name</th>
                                <th>Buyer Type</th>
                                <th>Sale Origination Province of Supplier</th>

                                <th>Document Type</th>
                                <th>Document Number</th>
                                <th>Document Date</th>

                                <th>Sale Type</th>
                                <th>Rate</th>
                                <th>Description</th>

                                <th>Quantity</th>
                                <th>UOM</th>
                                <th>Value of Sales Excluding Sales Tax</th>

                                <th>Fixed / notified value or Retail Price</th>
                                <th>Sales Tax/ FED in ST Mode</th>
                                <th>Extra Tax</th>

                                <th>ST Withheld at Source</th>
                                <th>SRO No. / Schedule No.</th>
                                <th>Item Sr. No.</th>

                                <th>Further Tax</th>
                                <th>Total Value of Sales</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php foreach ($data as $key => $item) { ?>
                                <tr>
                                    <td><?= $item['sr'] ?></td>
                                    <td><?= $item['buyer_ntn'] ?></td>
                                    <td><?= $item['buyer_cnic'] ?></td>

                                    <td><?= $item['buyer_name'] ?></td>
                                    <td><?= $item['buyer_type'] ?></td>
                                    <td><?= $item['sales_origin_province'] ?></td>

                                    <td><?= $item['document_type'] ?></td>
                                    <td><?= $item['document_number'] ?></td>
                                    <td><?= $item['date'] ?></td>

                                    <td><?= $item['sale_type'] ?></td>
                                    <td><?= $item['rate'] ?></td>
                                    <td><?= $item['description'] ?></td>

                                    <td><?= $item['quantity'] ?></td>
                                    <td><?= $item['uom'] ?></td>
                                    <td><?= $item['value_excl_tax'] ?></td>

                                    <td><?= $item['fixed_notified_val'] ?></td>
                                    <td><?= $item['sales_tax_fed'] ?></td>
                                    <td><?= $item['extra_tax'] ?></td>

                                    <td><?= $item['st_witheld'] ?></td>
                                    <td><?= $item['sr_no'] ?></td>
                                    <td><?= $item['item_sr_no'] ?></td>

                                    <td><?= $item['further_tax'] ?></td>
                                    <td><?= $item['total_Values_of_sales'] ?></td>

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
            scrollX: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            columnDefs: [
                {
                    "targets": [2],
                    "visible": false,
                },
                {
                    "targets": [4],
                    "visible": false
                },
                {
                    "targets": [5],
                    "visible": false
                },
                {
                    "targets": [6],
                    "visible": false
                },
                {
                    "targets": [10],
                    "visible": false
                },
                {
                    "targets": [13],
                    "visible": false
                },
                {
                    "targets": [17],
                    "visible": false
                },
                {
                    "targets": [18],
                    "visible": false
                },
                {
                    "targets": [19],
                    "visible": false
                },
                {
                    "targets": [20],
                    "visible": false
                },
                {
                    "targets": [22],
                    "visible": false
                },

            ]
        });
    });
</script>