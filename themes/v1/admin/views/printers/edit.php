<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Edit Printer </h3>
            </div>
            <div class="md-card-content" >
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'editFrom');
                    echo admin_form_open_multipart("#", $attrib);
                ?>

                    <div class="uk-grid">
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Title <span class="red" >*</span></label>
                                <input type="text" name="title" class="md-input md-input-success label-fixed" required value="<?php echo $printer->title; ?>" >
                                <input type="hidden" name="id" value="<?php echo $printer->id; ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Type <span class="red" >*</span></label><br>
                                <select name="type" class="uk-width-1-1 select2">
                                    <option value="network" <?php if($printer->type == "network" ){ echo 'selected'; } ?> >Network</option>
                                    <option value="windows" <?php if($printer->type == "windows" ){ echo 'selected'; } ?> >Windows</option>
                                    <option value="linux" <?php if($printer->type == "linux" ){ echo 'selected'; } ?> >Linux</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Profile <span class="red" >*</span></label><br>
                                <select name="profile" class="uk-width-1-1 select2">
                                    <option value="default" <?php if($printer->profile == "default" ){ echo 'selected'; } ?> >Default</option>
                                    <option value="windows" <?php if($printer->profile == "windows" ){ echo 'selected'; } ?> >Simple</option>
                                    <option value="SP2000" <?php if($printer->profile == "SP2000" ){ echo 'selected'; } ?> >Star-branded</option>
                                    <option value="TEP-200M" <?php if($printer->profile == "TEP-200M" ){ echo 'selected'; } ?> >Espon Tep</option>
                                    <option value="P822D" <?php if($printer->profile == "P822D" ){ echo 'selected'; } ?> >P822D</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Characters per line <span class="red" >*</span></label>
                                <input type="number" name="charline" class="md-input md-input-success label-fixed" required value="<?php echo $printer->char_per_line; ?>">
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Path (If Printer Type Window/Linux) </label>
                                <input type="text" name="path" class="md-input md-input-success label-fixed" value="<?php echo $printer->path; ?>">
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>IP Address (If Printer Type Network) </label>
                                <input type="text" name="ipaddress" class="md-input md-input-success label-fixed" value="<?php echo $printer->ip_address; ?>">
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Port (If Printer Type Network) </label>
                                <input type="number" name="port" class="md-input md-input-success label-fixed" value="<?php echo $printer->port; ?>">
                                <span>Most printers are open on port 9100</span>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>User <span class="red" >*</span></label><br>
                                <select name="user" class="uk-width-1-1 select2">
                                    <?php
                                        foreach($printer_users as $printer_user){
                                            echo '<option value="'.$printer_user->id.'" ';
                                            if($printer_user->id == $printer->user_id){
                                                echo 'selected';
                                            }
                                            echo ' >'.$printer_user->first_name.' '.$printer_user->last_name.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-large-1-1" style="padding-top: 20px;">
                            <button type="submit" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light" id="submitbtn" >Submit</button>
                            <a href="<?php echo base_url('admin/printers'); ?>" class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light" >Cancel</a>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('.select2').select2();
        $('#editFrom').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/printers/update'); ?>',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    console.log(obj);
                    if(obj.status){
                        toastr.success(obj.message);
                        window.location.href = "<?php echo base_url('admin/printers'); ?>";
                    }
                    else{
                        toastr.error(obj.message);
                        $('#submitbtn').prop('disabled', false);
                    }
                }
            });
        });
    });
</script>

