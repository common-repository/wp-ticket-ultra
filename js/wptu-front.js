if(typeof $ == 'undefined'){
	var $ = jQuery;
}
(function($) {
    jQuery(document).ready(function () { 
	
	   "use strict";
	   
	   
	   $("#wptu-registration-form").validationEngine({promptPosition: 'inline'});
	   $("#wptu-client-form").validationEngine({promptPosition: 'inline'});
	   
	  
	   
   
	   // Adding jQuery Datepicker
		jQuery(function() {
			
			var uultra_date_format =  jQuery('#uultra_date_format').val();			
			if(uultra_date_format==''){uultra_date_format='dd/mm/yy'}
			
			//alert(uultra_date_format);
			
			//jQuery( ".bupro-datepicker" ).datepicker({ showOtherMonths: true, dateFormat: uultra_date_format,  minDate: 0 });
		
			jQuery("#ui-datepicker-div").wrap('<div class="ui-datepicker-wrapper" />');
		});
		
		
	
	jQuery(document).on("click", ".wptu-location-front", function(e) {						
			
			var dep_id =  jQuery(this).attr("depto-id");
						
    		jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "wptu_get_custom_department_fields", "department_id": dep_id },
					
					success: function(data){							
							
																
						jQuery("#wp-custom-fields-public").html(data);											
										    
						

						}
				});			
			
			 
				
        });
		
		
		jQuery(document).on("click", "#wptu-btn-regist-confirm", function(e) {
			
					
				$("#wptu-client-form").submit();
					
			
									
    		e.preventDefault();		 
				
        });
		

		
				
		
	
			
	
		jQuery(document).on("click", "#wptu-btn-app-confirm", function(e) {
			
			e.preventDefault();				
			var frm_validation  = $("#wptu-registration-form").validationEngine('validate');	
			
			//check if user is a staff member trying to purchase an own service
			
			if(frm_validation)
			{
							
				var myRadioPayment = $('input[name=bup_payment_method]');
				var payment_method_selected = myRadioPayment.filter(':checked').val();				
				var payment_method =  jQuery("#bup_payment_method_stripe_hidden").val();
								
				if(payment_method=='stripe' && payment_method_selected=='stripe')
				{
					
					var wait_message = '<div class="bup_wait">' + bup_pro_front.wait_submit + '</div>';				
					jQuery('#bup-stripe-payment-errors').html(wait_message);					
					//bup_stripe_process_card();
				
				
				
				}else{
					
					//alert('other then submit');
					
					$("#wptu-registration-form").submit();
				
				}
				
				
			}else{
				
				
				
			}
			
			
									
    		e.preventDefault();		 
				
        });
		
 
       
    }); //END READY
})(jQuery);







