	
jQuery(document).ready(function($) {

	// simple multiple select
	jQuery('#atc_wc_product_search').select2();

	jQuery('#atc_wc_cat_search').select2();

	jQuery('#ct_wc_product_search').select2();

	jQuery('#wv_wc_product_search').select2();


	jQuery('#wc_product_search').select2();

 	//var name ='shasvat'; 
    // We'll pass this variable to the PHP function example_ajax_request
    $(document).ready(function(){
  				$("#test").click(function(){

			 				
  					 $.ajax({
					        url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
					        type: 'POST',
					        data: {
					            'action': 'hello_world',
					            
					        },
					        success:function(data) {
					            // This outputs the result of the ajax request
					            console.log(data);
					        },
					        error: function(errorThrown){
					            console.log(errorThrown);
					        }
					    });  
					});
				});

});