 var vehicleUser_Ajax,subserviceAjax,event_id,event_name,vehicleAjax,user_vehicleAjax,lat,lon,name,actionAjax,geocoder,eventData,eventAction,formAjax,input,searchBox,service,map,needToConfirm,curFeature,newFeature,curStatus,newStatus,cur,curSlug,brandAjax,vehicleAjax,selectedBrand,selectedFleet,selectedYear,no_image,backend_url,defaultDate,eventlists,place;
jQuery(document).ready(function($){	

	if($('.brandName').val()!='') $('.brandName').trigger('change');	
			
	$('.order-delete-list').on('click',function(){
		if (confirm("Are you sure ? This action is undoable!")) {
			var action = 'order_delete';
			var form = $(this).parent().parent().parent();
			formdata = 'orderID='+ $(this).data('order-id')+'&action='+action;
			do_order_action(form,formdata,action);
		}
		return false; 
	});	
			
	$(document).on('change','.change-order',function(){
		var order_id = $(this).data('order-id');
		var new_status = $(this).val();
		var old_status = $(this).data('order-status');
		form = $(this).parent().parent();
		if(new_status != old_status){
			if (confirm("Are you sure ?")){
				formdata = 'orderID='+order_id+'&status='+new_status+'&action=order_update_status';    
				action = 'order_update';
				do_order_action(form,formdata,action);
				$(this).data('order-status',new_status);
			}else{
				$(this).val(old_status);
			}
		}				
	});
	$(document).on('click','.add-cart',function(){
		var curAction =  $('#orderAction').val();
		var cur = $(this);
		$('#orderAction').val('create_cart');
			jQuery.ajax({
				url: backend_url+'order/ajax/',
				type: "POST",
				dataType: "json",
				data: $("form").serialize(),
				success:function(data){
					console.log(data);
					dcart = data.data.cart;
					additionals = new Array();
					$('.form-after-cart').removeClass('hidden');
					$('#subTotal').val(dcart.currency+' '+dcart.sub_total);

					jQuery.each(dcart.additionals, function(index, element) {
						var cek = additionals.indexOf(element.title);
							if(cek<0){
								$('#adminFee').val(dcart.currency+' '+ element.value);
							}
					});
					$('#grandTotal').val(dcart.currency+' '+dcart.total);
					//console.log(data.cart_token);
					$('#cart_token').val(data.data.cart_token);		
				}

			});
	});

	$(document).on('click','.add-order',function(){
		var curAction =  $('#orderAction').val();
		var cur = $(this);
		$('#orderAction').val('create_order');
		if (confirm("Are you sure ? This action is undoable!")) {
			jQuery.ajax({
				url: backend_url+'order/ajax/',
				type: "POST",
				dataType: "json",
				data: $("form").serialize(),
				success:function(data){
					
						
				}

			});
		}else{
			return false;
		}
	});

	//btn-check-voucher
	$(document).on('click','.btn-check-voucher',function(){
        var curAction =  $('#orderAction').val();
        var formVoucher = $(this).parent().parent().parent().parent();
        var dataCart;
        $('#orderAction').val('check_voucher');
            jQuery.ajax({
                url: backend_url+'order/ajax/',
                type: "POST",
                dataType: "json",
                data: $("form").serialize(),
                success:function(data){
                    console.log(data);
                    if(data.status==2){
                        alert(data.message);
                        $('.form-after-cart').addClass('hidden');
                        $(formVoucher).find('.voucher-order').addClass('hidden');
                    }
                    dataCart = data.data.cart;
                    $(formVoucher).find('.voucher-order').removeClass('hidden');
                    $(formVoucher).find('#voucher').val(dataCart.currency+' '+dataCart.vouchers.value);
                    $(formVoucher).find('#grandTotal').val(dataCart.currency+' '+dataCart.total);
                }
            });
    });

	// btn-check-user
	$('#userInfo').keypress(function(e){
		var cur = $(this);
        if(e.which == 13){//Enter key pressed
            $('.btn-check-user').click();//Trigger search button click event
            var new_email = $('#userInfo').val();
			if(new_email){
				jQuery.ajax({
				url: backend_url+'order/ajax/',
				type: "POST",
				dataType: "json",
				data: {action: 'check_available_userinfo', new_email : new_email},
				success:function(data){
					if(data.error == 1)
					{ 
						var email=data.email;
						//alert('User tidak terdaftar');
						$('.new_info').addClass('hidden');
						$('.new_user').removeClass('hidden');
						$('.new_service').addClass('hidden');
						$('.new-coordinate').addClass('hidden');
						$('#userName').val('');
						if(email==1)
							$('#userContact').parent().prev().text("Phone Number");
						else				
							$('#userContact').parent().prev().text("Email");
						
					}
					else
					{
						//alert('Email user terdaftar');
						$('.new_info').removeClass('hidden');
						$('.new_service').addClass('hidden');
						$('.new-coordinate').addClass('hidden');
						$('.new_user').addClass('hidden');
						$('#userName').val(data.fuserfirstname);
						$('#userID').val(data.fuserid);
						$('#userToken').val(data.fuserkleentoken);
						get_user_vehicle(data.fuserid,cur);


					}
				},
					error:function (){}
				});
			}
        }else{
        	$(document).on('click','.btn-check-user',function(){
			var new_email = $('#userInfo').val();
			if(new_email){
				jQuery.ajax({
				url: backend_url+'order/ajax/',
				type: "POST",
				dataType: "json",
				data: {action: 'check_available_userinfo', new_email : new_email},
				success:function(data){
					if(data.error == 1)
					{ 
						var email=data.email;
						//alert('User tidak terdaftar');
						$('.new_info').addClass('hidden');
						$('.new_user').removeClass('hidden');
						$('.new_service').addClass('hidden');
						$('.new-coordinate').addClass('hidden');
						$('#userName').val('');
						if(email==1)
							$('#userContact').parent().prev().text("Phone Number");
						else				
							$('#userContact').parent().prev().text("Email");
						
					}
					else
					{
						//alert('Email user terdaftar');
						$('.new_info').removeClass('hidden');
						$('.new_service').addClass('hidden');
						$('.new-coordinate').addClass('hidden');
						$('.new_user').addClass('hidden');
						$('#userName').val(data.fuserfirstname);
						$('#userID').val(data.fuserid);
						$('#userToken').val(data.fuserkleentoken);
						get_user_vehicle(data.fuserid,cur);
					}
				},
					error:function (){}
				});
			}
		});
        }
    });

    // $(document).on('click','.btn-check-user',function()){
    // 	var new_email = $('#userInfo').val();
    // 	if (new_email) {
    // 		jQuery.ajax({
    // 			url: backend_url+'order/ajax'
    // 		});
    // 	}
    // }

	// $(document).on('click','.btn-check-user',function(){
	// 	var new_email = $('#userInfo').val();
	// 	if(new_email){
	// 		jQuery.ajax({
	// 		url: backend_url+'order/ajax/',
	// 		type: "POST",
	// 		dataType: "json",
	// 		data: {action: 'check_available_userinfo', new_email : new_email},
	// 		success:function(data){
	// 			if(data.error == 1)
	// 			{ 
	// 				var email=data.email;
	// 				//alert('User tidak terdaftar');
	// 				$('.new_info').addClass('hidden');
	// 				$('.new_user').removeClass('hidden');
	// 				$('.new_service').addClass('hidden');
	// 				$('.new-coordinate').addClass('hidden');
	// 				$('#userName').val('');
	// 				if(email==1)
	// 					$('#userContact').parent().prev().text("Phone Number");
	// 				else				
	// 					$('#userContact').parent().prev().text("Email");
					
	// 			}
	// 			else
	// 			{
	// 				//alert('Email user terdaftar');
	// 				$('.new_info').removeClass('hidden');
	// 				$('.new_service').addClass('hidden');
	// 				$('.new-coordinate').addClass('hidden');
	// 				$('.new_user').addClass('hidden');
	// 				$('#userName').val(data.fuserfirstname);
	// 				$('#userID').val(data.fuserid);
	// 				$('#userToken').val(data.fuserkleentoken);

	// 			}
	// 		},
	// 			error:function (){}
	// 		});
	// 	}
	// });


	//btn-add-user

	$(document).on('click','.btn-add-user',function(){
		var new_firstname = $('#userFirstname').val();
		var new_lastname = $('#userLastname').val();
		var new_contact = $('#userContact').val();
		var new_info = $('#userInfo').val();
		var cur = $(this);
		$(this).addClass('hidden');	
		if(new_firstname != "" && new_lastname != "" && new_contact != "" && new_info != ""){
			//alert('masuk');
			jQuery.ajax({
			url: backend_url+'order/ajax/',
			type: "POST",
			dataType: "json",
			data: {action: 'add-user', new_contact : new_contact, new_firstname : new_firstname,new_lastname : new_lastname, new_info : new_info},
			beforeSend: function(){
					//$('form').find('button').prop('disabled',true);
					$(cur).parent().find(".new_service").html('Loading...');
			},
			success:function(data){
				if(data.error != 1)
				{ 
					alert('User baru berhasil terdaftar');
					//$('.new_info').removeClass('hidden');
					//$('.new_user').addClass('hidden');
					$('#userName').val(data.fuserfirstname);
					$('#userID').val(data.fuserid);
					$('#userToken').val(data.fuserkleentoken);	
					//
					$('.new_service').removeClass('hidden');
					$('.check-date').removeClass('hidden');
					$('.new-coordinate').removeClass('hidden');
					$('.add-service').removeClass('hidden');
					initMap();
					var curAction =  $('#orderAction').val();
					var cur = $(this);
					$('#orderAction').val('user_vehicle_delete');
					jQuery.ajax({
							url: backend_url+'order/ajax/',
							type: "POST",
							dataType: "json",
							data: $("form").serialize(),
							success:function(data){
							

							}

					});		
				}
			},
				error:function (){}
			});
		}else{
			alert("form masih kosong");
		}
	});

	$(document).on('click','.btn-next-service',function(){ 
		var cur = $(this);
		$('.new_service').removeClass('hidden');
		$('.check-date').removeClass('hidden');
		$('.new-coordinate').removeClass('hidden');
		$('.add-service').removeClass('hidden');
		initMap();
		$(this).addClass('hidden');
		//var action = 'user_vehicle_delete';
		var curAction =  $('#orderAction').val();
		//$('#orderAction').val('user_vehicle_delete'); 
		// jQuery.ajax({
		// 		url: backend_url+'order/ajax/',
		// 		type: "POST",
		// 		dataType: "json",
		// 		data: $("form").serialize(),
		// 		success:function(data){
				

		// 		}

		// });


		
		//$('.check-date').removeClass('hidden');
		//$('.new-coordinate').removeClass('hidden');
		//initMap();
		//get_user_vehicle(fuserid,cur);
		
	});

	$(document).on('click','.btn-next-add-service',function(){
		var curAction =  $('#orderAction').val();
		var cur = $(this);
		$('#orderAction').val('add_user_vehicle');
		jQuery.ajax({
				url: backend_url+'order/ajax/',
				type: "POST",
				dataType: "json",
				data: $("form").serialize(),
				success:function(data){
				
					// $('.new_service').removeClass('hidden');
					// $('.add-service').removeClass('hidden');
					// $('.check-date').removeClass('hidden');
					// $('.new-coordinate').removeClass('hidden');
					// initMap();
					// $(this).addClass('hidden');

				}

		});
			
	});

	$(document).on('click','.btn-next-add-service-again',function(){
		var curAction =  $('#orderAction').val();
		var cur = $(this);
		$('#orderAction').val('add_user_vehicle');
		jQuery.ajax({
				url: backend_url+'order/ajax/',
				type: "POST",
				dataType: "json",
				data: $("form").serialize(),
				success:function(data){
				
					// $('.new_service').removeClass('hidden');
					// $('.add-service').removeClass('hidden');
					// $('.check-date').addClass('hidden');
					// $('.new-coordinate').addClass('hidden');
					// initMap();
					// $(this).addClass('hidden');

				}

		});
			
	});

	$(document).on('change','.change-fleet',function(){
		var cur = $(this);
		var order_status = $(this).data('order-fleet');
		var orderID = $(this).data('order-id');
		var old_fleet = $(this).data('order-fleet');
		var new_status = 2;
		var new_fleet = $(this).val();
		form = $(this).parent().parent();
		if(new_fleet != null){
			if (confirm("Are you sure ?")){
				formdata = 'orderID='+orderID+'&fleet='+new_fleet+'&action=fleet_update';
				action = 'fleet_update';
				do_order_action(form,formdata,action);
				$(cur).data('order-fleet',new_fleet);
				formdata = 'orderID='+orderID+'&status='+new_status+'&action=order_update';
				action = 'order_update';
				do_order_action(form,formdata,action);
				$(cur).data('order-status',new_status);
			}else{
				$(this).val(old_fleet);
			}
		}			
	});
	
	if(detail_page){	
			var currentDate = new Date();
			$('#orderBookingdate').datepicker({
				defaultDate: new Date(),
				//defaultDate: "+1w",
				dateFormat: "yy-mm-dd",
				changeMonth: true,
				changeYear: true,
				maxDate: 30,
				minDate: "dateToday",
				yearRange: "c-50:c+10"
			});
			$("#orderBookingdate").datepicker("setDate", currentDate);

			$(document).on('click','.check-date',function(){
				var curAction =  $('#orderAction').val();
				var cur = $(this);
				//console.log($("form").serialize());
				$('#orderAction').val('check_available_hour');
				jQuery.ajax({
				url: backend_url+'order/ajax/',
				type: "POST",
				dataType: "json",
				data: $("form").serialize(),
				beforeSend: function(){
						//$('form').find('button').prop('disabled',true);
						$(cur).parent().parent().parent().find(".hour-list").html('Loading...');
				},
				success:function(data){	
					$(cur).parent().parent().parent().find(".hour-list").html('');
					$(cur).parent().parent().parent().find('.add-cart').removeClass('hidden');
					console.log(data);
						//$('form').find('button').prop('disabled',false);
						if(data.data.schedules!=null && data.data.schedules!=undefined){
							hours = new Array();
							jQuery.each(data.data.schedules, function(index, element) {
								var cek = hours.indexOf(element.start_hour);
								if(cek<0){
									hours.push(element.start_hour);
									if(element.availability==true){
										optionList = '<div class="radio"><label><input type="radio" name="hour" value="'+ element.start_hour.id +'-'+ element.end_hour.id +'" class="checkbox_single"/>'+ element.start_hour.hour +' - '+ element.end_hour.hour +' <p class="text-success">Available</p></label></div>';												
									}else{
										optionList = '<div class="radio"><label><input type="radio" name="hour" value="'+ element.start_hour.id +'-'+ element.end_hour.id +'" class="checkbox_single disabled"/>'+ element.start_hour.hour +' - '+ element.end_hour.hour +' <p class="text-danger">Not available</p></label></div>';												
									}
									
									jQuery(optionList).appendTo($(cur).parent().parent().parent().find(".hour-list"));
									
								}
							});
						}else{
							/*$(".hour-list .select-hour-first").text('Select Subservice First');	*/
						}
				},
					error:function (){}
				});
			});

		$(document).on('change','.subservice',function() {
			//var lastService = parseInt($('#lastService').val());
			var lastIndex = $(this).attr('data-index');
			console.log(lastIndex);
			var cur = $(this);
			var subserviceID = $(this).val();
			if(subserviceID){
				if(subserviceAjax) subserviceAjax.abort();
				subserviceAjax = jQuery.ajax({
					type: 'POST', 
					url: backend_url+'order/ajax',
					dataType: "json",
					data: {action: 'addon_list', subserviceID: subserviceID},
					beforeSend: function(){
						$(cur).prop('disabled',true);
						$(cur).parent().parent().parent().find(".addon-list ").html('');
					},
					success: function(data){
						console.log(data);
						$(cur).prop('disabled',false);
						if(data.addon!=null && data.addon!=undefined){
							addons = new Array();
							jQuery.each(data.addon, function(index, element) {
								var cek = addons.indexOf(element.faddonid);
								if(cek<0){
									addons.push(element.faddonid);
									//
										optionList = '<div class="checkbox"><label><input type="checkbox" name="order['+lastIndex+'][addon]" value="'+ element.faddonid +'" class="checkbox_single"/>'+ element.faddonname +'</label></div>';	
									//}else{
									//	optionList = '<div class="checkbox"><label><input type="checkbox" name="order[0][addon]" value="'+ element.faddonid +'" class="checkbox_single"/>'+ element.faddonname +'</label></div>';
									//}
									jQuery(optionList).appendTo($(cur).parent().parent().parent().find(".addon-list"));
								}
							});
							
							/*$(cur).parent().parent().parent().find(".addon-list .select-addon-first").text('Select Subsubservice Below');*/
							/*if(selectedSubsubservice!=undefined && selectedSubsubservice!=null && selectedSubsubservice!='')
								change_addon(selectedSubsubservice,$('.addon-list'));*/
						}else{
							//alert("NULL");
							optionList = '<div class="checkbox"><label><input type="hidden" name="order['+lastIndex+'][addon]" value="" class="checkbox_single"/></label></div>';
							jQuery(optionList).appendTo($(cur).parent().parent().parent().find(".addon-list"));
							//alert("NULL");
	
						}
					}
				});
			}
		});

		$(document).on('change','.orderService',function() {
			$('.vehicle-group').removeClass('hidden');
			var cur = $(this);
			var serviceID = $(this).val();

			if(serviceID){
				if(serviceAjax) serviceAjax.abort();
				serviceAjax = jQuery.ajax({
					type: 'POST', 
					url: backend_url+'order/ajax',
					dataType: "json",
					data: {action: 'subservice_list', serviceID: serviceID},
					beforeSend: function(){
						$(cur).prop('disabled',true);
						$(cur).parent().parent().parent().find(".subservice-list option").not('.select-subservice-first').remove();
					},
					success: function(data){
						console.log(data); 
						//console.log($(cur).parent().parent().parent().find(".subservice-list").html());
						$(cur).prop('disabled',false);
						if(data.subservice!=null && data.subservice!=undefined){
							subservices = new Array();
							jQuery.each(data.subservice, function(index, element) {
								var cek = subservices.indexOf(element.fsubserviceid);
								if(cek<0){
									subservices.push(element.fsubserviceid);
									optionList = '<option value="'+ element.fsubserviceid +'" data-price = "'+element.fsubserviceprice+'">'+element.fsubservicename+' - IDR '+element.fsubservicespecialprice+'</option>';
									//console.log(optionList);
									jQuery(optionList).appendTo($(cur).parent().parent().parent().find(".subservice-list"));
								}
							});
							$(cur).parent().parent().parent().find(".subservice-list .select-subservice-first").text('Select Subservice Below');
							/*if(selectedSubservice!=undefined && selectedSubservice!=null && selectedSubservice!='')
								change_subservice(selectedSubservice,$('.subservice-list'));*/
						}else{
							$(".subservice-list .select-subservice-first").text('Select Service First');	
						}
					}
				});
			}

		});

		//adam baru
		$(document).on('change','.orderAddVehicle',function() {
			$('.vehicle-group').removeClass('hidden');
			var cur = $(this);
			var userVehicleID = $(this).val();

			if(userVehicleID){
				if(serviceAjax) serviceAjax.abort();
				serviceAjax = jQuery.ajax({
					type: 'POST', 
					url: backend_url+'order/ajax',
					dataType: "json",
					data: {action: 'subservice_list', userVehicleID: userVehicleID}, 
					beforeSend: function(){
						$(cur).prop('disabled',true);
						$(cur).parent().parent().parent().find(".subservice-list option").not('.select-subservice-first').remove();
					},
					success: function(data){
						//console.log($(cur).parent().parent().parent().find(".subservice-list").html());
						$(cur).prop('disabled',false);
						if(data.subservice!=null && data.subservice!=undefined){
							subservices = new Array();
							jQuery.each(data.subservice, function(index, element) {
								var cek = subservices.indexOf(element.fsubserviceid);
								if(cek<0){
									subservices.push(element.fsubserviceid);
									optionList = '<option value="'+ element.fsubserviceid +'" data-price = "'+element.fsubserviceprice+'">'+element.fsubservicename+' - IDR '+element.fsubservicespecialprice+'</option>';
									console.log(optionList);
									jQuery(optionList).appendTo($(cur).parent().parent().parent().find(".subservice-list"));
								}
							});
							$(cur).parent().parent().parent().find(".subservice-list .select-subservice-first").text('Select Subservice Below');
							/*if(selectedSubservice!=undefined && selectedSubservice!=null && selectedSubservice!='')
								change_subservice(selectedSubservice,$('.subservice-list'));*/
						}else{
							$(".subservice-list .select-subservice-first").text('Select Service First');	
						}
					}
				});
			}

		});

		$(document).on('change','.brandName',function() {
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
						$(cur).parent().parent().parent().find(".vehicle-list option").not('.select-vehicle-first').remove();
						$(cur).parent().parent().parent().find(".year-list option").not('.select-year-first').remove();
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
									console.log(optionList);
									jQuery(optionList).appendTo($(cur).parent().parent().parent().find(".vehicle-list"));
								}
							});
							$(cur).parent().parent().parent().find(".vehicle-list .select-vehicle-first").text('Select Vehicle Below');
							if(selectedVehicle!=undefined && selectedVehicle!=null && selectedVehicle!='')
								change_vehicle(selectedVehicle,$('.vehicle-list'));
						}else{
							$(".vehicle-list .select-vehicle-first").text('Select Brand First');	
						}
					}
				});
			}
		});	

		$(document).on('change','.vehicle-list',function() {
			var cur = $(this);
			var vehicle_id = $(this).val();
			if(vehicle_id){
				change_year(vehicle_id,cur);
				change_color(vehicle_id,cur);
				change_transmission(vehicle_id,cur);
				change_user_vehicle(vehicle_id,cur)
			}
		});

		function change_user_vehicle(vehicle_id,cur){
			var user_id = $('form').find('#userID').val();
			if(vehicleUser_Ajax){vehicleUser_Ajax.abort();}
			vehicleUser_Ajax = jQuery.ajax({
				type: 'POST',
				url: backend_url+'order/ajax/',
				dataType: "json",
				data: {action: 'user_vehicle_id', vehicle_id : vehicle_id, user_id : user_id},
				beforeSend: function(){
					
				},
				success: function(data){

					if (data.user_vehicle!=null && data.user_vehicle!=undefined) {
						user_vehicles = new Array();
						jQuery.each(data.user_vehicle, function(index, element) {
							var cek = user_vehicles.indexOf(element.fuservehicleid);
							if (cek<0) {
								user_vehicles.push(element.fuservehicleid);
								$(cur).parent().parent().parent().find(".user_vehicle_id").val(element.fuservehicleid);
							}
						});
					}
					else{
						$(cur).parent().parent().parent().find(".select-year-first").text('Select Vehicle First');	
					}
					//alert(data.user_vehicle);
				}
			});
		}	
	

	function get_user_vehicle(fuserid,cur){

		if (user_vehicleAjax){user_vehicleAjax.abort()} 
		user_vehicleAjax = jQuery.ajax({
			type: 'POST',
			url: backend_url+'user/ajax/',
			dataType: "json",
			data: {action: 'user_vehicle', fuserid : fuserid},
			success:function(data){
				//console.log(data);
				user_vehicle = new Array();		
				var i = 1;	
				jQuery.each(data.user_vehicle, function(index, element) { 
					//alert(element.fuservehicleid);
					var cek = user_vehicle.indexOf(element.fuservehicleid); 
					 	if(cek<0){
							user_vehicle.push(element.fuservehicleid);

							optionList = '<option value="'+ element.fuservehicleid +'"> Vehicle '+i+++'</option>';
							jQuery(optionList).appendTo($(cur).parent().parent().parent().parent().find(".orderAddVehicle"));
							//console.log(jQuery(optionList).appendTo($(cur).parent().parent().parent().parent().find(".orderAddVehicle")));
							// optionList = '<option value="'+ element.fuservehicleid +'"> Add Vehicle Baru </option>';
							// jQuery(optionList).appendTo($(cur).parent().parent().parent().parent().find(".orderAddVehicle"));

							console.log(optionList);
						}
				});
			}
		});
	}


	$(document).on('change','.orderAddVehicle',function() {
		var cur = $(this);
		var data_vehicle_id = $(this).val();
		if(data_vehicle_id == null){
			change_brand(data_vehicle_id,cur);
			// change_year(vehicle_id,cur);
			// change_color(vehicle_id,cur);
			// change_transmission(vehicle_id,cur);
			// change_user_vehicle(vehicle_id,cur)
		}else{
			alert('error');
		}
	});

	function change_brand(data_vehicle_id,cur){  
		jQuery.ajax({
			type: 'POST',
			url: backend_url+'user/ajax/',
			dataType: "json",
			data: {action: 'brand_list', data_vehicle_id : data_vehicle_id}, 
			success: function(data){
				//console.log(data); 
				$(cur).parent().parent().parent().find(".brandName").prop('disabled',false);
				if(data.brand_list != null && data.brand_list != undefined){
					console.log(data.brand_list);
					brands = new Array(); 
					jQuery.each(data.brand_list, function(index, element) {
						var cek = brands.indexOf(element.fuservehicleid); 
						if(cek<0){
							brands.push(element.fuservehicleid);	 										
		
							optionList = '<option value="'+ element.fbrandname +'">'+element.fbrandname+'</option>'; 
							jQuery(optionList).appendTo($(cur).parent().parent().parent().parent().find(".brandName"));

							optionList = '<option value="'+ element.fvehiclename +'">'+element.fvehiclename+'</option>'; 
							jQuery(optionList).appendTo($(cur).parent().parent().parent().parent().find(".vehicle-list"));

							optionList = '<option value="'+ element.year +'">'+element.year+'</option>'; 						
							jQuery(optionList).appendTo($(cur).parent().parent().parent().parent().find(".year-list"));

							optionList = '<option value="'+ element.color +'">'+element.color+'</option>'; 						
							jQuery(optionList).appendTo($(cur).parent().parent().parent().parent().find(".color-list"));

							optionList = '<option value="'+ element.transmission +'">'+element.transmission+'</option>'; 					
							jQuery(optionList).appendTo($(cur).parent().parent().parent().parent().find(".transmission-list"));

							optionList = '<input value="'+ element.fvehicleplatno +'">'; 					
							jQuery(optionList).appendTo($(cur).parent().parent().parent().parent().find(".plate_no-list"));
						}
					});
					//$(cur).parent().parent().parent().find(".select-year-first").text('Select Year Below'); 
				}else{
					alert('error');
				} 
			}
		});
	}

	$(document).on('change','.orderAddVehicle',function(){
        var dataCart;
        var cur = $(this);
        //$('#orderAction').val('check_voucher');
            jQuery.ajax({
                url: backend_url+'order/ajax/',
                type: "POST",
                dataType: "json",
                data: $("form").serialize(),
                success:function(data){
                    console.log(data);
                    
                    dataCart = data.data.cart;
                    $(cur).parent().parent().parent().find('#brandName').val(dataCart.value);
                    $(cur).parent().parent().parent().find('#vehicleID').val(dataCart.total);
                }
            });
    });

	function change_year(vehicle_id,cur){
		if(vehicleAjax){vehicleAjax.abort();}
		vehicleAjax = jQuery.ajax({
			type: 'POST',
			url: backend_url+'setting/ajax/',
			dataType: "json",
			data: {action: 'year_list', vehicle_id : vehicle_id},
			beforeSend: function(){
				$(cur).parent().parent().parent().find(".year-list").prop('disabled',true);
				$(cur).parent().parent().parent().find(".year-list option").not('.select-year-first').remove();
			},
			success: function(data){
				$(cur).parent().parent().parent().find(".year-list").prop('disabled',false);
				//$("#vehicleYear").prop('disabled',false);
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
							
							jQuery(optionList).appendTo($(cur).parent().parent().parent().find(".year-list"));
						}
					});
					$(cur).parent().parent().parent().find(".select-year-first").text('Select Year Below');
				}else{
					$(cur).parent().parent().parent().find(".select-year-first").text('Select Vehicle First');	
				} 
			}
		});
	}	
}

function change_color(vehicle_id,cur){
	jQuery.ajax({
		type: 'POST',
		url: backend_url+'setting/ajax/',
		dataType: "json",
		data: {action: 'color_list', vehicle_id : vehicle_id},
		beforeSend: function(){
			$(cur).parent().parent().parent().find(".color-list").prop('disabled',true);
			$(cur).parent().parent().parent().find(".color-list option").not('.select-color-first').remove();
		},
		success: function(data){
			$(cur).parent().parent().parent().find('.color-list').prop('disabled',false);
			//$("#vehicleColor").prop('disabled',false);
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
						
						jQuery(optionList).appendTo($(cur).parent().parent().parent().find(".color-list"));
					}
				});
				$(cur).parent().parent().parent().find(".select-color-first").text('Select Color Below');
			}else{
				$(cur).parent().parent().parent().find(".select-color-first").text('Select Vehicle First');	
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
			$(cur).parent().parent().parent().find('.transmission-list').prop('disabled',true);
			$(".transmission-list option").not('.select-transmission-first').remove();
		},
		success: function(data){
			$(cur).parent().parent().parent().find(".transmission-list").prop('disabled',false);
			//$("#vehicleTransmission").prop('disabled',false);
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
						
						jQuery(optionList).appendTo($(cur).parent().parent().parent().find(".transmission-list"));
					}
				});
				$(cur).parent().parent().parent().find(".select-transmission-first").text('Select Transmission Below');
			}else{
				$(cur).parent().parent().parent().find(".select-transmission-first").text('Select Vehicle First');	
			}
		}
	});	
}

	$(document).on('click',".add-transm", function(e) {
		lastTransm = parseInt($('#lastTransm').val());
		curTransm = $('#newTransm .new-transm .transmImage').attr('name');
		newTransm = curTransm;
		$('#newTransm .new-transm .transmImage').attr('name',newTransm).attr('id',newTransm);
		$('#newTransm .new-transm label').attr('for',newTransm);
		$(this).parent().parent().parent().parent().find('tbody').append($('#newTransm .new-transm').html());
		$('#newTransm .new-transm .transmImage').attr('name',curTransm).attr('id',curTransm);	
		$('#newTransm .new-transm label').attr('for',curTransm);
		$('#lastTransm').val(lastTransm+1);
		return false;
	});

	$(document).on('click',".add-service", function(e) {
		lastService = parseInt($('#lastService').val());

		//$(this).addClass('hidden');

		$('.new-addservice').find('.new-Service').each(function(){
			newName = $(this).attr('name').replace("#n",lastService);
			newID = $(this).attr('id').replace("#n",lastService);
			$(this).attr('name',newName).attr('id',newID);
			console.log($(this).attr('name',newName).attr('id',newID));
		});

		$('.new-addservice').find('.label-new-service').each(function(){
			newService = $(this).attr('for').replace("#n",lastService);
			$(this).attr('for',newService);

		});

		$('.new-addservice').find('.subservice-list').attr('data-index',lastService);
		$(this).parent().parent().parent().parent().find('.pack-addservice').append($('#newService').html());
		$('#lastService').val(lastService+1);

		// //$('.new_service').addClass('hidden');
		// //$('.add-service').addClass('hidden');
		// $('.check-date').addClass('hidden');
		// $('.new-coordinate').addClass('hidden');
		// initMap();
		
		return false;
	});

});
	
function load_available_fleet(){
	jQuery('.change-fleet').each(function(){
		var cur = $(this);
		var booking_date = jQuery(this).data('booking-date');
		var hour_id =jQuery(this).data('hourid');
		var orderStatus = jQuery(this).data('order-status');
		if(orderStatus == 1 ){
			jQuery.ajax({
				type: 'POST',
				url: backend_url+'order/ajax/',
				dataType: "json",
				data: {action: 'get_available_fleet', booking_date : booking_date, hour_id :hour_id},
				beforeSend: function(){
					/*$(form).find('#orderFleet').prop('disabled',false);*/
					/*$(cur).not('.select-fleet-first').remove();
	*/			},
				success: function(data){
					$(form).find('#orderFleet').prop('disabled',true);
					optionList = '<option value="">Select Fleet First</option>';
					jQuery.each(data, function(index, element) {
						optionList = optionList  + '<option value="'+ element.ffleetid +'">'+element.ffleetname+'</option>';
					});
					$(cur).html(optionList);
				}
			});
		}
	});
}


function do_order_action(form,formdata,action){
	
	jQuery.post( backend_url+'order/ajax/', formdata, function(data)
	{
		var redirectUrl = backend_url+'order/';
		console.log(data);
		if(data=='success'){
			switch(action){
				case 'order_update_status_bulk':
				case 'order_delete':	
					window.location.assign(redirectUrl);
					break;
				case 'fleet_update':
					load_available_fleet();
					// $(form).find('#orderFleet').prop('disabled',true)
					$(form).find('#orderProcess option[value = 1]').prop('selected',false);
					$(form).find('#orderProcess option[value = 2]').prop('selected',true);
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