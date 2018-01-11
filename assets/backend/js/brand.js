jQuery(document).ready(function($){		 
			
	$('.brand-delete-list').on('click',function(){
		if (confirm("Are you sure ? This action is undoable!")) {
			var action = 'brand_delete';
			var form = $(this).parent().parent().parent();
			formdata = 'brandID='+ $(this).data('brand-id')+'&action='+action;
			do_brand_action(form,formdata,action);
		}
		return false;
	});	
			
	$('.change-brand').on('change',function(){ 
		var brand_id = $(this).data('brand-id');
		var new_status = $(this).val();
		var old_status = $(this).data('brand-status');
		form = $(this).parent().parent();
		if(new_status != old_status){
				if (confirm("Are you sure ?")){
				formdata = 'brandID='+brand_id+'&status='+new_status+'&action=brand_update_status';
				action = 'brand_update';
				do_brand_action(form,formdata,action);
			}
		}				
	});
	
	if(detail_page){
		$('.brand-form').submit(function(event){
			needToConfirm = false;
			var action = $('#brandAction').val();
			var form = $(this);
			$(form).find('button').prop('disabled',true);
			$('.btn-save-event').button('loading');
			$form = $(this).closest('form');
			if(action == 'brand_image_delete' || action == 'brand_delete'){
				formdata = $(this).serialize();
				do_brand_action(form,formdata,action);
				return false;
			}
		});

		$(document).on('click',".deleteImage", function(event) {
			if (confirm("Are you sure ? This action is undoable!")) {
				var action = 'brand_image_delete';
				var form = $(this).parent().parent().parent();
				formdata = 'brandID='+ $(this).data('brand-id')+'&action='+action+'&imageID='+$(this).data('image-id');
				do_brand_action(form,formdata,action);
			}
		});
	}
});
		
function do_brand_action(form,formdata,action){
	
	jQuery.post( backend_url+'brand/ajax/', formdata, function(data)
	{
		var redirectUrl = backend_url+'brand/';
		if(data=='success'){
			switch(action){
				case 'brand_status':
					$(form).attr('data-status',newStatus);			
					check = (newStatus) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>';
					$(form).html(check);
					break;
				case 'brand_image_delete':
					$('#currentBrandImage').attr('src',no_image);
					$('#deleteBrandImage').hide();
					break;
				case 'delete_bulk':
				case 'brand_delete':
					window.location.assign(backend_url+'brand/');
					break;break;	
			}
		}
	});	
}