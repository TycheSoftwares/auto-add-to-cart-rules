<?php

/*
 *register settings
 */

defined( 'ABSPATH' ) || exit;

class Aar_Settings
{
	/*
	 *Class Constructer
	 *	
	 */
	function  __construct() {

		add_action( 'admin_init', array($this,'settings_init' ) );
	
	}

	/*
	 *This function is for section heading on menu page
	 * & register settings in the database
	 *
	 */

	function settings_init() {

		

		register_setting( 'pluginPage', 'aar_user' );
		register_setting( 'pluginPage', 'aar_firstorder' );
		register_setting( 'pluginPage', 'aar_oneprd' );
		register_setting( 'pluginPage', 'aar_freeprd' );
		register_setting( 'pluginPage', 'aar_checkfreeprd' );
		register_setting( 'pluginPage', 'aar_removefreeprd' );
		register_setting( 'pluginPage', 'aar_removetotalfreeprd' );
		register_setting( 'pluginPage', 'aar_enteramt' );
		register_setting( 'pluginPage', 'aar_totalcart' );
		register_setting( 'pluginPage', 'aar_checktotalcart' );
		register_setting( 'pluginPage', 'aar_visit' );
		register_setting( 'pluginPage', 'aar_checkvisit' );
		register_setting( 'pluginPage', 'aar_login' );
		
		/**
		 *This will display the header of the section(For user)
		 *
		 */

		add_settings_section(
			'aar_user_login', 
			__( 'General Settings', 'cart-products-automatically' ), 
			array($this,'aar_settings_login'), 
			'pluginPage'
		);		

		add_settings_field( 
			'aar_user', 
			__( 'Select User', 'cart-products-automatically' ), 
			array($this,'aar_user_render'), 
			'pluginPage', 
			'aar_user_login' 
		);

		add_settings_field( 
			'aar_firstorder', 
			__( 'Enable', 'cart-products-automatically' ), 
			array($this,'aar_first_order'), 
			'pluginPage', 
			'aar_user_login' 
		);

		/**
		 *This will display the header of the section(add to cart action)
		 *
		 */

		add_settings_section(
			'aar_add_to_cart_action', 
			__( 'Add to Cart Action', 'cart-products-automatically' ), 
			array($this,'aar_settings_add_to_cart_action'), 
			'pluginPage'
		);

		/**
		 *This setting field is for enabling add to cart function  
		 *
		 */

		add_settings_field( 
			'aar_checkfreeprd', 
			__( 'Enable', 'cart-products-automatically' ), 
			array($this,'aar_checkfreeprd_render'), 
			'pluginPage', 
			'aar_add_to_cart_action' 
		);

		/**
		 *This setting field is to enter multiple categorys  on which function will work
		 *
		 */

		add_settings_field( 
			'aar_oneprd', 
			__( 'Multiple Category\'s', 'cart-products-automatically' ), 
			array($this,'aar_oneprd_render'), 
			'pluginPage', 
			'aar_add_to_cart_action' 
		);

		/**
		 *This setting field is to enter multiple products to be added automatically when add to cart function works
		 *
		 */

		add_settings_field( 
			'aar_freeprd', 
			__( 'Multple Product\'s', 'cart-products-automatically' ), 
			array($this,'aar_freeprd_render' ),
			'pluginPage', 
			'aar_add_to_cart_action' 
		);

		/**
		 *This setting field is for enabling not to add the free product again once its removed 
		 *
		 */

		add_settings_field( 
			'aar_removefreeprd', 
			__( 'Enable', 'cart-products-automatically' ), 
			array($this,'aar_removefreeprd_render'), 
			'pluginPage', 
			'aar_add_to_cart_action' 
		);

		/**
		 *This will display the header of the section(cart total)
		 *
		 */

		add_settings_section(
			'aar_cart_total', 
			__( 'Cart Total', 'cart-products-automatically' ), 
			array($this,'aar_settings_cart_total'), 
			'pluginPage'
		);

		/**
		 *This setting field is for enabling cart total function  
		 *
		 */

		add_settings_field( 
			'aar_checktotalcart', 
			__( 'Enable', 'cart-products-automatically' ), 
			array($this,'aar_checktotalcart_render'), 
			'pluginPage', 
			'aar_cart_total' 
		);

		/**
		 *This setting field is to enter amount of cart total on exceeds of which cart total function will work .
		 *
		 */

		add_settings_field( 
			'aar_enteramt', 
			__( 'Amount', 'cart-products-automatically' ), 
			array($this,'aar_enteramt_render'), 
			'pluginPage', 
			'aar_cart_total' 
		);

	    /**
		 *This setting field is to enter product to be added automatically when cart total function works
		 *
		 */

		add_settings_field( 
			'aar_totalcart', 
			__( 'Product', 'cart-products-automatically' ), 
			array($this,'aar_totalcart_render'), 
			'pluginPage', 
			'aar_cart_total' 
		);

		/**
		 *This setting field is for enabling not to  the free product again once its removed 
		 *
		 */

		add_settings_field( 
			'aar_removetotalfreeprd', 
			__( 'Enable', 'cart-products-automatically' ), 
			array($this,'aar_removetotalfreeprd_render'), 
			'pluginPage', 
			'aar_cart_total' 
		);

		/**
		 *This will display the header of the section(website visit)
		 *
		 */
		
		add_settings_section(
			'aar_website_visit', 
			__( 'Website Visit', 'cart-products-automatically' ), 
			array($this,'aar_settings_website_visit'), 
			'pluginPage'
		);

		/**
		 *This setting field is for enabling website visit function  
		 *
		 */

		add_settings_field( 
			'aar_checkvisit', 
			__( 'Enable', 'cart-products-automatically' ), 
			array($this,'aar_checkvisit_render'), 
			'pluginPage', 
			'aar_website_visit' 
		);

		/**
		 *This setting field is to enter product to be added automatically when website visit function works
		 *
		 */

		add_settings_field( 
			'aar_visit', 
			__( 'Product', 'cart-products-automatically' ), 
			array($this,'aar_visit_render'), 
			'pluginPage', 
			'aar_website_visit' 
		);
	}
	
	/**
	 *This function will display under the header of select user role function 
	 *
	 */ 

	function aar_settings_login() {
			echo __( '', 'cart-products-automatically' );	
	}

	/**
	 *This function will create the selectdown tool for selecting the user role 
	 *
	 */

	function aar_user_render() {
		$aar_user=get_option( 'aar_user');
		

		?>
		<select name="aar_user">
			<option value="guest" <?php selected("guest" , $aar_user , true); ?> >Guest</option>
			<option value="loggedin" <?php selected("loggedin" , $aar_user, true); ?> >Logged-in</option>
			<option value="both" <?php selected("both" , $aar_user , true); ?> >Both</option>
			
		</select>

	    <?php
	}

	/**
	 *This function will create the selectdown tool for selecting the user role 
	 *
	 */

	function aar_first_order() {
		$aar_firstorder=get_option( 'aar_firstorder');
		

		?>
		<input type='checkbox' name='aar_firstorder' <?php checked(1, $aar_firstorder , true); ?> value='1'> <?php _e('Enabling this will automatically add the product to cart only when the customer places their first order.','cart-products-automatically');?>
	    <?php
	}

	/**
	 *This function will create the checkbox to enable add to cart function
	 *
	 */

	function aar_checkfreeprd_render() { 

		$aar_checkfreeprd=get_option('aar_checkfreeprd');

		?>
		<input type='checkbox' name='aar_checkfreeprd' <?php checked(1, $aar_checkfreeprd , true); ?> value='1'> <?php _e('Enables add to cart action function.','cart-products-automatically');?>
		<?php

	}

	/**
	 *This function will create the text box for category id in add to cart function 
	 *
	 */

	function aar_oneprd_render() { 

		$aar_oneprd=get_option('aar_oneprd');

		if(!$aar_oneprd){
			$aar_oneprd="";
		}

		$atc_prd= array(
				       	'taxonomy'=> 'product_cat',
            			'orderby' =>'name'
				 );
		
		$atc_cat=get_categories( $atc_prd );
		
		?>
		<select id="atc_wc_cat_search"
				multiple="multiple" 
				style="width: 50%;" 
				name="aar_oneprd[productid][]">
		<?php
		    foreach ( $atc_cat as $key => $product ) {
	            $atc_product_ID 	= $product->term_id;
	            $atc_product_title 	= $product->name;
	            $selected = in_array( $atc_product_ID, $aar_oneprd['productid'] ) ? ' selected="selected" ' : '';
	            echo '<option value="' . esc_attr( $atc_product_ID ) . '" '.esc_attr($selected).' >' . wp_kses_post( $atc_product_title ) . '</option>';
	        }
    
    	?>
    	</select>
    	</br><p> <?php _e('Select category\'s  on which function will work.','cart-products-automatically');?></p>
		<?php

	}

	/**
	 *This function will create the select2 for products in add to cart function 
	 *
	 */

	function aar_freeprd_render() { 

		$aar_freeprd=get_option('aar_freeprd');
		
		if(!$aar_freeprd){
			$aar_freeprd="";
		}

		$atc_freeprd 		= array(
				        	'posts_per_page' => 8,
				        	'post_type' => 'product'
						);
		$atc_product_object = new WP_Query( $atc_freeprd );
		?>
		<select id="atc_wc_product_search"
				multiple="multiple" 
				style="width: 50%;" 
				name="aar_freeprd[productid][]">
		<?php
		if ( $atc_product_object->have_posts() ) { 
			$product_posts = $atc_product_object->get_posts();

	        foreach ( $product_posts as $key => $product ) {
	            $atc_product_ID 	= $product->ID;
	            $atc_product_title 	= $product->post_title;
	            $selected = in_array( $atc_product_ID, $aar_freeprd['productid'] ) ? ' selected="selected" ' : '';
	            echo '<option value="' . esc_attr( $atc_product_ID ) . '" '.esc_attr($selected).' >' . wp_kses_post( $atc_product_title ) . '</option>';
	        }
    	}
    	?>
    	</select>
    	</br><p> <?php _e('Select product\'s which will be added to the cart when product of particular category is added to the cart.','cart-products-automatically');?> </p>
		<?php

	}

	/**
	 *This function will create the checkbox to enable not to add free product once it is removed
	 *
	 */

	function aar_removefreeprd_render() { 

		$aar_removefreeprd=get_option('aar_removefreeprd');

		?>
		<input type='checkbox' name='aar_removefreeprd' <?php checked(1, $aar_removefreeprd , true); ?> value='1'> <?php _e('Enabling this will not add the free product again to the cart once it is removed by the user.','cart-products-automatically');?>
		<?php

	}

	/**
	 *This function will display under the header of add to cart section
	 *
	 */ 

	function aar_settings_add_to_cart_action() { 

		echo __( 'When product of multiple category\'s is added to cart, then also add multiple product to the cart.', 'cart-products-automatically' );

	}

	/**
	 *This function will create the checkbox to enable cart total function
	 *
	 */
	
	function aar_checktotalcart_render() { 

		$aar_checktotalcart=get_option('aar_checktotalcart');

		?>
		<input type='checkbox' name='aar_checktotalcart' <?php checked(1, $aar_checktotalcart , true); ?> value='1'> <?php _e('Enables cart total function.','cart-products-automatically');?>
		<?php

	}

	/**
	 *This function will create the text box to enter amount in  cart total function 
	 *
	 */

	function aar_enteramt_render() { 
		
		$aar_enteramt=get_option('aar_enteramt');

		if(!$aar_enteramt){
			$aar_enteramt="";
		}

		?>
		<input type='text' name='aar_enteramt' value='<?php echo $aar_enteramt; ?>'>
		</br><p> <?php _e('Enter amount of cart total on exceeds of which this function will work .','cart-products-automatically');?> </p>
		<?php

	}

	/**
	 *This function will create the select2 for product  in cart total function 
	 *
	 */

	function aar_totalcart_render() { 

		$aar_totalcart=get_option('aar_totalcart');

		if(!$aar_totalcart){
			$aar_totalcart="";
		}

		
		$ct_freeprd = array(
				        	'posts_per_page' => 8,
				        	'post_type' => 'product'
					);
		$ct_product_object = new WP_Query( $ct_freeprd );
		?>

		<select id="ct_wc_product_search"
				multiple="multiple" 
				style="width: 50%;" 
				name="aar_totalcart[productid][]">
		<?php
		if ( $ct_product_object->have_posts() ) { 
			$product_posts = $ct_product_object->get_posts();

	        foreach ( $product_posts as $key => $product ) {
	            $ct_product_ID 	= $product->ID;
	            $ct_product_title 	= $product->post_title;
	            $selected = in_array( $ct_product_ID, $aar_totalcart['productid'] ) ? ' selected="selected" ' : '';
	            echo '<option value="' . esc_attr( $ct_product_ID ) . '" '.esc_attr($selected).' >' . wp_kses_post( $ct_product_title ) . '</option>';
	        }
    	}
    	?>
    	</select>
		</br><p> <?php _e('Select product which will be added to the cart when cart total will exceeds by certain amount.','cart-products-automatically'); ?> </p>
		<?php

	}

	/**
	 *This function will create the checkbox to enable not to add free product once it is removed
	 *
	 */

	function aar_removetotalfreeprd_render() { 

		$aar_removetotalfreeprd=get_option('aar_removetotalfreeprd');

		?>
		<input type='checkbox' name='aar_removetotalfreeprd' <?php checked(1, $aar_removetotalfreeprd , true); ?> value='1'> <?php _e('Enabling this will not add the free product again to the cart once it is removed by the user.','cart-products-automatically');?>
		<?php

	}


	/**
	 *This function will display under the header of cart total section
	 *
	 */ 

	function aar_settings_cart_total() { 

		echo __( 'When total of cart exceeds by certain amount.', 'cart-products-automatically' );

	}

	/**
	 *This function will create the checkbox to enable website visit function
	 *
	 */

	function aar_checkvisit_render() { 

		$aar_checkvisit=get_option('aar_checkvisit');

		if(!$aar_checkvisit){
			$aar_checkvisit="";
		}
		?>
		<input type='checkbox' name='aar_checkvisit' <?php checked(1, $aar_checkvisit , true); ?> value='1'> <?php _e('Enables website visit function.','cart-products-automatically');?>
		<?php

	}

	/**
	 *This function will create the text box for product id in website visit function 
	 *
	 */

	function aar_visit_render() { 

		$aar_visit=get_option('aar_visit');

		if(!$aar_visit){
			$aar_visit="";
		}

		$wv_freeprd = array(
				        	'posts_per_page' => 8,
				        	'post_type' => 'product'
					);
		$wv_product_object = new WP_Query( $wv_freeprd );
		?>

		<select id="wv_wc_product_search"
				multiple="multiple" 
				style="width: 50%;" 
				name="aar_visit[productid][]">
		<?php
		if ( $wv_product_object->have_posts() ) { 
			$product_posts = $wv_product_object->get_posts();

	        foreach ( $product_posts as $key => $product ) {
	            $wv_product_ID 	= $product->ID;
	            $wv_product_title 	= $product->post_title;
	            $selected = in_array( $wv_product_ID, $aar_visit['productid'] ) ? ' selected="selected" ' : '';
	            echo '<option value="' . esc_attr( $wv_product_ID ) . '" '.esc_attr($selected).' >' . wp_kses_post( $wv_product_title ) . '</option>';
	        }
    	}
    	?>
    	</select>
		</br><p> <?php _e('Enter product which will be added to cart.','cart-products-automatically'); ?></p>
		<?php

	}

	/**
	 *This function will display under the header of website visit section
	 *
	 */ 

	function aar_settings_website_visit() { 

		echo __( 'Adding product to the cart when a customer visits your website.', 'cart-products-automatically' );

	}

	function aar_options_page() { 

			?>
			<form action='options.php' method='post'>

				<h2> <?php _e('Automatically Add a Product Into Cart.','cart-products-automatically'); ?></h2>
				<p> <?php _e('This will automatically add a product to your WooCommerce cart in 3 different scenarios.','cart-products-automatically'); ?></p>
				</br>

				<?php
				settings_fields( 'pluginPage' );
				do_settings_sections( 'pluginPage' );
				submit_button();
				?>

			</form>
			<?php

	}

}
$Aar_Settings = new Aar_Settings();
?>