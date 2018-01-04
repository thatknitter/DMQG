(function( $ ) {
    $(function() {
         
        // Add Color Picker to all inputs that have 'color-field' class
        $( '.cpa-color-picker' ).wpColorPicker();
         
    });
    
    $(document).ready(function() {	
    
    	if ($("#dynamic_ud_cimy_plugin").val().indexOf("INSTALLED AND ACTIVE") > -1) 
    		$("#cimy_key_names").show();
    	else
    		$("#cimy_key_names").hide();
    	
    });
    
    $(document).ready(function() {	
    	if ($("#ud_show_srch").is(':checked'))
    		$("#ud_srch_style").show();
    	else
    		$("#ud_srch_style").hide();
    });
    
    $(document).ready(function() {	
    	if ($("#user_directory_show_avatars").is(':checked'))
    		$("#user_directory_avatar_style").show();
    	else
    		$("#user_directory_avatar_style").hide();
    });
    
    $(document).ready(function() {	
    	if ($("#ud_author_page").is(':checked')) {	
    		$("#ud_target_window").show();
			$("#open_linked_page").show();
			$('#ud_auth_or_bp').show();
			
			if($('#ud_auth_or_bp').length)
			{
				if($('#ud_auth_or_bp').val() === 'auth')
					$("#show-auth-pg-lnk").show();
				else 
					$("#show-auth-pg-lnk").hide();
			}
			else
				$("#show-auth-pg-lnk").show();
		}
    	else {			
		    $("#show-auth-pg-lnk").hide();
    		$("#ud_target_window").hide();	
			$("#open_linked_page").hide();
			$('#ud_auth_or_bp').hide();
		}
    });
    
    $(function() {
   	$('#ud_author_page').change(function() {
			$("#open_linked_page").toggle(this.checked);
       		$("#ud_target_window").toggle(this.checked);
			$('#ud_auth_or_bp').toggle(this.checked);
			
			if($('#ud_auth_or_bp').length)
			{
				if($('#ud_auth_or_bp').val() === 'auth' && $('#ud_author_page').is(':checked'))
					$("#show-auth-pg-lnk").show();
				else 
					$("#show-auth-pg-lnk").hide();
			}
			else
			{
				$("#show-auth-pg-lnk").toggle(this.checked);
			}
   	});
    });
	
	$(function() {
   	$('#ud_auth_or_bp').change(function() {
       					
			if($('#ud_auth_or_bp').val() === 'bp')
			{
				$("#show-auth-pg-lnk").hide();
			}
			else 
			{
				$("#show-auth-pg-lnk").show();
			}
   	});
    });
    
    $(document).ready(function() {
    	if($('#user_directory_border').val() == 'no_border') {    	 	
    		$("#border-settings").hide();  		 
   	}
    	else
    	{
    		$("#border-settings").show();
    	} 
    }); 	
    
    $(function() {
   	$('#user_directory_border').change(function() {
       		if($('#user_directory_border').val() == 'no_border') {    	 	
    			$("#border-settings").hide();  		 
   		}
    		else
    		{
    			$("#border-settings").show();
    		} 
   	});
    });
    
    $(function() {
   	$('#user_directory_show_avatars').change(function() {
       		$("#user_directory_avatar_style").toggle(this.checked);
   	});
    });
    
    $(function() {
   	$('#ud_show_srch').change(function() {
       		$("#ud_srch_style").toggle(this.checked);
   	});
    });
    
    $(document).ready(function() {
    	if($('#ud_directory_type').val() == 'all-users') {    	 	
    		$("#one-page-dir-type-a").show();   	 	
    		$("#letter-link-dir-type").hide();
			$("#show_srch_results").hide();			
    		
    		if($('#ud_letter_divider').val() !== 'nld')
    		     $("#one-page-dir-type-b").show();  
    		else   
    		     $("#one-page-dir-type-b").hide();  	   		 
   	}
    	else
    	{   		
    		$("#one-page-dir-type-a").hide();  
    		$("#one-page-dir-type-b").hide();  	 	
    		$("#letter-link-dir-type").show(); 
			$("#show_srch_results").show();
    	} 
    }); 	
    
    $(function() {
   	$('#ud_directory_type').change(function() {
		    
       		if($('#ud_directory_type').val() == 'all-users') { 
       			$("#one-page-dir-type-a").show();   	 	
    			$("#letter-link-dir-type").hide();
				$("#show_srch_results").hide();		
    			
    			if($('#ud_letter_divider').val() !== 'nld')
    		     		$("#one-page-dir-type-b").show();  
    			else   
    		     		$("#one-page-dir-type-b").hide();  	  		 
			}
    		else
    		{
				
    			$("#one-page-dir-type-a").hide(); 
    			$("#one-page-dir-type-b").hide();  	 	
    			$("#letter-link-dir-type").show(); 
				$("#show_srch_results").show();						
    		} 
   	});
    });
      
    $(document).ready(function() {	
    	if ($("#user_directory_website").is(':checked'))
    		$("#Website").show();
    	else
    		$("#Website").hide();    		
    });  
    
    $(function() {
   	$('#user_directory_website').change(function() {
       		if ($("#user_directory_website").is(':checked'))
    			$("#Website").show();
   		else
   			$("#Website").hide(); 
    			
    		var Order = $("#sortable").sortable('toArray').toString();
                $('#user_directory_sort_order').val(Order);
    	   });
    });
    
    $(function() {
   	$('#ud_letter_divider').change(function() {
       		if ($("#ud_letter_divider").val() == 'ld-ds' || $("#ud_letter_divider").val() == 'ld-fl')
    			$("#one-page-dir-type-b").show();
   		else
   			$("#one-page-dir-type-b").hide(); 
    			
    	   });
    });
    
     $(document).ready(function() {	
    	if( ( $("#ud_letter_divider").val() == 'ld-ds' || $("#ud_letter_divider").val() == 'ld-fl' ) 
    		&& $('#ud_directory_type').val() == 'all-users')
    			$("#one-page-dir-type-b").show();
    	else
    		$("#one-page-dir-type-b").hide();  		
    });
    
    $(document).ready(function() {	
    	if ($("#user_directory_email").is(':checked'))
    		$("#Email").show();
    	else
    		$("#Email").hide();    		
    });  
    
    $(function() {
   	$('#user_directory_email').change(function() {
       		if ($("#user_directory_email").is(':checked'))
    			$("#Email").show();
   		else
   			$("#Email").hide(); 
    			
    		var Order = $("#sortable").sortable('toArray').toString();
                $('#user_directory_sort_order').val(Order);
    	   });
    });
    
    $(document).ready(function() {	
    	if ($("#user_directory_address").val() == "1")
    	{
    		$("#street1").hide();
    		$("#street2").hide();
    		$("#city").hide();
    		$("#state").hide();
    		$("#zip").hide();
			
			$("#user_directory_addr_1").val("");
			$("#user_directory_addr_2").val("");
			$("#user_directory_city").val("");
			$("#user_directory_state").val("");
			$("#user_directory_zip").val("");	
    		
    		$("#Address").hide(); 
			
			$("#address-down-arrow").show();
			$("#address-up-arrow").hide();
			
    	}
    	else
    	{
    		$("#street1").show();
    		$("#street2").show();
    		$("#city").show();
    		$("#state").show();
    		$("#zip").show();
						
			$("#Address").show(); 
			
			$("#address-up-arrow").show();
			$("#address-down-arrow").hide();
			
    	}
    });
    
	$( "#address-down-arrow" ).click(function() {
		
		    $("#street1").show();
    		$("#street2").show();
    		$("#city").show();
    		$("#state").show();
    		$("#zip").show();
			
		    $("#address-up-arrow").show();
			$("#address-down-arrow").hide();
			
			$("#Address").show(); 
			
			$("#user_directory_address").val("");
			
			var Order = $("#sortable").sortable('toArray').toString();
                $('#user_directory_sort_order').val(Order);
	});
	
	$( "#address-up-arrow" ).click(function() {
		
		    $("#street1").hide();
    		$("#street2").hide();
    		$("#city").hide();
    		$("#state").hide();
    		$("#zip").hide();
			
			$("#user_directory_addr_1").val("");
			$("#user_directory_addr_2").val("");
			$("#user_directory_city").val("");
			$("#user_directory_state").val("");
			$("#user_directory_zip").val("");	
			
			$("#Address").hide(); 
    		
			$("#address-down-arrow").show();
			$("#address-up-arrow").hide();
			
			$("#user_directory_address").val("1");
			
			var Order = $("#sortable").sortable('toArray').toString();
                $('#user_directory_sort_order').val(Order);
	});
    
    $(document).ready(function() {
		for(var i=1; i<11; i++)
		{	
			if($('#user_directory_num_meta_flds').val() >= i)     	 	
			{
				$("#meta_fld_" + i).show(); 
				$("#MetaKey" + i).show();   		 
			}
			else
			{
				$("#meta_fld_" + i).hide(); 
				$("#MetaKey" + i).hide();   
			}      
		}
		
		for(var iSrch=1; iSrch<16; iSrch++)
		{
           		
			if($('#user_directory_num_meta_srch_flds').val() >= iSrch)     	 	
			{
				 
				$("#meta_srch_fld_" + iSrch).show(); 
			}
			else
			{
				$("#meta_srch_fld_" + iSrch).hide(); 
			}      
		}
    }); 
    
    
    $(function() {
   	$('#user_directory_num_meta_flds').change(function() {
		
       		for(var i=1; i<11; i++)
				{	
					if($('#user_directory_num_meta_flds').val() >= i)     	 	
					{

						$("#meta_fld_" + i).show(); 

						$("#MetaKey" + i).show();   		 

					}

					else
					{

						$("#meta_fld_" + i).hide(); 

						$("#MetaKey" + i).hide();

						$("#user_directory_meta_field_" + i).val("");

						$("#user_directory_meta_label_" + i).val("");

					}      
				}	
    		
    		var Order = $("#sortable").sortable('toArray').toString();
                $('#user_directory_sort_order').val(Order); 
   	});
    });
    
	$(function() {
   	$('#user_directory_num_meta_srch_flds').change(function() {
		
       		for(var i=1; i<16; i++)
				{	
					if($('#user_directory_num_meta_srch_flds').val() >= i)     	 	
					{

						$("#meta_srch_fld_" + i).show();  		 

					}

					else
					{

						$("#meta_srch_fld_" + i).hide(); 

						$("#user_directory_meta_srch_field_" + i).val("");

						$("#user_directory_meta_srch_label_" + i).val("");

					}      
				}	
   	});
    });
   
    $(function() {
    	$( "#sortable" ).sortable();
    	$( "#sortable" ).disableSelection();
    }); 
    
    $('#Email').hover(function() {
        $(this).css('cursor','pointer');
    });
    $('#Website').hover(function() {
        $(this).css('cursor','pointer');
    });
    $('#Address').hover(function() {
        $(this).css('cursor','pointer');
    });
    $('#MetaKey1').hover(function() {
        $(this).css('cursor','pointer');
    });
    $('#MetaKey2').hover(function() {
        $(this).css('cursor','pointer');
    });
    $('#MetaKey3').hover(function() {
        $(this).css('cursor','pointer');
    });
    $('#MetaKey4').hover(function() {
        $(this).css('cursor','pointer');
    });
    $('#MetaKey5').hover(function() {
        $(this).css('cursor','pointer');
    });
    
    $(function() {
        $( "#sortable" ).sortable({
            placeholder: "ui-state-highlight",
            cursor: 'crosshair',
            update: function(event, ui) {
               var Order = $("#sortable").sortable('toArray').toString();
               $('#user_directory_sort_order').val(Order);
              
             }
         });
         
     });

	/* For Multipl Dirs Add-on */ 
	$( "#delete" ).click(function() {
		
    if ( confirm( "Delete this directory instance?" ) )
       document.LoadInstance.submit();
    else
        return false;
	});
	
	$( "#add" ).click(function() {
		
    if ( $('#dud_new_instance_name').val() ) 
       document.AddInstance.submit();
    else
	{
        alert("Please enter a new directory name.");
		return false;
	}
	});
	
})( jQuery );