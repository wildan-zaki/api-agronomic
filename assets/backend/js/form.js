            //$(".status-button").on('click',function() {
                $(document).on('change','.status-button',function(e){
              //event.preventDefault();
                var id = $(this).data('btn');
                var statusid = $('#form'+id).find('input[name="fproductstatus"]').val();//MANTAP
                //alert (statusid);
                var Data = $('#form'+id).serialize();
                $.ajax({
                    type: "POST",
                    url: backend_url+'/',
                    data: Data,
                    success: function(res) {
                        if(statusid == 1){
                        $('#form'+id).find('input[name="fproductstatus"]').val(0);
                        $('button[data-btn="'+id+'"]').removeClass('glyphicon-ok btn-success').addClass('glyphicon-remove btn-danger');
                        $('button[data-btn="'+id+'"]').attr( 'data-statusid','0');
                        }else{
                        $('#form'+id).find('input[name="fproductstatus"]').val(1);
                        $('button[data-btn="'+id+'"]').removeClass('glyphicon-remove btn-danger').addClass('glyphicon-ok btn-success');
                        $('button[data-btn="'+id+'"]').attr( 'data-statusid','1');
                        }
                    },
                });
            });
jQuery(document).ready(function($){                 
    $('.coupon-delete-list').on('click',function(){
        if (confirm("Are you sure ? This action is undoable!")) {
            var action = 'coupon_delete';
            var form = $(this).parent().parent().parent();
            formdata = 'couponID='+ $(this).data('coupon-id')+'&action='+action;
            do_coupon_action(form,formdata,action);
        }
        return false;
    });
    
    if(detail_page){            
        $('.coupon-form').submit(function(event){
            needToConfirm = false;
            var action = $('#couponAction').val();
            var form = $(this);
            $(form).find('button').prop('disabled',true);
            $('.btn-save-coupon').button('loading');
            $form = $(this).closest('form');
            if(action == 'coupon_delete'){
                formdata = $(this).serialize();
                do_coupon_action(form,formdata,action);
                return false;
            }
        });
        
        $(document).on('change','.select-conditions',function(){
            var cond_type = $(this).val();
            if(cond_type=='event'){
                form = $(this).parent().parent().find('.select-value');
                $(this).parent().parent().find('.select-value').removeClass('hidden').attr('name','condition_value[]');
                $(this).parent().parent().find('.cond-input-value').addClass('hidden').attr('name','condition_value_select[]');
                $(this).parent().parent().find('.select-operators option').each(function(event){
                    var opval = $(this).attr('value');
                    if(opval!='==') $(this).addClass('hidden');
                    else $(this).prop('selected',true);
                });
                action = 'coupon_event';
                formdata = 'action='+action;
                do_coupon_action(form,formdata,action);
            }else{
                $(this).parent().parent().find('.cond-input-value').removeClass('hidden').attr('name','condition_value[]');
                $(this).parent().parent().find('.select-value option').remove();
                $(this).parent().parent().find('.select-value').addClass('hidden').attr('name','condition_value_select[]');
                $(this).parent().parent().find('.select-operators option').removeClass('hidden');
            }
        });
            
        $(document).on('click',".add-coupon", function(event) {
            $(this).parent().parent().parent().parent().find('tbody').append($('#newCoupon .new-coupon').html());               
            return false;
        });
        
        $(document).on('click',".remove-coupon", function(event) {
            if (confirm("Are you sure ? This action is undoable!")) {   
                $(this).parent().parent().remove();
            }
            return false;
        });
        
        $('#couponStartDate, #couponEndDate').datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
        });
    }
});
        
function do_coupon_action(form,formdata,action){
    
    jQuery.post( backend_url+'coupon/ajax/', formdata, function(data)
    {
        if(form!=undefined && form!=null && form!=''){
            if(!$(form).hasClass('coupon-list')){
                $(form).find('button').prop('disabled',false);
                $(form).find('.btn-save-coupon').button('reset');
            }else{
                $(form).remove();   
            }
        }
        if(action!='coupon_event'){
            if(data=='success'){
                switch(action){
                    case 'coupon_status':
                        $(form).attr('data-status',newStatus);          
                        check = (newStatus==1) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>';
                        $(form).html(check);
                        break;
                    case 'delete_bulk':
                    case 'coupon_delete':
                        window.location.assign(backend_url+'coupon/');
                        break;
                        break;
                    default:
                        $('.btn-save-event').parent().parent().append('<div class="alert alert-success success-event col-sm-12 text-center" role="alert"><strong>Event Updated Successfully</strong></div>');
                        setTimeout(function(){
                            $('.success-event').remove();
                        },5000);
                        break;  
                }
            }
        }else{
            console.log(data);
            $(form).html(data); 
        }
    }); 
}