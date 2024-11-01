<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://samuilmarinov.co.uk
 * @since      1.0.0
 *
 * @package    Ship_to_multiple_addresses
 * @subpackage Ship_to_multiple_addresses/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Ship_to_multiple_addresses
 * @subpackage Ship_to_multiple_addresses/includes
 * @author     samuil marinov <samuil.marinov@gmail.com>
 */
class Ship_to_multiple_addresses_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		$b_title = 'Multiple Destinations';
		$product = get_page_by_title( $b_title, OBJECT, 'product' );
		$prod_id = $product->ID;
		wp_delete_post( $prod_id, true); 
	}

}
