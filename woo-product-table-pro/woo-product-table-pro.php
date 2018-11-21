<?php
/**
 * Plugin Name: WOO Product Table Pro
 * Plugin URI: https://codeastrology.com/wordpress-plugins/woo-product-table/
 * Description: WooCommerce all products display as a table in one page by shortcode. Fully responsive and mobile friendly. Easily customizable - color,background,title,text color etc.
 * Author: CodeAstrology
 * Author URI: https://codecanyon.net/user/codeastrology
 * Tags: woocommerce product,woocommerce product table, product table
 * 
 * Version: 3.7
 * Requires at least:    4.0.0
 * Tested up to:         4.9.8
 * WC requires at least: 3.0.0
 * WC tested up to: 	 3.5.0
 * 
 * Text Domain: wpt_pro
 * Domain Path: /languages/
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Defining constant
 */
define( 'WPT_PLUGIN_BASE_FOLDER', plugin_basename( dirname( __FILE__ ) ) );
define( 'WPT_PLUGIN_BASE_FILE', plugin_basename( __FILE__ ) );
define( "WPT_BASE_URL", WP_PLUGIN_URL . '/'. plugin_basename( dirname( __FILE__ ) ) . '/' );
define( "wpt_dir_base", dirname( __FILE__ ) . '/' );
define( "WPT_BASE_DIR", str_replace( '\\', '/', wpt_dir_base ) );


/**
 * Default Configuration for WOO Product Table Pro
 * 
 * @since 1.0.0 -5
 */
$shortCodeText = 'Product_Table';
//$wpt_ajax_permission_for_plugin = false;  //Has removed.
/**
* Including Plugin file for security
* Include_once
* 
* @since 1.0.0
*/
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
$WOO_Table = WOO_Product_Table::getInstance();

/**
 * @since 1.7
 */
WOO_Product_Table::$columns_array =  array(
    'serial_number' => 'SL',
    'thumbnails'    => 'Thumbnails',
    'children'      => 'Children',
    'product_title' => 'Product Title',
    'description'   =>  'Description',
    'category'      => 'Category',
    'tags'          => 'Tags',
    'sku'           => 'SKU',
    'weight'        => 'Weight(kg)',
    'length'        => 'Length(cm)',
    'width'         => 'Width(cm)',
    'height'        => 'Height(cm)',
    'rating'        => 'Rating',
    'stock'         => 'Stock',
    'price'         => 'Price',
    'wishlist'      => 'Wish List', //Added at 2.6
    'quantity'      => 'Quantity',
    'total'         => 'Total Price',
    'Message'       => 'Short Message', //Added at 1.9 date: 7.6.2018 d.m.y | To send custom Message to Client/Buyer
    'quick'         => 'Quick View',
    'date'          =>  'Date', //Added at 3.7 
    'modified_date' =>  'Modified Date', //Added at 3.7 
    'action'        => 'Action',
    'check'         => 'Check',
    'quoterequest'  => 'Quote Request', //Added at 2.6
);

/**
 * @since 1.7
 */
WOO_Product_Table::$colums_disable_array = array(
    'serial_number',
    'description',
    'tags',
    'weight',
    'length',
    'width',
    'height',
    'total',
    'quick',
    'date', //Added at 3.7 
    'modified_date', //Added at 3.7 
    'wishlist', //Added at 2.6
    'quoterequest', //Added at 2.6
    'Message', //Added at 1.9 date: 7.6.2018 d.m.y | To send custom Message to Client/Buyer
);

//Set Style Selection Options.
WOO_Product_Table::$style_form_options = array(
    'default'       =>  'Default Style',
    'blacky'       =>  'Beautifull Blacky',
    'smart'       =>  'Smart Thin',
    'none'             =>  'Select None',
    'green'         =>  'Green Style',
    'blue'         =>  'Blue Style',
    //'business'      =>  'Classical Business' //Deleted at 3.4 and replace with default template
);
/**
 * Set ShortCode text as Static Properties
 * 
 * @since 1.0.0 -5
 */
WOO_Product_Table::$shortCode = $shortCodeText;

/**
 * Set Default Value For Every where, 
 * 
 * @since 1.9
 */
WOO_Product_Table::$default = array(
    'custom_message_on_single_page'=>  true, //Set true to get form in Single Product page for Custom Message
    'plugin_name'           =>  WOO_Product_Table::getName(),
    'plugin_version'        =>  WOO_Product_Table::getVersion(),
    'sort_mini_filter'      =>  'ASC',
    'sort_searchbox_filter' =>  'ASC',
    'custom_add_to_cart'    =>  'add_cart_left_icon',
    'thumbs_image_size'     =>  60,
    'thumbs_lightbox'       => 1,
    'disable_product_link'  =>  '0',
    'disable_cat_tag_link'  =>  '0',
    'product_link_target'   =>  '_blank',
    'load_more_text'        =>  'Load more', //__( 'Load more', 'wpt_pro'),
    'quick_view_btn_text'   =>  __( 'Quick View', 'wpt_pro' ), //__( 'Load more', 'wpt_pro'),
    'loading_more_text'     =>  'Loading..', //__( 'Load more', 'wpt_pro'),
    'search_button_text'    =>  'Search', //__( 'Load more', 'wpt_pro'),
    'search_keyword_text'   =>  'Search Keyword', //__( 'Load more', 'wpt_pro'),
    'disable_loading_more'  =>  'normal',
    'instant_search_filter' =>  '0',
    'filter_text'           =>  'Filter:',
    'filter_reset_button'   =>  'Reset',
    'instant_search_text' =>  'Instant Search..',
    'yith_product_type' =>  'free',
    'yith_browse_list' =>  'Browse the list',
    'yith_add_to_quote_text' =>  'Add to Quote',
    'yith_add_to_quote_adding' =>  'Adding..',
    'yith_add_to_quote_added' =>  'Quoted',
    'default_quantity' =>  '1',
    'item'          =>  __( 'Item', 'wpt_pro' ), //It will use at custom.js file for Chinging
    'items'          =>  __( 'Items', 'wpt_pro' ), //It will use at custom.js file for Chinging
    'add2cart_all_added_text'=>  __( 'Added', 'wpt_pro' ), //It will use at custom.js file for Chinging
    'right_combination_message' => __( 'Not available', 'wpt_pro' ),
    'right_combination_message_alt' => __( 'Product variations is not set Properly. May be: price is not inputted. may be: Out of Stock.', 'wpt_pro' ),
    'no_more_query_message' => __( 'There is no more products based on current Query.', 'wpt_pro' ),
    'select_all_items_message' => __( 'Please select all items.', 'wpt_pro' ),
    'out_of_stock_message' => __( 'Out of Stock', 'wpt_pro' ),
    'adding_in_progress'    =>  __( 'Adding in Progress', 'wpt_pro' ),
    'no_right_combination'    =>  __( 'No Right Combination', 'wpt_pro' ),
    'sorry_out_of_stock'    =>  __( 'Sorry! Out of Stock!', 'wpt_pro' ),
    'type_your_message'    =>  __( 'Type your Message.', 'wpt_pro' ),
    'sorry_plz_right_combination' =>    __( 'Sorry, Please choose right combination.', 'wpt_pro' ),
    
    'all_selected_direct_checkout' => 'no',
    'product_direct_checkout' => 'no',
    
    //Added Search Box Features @Since 3.3
    'search_box_title' => __( 'Search Box (<small>All Fields Optional</small>)', 'wpt_pro' ),
    'search_box_searchkeyword' => __( 'Search Keyword', 'wpt_pro' ),
    'search_box_orderby' => __( 'Order By', 'wpt_pro' ),
    'search_box_order' => __( 'Order', 'wpt_pro' ),
    //For Default Table's Content
    'table_in_stock'        =>  __( 'In Stock', 'wpt_pro' ),//'In Stock',
    'table_out_of_stock'    =>  __( 'Out of Stock', 'wpt_pro' ),//'Out of Stock',
    'table_on_back_order'    =>  __( 'On Back Order', 'wpt_pro' ),//'On Back Order',
    
    //For Mini cart Default value change
    'mcart_cart'        =>  __( 'Cart', 'wpt_pro' ),//'Cart',
    'mcart_view_cart'   =>  __( 'View Cart', 'wpt_pro' ),//'View Cart',
    'mcart_checkout'    =>  __( 'Checkout', 'wpt_pro' ),//'Checkout',
    'mcart_price'       =>  __( 'Price', 'wpt_pro' ),//'Checkout',
    'mcart_subtotla'       =>  __( 'Subtotal', 'wpt_pro' ),//'Checkout',
    'mcart_view_title'    =>  __( 'View your shopping cart', 'wpt_pro' ),//'Checkout',
    'mcart_empty_now'    =>  __( 'Your cart is empty.', 'wpt_pro' ),//'Checkout',
    
);

/**
 * Main Manager Class for WOO Product Table Plugin.
 * All Important file included here.
 * Set Path and Constant also set WOO_Product_Table Class
 * Already set $_instance, So no need again call
 */
class WOO_Product_Table{
    
    /**
     * To set Default Value for Woo Product Table, So that, we can set Default Value in Plugin Start and 
     * can get Any were
     *
     * @var Array 
     */
    public static $default = array();
    
    /*
     * List of Path
     * 
     * @since 1.0.0
     * @var array
     */
    protected $paths = array();
    
    /**
     * Set like Constant static array
     * Get this by getPath() method
     * Set this by setConstant() method
     *  
     * @var type array
     */
    private static $constant = array();
    
    public static $shortCode;

    
    /**
     * Only for Admin Section, Collumn Array
     * 
     * @since 1.7
     * @var Array
     */
    public static $columns_array = array();

    
    /**
     * Only for Admin Section, Disable Collumn Array
     * 
     * @since 1.7
     * @var Array
     */
    public static $colums_disable_array = array();

    /**
     * Set Array for Style Form Section Options
     *
     * @var type 
     */
    public static $style_form_options = array();
    
    /**
    * Core singleton class
    * @var self - pattern realization
    */
   private static $_instance;
   
   /**
    * Set Plugin Mode as 1 for Giving Data to UPdate Options
    *
    * @var type Int
    */
   protected static $mode = 1;
   
    /**
    * Get the instane of WOO_Product_Table
    *
    * @return self
    */
   public static function getInstance() {
           if ( ! ( self::$_instance instanceof self ) ) {
                   self::$_instance = new self();
           }

           return self::$_instance;
   }
   
   
   public function __construct() {

       $dir = dirname( __FILE__ ); //dirname( __FILE__ )
       
       /**
        * See $path_args for Set Path and set Constant
        * 
        * @since 1.0.0
        */
       $path_args = array(
           'PLUGIN_BASE_FOLDER' =>  plugin_basename( $dir ),
           'PLUGIN_BASE_FILE' =>  plugin_basename( __FILE__ ),
           'BASE_URL' =>  WP_PLUGIN_URL. '/'. plugin_basename( $dir ) . '/',
           'BASE_DIR' =>  str_replace( '\\', '/', $dir . '/' ),
       );
       /**
        * Set Path Full with Constant as Array
        * 
        * @since 1.0.0
        */
       $this->setPath($path_args);
       
       /**
        * Set Constant
        * 
        * @since 1.0.0
        */
       $this->setConstant($path_args);
       
       //Load File
       if( is_admin() ){
        require_once $this->path('BASE_DIR','admin/plugin_setting_link.php');
        require_once $this->path('BASE_DIR','admin/menu.php');
        require_once $this->path('BASE_DIR','admin/style_js_adding_admin.php');
        require_once $this->path('BASE_DIR','admin/forms_admin.php');
        require_once $this->path('BASE_DIR','admin/configuration_page.php');
        /**
         * Currently Removed
         * @since 1.7
         */
        //require_once $this->path('BASE_DIR','admin/ajax_table_preview.php');
       }
       
       //Load these bellow file, Only woocommerce installed as well as Only for Front-End
       if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
           require_once $this->path('BASE_DIR','includes/style_js_adding.php');
           require_once $this->path('BASE_DIR','includes/functions.php');
           require_once $this->path('BASE_DIR','includes/ajax_add_to_cart.php'); //Added at V1.0.4 2/5/2018
           require_once $this->path('BASE_DIR','includes/shortcode.php');
           //require_once $this->path('BASE_DIR','includes/hide_on_mobile_css.php'); Removed at 1.0.4
       }else{
           require_once $this->path('BASE_DIR','includes/no_woocommerce.php');
       }
       
       
        //WOO_Product_Table::$_instance;
   }
   /**
    * Set Path
    * 
    * @param type $path_array
    * 
    * @since 1.0.0
    */
   public function setPath( $path_array ) {
       $this->paths = $path_array;
   }
   
   private function setConstant( $contanst_array ) {
       self::$constant = $this->paths;
   }
   /**
    * Set Path as like Constant Will Return Full Path
    * Name should like Constant and full Capitalize
    * 
    * @param type $name
    * @return string
    */
   public function path( $name, $_complete_full_file_path = false ) {
       $path = $this->paths[$name] . $_complete_full_file_path;
       return $path;
   }
   
   /**
    * To Get Full path to Anywhere based on Constant
    * 
    * @param type $constant_name
    * @return type String
    */
   public static function getPath( $constant_name = false ) {
       $path = self::$constant[$constant_name];
       return $path;
   }
   /**
    * Update Options when Installing
    * This method has update at Version 3.6
    * 
    * @since 1.0.0
    * @updated since 3.6_29.10.2018 d/m/y
    */
   public static function install() {
       //check current value
       $current_value = get_option('wpt_configure_options');
       //$current_value['disable_cat_tag_link']
       $default_value = self::$default;
       $changed_value = false;
       //Set default value in Options
       if($current_value){
           foreach( $default_value as $key=>$value ){
              if( isset($current_value[$key]) && $key != 'plugin_version' ){
                 $changed_value[$key] = $current_value[$key];
              }else{
                  $changed_value[$key] = $value;
              }
           }
           update_option( 'wpt_configure_options', $changed_value );
       }else{
           update_option( 'wpt_configure_options', $default_value );
       }
       
   }
   
   /**
    * Plugin Uninsall Activation Hook 
    * Static Method
    * 
    * @since 1.0.0
    */
   public function uninstall() {
       //Nothing for now
   }
   
   /**
    * Getting full Plugin data. We have used __FILE__ for the main plugin file.
    * 
    * @since V 1.5
    * @return Array Returnning Array of full Plugin's data for This Woo Product Table plugin
    */
   public static function getPluginData(){
       return get_plugin_data( __FILE__ );
   }
   
   /**
    * Getting Version by this Function/Method
    * 
    * @return type static String
    */
   public static function getVersion() {
       $data = self::getPluginData();
       return $data['Version'];
   }
   
   /**
    * Getting Version by this Function/Method
    * 
    * @return type static String
    */
   public static function getName() {
       $data = self::getPluginData();
       return $data['Name'];
   }
   public static function getDefault( $indexKey = false ){
       $default = self::$default;
       if( $indexKey && isset( $default[$indexKey] ) ){
           return $default[$indexKey];
       }
       return $default;
   }

}

//   $WOO_Table = WOO_Product_Table::getInstance();

/**
* Plugin Install and Uninstall
*/
register_activation_hook(__FILE__, array( 'WOO_Product_Table','install' ) );
register_deactivation_hook( __FILE__, array( 'WOO_Product_Table','uninstall' ) );
