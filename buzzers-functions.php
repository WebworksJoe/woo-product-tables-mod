<?php
/*
Plugin Name: Buzzers custom functionality
Description: Custom functionality for Buzzers Academies.
Version: 1.0
Author: Webworks
Author URI: https://www.webworksuk.com
*/

/**
 * Functions for using user meta children repeater fields in shop and order
 *
 */
 
// Dynamically set the array of pairs keys/values for children checkboxes
function checkbox_names(){
    $current_user_id = get_current_user_id();
    $results = array();
    if( have_rows('children', 'user_' . $current_user_id) ):
        $i = 1;
        while( have_rows('children', 'user_' . $current_user_id) ): the_row();
        
        if( get_sub_field('first_name') )
        
        $fname = get_sub_field('first_name');
        $name = 'child_' . $i;
        
        $results[$name] = $fname;
        
        $i++;
        
        endwhile; 
        
        else:
            echo "No children registered on your account.<br><a href='/my-account/children-ep/'>Add children now</a>";
    endif;
    
    //$array = explode( '|', rtrim( $list, '|' ) );
    //echo $list;
    return $results;
}

// Displaying the checkboxes on single product page - Checkboxes for Woo Product Tables called in modified woo-product-tables-pro/includes/shortcode.php
add_action( 'woocommerce_before_add_to_cart_button', 'add_fields_before_add_to_cart' );
function add_fields_before_add_to_cart( ) {
    global $product;
    //if( $product->get_id() != 2 ) return; // Only for product ID "2"

    ?>
    <div class="children-check">
        <h3><?php _e("Children attending", "buzzers-functions"); ?></h3>
        <?php foreach( checkbox_names() as $key => $value ): ?>
            <label><input type="checkbox" name="<?php echo $key; ?>" id="<?php echo $key; ?>"><?php echo ' ' . $value; ?></label>
        <?php endforeach; ?>
    </div>
    <?php
}

//add_filter( 'the_content', 'checkbox_names');

// Echo number of children registered to user - Not currently used
function echo_count_of_users_children(){
    $current_user_id = get_current_user_id();
    if( have_rows('children', 'user_' . $current_user_id) ):
        $i = 0; 
        while( have_rows('children', 'user_' . $current_user_id) ): the_row(); if( get_sub_field('first_name') ) 
        $i++;
        endwhile; 
        $tCount = $i;
    endif;
    echo $tCount;
}

// Generate the checkboxes from children first_name subfield - Called in modified woo-product-tables-pro/includes/shortcode.php 
function populate_children_options() {
    global $product;
    $current_user_id = get_current_user_id();
    $html = '';
    $i = 1;
    if( have_rows('children', 'user_' . $current_user_id) ):
        foreach( checkbox_names() as $key => $value ):
                $html .= '<label><input type="checkbox" name="'. $key . '" id="' . $key . '"> ' . $value . '</label>';
            endforeach;
        else: 
            $html = "No children registered";
	endif;
	return $html;
}

// Add data to cart item
add_filter( 'woocommerce_add_cart_item_data', 'add_cart_item_data', 25, 2 );
function add_cart_item_data( $cart_item_data, $product_id ) {
    // use this to add a conditional exit if necessary
    // e.g. if( $product_id != 2 ) return $cart_item_data; // Only for product ID "2"

    // Set the data for the cart item in cart object
    $data = array() ;

    foreach( checkbox_names() as $key => $value ){
        if( isset( $_POST[$key] ) )
            $cart_item_data['custom_data'][$key] = $data[$key] = $value;
    }
    // Add the data to session and generate a unique ID
    if( count($data > 0 ) ){
        $cart_item_data['custom_data']['unique_key'] = md5( microtime().rand() );
        WC()->session->set( 'custom_data', $data );
    }
    return $cart_item_data;
}


// Display custom data on cart and checkout page.
add_filter( 'woocommerce_get_item_data', 'get_item_data' , 25, 2 );
function get_item_data ( $cart_data, $cart_item ) {
    // use this to add a conditional exit if necessary
    // e.g. if( $cart_item['product_id'] != 2 ) return $cart_data;

    if( ! empty( $cart_item['custom_data'] ) ){
        $values =  array();
        foreach( $cart_item['custom_data'] as $key => $value )
            if( $key != 'unique_key' ){
                $values[] = $value;
            }
        $values = implode( ', ', $values );
        $cart_data[] = array(
            'name'    => __( "<a href='/my-account/children-ep/' title='My Children'>Children attending</a>", "buzzers-functions"),
            'display' => $values
        );
    }

    return $cart_data;
}

// Add order item meta.
add_action( 'woocommerce_add_order_item_meta', 'add_order_item_meta' , 10, 3 );
function add_order_item_meta ( $item_id, $cart_item, $cart_item_key ) {
    if ( isset( $cart_item[ 'custom_data' ] ) ) {
        $values =  array();
        foreach( $cart_item[ 'custom_data' ] as $key => $value )
            if( $key != 'unique_key' ){
                $values[] = $value;
            }
        $values = implode( ', ', $values );
        wc_add_order_item_meta( $item_id, __( "Children attending", "buzzers-functions"), $values );
    }
}
