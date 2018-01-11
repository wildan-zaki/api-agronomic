jQuery(document).ready(function($){		
				
	$('.user-delete-list').on('click',function(){
		if (confirm("Are you sure ? This action is undoable!")) {
			var action = 'user_delete';
			var form = $(this).parent().parent().parent();
			formdata = 'userID='+ $(this).data('user-id')+'&action='+action;
			do_user_action(form,formdata,action);
		}
		return false;
	});
		
	$('.remove-child').on('click',function(){
		if (confirm("Are you sure ? This action is undoable!")) {
			var action = 'user_child_delete';
			var form = $(this).parent().parent();
			formdata = 'userID='+ $(this).data('child-id')+'&action='+action;
			do_user_action(form,formdata,action);
		} 
		return false; 
	});
	
	if(detail_page){				
		$('.user-form').submit(function(event){
			needToConfirm = false;
			var action = $('#userAction').val();
			var form = $(this);
			$(form).find('button').prop('disabled',true);
			$('.btn-save-event').button('loading');
			$form = $(this).closest('form');
			if(action == 'user_image_delete' || action == 'child_image_delete' || action == 'user_delete'){
				formdata = $(this).serialize();
				do_user_action(form,formdata,action);
				return false;
			}
		});
			
		$(document).on('click',".add-child", function(event) {
			lastChild = parseInt($('#lastChild').val());
			curChild = $('#newChild .new-child .childImage').attr('name');
			newChild = curChild.replace("#n",lastChild);
			$('#newChild .new-child .childImage').attr('name',newChild).attr('id',newChild);
			$('#newChild .new-child label').attr('for',newChild);
			$(this).parent().parent().parent().parent().find('tbody').append($('#newChild .new-child').html());
			$('#newChild .new-child .childImage').attr('name',curChild).attr('id',curChild);	
			$('#newChild .new-child label').attr('for',curChild);
			$('#lastChild').val(lastChild+1);
			return false;
		});
		
		$('#userBirthdate').datepicker({
			dateFormat: "dd/mm/yy",
			changeMonth: true,
			changeYear: true,
			maxDate: 0,
			minDate: "-50y",
			yearRange: "c-50:c+10"
		});	
			
		$("body").on('focus','.child_birthdate',function(event){
			$(this).datepicker({
				dateFormat: "dd/mm/yy",
				changeMonth: true,
				changeYear: true,
				maxDate: 0,
				minDate: "-20y",
				yearRange: "c-20:c+10"
			});
		});
		
		$(document).on('change',".childImage",function(event){
			var target = $(this).prev();
			readURL(this,target);
			
		});

		$(document).on('click',".deleteImage", function(event) {
			if (confirm("Are you sure ? This action is undoable!")) {
				var action = 'user_image_delete';
				var form = $(this).parent().parent().parent();
				formdata = 'userID='+ $(this).data('user-id')+'&action='+action+'&imageID='+$(this).data('image-id');
				do_user_action(form,formdata,action);
			}
		});
	}
});
		
function do_user_action(form,formdata,action){
	jQuery.post( backend_url+'user/ajax/', formdata, function(data)
	{
		var redirectUrl = backend_url+'user/';
		if(data=='success'){
			switch(action){
				case 'user_status':
					$(form).attr('data-status',newStatus);			
					check = (newStatus==1) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>';
					$(form).html(check);
					break;
				case 'delete_bulk':
				case 'user_delete':
					window.location.assign(redirectUrl);
					break;						
				case 'user_image_delete':
					$('#currentUserImage').attr('src',no_image);
					$('#deleteUserImage').hide();
					break;
				case 'user_child_delete':
					$parent = $(form).parent();
					$(form).remove();
					start = 1;
					$($parent).find('tr').each(function(){
						newImg = 'childImage'+start;
						$(this).find('.childImage').attr('name',newImg).attr('id',newImg);
						$(this).find('label').attr('for',newImg);
						start++;
					});
					$('#lastChild').val(start);
					$(form).remove();
					break;
				default:
					$(form).addClass('success');
					setTimeout(function(){
						$(form).removeClass('success');
					},5000);
					break;	
			}
		}
	});	
}