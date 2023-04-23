<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>


<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('sales_report'); ?> <?php
                                                                                        if ($this->input->post('start_date')) {
                                                                                            echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
                                                                                        }
                                                                                        ?>
        </h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="#" id="xls" class="tip" title="<?= lang('download_xls') ?>">
                        <i class="icon fa fa-file-excel-o"></i>
                    </a>
                </li>

            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <?php

                $userId = $_SESSION['user_id'];

                $biller = $this->db->query("select biller_id from sma_users where id = '$userId'")->result_array();

                $biller_id =  $biller[0]['biller_id'];

                $qry1 = "SELECT t2.product_name, t1.date, t1.id, t1.reference_no,  'RhoCom' AS biller, t1.customer, t1.grand_total FROM `sma_sales` AS t1  LEFT JOIN `sma_sale_items` AS t2  ON t1.id = t2.sale_id  WHERE t1.supplier_id = '$biller_id' GROUP BY t2.sale_id order by t1.id desc";

                $getDataQ1 = $this->db->query($qry1)->result_array();

                // $qry1 = "SELECT t1.reference_no,  'RhoCom' AS biller, t1.customer, t1.grand_total FROM `sma_sales` AS t1  LEFT JOIN `sma_sale_items` AS t2  ON t1.id = t2.sale_id  WHERE supplier_id = '$biller_id'";
                // $get_sale_id_products = $this->db->query($qry)->result_array();
                $prod = [];



                ?>
                <div class="container">
                    <table id="salesReport" class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Refrence No</th>
                                <th>Biller</th>
                                <th>Customer</th>
                                <th>Products (Qty)</th>
                                <th>Grand Totaal</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php

                            foreach ($getDataQ1 as $key => $val) {
                                $id = $val['id'];
                                $q2 = "Select product_name, round(quantity) as quanitity from sma_sale_items where sale_id = '$id'";
                                $getDataQ2 = $this->db->query($q2)->result_array();


                            ?>
                                <tr>
                                    <td><?= $val['date'] ?></td>
                                    <td><?= $val['reference_no'] ?></td>
                                    <td><?= $val['biller'] ?></td>
                                    <td><?= $val['customer'] ?></td>
                                    <td>
                                        <?php

                                        foreach ($getDataQ2 as $key => $val2) {

                                            echo '<p>' . $val2['product_name'] . ' (' . $val2['quanitity'] . ' pcs)' . '</p>';
                                        }

                                        ?>
                                    </td>
                                    <td><?= $val['grand_total'] ?></td>
                                </tr>
                            <?php } ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-flash-1.6.4/b-html5-1.6.4/b-print-1.6.4/sb-1.0.0/datatables.min.css" />

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-flash-1.6.4/b-html5-1.6.4/b-print-1.6.4/sb-1.0.0/datatables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#salesReport').DataTable({
            "order": [
                [0, "desc"]
            ],
            "stateSave": true,
            "dom": 'Bfrtip',
            "buttons": [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ]
        });
    });
</script>