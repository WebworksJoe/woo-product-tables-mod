<?php

/**
  // This section will be add for Future, when want to add custom Shortcode
  $wpt_wpt_shortcode_text = get_option('wpt_wpt_shortcode_text');
  if($wpt_wpt_shortcode_text && !empty($wpt_wpt_shortcode_text)){
  add_shortcode($wpt_wpt_shortcode_text, 'wpt_shortcode_menu');
  }
 */
global $shortCodeText;
add_shortcode($shortCodeText, 'wpt_shortcode_generator');

/**
 * Shortcode Generator for WPT Plugin
 * https://codersaiful.net/woo-product-table-pro?add-to-cart=72&attribute_pa_size=l&attribute_pa_color=blue&quantity=1
 * 
 * @param array $atts
 * @return string
 * 
 * @since 1.0
 */
function wpt_shortcode_generator($atts = false) {
    //Getting WooProductTable Pro
    $config_value = get_option('wpt_configure_options');

    $html = '';

    /**
     * Set Variable $html to return
     * 
     * @since 1.1
     */
    
    $pairs = array('exclude' => false);
    extract( shortcode_atts( $pairs, $atts ) );

    
    $wpt_table_class = ( isset($atts['table_class']) && !empty($atts['table_class']) ? $atts['table_class'] : false );
    
    /**
     * Cart count Select, If any theme, doesn't work to display Cart count, use selection here
     * 
     * @since 1.9
     * @date 8.6.2018 d.m.y
     * @deprecated since version 3.7.10
     */
    //$cart_count_selector = ( isset($atts['cart_count_selector']) && !empty($atts['cart_count_selector']) ? $atts['cart_count_selector'] : false );
    $temp_number = ( isset($atts['temp_number']) && !empty($atts['temp_number']) ? $atts['temp_number'] : random_int(10, 300) ); 
    /**
     * @reference https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters
     */
    $wpt_product_short_order_by = ( isset($atts['sort_order_by']) && !empty($atts['sort_order_by']) ? $atts['sort_order_by'] : 'menu_order' );  //Removed date at V2.9 and Added menu_order as default
    $wpt_product_short = ( isset($atts['sort']) && !empty($atts['sort']) && in_array($atts['sort'], array('ASC', 'DESC')) ? $atts['sort'] : 'ASC' ); //Setting Default value ASC, Becuause added Randomize Option for Default at Bellow After Query
    //New Meta value of Custom Meta value of Order By @Since 3.4
    $meta_value_sort = ( isset($atts['meta_value_sort']) && !empty($atts['meta_value_sort']) ? $atts['meta_value_sort'] : '_sku' ); //Default Falue is _sku, I mean: if not set any value. than it will be '_sku'
    
    //New for Author: User id and username. added at 3.4
    $author = ( isset( $atts['author'] ) && !empty( $atts['author'] ) && is_numeric( $atts['author'] ) ? $atts['author'] : false ); 
    $author_name = ( isset( $atts['author_name'] ) && !empty( $atts['author_name'] ) ? $atts['author_name'] : false ); 
    
    $wpt_search_box = ( isset($atts['search_box']) && !empty($atts['search_box']) && $atts['search_box'] =='no' ? false : true ); //Set Search box Permission for now.
    
    $texonomiy_keywords_string = ( isset($atts['texonomiy_keywords']) && !empty($atts['texonomiy_keywords']) ? $atts['texonomiy_keywords'] : false );
    $texonomiy_keywords = wpt_explode_string_to_array($texonomiy_keywords_string, array('product_cat','product_tag')); //Set Default Taxonomy array
    
    $filter_keywords_string = ( isset($atts['filter']) && !empty($atts['filter']) ? $atts['filter'] : false );
    $filter_keywords = wpt_explode_string_to_array($filter_keywords_string, array('product_cat','product_tag') ); //Set Default Taxonomy array
    $wpt_filter_box = ( isset($atts['filter_box']) && !empty($atts['filter_box']) && $atts['filter_box'] =='no' ? false : true ); //Set Search box Permission for now.
    
    $product_cat_ids_string = ( isset($atts['product_cat_ids']) && !empty($atts['product_cat_ids']) ? $atts['product_cat_ids'] : false );
    $product_cat_ids = wpt_explode_string_to_array($product_cat_ids_string);
    
    
    $product_tag_ids_string = ( isset($atts['product_tag_ids']) && !empty($atts['product_tag_ids']) ? $atts['product_tag_ids'] : false );
    $product_tag_ids = wpt_explode_string_to_array($product_tag_ids_string);
    
    
    $cat_explude_string = ( isset($atts['cat_explude']) && !empty($atts['cat_explude']) ? $atts['cat_explude'] : false );
    $cat_explude = wpt_explode_string_to_array($cat_explude_string);
    
    $post_exclude_string = ( isset($atts['post_exclude']) && !empty($atts['post_exclude']) ? $atts['post_exclude'] : false );
    $post_exclude = wpt_explode_string_to_array($post_exclude_string);
    
    $only_stock = ( isset($atts['only_stock']) && !empty($atts['only_stock']) && $atts['only_stock'] == 'yes' ? true : false );
    /**
     * Minicart Feature for top or bottom
     * minicart_position is attribute name
     * @since 1.9
     */
    $minicart_position = ( isset($atts['minicart_position']) && !empty($atts['minicart_position']) ? $atts['minicart_position'] : 'top' );
    
    /**
     * New Classing system for Advance Mobile Responsive System
     * Added at 1.5
     * date: 12.5.2018 d/m/y
     */
    $mobile_responsive = ( isset($atts['mobile_responsive']) && $atts['mobile_responsive'] == 'no_responsive' ? false : 'mobile_responsive' );
    $product_cat_slugs_string = ( isset($atts['product_cat_slugs']) && !empty($atts['product_cat_slugs']) ? $atts['product_cat_slugs'] : false );
    $product_cat_slugs = wpt_explode_string_to_array($product_cat_slugs_string);


    $wpt_table_column_keywords_string = ( isset($atts['column_keyword']) && !empty($atts['column_keyword']) ? $atts['column_keyword'] : false );
    $wpt_table_column_keywords = wpt_explode_string_to_array($wpt_table_column_keywords_string);
    if( !$wpt_table_column_keywords ){
        $wpt_table_column_keywords = wpt_default_columns_keys_array();
        
    }
    
    $taxonomy_column_keywords = array_filter( $wpt_table_column_keywords,'taxonomy_column_generator' );
    $customfileds_column_keywords = array_filter( $wpt_table_column_keywords,'customfileds_column_generator' );
    //var_dump( $customfileds_column_keywords );
    //var_dump($taxonomy_column_keywords);
    $wpt_table_mobileHide_keywords_string = ( isset($atts['mobile_hide']) && !empty($atts['mobile_hide']) ? $atts['mobile_hide'] : false );
    $wpt_table_mobileHide_keywords = wpt_explode_string_to_array($wpt_table_mobileHide_keywords_string);
    
    //Added at 3.0.4 date: 17.8.2018 | d.m.y
    $wpt_ajax_action = ( isset($atts['ajax_action']) && $atts['ajax_action'] == 'no' ? 'no_ajax_action' : 'ajax_active' );
    
    $wpt_add_to_cart_text = ( isset($atts['add_to_cart_text']) && !empty($atts['add_to_cart_text']) ? $atts['add_to_cart_text'] : __( 'Add to cart', 'wpt_pro' ) );
    $wpt_added_to_cart_text = ( isset($atts['added_to_cart_text']) && !empty($atts['added_to_cart_text']) ? $atts['added_to_cart_text'] : __( 'Added', 'wpt_pro' ) );
    $wpt_adding_to_cart_text = ( isset($atts['adding_to_cart_text']) && !empty($atts['adding_to_cart_text']) ? $atts['adding_to_cart_text'] : __( 'Adding..', 'wpt_pro' ) );
    $wpt_add_to_cart_selected_text = ( isset($atts['add_to_cart_selected_text']) && !empty($atts['add_to_cart_selected_text']) ? $atts['add_to_cart_selected_text'] : __( 'Add to Cart [Selected]', 'wpt_pro' ) );
    $wpt_check_uncheck_all_text = ( isset($atts['check_uncheck_text']) && !empty($atts['check_uncheck_text']) ? $atts['check_uncheck_text'] : __( 'All Check/Uncheck','wpt_pro' ) );
    /**
     * Variations Added to V1.0.4
     * 
     * Again Deprecitated at V1.0.4 1/5/2018
     */
    //$wpt_variations_string = ( isset($atts['variations']) && !empty($atts['variations']) ? $atts['variations'] : false );

    //$wpt_variations = wpt_explode_string_to_array($wpt_variations_string);
    
    /**
     * Define permitted TD inside of Table, Not for Table head
     * Only for Table Body
     * Generate Array by wpt_define_permitted_td_array() which is available in functions.php file of Plugin
     * @since 1.0.4
     */
    $wpt_permitted_td = wpt_define_permitted_td_array( $wpt_table_column_keywords );

    $product_min_price = ( isset($atts['min_price']) && !empty($atts['min_price']) ? $atts['min_price'] : false );
    $product_max_price = ( isset($atts['max_price']) && !empty($atts['max_price']) ? $atts['max_price'] : false );

    //Table Column Title
    $wpt_table_column_title_string = ( isset($atts['column_title']) && !empty($atts['column_title']) ? $atts['column_title'] : false );
    $wpt_table_column_title = wpt_explode_string_to_array($wpt_table_column_title_string);
    
    if( !$wpt_table_column_title ){
        $wpt_table_column_title = wpt_default_columns_values_array();
    }
    /**
     * For Product Description Type
     * Here was Description lenght, Now here Description Type
     * @keyword description_type
     * @since 3.6
     */
    $wpt_description_type = ( isset($atts['description_type']) && !empty($atts['description_type']) && $atts['description_type'] != 'short_description' ? 'description'  : 'short_description' );
    /**
     * Table Template Selection Variable
     */
    $wpt_template = ( isset($atts['template']) && !empty($atts['template']) ? $atts['template'] : 'default' );
    
    /**
     * sET posts_per_page 
     * @since v 1.5
     */
    $posts_per_page = ( isset($atts['posts_per_page']) && !empty($atts['posts_per_page']) ? $atts['posts_per_page'] : 20 );

    /**
     * Args
     */
    $args = array(
        
        'posts_per_page' => $posts_per_page,//-1, //Permanent value -1 has removed from version 1.5
        'post_type' => array('product'), //, 'product_variation','product'
        'post_status'   =>  'publish', //Added at version 3.1 date: 6 sept, 2018
        /*
          'orderby'   => 'post_title',
          'order' => $wpt_product_short,
         */
        'meta_query' => array(
            /*
              array(
              'key' => '_price',
              'value' => 11,
              'compare' => '>',
              'type' => 'NUMERIC'
              ),

              array(
              'key' => '_price',
              'value' => 13,
              'compare' => '<',
              'type' => 'NUMERIC'
              ),
             
            array(//For Available product online
                'key' => '_stock_status',
                'value' => 'instock'
            )
            */
        ),
            /*
              'tax_query' => array(
              array(
              'taxonomy' => 'product_cat',
              'field' => 'id',
              'terms' => array(19),
              )
              ),
             */
    );
    
    /**
     * Issue of Query for Load More Button
     */
    if( isset( $_GET['s'] ) && !empty( $_GET['s'] ) ){
        $args['s'] = $_GET['s'];
    }else{
        unset( $args['s'] );
    }
    /*
    if( isset( $args['s'] ) && empty( $args['s'] ) ){
        unset( $args['s'] );
    }
    */
    //Final Sku Start
    if($meta_value_sort && $wpt_product_short_order_by == 'meta_value'){
        $args['meta_query'][] = array(
                'key'     => $meta_value_sort, //Default value is _sku : '_sku'
                'compare' => 'EXISTS',
            );
    }
    
    //Final Sku end
    //Author inof with Condition added 3.4
    if( $author ){
        $args['author'] = $author;
    }
    
    if( $author_name ){
        $args['author_name'] = $author_name;
    }
    
    //Author inof with Condition added 3.4  - End Here
    
    
    if($only_stock){
        $args['meta_query'][] = array(//For Available product online
                'key' => '_stock_status',
                'value' => 'instock'
            );
    }
    /**
     * Mordnanize Shorting Option
     * Actually Default Value  will be RANDOM, So If not set ASC or DESC, Than Sorting 
     * willbe Random by default. Althoug Just aftet WP_Query
     * 
     * @since 1.0.0 -9
     */
    if ($wpt_product_short) {
        $args['orderby'] = $wpt_product_short_order_by;//'post_title';
        $args['order'] = $wpt_product_short;
    }


    /**
     * Set Minimum Price for
     */
    if ($product_min_price) {
        $args['meta_query'][] = array(
            'key' => '_price',
            'value' => $product_min_price,
            'compare' => '>=',
            'type' => 'NUMERIC'
        );
    }

    /**
     * Set Maximum Price for
     */
    if ($product_max_price) {
        $args['meta_query'][] = array(
            'key' => '_price',
            'value' => $product_max_price,
            'compare' => '<=',
            'type' => 'NUMERIC'
        );
    }
    
    /**
     * Args Set for tax_query if available $product_cat_ids
     * 
     * @since 1.0
     */
    if ($product_cat_ids) {
        $args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field' => 'id',
                'terms' => $product_cat_ids,
                'operator' => 'IN'
            );

    }
    
    /**
     * Args Set for tax_query if available $product_tag_ids
     * 
     * @since 1.9
     */
    if ($product_tag_ids) {
        $args['tax_query'][] = array(
                'taxonomy' => 'product_tag',
                'field' => 'id',
                'terms' => $product_tag_ids,
                'operator' => 'IN'
            );

    }
    $args['tax_query']['relation'] = 'AND';
    //'operator' => 'IN'
    /**
     * Category Excluding System
     * 
     * @since 1.0.4
     * @date 27/04/2018
     */
    if($cat_explude){
        $args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field' => 'id',
                'terms' => $cat_explude,
                'operator' => 'NOT IN'
            );
    }
    
    /**
     * Post Exlucde
     * 
     * @since 1.0.4
     * @date 28/04/2018
     */
    if($post_exclude){
        $args['post__not_in'] = $post_exclude;
    }
    
    /**
     * Args Set for tax_query if available $product_cat_ids
     * 
     * @since 1.0
     */
    if ($product_cat_slugs) {
        $args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $product_cat_slugs,
            );
    }
    
    /**
     * Initialize Page Number
     */
    $args['paged'] =1;
    $html .= '<br class="wpt_clear">';
    /**
     * Add to cart Check Select /check/unchech Section
     * 
     * @version V1.0.4 
     * @date 2/5/2018
     */
    $html_check = $html_check_footer = false; $filter_identy_class = 'fullter_full';
    if( isset( $wpt_permitted_td['check'] ) ){
        $filter_identy_class = 'fulter_half';
        //
        $add_to_cart_selected_text = $wpt_add_to_cart_selected_text;//'Add to Cart [Selected]';
        
        $html_check .= "<div class='all_check_header_footer all_check_header check_header_{$temp_number}'>";
        $html_check_footer .= "<div class='all_check_header_footer all_check_footer check_footer_{$temp_number}'>";
        
        $html_check .= "<span><input data-type='universal_checkbox' data-temp_number='{$temp_number}' class='wpt_check_universal wpt_check_universal_header' id='wpt_check_uncheck_button_{$temp_number}' type='checkbox'><label for='wpt_check_uncheck_button_{$temp_number}'>{$wpt_check_uncheck_all_text}</lable></span>";
        
        $html_check .= "<a data-add_to_cart='{$wpt_add_to_cart_text}' data-temp_number='{$temp_number}' class='button add_to_cart_all_selected'>$add_to_cart_selected_text</a>";
        $html_check_footer .= "<a data-add_to_cart='{$wpt_add_to_cart_text}' data-temp_number='{$temp_number}' class='button add_to_cart_all_selected'>$add_to_cart_selected_text</a>";
        
        $html_check .= "</div>";
        $html_check_footer .= "</div>";
    }
    
    /**
     * Maintenace Filter
     * Mainly Mini Filter
     */
    $filter_html = false;
    if( $wpt_filter_box ){
        $filter_html .= "<div class='wpt_filter {$filter_identy_class}'>";
        $filter_html .= "<div class='wpt_filter_wrapper'>";
        $filter_html .= wpt_filter_box($temp_number, $filter_keywords);
        $filter_html .= "</div>";
        $filter_html .= "</div>"; //End of ./wpt_filter
    }
    /**
     * Tables Minicart Message div tag
     * By this feature, we able to display minicart at top or bottom of Table
     * 
     * @since 1.9
     */
    $table_minicart_message_box = "<div class='tables_cart_message_box tables_cart_message_box_{$temp_number}' data-type='load'></div>";
    
    //var_dump(the_widget( 'WC_Widget_Cart', 'title=' ));

    $html .= apply_filters('wpt_before_table_wrapper', ''); //Apply Filter Just Before Table Wrapper div tag

    $html .= "<div data-checkout_url='" . esc_attr( wc_get_checkout_url() ) . "' data-add_to_cart='" . esc_attr( $wpt_add_to_cart_text ) . "'  data-adding_to_cart='" . esc_attr( $wpt_adding_to_cart_text ) . "' data-added_to_cart='" . esc_attr( $wpt_added_to_cart_text ) . "' data-add_to_cart='{$wpt_add_to_cart_text}' data-site_url='" . site_url() . "' id='table_id_" . $temp_number . "' class='wpt_temporary_wrapper_" . $temp_number . " wpt_product_table_wrapper " . $wpt_template . "_wrapper woocommerce'>"; //Table Wrapper Div start here with class. //Added woocommerce class at wrapper div in V1.0.4
    //$html .= "<div data-add_to_cart='" . esc_attr( $wpt_add_to_cart_text ) . "'  data-adding_to_cart='" . esc_attr( $wpt_adding_to_cart_text ) . "' data-added_to_cart='" . esc_attr( $wpt_added_to_cart_text ) . "' data-out_of_stock_message='" . esc_attr( __( 'Out of Stock', 'wpt_pro' ) ) . "' data-select_all_items_message='" . esc_attr( __( 'Please select all items.', 'wpt_pro' ) ) . "' data-right_combination_message='" . esc_attr( __( 'Not available', 'wpt_pro' ) ) . "' data-add_to_cart='{$wpt_add_to_cart_text}' data-site_url='" . site_url() . "' id='table_id_" . $temp_number . "' class='wpt_temporary_wrapper_" . $temp_number . " wpt_product_table_wrapper " . $wpt_template . "_wrapper woocommerce'>"; //Table Wrapper Div start here with class. //Added woocommerce class at wrapper div in V1.0.4
    $html .= ($minicart_position == 'top' ? $table_minicart_message_box : false);//$minicart_position //"<div class='tables_cart_message_box_{$temp_number}'></div>";
    
    //Search Box Hander Here
    if( $wpt_search_box ){
        /**
         * Search Box Added here, Just before of Table 
         * 
         * @since 1.9
         * @date 9.6.2018 d.m.y
         */
        $html .= wpt_search_box( $temp_number, $texonomiy_keywords, $wpt_product_short_order_by, $wpt_product_short );
    }
    
    /**
     * Instant Sarch Box
     */
    $instance_search = false;
    if( $config_value['instant_search_filter'] == 1 ){
        $instance_search .= "<div class='instance_search_wrapper'>";
        $instance_search .= "<input data-temp_number='{$temp_number}' placeholder='{$config_value['instant_search_text']}' class='instance_search_input'>";
        //<input data-key="s" class="query_box_direct_value" id="single_keyword_57" value="" placeholder="Search keyword">
        $instance_search .= "</div>";
    }
    
    $html .= $instance_search; //For Instance Search Result
    $html .= $filter_html; //Its actually for Mini Filter Box
    $html .= $html_check; //Added at @Version 1.0.4
    $html .= '<br class="wpt_clear">'; //Added @Version 2.0
    $html .= apply_filters('wpt_before_table', ''); //Apply Filter Jese Before Table Tag
    
    
    
    /**
     * Why this array here, Actuall we will send this data as dataAttribute of Table's tag.
     * although function has called at bellow where this array need.
     */
    $table_row_generator_array = array(
        'args'                      => $args,
        'wpt_table_column_keywords' => $wpt_table_column_keywords,
        'wpt_product_short'         => $wpt_product_short,
        'wpt_permitted_td'          => $wpt_permitted_td,
        'wpt_add_to_cart_text'      => $wpt_add_to_cart_text,
        'temp_number'               => $temp_number,
        'texonomy_key'              => $taxonomy_column_keywords,
        'customfield_key'           => $customfileds_column_keywords,
        'filter_key'                => $filter_keywords,
        'filter_box'                => $wpt_filter_box,
        'description_type'        => $wpt_description_type,
        'ajax_action'               => $wpt_ajax_action,
    );
    //var_dump($table_row_generator_array);
    $html .= "<table data-page_number='2' data-config_json='" . esc_attr( wp_json_encode( $config_value ) ) . "' data-data_json='" . esc_attr( wp_json_encode( $table_row_generator_array ) ) . "' id='" . apply_filters('wpt_change_table_id', 'wpt_table') . "' class='{$mobile_responsive} wpt_temporary_table_" . $temp_number . " wpt_product_table " . $wpt_template . "_table $wpt_table_class " . $config_value['custom_add_to_cart'] . "'>"; //Table Tag start here.

    /**
     * this $responsive_table will use for responsive table css Selector.
     * I have used this table selector at the end of table
     * See at bellow inside of <<<EOF EOF;
     * 
     * @since 1.5
     */
    $responsive_table = "table#wpt_table.mobile_responsive.wpt_temporary_table_{$temp_number}.wpt_product_table";

    /**
     * Table Column Field Tilte Define here
     * 
     * @since 1.0.04
     */
    $column_title_html = $responsiveTableLabelData = false;
    if ($wpt_table_column_title && is_array($wpt_table_column_title) && count($wpt_table_column_title) >= 1) {
        $column_title_html .= '<thead><tr data-temp_number="' . $temp_number . '" class="wpt_table_header_row wpt_table_head">';
        foreach ( $wpt_table_column_title as $key=>$colunt_title ) {

            /**
             * this $responsiveTableLabelData will use for Responsives 
             */
            $responsiveTableLabelData .= $responsive_table . ' td:nth-of-type(' . ($key + 1) . '):before { content: "' . $colunt_title . '"; }';
            $column_class = ( isset( $wpt_table_column_keywords[$key] ) ? $wpt_table_column_keywords[$key] : '' );
            
            /**
             * Modified Table colum, Mainly for CheckBox Button's column.
             * Fron this 1.9 version, We will only show All check - checkbox here.
             * 
             * @since 1.9
             * @date: 10.6.2018 d.m.y
             */
            $colunt_title = ( $column_class != 'check' ? $colunt_title : "<input data-type='universal_checkbox' data-temp_number='{$temp_number}' class='wpt_check_universal' id='wpt_check_uncheck_column_{$temp_number}' type='checkbox'><label for=wpt_check_uncheck_column_{$temp_number}></label>" );
            
            $column_title_html .= "<th class='wpt_{$column_class}'>{$colunt_title}</th>";
            
        }
        $column_title_html .= '</tr></thead>';
    }
    $html .= $column_title_html;

    

    //var_dump($product_loop->posts); //Development Perpose.
    //var_dump($args); //Development Perpose.



    $html .= '<tbody>'; //Starting TBody here
    
    
    
    
    
    
    $html .= wpt_table_row_generator( $table_row_generator_array );
    
    
    
    
    
    
    $html .= '</tbody>'; //Tbody End here
    $html .= "</table>"; //Table tag end here.
    $Load_More_Text = $config_value['load_more_text'];
    $Load_More = '<div id="wpt_load_more_wrapper_' . $temp_number . '" class="wpt_load_more_wrapper ' . $config_value['disable_loading_more'] . '"><button data-temp_number="' . $temp_number . '" data-load_type="current_page" data-type="load_more" class="button wpt_load_more">' . $Load_More_Text . '</button></div>';
    $html .= ( $posts_per_page != -1 ? $Load_More : '' );//$Load_More;
    
    $html .= $html_check_footer;
    $html .= apply_filters('wpt_after_table', ''); //Apply Filter Just Before Table Wrapper div tag
    
    /**
     * Table Minicart for Footer.
     * Only will show, if select bottom minicart
     * 
     * @since 1.9
     */
    $html .= ($minicart_position == 'bottom' ? $table_minicart_message_box : false);
    
    $html .= "</div>"; //End of Table wrapper.
    $html .= apply_filters('wpt_after_table_wrapper', ''); //Apply Filter Just After Table Wrapper div tag
    
    
    /**
     * Extra content for Mobile Hide content Issue
     */
    $mobile_hide_css_code = false;
    if( $wpt_table_mobileHide_keywords && count( $wpt_table_mobileHide_keywords ) > 0 ){
        foreach( $wpt_table_mobileHide_keywords as $selector ){
            $mobile_hide_css_code .= "table#wpt_table.wpt_temporary_table_{$temp_number}.wpt_product_table th.wpt_" . $selector . ',';
            $mobile_hide_css_code .= "table#wpt_table.wpt_temporary_table_{$temp_number}.wpt_product_table td.wpt_" . $selector . ',';
        }
    }
    $mobile_hide_css_code .= '.hide_column_for_mobile_only_for_selected{ display: none!important;}';
    
    
    $table_css_n_js_array = array(
        'mobile_hide_css_code'      =>  $mobile_hide_css_code,
        'responsive_table'          =>  $responsive_table,
        'responsiveTableLabelData'  =>  $responsiveTableLabelData,
        'temp_number'               => $temp_number,
    );
    $html .= wpt_table_css_n_js_generator( $table_css_n_js_array );
    
    return $html;
}



/**
 * CSS and JS code generator, Its under Table
 * 
 * @param type $table_css_n_js_array
 * @return string CSS and CSS code for bellow of Table
 */
function wpt_table_css_n_js_generator( $table_css_n_js_array  ){
    
    $mobile_hide_css_code = $table_css_n_js_array['mobile_hide_css_code'];
    $responsive_table = $table_css_n_js_array['responsive_table'];
    $responsiveTableLabelData = $table_css_n_js_array['responsiveTableLabelData'];
    $temp_number = $table_css_n_js_array['temp_number'];
    $html = <<<EOF
<style>
@media 
only screen and (max-width: 760px) {
    $mobile_hide_css_code        
    /* Force table to not be like tables anymore */
    $responsive_table, 
    $responsive_table thead, 
    $responsive_table tbody, 
    $responsive_table th, 
    $responsive_table td, 
    $responsive_table tr { 
        display: block; 
    }

    /* Hide table headers (but not display: none;, for accessibility) */
    $responsive_table thead tr { 
        position: absolute;
        top: -9999px;
        left: -9999px;
    }

    $responsive_table tr { border: 1px solid #ddd;border-bottom: none; margin-bottom: 8px;}

    $responsive_table td { 
        border-bottom: 1px solid;
        position: relative;
        text-align: left !important;
        padding-left: 108px !important;
        height: 100%;
        border: none;
        border-bottom: 1px solid #ddd;    
    }
    $responsive_table td,$responsive_table td.wpt_check,$responsive_table td.wpt_quantity{
     width: 100%;       
    }
    $responsive_table td.wpt_quantity { 
       min-height: 57px;
    }
            
    $responsive_table td.wpt_thumbnails { 
       height: 100%;
       padding: 7px;
    }
            
    $responsive_table td.wpt_description { 
       min-height: 55px;
       height: 100%;
       padding: 7px;
    }
            
    $responsive_table td.wpt_action{ 
       height: 62px;
    }        
    $responsive_table td.data_product_variations.woocommerce-variation-add-to-cart.variations_button.woocommerce-variation-add-to-cart-disabled.wpt_action{ 
            height: 100%;
            padding: 7px 0;
    }
            
    $responsive_table td:before { 
        width: 88px;
        white-space: normal;
        background: #b7b7b736;
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        text-align: right;
        padding-right: 10px;
    }
    $responsiveTableLabelData
}            
</style>
<script>
    (function($) {
        $(document).ready(function() {
            $('body').on('change', '.wpt_temporary_table_{$temp_number} .wpt_quantity input.input-text.qty.text', function() {
                var target_Qty_Val = $(this).val();
                
                var target_product_id = $(this).closest('td.wpt_quantity').attr('data-target_id');
                var targetTotalSelector = $('.wpt_temporary_table_{$temp_number} .wpt_row_product_id_' + target_product_id + ' td.wpt_total.total_general');
                 
            
                var targetWeightSelector = $('.wpt_temporary_table_{$temp_number} .wpt_row_product_id_' + target_product_id + ' td.wpt_weight');
                var targetWeightAttr = $('.wpt_temporary_table_{$temp_number} .wpt_row_product_id_' + target_product_id + ' td.wpt_weight').attr('data-weight');
                var totalWeight =  parseFloat(targetWeightAttr) * parseFloat(target_Qty_Val);
                totalWeight = totalWeight.toFixed(2);
                if(totalWeight === 'NaN'){
                totalWeight = '';
                }
                targetWeightSelector.html(totalWeight);
                
                var targetTotalStrongSelector = $('.wpt_temporary_table_{$temp_number} .wpt_row_product_id_' + target_product_id + ' td.wpt_total.total_general strong');
                var targetPrice = targetTotalSelector.attr('data-price');
                var targetCurrency = targetTotalSelector.data('currency');
                var targetPriceDecimalSeparator = targetTotalSelector.data('price_decimal_separator');
                var targetPriceThousandlSeparator = targetTotalSelector.data('thousand_separator');
                var targetNumbersPoint = targetTotalSelector.data('number_of_decimal');
                var totalPrice = parseFloat(targetPrice) * parseFloat(target_Qty_Val);
                totalPrice = totalPrice.toFixed(targetNumbersPoint);
                
                $('.wpt_temporary_table_{$temp_number} .wpt_row_product_id_' + target_product_id + ' .wpt_action a.wpt_woo_add_cart_button').attr('data-quantity', target_Qty_Val);
                $('.yith_request_temp_{$temp_number}_id_' + target_product_id).attr('data-quantity', target_Qty_Val);
                targetTotalStrongSelector.html(targetCurrency + totalPrice.replace(".",targetPriceDecimalSeparator));
                //$(target_row_id + ' a.add_to_cart_button').attr('data-quantity', target_Qty_Val);
            });
            
        });
    })(jQuery);
</script>
EOF;
                return $html;
}

/**
 * Generate Table's Rot html based on Query args
 * 
 * @param type $args Query's args
 * @param type $wpt_table_column_keywords table's column
 * @param type $wpt_product_short Its actually for Product Sorting
 * @param type $wpt_permitted_td Permission or each td
 * @param type $wpt_add_to_cart_text add_to_cart text
 * @return String 
 */
function wpt_table_row_generator( $table_row_generator_array ){
    $html = false;
    //Getting WooProductTable Pro
    $config_value = get_option('wpt_configure_options');
    
    //var_dump($table_row_generator_array);
    $args                   = $table_row_generator_array['args'];
    $wpt_table_column_keywords = $table_row_generator_array['wpt_table_column_keywords'];
    $wpt_product_short      = $table_row_generator_array['wpt_product_short'];
    $wpt_permitted_td       = $table_row_generator_array['wpt_permitted_td'];
    $wpt_add_to_cart_text   = $table_row_generator_array['wpt_add_to_cart_text'];
    $temp_number            = $table_row_generator_array['temp_number'];
    $texonomy_key           = $table_row_generator_array['texonomy_key'];//texonomy_key
    $customfield_key        = $table_row_generator_array['customfield_key'];//texonomy_key
    $filter_key             = $table_row_generator_array['filter_key'];//texonomy_key
    $filter_box             = $table_row_generator_array['filter_box'];//Taxonomy Yes, or No
    $wpt_description_type = $table_row_generator_array['description_type'];
    $ajax_action            = $table_row_generator_array['ajax_action'];
    
    if( $args == false || $wpt_table_column_keywords == false ){
        return false;
    }
    //var_dump($args);
    $product_loop = new WP_Query($args);
    /**
     * If not set any Shorting (ASC/DESC) than Post loop will Random by Shuffle()
     * @since 1.0.0 -9
     */
    if ($wpt_product_short == 'random') {
        shuffle($product_loop->posts);
    }

    
    $wpt_table_row_serial = (( $args['paged'] - 1) * $args['posts_per_page']) + 1; //For giving class id for each Row as well
    if ($product_loop->have_posts()) : while ($product_loop->have_posts()): $product_loop->the_post();
            /**
             * Product Opject Define for get Important infomation for Each Product
             * 
             * @since 1.1
             */
            $wpt_product = wc_get_product( get_the_ID() );
            $data = $wpt_product->get_data();
            
            //var_dump($wpt_product);
            //var_dump($data);

            
            $product_type = $wpt_product->get_type();
            (Int) $id = $data['id']; //Added at Version 3.3
            /**
             * Min Quantity and Max Quantity
             * Min Max feature added at Version 3.3
             * These will be used in ['quantity'] and section as well as ['action'] section
             * @since 3.3
             */
            if(class_exists( 'WC_MMQ' ) ){
                $min_quantity = get_post_meta($id, '_wcmmq_min_quantity', true);
                $max_quantity = get_post_meta($id, '_wcmmq_max_quantity', true);
            }else{
                $min_quantity = false;
                $max_quantity = false;
            }
            
            
            
            $taxonomy_class = 'filter_row ';
            if( $filter_box && is_array( $filter_key ) && count( $filter_key ) > 0 ){
                foreach( $filter_key as $tax_keyword){
                    $terms = wp_get_post_terms($data['id'], $tax_keyword  );
                    
                    if( is_array( $terms ) && count( $terms ) > 0 ){
                        foreach( $terms as $term ){
                            $taxonomy_class .= $tax_keyword . '_' . $temp_number . '_' . $term->term_id . ' ';
                        }
                    
                    //var_dump( $terms );
                    }
                }
            }else{
               $taxonomy_class = 'no_filter'; 
            }
            
            /**
             * Table Row and
             * And Table Data filed here will display
             * Based on Query
             */
            $wpt_each_row = false;
            $html .= "<tr role='row' data-title='" . esc_attr( $data['name'] ) . "' data-product_id='" . $data['id'] . "' id='product_id_" . $data['id'] . "' class='visible_row wpt_row wpt_row_serial_$wpt_table_row_serial wpt_row_product_id_" . get_the_ID() . ' ' . $taxonomy_class . "'>";
            
            /**
             * Texonomy Handaler
             * 
             * @since 1.9 
             * @date: 10.6.2016 d.m.y
             */
            if(is_array( $texonomy_key ) && count( $texonomy_key ) > 0 ){
                foreach( $texonomy_key as $keyword ){
                   $generated_keyword = substr( $keyword, 4 );
                    $texonomy_content = '';
                    if(is_string( get_the_term_list($data['id'],$generated_keyword) ) ){
                        $texonomy_content = get_the_term_list($data['id'],$generated_keyword,'',', ');
                    }
                   $wpt_each_row[$keyword] = "<td class='wpt_{$keyword}'>" . $texonomy_content . "</td>";  
                }
            }
            //var_dump(get_post_meta($data['id'],'ffff'));
            /**
             * Texonomy Handaler
             * 
             * @since 1.9 
             * @date: 10.6.2016 d.m.y
             */
            if(is_array( $customfield_key ) && count( $customfield_key ) > 0 ){
                foreach( $customfield_key as $keyword ){
                   $generated_keyword = substr( $keyword, 3 );
                    $customfield_content = '';
                    $custom_meta = get_post_meta( $data['id'],$generated_keyword );
                    //var_dump($generated_keyword);
                    //var_dump($custom_meta);
                    if( is_array( $custom_meta ) && isset( $custom_meta[0] ) ){
                        $customfield_content = $custom_meta[0];
                    }
                   $wpt_each_row[$keyword] = "<td class='wpt_{$keyword}'>" . $customfield_content . "</td>";  
                }
            }
            
            
            //$wpt_each_row['tax_name'] = "<td class='wpt_tax_name'> tax_name </td>"; 
            
            /**
             * Define Serial Number for Each Row
             * 
             * @since 1.0
             */
            if ( isset( $wpt_permitted_td['serial_number'] ) ) {
                $wpt_each_row['serial_number'] = "<td class='wpt_serial_number'> $wpt_table_row_serial </td>";
            }
            
            $variable_class = $product_type.'_product';//$wpt_product->get_type();
            /*
             * This part also removed
            if( $wpt_product->get_type() == 'variable' ){
                $variable_class = 'variable_product';
            }
            */
            
            /**
             * Define Children options to add to booking
             * 
             * @Added by JL Webworks
             * @21/11/2018
             */
            $children_list_checkboxes = populate_children_options();
            $wpt_children = "<td class='wpt_children'>";
            $wpt_children .= $children_list_checkboxes;
            $wpt_children .= "</td>";
            $wpt_each_row['children'] = $wpt_children;
            
            
            /**
             * Define Custom Message to send Customer
             * 
             * @since 1.9
             * @date: 7/6/2018 d/m/y
             */
            if ( isset( $wpt_permitted_td['Message'] ) ) {
                $wpt_each_row['Message'] = "<td  class='wpt_Message'><input type='text' class='message message_{$temp_number}' id='message' placeholder='" . $config_value['type_your_message'] . "'></td>";
                //$wpt_each_row['Message'] = "<td  class='wpt_Message'><input type='text' class='message message_{$temp_number}' id='message' placeholder='" . __('Type your Message.') . "'></td>";
            }
                
                
            /**
             * Define Weight for Each Row
             * 
             * @since 1.0.4
             * @date: 5/5/2018
             */
            if ( isset( $wpt_permitted_td['weight'] ) ) {
                $wpt_each_row['weight'] = "<td data-weight_backup='" . $data['weight'] . "' data-weight='" . $data['weight'] . "' class='wpt_weight {$variable_class}'> " . $data['weight'] . " </td>";
            }
                
                
            /**
             * Define Length for Each Row
             * 
             * @since 1.0.4
             * @date: 5/5/2018
             */
            if ( isset( $wpt_permitted_td['length'] ) ) {
                $wpt_each_row['length'] = "<td data-length='" . $data['length'] . "' class='wpt_length {$variable_class}'> " . $data['length'] . " </td>";
            }
                
            /**
             * Define width for Each Row
             * 
             * @since 1.0.4
             * @date: 5/5/2018
             */
            if ( isset( $wpt_permitted_td['width'] ) ) {
                $wpt_each_row['width'] = "<td data-width='" . $data['width'] . "' class='wpt_width {$variable_class}'> " . $data['width'] . " </td>";
            }
                
            /**
             * Define height for Each Row
             * 
             * @since 1.0.4
             * @date: 5/5/2018
             */
            if ( isset( $wpt_permitted_td['height'] ) ) {
                $wpt_each_row['height'] = "<td data-height='" . $data['height'] . "' class='wpt_height {$variable_class}'> " . $data['height'] . " </td>";
            }
            
            /**
             * Added version 3.1
             * 
             * @since 3.1
             */
            if ( isset( $wpt_permitted_td['quick'] ) ) {
                $wpt_each_row['quick'] = '<td class="wpt_quick"><a href="#" class="button yith-wcqv-button" data-product_id="' . $data['id'] . '">' . $config_value['quick_view_btn_text'] . '</a></td>';
                //<a href="#" class="button yith-wcqv-button" data-product_id="' . $data['id'] . '">Quick</a>
            }
                
            /**
             * Define Stock Status for Each Product
             * 
             * @since 1.0.4
             * @date 28/04/2018
             */
            if ( isset( $wpt_permitted_td['stock'] ) ) {
                $stock_status_message = $stock_status_message = $config_value['table_out_of_stock'];
                if( $data['stock_status'] == 'instock' ){
                   $stock_status_message =  $data['stock_quantity'] . ' ' . $config_value['table_in_stock']; 
                }elseif( $data['stock_status'] == 'onbackorder' ){
                    $stock_status_message = $config_value['table_on_back_order'];//'On Back Order';
                }
                $wpt_each_row['stock'] = "<td class='wpt_stock'> <span class='{$data['stock_status']}'>" . $stock_status_message . " </span></td>";
                //$wpt_each_row['stock'] = "<td class='wpt_stock'> <span class='{$data['stock_status']}'>" . ( $data['stock_status'] != 'instock' ? 'Out of Stock' : $data['stock_quantity'] . ' ' . 'In Stock' ) . " </span></td>";
            }
            //var_dump($data);
                
                
            /**
             * Product Title Display with Condition
             *  valign="middle"
             */
            if ( isset( $wpt_permitted_td['thumbnails'] ) ) {
                $wpt_single_thumbnails = false;
                $wpt_single_thumbnails .= "<td valign='middle' class='wpt_thumbnails'>";
                //$config_value['thumbs_image_size'] Getting data from my plugins get_options()
                $wpt_single_thumbnails .= woocommerce_get_product_thumbnail(array( $config_value['thumbs_image_size'], $config_value['thumbs_image_size']));
                $wpt_single_thumbnails .= "</td>";
                $wpt_each_row['thumbnails'] = $wpt_single_thumbnails;
            }

            /**
             * Product Title Display with Condition
             */
            if ( isset( $wpt_permitted_td['product_title'] ) ) {
                $wpt_single_product_title = false;
                $wpt_single_product_title .= "<td class='wpt_product_title'>";
                if( $config_value['disable_product_link'] == '0' ){
                    //$config_value['product_link_target'] for $config value
                    $wpt_single_product_title .= "<a target='{$config_value['product_link_target']}' href='" . esc_url(get_the_permalink()) . "'>" . get_the_title() . "</a>";
                }else{
                    $wpt_single_product_title .= get_the_title();
                }
                $wpt_single_product_title .= "</td>";
                $wpt_each_row['product_title'] = $wpt_single_product_title;
            }

            /**
             * Product Description Display with Condition
             */
            if ( isset( $wpt_permitted_td['description'] ) ) {
                //Generated Description @Since Version: 3.4
                $desc = $data[$wpt_description_type];

                //$desc = substr(get_the_content(), 0, $wpt_description_type);
                $desc_attr = strip_tags($desc);
                $wpt_each_row['description'] = "<td class='wpt_description'  data-product_description='" . esc_attr( $desc_attr ) . "'><p>" .  $desc . "</p></td>";
            }

            /**
             * Product Category Display with Condition
             */
            if ( isset( $wpt_permitted_td['category'] ) ) {
                $wpt_single_category = false;
                /**
                 * $wpt_cotegory_col Define at before of TR (Table Row)
                 * $wpt_cotegory_col = wc_get_product_category_list(get_the_ID());
                 */
                $wpt_cotegory_col = wc_get_product_category_list( $data['id'] );
                $wpt_single_category .= "<td class='wpt_category'>";
                $wpt_single_category .= $wpt_cotegory_col;
                $wpt_single_category .= "</td>";

                $wpt_each_row['category'] = $wpt_single_category;
            }

            /**
             * Product Tags Display with Condition
             */
            if ( isset( $wpt_permitted_td['tags'] ) ) {
                $wpt_single_tags = false;
                $wpt_tag_col = wc_get_product_tag_list( $data['id'] );
                $wpt_single_tags .= "<td class='wpt_tags'>";
                $wpt_single_tags .= $wpt_tag_col;
                $wpt_single_tags .= "</td>";
                $wpt_each_row['tags'] = $wpt_single_tags;
            }


            /**
             * Product SKU Dispaly
             */
            if ( isset( $wpt_permitted_td['sku'] ) ) {
                $wpt_each_row['sku'] = "<td data-sku='" . $wpt_product->get_sku() . "' class='wpt_sku'><p>" . $wpt_product->get_sku() . "</p></td>";
            }


            /**
             * Product Rating Dispaly
             */
            if ( isset( $wpt_permitted_td['rating'] ) ) {
            //Add here @version 1.0.4
            $wpt_average = $data['average_rating'];
            $wpt_product_rating = '<div class="star-rating" title="' . sprintf(__('Rated %s out of 5', 'woocommerce'), $wpt_average) . '"><span style="width:' . ( ( $wpt_average / 5 ) * 100 ) . '%"><strong itemprop="ratingValue" class="rating">' . $wpt_average . '</strong> ' . __('out of 5', 'woocommerce') . '</span></div>';


                $wpt_each_row['rating'] = "<td class='wpt_rating woocommerce'><p>" . $wpt_product_rating . "</p></td>";
            }

            /**
             * Display Price
             */
            if ( isset( $wpt_permitted_td['price'] ) ) {
                $wpt_single_price = false;
                $wpt_single_price .= "<td class='wpt_price'  id='price_value_id_" . $data['id'] . "' data-price_html='" . esc_attr( $wpt_product->get_price_html() ) . "'> ";
                $wpt_single_price .= '<span class="wpt_product_price">';
                $wpt_single_price .= $wpt_product->get_price_html(); //Here was woocommerce_template_loop_price() at version 1.0
                $wpt_single_price .= '</span>';
                $wpt_single_price .= " </td>";

                $wpt_each_row['price'] = $wpt_single_price;
            }
            
            /**
             * Default Quantity is not change able from Configure page. 
             * It will use at $wpt_permitted_td['quantity'], $wpt_permitted_td['quoterequest'],$wpt_permitted_td['total'],$wpt_permitted_td['action']
             * 
             * @since 2.9
             */
            $default_quantity = ( !empty( $min_quantity ) && $min_quantity > 0 ? $min_quantity : $config_value['default_quantity'] );//$config_value['default_quantity'];
            $enable_class_based_quantity = ($default_quantity < 1 ? 'disabled' : 'enabled');
            
            /**
             * Display Quantity for WooCommerce Product Loop
             * $current_config_value['default_quantity']
             */
            if ( isset( $wpt_permitted_td['quantity'] ) ) {
                $wpt_single_quantity = false;
                $wpt_single_quantity .= "<td class='wpt_quantity' data-target_id='" . $data['id'] . "'> ";
                $wpt_single_quantity .= woocommerce_quantity_input( array( 
                                                        'input_value' => $default_quantity,
                                                        'min_value' => $min_quantity,
                                                        'max_value' => $max_quantity,
                                                                ) , false, false ); //Here was only woocommerce_quantity_input() at version 1.0
                $wpt_single_quantity .= " </td>";
                $wpt_each_row['quantity'] = $wpt_single_quantity; 
            }

            /**
             * Display Quantity for WooCommerce Product Loop
             */
            if ( isset( $wpt_permitted_td['check'] ) ) {
                $wpt_single_check = false;
                $wpt_single_check .= "<td class='wpt_check' data-target_id='" . $data['id'] . "'> ";
                $wpt_single_check .= "<input data-product_type='" . $wpt_product->get_type() . "' id='check_id_{$temp_number}_" . $data['id'] . "' data-temp_number='{$temp_number}' data-product_id='" . $data['id'] . "' class='" . ( $product_type == 'grouped' || $product_type == 'variable' || $product_type == 'external' || ( $data['stock_status'] != 'instock' && $data['stock_status'] != 'onbackorder' ) ? 'disabled' : 'enabled' ) . " wpt_tabel_checkbox wpt_td_checkbox wpt_check_temp_{$temp_number}_pr_" . $data['id'] . " wpt_check_{$temp_number} wpt_inside_check_{$temp_number}' type='checkbox' value='0'><label for='check_id_{$temp_number}_" . $data['id'] . "'></label>";
                $wpt_single_check .= " </td>";
                $wpt_each_row['check'] = $wpt_single_check;
            } //check   
                
            /**
             * For Variable Product
             * 
             */
            $row_class = $data_product_variations = $variation_html = $variable_for_total = false;
            $quote_class = 'enabled';
            //var_dump($wpt_product->get_type()); //grouped
            if( $wpt_product->get_type() == 'variable' ){
                /**
                 * $variable_for_total variable will use in Total colum. So we need just True false information
                 */
                $variable_for_total = true;
                $row_class = 'data_product_variations woocommerce-variation-add-to-cart variations_button woocommerce-variation-add-to-cart-disabled';
                $quote_class = 'variations_button disabled';
                $variable = new WC_Product_Variable($data['id']);
                
                $available_variations = $variable->get_available_variations();
                $data_product_variations = htmlspecialchars( wp_json_encode( $available_variations ) );
                
                
                $attributes = $variable->get_variation_attributes();
                //var_dump($attributes);
                $variation_html = wpt_variations_attribute_to_select( $attributes, $data['id'], $temp_number );                 
            }

            
            
            /**
             * It should Place here, Because here will be use $variable_for_total
             * Define Total for Each Product
             * 
             * @since 1.5
             * @date 12/05/2018 d/m/y
             */
            
            if ( isset( $wpt_permitted_td['total'] ) ) {
                $price_decimal_separator = wc_get_price_decimal_separator(); //For Decimal Deparator
                $thousand_separator = wc_get_price_thousand_separator();
                $number_of_decimal = wc_get_price_decimals();
                $wpt_display_total = $data['price'] * $default_quantity;
                $wpt_each_row['total'] = "<td data-number_of_decimal='" . esc_attr( $number_of_decimal ) . "' data-thousand_separator='" . esc_attr( $thousand_separator ) . "' data-price_decimal_separator='" . esc_attr( $price_decimal_separator ) . "' data-price='" . $data['price'] . "' data-currency='" . esc_attr( get_woocommerce_currency_symbol() ) . "' class='wpt_total " . ( $variable_for_total || !$data['price'] ? 'total_variaion' : 'total_general' ) . "'><strong>" . ( !$variable_for_total ? get_woocommerce_currency_symbol() . number_format( $wpt_display_total, $number_of_decimal, $price_decimal_separator, $thousand_separator ) : false ) . "</strong></td>"; // && $data['price']
            }


            
            
            //Out_of_stock class Variable
            $stock_status = $data['stock_status'];
            $stock_status_class = ( $stock_status == 'onbackorder' || $stock_status == 'instock' ? 'add_to_cart_button' : $stock_status . '_add_to_cart_button disabled' );

            
            
            /**
             * For WishList
             * @since 2.6
             */
            if ( isset( $wpt_permitted_td['wishlist'] ) ) {
                $wpt_wishlist = false;
                $wpt_wishlist .= "<td class='wpt_wishlist'  data-product_id='" . $data['id'] . "'> ";
                $wpt_wishlist .= do_shortcode('[yith_wcwl_add_to_wishlist product_id='. $data['id'] .' icon="'. (get_option('yith_wcwl_add_to_wishlist_icon') != '' && get_option('yith_wcwl_use_button') == 'yes' ? get_option('yith_wcwl_add_to_wishlist_icon') : 'fa-heart') .'"]');
        
                $wpt_wishlist .= "</td>";
                $wpt_each_row['wishlist'] = $wpt_wishlist;
            }    
            
            
                
            /**
             * For Quote Request
             * @since 2.6
             */
            if ( isset( $wpt_permitted_td['quoterequest'] ) ) {
                $wpt_nonce = wp_create_nonce( 'add-request-quote-' . $data['id'] );
                //var_dump($wpt_nonce);
                
                $wpt_quoterequest = false;
                $wpt_quoterequest .= "<td class='wpt_quoterequest'  data-product_id='" . $data['id'] . "'> ";
                $Add_to_Quote = $config_value['yith_add_to_quote_text'];//'Add to Quote';
                $data_message = '{"text":"'. $Add_to_Quote .'","adding":"' . $config_value['yith_add_to_quote_adding'] . '","added":"' . $config_value['yith_add_to_quote_added'] . '"}';
                $wpt_quoterequest .= "<a data-yith_browse_list='{$config_value['yith_browse_list']}' data-yith_product_type='{$config_value['yith_product_type']}' data-response_msg='' data-msg='{$data_message}' data-wp_nonce='{$wpt_nonce}' data-quote_data='' data-variation='' data-variation_id='' data-product_id='{$data['id']}' class='{$quote_class} yith_request_temp_{$temp_number}_id_{$data['id']} yith_add_to_quote_request button' href='#' data-quantity='{$default_quantity}' data-selector='yith_request_temp_{$temp_number}_id_{$data['id']}'>{$Add_to_Quote}</a>";
                //data-variation="{&quot;attribute_pa_color&quot;:&quot;red&quot;,&quot;attribute_logo&quot;:&quot;No&quot;}" data-variation_id="26"
    
                //$wpt_quoterequest .= do_action('woocommerce_after_add_to_cart_button', $data['id'], $product);;
                $wpt_quoterequest .= "</td>";

                $wpt_each_row['quoterequest'] = $wpt_quoterequest;
            }   
            
            /**
             * To display Product's Publish Date
             * 
             * @since 3.7
             * @date 10.11.2018 d.m.y
             */
            if ( isset( $wpt_permitted_td['date'] ) ) {
                $wpt_date = false;
                $wpt_date .= "<td class='wpt_date'> ";
                $wpt_date .= get_the_date();
        
                $wpt_date .= "</td>";
                $wpt_each_row['date'] = $wpt_date;
            }  
             
            /**
             * To display Product's Publish Modified Date
             * 
             * @since 3.7
             * @date 10.11.2018 d.m.y
             */
            if ( isset( $wpt_permitted_td['modified_date'] ) ) {
                //$date = $data['date_created'];
            $date_modified = $data['date_modified'];
            //var_dump($date_modified);

            //var_dump($date->date( get_option( 'date_format' ) ) );


                $wpt_modified_date = false;
                $wpt_modified_date .= "<td class='wpt_modified_date'> ";
                $wpt_modified_date .= $date_modified->date( get_option( 'date_format' ) );
        
                $wpt_modified_date .= "</td>";
                $wpt_each_row['modified_date'] = $wpt_modified_date;
            }  
             
            /**
             * Display Add-To-Cart Button
             */
            if ( isset( $wpt_permitted_td['action'] ) ) {
                $wpt_single_action = false;
                $wpt_single_action .= "<td data-temp_number='{$temp_number}' class='{$row_class} wpt_action wpt_action_" . $data['id'] . "' data-quantity='1' data-product_id='" . $data['id'] . "' data-product_variations = '" . esc_attr( $data_product_variations ) . "'> ";
                $wpt_single_action .= $variation_html;
                //$wpt_single_action .= '<span class="wpt_product_price">';
                /*
                $wpt_single_action .= sprintf('<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>', 
                        esc_url($wpt_product->add_to_cart_url()), 
                        esc_attr(1), esc_attr($wpt_product->get_id()), 
                        esc_attr($wpt_product->get_sku()), 
                        esc_attr('button product_type_simple add_to_cart_button ajax_add_to_cart'), 
                        esc_html($wpt_product->add_to_cart_text())
                );
                */
                $ajax_action_final = ( $product_type == 'grouped' || $product_type == 'external' ? 'no_ajax_action ' : $ajax_action . ' ' );//$ajax_action;
               //var_dump($wpt_product->get_type());//external
                if( $product_type == 'grouped' || $product_type == 'external' ){
                    $add_to_cart_url = $wpt_product->add_to_cart_url();
                    
                }else{
                    $add_to_cart_url = ( $ajax_action == 'no_ajax_action' ? get_the_permalink() : '?add-to-cart=' .  $data['id'] );// '?add-to-cart=' .  $data['id'];
                    
                }
                //$add_to_cart_url = ( $product_type != 'external' ? '?add-to-cart=' .  $data['id'] : $wpt_product->add_to_cart_url() );//'?add-to-cart=' .  $data['id']; //home_url() . 
                $wpt_add_to_cart_text_final = ( $product_type == 'grouped' || $product_type == 'external' || $wpt_add_to_cart_text == ' ' ? $wpt_product->add_to_cart_text() : $wpt_add_to_cart_text );//'?add-to-cart=' .  $data['id']; //home_url() . 
                $wpt_single_action .= apply_filters('woocommerce_loop_add_to_cart_link', 
                        sprintf('<a rel="nofollow" data-add_to_cart_url="%s" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>', 
                                esc_attr( $add_to_cart_url ),
                                //'http://localhost/practice-wp/product-table/?add-to-cart=' . $data['id'] . '&attribute_borno=ETC&quantity=10', 
                                esc_url( $add_to_cart_url ), 
                                //esc_url( $wpt_product->add_to_cart_url() ), 
                                esc_attr( $default_quantity ), //1 here was 1 before 2.8
                                esc_attr($wpt_product->get_id()), 
                                esc_attr($wpt_product->get_sku()), 
                                esc_attr( $ajax_action_final . ( $row_class ? 'wpt_variation_product single_add_to_cart_button button alt disabled wc-variation-selection-needed wpt_woo_add_cart_button' : 'button wpt_woo_add_cart_button ' . $stock_status_class ) ), //ajax_add_to_cart  //|| !$data['price']
                                esc_html( $wpt_add_to_cart_text_final )
                                //esc_html($wpt_product->add_to_cart_text())
                        ), $wpt_product);


                //woocommerce_template_loop_add_to_cart();
                //$wpt_single_action .= '</span>';
                $wpt_single_action .= " </td>";

                $wpt_each_row['action'] = $wpt_single_action;
            }
            
            
            //var_dump($wpt_table_column_keywords);
            //var_dump($wpt_each_row);
            /**
            if(is_array($wpt_table_column_keywords) && count($wpt_table_column_keywords) > 0){
                $final_row_data = false;
                foreach($wpt_table_column_keywords as $each_keyword){
                    $final_row_data .= ( isset($wpt_each_row[$each_keyword]) ? $wpt_each_row[$each_keyword] : false );
                }
            }
             */
            
            $html .= wpt_generate_each_row_data($wpt_table_column_keywords, $wpt_each_row);
            $html .= "</tr>"; //End of Table row

            $wpt_table_row_serial++; //Increasing Serial Number.

        endwhile;
        wp_reset_query();
    else:
        $html .= apply_filters('wpt_product_not_found', 'Product Not found');
    endif;
    
    return $html;
}

/**
 * Texonomy select box for Texonomy.
 * 
 * @param type $texonomy_keyword
 * @param type $temp_number
 * @param type $current_select_texonomies
 * @return string|boolean
 */
function wpt_texonomy_search_generator( $texonomy_keyword, $temp_number){
    //Added at 3.1 date: 10.9.2018
    $config_value = get_option('wpt_configure_options');
    $html = false;
    if( !$texonomy_keyword || is_array( $texonomy_keyword )){
        return false;
    }
    
    /**
     * Need for get_texonomy and get_terms
     */
    $texonomy_sarch_args = array('hide_empty' => true,'orderby' => 'count','order' => 'DESC');
    
        $taxonomy_details = get_taxonomy( $texonomy_keyword );
        if( !$taxonomy_details ){
            return false;
        }
        $label = $taxonomy_details->label;
        $label_all_items = $taxonomy_details->labels->all_items;
        $html .= "<div class='search_single search_single_texonomy search_single_{$texonomy_keyword}'>";
        $html .= "<label class='search_keyword_label {$texonomy_keyword}' for='{$texonomy_keyword}_{$temp_number}'>" . __( 'Choose', 'wpt_pro' ) . " {$label}</label>";
        $html .= "<p data-key='{$texonomy_keyword}' class='search_select query search_select_{$texonomy_keyword}' id='{$texonomy_keyword}_{$temp_number}' multiple>";
            //$html .= "<option value=''>{$label_all_items}</option>";
            $texonomy_boj = get_terms( $texonomy_keyword, $texonomy_sarch_args );
            if( count( $texonomy_boj ) > 0 ){
                
                //Search box's Filter Sorting Added at Version 3.1
                $customized_texonomy_boj = false;
                foreach( $texonomy_boj as $item ){
                    //
                    $name = $item->name;
                    $customized_texonomy_boj[$name] = $item;
                    
                }
                $customized_texonomy_boj = wpt_sorting_array( $customized_texonomy_boj, $config_value['sort_searchbox_filter'] );
                
                foreach( $customized_texonomy_boj as $item ){
                    //$html .= "<label class='texonomy_label taxonomy_label_{$texonomy_keyword}'><input value='{$item->term_id}' name='texonomy_{$texonomy_keyword}' type='checkbox'>  {$item->name} ({$item->count})</label>";
                    //$html .= "<label class='texonomy_label taxonomy_label_{$texonomy_keyword}'><input value='{$item->term_id}' name='texonomy_{$texonomy_keyword}' type='checkbox'>  {$item->name} ({$item->count})</label>";
                    $html .= "<span class='texonomy_checkbox_single'>";
                    $html .= "<input value='{$item->term_id}' id='{$texonomy_keyword}_{$temp_number}_{$item->term_id}'  class='texonomy_check_box texonomy_{$texonomy_keyword}' name='texonomy_{$texonomy_keyword}' type='checkbox'>";
                    $html .= "<label class='texonomy_label taxonomy_label_{$texonomy_keyword}' for='{$texonomy_keyword}_{$temp_number}_{$item->term_id}'>{$item->name} <strong>({$item->count})</strong></label>";
                    $html .= "</span>";

                    //$html .= "<option value='{$item->term_id}' " . ( is_array($current_select_texonomies) && in_array($item->term_id, $current_select_texonomies) ? 'selected' : false ) . ">{$item->name} ({$item->count}) </option>";
                }
            }
        $html .= "</p>";
        $html .= "</div>"; //End of .search_single
    
    
    return $html;
}

/**
 * Sorting Associative array based on ASC,DESC or None.
 * 
 * @param type $array Associative Array
 * @param type $sorting_type Available type ASC,DESC,None
 * @return Array
 */
function wpt_sorting_array( $array, $sorting_type ){
    if( $sorting_type == 'ASC' ){
        ksort( $array );
    }else if( $sorting_type == 'DESC' ){
        krsort( $array );
    }
    
    return $array;
}

/**
 * Texonomy select for Filter -- Texonomy.
 * 
 * @param type $texonomy_keyword
 * @param type $temp_number
 * @param type $current_select_texonomies
 * @return string|boolean
 */
function wpt_texonomy_filter_generator( $texonomy_keyword, $temp_number ){
    //Getting data from options
    $config_value = get_option('wpt_configure_options');
    
    $html = false;
    if( !$texonomy_keyword || is_array( $texonomy_keyword )){
        return false;
    }
    
    /**
     * Need for get_texonomy and get_terms
     */
    $texonomy_sarch_args = array('hide_empty' => true,'orderby' => 'count','order' => 'DESC');
    
        $taxonomy_details = get_taxonomy( $texonomy_keyword );
        if( !$taxonomy_details ){
            return false;
        }
        
        $label = $taxonomy_details->labels->singular_name;
        //$label = $taxonomy_details->label;
        //$label_all_items = $taxonomy_details->labels->all_items;
        //$html .= "<div class='search_single search_single_texonomy search_single_{$texonomy_keyword}'>";
        //$html .= "<label class='search_keyword_label {$texonomy_keyword}' for='{$texonomy_keyword}_{$temp_number}'>" . __( 'Choose', 'wpt_pro' ) . " {$label}</label>";
        $html .= "<select data-temp_number='{$temp_number}' data-key='{$texonomy_keyword}' class='filter_select select2 filter filter_select_{$texonomy_keyword}' id='{$texonomy_keyword}_{$temp_number}'>";
            $html .= "<option style='color:#aaa !important;' value=''>{$label}</option>";
            $texonomy_boj = get_terms( $texonomy_keyword, $texonomy_sarch_args );
            //var_dump($texonomy_boj);
            if( count( $texonomy_boj ) > 0 ){
                //var_dump($texonomy_boj);
                $customized_texonomy_boj = false;
                foreach( $texonomy_boj as $item ){
                    //
                    $name = $item->name;
                    $customized_texonomy_boj[$name] = $item;
                    
                }
                $customized_texonomy_boj = wpt_sorting_array( $customized_texonomy_boj, $config_value['sort_mini_filter'] );
                foreach( $customized_texonomy_boj as $item ){  
                    $html .= "<option value='{$texonomy_keyword}_{$temp_number}_{$item->term_id}'>{$item->name}</option>";
                    //$html .= "<option value='{$item->term_id}' " . ( is_array($current_select_texonomies) && in_array($item->term_id, $current_select_texonomies) ? 'selected' : false ) . ">{$item->name} ({$item->count}) </option>";
                }
            }
        $html .= "</select>";
        //$html .= "</div>"; //End of .search_single
    
    
    return $html;
}

/**
 * Total Search box Generator
 * 
 * @param type $temp_number It's a Temporay Number for each Table,
 * @param type $search_box_texonomiy_keyword Obviously should be a Array, for product_cat tag etc
 * @return string
 */
function wpt_search_box($temp_number, $search_box_texonomiy_keyword = array( 'product_cat', 'product_tag' ), $order_by = false, $order = false ){
    $config_value = get_option('wpt_configure_options');
    $html = false;
    $html .= "<div id='search_box_{$temp_number}' class='wpt_search_box search_box_{$temp_number}'>";
    $html .= '<div class="search_box_fixer">'; //Search_box inside fixer
    $html .= '<h3 class="search_box_label">' . $config_value['search_box_title'] . '</h3>';
    //$html .= '<h3 class="search_box_label">' . __( 'Search Box (<small>All Fields Optional</small>)', 'wpt_pro' ) . '</h3>'; //Changed at Version 3.3
    
    $html .= "<div class='search_box_wrapper'>";
    
    /**
     * Search Input Box
     * At Version 3.3, we have changed few features
     */
    
    $html .= "<div class='search_single search_single_direct'>";
        
        $single_keyword = $config_value['search_box_searchkeyword'];//__( 'Search keyword', 'wpt_pro' );
        $html .= "<div class='search_single_column'>";
        $html .= '<label class="search_keyword_label single_keyword" for="single_keyword_' . $temp_number . '">' . $single_keyword . '</label>';
        $html .= '<input data-key="s" class="query_box_direct_value" id="single_keyword_' . $temp_number . '" value="" placeholder="' . $single_keyword . '"/>';
        $html .= "</div>";// End of .search_single_column
        
        $single_keyword = $config_value['search_box_orderby'];//__( 'Order By', 'wpt_pro' ); //search_box_orderby
        $html .= "<div class='search_single_column search_single_sort search_single_order_by'>";
        $html .= '<label class="search_keyword_label single_keyword" for="order_by' . $temp_number . '">' . $single_keyword . '</label>';
        
        $html .= '<select data-key="orderby" id="order_by_' . $temp_number . '" class="query_box_direct_value select2" >';
        $html .= '<option value="name" '. wpt_check_sortOrder( $order_by, 'name' ) .'>Name</option>';
        $html .= '<option value="menu_order" '. wpt_check_sortOrder( $order_by, 'menu_order' ) .'>Menu Order</option>';
        $html .= '<option value="type" '. wpt_check_sortOrder( $order_by, 'type' ) .'>Type</option>';
        $html .= '<option value="comment_count" '. wpt_check_sortOrder( $order_by, 'comment_count' ) .'>Reviews</option>';
        $html .= '</select>';
        /*
        $html .= <<<EOF

        <option value="name" selected>Name(Default)</option>
        <option value="menu_order">Menu Order</option>
        <option value="date">Date</option>
        <option value="type">Type</option>
        <option value="comment_count">Reviews</option>

    </select>    
EOF;
*/
        $html .= "</div>";// End of .search_single_column

        $single_keyword = $config_value['search_box_order']; //__( 'Order', 'wpt_pro' );
        $html .= "<div class='search_single_column search_single_order'>";
        $html .= '<label class="search_keyword_label single_keyword" for="order_' . $temp_number . '">' . $single_keyword . '</label>';
        $html .= '<select data-key="order" id="order_' . $temp_number . '" class="query_box_direct_value select2" >  ';
        $html .= '<option value="ASC" '. wpt_check_sortOrder( $order, 'ASC' ) .'>ASCENDING</option>';
        $html .= '<option value="DESC" '. wpt_check_sortOrder( $order, 'DESC' ) .'>DESCENDING</option>';
        $html .= '<option value="random" '. wpt_check_sortOrder( $order, 'random' ) .'>Random</option>';
        $html .= '</select>';
        /*
$html .= <<<EOF
<select data-key="order" id="order_{$temp_number}" class="query_box_direct_value select2" >  
    <option value="ASC" selected>ASCENDING (Default)</option>
    <option value="DESC">DESCENDING</option>
    <option value="random">Random</option>
</select>  
EOF;
*/
        $html .= "</div>";// End of .search_single_column
        
        
        
    $html .= "</div>"; //end of .search_single
    
    /**
     * Texonomies Handle based on $search_box_texonomiy_keyword
     * Default cat and tag for product
     * 
     * @since 1.9
     * @date 10.6.2018 d.m.y
     */
    if( is_array( $search_box_texonomiy_keyword ) && count( $search_box_texonomiy_keyword ) > 0 ){
        foreach( $search_box_texonomiy_keyword as $texonomy_name ){
           $html .= wpt_texonomy_search_generator( $texonomy_name,$temp_number ); 
        }
    }
    /* This section has removed on update
    // $current_select_texonomy : used as $product_cat_ids
    $html .= wpt_texonomy_search_generator( 'product_cat',$temp_number );
    $html .= wpt_texonomy_search_generator( 'product_tag',$temp_number );
    $html .= wpt_texonomy_search_generator( 'size',$temp_number );
    */
    
    $html .= '</div>'; //End of .search_box_singles
    
    $html .= '<button data-type="query" data-temp_number="' . $temp_number . '" id="wpt_query_search_button_' . $temp_number . '" class="button wpt_search_button query_button wpt_query_search_button wpt_query_search_button_' . $temp_number . '">' . $config_value['search_button_text'] . '</button>';
    $html .= '</div>';//End of .search_box_fixer
    $html .= '</div>';//End of .wpt_search_box
    return $html;
}
/**
 * Total Search box Generator
 * 
 * @param type $temp_number It's a Temporay Number for each Table,
 * @param type $search_box_texonomiy_keyword Obviously should be a Array, for product_cat tag etc
 * @return string
 */
function wpt_filter_box($temp_number, $filter_keywords = false ){
    $html = $html_select = false;
    $config_value = get_option('wpt_configure_options');
    
    /**
     * Texonomies Handle based on $search_box_texonomiy_keyword
     * Default cat and tag for product
     * 
     * @since 20
     * @date 11.6.2018 d.m.y
     */
    if( is_array( $filter_keywords ) && count( $filter_keywords ) > 0 ){
        foreach( $filter_keywords as $texonomy_name ){
           $html_select .= wpt_texonomy_filter_generator( $texonomy_name,$temp_number ); 
        }
    }
    if( $html_select ){
        $html .= "<label>" . __( $config_value['filter_text'], 'wpt_pro' ) . "</label>" . $html_select;
        //$html .= "<label>" . __( 'Filter:', 'wpt_pro' ) . "</label>" . $html_select;
        $html .= '<a href="#" data-type="reset " data-temp_number="' . $temp_number . '" id="wpt_filter_reset_' . $temp_number . '" class="wpt_filter_reset wpt_filter_reset_' . $temp_number . '">' . __( $config_value['filter_reset_button'], 'wpt_pro' ) . '</a>';
    }
    return $html;
}
