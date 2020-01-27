<?php
/**
 * Plugin Name: Auto Add To Cart Rules
 * Plugin URI:https://localhost/wordpress_shas/wordpress/wp-admin/plugins/cart_products_automatically
 * Description: A plugin which add products to the cart automatically when the given conditions comes true.
 * Version: 1.0
 * Requires at least: 5.2
 * Requires PHP:4.3
 * Author: Shasvat Shah
 * Author URI:https://www.shasvat.com
 * Text Domain:auto-addtocart-rules
 * Domain Path: /languages/
 *
 * @package Auto_Add_To_Cart_Rules
 */

/**
 * Exit if accessed directly
 */
defined( 'ABSPATH' ) || exit;

/**
 * Main Class
 */
class Auto_Add_To_Cart_Rules {
	/**
	 * Constructer
	 */
	public function __construct() {

			$this->aar_define_constants();
			$this->aar_hooks();
			$this->aar_settings_aipi();
	}

	/**
	 * This function contains all the hooks for the plugin.
	 */
	public function aar_hooks() {

		if ( $this->aar_is_woocommerce_active() ) {
			add_action( 'admin_menu', array( $this, 'aar_add_admin_menu' ), 80 );
			add_action( 'woocommerce_add_to_cart', array( $this, 'aar_add_oneproduct_to_cart' ), 10, 2 );
			add_action( 'template_redirect', array( $this, 'aar_add_freeproduct_to_cart' ) );
			add_action( 'template_redirect', array( $this, 'aar_remove_product_from_cart' ) );
			add_action( 'template_redirect', array( $this, 'aar_add_visitproduct_to_cart' ) );
			add_action( 'plugins_loaded', array( $this, 'aar_load_plugin_textdomain' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'aar_enqueue_script' ) );
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'aar_setprice' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'aar_error_notice' ) );
		}
	}

	/**
	 * Function for defining constatnts
	 */
	public function aar_define_constants() {
		define( 'AAR_VERSION', '1.0' );
		define( 'SETTINGS_FILE', dirname( __FILE__ ) . '/includes/class-aar-settings.php' );
	}

	/**
	 * Function to load settings file
	 */
	public function aar_settings_aipi() {
		include_once SETTINGS_FILE;
	}

	/**
	 * For Enqueuing JS script
	 */
	public function aar_enqueue_script() {

		wp_enqueue_style( 'aar-woocommerce_admin_styles', plugins_url() . '/woocommerce/assets/css/admin.css', '', AAR_VERSION, false );
		wp_register_script( 'select2', plugins_url() . '/woocommerce/assets/js/select2/select2.min.js', array( 'jquery', 'jquery-ui-widget', 'jquery-ui-core' ), 'AAR_VERSION', false );
		wp_enqueue_script( 'select2' );
		wp_enqueue_script( 'aar_product_search', plugin_dir_url( __FILE__ ) . '/assets/js/aar_product_search.js', array( 'jquery', 'select2' ), 'AAR_VERSION', true );
		wp_localize_script( 'aar_product_search-ajax-script', 'aar_product_search_ajax_obj', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}

	/**
	 * Function to display a notice to user if the WooCommerce plugin is deactive.
	 *
	 * @hook admin_notices
	 */
	public function aar_error_notice() {

		?>
		<div class='error'>
		<p><b>
				<?php esc_attr__( 'Auto Add To Cart Rules is enabled but not effective. It requires  WooCommerce Plugin activated in order to work.', 'auto-addtocart-rules' ); ?>
			</b></p>
		</div>
		<?php
	}

	/**
	 * Function to check if WooCommerce Plugin is active or not.
	 *
	 * @return bool $check true if WooCommerce Plugin is active else false.
	 */
	public function aar_is_woocommerce_active() {
		$check          = false;
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

		if ( in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) ) {
			$check = true;
		}
		return $check;
	}

	/**
	 * This function will load the text domain
	 */
	public function aar_load_plugin_textdomain() {

		load_plugin_textdomain( 'auto-addtocart-rules', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * This function will add menu in admin panel
	 */
	public function aar_add_admin_menu() {

		add_submenu_page( 'woocommerce', esc_attr__( 'Add To Cart Rules', 'auto-addtocart-rules' ), esc_attr__( 'Add To Cart Rules', 'auto-addtocart-rules' ), 'manage_options', 'aar_cart_products_automatically', array( 'Aar_settings', 'aar_options_page' ) );

	}

	/**
	 * This Function will check if the customer had any previous order or not
	 *
	 * @return bool "false" when customer has already at least one order (true if not)
	 */
	public function aar_is_order_available() {

		$args = array(
			'customer_id' => get_current_user_id(),
		);

		$orders = wc_get_orders( $args );
		return count( $orders ) > 0 ? false : true;
	}

	/**
	 * This function will check the role of the user dependent on that other function will work.
	 *
	 * @return bool $user as per different conditions.
	 */
	public function aar_check_user_role() {

		$aar_user = get_option( 'aar_user' );
		$user     = true;

		if ( 'loggedin' === $aar_user ) {

			if ( is_user_logged_in() ) {

				$aar_firstorder = get_option( 'aar_firstorder' );
				if ( '1' === $aar_firstorder ) {
					$user = $this->aar_is_order_available();
				} else {
					$user = true;
				}
			} else {
				$user = false;
			}
		} else {
			$user = false;
		}

		if ( 'guest' === $aar_user ) {

			if ( is_user_logged_in() ) {
					$user = false;
			} else {
					$user = true;
			}
		}

		if ( 'both' === $aar_user ) {

			if ( is_user_logged_in() ) {

				$aar_firstorder = get_option( 'aar_firstorder' );

				if ( '1' === $aar_firstorder ) {
					$user = $this->aar_is_order_available();
				} else {
					$user = true;
				}
			} else {
					$user = true;
			}
		}
		return $user;
	}

	/**
	 * This function is for adding the multiple product to the cart if the
	 * users adds the multiple category product to the cart.
	 *
	 * @param int $item_key Item Key.
	 * @param int $product_id Product Id.
	 */
	public function aar_add_oneproduct_to_cart( $item_key, $product_id ) {

		$user_role        = $this->aar_check_user_role();
		$aar_checkfreeprd = get_option( 'aar_checkfreeprd' );

		if ( $user_role ) {

			if ( '1' === $aar_checkfreeprd ) {

				$remove              = true;
				$aar_one             = get_option( 'aar_oneprd' );
				$aar_free            = get_option( 'aar_freeprd' );
				$aar_removefreeprd   = get_option( 'aar_removefreeprd' );
				$aar_freeprd         = $aar_free['productid'];
				$product_category_id = $aar_one['productid'];

				if ( '1' === $aar_removefreeprd ) {

					if ( isset( WC()->session ) && ! is_null( WC()->session->get( 'removed_cart_contents' ) ) && WC()->session->get( 'removed_cart_contents' ) !== '' ) { // checking is any products is removed or not.

						$removed_cart_contents = WC()->session->get( 'removed_cart_contents' );

						foreach ( $removed_cart_contents as $key => $value ) {
							$list_of_removed_product[] = $value['product_id'];
						}

						$list_of_removed_product = array_unique( $list_of_removed_product );

						foreach ( $aar_freeprd as $key => $free_product_ids ) {

							if ( in_array( (int) $free_product_ids, $list_of_removed_product, true ) ) { // If free product found in 													list of removed product then do nothing.
								$remove = false;
								break; // do nothing if product is already removed by customer.
							}
						}
					}
				}

				if ( $remove ) {

					$product_cats_ids          = wc_get_product_term_ids( $product_id, 'product_cat' ); // Getting assigned categories of product which is being added to cart.
					$product_category_id_check = false;

					foreach ( $product_category_id as $key => $value ) {
						if ( in_array( (int) $value, $product_cats_ids, true ) ) { // Checking if the specified category is being 													  matched or not.
							$product_category_id_check = true;
						}
					}

					if ( ! is_admin() && $product_category_id_check ) {

						$free_product_ids = $aar_freeprd;// Product Ids of the free products which will 													  get added to cart e.g Gift A & Gift B.

						foreach ( $free_product_ids as $pkey => $free_product_id ) {

							$found = false;
							// check if product already in cart.

							if ( count( WC()->cart->get_cart() ) > 0 ) {

								foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

									$_product = $values['data'];

									if ( $_product->get_id() === (int) $free_product_id ) {
										$found = true;
									}
								}

								if ( ! $found ) {
									WC()->cart->add_to_cart( $free_product_id );
								}
							} else {
								// if no products in cart, add it.
								WC()->cart->add_to_cart( $free_product_id );
							}
						}
					}
				}
			}
		}
	}

	/**
	 * This function is for adding the product when
	 * the total of cart exceeds certain amount.
	 */
	public function aar_add_freeproduct_to_cart() {

		$user_role          = $this->aar_check_user_role();
		$aar_checktotalcart = get_option( 'aar_checktotalcart' );

		if ( $user_role ) {

			if ( '1' === $aar_checktotalcart ) {

				$remove = true;
				global $woocommerce;
				$cart_total   = get_option( 'aar_enteramt' );
				$free_product = get_option( 'aar_totalcart' );

				$array = array_values( $free_product );

				foreach ( $array as $key => $value ) {
					$product = $value;
				}

				foreach ( $product as $key => $value ) {
					$free_product_id = $value;
				}

				$remove = $this->aar_ct_remove_product();

				if ( $remove ) {

					global $woocommerce;

					if ( $woocommerce->cart->total >= $cart_total ) {

						if ( ! is_admin() ) {

							$free_product = get_option( 'aar_totalcart' );
							$array        = array_values( $free_product );

							foreach ( $array as $key => $value ) {
								$product = $value;
							}

							foreach ( $product as $key => $value ) {
								$free_product_id = $value;
							}
							// Product Id of the free product which will 													get added to cart.

							$found = false;

							// check if product already in cart.

							if ( count( WC()->cart->get_cart() ) > 0 ) {

								foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

									$_product = $values['data'];

									if ( $_product->get_id() === (int) $free_product_id ) {
										$found = true;
									}
								}
								// if product not found, add it.
								if ( ! $found ) {
									WC()->cart->add_to_cart( $free_product_id );
								}
							} else {
								// if no products in cart, add it.
								WC()->cart->add_to_cart( $free_product_id );
							}
						}
					} /* This else will remove the automatically added product from the cart when the cart total is less than the certain amount added by the admin. */ else {

						if ( ! is_admin() ) {

							$free_product = get_option( 'aar_totalcart' );
							$array        = array_values( $free_product );

							foreach ( $array as $key => $value ) {
								$product = $value;
							}

							foreach ( $product as $key => $value ) {
								$free_product_id = $value;
							} // Product Id of the free product which will 													get added to cart

							$found = false;

							// check if product already in cart.
							if ( count( WC()->cart->get_cart() ) > 0 ) {

								foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

									$_product = $values['data'];

									if ( $_product->get_id() !== (int) $free_product_id ) {
										$found = false;
									} else {
										$found = true;
										break;
									}
								}
								if ( $found ) {
									$free_pro_cart_id = WC()->cart->generate_cart_id( $free_product_id );
									unset( WC()->cart->cart_contents[ $free_pro_cart_id ] );
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * This function will automatically adds the free product
	 * to the cart when customer visits our website.
	 */
	public function aar_add_visitproduct_to_cart() {

		$user_role      = $this->aar_check_user_role();
		$aar_checkvisit = get_option( 'aar_checkvisit' );
		if ( $user_role ) {

			if ( '1' === $aar_checkvisit ) {

				if ( ! is_admin() && ! is_cart() && ! is_checkout() ) {

					$product_id = get_option( 'aar_visit' ); // Product Id of the free product which will get added to cart.
					$array      = array_values( $product_id );

					foreach ( $array as $key => $value ) {
						$product = $value;
					}
					foreach ( $product as $key => $value ) {
						$products = $value;
					}
					$found = false;

					// check if product already in cart.
					if ( count( WC()->cart->get_cart() ) > 0 ) {

						foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
							$_product = $values['data'];

							if ( $_product->get_id() === (int) $products ) {
								$found = true;
							}
						}

						// if product not found, add it.
						if ( ! $found ) {
							WC()->cart->add_to_cart( $products );
						}
					} else {
						// if no products in cart, add it.
						WC()->cart->add_to_cart( $products );
					}
				}
			}
		}
	}

	/**
	 * This function will remove the automatically added product when the product is removed by the user.
	 */
	public function aar_remove_product_from_cart() {
		$aar_checkfreeprd = get_option( 'aar_checkfreeprd' );

		if ( '1' === $aar_checkfreeprd ) {

			$aar_one     = get_option( 'aar_oneprd' );
			$aar_freeprd = get_option( 'aar_freeprd' );

			// Run only in the Cart or Checkout Page.
			if ( is_cart() || is_checkout() ) {

				$product_category_id = $aar_one['productid'];

				$prod_to_remove = $aar_freeprd['productid'];

				$cart_contains_category = false; // Default set to false : This means cart doesn't contains any product of perticular category.

				$free_pro_cart_id = '';

				foreach ( WC()->cart->cart_contents as $prod_in_cart ) {

					// Get the Variation or Product ID.
					$prod_id          = ( isset( $prod_in_cart['variation_id'] ) && 0 !== $prod_in_cart['variation_id'] ) ? $prod_in_cart['variation_id'] : $prod_in_cart['product_id'];
					$product_cats_ids = wc_get_product_term_ids( $prod_id, 'product_cat' );

					foreach ( $product_category_id as $key => $value ) {

						if ( in_array( (int) $value, $product_cats_ids, true ) ) {
							$cart_contains_category = true; // cart has the product of particular category.
							break;
						}
					}
				}

				foreach ( $prod_to_remove as $key => $value1 ) {

					if ( ! $cart_contains_category ) { // remove free product if cart doesn't contain product of perticular category.
						$free_pro_cart_id = WC()->cart->generate_cart_id( $value1 );
						// Remove it from the cart by un-setting it.
						unset( WC()->cart->cart_contents[ $free_pro_cart_id ] );
					}
				}
			}
		}
	}

	/**
	 * This function will not automatically add the product to the cart once it is removed by the customer.
	 */
	public function aar_ct_remove_product() {

		$remove                  = true;
		$aar_removetotalfreeprd  = get_option( 'aar_removetotalfreeprd' );
		$list_of_removed_product = array(); // In this we will add all the removed product ids.
		$free_product            = get_option( 'aar_totalcart' );
		$array                   = array_values( $free_product );

		foreach ( $array as $key => $value ) {
			$product = $value;
		}

		foreach ( $product as $key => $value ) {
			$free_product_id = $value;
		} // Product Id of the free product which will get added to cart.

		if ( '1' === $aar_removetotalfreeprd ) {

			if ( isset( WC()->session ) && ! is_null( WC()->session->get( 'removed_cart_contents' ) ) && WC()->session->get( 'removed_cart_contents' ) !== '' ) { // checking is any products is removed or not.

				$removed_cart_contents = WC()->session->get( 'removed_cart_contents' );

				foreach ( $removed_cart_contents as $key => $value ) {
					$list_of_removed_product[] = $value['product_id'];
				}

				$list_of_removed_product = array_unique( $list_of_removed_product );

			}
			if ( in_array( (int) $free_product_id, $list_of_removed_product, true ) ) { // If free product found in list of 																	removed product then do nothing.
					$remove = false;

			}
		}

		return $remove;
	}

	/**
	 * This function is to set the price of the automatically added product to the cart to 0.
	 *
	 * @param array $cart_object Cart object.
	 */
	public function aar_setprice( $cart_object ) {
		$aar_price = get_option( 'aar_price' );

		if ( '1' === $aar_price ) {

			$atc_free_product_ids = '';
			$ct_free_product_ids  = '';
			$wvf_free_product_ids = '';
			$aar_checkfreeprd     = get_option( 'aar_checkfreeprd' );
			$aar_checktotalcart   = get_option( 'aar_checktotalcart' );
			$aar_checkvisit       = get_option( 'aar_checkvisit' );
			$custom_price         = 0; // This will be your custome price.

			if ( '1' === $aar_checkfreeprd ) {

				$aar_free    = get_option( 'aar_freeprd' );
				$aar_freeprd = $aar_free['productid'];
				foreach ( $aar_freeprd as $key => $value ) {
					$atc_free_product_ids = $value;
				}
			}

			if ( '1' === $aar_checktotalcart ) {

				$free_product = get_option( 'aar_totalcart' );
				$array        = array_values( $free_product );

				foreach ( $array as $key => $value ) {
					$product = $value;
				}
				foreach ( $product as $key => $value ) {
					$ct_free_product_ids = $value;
				}
			}

			if ( '1' === $aar_checkvisit ) {

				$product_id = get_option( 'aar_visit' );
				$array      = array_values( $product_id );

				foreach ( $array as $key => $value ) {
					$product = $value;
				}
				foreach ( $product as $key => $value ) {
					$wvf_free_product_ids = $value;
				}
			}

			$free_product_id = array( $atc_free_product_ids, $ct_free_product_ids, $wvf_free_product_ids );

			foreach ( $cart_object->cart_contents as $key => $value ) {
					$_product = $value['data'];
					$product  = $_product->get_id();

				if ( in_array( $product, array_map( 'intval', $free_product_id ), true ) ) {

						$value['data']->set_price( $custom_price );

				}
			}
		}
	}

}

new Auto_Add_To_Cart_Rules();
