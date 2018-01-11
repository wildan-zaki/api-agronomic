jQuery(document).ready(function($){		
	$('.fleet-delete-list').on('click',function(){
		if (confirm("Are you sure ? This action is undoable!")) {
			var action = 'fleet_delete';
			var form = $(this).parent().parent().parent();
			formdata = 'fleetID='+ $(this).data('fleet-id')+'&action='+action;
			do_fleet_action(form,formdata,action);
		}
		return false;
	});
	
	if(detail_page){			
		$('.fleet-form').submit(function(event){
			needToConfirm = false;
			var action = $('#fleetAction').val();
			var form = $(this);
			$(form).find('button').prop('disabled',true);
			$('.btn-save-fleet').button('loading');
			$form = $(this).closest('form');
			
			if(action == 'fleet_delete'){
				formdata = $(this).serialize();
				do_fleet_action(form,formdata,action);
				return false;
			}
		});	
	}
});
		
function do_fleet_action(form,formdata,action){
	
	jQuery.post( backend_url+'fleet/ajax/', formdata, function(data)
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
				case 'fleet_status':
					$(form).attr('data-status',newStatus);			
					check = (newStatus) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>';
					$(form).html(check);
					break;
				case 'delete_bulk':
				case 'fleet_delete':
					window.location.assign(backend_url+'fleet/');
					break;break;	
			}
		}
	});	
}