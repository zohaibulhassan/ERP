<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Add Bulk Discount'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'stForm');
                echo admin_form_open_multipart("system_settings/add_bulk_discount", $attrib);
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <?php /* if ($Owner || $Admin || !$this->session->userdata('warehouse_id')) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("warehouse", "warehouse"); ?>
                                    <?php
                                    $wh[''] = '';
                                    foreach ($warehouses as $warehouse) {
                                        $wh[$warehouse->id] = $warehouse->name;
                                    }
                                    echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $Settings->default_warehouse), 'id="warehouse" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("warehouse") . '" required="required" style="width:100%;" ');
                                    ?>
                                </div>
                            </div>
                        <?php } else {
                            $warehouse_input = array(
                                'type' => 'hidden',
                                'name' => 'warehouse',
                                'id' => 'warehouse',
                                'value' => $this->session->userdata('warehouse_id'),
                                );

                            echo form_input($warehouse_input);
                        } ?>

                        <?php if ($Owner || $Admin) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("date", "date"); ?>
                                    <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : $this->sma->hrld(date('Y-m-d'))), 'class="form-control input-tip " id="date" required="required"'); ?>
                                </div>
                            </div>
                        <?php } */?>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("Discount Name", "dis_name"); ?>
                                <?php echo form_input('discount_name', (isset($_POST['discount_name']) ? $_POST['discount_name'] : ''), 'class="form-control input-tip" id="dis_name"'); ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("Discount Code", "dis_code"); ?>
                                <?php echo form_input('discount_code', (isset($_POST['discount_code']) ? $_POST['discount_code'] : ''), 'class="form-control input-tip" id="dis_code"'); ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("Percentage", "per"); ?>
                                <?php echo form_input('percentage', (isset($_POST['percentage']) ? $_POST['percentage'] : ''), 'class="form-control input-tip" id="per"'); ?>
                            </div>
                        </div>

                        <div class="clearfix"></div>




                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("Supplier", "Supplier"); ?>
                                <?php
                                foreach ($supplier as $supplier) {
                                    $wh[$supplier->id] = $supplier->name;
                                }
                                echo form_dropdown('supplier[]', $wh, (isset($_POST['supplier']) ? $_POST['supplier'] : 0), 'id="supplier" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("supplier") . '" style="width:100%;" multiple');
                                ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("brands", "brand"); ?>
                                <?php
                                foreach ($brands as $brand) {
                                    $whs[$brand->id] = $brand->name;
                                }
                                echo form_dropdown('brand[]', $whs, (isset($_POST['brand']) ? $_POST['brand'] : 0), 'id="brand" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("brand") . '" style="width:100%;" multiple');
                                ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("Product", "Product"); ?>
                                <?php
                                foreach ($products as $product) {
                                    $whss[$product->id] = $product->name;
                                }
                                echo form_dropdown('product[]', $whss, (isset($_POST['product']) ? $_POST['product'] : 0), 'id="product" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("product") . '" style="width:100%;" multiple');
                                ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('Start Date', 'start_date'); ?>
                                <?= form_input('start_date', set_value('start_date'), 'class="form-control tip date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('End Date', 'end_date'); ?>
                                <?= form_input('end_date', set_value('end_date'), 'class="form-control tip date" id="end_date"'); ?>
                            </div>
                        </div>



                        <!-- <div class="col-md-12">
                            <label><?= lang("type"); ?> *</label>
                            <div class="form-group">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-6 col-sm-2">
                                                <input type="radio" class="checkbox type" value="full" name="type" id="full" <?= $this->input->post('type') ? 'checked="checked"' : ''; ?> required="required">
                                                <label for="full" class="padding05">
                                                    <?= lang('full'); ?>
                                                </label>
                                            </div>
                                            <div class="col-xs-6 col-sm-2">
                                                <input type="radio" class="checkbox type" value="partial" name="type" id="partial" <?= $this->input->post('type') ? 'checked="checked"' : ''; ?>>
                                                <label for="partial" class="padding05">
                                                    <?= lang('partial'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                        <div class="clearfix"></div>
                        <div class="col-md-12 partials" style="display:none;">
                        <div class="well well-sm">
                                
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-md-12">
                            <div class="fprom-group">
                                <?= form_submit('count_stock', lang("submit"), 'id="count_stock" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#brand option[value=''], #category option[value='']").remove();
        $('.type').on('ifChecked', function(e){
            var type_opt = $(this).val();
            if (type_opt == 'partial')
                $('.partials').slideDown();
            else
                $('.partials').slideUp();
            $('#stForm').bootstrapValidator('revalidateField', $(this));
        });
        $(".date").datetimepicker({format: site.dateFormats.js_ldate, fontAwesome: true, language: 'sma', weekStart: 1, todayBtn: 1, autoclose: 1, todayHighlight: 1, startView: 2, forceParse: 0, startDate: "<?= $this->sma->hrld(date('Y-m-d')); ?>"});
        
    });
</script>