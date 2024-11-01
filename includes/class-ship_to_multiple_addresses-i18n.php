<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://samuilmarinov.co.uk
 * @since      1.0.0
 *
 * @package    Ship_to_multiple_addresses
 * @subpackage Ship_to_multiple_addresses/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Ship_to_multiple_addresses
 * @subpackage Ship_to_multiple_addresses/includes
 * @author     samuil marinov <samuil.marinov@gmail.com>
 */
class Ship_to_multiple_addresses_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ship_to_multiple_addresses',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
