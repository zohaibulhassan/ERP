<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$v = "";

if ($this->input->post('report_type')) {
    $v .= "&report_type=" . $this->input->post('report_type');
}

if ($this->input->post('buyer_or_supplier')) {
    $v .= "&buyer_or_supplier=" . $this->input->post('buyer_or_supplier');
}

if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}

?>
<script>
    $(document).ready(function () {
        function spb(x) {
            v = x.split('__');
            return '('+formatQuantity2(v[0])+') <strong>'+formatMoney(v[1])+'</strong>';
        }
        oTable = $('#PrRData').dataTable({
            "aaSorting": [[4, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('reports/getListReportingBestSeller/?v=1'.$v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
        });
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
        $("#supplier").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
            allowClear: true,
            placeholder: "<?= lang('select_category_to_load') ?>", data: [
                {id: '', text: '<?= lang('select_category_to_load') ?>'}
            ]
        });
        $('#report_type').change(function () {
            var v = $(this).val();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('reports/getList') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $("#supplier").select2("destroy").empty().attr("placeholder", "<?= lang('select_supplier') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                data: scdata
                            });
                        } else {
                            $("#supplier").select2("destroy").empty().attr("placeholder", "<?= lang('no_supplier') ?>").select2({allowClear: true,
                                placeholder: "<?= lang('no_supplier') ?>",
                                data: [{id: '', text: '<?= lang('no_supplier') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $("#supplier").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({allowClear: true,
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
                    $("#supplier").select2("destroy").empty().attr("placeholder", "<?= lang('select_supplier') ?>").select2({allowClear: true,
                        placeholder: "<?= lang('no_supplier') ?>",
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
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i>Best Seller<?php
            if ($this->input->post('start_date')) {
                echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
            }
            ?></h2>

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

                <p class="introtext"><?= lang('customize_report'); ?></p>

                <div id="" style="display: block !important;">

                    <?php echo admin_form_open("reports/custom_best_sellers"); ?>
                    
                    <div class="row">


                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label" for="Report Type"><?= lang("Report Type"); ?></label>
                            <?php
                            $wh[""]  = 'Select Report Type';
                            $wh["1"] = 'Brand Wise';
                            $wh["2"] = 'Buyer Wise';
                            // $wh["3"] = 'Supplier Wise';
                            echo form_dropdown('report_type', $wh, (isset($_POST['report_type']) ? $_POST['report_type'] : ""), 'class="form-control" id="report_type" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("report_type") . '"');
                            ?>
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="form-group">
                            <!-- <?= lang("supplier", "supplier") ?> -->
                            <label class="control-label" for="Report Type">&nbsp;</label>
                            <div class="controls" id="subcat_data"> <?php
                                echo form_input('buyer_or_supplier', (isset($_POST['supplier']) ? $_POST['supplier'] : ''), 'class="form-control" id="supplier"  placeholder="' . lang("select_category_to_load") . '"');
                                ?>
                            </div>
                        </div>
                    </div>

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
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Date</th>" : (($_POST['report_type'] == 2) ? "<th>Product Name</th>" : ""); ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Own Company</th>" : (($_POST['report_type'] == 2) ? "<th>Quantity</th>" : ""); ?>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="15" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Date</th>" : (($_POST['report_type'] == 2) ? "<th>Product Name</th>" : ""); ?>
                        <?php echo ($_POST['report_type'] == 1) ? "<th>Reference No</th>" : (($_POST['report_type'] == 2) ? "<th>Quantity</th>" : ""); ?>
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
            window.location.href = "<?=admin_url('reports/getListReportingBestSeller/pdf/?v=1'.$v)?>";
            
            return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            // window.location.href = "<?=admin_url('reports/getProductsReport/0/xls/?v=1'.$v)?>";
            window.location.href = "<?=admin_url('reports/getListReportingBestSeller/0/xls/?v=1'.$v)?>";
            return false;
        });
    });
</script>
