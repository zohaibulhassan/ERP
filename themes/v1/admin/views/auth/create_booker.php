<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Add Booker </h3>
            </div>
            <div class="md-card-content" >
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'addFrom');
                    echo admin_form_open_multipart("#", $attrib);
                ?>
                    <div class="uk-grid">
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>First Name <span class="red" >*</span></label>
                                <input type="text" name="first_name" class="md-input md-input-success label-fixed" required>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Last Name <span class="red" >*</span></label>
                                <input type="text" name="last_name" class="md-input md-input-success label-fixed" required>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Gender <span class="red" >*</span></label>
                                <select name="gender" class="uk-width-1-1 select2" required >
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Company Name <span class="red" >*</span></label>
                                <input type="text" name="company" class="md-input md-input-success label-fixed" required>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Phone <span class="red" >*</span></label>
                                <input type="text" name="phone" class="md-input md-input-success label-fixed" required>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Email <span class="red" >*</span></label>
                                <input type="email" name="email" class="md-input md-input-success label-fixed" required>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Username <span class="red" >*</span></label>
                                <input type="text" name="username" class="md-input md-input-success label-fixed" required>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Password <span class="red" >*</span></label>
                                <input type="password" name="password" class="md-input md-input-success label-fixed" required>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Confirm Password <span class="red" >*</span></label>
                                <input type="hidden" name="group" value="4" >
                                <input type="password" name="confirm_password" class="md-input md-input-success label-fixed" required>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Warehouse <span class="red" >*</span></label>
                                <select name="warehouse" class="uk-width-1-1 select2" required >
                                    <option value="0">All Warehouses</option>
                                    <?php
                                        foreach ($warehouses as $warehouse) {
                                            ?>
                                                <option value="<?php echo $warehouse->id; ?>"><?php echo $warehouse->name; ?></option>
                                            <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Area Manager <span class="red" >*</span></label>
                                <select name="reportperson" class="uk-width-1-1 select2" required >
                                    <?php
                                        foreach ($userslist as $userlist) {
                                            ?>
                                                <option value="<?php echo $userlist->id; ?>"><?php echo $userlist->first_name.' '.$userlist->last_name ; ?></option>
                                            <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-large-1-1" style="padding-top: 20px;">
                            <button type="submit" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light" id="submitbtn" >Submit</button>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<!-- CK Editor 5 -->
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/ckeditor5/ckeditor.js"></script>
<script>
    ClassicEditor
    .create( document.querySelector( '#editor' ),{
        toolbar: {
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ],
            shouldNotGroupWhenFull: true
        }
    })
    .then( editor => {
        window.editor = editor;
    })
    .catch( error => {
        console.error( error );
    });
</script>
<script>
    $(document).ready(function(){
        $('.select2').select2();
        $('#addFrom').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/auth/create_booker_submit'); ?>',
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
                        $('#addFrom')[0].reset();
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

