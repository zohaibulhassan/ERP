<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('companies'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open_multipart("system_settings/add_own_companies", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <?= lang('Company name', 'companyname'); ?>
                <?= form_input('companyname', '', 'class="form-control gen_slug" id="companyname" required="required"'); ?>
            </div>

            
            <div class="form-group">
                <?= lang('NTN #', 'NTN'); ?>
                <?= form_input('ntn', '', 'class="form-control" id="ntn"'); ?>
            </div>

            <div class="form-group">
                <?= lang('STRN #', 'STRN'); ?>
                <?= form_input('strn', '', 'class="form-control" id="strn"'); ?>
            </div>

            <div class="form-group all">
                <?= lang('Register Address', 'registeraddress'); ?>
                <?= form_input('registeraddress', set_value('registeraddress'), 'class="form-control tip" id="registeraddress" required="required"'); ?>
            </div>


            <div class="form-group all">
                <?= lang('Warehouse Address', 'warehouseaddress'); ?>
                <?= form_input('warehouseaddress', set_value('warehouseaddress'), 'class="form-control tip" id="warehouseaddress"'); ?>
            </div>
            
            <div class="form-group">
                <?= lang('SRB #', 'SRB'); ?>
                <?= form_input('srb', '', 'class="form-control" id="srb"'); ?>
            </div>

            <div class="form-group all">
                <?= lang('Register Person', 'registerperson'); ?>
                <?= form_input('registerperson', set_value('registerperson'), 'class="form-control tip" id="registerperson" '); ?>
            </div>

            <div class="form-group">
                <?= lang('Mobile #', 'Mobile'); ?>
                <?= form_input('mobile', '', 'class="form-control" id="mobile"'); ?>
            </div>
            <div class="form-group">
                <label>Auto Invoice Generate</label>
                <select name="autoinvoice" class="form-control">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
            <!-- <div class="form-group all">
                <?= lang('description', 'description'); ?>
                <?= form_input('description', set_value('description'), 'class="form-control tip" id="description" required="required"'); ?>
            </div> -->

            <!-- <div class="form-group">
                <?= lang("image", "image") ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
            </div> -->

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_own_companies', lang('add_own_companies'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
<script>
    $(document).ready(function() {
        $('.gen_slug').change(function(e) {
            getSlug($(this).val(), 'own_companies');
        });
    });
</script>
