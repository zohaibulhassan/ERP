
<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Add Route </h3>
            </div>
            <div class="md-card-content" >
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'addFrom');
                    echo admin_form_open_multipart("#", $attrib);
                ?>
                    <div class="uk-grid">
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Title <span class="red" >*</span></label>
                                <input type="text" name="title" class="md-input md-input-success label-fixed" required>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Type <span class="red" >*</span></label><br>
                                <select name="type" class="uk-width-1-1 select2">
                                    <option value="network">Network</option>
                                    <option value="windows">Windows</option>
                                    <option value="linux">Linux</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Profile <span class="red" >*</span></label><br>
                                <select name="profile" class="uk-width-1-1 select2">
                                    <option value="default">Default</option>
                                    <option value="windows">Simple</option>
                                    <option value="SP2000">Star-branded</option>
                                    <option value="TEP-200M">Espon Tep</option>
                                    <option value="P822D">P822D</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Characters per line <span class="red" >*</span></label>
                                <input type="number" name="charline" class="md-input md-input-success label-fixed" required>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Path (If Printer Type Window/Linux) </label>
                                <input type="text" name="path" class="md-input md-input-success label-fixed">
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>IP Address (If Printer Type Network) </label>
                                <input type="text" name="ipaddress" class="md-input md-input-success label-fixed">
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Port (If Printer Type Network) </label>
                                <input type="number" name="port" class="md-input md-input-success label-fixed">
                                <span>Most printers are open on port 9100</span>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="md-input-wrapper md-input-filled">
                                <label>User <span class="red" >*</span></label><br>
                                <select name="user" class="uk-width-1-1 select2">
                                    <?php
                                        foreach($printer_users as $printer_user){
                                            echo '<option value="'.$printer_user->id.'">'.$printer_user->first_name.' '.$printer_user->last_name.'</option>';
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
        $('#addFrom').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/printers/create'); ?>',
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

