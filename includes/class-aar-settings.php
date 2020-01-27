<?php
/**
 * Register settings
 *
 * @package register_settings
 */

defined( 'ABSPATH' ) || exit;

/**
 * Settings class
 */
class Aar_Settings {

	/**
	 * Class Constructer
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'aar_settings_init' ) );
	}

	/**
	 * This function is for section heading on menu page
	 * & register settings in the database
	 */
	public function aar_settings_init() {
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
		register_setting( 'pluginPage', 'aar_price' );

		/**
		 *This will display the header of the section(For user)
		 */
		add_settings_section(
			'aar_user_login',
			__( 'General Settings', 'auto-addtocart-rules' ),
			array( $this, 'aar_settings_login' ),
			'pluginPage'
		);

		/**
		 * This setting field is for selecting user role.
		 */
		add_settings_field(
			'aar_user',
			__( 'Select User Role', 'auto-addtocart-rules' ),
			array( $this, 'aar_user_select' ),
			'pluginPage',
			'aar_user_login'
		);

		/**
		 * This setting field is for enabling automatically add the product to cart only when the *customer places their first order.
		 */
		add_settings_field(
			'aar_firstorder',
			__( 'First Order Discount', 'auto-addtocart-rules' ),
			array( $this, 'aar_first_order' ),
			'pluginPage',
			'aar_user_login'
		);

		/**
		 * This setting field is for enabling to set the price of free product to 0.
		 */
		add_settings_field(
			'aar_price',
			__( 'Gift A Product', 'auto-addtocart-rules' ),
			array( $this, 'aar_set_price' ),
			'pluginPage',
			'aar_user_login'
		);

		/**
		 * This will display the header of the section(add to cart action)
		 */
		add_settings_section(
			'aar_atc_add_to_cart_action',
			__( 'Add to Cart Action', 'auto-addtocart-rules' ),
			array( $this, 'aar_atc_settings_add_to_cart_action' ),
			'pluginPage'
		);

		/**
		 * This setting field is for enabling add to cart function
		 */
		add_settings_field(
			'aar_atc_checkfreeprd',
			__( 'Enable', 'auto-addtocart-rules' ),
			array( $this, 'aar_atc_product_enable' ),
			'pluginPage',
			'aar_atc_add_to_cart_action'
		);

		/**
		 *This setting field is to enter multiple categorys  on which function will work
		 */
		add_settings_field(
			'aar_oneprd',
			__( 'Multiple Category\'s', 'auto-addtocart-rules' ),
			array( $this, 'aar_atc_category' ),
			'pluginPage',
			'aar_atc_add_to_cart_action'
		);

		/**
		 *This setting field is to enter multiple products to be added automatically when add to cart function works
		 */
		add_settings_field(
			'aar_freeprd',
			__( 'Multple Product\'s', 'auto-addtocart-rules' ),
			array( $this, 'aar_atc_product' ),
			'pluginPage',
			'aar_atc_add_to_cart_action'
		);

		/**
		 *This setting field is for enabling not to add the free product again once its removed
		 */
		add_settings_field(
			'aar_removefreeprd',
			__( 'Enable', 'auto-addtocart-rules' ),
			array( $this, 'aar_atc_remove_product' ),
			'pluginPage',
			'aar_atc_add_to_cart_action'
		);

		/**
		 *This will display the header of the section(cart total)
		 */
		add_settings_section(
			'aar_ct_cart_total',
			__( 'Cart Total', 'auto-addtocart-rules' ),
			array( $this, 'aar_ct_settings_cart_total' ),
			'pluginPage'
		);

		/**
		 *This setting field is for enabling cart total function
		 */
		add_settings_field(
			'aar_checktotalcart',
			__( 'Enable', 'auto-addtocart-rules' ),
			array( $this, 'aar_ct_enable' ),
			'pluginPage',
			'aar_ct_cart_total'
		);

		/**
		 *This setting field is to enter amount of cart total on exceeds of which cart total function will work .
		 */
		add_settings_field(
			'aar_enteramt',
			__( 'Amount', 'auto-addtocart-rules' ),
			array( $this, 'aar_ct_enter_amount' ),
			'pluginPage',
			'aar_ct_cart_total'
		);

		/**
		 *This setting field is to enter product to be added automatically when cart total function works
		 */
		add_settings_field(
			'aar_totalcart',
			__( 'Product', 'auto-addtocart-rules' ),
			array( $this, 'aar_ct_product' ),
			'pluginPage',
			'aar_ct_cart_total'
		);

		/**
		 *This setting field is for enabling not to  the free product again once its removed
		 */
		add_settings_field(
			'aar_removetotalfreeprd',
			__( 'Enable', 'auto-addtocart-rules' ),
			array( $this, 'aar_ct_remove_product' ),
			'pluginPage',
			'aar_ct_cart_total'
		);

		/**
		 *This will display the header of the section(website visit)
		 */
		add_settings_section(
			'aar_wvf_website_visit',
			__( 'Website Visit', 'auto-addtocart-rules' ),
			array( $this, 'aar_ct_settings_website_visit' ),
			'pluginPage'
		);

		/**
		 *This setting field is for enabling website visit function
		 */
		add_settings_field(
			'aar_checkvisit',
			__( 'Enable', 'auto-addtocart-rules' ),
			array( $this, 'aar_wvf_product_enable' ),
			'pluginPage',
			'aar_wvf_website_visit'
		);

		/**
		 *This setting field is to enter product to be added automatically when website visit function works
		 */
		add_settings_field(
			'aar_visit',
			__( 'Product', 'auto-addtocart-rules' ),
			array( $this, 'aar_wvf_product' ),
			'pluginPage',
			'aar_wvf_website_visit'
		);
	}

	/**
	 * This function will display under the header of select user role function
	 */
	public function aar_settings_login() {

	}

	/**
	 * This function will create the selectdown tool for selecting the user role
	 */
	public function aar_user_select() {
		$aar_user = get_option( 'aar_user' );
		?>
		<select name="aar_user">
			<option value="guest" <?php selected( 'guest', $aar_user, true ); ?> >Guest</option>
			<option value="loggedin" <?php selected( 'loggedin', $aar_user, true ); ?> >Logged-in</option>
			<option value="both" <?php selected( 'both', $aar_user, true ); ?> >Both</option>
		</select>
		<?php
			esc_attr_e( 'All scenarios will be affected only to the selected user.', 'auto-addtocart-rules' );
	}

	/**
	 * This function will create the checkbox for enabling  automatically add the product to cart only when the customer places their first order.
	 */
	public function aar_first_order() {
		$aar_firstorder = get_option( 'aar_firstorder' );
		?>
		<input type='checkbox' name='aar_firstorder' <?php checked( 1, $aar_firstorder, true ); ?> value='1'> <?php esc_attr_e( 'Enabling this will automatically add the product to cart only when the customer places their first order.', 'auto-addtocart-rules' ); ?>
		<?php
	}


	/**
	 * This function will create the checkbox to enable to set the free product price to 0.
	 */
	public function aar_set_price() {
		$aar_price = get_option( 'aar_price' );
		?>
		<input type='checkbox' name='aar_price' <?php checked( 1, $aar_price, true ); ?> value='1'> <?php esc_attr_e( 'Enabling this will set the price of the automatically added product to the cart to 0.', 'auto-addtocart-rules' ); ?>
		<?php
	}

	/**
	 * This function will create the checkbox to enable add to cart function
	 */
	public function aar_atc_product_enable() {
		$aar_checkfreeprd = get_option( 'aar_checkfreeprd' );
		?>
		<input type='checkbox' name='aar_checkfreeprd' <?php checked( 1, $aar_checkfreeprd, true ); ?> value='1'> <?php esc_attr_e( 'Enable this to automatically add multiple product to the cart when the product of multiple category is added to cart..', 'auto-addtocart-rules' ); ?>
		<?php
	}

	/**
	 * This function will create the text box for category id in add to cart function
	 */
	public function aar_atc_category() {
		$aar_oneprd = get_option( 'aar_oneprd' );

		if ( ! $aar_oneprd ) {
			$aar_oneprd = '';
		}

		$atc_prd = array(
			'taxonomy' => 'product_cat',
			'orderby'  => 'name',
		);
		$atc_cat = get_categories( $atc_prd );
		?>
		<select id="aar_atc_wc_cat_search"
				multiple="multiple" 
				style="width: 50%;" 
				name="aar_oneprd[productid][]">
		<?php
		foreach ( $atc_cat as $key => $product ) {
			$atc_product_id    = $product->term_id;
			$atc_product_title = $product->name;
			$product_id_int    = array_map( 'intval', $aar_oneprd['productid'] );
			$selected          = in_array( $atc_product_id, $product_id_int, true ) ? ' selected="selected" ' : '';
			echo '<option value="' . esc_attr( $atc_product_id ) . '" ' . esc_attr( $selected ) . ' >' . wp_kses_post( $atc_product_title ) . '</option>';
		}
		?>
		</select>
		</br><p> <?php esc_attr_e( 'Select category\'s  on which function will work.', 'auto-addtocart-rules' ); ?></p>
		<?php

	}

	/**
	 * This function will create the select2 for products in add to cart function
	 */
	public function aar_atc_product() {

		$aar_freeprd = get_option( 'aar_freeprd' );

		if ( ! $aar_freeprd ) {
			$aar_freeprd = '';
		}

		$atc_freeprd        = array(
			'posts_per_page' => 8,
			'post_type'      => 'product',
		);
		$atc_product_object = new WP_Query( $atc_freeprd );
		?>
		<select id="aar_atc_wc_product_search"
				multiple="multiple" 
				style="width: 50%;" 
				name="aar_freeprd[productid][]">
		<?php
		if ( $atc_product_object->have_posts() ) {
			$product_posts = $atc_product_object->get_posts();

			foreach ( $product_posts as $key => $product ) {
				$atc_product_id    = $product->ID;
				$atc_product_title = $product->post_title;
				$product_id_int    = array_map( 'intval', $aar_freeprd['productid'] );
				$selected          = in_array( $atc_product_id, $product_id_int, true ) ? ' selected="selected" ' : '';
				echo '<option value="' . esc_attr( $atc_product_id ) . '" ' . esc_attr( $selected ) . ' >' . wp_kses_post( $atc_product_title ) . '</option>';
			}
		}

		?>
		</select>
		</br><p> <?php esc_attr_e( 'Select product\'s which will be added to the cart when product of particular category is added to the cart.', 'auto-addtocart-rules' ); ?> </p>
		<?php

	}

	/**
	 * This function will create the checkbox to enable not to add free product once it is removed
	 */
	public function aar_atc_remove_product() {
		$aar_removefreeprd = get_option( 'aar_removefreeprd' );
		?>
		<input type='checkbox' name='aar_removefreeprd' <?php checked( 1, $aar_removefreeprd, true ); ?> value='1'> <?php esc_attr_e( 'Enabling this will not add the free product again to the cart once it is removed by the user.', 'auto-addtocart-rules' ); ?>
		<?php
	}

	/**
	 * This function will display under the header of add to cart section
	 */
	public function aar_atc_settings_add_to_cart_action() {

	}

	/**
	 * This function will create the checkbox to enable cart total function
	 */
	public function aar_ct_enable() {
		$aar_checktotalcart = get_option( 'aar_checktotalcart' );
		?>
		<input type='checkbox' name='aar_checktotalcart' <?php checked( 1, $aar_checktotalcart, true ); ?> value='1'> <?php esc_attr_e( 'Enable this to automatically add the product to cart when total of cart exceeds by certain amount.', 'auto-addtocart-rules' ); ?>
		<?php
	}

	/**
	 * This function will create the text box to enter amount in  cart total function
	 */
	public function aar_ct_enter_amount() {
		$aar_enteramt = get_option( 'aar_enteramt' );
		if ( ! $aar_enteramt ) {
			$aar_enteramt = '';
		}
		?>
		<input type='text' name='aar_enteramt' value='<?php echo esc_attr( $aar_enteramt ); ?>'>
		</br><p> <?php esc_attr_e( 'Enter amount of cart total on exceeds of which this function will work .', 'auto-addtocart-rules' ); ?> </p>
		<?php
	}

	/**
	 * This function will create the select2 for product  in cart total function
	 */
	public function aar_ct_product() {
		$aar_totalcart = get_option( 'aar_totalcart' );
		if ( ! $aar_totalcart ) {
			$aar_totalcart = '';
		}

		$ct_freeprd        = array(
			'posts_per_page' => 8,
			'post_type'      => 'product',
		);
		$ct_product_object = new WP_Query( $ct_freeprd );
		?>
		<select id="aar_ct_wc_product_search"
				multiple="multiple" 
				style="width: 50%;" 
				name="aar_totalcart[productid][]">
		<?php
		if ( $ct_product_object->have_posts() ) {
			$product_posts = $ct_product_object->get_posts();
			foreach ( $product_posts as $key => $product ) {
					$ct_product_id    = $product->ID;
					$ct_product_title = $product->post_title;
					$product_id_int   = array_map( 'intval', $aar_totalcart['productid'] );
					$selected         = in_array( $ct_product_id, $product_id_int, true ) ? ' selected="selected" ' : '';
					echo '<option value="' . esc_attr( $ct_product_id ) . '" ' . esc_attr( $selected ) . ' >' . wp_kses_post( $ct_product_title ) . '</option>';
			}
		}

		?>
		</select>
		</br><p> <?php esc_attr_e( 'Select product which will be added to the cart when cart total will exceeds by certain amount.', 'auto-addtocart-rules' ); ?> </p>
		<?php
	}

	/**
	 * This function will create the checkbox to enable not to add free product once it is removed
	 */
	public function aar_ct_remove_product() {
		$aar_removetotalfreeprd = get_option( 'aar_removetotalfreeprd' );
		?>
		<input type='checkbox' name='aar_removetotalfreeprd' <?php checked( 1, $aar_removetotalfreeprd, true ); ?> value='1'> <?php esc_attr_e( 'Enabling this will not add the free product again to the cart once it is removed by the user.', 'auto-addtocart-rules' ); ?>
		<?php
	}

	/**
	 * This function will display under the header of cart total section
	 */
	public function aar_ct_settings_cart_total() {

	}

	/**
	 * This function will create the checkbox to enable website visit function
	 */
	public function aar_wvf_product_enable() {
		$aar_checkvisit = get_option( 'aar_checkvisit' );
		if ( ! $aar_checkvisit ) {
			$aar_checkvisit = '';
		}
		?>
		<input type='checkbox' name='aar_checkvisit' <?php checked( 1, $aar_checkvisit, true ); ?> value='1'> <?php esc_attr_e( 'Enable this to automatically add the product to cart when customer visits your website.', 'auto-addtocart-rules' ); ?>
		<?php
	}

	/**
	 * This function will create the select2 for product select in website visit function.
	 */
	public function aar_wvf_product() {
		$aar_visit = get_option( 'aar_visit' );
		if ( ! $aar_visit ) {
			$aar_visit = '';
		}

		$wv_freeprd        = array(
			'posts_per_page' => 8,
			'post_type'      => 'product',
		);
		$wv_product_object = new WP_Query( $wv_freeprd );
		?>
		<select id="aar_wv_wc_product_search"
				multiple="multiple" 
				style="width: 50%;" 
				name="aar_visit[productid][]">
		<?php
		if ( $wv_product_object->have_posts() ) {
			$product_posts = $wv_product_object->get_posts();

			foreach ( $product_posts as $key => $product ) {
					$wv_product_id    = $product->ID;
					$wv_product_title = $product->post_title;
					$product_id_int   = array_map( 'intval', $aar_visit['productid'] );
					$selected         = in_array( $wv_product_id, $product_id_int, true ) ? ' selected="selected" ' : '';
				echo '<option value="' . esc_attr( $wv_product_id ) . '" ' . esc_attr( $selected ) . ' >' . wp_kses_post( $wv_product_title ) . '</option>';
			}
		}
		?>
		</select>
		</br><p> <?php esc_attr_e( 'Enter product which will be added to cart.', 'auto-addtocart-rules' ); ?></p>
		<?php
	}

	/**
	 * This function will display under the header of website visit section
	 */
	public function aar_ct_settings_website_visit() {

	}

	/**
	 * This function will display the main header & submit button of the page.
	 */
	public static function aar_options_page() {
		?>
			<form action='options.php' method='post'>

				<h2> <?php esc_attr_e( 'Automatically Add a Product Into Cart.', 'auto-addtocart-rules' ); ?></h2>
				<p> <?php esc_attr_e( 'This will automatically add a product to your WooCommerce cart in 3 different scenarios.', 'auto-addtocart-rules' ); ?></p>
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

new Aar_Settings();
