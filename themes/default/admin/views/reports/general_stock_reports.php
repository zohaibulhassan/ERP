<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script src="<?= $assets; ?>js/hc/highcharts.js"></script>


<?php if ($Owner || $Admin) { ?>
    <div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Company Wise Stock'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("purchases/add", $attrib)
                ?>


                <div class="row">
                    <div class="col-lg-12">

                        <?php if ($Owner || $Admin || !$this->session->userdata('own_companies_id')) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("own_companies", "poown_companies"); ?>
                                    <?php
                                    $oc[''] = '';
                                    foreach ($own_company as $own_companies) {
                                        $oc[$own_companies->id] = $own_companies->companyname;
                                    }
                                    echo form_dropdown('own_company', $oc, (isset($_POST['own_companies']) ? $_POST['own_companies'] : $Settings->default_warehouse), 'id="poown_companies" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("own_companies") . '" required="required" style="width:100%;" ');
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


                        <?php if ($Owner || $Admin) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="item_code">Item Code</label>
                                    <?php echo form_input('text', (isset($_POST['item_code']) ? $_POST['item_code'] : ""), 'class="form-control input-tip" id="item_code" required="required"'); ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="item_batch">Item Batch</label>
                                <?php echo form_input('item_batch', (isset($_POST['item_batch']) ? $_POST['item_batch'] : $ponumber), 'class="form-control input-tip" id="item_batch"'); ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("reference_no", "poref"); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $ponumber), 'class="form-control input-tip" id="poref"'); ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("reference_no", "poref"); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $ponumber), 'class="form-control input-tip" id="poref"'); ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("reference_no", "poref"); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $ponumber), 'class="form-control input-tip" id="poref"'); ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("reference_no", "poref"); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $ponumber), 'class="form-control input-tip" id="poref"'); ?>
                            </div>
                        </div>
                        
                        <!-- <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("status", "postatus"); ?>
                                <?php
                                $post = array('received' => lang('received'), 'pending' => lang('pending'), 'ordered' => lang('ordered'));
                                echo form_dropdown('status', $post, (isset($_POST['status']) ? $_POST['status'] : ''), 'id="postatus" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("status") . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("document", "document") ?>
                                <input id="document" type="file" data-browse-label="<?= lang('browse'); ?>" name="document" data-show-upload="false"
                                       data-show-preview="false" class="form-control file">
                            </div>
                        </div> -->


                        <!-- <?php if ($Owner || $Admin || !$this->session->userdata('own_companies_id')) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("own_companies", "poown_companies"); ?>
                                    <?php
                                    $oc[''] = '';
                                    foreach ($own_company as $own_companies) {
                                        $oc[$own_companies->id] = $own_companies->companyname;
                                    }
                                    echo form_dropdown('own_company', $oc, (isset($_POST['own_companies']) ? $_POST['own_companies'] : $Settings->default_warehouse), 'id="poown_companies" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("own_companies") . '" required="required" style="width:100%;" ');
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
                        } ?> -->
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>
