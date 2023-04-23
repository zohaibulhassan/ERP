

   <link href="<?= $assets ?>plugins/full_calender/main.css" rel="stylesheet" type="text/css" />
   <link href="<?= $assets ?>plugins/full_calender/grid_main.css" rel="stylesheet" type="text/css" />

<div id="page_content">
    <div id="page_content_inner">
        <div class="md-card">
            <div class="md-card-toolbar">
                <h3 class="md-card-toolbar-heading-text">Pop Journey</h3>
                <div class="md-card-toolbar-actions">
                    <i class="md-icon material-icons md-card-fullscreen-activate"></i>
                </div>
            </div>
            <div class="md-card-content">
                <div id="dates-calendar" class="dates-calendar"></div>
            </div>
        </div>
    </div>
</div>

<div class="uk-modal" id="task_modal">
    <?php
        $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'addFrom', "data-action" => '');
        echo admin_form_open_multipart("#", $attrib);
    ?>
        <div class="uk-modal-dialog">
            <div class="uk-modal-header">
                <h3 class="uk-modal-title">Create Journey</h3>
            </div>
            <div class="uk-modal-body">
                <div class="uk-grid">
                    <div class="uk-width-large-1-1">
                        <div class="md-input-wrapper md-input-filled">
                            <label>Routes <span class="red" >*</span></label>
                            <input type="hidden"  id="date_sel" name="date" class="md-input md-input-success label-fixed" required >
                            <input type="hidden"  id="j_id" name="j_id" class="md-input md-input-success label-fixed" required >
                            <select class="md-input md-input-success label-fixed" name="routes" id="route_id">
                            	<option value="">Select Routes</option>
                            	<?php if(!empty($routes)){?>
                            		<?php  foreach($routes as $r){?>
                            			<option value="<?php echo $r->id?>"><?php echo $r->name?></option>
                            		<?php }?>
                            	<?php }?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-modal-footer uk-text-right">
                <button type="submit" class="md-btn md-btn-success md-btn-flat" id="submitbtn" >Submit</button>
                <button type="button" class="md-btn md-btn-flat uk-modal-close" >Close</button>
            </div>
        </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript" src="<?= $assets ?>plugins/full_calender/main.js"></script>
<script type="text/javascript" src="<?= $assets ?>plugins/full_calender/intrac_main.js"></script>
<script type="text/javascript" src="<?= $assets ?>plugins/full_calender/daygridmain.js"></script>


<script>

	

	function parseDate(s) {
	  var b = s.split(/\D/);
	  return new Date(b[2], --b[0], b[1]);
	}

	var today = new Date();
	today.setHours(0,0,0,0);
	
	var calendarEl,calendar,lastId,formModal;
	calendarEl = document.getElementById('dates-calendar');
	if(calendar){
		calendar.destroy();
	}
	calendar = new FullCalendar.Calendar(calendarEl, {
		plugins: [ 'dayGrid' ,'interaction'],
		header: {},
		selectable: true,
		selectMirror: false,
		allDay:false,
		editable: false,
		eventLimit: true,
		defaultView: 'dayGridMonth',
		events:{
			url:"<?php echo base_url('admin/pop_journey/load_journey')?>",
			extraParams:{
				id: '<?php echo $user_id?>',
			}
		},
		weekends : false,
		loading:function (isLoading) {
			if(!isLoading){
				$(calendarEl).removeClass('loading');
			}else{
				$(calendarEl).addClass('loading');
			}
		},
		select: function(arg) {
			console.log(arg);
			var startDate = arg.startStr;
			startDate = new Date(startDate);
			startDate.setHours(0,0,0,0);
			console.log(startDate);
			console.log(today);

			if (startDate < today) {
				alert('Cant Add event on Past Dates');
				return false;
			}
			
			$('#date_sel').val(arg.startStr);
			$('#route_id').val('');
			$('#del_task').hide();
			$('#addFrom').attr('data-action','<?php echo base_url('admin/pop_journey/insert_journey'); ?>');
			 UIkit.modal('#task_modal').show();
		},
		eventClick:function (info) {
			var form = Object.assign({},info.event.extendedProps);
			form.start_date = moment(info.event.start).format('YYYY-MM-DD');
			form.end_date = moment(info.event.start).format('YYYY-MM-DD');
			form.id = info.event.id;
			console.log(form);
			var formattedDate = new Date(info.event.start);
			var d = formattedDate.getDate();
			var m =  formattedDate.getMonth();
			m += 1;  // JavaScript months are 0-11
			var y = formattedDate.getFullYear();
			if (d < 10) {
		        d = "0" + d;
		    }
		    if (m < 10) {
		        m = "0" + m;
		    }
			var date = (y + "-" + m + "-" + d);
			console.log(date);
			$('#date_sel').val(date);
			$('#route_id').val(form.route_id);
			$('#j_id').val(form.id);
			$('#addFrom').attr('data-action','<?php echo base_url('admin/pop_journey/update_journey'); ?>');
			UIkit.modal('#task_modal').show();
		},
		eventRender: function (info) {
			console.log(info.event.extendedProps);
			$(info.el).prepend( "<div class='ibox-tools'><a style='background-color: red; color:white; margin-right: 10px; padding:0px 6px' class='pull-left del_task' data-id='"+info.event.id+"'><i class='fa fa-times closeon'></i></a></div>" );
			$(info.el).find('.fc-title').html(`( Assigned ) - ${info.event.title}`);
		}
	});
	calendar.render();
	$('body').on('click','.del_task',function(e){
	// $('.del_task').click(function(e){
		e.preventDefault();
		var id  = $(this).attr('data-id');
		console.log(id);
		if (confirm("Are You Sure? ") == true) {
		  console.log("You pressed OK!");
		  $.ajax({
            url: '<?php echo base_url('admin/pop_journey/delete_journey')?>',
            type: 'POST',
            data: {
            	'id' : id,
            	"<?php echo $this->security->get_csrf_token_name(); ?>" : "<?php echo $this->security->get_csrf_hash(); ?>"
            },
            success: function(data) {
            	// console.log(data);
            	// return false;
                var obj = jQuery.parseJSON(data);
                console.log(obj);
                if(obj.status){
                    toastr.success(obj.message);
                    calendar.refetchEvents();
                }
                else{
                    toastr.error(obj.message);
                }
                $('#submitbtn').prop('disabled', false);
            }
        });
		} else {
		  console.log("You canceled!");
		}
		return false;
	});


	$('.close').click(function(){
		$('#task_modal').modal('hide');
	});

	$('#addFrom').submit(function(e){
            e.preventDefault();

            $('#submitbtn').prop('disabled', true);
            var action = $(this).attr('data-action');
            $.ajax({
                url: action,
                type: 'POST',
                data: {
                	'id' : $('#j_id').val(),
                	'user_id' : '<?php echo $user_id?>',
                	'date' : $('#date_sel').val(),
                	'route_id' : $('#route_id').val(),
                	"<?php echo $this->security->get_csrf_token_name(); ?>" : "<?php echo $this->security->get_csrf_hash(); ?>"
                },
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    console.log(obj);
                    if(obj.status){
                        toastr.success(obj.message);
                        UIkit.modal('#task_modal').hide();
                        calendar.refetchEvents();
                        $('#addFrom')[0].reset();
                    }
                    else{
                        toastr.error(obj.message);
                    }
                    $('#submitbtn').prop('disabled', false);
                }
            });
        });
</script>