<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css" media="screen">
    #PRData td:nth-child(7) {
        text-align: right;
    }

    <?php if ($Owner || $Admin || $this->session->userdata('show_cost')) { ?>#PRData td:nth-child(9) {
        text-align: right;
    }

    <?php }
    if ($Owner || $Admin || $this->session->userdata('show_price')) { ?>#PRData td:nth-child(8) {
        text-align: right;
    }

    <?php } ?>
</style>
<script>
 var oTable;
    $(document).ready(function() {
        oTable = $('#PRData').dataTable({
            "aaSorting": [
                [2, "asc"],
                [3, "asc"]
            ],
            "aLengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            "iDisplayLength": 25,
            'bProcessing': true,
            'bServerSide': true,
            'sAjaxSource': '<?= admin_url('products/getProducts' . ($warehouse_id ? '/' . $warehouse_id : '') . ($supplier ? '?supplier=' . $supplier->id : '')) ?>',
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
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[0];
                nRow.className = "product_link";
                //if(aData[7] > aData[9]){ nRow.className = "product_link warning"; } else { nRow.className = "product_link"; }
                return nRow;
            },
            "aoColumns": [
                //  {"bSortable": false, "mRender": checkbox}, {"bSortable": false,"mRender": img_hl}, null, null, null, null, null, null, null, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": formatQuantity}, null, {"bVisible": false}, {"mRender": formatQuantity}, {"bSortable": false}


                // Working Company
                //  {"bSortable": false, "mRender": checkbox}, {"bSortable": false,"mRender": img_hl}, null, null, null, null, null, null, null, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": formatQuantity}, null, {"bVisible": false}, {"mRender": formatQuantity}, {"bSortable": false}



                 {
                        "bSortable": false,
                        "mRender": checkbox
                    }, {
                        "bSortable": false,
                        "mRender": img_hl
                    },
                    null, null, null, null, null, null, null, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {
                        "mRender": formatQuantity
                    },
                    null, {"bVisible": false}, {
                        "mRender": formatQuantity,
                        "bSortable": false
                    }, {
                        "bSortable": false
                    }, {
                        "bSortable": false
                    }
                

                
                // null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null
            ]
        }).fnSetFilteringDelay().dtFilter([
            // {column_number: 2, filter_default_label: "[Code]", filter_type: "text", data: []},
            // {column_number: 3, filter_default_label: "[Name]", filter_type: "text", data: []},
            // {column_number: 4, filter_default_label: "[Brand]", filter_type: "text", data: []},
            // {column_number: 5, filter_default_label: "[Category]", filter_type: "text", data: []},
                        // {column_number: 8, filter_default_label: "[Quantity]", filter_type: "text", data: []},
            // {column_number: 9, filter_default_label: "[Unit]", filter_type: "text", data: []},
            //             // {column_number: 11, filter_default_label: "[Alert Quantity]", filter_type: "text", data: []},
        ], "footer");

    });</script>
<?php /* if ($Owner || $GP['bulk_actions']) { */
echo admin_form_open('products/product_actions' . ($warehouse_id ? '/' . $warehouse_id : ''), 'id="action-form"');
/* }*/  ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i><?= lang('products') . ' (' . ($warehouse_id ? $warehouse->name : lang('all_warehouses')) . ')' . ($supplier ? ' (' . lang('supplier') . ': ' . ($supplier->company && $supplier->company != '-' ? $supplier->company : $supplier->name) . ')' : ''); ?>
        </h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li>
                            <a href="<?= admin_url('products/add') ?>">
                                <i class="fa fa-plus-circle"></i> <?= lang('add_product') ?>
                            </a>
                        </li>
                        <?php if (!$warehouse_id) { ?>
                            <li>
                                <a href="<?= admin_url('products/update_price') ?>" data-toggle="modal" data-target="#myModal">
                                    <i class="fa fa-file-excel-o"></i> <?= lang('update_price') ?>
                                </a>
                            </li>
                        <?php } ?>
                        <li>
                            <a href="#" id="labelProducts" data-action="labels">
                                <i class="fa fa-print"></i> <?= lang('print_barcode_label') ?>
                            </a>
                        </li>
                        <li>
                            <a href="#" id="sync_quantity" data-action="sync_quantity">
                                <i class="fa fa-arrows-v"></i> <?= lang('sync_quantity') ?>
                            </a>
                        </li>
                        <li>
                            <a href="#" id="excel" data-action="export_excel">
                                <i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#" class="bpo" title="<b><?= $this->lang->line("delete_products") ?></b>" data-content="<p><?= lang('r_u_sure') ?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button>" data-html="true" data-placement="left">
                                <i class="fa fa-trash-o"></i> <?= lang('delete_products') ?>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php if (!empty($warehouses)) { ?>
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-building-o tip" data-placement="left" title="<?= lang("warehouses") ?>"></i></a>
                        <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                            <li><a href="<?= admin_url('products') ?>"><i class="fa fa-building-o"></i> <?= lang('all_warehouses') ?></a></li>
                            <li class="divider"></li>
                            <?php
                            foreach ($warehouses as $warehouse) {
                                echo '<li><a href="' . admin_url('products/' . $warehouse->id) . '"><i class="fa fa-building"></i>' . $warehouse->name . '</a></li>';
                            }
                            ?>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="PRData" class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                            <tr class="primary">
                                <th style="min-width:30px; width: 30px; text-align: center;">
                                    <input class="checkbox checkth" type="checkbox" name="check" />
                                </th>
                                <th style="min-width:40px; width: 40px; text-align: center;"><?php echo $this->lang->line("image"); ?></th>
                                <th><?= lang("code") ?></th>
                                <th><?= lang("name") ?></th>
                                <th><?= lang("brand") ?></th>
                                <th><?= lang("category") ?></th>


                                <?php if ($Owner || $Admin || $group_id_sale_show !== "9") { ?>


                                    <th><?= lang("cost") ?></th>';
                                    <th><?= lang("price") ?></th>';
                                    <?php
                                    // if ($Owner || $Admin  || $group_id_sale_show == "6") {
                                    //     echo '<th>' . lang("cost") . '</th>';
                                    //     echo '<th>' . lang("price") . '</th>';
                                    // } else {
                                    //     if ($this->session->userdata('show_cost')) {
                                    //         echo '<th>' . lang("cost") . '</th>';
                                    //     }
                                    //     if ($this->session->userdata('show_price')) {
                                    //         echo '<th>' . lang("price") . '</th>';
                                    //     }
                                    // }
                                    ?>
                                    <th><?= lang("dropship") ?></th>
                                    <th><?= lang("crossdock") ?></th>

                                <?php } ?>

                                <th><?= lang("MRP") ?></th>
                                <th><?= lang("quantity") ?></th>
                                <?php if ($group_id_sale_show !== "12") { ?>
                                    <th><?= lang("unit") ?></th>
                                    <th><?= lang("rack") ?></th>
                                    <th><?= lang("alert_quantity") ?></th>
                                    <th><?= lang("Warehouse Id") ?></th>
                                    <th style="min-width:65px; text-align:center;"><?= lang("actions") ?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                            </tr>
                        </tbody>

                        <tfoot class="dtFilter">
                            <tr class="active">
                                <th style="min-width:30px; width: 30px; text-align: center;">
                                    <input class="checkbox checkft" type="checkbox" name="check" />
                                </th>
                                <th style="min-width:40px; width: 40px; text-align: center;"><?php echo $this->lang->line("image"); ?></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <?php
                                // if ($Owner || $Admin) {
                                //     echo '<th></th>';
                                //     echo '<th></th>';
                                //     echo '<th></th>';
                                //     echo '<th></th>';
                                //     echo '<th></th>';
                                // } else {
                                //     if ($this->session->userdata('show_cost')) {
                                //         echo '<th></th>';
                                //     }
                                //     if ($this->session->userdata('show_price')) {
                                //         echo '<th></th>';
                                //     }
                                // }
                                ?>
                                <th></th>

                                <?php if ($Owner || $Admin || $group_id_sale_show !== "9") { ?>

                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>

                                <?php } ?>

                                <th></th>
                                <?php if ($group_id_sale_show !== "12") { ?>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th style="width:65px; text-align:center;"><?= lang("actions") ?></th>
                                <?php } ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if ($Owner || $GP['bulk_actions']) { ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action" />
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>
<?php } ?>