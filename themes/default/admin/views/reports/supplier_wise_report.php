<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$v = "";

if ($this->input->post('product')) {
    $v .= "&product=" . $this->input->post('product');
}

if ($this->input->post('report_typesss')) {
    $v .= "&report_typesss=" . $this->input->post('report_typesss');
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
?>
<script>
    $(document).ready(function () {
        function spb(x) {
            v = x.split('__');
            return '('+formatQuantity2(v[0])+') <strong>'+formatMoney(v[1])+'</strong>';
        }
        oTable = $('#PrRData').dataTable({
            "aaSorting": [[3, "desc"], [2, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('reports/getListReporting/?v=1'.$v) ?>',
            // 'sAjaxSource': '<?= admin_url('reports/getListReporting/?v=1'.$v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
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
        //         $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
        //     },
        //     'fnRowCallback': function (nRow, aData, iDisplayIndex) {
        //         nRow.id = aData[2];
        //         nRow.className = "product_link2";
        //         return nRow;
        //     },
        //     // "aoColumns": [null, null, {"mRender": spb}, {"mRender": spb}, {"mRender": currencyFormat}, {"mRender": spb}],
        //     "aoColumns": [null, null, null, null, null, null, null, null, null, null, null, null, null],
        //     "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
        //         var pq = 0, sq = 0, bq = 0, pa = 0, sa = 0, ba = 0, pl = 0;
        //         for (var i = 0; i < aaData.length; i++) {
        //             p = (aaData[aiDisplay[i]][1]);
        //             // s = (aaData[aiDisplay[i]][3]).split('__');
        //             // b = (aaData[aiDisplay[i]][4]).split('__');
        //             // b = (aaData[aiDisplay[i]][5]).split('__');
        //             // pq += parseFloat(p[0]);
        //             // pa += parseFloat(p[1]);0
        //             // pa += parseFloat(p[2]);
        //             // sq += parseFloat(s[2]);
        //             // sa += parseFloat(s[3]);
        //             // bq += parseFloat(b[4]);
        //             // ba += parseFloat(b[5]);
        //             // ba += parseFloat(b[6]);
        //             // ba += parseFloat(b[7]);
        //             // ba += parseFloat(b[8]);
        //             // ba += parseFloat(b[9]);
        //             // ba += parseFloat(b[10]);
        //             // ba += parseFloat(b[11]);
        //             // ba += parseFloat(b[12]);
        //             // ba += parseFloat(b[13]);
        //             // ba += parseFloat(b[14]);
        //             // pl += parseFloat(aaData[aiDisplay[i]][14]);
        //         }
        //         var nCells = nRow.getElementsByTagName('th');
        //         nCells[2].innerHTML = '<div class="text-right">('+formatQuantity2(pq)+') '+formatMoney(pa)+'</div>';
        //         // nCells[2].innerHTML = '<div class="text-right">('+formatQuantity2(pq)+') '+formatMoney(pa)+'</div>';
        //         // nCells[3].innerHTML = '<div class="text-right">('+formatQuantity2(sq)+') '+formatMoney(sa)+'</div>';
        //         // nCells[4].innerHTML = currencyFormat(parseFloat(pl));
        //         // nCells[5].innerHTML = '<div class="text-right">('+formatQuantity2(bq)+') '+formatMoney(ba)+'</div>';
        //         // nCells[6].innerHTML = '<div class="text-right">('+formatQuantity2(bq)+') '+formatMoney(ba)+'</div>';
        //         // nCells[7].innerHTML = '<div class="text-right">('+formatQuantity2(bq)+') '+formatMoney(ba)+'</div>';
        //         // nCells[8].innerHTML = '<div class="text-right">('+formatQuantity2(bq)+') '+formatMoney(ba)+'</div>';
        //         // nCells[9].innerHTML = '<div class="text-right">('+formatQuantity2(bq)+') '+formatMoney(ba)+'</div>';
        //         // nCells[10].innerHTML = '<div class="text-right">('+formatQuantity2(bq)+') '+formatMoney(ba)+'</div>';
        //         // nCells[11].innerHTML = '<div class="text-right">('+formatQuantity2(bq)+') '+formatMoney(ba)+'</div>';
        //         // nCells[12].innerHTML = '<div class="text-right">('+formatQuantity2(bq)+') '+formatMoney(ba)+'</div>';
        //         // nCells[13].innerHTML = '<div class="text-right">('+formatQuantity2(bq)+') '+formatMoney(ba)+'</div>';
        //         // nCells[14].innerHTML = '<div class="text-right">('+formatQuantity2(bq)+') '+formatMoney(ba)+'</div>';
        //     }
        // }).fnSetFilteringDelay().dtFilter([
        //     {column_number: 0, filter_default_label: "[<?=lang('product_code');?>]", filter_type: "text", data: []},
        //     {column_number: 1, filter_default_label: "[<?=lang('product_name');?>]", filter_type: "text", data: []},
        // ], "footer");
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
        $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        // $('#category').select2({allowClear: true, placeholder: "<?= lang('select'); ?>", minimumResultsForSearch: 7}).select2('destroy');
        $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
            allowClear: true,
            placeholder: "<?= lang('select_category_to_load') ?>", data: [
                {id: '', text: '<?= lang('select_category_to_load') ?>'}
            ]
        });
        $('#category').change(function () {
            var v = $(this).val();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('products/getSubCategories') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                data: scdata
                            });
                        } else {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('no_subcategory') ?>",
                                data: [{id: '', text: '<?= lang('no_subcategory') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({allowClear: true,
                    placeholder: "<?= lang('select_category_to_load') ?>",
                    data: [{id: '', text: '<?= lang('select_category_to_load') ?>'}]
                });
            }
        });
        <?php if (isset($_POST['category']) && !empty($_POST['category'])) { ?>
        $.ajax({
            type: "get", async: false,
            url: "<?= admin_url('products/getSubCategories') ?>/" + <?= $_POST['category'] ?>,
            dataType: "json",
            success: function (scdata) {
                if (scdata != null) {
                    $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({allowClear: true,
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
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i>Detail Report<?php
            if ($this->input->post('start_date')) {
                echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
            }
            ?></h2>

        <div class="box-icon">
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
        </div>
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
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>

                <div id="form">

                    <?php // echo admin_form_open("reports/detail_products_listing"); ?>
                    <?php echo admin_form_open("reports/detail_reports_supplier_wiseasd"); ?>
                    
                    <div class="row">


                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label" for="Report Type"><?= lang("Report Type"); ?></label>
                            <?php
                            $wh[""]  = 'Select Report Type';
                            $wh["1"] = 'Purchase Report';
                            $wh["2"] = 'Sales Report';
                            $wh["3"] = 'Product Report';
                            $wh["4"] = 'Purchase Payment Report';
                            $wh["5"] = 'Sales Payment Report';
                            $wh["6"] = 'Batch wise Report';
                            echo form_dropdown('report_typesss', $wh, (isset($_POST['report_typesss']) ? $_POST['report_typesss'] : ""), 'class="form-control" id="report_typesss" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("report_typesss") . '"');
                            ?>
                        </div>
                    </div>


                        <?php if ($Owner || $Admin || !$this->session->userdata('own_companies_id')) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("own_companies", "poown_companies"); ?>
                                    <?php
                                    $oc["0"] = "All";
                                    foreach ($own_company as $own_companies) {
                                        $oc[$own_companies->id] = $own_companies->companyname;
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



                        <?php if ($Owner || $Admin || !$this->session->userdata('biller_id')) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("biller", "slbiller"); ?>
                                    <?php
                                    $bl["0"] = "All";
                                    foreach ($billers as $biller) {
                                        $bl[$biller->id] = $biller->company != '-' ? $biller->company : $biller->name;
                                    }
                                    echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : $bl["0"] /*$Settings->default_biller*/ ), 'id="slbiller" data-placeholder="' . lang("select") . ' ' . lang("biller") . '" required="required" class="form-control input-tip select" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        <?php } else {
                            $biller_input = array(
                                'type' => 'hidden',
                                'name' => 'biller',
                                'id' => 'slbiller',
                                'value' => $this->session->userdata('biller_id'),
                            );

                            echo form_input($biller_input);
                        } ?>



                        <!-- <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("product", "suggest_product"); ?>
                                <?php echo form_input('sproduct', (isset($_POST['sproduct']) ? $_POST['sproduct'] : ""), 'class="form-control" id="suggest_product"'); ?>
                                <input type="hidden" name="product" value="<?= isset($_POST['product']) ? $_POST['product'] : "" ?>" id="report_product_id"/>
                            </div>
                        </div> -->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("category", "category") ?>
                                <?php
                                $cat[''] = lang('select').' '.lang('category');
                                foreach ($categories as $category) {
                                    $cat[$category->id] = $category->name;
                                }
                                echo form_dropdown('category', $cat, (isset($_POST['category']) ? $_POST['category'] : ''), 'class="form-control select" id="category" placeholder="' . lang("select") . " " . lang("category") . '" style="width:100%"')
                                ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("subcategory", "subcategory") ?>
                                <div class="controls" id="subcat_data"> <?php
                                    echo form_input('subcategory', (isset($_POST['subcategory']) ? $_POST['subcategory'] : ''), 'class="form-control" id="subcategory"  placeholder="' . lang("select_category_to_load") . '"');
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("brand", "brand") ?>
                                <?php
                                $bt[''] = lang('select').' '.lang('brand');
                                foreach ($brands as $brand) {
                                    $bt[$brand->id] = $brand->name;
                                }
                                echo form_dropdown('brand', $bt, (isset($_POST['brand']) ? $_POST['brand'] : ''), 'class="form-control select" id="brand" placeholder="' . lang("select") . " " . lang("brand") . '" style="width:100%"')
                                ?>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang("warehouse"); ?></label>
                                <?php
                                $wh[""] = lang('select').' '.lang('warehouse');
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ""), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("warehouse") . '"');
                                ?>
                            </div>
                        </div>

                        <!-- <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang('pcf1', 'cf1') ?>
                                <?= form_input('cf1', (isset($_POST['cf1']) ? $_POST['cf1'] : ''), 'class="form-control tip" id="cf1"') ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang('pcf2', 'cf2') ?>
                                <?= form_input('cf2', (isset($_POST['cf2']) ? $_POST['cf2'] : ''), 'class="form-control tip" id="cf2"') ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang('pcf3', 'cf3') ?>
                                <?= form_input('cf3', (isset($_POST['cf3']) ? $_POST['cf3'] : ''), 'class="form-control tip" id="cf3"') ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang('pcf4', 'cf4') ?>
                                <?= form_input('cf4', (isset($_POST['cf4']) ? $_POST['cf4'] : ''), 'class="form-control tip" id="cf4"') ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang('pcf5', 'cf5') ?>
                                <?= form_input('cf5', (isset($_POST['cf5']) ? $_POST['cf5'] : ''), 'class="form-control tip" id="cf5"') ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang('pcf6', 'cf6') ?>
                                <?= form_input('cf6', (isset($_POST['cf6']) ? $_POST['cf6'] : ''), 'class="form-control tip" id="cf6"') ?>
                            </div>
                        </div> -->


                        
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="form-control date" id="end_date"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>

                <div class="clearfix"></div>

                <?php if ((isset($_POST['submit_report']))) { ?>

                <div class="table-responsive">
                    <table id="PrRData"
                           class="table table-striped table-bordered table-condensed table-hover dfTable reports-table"
                           style="margin-bottom:5px;">
                        <thead>
                            
                        <tr class="active">
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Company Name</th>" : (($_POST['report_type'] == 2) ? "<th>Company Name</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Name</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>HSN Code</th>" : (($_POST['report_type'] == 2) ? "<th>HSN Code</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Price excluding Tax</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Company</th>" : (($_POST['report_type'] == 2) ? "<th>Company</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Consignment</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Date</th>" : (($_POST['report_type'] == 2) ? "<th>Date</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Dropship</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Reference No</th>" : (($_POST['report_type'] == 2) ? "<th>Reference No</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Asanbuy</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Product Name</th>" : (($_POST['report_type'] == 2) ? "<th>Product Name</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>mrp</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Quantity</th>" : (($_POST['report_type'] == 2) ? "<th>Quantity</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Quantity Balance</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Quantity Received</th>" : (($_POST['report_type'] == 2) ? "" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Expiry</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Expiry</th>" : (($_POST['report_type'] == 2) ? "<th>Expiry</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Batch</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Batch</th>" : (($_POST['report_type'] == 2) ? "<th>Batch</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Sales Incentive</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Net Unit Cost</th>" : (($_POST['report_type'] == 2) ? "<th>Net Unit Cost</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Trade Discount</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Discount</th>" : (($_POST['report_type'] == 2) ? "<th>Discount</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Consumer Discount</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Subtotal</th>" : (($_POST['report_type'] == 2) ? "<th>Subtotal</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Fed Tax</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Sales Incentive</th>" : (($_POST['report_type'] == 2) ? "<th>Sales Incentive</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Further Tax Tax</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Trade Discount</th>" : (($_POST['report_type'] == 2) ? "<th>Trade Discount</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Consumer Discount</th>" : (($_POST['report_type'] == 2) ? "<th>Consumer Discount</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Further Tax</th>" : (($_POST['report_type'] == 2) ? "<th>Further Tax</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Fed Tax</th>" : (($_POST['report_type'] == 2) ? "<th>Fed Tax</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Brand</th>" : (($_POST['report_type'] == 2) ? "<th>Brand</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "" : "Own Company"))))) ?>

                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="15" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Company Name</th>" : (($_POST['report_type'] == 2) ? "<th>Company Name</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Name</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>HSN Code</th>" : (($_POST['report_type'] == 2) ? "<th>HSN Code</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Price excluding Tax</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Company</th>" : (($_POST['report_type'] == 2) ? "<th>Company</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Consignment</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Date</th>" : (($_POST['report_type'] == 2) ? "<th>Date</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Dropship</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Reference No</th>" : (($_POST['report_type'] == 2) ? "<th>Reference No</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Asanbuy</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Product Name</th>" : (($_POST['report_type'] == 2) ? "<th>Product Name</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>mrp</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Quantity</th>" : (($_POST['report_type'] == 2) ? "<th>Quantity</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Quantity Balance</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Quantity Received</th>" : (($_POST['report_type'] == 2) ? "" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Expiry</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Expiry</th>" : (($_POST['report_type'] == 2) ? "<th>Expiry</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Batch</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Batch</th>" : (($_POST['report_type'] == 2) ? "<th>Batch</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Sales Incentive</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Net Unit Cost</th>" : (($_POST['report_type'] == 2) ? "<th>Net Unit Cost</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Trade Discount</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Discount</th>" : (($_POST['report_type'] == 2) ? "<th>Discount</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Consumer Discount</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Subtotal</th>" : (($_POST['report_type'] == 2) ? "<th>Subtotal</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Fed Tax</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Sales Incentive</th>" : (($_POST['report_type'] == 2) ? "<th>Sales Incentive</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "<th>Further Tax Tax</th>" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Trade Discount</th>" : (($_POST['report_type'] == 2) ? "<th>Trade Discount</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Consumer Discount</th>" : (($_POST['report_type'] == 2) ? "<th>Consumer Discount</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Further Tax</th>" : (($_POST['report_type'] == 2) ? "<th>Further Tax</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Fed Tax</th>" : (($_POST['report_type'] == 2) ? "<th>Fed Tax</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "" : "Own Company"))))) ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Brand</th>" : (($_POST['report_type'] == 2) ? "<th>Brand</th>" : (($_POST['report_type'] == 3) ? "Company Name 3" : (($_POST['report_type'] == 4) ? "Company Name 4" : (($_POST['report_type'] == 5) ? "Company Name 5" : (($_POST['report_type'] == 6) ? "" : "Own Company"))))) ?>
                        </tr>
                        </tfoot>
                    </table>
                </div>

                <?php } ?>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').click(function (event) {
            event.preventDefault();
            // window.location.href = "<?=admin_url('reports/getProductsReport/pdf/?v=1'.$v)?>";
            window.location.href = "<?=admin_url('reports/getListReporting/pdf/?v=1'.$v)?>";
            
            return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            // window.location.href = "<?=admin_url('reports/getProductsReport/0/xls/?v=1'.$v)?>";
            window.location.href = "<?=admin_url('reports/getListReporting/0/xls/?v=1'.$v)?>";
            return false;
        });
        $('#image').click(function (event) {
            event.preventDefault();
            html2canvas($('.box'), {
                onrendered: function (canvas) {
                    openImg(canvas.toDataURL());
                }
            });
            return false;
        });
    });
</script>
