<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$v = "";

if ($this->input->post('product')) {
    $v .= "&product=" . $this->input->post('product');
}

if ($this->input->post('report_type')) {
    $v .= "&report_type=" . $this->input->post('report_type');
}

if ($this->input->post('own_company')) {
    $v .= "&own_company=" . $this->input->post('own_company');
}

if ($this->input->post('biller')) {
    $v .= "&biller=" . $this->input->post('biller');
}

if ($this->input->post('category')) {
    $v .= "&category=" . $this->input->post('category');
}
if ($this->input->post('brand')) {
    $v .= "&brand=" . $this->input->post('brand');
}
if ($this->input->post('subcategory')) {
    $v .= "&subcategory=" . $this->input->post('subcategory');
}
if ($this->input->post('warehouse')) {
    $v .= "&warehouse=" . $this->input->post('warehouse');
}
if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}
if ($this->input->post('cf1')) {
    $v .= "&cf1=" . $this->input->post('cf1');
}
if ($this->input->post('cf2')) {
    $v .= "&cf2=" . $this->input->post('cf2');
}
if ($this->input->post('cf3')) {
    $v .= "&cf3=" . $this->input->post('cf3');
}
if ($this->input->post('cf4')) {
    $v .= "&cf4=" . $this->input->post('cf4');
}
if ($this->input->post('cf5')) {
    $v .= "&cf5=" . $this->input->post('cf5');
}
if ($this->input->post('cf6')) {
    $v .= "&cf6=" . $this->input->post('cf6');
}
// echo '<pre>';
// print_r($v);
// echo '</pre>';
//die;
?>
<script>
    $(document).ready(function() {
        function spb(x) {
            v = x.split('__');
            return '(' + formatQuantity2(v[0]) + ') <strong>' + formatMoney(v[1]) + '</strong>';
        }
        oTable = $('#PrRData').dataTable({
            "aaSorting": [
                [3, "desc"],
                [2, "desc"]
            ],
            "aLengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true,
            'bServerSide': true,
            'sAjaxSource': '<?= admin_url('reports/getListReporting/?v=1' . $v) ?>',
            //'sAjaxSource': '<?= admin_url('reports/getListReporting/?v=1&report_type=7') ?>',
            'fnServerData': function(sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            },
            // 'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            //     nRow.id = aData[9];
            //     nRow.className = (aData[5] > 0) ? "invoice_link2" : "invoice_link2 warning";
            //     return nRow;
            // },
            // "aoColumns": [null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null],
            // "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
            //     var gtotal = 0, paid = 0, balance = 0;
            //     for (var i = 0; i < aaData.length; i++) {
            //     }
            //     var nCells = nRow.getElementsByTagName('th');
            // }
        });

    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#form').hide();
        $('.toggle_down').click(function() {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function() {
            $("#form").slideUp();
            return false;
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        // $('#category').select2({allowClear: true, placeholder: "<?= lang('select'); ?>", minimumResultsForSearch: 7}).select2('destroy');
        $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
            allowClear: true,
            placeholder: "<?= lang('select_category_to_load') ?>",
            data: [{
                id: '',
                text: '<?= lang('select_category_to_load') ?>'
            }]
        });
        $('#category').change(function() {
            var v = $(this).val();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('products/getSubCategories') ?>/" + v,
                    dataType: "json",
                    success: function(scdata) {
                        if (scdata != null) {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                                allowClear: true,
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                data: scdata
                            });
                        } else {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({
                                allowClear: true,
                                placeholder: "<?= lang('no_subcategory') ?>",
                                data: [{
                                    id: '',
                                    text: '<?= lang('no_subcategory') ?>'
                                }]
                            });
                        }
                    },
                    error: function() {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
                    allowClear: true,
                    placeholder: "<?= lang('select_category_to_load') ?>",
                    data: [{
                        id: '',
                        text: '<?= lang('select_category_to_load') ?>'
                    }]
                });
            }
        });
        <?php if (isset($_POST['category']) && !empty($_POST['category'])) { ?>
            $.ajax({
                type: "get",
                async: false,
                url: "<?= admin_url('products/getSubCategories') ?>/" + <?= $_POST['category'] ?>,
                dataType: "json",
                success: function(scdata) {
                    if (scdata != null) {
                        $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                            allowClear: true,
                            placeholder: "<?= lang('no_subcategory') ?>",
                            data: scdata
                        });
                    }
                }
            });
        <?php } ?>
    });
</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i>Product Ledger Report<?php
                                                                                    if ($this->input->post('start_date')) {
                                                                                        echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
                                                                                    }
                                                                                    ?></h2>

        <!-- <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="#" class="toggle_up tip" title="<?= lang('hide_form') ?>">
                        <i class="icon fa fa-toggle-up"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" class="toggle_down tip" title="<?= lang('show_form') ?>">
                        <i class="icon fa fa-toggle-down"></i>
                    </a>
                </li>
            </ul>
        </div> -->
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="#" id="xls" class="tip" title="<?= lang('download_xls') ?>">
                        <i class="icon fa fa-file-excel-o"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" id="image" class="tip" title="<?= lang('save_image') ?>">
                        <i class="icon fa fa-file-picture-o"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <?php
    // echo '<pre>';
    // print_r($data);
    // echo '</pre>';
    ?>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>

                <div id="">

                    <?php // echo admin_form_open("reports/detail_products_listing"); 
                    ?>
                    <?php echo admin_form_open("reports/product_ledger_report"); ?>

                    <div class="row">


                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control date" id="start_date" required'); ?>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="form-control date" id="end_date" required'); ?>
                            </div>
                        </div>


                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("product", "suggest_product"); ?>
                                <?php echo form_input('sproduct', (isset($_POST['sproduct']) ? $_POST['sproduct'] : ""), 'class="form-control" id="suggest_product"'); ?>
                                <input type="hidden" name="product" value="<?= isset($_POST['product']) ? $_POST['product'] : "" ?>" id="report_product_id" />
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang("warehouse"); ?></label>
                                <?php
                                $whl[""] = lang('select') . ' ' . lang('warehouse');
                                foreach ($warehouses as $warehouse) {
                                    $whl[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $whl, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ""), 'class="form-control" required id="warehouse" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("warehouse") . '"');
                                ?>
                            </div>
                        </div>


                    </div>
                    <div class="form-group">
                        <div class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>

                <div class="clearfix"></div>

                <div class="container">
                    <div align="center" class="heading">

                        <h4><strong>Purchase Qty = <?= $data2[0]['total_purchase_quantity'] + $data2[2]['addition_quantity'] ?>, Sales Qty = <?= $data2[1]['total_sale_quantity'] ?>, Balance = <?= $data2[0]['total_purchase_quantity'] + $data2[2]['addition_quantity'] - $data2[1]['total_sale_quantity'] ?></strong></h4>
                    </div>

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
                                <th>Customer / Supplier</th>
                                <th>Quantity</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($data as $key => $val) {
                                $last = $data2[0]['total_purchase_quantity'] + $data2[2]['addition_quantity'] - $data2[1]['total_sale_quantity'];

                                if ($i == 1) {
                                    //$new_val = $last + (-$val['quantity']);

                                    if ($val['type_of'] === 'Sale') {
                                        $new_val_3 = $last + (-$val['quantity']);
                                    } else {
                                        $new_val_3 = $last + $val['quantity'];
                                    }

                                    $new_val = $new_val_3;
                                } else {
                                    if ($val['type_of'] === 'Sale') {
                                        $new_val_2 = $new_val + (-$val['quantity']);
                                    } else {
                                        $new_val_2 = $new_val + $val['quantity'];
                                    }

                                    $new_val = $new_val_2;
                                }


                            ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <td><?= $val['type_of'] ?></td>
                                    <td><?= $val['invoice_no'] ?></td>
                                    <td><?= $val['po_number'] ?></td>
                                    <td><?= $val['ddate'] ?></td>
                                    <td><?= $val['product_id'] ?></td>
                                    <td><?= $val['batch'] ?></td>
                                    <td><?= $val['customer_supplier'] ?></td>
                                    <td><?= $val['quantity'] ?></td>
                                    <td><?= $new_val ?></td>
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
            order: [
                [1, "asc"]
            ],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>