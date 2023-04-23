<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_own_companies'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open_multipart("system_settings/edit_own_companies/" . $own_companies->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('update_info'); ?></p>


             <div class="form-group">
                <?= lang('Company name', 'companyname'); ?>
                <?= form_input('companyname', $own_companies->companyname, 'class="form-control gen_slug" id="companyname" required="required"'); ?>
            </div>

            
            <div class="form-group">
                <?= lang('NTN #', 'NTN'); ?>
                <?= form_input('ntn', $own_companies->ntn, 'class="form-control" id="ntn"'); ?>
            </div>

            <div class="form-group">
                <?= lang('STRN #', 'STRN'); ?>
                <?= form_input('strn', $own_companies->strn, 'class="form-control" id="strn"'); ?>
            </div>

            <div class="form-group all">
                <?= lang('Register Address', 'registeraddress'); ?>
                <?= form_input('registeraddress', $own_companies->registeraddress, 'class="form-control tip" id="registeraddress" required="required"'); ?>
            </div>


            <div class="form-group all">
                <?= lang('Warehouse Address', 'warehouseaddress'); ?>
                <?= form_input('warehouseaddress', $own_companies->warehouseaddress, 'class="form-control tip" id="warehouseaddress"'); ?>
            </div>
            
            <div class="form-group">
                <?= lang('SRB #', 'SRB'); ?>
                <?= form_input('srb', $own_companies->srb, 'class="form-control" id="srb"'); ?>
            </div>

            <div class="form-group all">
                <?= lang('Register Person', 'registerperson'); ?>
                <?= form_input('registerperson', $own_companies->registerperson, 'class="form-control tip" id="registerperson" '); ?>
            </div>

            <div class="form-group">
                <?= lang('Mobile #', 'Mobile'); ?>
                <?= form_input('mobile', $own_companies->mobile, 'class="form-control" id="mobile"'); ?>
            </div>
            <div class="form-group">
                <label>Auto Invoice Generate</label>
                <select name="autoinvoice" class="form-control">
                    <option value="0" <?php if($own_companies->auto_invoice_gen == 0){ echo 'selected'; } ?> >No</option>
                    <option value="1" <?php if($own_companies->auto_invoice_gen == 1){ echo 'selected'; } ?> >Yes</option>
                </select>
            </div>






            <!-- <div class="form-group">
                <?= lang('code', 'code'); ?>
                <?= form_input('code', $own_companies->companyname, 'class="form-control" id="code"'); ?>
            </div>

            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?= form_input('name', $own_companies->name, 'class="form-control gen_slug" id="name" required="required"'); ?>
            </div>

            <div class="form-group all">
                <?= lang('slug', 'slug'); ?>
                <?= form_input('slug', set_value('slug', $own_companies->slug), 'class="form-control tip" id="slug" required="required"'); ?>
            </div>

            <div class="form-group all">
                <?= lang('description', 'description'); ?>
                <?= form_input('description', set_value('description', $own_companies->description), 'class="form-control tip" id="description" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang("image", "image") ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false"
                       class="form-control file">
            </div> -->
            <?php echo form_hidden('id', $own_companies->id); ?>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_own_companies', lang('edit_own_companies'), 'class="btn btn-primary"'); ?>
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
