<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://samuilmarinov.co.uk
 * @since             1.2.0
 * @package           Ship_to_multiple_addresses
 *
 * @wordpress-plugin
 * Plugin Name:       Ship to Multiple Addresses
 * Plugin URI:        samuilmarinov.co.uk
 * Description:       This plugin allows shipping to multiple addresses.
 * Version:           1.2.0
 * Author:            samuil marinov
 * Author URI:        https://samuilmarinov.co.uk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ship_to_multiple_addresses
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.2.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SHIP_TO_MULTIPLE_ADDRESSES_VERSION', '1.2.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ship_to_multiple_addresses-activator.php
 */
function activate_ship_to_multiple_addresses() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ship_to_multiple_addresses-activator.php';
	Ship_to_multiple_addresses_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ship_to_multiple_addresses-deactivator.php
 */
function deactivate_ship_to_multiple_addresses() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ship_to_multiple_addresses-deactivator.php';
	Ship_to_multiple_addresses_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ship_to_multiple_addresses' );
register_deactivation_hook( __FILE__, 'deactivate_ship_to_multiple_addresses' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ship_to_multiple_addresses.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.2.0
 */
function run_ship_to_multiple_addresses() {

	$plugin = new Ship_to_multiple_addresses();

//PLUGIN LINK
function ship_to_multiple_addresses_action_links( $links ) {
    $links = array_merge( array(
        '<a target="_blank" style="padding:3px 5px; background:red; color:white; border-radius:15px;" href="https://samuilmarinov.co.uk/product/ship-to-multiple-addresses-pro/">GET PRO!</a>',
        '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=products&section=destinations_text_section' ) ) . '">' . __( 'Settings', 'ship_to_multiple_addresses' ) . '</a>',
        '<img style="position: absolute; left: 17rem; margin-top: -1.8rem;" width=128 height=128 src="/wp-content/plugins/ship-to-multiple-addresses/admin/icon-256x256.png">'
    ), $links );

    return $links;
}

add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ship_to_multiple_addresses_action_links' );
//PLUGIN LINK
// Hook before adding fees 
add_action('woocommerce_cart_calculate_fees' , 'add_custom_fees');
        
function add_custom_fees( WC_Cart $cart ){
    $b_title = 'Multiple Destinations';
    $product = get_page_by_title( $b_title, OBJECT, 'product' );
    $prod_id = $product->ID;

     $in_cart = false;
  foreach( WC()->cart->get_cart() as $cart_item ) {
$product_in_cart = $cart_item['product_id'];
if ( $product_in_cart === $prod_id ) $in_cart = true;
}

if ( $in_cart ) {  
global $woocommerce;
    $count = $woocommerce->cart->cart_contents_count;
    $items = $woocommerce->cart->get_cart();
    $mult = $count -1;
    $individual_price_on = get_option('destinations_price_section_on');

    if($individual_price_on === 'yes'){
        $shipping_total =  WC()->cart->shipping_total;
    }else{
        $shipping_total = get_option('destinations_individual_section');
    }
     
    $fees = $mult*$shipping_total;
    $cart->add_fee( 'Handling fee', $fees);

}
}

//ADD BUTTON TO CART
add_action( 'woocommerce_after_cart_table', 'woo_add_continue_shopping_button_to_cart' );
function woo_add_continue_shopping_button_to_cart() {
global $woocommerce;
$main_funct = get_option('destinations_main_section_on');
if($main_funct === 'yes'){
 $shop_page_url = get_permalink( woocommerce_get_page_id( 'shop' ) );
 $b_title = 'Multiple Destinations';
 $product = get_page_by_title( $b_title, OBJECT, 'product' );
 $prod_id = $product->ID;
 $theme = wp_get_theme();
$in_cart = false;
foreach( WC()->cart->get_cart() as $cart_item ) {
$product_in_cart = $cart_item['product_id'];
if ( $product_in_cart === $prod_id ) $in_cart = true;
}
$count = $woocommerce->cart->cart_contents_count;
if ( !$in_cart && $count > 1) {  
 if('XStore' == $theme->name || 'XStore' == $theme->parent_theme){
   echo '<a href="/cart/?add-to-cart='.$prod_id.'"><i style="font-size: 2rem; vertical-align: sub;" class="et-icon et-delivery"></i>Ship to multiple addresses</a>';
 }else{
 echo '<div class="woocommerce-message">';
 echo ' <a href="/cart/?add-to-cart='.$prod_id.'" class="button">Ship to multiple addresses →</a>';
 echo '</div>';
 }
}

}else{
    //NADA
}
}
//ADD CUSTOM META TO ENABLE INDIVIDUALLY SOLD
//CREATE PRODUCT
add_action('wp_head', 'product_multi');
function product_multi(){
  $b_title = 'Multiple Destinations';
  $product = get_page_by_title( $b_title, OBJECT, 'product' );
  $prod_id = $product->ID; 
if ( ! is_admin() ) {
    require_once( ABSPATH . 'wp-admin/includes/post.php' );
}
if ( 0 === post_exists( $b_title ) ) {
$args = array(  
  'post_author' => 1, 
  'post_content' => 'Multiple Destinations',
  'post_status' => "Publish", // (Draft | Pending | Publish)
  'post_title' => 'Multiple Destinations',
  'post_parent' => '',
  'post_type' => "product"
); 

// Create a simple WooCommerce product
$post_id = wp_insert_post( $args );

// Setting the product type
wp_set_object_terms( $post_id, 'simple', 'product_type' );

// Setting the product price

update_post_meta( $post_id, '_price', 0 );
update_post_meta( $post_id, '_regular_price', 0 );
update_post_meta( $post_id, '_product_attributes', array() );
update_post_meta( $post_id, '_virtual', 'yes');
update_post_meta( $post_id, '_sold_individually', TRUE );
update_post_meta($post_id,"_visibility", '');
$terms = array( 'exclude-from-search', 'exclude-from-catalog' ); // for hidden..
wp_set_post_terms( $post_id, $terms, 'product_visibility', false );

$postdate = '2010-02-23 18:57:33';

            $my_args = array(
               'ID' => $post_id,
               'post_date' => $postdate
            );

            if ( ! wp_is_post_revision( $post_id ) ){

                    // unhook this function so it doesn't loop infinitely
                    remove_action('save_post', 'my_function');

                    // update the post, which calls save_post again
                    wp_update_post( $my_args );

                    // re-hook this function
                    add_action('save_post', 'my_function');
            }

//ADD FEATURED IMAGE
// Add Featured Image to Post
$image_url        = 'http://samuilmarinov.co.uk/woo/Multiple_Destinations_1-512.png'; // Define the image URL here
$image_name       = 'wp-header-logo.png';
$upload_dir       = wp_upload_dir(); // Set upload folder
$image_data       = file_get_contents($image_url); // Get image data
$unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
$filename         = basename( $unique_file_name ); // Create image file name

// Check folder permission and define file location
if( wp_mkdir_p( $upload_dir['path'] ) ) {
	$file = $upload_dir['path'] . '/' . $filename;
} else {
	$file = $upload_dir['basedir'] . '/' . $filename;
}

// Create the image  file on the server
file_put_contents( $file, $image_data );

// Check image file type
$wp_filetype = wp_check_filetype( $filename, null );

// Set attachment data
$attachment = array(
	'post_mime_type' => $wp_filetype['type'],
	'post_title'     => sanitize_file_name( $filename ),
	'post_content'   => '',
	'post_status'    => 'inherit'
);

// Create the attachment
$attach_id = wp_insert_attachment( $attachment, $file, $post_id );

// Include image.php
require_once(ABSPATH . 'wp-admin/includes/image.php');

// Define attachment metadata
$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

// Assign metadata to attachment
wp_update_attachment_metadata( $attach_id, $attach_data );

// And finally assign featured image to post
set_post_thumbnail( $post_id, $attach_id );

} else {
  // The post exists
}


}
//Custom WooCommerce Checkout Fields based on Quantity
add_action('woocommerce_after_checkout_billing_form', 'person_details');
function person_details($checkout)
{ 
  $b_title = 'Multiple Destinations';
  $product = get_page_by_title( $b_title, OBJECT, 'product' );
        $prod_id = $product->ID;

   $in_cart = false;
  
   foreach( WC()->cart->get_cart() as $cart_item ) {
      $product_in_cart = $cart_item['product_id'];
      if ( $product_in_cart === $prod_id ) $in_cart = true;
   }
   $main_funct = get_option('destinations_main_section_on');
   if($main_funct === 'yes'){

   if ( $in_cart ) {
   global $woocommerce;
   $count = $woocommerce->cart->cart_contents_count;
   $items = $woocommerce->cart->get_cart();
   $i=0;

      foreach($items as $item => $values) { 
    
             
        $up_to = $values['quantity'] -1;
        for ($x = 0; $x <= $up_to; $x++) {
        $i++;
            $_product =  wc_get_product( $values['data']->get_id()); 
             $actual = $x + 1;
             $d_title = $_product->get_title();
             if($d_title != 'Multiple Destinations'){
                echo '<style>#ship-to-different-address{display:none;}</style>'; 
                print ('<h3>Please enter details for  '.$actual.' - '. $_product->get_title() .'</h3>');
                woocommerce_form_field('cstm_product_name' . $i, array(
                  'type' => 'hidden',
                  'class' => array(
                      'my-field-class form-row-wide'
                  ) ,
                  'default' => $_product->get_title(),  
              ) , $checkout->get_value('cstm_product_name' . $i));
              echo '<div class="clear"></div>';
              woocommerce_form_field('cstm_full_name' . $i, array(
                  'type' => 'text',
                  'class' => array(
                      'my-field-class form-row-wide'
                  ) ,
                  'label' => __('Full name') ,
                  'placeholder' => __('Enter full name') ,
              ) , $checkout->get_value('cstm_full_name' . $i));
              echo '<div class="clear"></div>';
              woocommerce_form_field('cstm_phone' . $i, array(
                  'type' => 'text',
                  'class' => array(
                      'my-field-class form-row-first'
                  ) ,
                  'label' => __('Phone') ,
                  'placeholder' => __('Enter phone number') ,
              ) , $checkout->get_value('cstm_phone' . $i));
              woocommerce_form_field('cstm_email' . $i, array(
                  'type' => 'email',
                  'class' => array(
                      'my-field-class form-row-last'
                  ) ,
                  'label' => __('Email address') ,
                  'placeholder' => __('Enter email address') ,
              ) , $checkout->get_value('cstm_email' . $i));
              echo '<div class="clear"></div>';
              woocommerce_form_field('cstm_address' . $i, array(
                  'type' => 'textarea',
                  'class' => array(
                      'my-field-class form-row-wide'
                  ) ,
                  'label' => __('Full address') ,
                  'placeholder' => __('Enter full address') ,
              ) , $checkout->get_value('cstm_address' . $i));
              $gift_message = get_option('destinations_gift_section_on');
              if($gift_message === 'yes'){ 
                echo '<div class="clear"></div>';
                woocommerce_form_field('cstm_custom_message' . $i, array(
                    'type' => 'textarea',
                    'class' => array(
                        'my-field-class form-row-wide'
                    ) ,
                    'label' => __('Custom Message') ,
                    'placeholder' => __('your message ...') ,
                ) , $checkout->get_value('cstm_custom_message' . $i));

              }
              

          }

        }
      }
  }
}
}
/**
 * Save value of fields
 */
add_action('woocommerce_checkout_update_order_meta', 'customise_checkout_field_update_order_meta');
function customise_checkout_field_update_order_meta($order_id)
{
    global $woocommerce;
    $count = $woocommerce->cart->cart_contents_count;
    $i = 0;
    for ($k = 1;$k <= $count;$k++)
    {
        $i++;
        if (!empty($_POST['cstm_product_name' . $i]))
        {
            update_post_meta($order_id, 'product_' . $i, sanitize_text_field($_POST['cstm_product_name' . $i]));
        }
        if (!empty($_POST['cstm_full_name' . $i]))
        {
            update_post_meta($order_id, 'name_' . $i, sanitize_text_field($_POST['cstm_full_name' . $i]));
        }
        if (!empty($_POST['cstm_phone' . $i]))
        {
            update_post_meta($order_id, 'phone_' . $i, sanitize_text_field($_POST['cstm_phone' . $i]));
        }
        if (!empty($_POST['cstm_email' . $i]))
        {
            update_post_meta($order_id, 'email_' . $i, sanitize_text_field($_POST['cstm_email' . $i]));
        }
        if (!empty($_POST['cstm_address' . $i]))
        {
            update_post_meta($order_id, 'address_' . $i, sanitize_text_field($_POST['cstm_address' . $i]));
        }
        $gift_message = get_option('destinations_gift_section_on');
        if($gift_message === 'yes'){ 
        if (!empty($_POST['cstm_custom_message' . $i]))
        {
            update_post_meta($order_id, 'custommessage_' . $i, sanitize_text_field($_POST['cstm_custom_message' . $i]));
        } 
        }
       
    }
}


/**
 * Add fields to order emails
 *
 */
add_filter('woocommerce_email_order_meta_keys', 'my_custom_checkout_field_order_meta_keys');
function my_custom_checkout_field_order_meta_keys($keys)
{
    $i = 0;
    for ($k = 1;$k <= 50;$k++)
    {
        $i++;
        $keys[] = 'product_' . $i;
        $keys[] = 'name_' . $i;
        $keys[] = 'phone_' . $i;
        $keys[] = 'email_' . $i;
        $keys[] = 'address_' . $i;
        $gift_message = get_option('destinations_gift_section_on');
        if($gift_message === 'yes'){
        $keys[] = 'custommessage_' . $i;
        }
    }
    return $keys;
}


//ADD ADDRESSES TO ORDER BACKEND NOTES
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'mm_addresses_admin_order_meta', 10, 3 );
   
function mm_addresses_admin_order_meta( $order) {    
  
 
$items = $order->get_items(); 
foreach ( $items as $item_id => $item ) {
    $product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
    $b_title = 'Multiple Destinations';
  $product = get_page_by_title( $b_title, OBJECT, 'product' );
  $prod_id = $product->ID;
   if ( $product_id === $prod_id ) {
    
$i = 0;
    for ($k = 1;$k <= 50;$k++)
    {
        $i++;
        $product = 'product_' . $i;
        $name = 'name_' . $i;
        $phone = 'phone_' . $i;
        $email = 'email_' . $i;
        $address = 'address_' . $i;
        $gift_message_text = 'custommessage_' . $i;
        $prod_check = get_post_meta( $order->get_id(), $product, true );
        
        if($prod_check != ''){
        echo '<div style="padding-bottom:1rem; border-bottom:1px solid black;" class="order_details">';
         echo '<p style="display:flex;line-height: 0;"><strong style="width:60px; margin-right:1rem;">'.__('Product'.$i.'').':</strong>' . get_post_meta( $order->get_id(), $product, true ) . '</p>';
          echo '<p style="display:flex;line-height: 0;"><strong style="width:60px; margin-right:1rem;">'.__('Name '.$i.'').':</strong>' . get_post_meta( $order->get_id(), $name, true ) . '</p>';
           echo '<p style="display:flex;line-height: 0;"><strong style="width:60px; margin-right:1rem;">'.__('Phone'.$i.'').':</strong>' . get_post_meta( $order->get_id(), $phone, true ) . '</p>';
            echo '<p style="display:flex;line-height: 0;"><strong style="width:60px; margin-right:1rem;">'.__('Email'.$i.'').':</strong>' . get_post_meta( $order->get_id(), $email, true ) . '</p>';
             echo '<p style="display:flex;line-height: 0;"><strong style="width:60px; margin-right:1rem;">'.__('Address'.$i.'').':</strong>' . get_post_meta( $order->get_id(), $address, true ) . '</p>';
             $gift_message = get_option('destinations_gift_section_on');
             if($gift_message === 'yes'){
                echo '<p style="display:flex;line-height: 0;"><strong style="width:60px; margin-right:1rem;">'.__('Message'.$i.'').':</strong>' . get_post_meta( $order->get_id(), $gift_message_text, true ) . '</p>'; 
             }
             echo '</div>';
         }    
    }


   }
}
    
}
//ADD OPTIONS
add_filter( 'woocommerce_get_sections_products' , 'destinations_add_settings_tab' );

function destinations_add_settings_tab( $settings_tab ){
     $settings_tab['destinations_text_section'] = __( 'Ship to multiple addresses' );
     return $settings_tab;
}

add_filter( 'woocommerce_get_settings_products' , 'destinations_get_settings' , 10, 2 );

function destinations_get_settings( $settings, $current_section ) {
         $custom_settings = array();
        
        if( 'destinations_text_section' == $current_section ) {
       

              $custom_settings =  array(
               		  array(
                            'name' => __( 'Ship to multiple addresses' ),
                            'type' => 'title',
                            'id'   => 'destinations_title'
                      ),
                      array(
                        'name' => __( 'Turn plugin ON/OFF' ),
                        'label' => __( 'Enable plugin functionality' ),
                        'type'          => 'radio',
                        'default' =>    'no',
                        'id'   => 'destinations_main_section_on',
                        'options' => array(
                            'no' => __('OFF'),
                            'yes' => __('ON')
                        )
                      ),
                    array(
                        'name' => __( 'SHIPPING COST' ),
                        'desc' => __( '' ),
                        'label' => __( 'Use selected shipping price' ),
                        'type'          => 'radio',
                        'default' =>    'no',
                        'id'   => 'destinations_price_section_on',
                        'options' => array(
                            'yes' => __('CALCULATE DEFAULT SHIPPING COST WHEN SHIPPING TO MULTIPLE ADDRESSES'),
                            'no' => __('ADD FIXED FEE PER PRODUCT WHEN SHIPPING TO MULTIPLE ADDRESSES (input below)')
                        )
                      ),
                      array(
                            'name' => __( 'FIXED FEE PER PRODUCT' ),
                            'type' => 'text',
                            'id'   => 'destinations_individual_section'
                      ), 
                        array(
                            'name' => __( 'ALLOW CUSTOM NOTE OPTION' ),
                            'label' => __( 'ALLOW CUSTOM NOTE OPTION' ),
                            'type'          => 'radio',
                            'default' =>    'no',
                            'id'   => 'destinations_gift_section_on',
                            'options' => array(
                                'no' => __('NO'),
                                'yes' => __('YES')
                            )
                        )

        );

           return $custom_settings;
       } else {
            return $settings;
       }

}
// ADD OPTIONS

//END
	$plugin->run();

}
run_ship_to_multiple_addresses();
