jQuery(document).ready(function($) {
	
	$('.crew-delete-list').on('click',function(){
		if (confirm("Are you sure ? This action is undoable!")) {
			var action = 'crew_delete';
			var form = $(this).parent().parent().parent();
			formdata = 'crewID='+ $(this).data('crew-id')+'&action='+action;
			do_crew_action(form,formdata,action);
		}
		return false;
	});



	$('.change-crew').on('change',function(){ 
		var crew_id = $(this).data('crew-id');
		var new_fleet = $(this).val();
		var old_fleet = $(this).data('crew-fleet');
		form = $(this).parent().parent();
		if(new_fleet != old_fleet){
			if (confirm("Are you sure ?")){
				formdata = 'crewID='+crew_id+'&fleet='+new_fleet+'&action=crew_update_fleet';
				action = 'crew_update';
				do_crew_action(form,formdata,action);
			}else{
				$(this).val('');
			}
		}				
	});
	
	if(detail_page){			
		$('.crew-form').submit(function(event){
			needToConfirm = false;
			var action = $('#crewAction').val();
			var form = $(this);
			$(form).find('button').prop('disabled',true);
			$('.btn-save-event').button('loading');
			$form = $(this).closest('form');
			if(action == 'crew_image_delete' || action == 'crew_delete'){
				formdata = $(this).serialize();
				do_crew_action(form,formdata,action);
				return false;
			}
		});

		$(document).on('click',".deleteImage", function(event) {
			if (confirm("Are you sure ? This action is undoable!")) {
				var action = 'crew_image_delete';
				var form = $(this).parent().parent().parent();
				formdata = 'crewID='+ $(this).data('crew-id')+'&action='+action+'&imageID='+$(this).data('image-id');
				do_crew_action(form,formdata,action);
			}
		});

		if($('#pac-input').val()!=''){
			$('#pac-input').focus();	
		}

		$('#crewBirthdate').datepicker({
			dateFormat: "dd/mm/yy",
			changeMonth: true,
			changeYear: true,
			maxDate: 0,
			minDate: "-50y",
			yearRange: "c-50:c+10"
		});

		$('#crewJoindate').datepicker({
			dateFormat: "dd/mm/yy",
			changeMonth: true,
			changeYear: true,
			maxDate: 0,
			minDate: "-50y",
			yearRange: "c-50:c+10"
		});
			
	}
});
		
function do_crew_action(form,formdata,action){
	
	jQuery.post( backend_url+'crew/ajax/', formdata, function(data)
	{
		if(form!=undefined && form!=null && form!=''){
			if(!$(form).hasClass('event-list')){
				$(form).find('button').prop('disabled',false);
				$(form).find('.btn-save-event').button('reset');
			}else{
				$(form).remove();
			}
		}
		if(data=='success'){
			switch(action){
				case 'crew_status':
					$(form).attr('data-status',newStatus);			
					check = (newStatus) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>';
					$(form).html(check);
					break;
				case 'crew_image_delete':
					$('#currentCrewImage').attr('src',no_image);
					$('#deleteCrewImage').hide();
					break;
				case 'delete_bulk':
				case 'crew_delete':
					window.location.assign(backend_url+'crew/');
					break;break;	
			}
		}
	});	
}