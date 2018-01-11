jQuery(document).ready(function($){				
	selectedCountry = $('#countryCode').val();	
	selectedProvince = $('.province-list').val();
	selectedCity = $('.city-list').val();
	needToConfirm = false;
	window.onbeforeunload = askConfirm;	
	$("select,input,textarea").change(function() {
		needToConfirm = true;
	});
				
	$("#brandImage,#crewImage,#serviceImage,#vehicleImage,#userImage,#countryImage").on('change',function(event){
		var target = $(this).parent().prev().find('img');
		readURL(this,target);
		
	});
			
	/*$(document).on('click',"#deleteUserImage, #deleteVehicleImage, #deleteCrewImage, #deleteServiceImage, #deleteImage", function(event) {
		if (confirm("Are you sure ? This action is undoable!")) {
			var id = $(this).attr('id');
			switch(id){
				case 'deleteImage':
					$('#eventAction').val('event_image_delete');
					$('.event-form').submit();
					break;
				case 'deleteSettingImage': 
					$('#settingAction').val('image_delete'); 
					$('.setting-form').submit();
					break;
				case 'deleteUserImage': 
					$('#userAction').val('user_image_delete');
					$('.user-form').submit();
					break;
				case 'deleteCrewImage': 
					$('#crewAction').val('crew_image_delete');
					$('.crew-form').submit();
					break;
				case 'deleteServiceImage': 
					$('#serviceAction').val('service_image_delete');
					$('.service-form').submit();
					break;
			}
			
		}
	});*/
			
	$(document).on('click',".btn-delete-user, .btn-delete-setting, .btn-delete-crew, .btn-delete-fleet, .btn-delete-event, .btn-delete-vehicle", function(event) {
		if (confirm("Are you sure ? This action is undoable!")) {
			var cls = $(this).attr('class').replace("btn btn-link ","");
			switch(cls){						
				case 'btn-delete-event':
					$('#eventAction').val('event_delete');
					$('.event-form').submit();
					break;
				case 'btn-delete-fleet':
					$('#fleetAction').val('fleet_delete');
					$('.fleet-form').submit();
					break;					
				case 'btn-delete-vehicle':
					$('#vehicleAction').val('vehicle_delete');
					$('.vehicle-form').submit();
					break;
				case 'btn-delete-setting':
					$('#settingAction').val('setting_delete');
					$('.setting-form').submit();
					break;
				case 'btn-delete-user':
					$('#userAction').val('user_delete');
					$('.user-form').submit();
					break;	
				case 'btn-delete-merchant':
					$('#merchantAction').val('merchant_delete');
					$('.merchant-form').submit();
					break;	
			}
		}
	});
		
	$(document).on('mouseenter','.photo-list',function(event) {
		$(this).find('.hover').removeClass('hidden');
	});
	$(document).on('mouseleave','.photo-list',function(event) {
		$(this).find('.hover').addClass('hidden');
	});
	
	$('#addPhoto').on('click',function(event){
		$('.addPhoto').removeClass('hidden');
		$(this).addClass('hidden');
	});
});	

function askConfirm() {
	if (needToConfirm) {
		// Put your custom message here 
		return "Your unsaved data will be lost."; 
	}
}