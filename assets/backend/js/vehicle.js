jQuery(document).ready(function($){		 
			
	$('.vehicle-delete-list').on('click',function(){
		if (confirm("Are you sure ? This action is undoable!")) {
			var action = 'vehicle_delete';
			var form = $(this).parent().parent().parent();
			formdata = 'vehicleID='+ $(this).data('vehicle-id')+'&action='+action;
			do_vehicle_action(form,formdata,action);
		}
		return false;
	});


	$('.remove-meta').on('click',function(){
		var vehicleid = $(this).data('vehicle-id');
		var meta = $(this);
		
			if (vehicleid){
				if (confirm("Are you sure ? This action is undoable!")) {
					var action = 'vehicle_delete_meta';
					var form = $(this).parent().parent();
					formdata = 'vehicleID='+ $(this).data('vehicle-id')+'&vehicleMeta='+$(this).data('meta-id')+'&action='+action;
					do_vehicle_action(form,formdata,action,meta);
				}
			}else{
				//alert(vehicleid);
				$(form).remove();
			}
		return false; 
	});
	
	if(detail_page){
		$('.vehicle-form').submit(function(event){
			needToConfirm = false;
			lastTransm = parseInt($('#lastTransm').val());
			lastYear = parseInt($('#lastYear').val());
			lastColor = parseInt($('#lastColor').val());
			var action = $('#vehicleAction').val();
			var form = $(this);
			if( lastTransm < 1 || lastYear < 1 || lastColor < 1){
			alert("Form cannot empty, please complete the data")
			}else{
				$(form).find('button').prop('disabled',true);
				$('.btn-save-event').button('loading');
				$form = $(this).closest('form');
				if(action == 'vehicle_image_delete' || action == 'vehicle_delete'){
					formdata = $(this).serialize();
					do_vehicle_action(form,formdata,action);
					return false;
				}	
			}
		});

		$(document).on('click',".deleteImage", function(event) {
			if (confirm("Are you sure ? This action is undoable!")) {
				var action = 'vehicle_image_delete';
				var form = $(this).parent().parent().parent();
				formdata = 'vehicleID='+ $(this).data('vehicle-id')+'&action='+action+'&imageID='+$(this).data('image-id');
				do_vehicle_action(form,formdata,action);
			}
		});

		//add field
		$(document).on('click',".add-year", function(e) {
			lastYear = parseInt($('#lastYear').val());
			curYear = $('#newYear .new-year .yearImage').attr('name');
			newYear = curYear;
			$('#newYear .new-year .yearImage').attr('name',newYear).attr('id',newYear);
			$('#newYear .new-year label').attr('for',newYear);
			$(this).parent().parent().parent().parent().find('tbody').append($('#newYear .new-year').html());
			$('#newYear .new-year .yearImage').attr('name',curYear).attr('id',curYear);	
			$('#newYear .new-year label').attr('for',curYear);
			$('#lastYear').val(lastYear+1);
			return false;
		});

		$(document).on('click',".add-color", function(e) {
			lastColor = parseInt($('#lastColor').val());
			curColor = $('#newColor .new-color .colorImage').attr('name');
			newColor = curColor;
			$('#newColor .new-color .colorImage').attr('name',newColor).attr('id',newColor);
			$('#newColor .new-color label').attr('for',newColor);
			$(this).parent().parent().parent().parent().find('tbody').append($('#newColor .new-color').html());
			$('#newColor .new-color .colorImage').attr('name',curColor).attr('id',curColor);	
			$('#newColor .new-color label').attr('for',curColor);
			$('#lastColor').val(lastColor+1);
			return false;
		});

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
		//remove field
		$(document).on('click',".remove-year", function(e) {
			var form = $(this).parent().parent().parent();
			lastYear = parseInt($('#lastYear').val());
			curYear = $('#newYear .new-year .yearImage').attr('name');
			newYear = curYear;
			$(form).remove('div:last-child');
			
			$('#lastYear').val(lastYear-1);
			return false;
		});
		var max_fields      = 20; //maximum input boxes allowed
	    var wrapper_year         	= $(".input_wrap_year"); //Fields wrapper
	    var wrapper_color         	= $(".input_wrap_color"); //Fields wrapper
	    var wrapper_cctransm        = $(".input_wrap_cctransm"); //Fields wrapper
	    var add_button_year      	= $(".add_field_year"); //Add button ID
	    var add_button_color      	= $(".add_field_color"); //Add button ID
	    var add_button_cctransm     = $(".add_field_cctransm"); //Add button ID
	   
	    var x = 0; //initlal text box count

	   /* $(add_button_year).click(function(e){ //on add input button click
	        e.preventDefault();
	        if(x < max_fields){ //max input box allowed
	            x++; //text box increment
	            $(wrapper_year).append('<div><input type="text" class="form-control" id="vehicleYear" name="vehicleYear['+x+']" placeholder="Year" value=""><a href="#" class="remove_field remove-meta">Remove</a></div>'); //add input box
	        }
	    });

	    $(add_button_color).click(function(e){ //on add input button click
	        e.preventDefault();
	        if(x < max_fields){ //max input box allowed
	            x++; //text box increment
	            $(wrapper_color).append('<div><input type="text" class="form-control" id="vehicleColor" name="vehicleColor['+x+']" placeholder="Color" value=""><a href="#" class="remove_field remove-meta">Remove</a></div>'); //add input box
	        }
	    });

	    /*$(wrapper_year).on("click",".remove_field", function(e){ //user click on remove text
	        e.preventDefault(); $(this).parent('div').remove(); x--;
	    });

	    $(wrapper_color).on("click",".remove_field", function(e){ //user click on remove text
	        e.preventDefault(); $(this).parent('div').remove(); x--;
	    });*/
	}
});
		
function do_vehicle_action(form,formdata,action,meta){
	
	jQuery.post( backend_url+'vehicle/ajax/', formdata, function(data)
	{
		var redirectUrl = backend_url+'vehicle/';
		if(data=='success'){
			switch(action){
				case'vehicle_delete_meta':
					$(meta).parent().parent().parent().remove();
				break;
				case 'vehicle_status':
					$(form).attr('data-status',newStatus);			
					check = (newStatus) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>';
					$(form).html(check);
					break;
				case 'vehicle_image_delete':
					$('#currentVehicleImage').attr('src',no_image);
					$('#deleteVehicleImage').hide();
					break;
				case 'delete_bulk':
				case 'vehicle_delete':
					window.location.assign(backend_url+'vehicle/');
					break;	
			}
		}
	});	
}