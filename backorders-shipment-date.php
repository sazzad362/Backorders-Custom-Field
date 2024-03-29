<?php 
/*
 * Plugin Name: Backorders Shipment Date
 * Plugin URI: https://www.smarterdevs.com/
 * Description: Backorders text. Add a custom backorders text to be displayed when products are on backorders.
 * Version: 1.0
 * Author: Sazzad Hossain Salim
 * Author URI: https://sazzad362.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

add_action( 'woocommerce_product_options_stock_fields', 'add_product_options_stock_custom_field', 20 );
function add_product_options_stock_custom_field() {
    global $product_object, $post;

    woocommerce_wp_text_input( array(
        'id'          => '_backorder_text',
        'type'        => 'text',
        'label'       => __( 'Backorders Shipment Date', 'woocommerce' ),
        'description' => __( 'Backorders text. Add a custom backorders text to be displayed when products are on backorders.', 'woocommerce' ),
        'desc_tip'    => true,
    ) );

    // jQuery: HIDE the fied if backorders are not enabled
    ?>
    <script type="text/javascript">
    jQuery( function($){
        var a = 'select#_backorders',
            b = 'p._backorder_text_field';

        if( $(a).val() === 'no' )
            $(b).hide();

        $(a).on('change blur', function(){
            if( $(a).val() === 'no' )
                $(b).hide();
            else
                $(b).show();
        });
    });
    </script>
    <?php
}

// Save the custom field value from admin product edit pages - inventory tab
add_action( 'woocommerce_process_product_meta', 'save_product_options_stock_custom_field', 20, 1 );
function save_product_options_stock_custom_field( $product_id ) {
    if ( isset( $_POST['_backorder_text'] ) )
        update_post_meta( $product_id, '_backorder_text', sanitize_text_field( $_POST['_backorder_text'] ) );
}

// Variations: Add a custom field in admin variation options inventory
add_action( 'woocommerce_variation_options_inventory', 'add_variation_settings_fields', 20, 3 );
function add_variation_settings_fields( $loop, $variation_data, $variation_post ) {

    woocommerce_wp_text_input( array(
        'id'            => '_backorder_text'.$loop,
        'name'          => '_backorder_text['.$loop.']',
        'value'         => get_post_meta( $variation_post->ID, '_backorder_text', true ),
        'type'          => 'text',
        'label'         => __( 'Backorders text', 'woocommerce' ),
        'description'   => __( 'Backorders text. Add a custom backorders text to be displayed when products are on backorders.', 'woocommerce' ),
        'desc_tip'      => true,
        'wrapper_class' => 'form-row form-row-first',
    ) );
}

// Variations: Save a custom field value from admin variation options inventory
add_action( 'woocommerce_save_product_variation', 'save_variation_settings_fields', 10, 2 );
function save_variation_settings_fields( $variation_id, $i ) {
    if( isset( $_POST['_backorder_text'][$i] ) )
        update_post_meta( $variation_id, '_backorder_text', sanitize_text_field( $_POST['_backorder_text'][$i] ) );
}

add_filter( 'woocommerce_get_availability', 'custom_on_backorder_text', 10, 2 );
function custom_on_backorder_text( $availability, $product ) {
    $backorder_text = get_post_meta( $product->get_id(), '_backorder_text', true );

    if( $availability['class'] === 'available-on-backorder' && ! empty( $backorder_text ) )
        $availability['availability'] = $backorder_text;

    return $availability;
}
?>