<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Open Regiser </h3>
            </div>
            <div class="md-card-content" >
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'productFrom');
                    echo admin_form_open_multipart("pos/open_register", $attrib);
                ?>
                    <div class="uk-grid">
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Cash in Hand <span class="red" >*</span></label>
                                <input type="number" name="cash_in_hand" class="md-input md-input-success label-fixed" required>
                            </div>
                        </div>
                        <div class="uk-width-large-1-1" style="padding-top: 20px;">
                            <button type="submit" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light" id="submitbtn" >Submit</button>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
