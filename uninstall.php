<?php
	/**
	 * Exit if uninstall constant is not defined
	 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
		exit;
}
	// delete plugin options.
	delete_option( 'aar_user' );
	delete_option( 'aar_firstorder' );
	delete_option( 'aar_oneprd' );
	delete_option( 'aar_freeprd' );
	delete_option( 'aar_checkfreeprd' );
	delete_option( 'aar_removefreeprd' );
	delete_option( 'aar_removetotalfreeprd' );
	delete_option( 'aar_enteramt' );
	delete_option( 'aar_totalcart' );
	delete_option( 'aar_checktotalcart' );
	delete_option( 'aar_visit' );
	delete_option( 'aar_checkvisit' );
	delete_option( 'aar_login' );


