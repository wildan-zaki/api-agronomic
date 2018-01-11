var event_id,event_name,vehicleAjax,lat,lon,name,actionAjax,geocoder,eventData,eventAction,brandData,brandAction,formAjax,input,searchBox,map,needToConfirm,curFeature,newFeature,curStatus,newStatus,cur,curSlug,brandAjax,vehicleAjax,serviceAjax,selectedBrand,selectedVehicle,selectedYear,selectedColor,selectedTransmission,no_image,backend_url,defaultDate,eventlists,place;
var detail_page = false; 
jQuery(document).ready(function($){
	$( "#pac-input" ).keypress(function() {
	  if ( event.which == 13) {
	   event.preventDefault(); 
	  }
	});
	
	$('.collapse').on('show.bs.collapse', function () {  
	  $('.collapse').collapse('hide');
	})
	
	$(".hover-list").hover(function(){
		$(".helper-hover").addClass('hidden');
		$(this).find('.helper-hover').removeClass('hidden');
	},function(){
		$(".helper-hover").addClass('hidden');
	});
	
	$(".sort-th").hover(function(){
		$(".sort").addClass('hidden');
		$(this).find('.sort').removeClass('hidden');
	},function(){
		$(".sort").addClass('hidden');
	});
			
	$('.btn-status-list').on('click',function(){
		if (confirm("Are you sure ?")) {
			cur = $(this).data('action');
			var action = cur+'_status';
			curStatus = $(this).attr('data-status');
			newStatus = (curStatus==1) ? 0 : 1;
			var form = $(this);
			formdata = cur+'ID='+ $(this).data(cur+'-id')+'&action='+action+'&status='+newStatus;
			switch(cur){
				case 'user':
					do_user_action(form,formdata,action);
					break;
				case 'service':
					do_service_action(form,formdata,action);
					break;
				case 'brand':
					do_brand_action(form,formdata,action);
					break;
				case 'fleet':
					do_fleet_action(form,formdata,action);
					break;
				case 'vehicle':
					do_vehicle_action(form,formdata,action);
					break;
				case 'crew':
					do_crew_action(form,formdata,action);
					break;
				case 'slider':
					do_slider_action(form,formdata,action);
					break;
				case 'city':
				case 'province':
				case 'country':
				case 'category':
				case 'catmer':
				case 'interest':
					do_setting_action(form,formdata,action);
					break;
			}
		}
		return false;
	});
			
	$('[data-toggle="tooltip"]').tooltip();
			
	$('.checkbox_all').on('change',function(){
		form = $(this).closest('form');
		doCheck = ($(this).is(':checked')) ? true : false;
		$('.checkbox_single').prop('checked',false);
		$(form).find('.checkbox_single').each(function(){
			if(doCheck)
				$(this).prop('checked',true);
		});
	});
	
	$(document).on('change','.uploadCSV',function(event){
		csv = $(this).val();
		if(csv!='' && csv!=undefined && csv!=null){
			$('.bulk-action').val('upload_csv');
			setting_name = $('.setting_name').val();
			setting_url = 'setting/'+setting_name+'/csv/upload';
			post_url = backend_url+setting_url;
			$('.form-bulk').attr('action',post_url);
			$('.form-bulk').submit();	
		}
	});

	/*$(document).on('change','#brandName',function() {
		var cur = $(this);
		var brandName = $(this).val();

		if(brandName){
			if(brandAjax) brandAjax.abort();
			brandAjax = jQuery.ajax({
				type: 'POST', 
				url: backend_url+'setting/ajax',
				dataType: "json",
				data: {action: 'vehicle_list', brandName : brandName},
				beforeSend: function(){
					$(cur).prop('disabled',true);
					$(".vehicle-list option").not('.select-vehicle-first').remove();
					$(".year-list option").not('.select-year-first').remove();
				},
				success: function(data){
					$(cur).prop('disabled',false);
					if(data.vehicle!=null && data.vehicle!=undefined){
						vehicles = new Array();
						jQuery.each(data.vehicle, function(index, element) {
							var cek = vehicles.indexOf(element.fvehicleid);
							if(cek<0){
								vehicles.push(element.fvehicleid);
								if(element.fvehicleid==selectedVehicle)
									optionList = '<option value="'+ element.fvehicleid +'" selected>'+element.fvehiclename+'</option>';
								else
									optionList = '<option value="'+ element.fvehicleid +'">'+element.fvehiclename+'</option>';
								jQuery(optionList).appendTo($(".vehicle-list"));
							}
						});
						$(".vehicle-list .select-vehicle-first").text('Select Vehicle Below');
						if(selectedVehicle!=undefined && selectedVehicle!=null && selectedVehicle!='')
							change_vehicle(selectedVehicle,$('.vehicle-list'));
					}else{
						$(".vehicle-list .select-vehicle-first").text('Select Brand First');	
					}
				}
			});
		}
	});	
			
	$("#fleetVehicle").on('change',function() {
		var cur = $(this);
		var vehicle_id = $(this).val();
		if(vehicle_id){
			change_vehicle(vehicle_id,cur);
			change_color(vehicle_id,cur);
			change_transmission(vehicle_id,cur);
		}
	});	*/

	
	$('.form-bulk').submit(function(event){
		var action = $('#bulkAction').val();
		if(action=='' || action==null) action = $('#bulkActionSelector').val();
		var filter = $('#bulkActionFilter').val();
		
		if(filter!='' && filter!=null && filter!=undefined){ return true; }
		else{window.location.assign(backend_url+curSlug);}
		var id = $(this).attr('id');
		if(action=='' || action==null) return false;
		form = $(this).closest('form');
		if(action!='upload_csv'){
			doForm = false;
			$(form).find('.checkbox_single').each(function(){
				if($(this).is(':checked')){
					doForm = true;
					return false;	
				}
			});
			if(!doForm) return false;
		}
		$(form).find('button').prop('disabled',true);
		
		switch(action){
			case 'upload_csv':
				if (!confirm("Are you sure ? This action will update all the current data from this csv files!")) {
					return false;
				}
				break;
			case 'order_update_status_bulk':
				if (confirm("Are you sure ? This action will update all the selected order with new status.")) {
					formdata = $(this).serialize();
					do_order_action(form,formdata,action);
					return false;
				}
				break;
			case 'delete_bulk':
				if (confirm("Are you sure ? This action is undoable!")) {
					formdata = $(this).serialize();
					switch(id){
						case 'formBulkBrand':
							do_brand_action(form,formdata,action);
							break;
						case 'formBulkMerchant':
							do_service_action(form,formdata,action);
							break;
						case 'formBulkService':
						case 'formBulkSubservice':
						case 'formBulkAddon':
							do_service_action(form,formdata,action);
							break;
						case 'formBulkCoupon':
							do_coupon_action(form,formdata,action);
							break;
						case 'formBulkCrew':
							do_crew_action(form,formdata,action);
							break;
						case 'formBulkFleet':
							do_fleet_action(form,formdata,action);
							break;
						case 'formBulkVehicle':
							do_vehicle_action(form,formdata,action);
							break;
						case 'formBulkSlider':
							do_slider_action(form,formdata,action);
							break;
					}
					return false;
				}
				break;	
		}
	});
}); 
		
/*function change_vehicle(vehicle_id,cur){
	if(vehicleAjax){vehicleAjax.abort();}
	vehicleAjax = jQuery.ajax({
		type: 'POST',
		url: backend_url+'setting/ajax/',
		dataType: "json",
		data: {action: 'year_list', vehicle_id : vehicle_id},
		beforeSend: function(){
			$(cur).prop('disabled',true);
			$(".year-list").prop('disabled',true);
			$(".year-list option").not('.select-year-first').remove();
		},
		success: function(data){
			$(cur).prop('disabled',false);
			$("#fleetYear").prop('disabled',false);
			if(data.year!=null && data.year!=undefined){
				years = new Array();
				jQuery.each(data.year, function(index, element) {
					var cek = years.indexOf(element.fmetavalue);
					if(cek<0){
						years.push(element.fmetavalue);											
						if(element.fmetavalue==selectedYear)
							optionList = '<option value="'+ element.fmetavalue +'" selected>'+element.fmetavalue+'</option>';
						else
							optionList = '<option value="'+ element.fmetavalue +'">'+element.fmetavalue+'</option>';
						
						jQuery(optionList).appendTo($("#fleetYear"));
					}
				});
				$("#fleetYear .select-year-first").text('Select Year Below');
			}else{
				$("#fleetYear .select-year-first").text('Select Vehicle First');	
			}
		}
	});	
}

function change_color(vehicle_id,cur){
	jQuery.ajax({
		type: 'POST',
		url: backend_url+'setting/ajax/',
		dataType: "json",
		data: {action: 'color_list', vehicle_id : vehicle_id},
		beforeSend: function(){
			$(".color-list").prop('disabled',true);
			$(".color-list option").not('.select-color-first').remove();
		},
		success: function(data){
			$(cur).prop('disabled',false);
			$("#fleetColor").prop('disabled',false);
			if(data.color!=null && data.color!=undefined){
				colors = new Array();
				jQuery.each(data.color, function(index, element) {
					var cek = colors.indexOf(element.fmetavalue);
					if(cek<0){
						colors.push(element.fmetavalue);											
						if(element.fmetavalue==selectedColor)
							optionList = '<option value="'+ element.fmetavalue +'" selected>'+element.fmetavalue+'</option>';
						else
							optionList = '<option value="'+ element.fmetavalue +'">'+element.fmetavalue+'</option>';
						
						jQuery(optionList).appendTo($("#fleetColor"));
					}
				});
				$("#fleetColor .select-color-first").text('Select Color Below');
			}else{
				$("#fleetColor .select-color-first").text('Select Vehicle First');	
			}
		}
	});	
}

function change_transmission(vehicle_id,cur){
	jQuery.ajax({
		type: 'POST',
		url: backend_url+'setting/ajax/',
		dataType: "json",
		data: {action: 'transmission_list', vehicle_id : vehicle_id},
		beforeSend: function(){
			$(".transmission-list").prop('disabled',true);
			$(".transmission-list option").not('.select-transmission-first').remove();
		},
		success: function(data){
			$(cur).prop('disabled',false);
			$("#fleetTransmission").prop('disabled',false);
			if(data.transmission!=null && data.transmission!=undefined){
				transmissions = new Array();
				jQuery.each(data.transmission, function(index, element) {
					var cek = transmissions.indexOf(element.fmetavalue);
					if(cek<0){
						transmissions.push(element.fmetavalue);											
						if(element.fmetavalue==selectedTransmission)
							optionList = '<option value="'+ element.fmetavalue +'" selected>'+element.fmetavalue+'</option>';
						else
							optionList = '<option value="'+ element.fmetavalue +'">'+element.fmetavalue+'</option>';
						
						jQuery(optionList).appendTo($("#fleetTransmission"));
					}
				});
				$("#fleetTransmission .select-transmission-first").text('Select Transmission Below');
			}else{
				$("#fleetTransmission .select-transmission-first").text('Select Vehicle First');	
			}
		}
	});	
}*/

function readURL(input,target) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		
		reader.onload = function (e) {
			$(target).attr('src',e.target.result);
		}
		
		reader.readAsDataURL(input.files[0]);
	}
}

function initMap() {
	var lat = $('#map').attr("lat");
	var lon = $('#map').attr('lon');
	var name = $('#map').attr('name');
	geocoder = new google.maps.Geocoder();
	var myOptions = {
		zoom: 15,
		/*mapTypeId: google.maps.MapTypeId.ROADMAP*/
	};
	if((lat=='' && lon=='') || lat==undefined){
		lat = -6.2293867;
		lon = 106.6894289;					
	}
	myOptions['center'] = new google.maps.LatLng(lat, lon)
	map = new google.maps.Map(document.getElementById('map'),myOptions);
	  
  var marker = new google.maps.Marker({
	map: map,
	position: map.getCenter(),
	draggable: true
  });

  // Create the search box and link it to the UI element.
  input = document.getElementById('pac-input');
  searchBox = new google.maps.places.SearchBox(input);
  service = new google.maps.places.PlacesService(map);
  map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

  // Bias the SearchBox results towards current map's viewport.
  map.addListener('bounds_changed', function() {
	searchBox.setBounds(map.getBounds());
  });

  var markers = [];
  // Listen for the event fired when the user selects a prediction and retrieve
  // more details for that place.
  searchBox.addListener('places_changed', function() {
	var places = searchBox.getPlaces();

	if (places.length == 0) {
	  return;
	}

	// Clear out the old markers.
	markers.forEach(function(marker) {
	  marker.setMap(null);
	});
	markers = [];

	// For each place, get the icon, name and location.
	var bounds = new google.maps.LatLngBounds();
	places.forEach(function(place) {

	  // Create a marker for each place.
	  
	 // markers.push(new google.maps.Marker({
//			map: map,
//			//icon: icon,
//			title: place.name,
//			position: place.geometry.location,
//			draggable: true
//		  }));
	  if (marker != null) {
			marker.remove();
		}
	  var marker = new google.maps.Marker({
		map: map,
		title: place.name,
		position: place.geometry.location,
		draggable: true
	  });	
	  newLat = place.geometry.location.lat();
	  newLon = place.geometry.location.lng();
	  $('#coordinate').val(newLat+','+newLon);
	  
	  $('#eventAddress').html(place.formatted_address);
	  console.log(place);
	  if(place.address_components!=null){
		  mappingAddress(place.address_components);
	  }
	  
	  google.maps.event.addListener(marker, 'dragend', function(evt){
		 newLat = evt.latLng.lat();
		 newLon = evt.latLng.lng()
		 $('#coordinate').val(newLat+','+newLon);
		 geocodePosition(marker.getPosition());
	  });
	  
	  if (place.geometry.viewport) {
		// Only geocodes have viewport.
		bounds.union(place.geometry.viewport);
	  } else {
		bounds.extend(place.geometry.location);
	  }
	});
	map.fitBounds(bounds);
	
  });
  google.maps.event.addListener(marker, 'dragend', function(evt){
	 newLat = evt.latLng.lat();
	 newLon = evt.latLng.lng()
	 $(coordinate).val(newLat+','+newLon);
	 geocodePosition(marker.getPosition());
  });
}
	
function geocodePosition(pos) {
	  geocoder.geocode({
		latLng: pos
	  }, function(responses) {
		if (responses && responses.length > 0) {
		  $('#eventAddress').html(responses[0].formatted_address);
		  
		  if(responses[0].address_components!=null){
			mappingAddress(responses[0].address_components);
		  }
		  
		  //updateMarkerAddress(responses[0].formatted_address);
		} else {
		  //updateMarkerAddress('Cannot determine address at this location.');
		}
	  });
	}
	
function mappingAddress(address_components){
	var val, kecamatan, kode_pos;
	console.log(address_components);
	for(vax in address_components){
		if(kecamatan!=null && kode_pos!=null) return false;
		if(address_components[vax].types[0]=='administrative_area_level_3'){
			$('#eventKecamatan').val(address_components[vax].long_name);
			kecamatan = address_components[vax].long_name;
		}
		if(address_components[vax].types[0]=='postal_code'){
			$('#eventPostal').val(address_components[vax].long_name);
			kode_pos = address_components[vax].long_name;
		}
	}
}