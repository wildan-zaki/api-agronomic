jQuery(document).ready(function($){		
	$('.service-delete-list').on('click',function(){
		if (confirm("Are you sure ? This action is undoable!")) {
			var action = 'service_delete';
			var form = $(this).parent().parent().parent();
			formdata = 'serviceID='+ $(this).data('service-id')+'&action='+action;
			do_service_action(form,formdata,action);
		}
		return false;
	});

	$('.subservice-delete-list').on('click',function(){
		if (confirm("Are you sure ? This action is undoable!")) {
			var action = 'subservice_delete';
			var form = $(this).parent().parent().parent();
			formdata = 'subserviceID='+ $(this).data('subservice-id')+'&action='+action;
			do_service_action(form,formdata,action);
		}
		return false;
	});

	$('.addon-delete-list').on('click',function(){
		if (confirm("Are you sure ? This action is undoable!")) {
			var action = 'addon_delete';
			var form = $(this).parent().parent().parent();
			formdata = 'addonID='+ $(this).data('addon-id')+'&action='+action;
			alert
			//do_service_action(form,formdata,action);
		}
		return false;
	});
	
	if(detail_page){			
		$('.service-form').submit(function(event){
			needToConfirm = false;
			var action = $('#serviceAction').val();
			var form = $(this);
			$(form).find('button').prop('disabled',true);
			$('.btn-save-event').button('loading');
			$form = $(this).closest('form');
			if(action == 'service_image_delete' || action == 'service_delete'){
				formdata = $(this).serialize();
				alert (formdata);
				do_service_action(form,formdata,action);
				return false;
			}
		});

		$(document).on('click',".deleteImage", function(event) {
			if (confirm("Are you sure ? This action is undoable!")) {
				var action = 'service_image_delete';
				var form = $(this).parent().parent().parent();
				formdata = 'serviceID='+ $(this).data('service-id')+'&action='+action+'&imageID='+$(this).data('image-id');
				do_service_action(form,formdata,action);
			}
		});

		$(document).on('click',".deleteSubserviceImage", function(event) {
			if (confirm("Are you sure ? This action is undoable!")) {
				var action = 'subservice_image_delete';
				var form = $(this).parent().parent().parent();
				formdata = 'subserviceID='+ $(this).data('service-id')+'&action='+action+'&imageID='+$(this).data('image-id');
				do_service_action(form,formdata,action);
			}
		});

		$(document).on('click',".deleteAddonImage", function(event) {
			if (confirm("Are you sure ? This action is undoable!")) {
				var action = 'add_service_image_delete';
				var form = $(this).parent().parent().parent();
				formdata = 'serviceID='+ $(this).data('service-id')+'&action='+action+'&imageID='+$(this).data('image-id');
				do_service_action(form,formdata,action);
			}
		});
	}
});
		
function do_service_action(form,formdata,action){
	alert(curSlug);
	jQuery.post( backend_url+'service/ajax/', formdata, function(data)
	{
		var redirectUrl = backend_url+'service/';
		if(data=='success'){
			switch(action){
				case 'service_status':
					$(form).attr('data-status',newStatus);			
					check = (newStatus) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>';
					$(form).html(check);
					break;
				case 'service_image_delete':
					$('#currentServiceImage').attr('src',no_image);
					$('#deleteServiceImage').hide();
					break;
				case 'subservice_image_delete':
					$('#currentServiceImage').attr('src',no_image);
					$('#deleteServiceImage').hide();
					break;
				case 'delete_bulk':
					if(curSlug == 'service/addon/')window.location.assign(backend_url+curSlug);
					if(curSlug == 'service/subservice/')window.location.assign(backend_url+curSlug);
					if(curSlug == 'service') window.location.assign(backend_url+curSlug);
					break;
				case 'service_delete':
					window.location.assign(backend_url+'service/');
					break;

			}
		}
	});	
}