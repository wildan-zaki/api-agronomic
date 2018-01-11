jQuery(document).ready(function($){			
			
	$('.country-delete-list, .province-delete-list, .city-delete-list, .category-delete-list, .interest-delete-list').on('click',function(){
		if (confirm("Are you sure ? This action is undoable!")) {
			var cls = $(this).attr('class').replace("-delete-list","").trim(); 
			var action = cls+'_delete';
			var form = $(this).parent().parent().parent();
			formdata = cls+'ID='+ $(this).data(cls+'-id')+'&action='+action;
			do_setting_action(form,formdata,action);
		}
		return false;
	});
	
	if(detail_page){				
		$('.setting-form').submit(function(event){
			needToConfirm = false;
			var action = $('#settingAction').val();
			var form = $(this);
			$(form).find('button').prop('disabled',true);
			$('.btn-save-setting').button('loading');
			$form = $(this).closest('form');
			
			if(action == 'image_delete' || action == 'setting_delete'){
				formdata = $(this).serialize();
				do_setting_action(form,formdata,action);
				return false;
			}
		});
		
		$('#btnUpdateColor').on('click',function(){
			var bgcolor = $('#categoryBGColor').val();
			var fntcolor = $("#categoryFontColor").val();
			$('a.current-image').css({'background-color':bgcolor,'color':fntcolor});
		});
		
		$(document).on('change',"#categoryIcon",function(event){
			var target = $(this).parent().prev().find('.img-responsive');
			readURL(this,target);
			
		});
	
		$(document).on('click','#addFee',function(){
			var fee = parseInt($(this).data('remove'))+1
			$(this).parent().append($('#newFee .new-fee').html());				;
			$(this).parent().find('.add-clearfix-new').addClass('add-clearfix-'+fee).removeClass('add-clearfix-new');
			$(this).parent().find('.add-label-new').addClass('add-label-'+fee).removeClass('add-label-new');
			$(this).parent().find('.add-name-new').addClass('add-name-'+fee).removeClass('add-name-new');
			$(this).parent().find('.add-value-new').addClass('add-value-'+fee).removeClass('add-value-new');
			$(this).parent().find('.add-remove-new').addClass('add-remove-'+fee).removeClass('add-remove-new').attr('data-remove',fee);
			$(this).addClass('removeFee').removeAttr('id');
			$(this).find('button').addClass('btn-danger').removeClass('btn-primary');
			$(this).find('button span').addClass('glyphicon-minus').removeClass('glyphicon-plus');
			return false;
		});
		
		$(document).on('click','.removeFee',function(){
			var rem = $(this).data('remove');
			$('.add-clearfix-'+rem).remove();	
			$('.add-label-'+rem).remove();	
			$('.add-name-'+rem).remove();	
			$('.add-value-'+rem).remove();	
			$(this).remove();	
			return false;
		});
	
		$(document).on('click','#addPayment',function(){
			var pay = parseInt($(this).data('remove'))+1
			console.log(pay);
			$(this).parent().append($('#newPayment .new-payment').html());
			console.log($('#newPayment .new-payment').html());
			$(this).parent().find('.add-payment-prefix-new').addClass('add-payment-prefix-'+pay).removeClass('add-payment-prefix-new');
			$(this).parent().find('.add-payment-copy-new').addClass('add-payment-copy-'+pay).removeClass('add-payment-copy-new');
			$(this).parent().find('.add-payment-remove-new').addClass('add-payment-remove-'+pay).removeClass('add-payment-remove-new').attr('data-remove',pay);
			$(this).addClass('removePaymentCopy').removeAttr('id');
			$(this).addClass('btn-danger').removeClass('btn-primary');
			$(this).html('<span class="glyphicon glyphicon-minus"></span>Remove');
			return false;
		});
	}
});
		
function do_setting_action(form,formdata,action){
	if(action.indexOf('csv')==-1){
		post_url = backend_url+'setting/ajax/';
	}else{
		setting_name = $('.setting_name').val();
		setting_url = 'setting/'+setting_name+'/csv/upload';
		post_url = backend_url+setting_url;
	}
	jQuery.post( post_url, formdata, function(data)
	{
		if(form!=undefined && form!=null && form!=''){
			if(!$(form).hasClass('event-list')){
				$(form).find('button').prop('disabled',false);
				$(form).find('.btn-save-setting').button('reset');
			}else{
				$(form).remove();	
			}
		}
		var redirectUrl = backend_url+curSlug;
		if(data=='success'){
			switch(action){
				case 'province_status':
				case 'country_status':
				case 'city_status':
				case 'category_status':
				case 'catmer_status':
				case 'interest_status':
					$(form).attr('data-status',newStatus);			
					check = (newStatus==1) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>';
					$(form).html(check);
					break;
				case 'delete_bulk':
				case 'province_delete':
				case 'country_delete':	
				case 'setting_delete':	
				case 'city_delete':	
				case 'category_delete':	
				case 'interest_delete':
					window.location.assign(redirectUrl);
					break;						
				case 'image_delete':
					$('#currentSettingImage').attr('src',no_image);
					$('#deleteSettingImage').hide();
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