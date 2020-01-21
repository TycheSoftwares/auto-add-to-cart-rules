<?php

/*
Plugin Name: Auto Add To Cart Rules
Plugin URI:https://localhost/wordpress_shas/wordpress/wp-admin/plugins/cart_products_automatically
Description: A plugin which add products to the cart automatically when the given conditions comes true.
Version: 0.1.1
Requires at least: 5.2
Requires PHP:4.3
Author: Shasvat Shah
Author URI:https://www.shasvat.com
Text Domain:cart-products-automatically
Domain Path: /languages/
*/

/**
 *Exit if accessed directly
 * 
 */
defined( 'ABSPATH' ) || exit;

/*
 *Main Class
 */

class Auto_Add_To_Cart_Rules
{
	
	/*
	 *Constructer
	 */

	function __construct() {

		if ( $this->is_woocommerce_active() ) {
		
			include_once dirname( __FILE__ ) . '/includes/settings.php';
			add_action( 'admin_menu', array($this,'aar_add_admin_menu' ) );
			//add_action( 'admin_init', array($this,'aar_settings_init' ) );
			add_action( 'woocommerce_add_to_cart', array($this,'aar_add_oneproduct_to_cart'), 10, 2 );
			add_action( 'template_redirect', array($this,'aar_add_freeproduct_to_cart' ) );
		    add_action( 'template_redirect', array($this,'remove_product_from_cart' ) );
		    add_action( 'template_redirect', array($this,'aar_add_visitproduct_to_cart' ) );	
			add_action( 'plugins_loaded', array($this,'load_plugin_textdomain' ) );
			add_action( 'admin_enqueue_scripts', array($this,'enqueue_script' ) );
		}
		else {
		
			add_action( 'admin_notices',array( &$this, 	'aar_error_notice' ) );
		}
	}

	/*
	 *For Enqueuing	JS script 
	 */
	
	function enqueue_script(){
 	
 	   	wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
    	wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery') );
       	wp_enqueue_script('aar_select2', plugin_dir_url(__FILE__) . '/assets/js/aar_select2.js',array('jquery', 'select2'));
   	 	wp_localize_script('aar_select2-ajax-script','aar_select2_ajax_obj',array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ));
	}

	/*
	 *Function to display a notice to user if the WooCommerce plugin is deactive.
	 *
	 * @hook admin_notices 	
	 */

	function aar_error_notice() {

		echo '<div class="error">
	        		<p><b>Auto Add To Cart Rules is enabled but not effective. It requires  WooCommerce Plugin activated in order to work.</b></p>
	        	 </div>';
	}
	
	/**
	 * Function to check if WooCommerce Plugin is active or not.
	 *
	 * @return bool $check true if WooCommerce Plugin is active else false.
	 */

	function is_woocommerce_active() {

		$check = false;

		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
		
		if ( in_array( 'woocommerce/woocommerce.php', $active_plugins ) ) {
    		$check = true;
		}

		return $check;
	}	

	/**
	 *This function will load the text domain
	 *
	 */
	
	function load_plugin_textdomain() {
		
		load_plugin_textdomain( 'cart-products-automatically', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}
	
	/**
     *This function will add menu in admin panel
     *
	 */

	function aar_add_admin_menu() { 

		add_menu_page( 'Cart Products Automatically', 'Cart Products Automatically', 'manage_options', 'aar_cart_products_automatically', array('aar_settings' ,'aar_options_page' ) );

	}

	/**
	 * This Function will check if the customer had any previous order or not 
	 *
	 * @return bool "false" when customer has already at least one order (true if not)
	 */

	function is_order_available() {

		$customer_orders = get_posts( array(
			    	    'numberposts' => -1,
		    	    	'meta_key'    => '_customer_user',
		        		'meta_value'  => get_current_user_id(),
		        		'post_type'   => 'shop_order',//, // WC orders post type
		        		'post_status' => 'any' // Only orders with status "completed"
			    	
		) );
		//
		return count($customer_orders) > 0 ? false : true; 
	}

	/**
	 * This function will check the role of the user dependent on that other function will work.
	 * 
	 * @return bool $user as per different conditions. 
	 */

	function check_user_role() {

		$aar_user=get_option( 'aar_user');
 		$user=true;
 		
 		if ($aar_user == "loggedin") {

 			if ( is_user_logged_in() ) {
 				
 				$aar_firstorder = get_option( 'aar_firstorder' );		
				
				if( $aar_firstorder == 1){

 					$user = $this->is_order_available();
 				}
				else{
					$user=true;
 				}
			}
			else{
					$user=false;
 				}
		}
		else{
					$user=false;			
		}
 		 		
 		if ($aar_user == "guest") {

 			if ( is_user_logged_in() ) {
			
					$user=false;
			}
			else{
					$user=true;			
			}
 		}

 		if ($aar_user == "both") {

 			if ( is_user_logged_in() ) {

 				$aar_firstorder = get_option( 'aar_firstorder' );		
				
				if( $aar_firstorder == 1){
 					$user = $this->is_order_available();
				}
				else{
					$user=true;
 				}
			}
			else{
					$user=true;
 				}

 		}

 		return $user;
	}

	/**
	 * This function is for adding the multiple product to the cart if the
	 * users adds the multiple category product to the cart.
	 *
	 * 
	 */

 	function aar_add_oneproduct_to_cart( $item_key, $product_id ) { 

 		$user_role= $this->check_user_role();

 	if($user_role){
 		
	 	if(get_option('aar_checkfreeprd') == 1) {
	 			 		
	 			$remove=true;
		 		$aar_one=get_option('aar_oneprd');
		 		$aar_free=get_option('aar_freeprd');
		 		$aar_freeprd = $aar_free['productid'];
		 		$product_category_id 	= $aar_one['productid'];

		 	if(get_option('aar_removefreeprd') == 1){

	 			if ( isset( WC()->session ) && !is_null( WC()->session->get( 'removed_cart_contents' ) ) && WC()->session->get( 'removed_cart_contents' ) != '' ) { // checking is any products is removed or not.
				
					$removed_cart_contents = WC()->session->get( 'removed_cart_contents' );
				
					foreach ( $removed_cart_contents as $key => $value ) {
						$list_of_removed_product[] = $value['product_id'];			
					}
				
					$list_of_removed_product = array_unique( $list_of_removed_product );
				
					foreach ( $aar_freeprd as $key => $free_product_ids ) {
								
						if ( in_array( $free_product_ids, $list_of_removed_product ) ){ // If free product found in 													list of removed product then do nothing.
							$remove=false;
							break; // do nothing if product is already removed by customer.
						}
					}
				}
			}

			if($remove){
					
				$product_cats_ids 		= wc_get_product_term_ids( $product_id, 'product_cat' ); // Getting assigned categories of product which is being added to cart
				$product_category_id_check = false;
				
				foreach ( $product_category_id as $key => $value ) {

					if ( in_array( $value, $product_cats_ids ) ) { // Checking if the specified category is being 													  matched or 	not.
						$product_category_id_check = true;
					}
				}

				if ( ! is_admin() && $product_category_id_check ) {
			        		
			        $free_product_ids = $aar_freeprd;// Product Ids of the free products which will 													  get added to cart e.g Gift A & Gift B
			        	
				    foreach ( $free_product_ids as $pkey => $free_product_id ) {

				        $found = false;
					    //check if product already in cart
					        
					    if ( sizeof( WC()->cart->get_cart() ) > 0 ) {

					        foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					                
					        	$_product = $values['data'];
					                
					        	if ( $_product->get_id() == $free_product_id )
					               	$found = true;
					        }

					       	   	if ( ! $found )
					           	WC()->cart->add_to_cart( $free_product_id );
					    }
						else {
					        // if no products in cart, add it
					        WC()->cart->add_to_cart( $free_product_id );
					    }
			        }                
		    	} 
	    	} 
	    }
	}
}

	/**
	 *This function is for adding the product when
	 *the total of cart exceeds certain amount.
	 *
	 */
	
	function aar_add_freeproduct_to_cart() {

		$user_role= $this->check_user_role();

 	if($user_role){

		if(get_option('aar_checktotalcart') == 1) {
				
				$remove=true;	
				global $woocommerce;
				$cart_total	=get_option('aar_enteramt');
				$list_of_removed_product 	= array(); // In this we will add all the removed product ids
				$free_product = get_option('aar_totalcart');
				//var_dump($free_product);
				//$free_product_id=$free_product['productid'];
				$array=array_values($free_product);
	        	
	        	foreach ($array as $key => $value) {
			      		$product=$value;
	        	}
	        	
	        	foreach ($product as $key => $value) {
	        		$free_product_id=$value;
	        	}
				
				//var_dump($free_product_id);
				//exit();
			
			/*if(get_option('aar_removetotalfreeprd') == 1) {	
				//echo "123";
				//exit();
				if ( isset( WC()->session ) && !is_null( WC()->session->get( 'removed_cart_contents' ) ) && WC()->session->get( 'removed_cart_contents' ) != '' ) { // checking is any products is removed or not.
				
					$removed_cart_contents = WC()->session->get( 'removed_cart_contents' );
					
					foreach ( $removed_cart_contents as $key => $value ) {
						$list_of_removed_product[] = $value['product_id'];			
					}
					
					$list_of_removed_product = array_unique( $list_of_removed_product );
				
				}
					
					if ( in_array( $free_product_id, $list_of_removed_product ) ){ // If free product found in list of 																	removed product then do nothing.
						$remove=false;
						
						return; // do nothing if product is already removed by customer.
					}
			}*/
									
			if($remove){
				
			 	global $woocommerce;
								
				if ( $woocommerce->cart->total >= $cart_total ) {
					
					if ( ! is_admin() /*&& ! is_cart() && ! is_checkout()*/ ) {
						//echo "123";					
						//exit();
				    	$free_product = get_option('aar_totalcart');
						$array=array_values($free_product);
				        	
				        	foreach ($array as $key => $value) {
						      		$product=$value;
				        	}
				        	
				        	foreach ($product as $key => $value) {
				        		$free_product_id=$value;
				        	} 
				        	//print_r($free_product_id);
				        	//exit();
				        	// Product Id of the free product which will 													get added to cart

				        	/*echo "<pre>";	print_r($free_product_id); echo "</pre>";
							die();
				      		*/
				      		$found = false;
				      
				      	//echo "<pre>";	print_r($free_product_id); echo "</pre>";
				       	//check if product already in cart
				      
				        if ( sizeof( WC()->cart->get_cart() ) > 0 ) {

					        foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					            	    
					            $_product = $values['data'];
					                
					            if ( $_product->get_id() == $free_product_id )
					                $found = true;	                
					        }
					        //var_dump($free_product_id);
					        //exit();
					        // if product not found, add it
					        if ( ! $found ){
					            WC()->cart->add_to_cart( $free_product_id );
					        }
					        // var_dump($free_product_id);
					   		// exit();
					    } 

					    else{
					        // if no products in cart, add it
					        WC()->cart->add_to_cart( $free_product_id );
					    }        
					}
				}
				
				/**
				 *This else will remove the automatically added product from the cart when
				  the cart total is less than the certain amount added by the admin
				 *
				 */	
				
				else{
						
					if ( ! is_admin()  ) {
				    
				        $free_product = get_option('aar_totalcart');
						$array=array_values($free_product);
						        	
						   	foreach ($array as $key => $value) {
						      		$product=$value;
						   	}
						    	
						  	foreach ($product as $key => $value) {
						      		$free_product_id=$value;
						   	} // Product Id of the free product which will 													get added to cart
					    
					    $found = false;

					    //check if product already in cart
					    if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
					            
					        foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					            	
					           	$_product = $values['data'];
					                
					            if ( $_product->get_id() != $free_product_id ){
					              	$found = false;
					            }
					            else{
					          	$found = true;
					          	break;
					            }
					        }
					        if($found){
				  	    		$free_pro_cart_id = WC()->cart->generate_cart_id( $free_product_id );
				          		unset( WC()->cart->cart_contents[ $free_pro_cart_id ]);
					       	}
					    }        
					}
				}
			}
		}
	}
}
	
	/**
	 *This function will automatically adds the free product
	  to the cart when customer visits our website.
	 *
	 */

	function aar_add_visitproduct_to_cart() {

		$user_role= $this->check_user_role();
	
 	if($user_role){

		if(get_option('aar_checkvisit') == 1) {

			$product=true;

			/*if(get_option('aar_login') == 1) {	

				if ( is_user_logged_in() ) {
					$product=true;
				}
				else{
					$product=false;
				}
    		}*/

    		if($product){	
		

		    	if ( ! is_admin() && !is_cart() && !is_checkout() ) {
		        	
		        	$product_id =   get_option('aar_visit'); // Product Id of the free product which will get added to cart
		        	$array=array_values($product_id);
		        	foreach ($array as $key => $value) {
				      		$product=$value;
		        	}
		        	foreach ($product as $key => $value) {
		        		$products=$value;
		        	}
		        	$found 	= false;
		        	
		        	//check if product already in cart
		        	if ( sizeof( WC()->cart->get_cart() ) > 0 ) {

		            	foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
		                   	$_product = $values['data'];

		                	if ( $_product->get_id() == $products )
		                    	$found = true;
		            	}

		            	// if product not found, add it
		            	if ( ! $found )
		                   	WC()->cart->add_to_cart( $products );
		        	}
		        	
		        	else{
		         	   // if no products in cart, add it
		            	WC()->cart->add_to_cart( $products);
		        	}
		   		}	    
			}
		}
	}
}


	/**
	 *This function will remove the automatically added product when the product is removed by the user. 
	 *
	 */

	function remove_product_from_cart() {
    
	    	$aar_one=get_option('aar_oneprd');
	    	$aar_freeprd=get_option('aar_freeprd');
	    	
	    // Run only in the Cart or Checkout Page
	    if ( is_cart() || is_checkout() ) {
	    	
	    	$product_category_id 	=   $aar_one['productid'];//explode(",", $aar_oneprd);//ID of  	category     
	        
	        $prod_to_remove     	= 	$aar_freeprd['productid'];//explode(",",$aar_freeprd); // Product ID of Free Product
	        
	        $cart_contains_category = false; // Default set to false : This means cart doesn't contains any product of perticular category
	        
	        $free_pro_cart_id 		= "";

	        foreach ( WC()->cart->cart_contents as $prod_in_cart ) {
	          
	            // Get the Variation or Product ID
	            $prod_id = ( isset( $prod_in_cart['variation_id'] ) && $prod_in_cart['variation_id'] != 0 ) ? $prod_in_cart['variation_id'] : $prod_in_cart['product_id'];
	            $product_cats_ids = wc_get_product_term_ids( $prod_id, 'product_cat' );

	        	foreach ( $product_category_id as $key => $value ) {
		
	     	       if ( in_array( $value, $product_cats_ids ) ){
	        	    	$cart_contains_category = true; // cart has the product of particular category.            	
	            		break;
	            	}
	        	}
	     	}
	        
	        foreach ( $prod_to_remove as $key => $value1 ) {
	        	
	        	if ( !$cart_contains_category ) { // remove free product if cart doesn't contain product of perticular 										category
	        		$free_pro_cart_id = WC()->cart->generate_cart_id( $value1 );
	            	// Remove it from the cart by un-setting it
	            	unset( WC()->cart->cart_contents[ $free_pro_cart_id ] );             
    	    	}
    		}
    	}
	}

}

new Auto_Add_To_Cart_Rules();

?>