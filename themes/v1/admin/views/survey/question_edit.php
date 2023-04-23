<style>
    .bootstrap-tagsinput {
        border: none;
        border-bottom: 1px solid #8ac53e;
        border-radius: 0;
        margin-top: 20px;
        width:100%;
    }
    .bootstrap-tagsinput .tag {
        color: white;
        padding: 3px 8px;
        border-radius: 5px;
        line-height: 30px;
        background: #8ac53e;
        margin: 4px 2px !important;
    }
</style>
<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Add Question </h3>
            </div>
            <div class="md-card-content" >
                <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'addFrom');
                    echo admin_form_open_multipart("#", $attrib);
                ?>
                    <div class="uk-grid">
                        <div class="uk-width-large-1-1">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Question <span class="red" >*</span></label>
                                <input type="hidden" name="id" value="<?php echo $question->id; ?>" >
                                <input type="text" name="question" class="md-input md-input-success label-fixed" required value="<?php echo $question->question; ?>" >
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Type <span class="red" >*</span></label>
                                <select class="uk-width-1-1 select2" name="type">
                                    <option value="1" <?php if($question->type == 1){ echo 'selected'; } ?> >Text</option>
                                    <option value="2" <?php if($question->type == 2){ echo 'selected'; } ?> >Multi Selection</option>
                                    <option value="3" <?php if($question->type == 3){ echo 'selected'; } ?> >Single Seelction</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Status <span class="red" >*</span></label>
                                <select class="uk-width-1-1 select2" name="status">
                                    <option value="1" <?php if($question->status == 1){ echo 'selected'; } ?> >Publish</option>
                                    <option value="0" <?php if($question->status == 0){ echo 'selected'; } ?> >Draft</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-1">
                            <div class="md-input-wrapper md-input-filled">
                                <label>Options</label>
                                <?php
                                    $options = "";
                                    $opts = json_decode($question->options);
                                    foreach($opts as $key => $opt){
                                        if($key > 0){
                                            $options .= ",";
                                        }
                                        $options .= $opt;
                                    }
                                ?>
                                <input type="text" value="<?php echo $options; ?>" data-role="tagsinput" name="options" class="md-input md-input-success label-fixed" />
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-large-1-1" style="padding-top: 20px;">
                            <button type="submit" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light" id="submitbtn" >Submit</button>
                            <a href="<?php echo base_url('admin/survey/questoins'); ?>" class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light" >Cancel</a>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/bootstrap-tagsinput-latest/dist/bootstrap-tagsinput.min.js"></script>
<script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/bootstrap-tagsinput-latest/dist/bootstrap-tagsinput/bootstrap-tagsinput-angular.min.js"></script>
<script>
    $(document).ready(function(){
        $('.select2').select2();
        $('#addFrom').submit(function(e){
            e.preventDefault();
            $('#submitbtn').prop('disabled', true);
            $.ajax({
                url: '<?php echo base_url('admin/survey/update_question'); ?>',
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
                        window.location.href = "<?php echo base_url('admin/survey/questions'); ?>";
                    }
                    else{
                        toastr.error(obj.message);
                        $('#submitbtn').prop('disabled', false);
                    }
                }
            });
        });

        $('.bootstrap-tagsinput input').on('keypress', function(e){
                if (e.keyCode == 13){
                    e.keyCode = 188;
                    e.preventDefault();
                };
            });

    });
</script>

