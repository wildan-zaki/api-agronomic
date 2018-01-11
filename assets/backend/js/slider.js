jQuery(document).ready(function($){		
	$('.slider-delete-list').on('click',function(){
		if (confirm("Are you sure ? This action is undoable!")) {
			var action = 'slider_delete';
			var form = $(this).parent().parent().parent();
			formdata = 'sliderID='+ $(this).data('slider-id')+'&action='+action;
			do_slider_action(form,formdata,action);
		}
		return false;
	});
	
	if(detail_page){			
		$('.slider-form').submit(function(event){
			needToConfirm = false;
			var action = $('#sliderAction').val();
			var form = $(this);
			$(form).find('button').prop('disabled',true);
			$('.btn-save-event').button('loading');
			$form = $(this).closest('form');
			if(action == 'slider_image_delete' || action == 'slider_delete'){
				formdata = $(this).serialize();
				do_slider_action(form,formdata,action);
				return false;
			}
		});

		$(document).on('click',".deleteImage", function(event) {
			if (confirm("Are you sure ? This action is undoable!")) {
				var action = 'slider_image_delete';
				var form = $(this).parent().parent().parent();
				formdata = 'sliderID='+ $(this).data('slider-id')+'&action='+action+'&imageID='+$(this).data('image-id');
				do_slider_action(form,formdata,action);
			}
		});
	}
});
		
function do_slider_action(form,formdata,action){
	
	jQuery.post( backend_url+'slider/ajax/', formdata, function(data)
	{
		var redirectUrl = backend_url+'slider/';
		if(data=='success'){
			switch(action){
				case 'slider_status':
					$(form).attr('data-status',newStatus);			
					check = (newStatus) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>';
					$(form).html(check);
					break;
				case 'slider_image_delete':
					$('#currentSliderImage').attr('src',no_image);
					$('#deleteSliderImage').hide();
					break;
				case 'delete_bulk':
				case 'slider_delete':
					window.location.assign(backend_url+'slider/');
					break;break;	
			}
		}
	});	
}