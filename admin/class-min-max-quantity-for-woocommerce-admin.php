<?php

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.multidots.com
 * @since      1.0.0
 *
 * @package    Min_Max_Quantity_For_WooCommerce
 * @subpackage Min_Max_Quantity_For_WooCommerce/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Min_Max_Quantity_For_WooCommerce
 * @subpackage Min_Max_Quantity_For_WooCommerce/admin
 * @author     thedotstore <hello@thedotstore.com>
 */
class MMQW_Min_Max_Quantity_For_WooCommerce_Admin {
    public static $hook = null;

    const min_max_quantity_post_type = 'wc_mmqw';

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     *
     * @since    1.0.0
     *
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Shipping zone page
     *
     * @uses     MMQW_Shipping_Zone class
     * @uses     MMQW_Shipping_Zone::output()
     *
     * @since    1.0.0
     */
    public static function mmqw_checkout_settings_page() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/mmqw-checkout-settings-page.php';
    }

    /**
     * Mange Messages
     *
     * @since    1.0.0
     */
    public static function mmqw_manage_messages_page() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/mmqw-manage-message-page.php';
    }

    /**
     * Get MMQW shipping method
     *
     * @param string $args
     *
     * @return string $default_lang
     *
     * @since  3.4
     *
     */
    public static function mmqw_get_all_rule_list( $args ) {
        global $sitepress;
        if ( !empty( $sitepress ) ) {
            $default_lang = $sitepress->get_current_language();
        } else {
            $get_site_language = get_bloginfo( 'language' );
            if ( false !== strpos( $get_site_language, '-' ) ) {
                $get_site_language_explode = explode( '-', $get_site_language );
                $default_lang = $get_site_language_explode[0];
            } else {
                $default_lang = $get_site_language;
            }
        }
        $sm_args = array(
            'post_type'        => self::min_max_quantity_post_type,
            'posts_per_page'   => -1,
            'orderby'          => 'menu_order',
            'order'            => 'ASC',
            'suppress_filters' => false,
        );
        if ( 'not_list' === $args ) {
            $sm_args['post_status'] = 'publish';
        }
        $get_all_shipping = new WP_Query($sm_args);
        $get_all_sm = $get_all_shipping->get_posts();
        $sort_order = array();
        $getSortOrder = get_option( 'sm_sortable_order_' . $default_lang );
        if ( isset( $getSortOrder ) && !empty( $getSortOrder ) ) {
            foreach ( $getSortOrder as $sort ) {
                $sort_order[$sort] = array();
            }
        }
        foreach ( $get_all_sm as $carrier_id => $carrier ) {
            $carrier_name = $carrier->ID;
            if ( array_key_exists( $carrier_name, $sort_order ) ) {
                $sort_order[$carrier_name][$carrier_id] = $get_all_sm[$carrier_id];
                unset($get_all_sm[$carrier_id]);
            }
        }
        foreach ( $sort_order as $carriers ) {
            $get_all_sm = array_merge( $get_all_sm, $carriers );
        }
        return $get_all_sm;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @param string $hook display current page name
     *
     * @since    1.0.0
     *
     */
    public function mmqw_enqueue_styles( $hook ) {
        if ( false !== strpos( $hook, '_page_mmqw' ) ) {
            wp_enqueue_style(
                $this->plugin_name . 'select2-min',
                plugin_dir_url( __FILE__ ) . 'css/select2.min.css',
                array(),
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . '-jquery-ui-css',
                plugin_dir_url( __FILE__ ) . 'css/jquery-ui.min.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . 'main-style',
                plugin_dir_url( __FILE__ ) . 'css/style.css',
                array(),
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . 'media-css',
                plugin_dir_url( __FILE__ ) . 'css/media.css',
                array(),
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . 'plugin-new-style',
                plugin_dir_url( __FILE__ ) . 'css/plugin-new-style.css',
                array(),
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . 'plugin-setup-wizard',
                plugin_dir_url( __FILE__ ) . 'css/plugin-setup-wizard.css',
                array(),
                'all'
            );
            if ( !(mmqw_fs()->is__premium_only() && mmqw_fs()->can_use_premium_code()) ) {
                wp_enqueue_style(
                    $this->plugin_name . 'upgrade-dashboard-style',
                    plugin_dir_url( __FILE__ ) . 'css/upgrade-dashboard.css',
                    array(),
                    'all'
                );
            }
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @param string $hook display current page name
     *
     * @since    1.0.0
     *
     */
    public function mmqw_enqueue_scripts( $hook ) {
        wp_enqueue_style( 'wp-jquery-ui-dialog' );
        wp_enqueue_script( 'jquery-ui-accordion' );
        wp_enqueue_script( 'jquery-ui-datepicker' );
        if ( false !== strpos( $hook, '_page_mmqw' ) ) {
            wp_enqueue_script(
                $this->plugin_name . '-select2-full-min',
                plugin_dir_url( __FILE__ ) . 'js/select2.full.min.js',
                array('jquery', 'jquery-ui-datepicker'),
                $this->version,
                false
            );
            wp_enqueue_script(
                $this->plugin_name . '-help-scout-beacon-js',
                plugin_dir_url( __FILE__ ) . 'js/help-scout-beacon.js',
                array('jquery'),
                $this->version,
                false
            );
            wp_enqueue_script( 'jquery-tiptip' );
            wp_enqueue_script(
                $this->plugin_name . 'freemius_pro',
                'https://checkout.freemius.com/checkout.min.js',
                array('jquery'),
                $this->version,
                true
            );
            wp_enqueue_script(
                $this->plugin_name,
                plugin_dir_url( __FILE__ ) . 'js/min-max-quantity-for-woocommerce-admin.js',
                array(
                    'jquery',
                    'jquery-ui-dialog',
                    'jquery-ui-accordion',
                    'jquery-ui-sortable',
                    'select2'
                ),
                $this->version,
                false
            );
            wp_localize_script( $this->plugin_name, 'coditional_vars', array(
                'ajaxurl'                          => admin_url( 'admin-ajax.php' ),
                'ajax_icon'                        => esc_url( plugin_dir_url( __FILE__ ) . '/images/ajax-loader.gif' ),
                'plugin_url'                       => plugin_dir_url( __FILE__ ),
                'mmqw_ajax_nonce'                  => wp_create_nonce( 'mmqw_nonce' ),
                'setup_wizard_ajax_nonce'          => wp_create_nonce( 'wizard_ajax_nonce' ),
                'dpb_api_url'                      => MMQW_STORE_URL,
                'country'                          => esc_html__( 'Country', 'min-and-max-quantity-for-woocommerce' ),
                'min_quantity'                     => esc_html__( 'Min quantity', 'min-and-max-quantity-for-woocommerce' ),
                'max_quantity'                     => esc_html__( 'Max quantity', 'min-and-max-quantity-for-woocommerce' ),
                'validation_length1'               => esc_html__( 'Please enter 3 or more characters', 'min-and-max-quantity-for-woocommerce' ),
                'select_some_options'              => esc_html__( 'Select some options', 'min-and-max-quantity-for-woocommerce' ),
                'select_category'                  => esc_html__( 'Select Category', 'min-and-max-quantity-for-woocommerce' ),
                'delete'                           => esc_html__( 'Delete', 'min-and-max-quantity-for-woocommerce' ),
                'min_max_qty_error'                => esc_html__( 'Max quantity should be greater or equal to min quantity.', 'min-and-max-quantity-for-woocommerce' ),
                'success_msg1'                     => esc_html__( 'Order saved successfully', 'min-and-max-quantity-for-woocommerce' ),
                'warning_msg5'                     => esc_html__( 'Please fill some required fields in the Advanced Rules section.', 'min-and-max-quantity-for-woocommerce' ),
                'location_specific'                => esc_html__( 'Location Specific', 'min-and-max-quantity-for-woocommerce' ),
                'product_specific'                 => esc_html__( 'Product Specific', 'min-and-max-quantity-for-woocommerce' ),
                'cart_specific'                    => esc_html__( 'Cart Specific', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_product'                   => esc_html__( 'Product', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_variable_product'          => esc_html__( 'Variable Product', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_category_product'          => esc_html__( 'Category', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_country'                   => esc_html__( 'Country', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_total_sales'               => esc_html__( 'Total Sales', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_stock_quantity'            => esc_html__( 'Stock Quantity', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_sale_price'                => esc_html__( 'Sale Price', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_product_age'               => esc_html__( 'Product Age', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_best_sellers'              => esc_html__( 'Best Sellers', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_product_attributes'        => esc_html__( 'Product Attributes', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_cart_coupon'               => esc_html__( 'Cart Coupon', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_shipping_method'           => esc_html__( 'Shipping Method', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_shipping_zone'             => esc_html__( 'Shipping Zone', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_total_sales_in_pro'        => esc_html__( 'Total Sales 🔒', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_stock_quantity_in_pro'     => esc_html__( 'Stock Quantity 🔒', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_sale_price_in_pro'         => esc_html__( 'Sale Price 🔒', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_product_age_in_pro'        => esc_html__( 'Product Age 🔒', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_best_sellers_in_pro'       => esc_html__( 'Best Sellers 🔒', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_product_attributes_in_pro' => esc_html__( 'Product Attributes 🔒', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_cart_coupon_in_pro'        => esc_html__( 'Cart Coupon 🔒', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_shipping_method_in_pro'    => esc_html__( 'Shipping Method 🔒', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_shipping_zone_in_pro'      => esc_html__( 'Shipping Zone 🔒', 'min-and-max-quantity-for-woocommerce' ),
                'equal_to'                         => esc_html__( 'Equal to ( = )', 'min-and-max-quantity-for-woocommerce' ),
                'greater_than'                     => esc_html__( 'Greater than ( > )', 'min-and-max-quantity-for-woocommerce' ),
                'less_than'                        => esc_html__( 'Less than ( < )', 'min-and-max-quantity-for-woocommerce' ),
                'greater_or_equal_to'              => esc_html__( 'Greater or Equal to ( >= )', 'min-and-max-quantity-for-woocommerce' ),
                'less_or_equal_to'                 => esc_html__( 'Less or Equal to ( <= )', 'min-and-max-quantity-for-woocommerce' ),
                'not_equal_to'                     => esc_html__( 'Not Equal to ( != )', 'min-and-max-quantity-for-woocommerce' ),
                'default_qty_min'                  => esc_html__( 'Default quantity should greater or equal to min quantity ', 'min-and-max-quantity-for-woocommerce' ),
                'default_qty_max'                  => esc_html__( 'Default quantity should less or equal to max quantity', 'min-and-max-quantity-for-woocommerce' ),
                'user_specific'                    => esc_html__( 'User Specific', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_user'                      => esc_html__( 'User', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_user_role'                 => esc_html__( 'User Role', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_user_in_pro'               => esc_html__( 'User 🔒', 'min-and-max-quantity-for-woocommerce' ),
                'qty_on_user_role_in_pro'          => esc_html__( 'User Role 🔒', 'min-and-max-quantity-for-woocommerce' ),
                'limit_section'                    => esc_html__( 'Limit Section', 'min-and-max-quantity-for-woocommerce' ),
                'time_frame'                       => esc_html__( 'Time Frame', 'min-and-max-quantity-for-woocommerce' ),
                'time_frame_in_pro'                => esc_html__( 'Time Frame 🔒', 'min-and-max-quantity-for-woocommerce' ),
                'time_frame_error'                 => esc_html__( 'Please add valid end time and start time', 'min-and-max-quantity-for-woocommerce' ),
                'start_time_future'                => esc_html__( 'Start time should be greater than current time', 'min-and-max-quantity-for-woocommerce' ),
                'end_time_future'                  => esc_html__( 'End time should be greater than current time', 'min-and-max-quantity-for-woocommerce' ),
            ) );
        }
    }

    /**
     * Add plugin menus
     *
     * @since  1.0.0
     *
     */
    public function mmqw_dot_store_plugin_menu_integration() {
        global $GLOBALS;
        if ( empty( $GLOBALS['admin_page_hooks']['dots_store'] ) ) {
            add_menu_page(
                'Dotstore Plugins',
                __( 'Dotstore Plugins', 'min-and-max-quantity-for-woocommerce' ),
                'null',
                'dots_store',
                array($this, 'dot_store_menu_page'),
                'dashicons-marker',
                25
            );
        }
        $get_hook = add_submenu_page(
            'dots_store',
            'Min/Max Quantity',
            'Min/Max Quantity',
            'manage_options',
            'mmqw-rules-list',
            array($this, 'mmqw_rules_list_page')
        );
        add_submenu_page(
            'dots_store',
            'Checkout Settings',
            'Checkout Settings',
            'manage_options',
            'mmqw-checkout-settings',
            array($this, 'mmqw_checkout_settings_page')
        );
        add_submenu_page(
            'dots_store',
            'Manage Messages',
            'Manage Messages',
            'manage_options',
            'mmqw-manage-messages',
            array($this, 'mmqw_manage_messages_page')
        );
        add_submenu_page(
            'dots_store',
            'Getting Started',
            'Getting Started',
            'manage_options',
            'mmqw-get-started',
            array($this, 'mmqw_get_started_page')
        );
        add_submenu_page(
            'dots_store',
            'Display Rules',
            'Display Rules',
            'manage_options',
            'mmqw-display-rules',
            array($this, 'mmqw_display_rules_page')
        );
        add_submenu_page(
            'dots_store',
            'Get Premium',
            'Get Premium',
            'manage_options',
            'mmqw-upgrade-dashboard',
            array($this, 'mmqw_free_user_upgrade_page')
        );
        add_action( "load-{$get_hook}", array($this, "mmqw_screen_options") );
    }

    /**
     * Add custom css for dotstore icon in admin area
     *
     * @since  1.0.0
     *
     */
    public function mmqw_dot_store_icon_css() {
        echo '<style>
	    .toplevel_page_dots_store .dashicons-marker::after{content:"";border:3px solid;position:absolute;top:14px;left:15px;border-radius:50%;opacity: 0.6;}
	    li.toplevel_page_dots_store:hover .dashicons-marker::after,li.toplevel_page_dots_store.current .dashicons-marker::after{opacity: 1;}
	  	</style>';
    }

    /**
     * Screen option for min/max list
     *
     * @since    1.0.0
     */
    public function mmqw_screen_options() {
        $get_action = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( isset( $get_action ) && 'mmqw-rules-list' === $get_action ) {
            $args = array(
                'label'   => esc_html__( 'List Per Page', 'min-and-max-quantity-for-woocommerce' ),
                'default' => 10,
                'option'  => 'mmqw_count_per_page',
            );
            add_screen_option( 'per_page', $args );
        }
    }

    /**
     * Add screen option for per page
     *
     * @param bool   $status
     * @param string $option
     * @param int    $value
     *
     * @return int $value
     * @since 1.0.0
     *
     */
    public function mmqw_set_screen_options( $status, $option, $value ) {
        $mmqw_screens = array('mmqw_count_per_page');
        if ( in_array( $option, $mmqw_screens, true ) ) {
            return $value;
        }
        return $status;
    }

    /**
     * Shipping List Page
     *
     * @since    1.0.0
     */
    public function mmqw_rules_list_page() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/mmqw-list-rules-page.php';
        $ammqw_rule_lising_obj = new MMQW_Rule_Listing_Page();
        $ammqw_rule_lising_obj->mmqw_rule_output();
    }

    /**
     * Quick guide page
     *
     * @since    1.0.0
     */
    public function mmqw_get_started_page() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/mmqw-get-started-page.php';
    }

    // Display rules page
    public function mmqw_display_rules_page() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/mmqw-display-rules.php';
    }

    /**
     * Premium version info page
     *
     */
    public function mmqw_free_user_upgrade_page() {
        require_once plugin_dir_path( __FILE__ ) . '/partials/dots-upgrade-dashboard.php';
    }

    /**
     * Redirect to shipping list page
     *
     * @since    1.0.0
     */
    public function mmqw_redirect_rule_list_function() {
        $get_section = filter_input( INPUT_GET, 'section', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( isset( $get_section ) && !empty( $get_section ) && 'mmqw' === $get_section ) {
            wp_safe_redirect( add_query_arg( array(
                'page' => 'mmqw-rules-list',
            ), admin_url( 'admin.php' ) ) );
            exit;
        }
    }

    /**
     * Redirect to quick start guide after plugin activation
     *
     * @uses     mmqw_register_post_type()
     *
     * @since    1.0.0
     */
    public function mmqw_welcome_screen_do_activation_redirect() {
        $this->mmqw_register_post_type();
        // if no activation redirect
        if ( !get_transient( '_welcome_screen_mmqw_mode_activation_redirect_data' ) ) {
            return;
        }
        // Delete the redirect transient
        delete_transient( '_welcome_screen_mmqw_mode_activation_redirect_data' );
        // if activating from network, or bulk
        $activate_multi = filter_input( INPUT_GET, 'activate-multi', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( is_network_admin() || isset( $activate_multi ) ) {
            return;
        }
        // Redirect to pluign manage rules page
        wp_safe_redirect( add_query_arg( array(
            'page' => 'mmqw-rules-list',
        ), admin_url( 'admin.php' ) ) );
        exit;
    }

    /**
     * Register post type
     *
     * @since    1.0.0
     */
    public function mmqw_register_post_type() {
        register_post_type( self::min_max_quantity_post_type, array(
            'labels' => array(
                'name'          => __( 'Min/Max Quantity Rules', 'min-and-max-quantity-for-woocommerce' ),
                'singular_name' => __( 'Min/Max Quantity Rules', 'min-and-max-quantity-for-woocommerce' ),
            ),
        ) );
    }

    /**
     * Remove submenu from admin screeen
     *
     * @since    1.0.0
     */
    public function mmqw_remove_admin_submenus() {
        remove_submenu_page( 'dots_store', 'dots_store' );
        remove_submenu_page( 'dots_store', 'mmqw-checkout-settings' );
        remove_submenu_page( 'dots_store', 'mmqw-manage-messages' );
        remove_submenu_page( 'dots_store', 'mmqw-get-started' );
        remove_submenu_page( 'dots_store', 'mmqw-display-rules' );
        remove_submenu_page( 'dots_store', 'mmqw-upgrade-dashboard' );
    }

    /**
     * Get default site language
     *
     * @return string $default_lang
     *
     * @since  3.4
     *
     */
    public function mmqw_get_default_langugae_with_sitpress() {
        global $sitepress;
        if ( !empty( $sitepress ) ) {
            $default_lang = $sitepress->get_current_language();
        } else {
            $default_lang = $this->mmqw_get_current_site_language();
        }
        return $default_lang;
    }

    /**
     * Get current site langugae
     *
     * @return string $default_lang
     * @since 1.0.0
     *
     */
    public function mmqw_get_current_site_language() {
        $get_site_language = get_bloginfo( 'language' );
        if ( false !== strpos( $get_site_language, '-' ) ) {
            $get_site_language_explode = explode( '-', $get_site_language );
            $default_lang = $get_site_language_explode[0];
        } else {
            $default_lang = $get_site_language;
        }
        return $default_lang;
    }

    /**
     * Save shipping order in shipping list section
     *
     * @since 1.0.0
     */
    public function mmqw_sm_sort_order() {
        $default_lang = $this->mmqw_get_default_langugae_with_sitpress();
        $get_smOrderArray = filter_input(
            INPUT_GET,
            'smOrderArray',
            FILTER_SANITIZE_NUMBER_INT,
            FILTER_REQUIRE_ARRAY
        );
        $smOrderArray = ( !empty( $get_smOrderArray ) ? array_map( 'sanitize_text_field', wp_unslash( $get_smOrderArray ) ) : '' );
        if ( isset( $smOrderArray ) && !empty( $smOrderArray ) ) {
            update_option( 'sm_sortable_order_' . $default_lang, $smOrderArray );
        }
        wp_die();
    }

    /**
     * Convert array to json
     *
     * @param array $arr
     *
     * @return array $filter_data
     * @since 1.0.0
     *
     */
    public function mmqw_convert_array_to_json( $arr ) {
        $filter_data = [];
        foreach ( $arr as $key => $value ) {
            $option = [];
            $option['name'] = $value;
            $option['attributes']['value'] = $key;
            $filter_data[] = $option;
        }
        return $filter_data;
    }

    /**
     * Display variable product list based product specific option
     *
     * @return string $html
     * @uses   mmqw_get_default_langugae_with_sitpress()
     * @uses   wc_get_product()
     * @uses   WC_Product::is_type()
     * @uses   Min_Max_Quantity_For_WooCommerce::mmqw_allowed_html_tags()
     *
     * @since  1.0.0
     *
     */
    public function mmqw_product_fees_conditions_variable_values_product_ajax() {
        global $sitepress;
        $default_lang = $this->mmqw_get_default_langugae_with_sitpress();
        $json = true;
        $filter_variable_product_list = [];
        $request_value = filter_input( INPUT_GET, 'value', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $post_value = ( isset( $request_value ) ? sanitize_text_field( $request_value ) : '' );
        $baselang_product_ids = array();
        function mmqw_posts_wheres(  $where, &$wp_query  ) {
            global $wpdb;
            $search_term = $wp_query->get( 'search_pro_title' );
            if ( !empty( $search_term ) ) {
                $search_term_like = $wpdb->esc_like( $search_term );
                $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $search_term_like ) . '%\'';
            }
            return $where;
        }

        $product_args = array(
            'post_type'        => 'product',
            'posts_per_page'   => -1,
            'search_pro_title' => $post_value,
            'post_status'      => 'publish',
            'orderby'          => 'title',
            'order'            => 'ASC',
        );
        $get_all_products = new WP_Query($product_args);
        if ( !empty( $get_all_products ) ) {
            foreach ( $get_all_products->posts as $get_all_product ) {
                $_product = wc_get_product( $get_all_product->ID );
                if ( $_product->is_type( 'variable' ) ) {
                    $variations = $_product->get_available_variations();
                    foreach ( $variations as $value ) {
                        if ( !empty( $sitepress ) ) {
                            $defaultlang_product_id = apply_filters(
                                'wpml_object_id',
                                $value['variation_id'],
                                'product',
                                true,
                                $default_lang
                            );
                        } else {
                            $defaultlang_product_id = $value['variation_id'];
                        }
                        $baselang_product_ids[] = $defaultlang_product_id;
                    }
                }
            }
        }
        $html = '';
        if ( isset( $baselang_product_ids ) && !empty( $baselang_product_ids ) ) {
            foreach ( $baselang_product_ids as $baselang_product_id ) {
                $html .= '<option value="' . esc_attr( $baselang_product_id ) . '">' . '#' . esc_html( $baselang_product_id ) . ' - ' . esc_html( get_the_title( $baselang_product_id ) ) . '</option>';
                $filter_variable_product_list[] = array($baselang_product_id, get_the_title( $baselang_product_id ));
            }
        }
        if ( $json ) {
            echo wp_json_encode( $filter_variable_product_list );
            wp_die();
        }
        echo wp_kses( $html, Min_Max_Quantity_For_WooCommerce::mmqw_allowed_html_tags() );
        wp_die();
    }

    /**
     * Handle AJAX request for products.
     *
     * @since 3.3
     */
    public function mmqw_load_products_ajax() {
        check_ajax_referer( 'mmqw_nonce', 'security' );
        // Check if search term is set
        if ( isset( $_GET['q'] ) ) {
            $search_term = sanitize_text_field( wp_unslash( $_GET['q'] ) );
            $args = array(
                'post_type'      => array('product', 'product_variation'),
                'posts_per_page' => -1,
                's'              => $search_term,
                'post_status'    => 'publish',
            );
            $products = new WP_Query($args);
            $results = array();
            if ( $products->have_posts() ) {
                while ( $products->have_posts() ) {
                    $products->the_post();
                    $product_id = get_the_ID();
                    $product = wc_get_product( $product_id );
                    if ( $product->is_type( 'variable' ) ) {
                        $available_variations = $product->get_available_variations();
                        foreach ( $available_variations as $variation ) {
                            $variation_id = $variation['variation_id'];
                            $variation_obj = wc_get_product( $variation_id );
                            $results[] = array(
                                'id'   => $variation_id,
                                'text' => '#' . $variation_id . ' - ' . $variation_obj->get_name(),
                            );
                        }
                    } elseif ( $product->is_type( 'simple' ) ) {
                        $results[] = array(
                            'id'   => $product_id,
                            'text' => '#' . $product_id . ' - ' . get_the_title(),
                        );
                    }
                }
            }
            wp_reset_postdata();
            wp_send_json( $results );
        }
        wp_die();
    }

    /**
     * Display simple and variable product list based product specific option in Advance Pricing Rules
     *
     * @return string $html
     * @uses   mmqw_get_default_langugae_with_sitpress()
     * @uses   wc_get_product()
     * @uses   WC_Product::is_type()
     * @uses   get_available_variations()
     * @uses   Min_Max_Quantity_For_WooCommerce::mmqw_allowed_html_tags()
     *
     * @since  3.4
     *
     */
    public function mmqw_simple_and_variation_product_list_ajax() {
        global $sitepress;
        $default_lang = $this->mmqw_get_default_langugae_with_sitpress();
        $json = true;
        $filter_product_list = [];
        $request_value = filter_input( INPUT_GET, 'value', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $post_value = ( isset( $request_value ) ? sanitize_text_field( $request_value ) : '' );
        $request_with_variable = filter_input( INPUT_GET, 'with_variable', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $post_with_variable = ( isset( $request_with_variable ) ? sanitize_text_field( $request_with_variable ) : '' );
        $baselang_simple_product_ids = array();
        $baselang_variation_product_ids = array();
        function mmqw_posts_where(  $where, $wp_query  ) {
            global $wpdb;
            $search_term = $wp_query->get( 'search_pro_title' );
            if ( !empty( $search_term ) ) {
                $search_term_like = $wpdb->esc_like( $search_term );
                $where .= ' AND ( ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $search_term_like ) . '%\' OR ' . $wpdb->posts . '.ID IN ( SELECT ' . $wpdb->postmeta . '.post_id  FROM ' . $wpdb->postmeta . ' WHERE ' . $wpdb->postmeta . '.meta_key = "_sku" AND ' . $wpdb->postmeta . '.meta_value LIKE \'%' . esc_sql( $search_term_like ) . '%\'))';
            }
            return $where;
        }

        $product_args = array(
            'post_type'        => 'product',
            'posts_per_page'   => 100,
            'search_pro_title' => $post_value,
            'post_status'      => 'publish',
            'orderby'          => 'title',
            'order'            => 'ASC',
        );
        add_filter(
            'posts_where',
            'mmqw_posts_where',
            10,
            2
        );
        $get_wp_query = new WP_Query($product_args);
        remove_filter(
            'posts_where',
            'mmqw_posts_where',
            10,
            2
        );
        $get_all_products = $get_wp_query->posts;
        if ( isset( $get_all_products ) && !empty( $get_all_products ) ) {
            foreach ( $get_all_products as $get_all_product ) {
                $_product = wc_get_product( $get_all_product->ID );
                if ( $_product->is_type( 'variable' ) ) {
                    $variations = $_product->get_available_variations();
                    foreach ( $variations as $value ) {
                        if ( !empty( $sitepress ) ) {
                            $defaultlang_variation_product_id = apply_filters(
                                'wpml_object_id',
                                $value['variation_id'],
                                'product',
                                true,
                                $default_lang
                            );
                        } else {
                            $defaultlang_variation_product_id = $value['variation_id'];
                        }
                        $baselang_variation_product_ids[] = $defaultlang_variation_product_id;
                    }
                }
                if ( $_product->is_type( 'simple' ) ) {
                    if ( !empty( $sitepress ) ) {
                        $defaultlang_simple_product_id = apply_filters(
                            'wpml_object_id',
                            $get_all_product->ID,
                            'product',
                            true,
                            $default_lang
                        );
                    } else {
                        $defaultlang_simple_product_id = $get_all_product->ID;
                    }
                    $baselang_simple_product_ids[] = $defaultlang_simple_product_id;
                }
            }
        }
        if ( 'true' === $post_with_variable ) {
            $baselang_product_ids = $baselang_variation_product_ids;
        } else {
            $baselang_product_ids = $baselang_simple_product_ids;
        }
        $html = '';
        if ( isset( $baselang_product_ids ) && !empty( $baselang_product_ids ) ) {
            foreach ( $baselang_product_ids as $baselang_product_id ) {
                $html .= '<option value="' . esc_attr( $baselang_product_id ) . '">' . '#' . esc_html( $baselang_product_id ) . ' - ' . esc_html( get_the_title( $baselang_product_id ) ) . '</option>';
                $filter_product_list[] = array($baselang_product_id, get_the_title( $baselang_product_id ));
            }
        }
        if ( $json ) {
            echo wp_json_encode( $filter_product_list );
            wp_die();
        }
        echo wp_kses( $html, Min_Max_Quantity_For_WooCommerce::mmqw_allowed_html_tags() );
        wp_die();
    }

    /**
     * Save shipping method
     * 
     * @return bool false if post is empty otherwise it will redirect to shipping method list
     * @since  1.0.0
     *
     * @uses   update_post_meta()
     *
     */
    public function mmqw_rules_conditions_save() {
        global $sitepress;
        if ( empty( $_POST['mmqw_conditions_save'] ) ) {
            return false;
        }
        $post_type = filter_input( INPUT_POST, 'post_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $mmqw_conditions_save = filter_input( INPUT_POST, 'mmqw_conditions_save', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( isset( $post_type ) && self::min_max_quantity_post_type === sanitize_text_field( $post_type ) && wp_verify_nonce( sanitize_text_field( $mmqw_conditions_save ), 'mmqw_save_action' ) ) {
            $method_id = filter_input( INPUT_POST, 'fee_post_id', FILTER_SANITIZE_NUMBER_INT );
            $sm_status = filter_input( INPUT_POST, 'sm_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $fee_settings_product_fee_title = filter_input( INPUT_POST, 'fee_settings_product_fee_title', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $get_fee_settings_unique_shipping_title = filter_input( INPUT_POST, 'fee_settings_unique_shipping_title', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $fee_settings_unique_shipping_title = ( isset( $get_fee_settings_unique_shipping_title ) ? sanitize_text_field( $get_fee_settings_unique_shipping_title ) : '' );
            $mmqw_rules_count = self::mmqw_sm_count_rules();
            settype( $method_id, 'integer' );
            if ( isset( $sm_status ) ) {
                $post_status = 'publish';
            } else {
                $post_status = 'draft';
            }
            if ( '' !== $method_id && 0 !== $method_id ) {
                $fee_post = array(
                    'ID'          => $method_id,
                    'post_title'  => $fee_settings_unique_shipping_title,
                    'post_status' => $post_status,
                    'menu_order'  => $mmqw_rules_count + 1,
                    'post_type'   => self::min_max_quantity_post_type,
                );
                $method_id = wp_update_post( $fee_post );
            } else {
                $fee_post = array(
                    'post_title'  => $fee_settings_unique_shipping_title,
                    'post_status' => $post_status,
                    'menu_order'  => $mmqw_rules_count + 1,
                    'post_type'   => self::min_max_quantity_post_type,
                );
                $method_id = wp_insert_post( $fee_post );
            }
            if ( '' !== $method_id && 0 !== $method_id ) {
                if ( $method_id > 0 ) {
                    // Save group data
                    $mmqw_groups = filter_input(
                        INPUT_POST,
                        'mmqw_group',
                        FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                        FILTER_REQUIRE_ARRAY
                    );
                    $mmqw_group = ( isset( $mmqw_groups ) && !empty( $mmqw_groups ) ? $mmqw_groups : array() );
                    $get_mmqw_total_groups = filter_input( INPUT_POST, 'mmqw_total_groups', FILTER_SANITIZE_NUMBER_INT );
                    $mmqw_total_groups = ( isset( $get_mmqw_total_groups ) ? wp_unslash( absint( $get_mmqw_total_groups ) ) : 1 );
                    update_post_meta( $method_id, 'mmqw_total_groups', $mmqw_total_groups );
                    update_post_meta( $method_id, 'mmqw_rule_groups', $mmqw_group );
                    update_post_meta( $method_id, 'fee_settings_unique_shipping_title', $fee_settings_unique_shipping_title );
                    if ( !empty( $sitepress ) ) {
                        do_action(
                            'wpml_register_single_string',
                            'min-and-max-quantity-for-woocommerce',
                            sanitize_text_field( $fee_settings_product_fee_title ),
                            sanitize_text_field( $fee_settings_product_fee_title )
                        );
                    }
                    $getSortOrder = get_option( 'sm_sortable_order' );
                    if ( !empty( $getSortOrder ) ) {
                        foreach ( $getSortOrder as $getSortOrder_id ) {
                            settype( $getSortOrder_id, 'integer' );
                        }
                        array_unshift( $getSortOrder, $method_id );
                    }
                    update_option( 'sm_sortable_order', $getSortOrder );
                }
            } else {
                echo '<div class="updated error"><p>' . esc_html__( 'Error saving Min/Max Quantity rule.', 'min-and-max-quantity-for-woocommerce' ) . '</p></div>';
                return false;
            }
            $mmqwnonce = wp_create_nonce( 'mmqwnonce' );
            wp_safe_redirect( add_query_arg( array(
                'page'     => 'mmqw-rules-list',
                '_wpnonce' => esc_attr( $mmqwnonce ),
            ), admin_url( 'admin.php' ) ) );
            exit;
        }
    }

    /**
     * Count total shipping method
     *
     * @return int $count_method
     * @since    3.5
     *
     */
    public static function mmqw_sm_count_rules() {
        $shipping_method_args = array(
            'post_type'      => self::min_max_quantity_post_type,
            'post_status'    => array('publish', 'draft'),
            'posts_per_page' => -1,
            'orderby'        => 'ID',
            'order'          => 'DESC',
        );
        $sm_post_query = new WP_Query($shipping_method_args);
        $shipping_method_list = $sm_post_query->posts;
        return count( $shipping_method_list );
    }

    /**
     * Review message in footer
     *
     * @return string
     * @since  1.0.0
     *
     */
    public function mmqw_admin_footer_review() {
        $url = 'https://wordpress.org/plugins/min-and-max-quantity-for-woocommerce/#reviews';
        $html = sprintf( wp_kses( __( '<strong>We need your support</strong> to keep updating and improving the plugin. Please <a href="%1$s" target="_blank">help us by leaving a good review</a> :) Thanks!', 'min-and-max-quantity-for-woocommerce' ), array(
            'strong' => array(),
            'a'      => array(
                'href'   => array(),
                'target' => 'blank',
            ),
        ) ), esc_url( $url ) );
        echo wp_kses_post( $html );
    }

    /**
     * Change shipping status from list of shipping method
     *
     * @since  3.4
     *
     * @uses   update_post_meta()
     *
     * if current_shipping_id is empty then it will give message.
     */
    public function mmqw_change_status_from_list_section() {
        global $sitepress;
        $default_lang = $this->mmqw_get_default_langugae_with_sitpress();
        /* Check for post request */
        $get_current_shipping_id = filter_input( INPUT_GET, 'current_shipping_id', FILTER_SANITIZE_NUMBER_INT );
        if ( !empty( $sitepress ) ) {
            $get_current_shipping_id = apply_filters(
                'wpml_object_id',
                $get_current_shipping_id,
                'product',
                true,
                $default_lang
            );
        } else {
            $get_current_shipping_id = $get_current_shipping_id;
        }
        $get_current_value = filter_input( INPUT_GET, 'current_value', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_post_id = ( isset( $get_current_shipping_id ) ? absint( $get_current_shipping_id ) : '' );
        if ( empty( $get_post_id ) ) {
            echo '<strong>' . esc_html__( 'Something went wrong', 'min-and-max-quantity-for-woocommerce' ) . '</strong>';
            wp_die();
        }
        $current_value = ( isset( $get_current_value ) ? sanitize_text_field( $get_current_value ) : '' );
        if ( 'true' === $current_value ) {
            $post_args = array(
                'ID'          => $get_post_id,
                'post_status' => 'publish',
                'post_type'   => self::min_max_quantity_post_type,
            );
            $post_update = wp_update_post( $post_args );
            update_post_meta( $get_post_id, 'sm_status', 'on' );
        } else {
            $post_args = array(
                'ID'          => $get_post_id,
                'post_status' => 'draft',
                'post_type'   => self::min_max_quantity_post_type,
            );
            $post_update = wp_update_post( $post_args );
            update_post_meta( $get_post_id, 'sm_status', 'off' );
        }
        if ( !empty( $post_update ) ) {
            echo esc_html__( 'Rule status changed successfully.', 'min-and-max-quantity-for-woocommerce' );
        } else {
            echo esc_html__( 'Something went wrong', 'min-and-max-quantity-for-woocommerce' );
        }
        wp_die();
    }

    /**
     * Save all the custom messages
     *
     * @param array $custom_messages_array
     *
     * @return bool
     */
    public function mmqw_custom_messages_save( $custom_messages_array = array() ) {
        if ( empty( $custom_messages_array ) ) {
            return false;
        }
        $min_order_quantity_reached = ( !empty( $custom_messages_array['min_order_quantity_reached'] ) ? sanitize_textarea_field( wp_unslash( $custom_messages_array['min_order_quantity_reached'] ) ) : '' );
        $max_order_quantity_exceeded = ( !empty( $custom_messages_array['max_order_quantity_exceeded'] ) ? sanitize_textarea_field( wp_unslash( $custom_messages_array['max_order_quantity_exceeded'] ) ) : '' );
        $min_order_value_reached = ( !empty( $custom_messages_array['min_order_value_reached'] ) ? sanitize_textarea_field( wp_unslash( $custom_messages_array['min_order_value_reached'] ) ) : '' );
        $max_order_value_exceeded = ( !empty( $custom_messages_array['max_order_value_exceeded'] ) ? sanitize_textarea_field( wp_unslash( $custom_messages_array['max_order_value_exceeded'] ) ) : '' );
        $min_order_item_reached = ( !empty( $custom_messages_array['min_order_item_reached'] ) ? sanitize_textarea_field( wp_unslash( $custom_messages_array['min_order_item_reached'] ) ) : '' );
        $max_order_item_exceeded = ( !empty( $custom_messages_array['max_order_item_exceeded'] ) ? sanitize_textarea_field( wp_unslash( $custom_messages_array['max_order_item_exceeded'] ) ) : '' );
        update_option( 'min_order_quantity_reached', $min_order_quantity_reached );
        update_option( 'max_order_quantity_exceeded', $max_order_quantity_exceeded );
        update_option( 'min_order_value_reached', $min_order_value_reached );
        update_option( 'max_order_value_exceeded', $max_order_value_exceeded );
        update_option( 'min_order_item_reached', $min_order_item_reached );
        update_option( 'max_order_item_exceeded', $max_order_item_exceeded );
        return true;
    }

    /**
     * Save custom checkout page settings
     *
     * @param array $checkout_settings_array
     *
     * @return bool
     */
    public function mmqw_checkout_settings_save( $checkout_settings_array = array() ) {
        if ( empty( $checkout_settings_array ) ) {
            return false;
        }
        $min_order_quantity = ( !empty( $checkout_settings_array['min_order_quantity'] ) ? sanitize_text_field( wp_unslash( $checkout_settings_array['min_order_quantity'] ) ) : '' );
        $max_order_quantity = ( !empty( $checkout_settings_array['max_order_quantity'] ) ? sanitize_text_field( wp_unslash( $checkout_settings_array['max_order_quantity'] ) ) : '' );
        $min_order_value = ( !empty( $checkout_settings_array['min_order_value'] ) ? sanitize_text_field( wp_unslash( $checkout_settings_array['min_order_value'] ) ) : '' );
        $max_order_value = ( !empty( $checkout_settings_array['max_order_value'] ) ? sanitize_text_field( wp_unslash( $checkout_settings_array['max_order_value'] ) ) : '' );
        $min_items_quantity = ( !empty( $checkout_settings_array['min_items_quantity'] ) ? sanitize_text_field( wp_unslash( $checkout_settings_array['min_items_quantity'] ) ) : '' );
        $max_items_quantity = ( !empty( $checkout_settings_array['max_items_quantity'] ) ? sanitize_text_field( wp_unslash( $checkout_settings_array['max_items_quantity'] ) ) : '' );
        update_option( 'min_order_quantity', $min_order_quantity );
        update_option( 'max_order_quantity', $max_order_quantity );
        update_option( 'min_order_value', $min_order_value );
        update_option( 'max_order_value', $max_order_value );
        update_option( 'min_items_quantity', $min_items_quantity );
        update_option( 'max_items_quantity', $max_items_quantity );
        return true;
    }

    /**
     * Min/Max rule save message
     * 
     * @since 1.1.0
     */
    public function mmqw_updated_message( $message, $validation_msg ) {
        if ( empty( $message ) ) {
            return false;
        }
        if ( 'created' === $message ) {
            $updated_message = esc_html__( "Min/max rule has been created.", 'min-and-max-quantity-for-woocommerce' );
        } elseif ( 'saved' === $message ) {
            $updated_message = esc_html__( "Min/max rule has been updated.", 'min-and-max-quantity-for-woocommerce' );
        } elseif ( 'deleted' === $message ) {
            $updated_message = esc_html__( "Min/max rule has been deleted.", 'min-and-max-quantity-for-woocommerce' );
        } elseif ( 'duplicated' === $message ) {
            $updated_message = esc_html__( "Min/max rule has been duplicated.", 'min-and-max-quantity-for-woocommerce' );
        } elseif ( 'disabled' === $message ) {
            $updated_message = esc_html__( "Min/max rule has been disabled.", 'min-and-max-quantity-for-woocommerce' );
        } elseif ( 'enabled' === $message ) {
            $updated_message = esc_html__( "Min/max rule has been enabled.", 'min-and-max-quantity-for-woocommerce' );
        }
        if ( 'failed' === $message ) {
            $failed_messsage = esc_html__( "There was an error with saving data.", 'min-and-max-quantity-for-woocommerce' );
        } elseif ( 'nonce_check' === $message ) {
            $failed_messsage = esc_html__( "There was an error with security check.", 'min-and-max-quantity-for-woocommerce' );
        }
        if ( 'validated' === $message ) {
            $validated_messsage = esc_html( $validation_msg );
        }
        if ( !empty( $updated_message ) ) {
            echo sprintf( '<div id="message" class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( $updated_message ) );
            return false;
        }
        if ( !empty( $failed_messsage ) ) {
            echo sprintf( '<div id="message" class="notice notice-error is-dismissible"><p>%s</p></div>', esc_html( $failed_messsage ) );
            return false;
        }
        if ( !empty( $validated_messsage ) ) {
            echo sprintf( '<div id="message" class="notice notice-error is-dismissible"><p>%s</p></div>', esc_html( $validated_messsage ) );
            return false;
        }
    }

    /**
     * Min/Max add new group ajax callback
     * 
     * @since 1.1.0
     */
    public function mmqw_add_new_group_html_ajax() {
        //Check ajax nonce reference
        check_ajax_referer( 'mmqw_nonce', 'security' );
        $minVal = 113241;
        $maxVal = 999999;
        $random_number = wp_rand( $minVal, $maxVal );
        $mmqw_total_groups_no = filter_input( INPUT_POST, 'mmqw_total_groups_no', FILTER_SANITIZE_NUMBER_INT );
        $mmqw_post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
        $allowed_tooltip_html = wp_kses_allowed_html( 'post' )['span'];
        ?>
						<div id="mmqw-rules-group-<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>" class="mmqw-rules-group-main">
							<input type="hidden" name="mmqw_group[<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>][mmqw_group_id]" value="<?php 
        echo esc_attr( $mmqw_post_id . '_' . $mmqw_total_groups_no . '_' . $random_number );
        ?>" />
							<div class="mmqw-select">
								<input type="checkbox" name="mmqw_minus" class="mmqw-select-opt">
							</div>
							<div class="mmqw-group-status">
								<div class="switch_status_div">
									<label class="switch switch_in_pricing_rules">
										<input type="checkbox" name="mmqw_group[<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>][mmqw_group_status]" value="on" checked="">
										<div class="slider round"></div>
									</label>
									<?php 
        echo wp_kses( wc_help_tip( esc_html__( 'You can turn off this button, if you do not need to apply this group\'s min max rules.', 'min-and-max-quantity-for-woocommerce' ) ), array(
            'span' => $allowed_tooltip_html,
        ) );
        ?>
								</div>
							</div>
							<div class="mmqw-rules-group-title sub-title">
								<h2>#<?php 
        esc_html_e( $mmqw_total_groups_no, 'min-and-max-quantity-for-woocommerce' );
        ?></h2>
							</div>
							<div class="mmqw-rules-group-body">
								<div class="shipping-method-rules mmqw-conditions-section">
									<div class="sub-title">
										<div class="mmqw-conditions-title">
											<h2><?php 
        esc_html_e( 'Conditions', 'min-and-max-quantity-for-woocommerce' );
        ?></h2>
											<div class="tap">
												<a class="button button-primary button-large mmqw-add-new-rule" href="javascript:void(0);">
													<?php 
        esc_html_e( '+ Add Rule', 'min-and-max-quantity-for-woocommerce' );
        ?>
												</a>
											</div>
											<?php 
        ?>
														<div class="mmqw-rule-condition">
															<p class="mmqw-conditions-description">
																<?php 
        esc_html_e( 'below', 'min-and-max-quantity-for-woocommerce' );
        ?>
															</p>
															<select name="mmqw_group[<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>][mmqw_rule_condition_type]" class="mmqw_rule_condition_type">
																<option value="any">
																	<?php 
        esc_html_e( 'Any', 'min-and-max-quantity-for-woocommerce' );
        ?>
																</option>
																<option value="all_in_pro">
																	<?php 
        esc_html_e( 'All 🔒', 'min-and-max-quantity-for-woocommerce' );
        ?>
																</option>
															</select>
															<p class="mmqw-conditions-description">
																<?php 
        esc_html_e( 'rule match', 'min-and-max-quantity-for-woocommerce' );
        ?>
															</p>
														</div>
													<?php 
        ?>
										</div>
									</div>
									<div class="tap">
										<table id="tbl-min-max-rules-<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>" class="tbl_product_fee table-outer tap-cas form-table shipping-method-table">
											<tbody>
												<tr id="mmqw_group_<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>_row_1" valign="top">
													<th class="titledesc th_mmqw_rule_condition" scope="row">
														<select id="mmqw_qroup_<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>_conditions_condition_1" group-number="<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>" class="mmqw_rule_condition" rel-id="0" name="mmqw_group[<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>][mmqw_rule][0][mmqw_rule_condition]">
															<optgroup label="<?php 
        esc_attr_e( 'Product Specific', 'min-and-max-quantity-for-woocommerce' );
        ?>">
																<option value="product">
																	<?php 
        esc_html_e( 'Product', 'min-and-max-quantity-for-woocommerce' );
        ?>
																</option>
																<option value="variable_product">
																	<?php 
        esc_html_e( 'Variable Product', 'min-and-max-quantity-for-woocommerce' );
        ?>
																</option>
																<option value="category">
																	<?php 
        esc_html_e( 'Category', 'min-and-max-quantity-for-woocommerce' );
        ?>
																</option>

																<?php 
        ?>
																				<option value="total_sales_in_pro">
																					<?php 
        esc_html_e( 'Total Sales 🔒', 'min-and-max-quantity-for-woocommerce' );
        ?>
																				</option>
																				<option value="stock_quantity_in_pro">
																					<?php 
        esc_html_e( 'Stock Quantity 🔒', 'min-and-max-quantity-for-woocommerce' );
        ?>
																				</option>
																				<option value="sale_price_in_pro">
																					<?php 
        esc_html_e( 'Sale Price 🔒', 'min-and-max-quantity-for-woocommerce' );
        ?>
																				</option>
																				<option value="product_age_in_pro">
																					<?php 
        esc_html_e( 'Product Age 🔒', 'min-and-max-quantity-for-woocommerce' );
        ?>
																				</option>
																				<option value="best_sellers_in_pro">
																					<?php 
        esc_html_e( 'Best Sellers 🔒', 'min-and-max-quantity-for-woocommerce' );
        ?>
																				</option>
																				<option value="product_attributes_in_pro">
																					<?php 
        esc_html_e( 'Product Attributes 🔒', 'min-and-max-quantity-for-woocommerce' );
        ?>
																				</option>
																	<?php 
        ?>
															</optgroup>
															<optgroup label="<?php 
        esc_attr_e( 'Location Specific', 'min-and-max-quantity-for-woocommerce' );
        ?>">
																<option value="country">
																	<?php 
        esc_html_e( 'Country', 'min-and-max-quantity-for-woocommerce' );
        ?>
																</option>
															</optgroup>
															<?php 
        ?>
																			<optgroup label="<?php 
        esc_attr_e( 'User Specific', 'min-and-max-quantity-for-woocommerce' );
        ?>">
																				<option value="user_role_in_pro">
																					<?php 
        esc_html_e( 'User Role 🔒', 'min-and-max-quantity-for-woocommerce' );
        ?>
																				</option>
																				<option value="user_in_pro">
																					<?php 
        esc_html_e( 'User 🔒', 'min-and-max-quantity-for-woocommerce' );
        ?>
																				</option>
																			</optgroup>
																			<optgroup label="<?php 
        esc_attr_e( 'Limit Section', 'min-and-max-quantity-for-woocommerce' );
        ?>">
																				<option value="limit_section_time_frame_in_pro">
																					<?php 
        esc_html_e( 'Time Frame 🔒', 'min-and-max-quantity-for-woocommerce' );
        ?>
																				</option>
																			</optgroup>
																			<optgroup label="<?php 
        esc_attr_e( 'Cart Specific', 'min-and-max-quantity-for-woocommerce' );
        ?>">
																				<option value="cart_coupon_in_pro">
																					<?php 
        esc_html_e( 'Cart Coupon 🔒', 'min-and-max-quantity-for-woocommerce' );
        ?>
																				</option>
																				<option value="shipping_zone_in_pro">
																					<?php 
        esc_html_e( 'Shipping Zone 🔒', 'min-and-max-quantity-for-woocommerce' );
        ?>
																				</option>
																				<option value="shipping_method_in_pro">
																					<?php 
        esc_html_e( 'Shipping Method 🔒', 'min-and-max-quantity-for-woocommerce' );
        ?>
																				</option>
																			</optgroup>
																<?php 
        ?>
														</select>
													</th>
													<td class="select_condition_for_in_notin">
														<select name="mmqw_group[<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>][mmqw_rule][0][mmqw_rule_condition_is]" class="mmqw_rule_condition_is mmqw_rule_group_<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>_condition_is_0" id="mmqw_qroup_<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>_conditions_condition_is_0">
															<option value="is_equal_to">
																<?php 
        esc_html_e( 'Equal to ( = )', 'min-and-max-quantity-for-woocommerce' );
        ?>
															</option>
															<option value="not_in">
																<?php 
        esc_html_e( 'Not Equal to ( != )', 'min-and-max-quantity-for-woocommerce' );
        ?>
															</option>
														</select>
													</td>
													<td id="group_<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>_column_0" class="condition-value">
														<select rel-id="0" id="mmqw_group_<?php 
        echo esc_attr( $mmqw_total_groups_no, 'min-and-max-quantity-for-woocommerce' );
        ?>_rule_condition_value_0" name="mmqw_group[<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>][mmqw_rule][0][mmqw_rule_condition_value][]"
														class="mmqw_product mmqw_product_rule_condition_val multiselect2 min_max_select" multiple="multiple" data-placeholder="Please enter 3 or more characters">
														</select>
													</td>
													<td class="delete-td-row">
														<a id="fee-delete-field" class="delete-row" href="javascript:void(0);" title="Delete">
															<i class="dashicons dashicons-trash"></i>
														</a>
													</td>
												</tr>
											</tbody>
										</table>
										<input type="hidden" name="mmqw_total_rows_group_<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>" id="mmqw_total_rows_group_<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>" value="1">
									</div>
								</div>
								<div class="shipping-method-rules mmqw-actions-section" style="margin-top: 15px;">
									<div class="sub-title">
										<div class="mmqw-conditions-title">
											<h2><?php 
        esc_html_e( 'Actions', 'min-and-max-quantity-for-woocommerce' );
        ?></h2>
										</div>
									</div>
									<div class="tap">
										<table id="tbl-shipping-method" class="tbl_product_fee table-outer tap-cas form-table shipping-method-table">
											<tbody>
												<tr valign="top">
													<td>
														<label for="mmqw_group_<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>_min_qty">
															<?php 
        esc_html_e( 'Min Quantity', 'min-and-max-quantity-for-woocommerce' );
        ?>
														</label>
														<input type="number" name="mmqw_group[<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>][mmqw_group_min_qty]" class="text-class qty-class mmqw-min-qty-field" id="mmqw_group_<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>_min_qty" placeholder="Min quantity"
														value="" min="0">
														<?php 
        echo wp_kses( wc_help_tip( esc_html__( 'You can set a minimum product quantity to apply on product detail page and cart page.', 'min-and-max-quantity-for-woocommerce' ) ), array(
            'span' => $allowed_tooltip_html,
        ) );
        ?>
													</td>
													<td>
														<label for="mmqw_group_<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>_max_qty">
															<?php 
        esc_html_e( 'Max Quantity', 'min-and-max-quantity-for-woocommerce' );
        ?>
														</label>
														<input type="number" name="mmqw_group[<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>][mmqw_group_max_qty]" class="text-class qty-class mmqw-max-qty-field" id="mmqw_group_<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>_max_qty" placeholder="Max quantity"
														value="" min="0">
														<?php 
        echo wp_kses( wc_help_tip( esc_html__( 'You can set a maximum product quantity to apply on product detail page and cart page.', 'min-and-max-quantity-for-woocommerce' ) ), array(
            'span' => $allowed_tooltip_html,
        ) );
        ?>
													</td>
													<?php 
        ?>
																	<td>
																		<label for="mmqw_group_<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>_step_qty">
																			<?php 
        esc_html_e( 'Step Quantity', 'min-and-max-quantity-for-woocommerce' );
        ?>
																			<span class="mmqw-pro-label"></span>
																		</label>
																		<input type="number" name="mmqw_group[<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>][mmqw_group_step_qty_in_pro]" disabled class="text-class qty-class mmqw-step-qty-field" id="mmqw_group_<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>_step_qty" placeholder="Step quantity"
																		value="" min="0">
																		<?php 
        echo wp_kses( wc_help_tip( esc_html__( 'You can set a step quantity to apply on product detail page and cart page.', 'min-and-max-quantity-for-woocommerce' ) ), array(
            'span' => $allowed_tooltip_html,
        ) );
        ?>
																	</td>
																	<td>
																		<label for="mmqw_group_<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>_default_qty">
																			<?php 
        esc_html_e( 'Default Quantity', 'min-and-max-quantity-for-woocommerce' );
        ?>
																			<span class="mmqw-pro-label"></span>
																		</label>
																		<input type="number" name="mmqw_group[<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>][mmqw_group_default_qty_in_pro]" disabled class="text-class qty-class mmqw-default-qty-field" id="mmqw_group_<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>_default_qty"
																		placeholder="Default quantity" value="">
																		<?php 
        echo wp_kses( wc_help_tip( esc_html__( 'You can set a default quantity to apply on product detail page and cart page.', 'min-and-max-quantity-for-woocommerce' ) ), array(
            'span' => $allowed_tooltip_html,
        ) );
        ?>
																	</td>
																	<td>
																		<label for="mmqw_group_<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>_default_qty_selector">
																			<?php 
        esc_html_e( 'Quantity Selector', 'min-and-max-quantity-for-woocommerce' );
        ?>
																			<span class="mmqw-pro-label"></span>
																		</label>
																		<select name="mmqw_group[<?php 
        echo esc_attr( $mmqw_total_groups_no );
        ?>][mmqw_group_default_qty_selector_in_pro]" disabled class="mmqw_group_default_qty_selector">
																			<option value="text">
																				<?php 
        esc_html_e( 'Default Input', 'min-and-max-quantity-for-woocommerce' );
        ?>
																			</option>
																			<option value="select">
																				<?php 
        esc_html_e( 'Select Dropdown', 'min-and-max-quantity-for-woocommerce' );
        ?>
																			</option>
																			<option value="radio">
																				<?php 
        esc_html_e( 'Radio Button', 'min-and-max-quantity-for-woocommerce' );
        ?>
																			</option>

																		</select>
																	</td>
														<?php 
        ?>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						<?php 
        wp_die();
    }

    /**
     * Simple product id
     *
     * @param int $prd_id
     * @param object $_product
     * @param string $default_lang
     *
     * @return array $baselang_variation_product_ids
     *
     * @since  1.0.0
     */
    public function mmqw_simple_product_id(
        $baselang_simple_product_ids,
        $prd_id,
        $_product,
        $default_lang
    ) {
        global $sitepress;
        if ( $_product->is_type( 'simple' ) ) {
            if ( !empty( $sitepress ) ) {
                $defaultlang_simple_product_id = apply_filters(
                    'wpml_object_id',
                    $prd_id,
                    'product',
                    true,
                    $default_lang
                );
            } else {
                $defaultlang_simple_product_id = $prd_id;
            }
            $baselang_simple_product_ids[] = $defaultlang_simple_product_id;
        }
        return $baselang_simple_product_ids;
    }

    /**
     * Get product list
     *
     * @return string $html
     * @since  1.1.0
     *
     * @uses   mmqw_get_default_langugae_with_sitpress()
     *
     */
    public function mmqw_get_product_list(
        $group = '',
        $count = '',
        $selected = array(),
        $action = '',
        $json = false
    ) {
        $default_lang = $this->mmqw_get_default_langugae_with_sitpress();
        $post_in = '';
        $get_product_list_count = '';
        if ( 'edit' === $action ) {
            $post_in = $selected;
            $get_product_list_count = -1;
        } else {
            $post_in = '';
            $get_product_list_count = 10;
        }
        $get_all_products = new WP_Query(array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => $get_product_list_count,
            'fields'         => 'ids',
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'post__in'       => $post_in,
        ));
        $defaultlang_simple_product_ids = array();
        if ( isset( $get_all_products->posts ) && !empty( $get_all_products->posts ) ) {
            foreach ( $get_all_products->posts as $get_all_product ) {
                $_product = wc_get_product( $get_all_product );
                $defaultlang_simple_product_ids = $this->mmqw_simple_product_id(
                    $defaultlang_simple_product_ids,
                    $get_all_product,
                    $_product,
                    $default_lang
                );
            }
        }
        $html = '<select id="mmqw_group_' . esc_attr( $group ) . '_rule_condition_value_' . esc_attr( $count ) . '" rel-id="' . esc_attr( $count ) . '" name="mmqw_group[' . esc_attr( $group ) . '][mmqw_rule][' . esc_attr( $count ) . '][mmqw_rule_condition_value][]" class="min_max_select mmqw_rule_condition_value mmqw_rule_condition_value_' . esc_attr( $count ) . ' multiselect2 mmqw_product mmqw_product_rule_condition_val" multiple="multiple">';
        if ( isset( $defaultlang_simple_product_ids ) && !empty( $defaultlang_simple_product_ids ) ) {
            foreach ( $defaultlang_simple_product_ids as $new_product_id ) {
                $selected = array_map( 'intval', $selected );
                $selectedVal = ( is_array( $selected ) && !empty( $selected ) && in_array( $new_product_id, $selected, true ) ? 'selected=selected' : '' );
                if ( '' !== $selectedVal ) {
                    $html .= '<option value="' . esc_attr( $new_product_id ) . '" ' . esc_attr( $selectedVal ) . '>' . '#' . esc_html( $new_product_id ) . ' - ' . esc_html( get_the_title( $new_product_id ) ) . '</option>';
                }
            }
        }
        $html .= '</select>';
        if ( $json ) {
            return [];
        }
        return $html;
    }

    /**
     * Get variable product list
     *
     * @return string $html
     *
     * @since  1.1.0
     *
     * @uses   mmqw_get_default_langugae_with_sitpress()
     * @uses   wc_get_product()
     */
    public function mmqw_get_variable_product_list(
        $group = '',
        $count = '',
        $selected = array(),
        $action = '',
        $json = false
    ) {
        global $sitepress;
        $default_lang = $this->mmqw_get_default_langugae_with_sitpress();
        $post_in = '';
        $get_varible_product_list_count = '';
        if ( 'edit' === $action ) {
            $post_in = $selected;
            $get_varible_product_list_count = -1;
        } else {
            $post_in = '';
            $get_varible_product_list_count = 10;
        }
        $get_all_products = new WP_Query(array(
            'post_type'      => 'product_variation',
            'post_status'    => 'publish',
            'posts_per_page' => $get_varible_product_list_count,
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'post__in'       => $post_in,
        ));
        $html = '<select id="mmqw_group_' . esc_attr( $group ) . '_rule_condition_value_' . esc_attr( $count ) . '" rel-id="' . esc_attr( $count ) . '" name="mmqw_group[' . esc_attr( $group ) . '][mmqw_rule][' . esc_attr( $count ) . '][mmqw_rule_condition_value][]" class="min_max_select mmqw_rule_condition_value mmqw_rule_condition_value_' . esc_attr( $count ) . ' multiselect2 mmqw_product_variation mmqw_var_product_rule_condition_val" multiple="multiple">';
        if ( !empty( $get_all_products->posts ) ) {
            foreach ( $get_all_products->posts as $post ) {
                $_product = wc_get_product( $post->ID );
                if ( $_product instanceof WC_Product ) {
                    if ( !empty( $sitepress ) ) {
                        $new_product_id = apply_filters(
                            'wpml_object_id',
                            $post->ID,
                            'product',
                            true,
                            $default_lang
                        );
                    } else {
                        $new_product_id = $post->ID;
                    }
                    $selected = array_map( 'intval', $selected );
                    $selectedVal = ( is_array( $selected ) && !empty( $selected ) && in_array( $new_product_id, $selected, true ) ? 'selected=selected' : '' );
                    if ( '' !== $selectedVal ) {
                        $html .= '<option value="' . esc_attr( $new_product_id ) . '" ' . esc_attr( $selectedVal ) . '>' . '#' . esc_html( $new_product_id ) . ' - ' . esc_html( get_the_title( $new_product_id ) ) . '</option>';
                    }
                }
            }
        }
        $html .= '</select>';
        if ( $json ) {
            return [];
        }
        return $html;
    }

    /**
     * Get category list
     *
     * @return string $html
     * @since  1.1.0
     *
     * @uses   mmqw_get_default_langugae_with_sitpress()
     * @uses   get_categories()
     * @uses   get_term_by()
     *
     */
    public function mmqw_get_category_list(
        $group = '',
        $count = '',
        $selected = array(),
        $json = false
    ) {
        global $sitepress;
        $default_lang = $this->mmqw_get_default_langugae_with_sitpress();
        $filter_categories = [];
        $taxonomy = 'product_cat';
        $post_status = 'publish';
        $orderby = 'name';
        $hierarchical = 1;
        $empty = 0;
        $args = array(
            'post_type'      => 'product',
            'post_status'    => $post_status,
            'taxonomy'       => $taxonomy,
            'orderby'        => $orderby,
            'hierarchical'   => $hierarchical,
            'hide_empty'     => $empty,
            'posts_per_page' => -1,
        );
        $get_all_categories = get_categories( $args );
        $html = '<select id="mmqw_group_' . esc_attr( $group ) . '_rule_condition_value_' . esc_attr( $count ) . '" rel-id="' . esc_attr( $count ) . '" name="mmqw_group[' . esc_attr( $group ) . '][mmqw_rule][' . esc_attr( $count ) . '][mmqw_rule_condition_value][]" class="min_max_select mmqw_rule_condition_value mmqw_rule_condition_value_' . esc_attr( $count ) . ' multiselect2" multiple="multiple">';
        if ( isset( $get_all_categories ) && !empty( $get_all_categories ) ) {
            foreach ( $get_all_categories as $get_all_category ) {
                if ( !empty( $sitepress ) ) {
                    $new_cat_id = apply_filters(
                        'wpml_object_id',
                        $get_all_category->term_id,
                        'product_cat',
                        true,
                        $default_lang
                    );
                } else {
                    $new_cat_id = $get_all_category->term_id;
                }
                $selected = array_map( 'intval', $selected );
                $selectedVal = ( is_array( $selected ) && !empty( $selected ) && in_array( $new_cat_id, $selected, true ) ? 'selected=selected' : '' );
                $category = get_term_by( 'id', $new_cat_id, 'product_cat' );
                $parent_category = get_term_by( 'id', $category->parent, 'product_cat' );
                if ( $category->parent > 0 ) {
                    $html .= '<option value=' . esc_attr( $category->term_id ) . ' ' . esc_attr( $selectedVal ) . '>' . '#' . esc_html( $parent_category->name ) . '->' . esc_html( $category->name ) . '</option>';
                    $filter_categories[$category->term_id] = '#' . $parent_category->name . '->' . $category->name;
                } else {
                    $html .= '<option value=' . esc_attr( $category->term_id ) . ' ' . esc_attr( $selectedVal ) . '>' . esc_html( $category->name ) . '</option>';
                    $filter_categories[$category->term_id] = $category->name;
                }
            }
        }
        $html .= '</select>';
        if ( $json ) {
            return $this->mmqw_convert_array_to_json( $filter_categories );
        }
        return $html;
    }

    /**
     * Get country list
     *
     * @return string $html
     * @since  1.1.0
     *
     * @uses   WC_Countries() class
     *
     */
    public function mmqw_get_country_list(
        $group = '',
        $count = '',
        $selected = array(),
        $json = false
    ) {
        $countries_obj = new WC_Countries();
        $getCountries = $countries_obj->__get( 'countries' );
        $html = '<select id="mmqw_group_' . esc_attr( $group ) . '_rule_condition_value_' . esc_attr( $count ) . '" rel-id="' . esc_attr( $count ) . '" name="mmqw_group[' . esc_attr( $group ) . '][mmqw_rule][' . esc_attr( $count ) . '][mmqw_rule_condition_value][]" class="min_max_select mmqw_rule_condition_value mmqw_rule_condition_value_' . esc_attr( $count ) . ' multiselect2" multiple="multiple">';
        if ( !empty( $getCountries ) ) {
            foreach ( $getCountries as $code => $country ) {
                $selectedVal = ( is_array( $selected ) && !empty( $selected ) && in_array( $code, $selected, true ) ? 'selected=selected' : '' );
                $html .= '<option value="' . esc_attr( $code ) . '" ' . esc_attr( $selectedVal ) . '>' . esc_html( $country ) . '</option>';
            }
        }
        $html .= '</select>';
        if ( $json ) {
            return $this->mmqw_convert_array_to_json( $getCountries );
        }
        return $html;
    }

    /**
     * Display textfield and multiselect dropdown based on conditions
     *
     * @return string $html
     * @since 1.1.0
     *
     * @uses  mmqw_get_product_list()
     * @uses  mmqw_get_variable_product_list()
     * @uses  mmqw_get_category_list()
     * @uses  mmqw_get_country_list()
     *
     */
    public function mmqw_rules_conditions_values_ajax() {
        $get_condition = filter_input( INPUT_GET, 'condition', FILTER_SANITIZE_STRING );
        $get_count = filter_input( INPUT_GET, 'count', FILTER_SANITIZE_NUMBER_INT );
        $get_group = filter_input( INPUT_GET, 'group', FILTER_SANITIZE_NUMBER_INT );
        $condition = ( isset( $get_condition ) ? sanitize_text_field( $get_condition ) : '' );
        $count = ( isset( $get_count ) ? sanitize_text_field( $get_count ) : '' );
        $group = ( isset( $get_group ) ? sanitize_text_field( $get_group ) : '' );
        $html = '';
        if ( 'product' === $condition ) {
            $html .= wp_json_encode( $this->mmqw_get_product_list(
                $group,
                $count,
                [],
                '',
                true
            ) );
        } elseif ( 'variable_product' === $condition ) {
            $html .= wp_json_encode( $this->mmqw_get_variable_product_list(
                $group,
                $count,
                [],
                '',
                true
            ) );
        } elseif ( 'category' === $condition ) {
            $html .= wp_json_encode( $this->mmqw_get_category_list(
                $group,
                $count,
                [],
                true
            ) );
        } elseif ( 'country' === $condition ) {
            $html .= wp_json_encode( $this->mmqw_get_country_list(
                $group,
                $count,
                [],
                true
            ) );
        }
        echo wp_kses( $html, Min_Max_Quantity_For_WooCommerce::mmqw_allowed_html_tags() );
        wp_die();
        // this is required to terminate immediately and return a proper response
    }

    /**
     * Get old layout rules data to migrate
     *
     * @return array $mmqw_group
     * @since 1.1.0
     *
     */
    public function mmqw_get_rules_data_to_migrate() {
        $mmqw_group = array();
        $minVal = 113241;
        $maxVal = 999999;
        $random_number = wp_rand( $minVal, $maxVal );
        $plugin_public = new MMQW_Min_Max_Quantity_For_WooCommerce_Public($this->plugin_name, $this->version);
        $default_lang = $plugin_public->mmqw_get_default_langugae_with_sitpress();
        $get_all_sm = $plugin_public->mmqw_get_all_min_max_rules();
        if ( isset( $get_all_sm ) && !empty( $get_all_sm ) ) {
            foreach ( $get_all_sm as $rule ) {
                $mm_rule_id = $rule->ID;
                if ( !empty( $sitepress ) ) {
                    $sm_post_id = apply_filters(
                        'wpml_object_id',
                        $mm_rule_id,
                        'wc_mmqw',
                        true,
                        $default_lang
                    );
                } else {
                    $sm_post_id = $mm_rule_id;
                }
                if ( !empty( $sitepress ) ) {
                    if ( version_compare( ICL_SITEPRESS_VERSION, '3.2', '>=' ) ) {
                        $language_information = apply_filters( 'wpml_post_language_details', null, $sm_post_id );
                    } else {
                        $language_information = wpml_get_language_information( $sm_post_id );
                    }
                    $post_id_language_code = $language_information['language_code'];
                } else {
                    $post_id_language_code = $plugin_public->mmqw_get_default_langugae_with_sitpress();
                }
                /** Check the rule language and set the Min/Max quantity argument as per the rules added by the admin */
                if ( $post_id_language_code === $default_lang ) {
                    $cost_on_product_status = get_post_meta( $sm_post_id, 'cost_on_product_status', true );
                    $cost_on_product_variation_status = get_post_meta( $sm_post_id, 'cost_on_product_variation_status', true );
                    $cost_on_category_status = get_post_meta( $sm_post_id, 'cost_on_category_status', true );
                    $cost_on_country_status = get_post_meta( $sm_post_id, 'cost_on_country_status', true );
                    $sm_metabox_ap_product = get_post_meta( $sm_post_id, 'sm_metabox_ap_product', true );
                    if ( is_serialized( $sm_metabox_ap_product ) ) {
                        $sm_metabox_ap_product = maybe_unserialize( $sm_metabox_ap_product );
                    } else {
                        $sm_metabox_ap_product = $sm_metabox_ap_product;
                    }
                    $sm_metabox_ap_product_variation = get_post_meta( $sm_post_id, 'sm_metabox_ap_product_variation', true );
                    if ( is_serialized( $sm_metabox_ap_product_variation ) ) {
                        $sm_metabox_ap_product_variation = maybe_unserialize( $sm_metabox_ap_product_variation );
                    } else {
                        $sm_metabox_ap_product_variation = $sm_metabox_ap_product_variation;
                    }
                    $sm_metabox_ap_category = get_post_meta( $sm_post_id, 'sm_metabox_ap_category', true );
                    if ( is_serialized( $sm_metabox_ap_category ) ) {
                        $sm_metabox_ap_category = maybe_unserialize( $sm_metabox_ap_category );
                    } else {
                        $sm_metabox_ap_category = $sm_metabox_ap_category;
                    }
                    $sm_metabox_ap_country = get_post_meta( $sm_post_id, 'sm_metabox_ap_country', true );
                    if ( is_serialized( $sm_metabox_ap_country ) ) {
                        $sm_metabox_ap_country = maybe_unserialize( $sm_metabox_ap_country );
                    } else {
                        $sm_metabox_ap_country = $sm_metabox_ap_country;
                    }
                    $filled_arr = array();
                    if ( !empty( $sm_metabox_ap_product ) && is_array( $sm_metabox_ap_product ) ) {
                        foreach ( $sm_metabox_ap_product as $app_arr ) {
                            if ( !empty( $app_arr ) || '' !== $app_arr ) {
                                if ( '' !== $app_arr['ap_fees_products'] && ('' !== $app_arr['ap_fees_ap_prd_min_qty'] || '' !== $app_arr['ap_fees_ap_prd_max_qty']) ) {
                                    $filled_arr[] = $app_arr;
                                }
                            }
                        }
                    }
                    //check APR exist
                    if ( isset( $filled_arr ) && !empty( $filled_arr ) ) {
                        foreach ( $filled_arr as $key => $productfees ) {
                            $fees_ap_fees_products = ( isset( $productfees['ap_fees_products'] ) ? $productfees['ap_fees_products'] : '' );
                            $ap_fees_ap_min_qty = ( isset( $productfees['ap_fees_ap_prd_min_qty'] ) ? $productfees['ap_fees_ap_prd_min_qty'] : '' );
                            $ap_fees_ap_max_qty = ( isset( $productfees['ap_fees_ap_prd_max_qty'] ) ? $productfees['ap_fees_ap_prd_max_qty'] : '' );
                            $mmqw_group[$sm_post_id][] = $this->mmqw_save_old_layout_plugin_settings(
                                $sm_post_id . '_' . $key . '_' . $random_number,
                                $cost_on_product_status,
                                'product',
                                'is_equal_to',
                                $fees_ap_fees_products,
                                $ap_fees_ap_min_qty,
                                $ap_fees_ap_max_qty
                            );
                        }
                    }
                    $filled_product_variation = array();
                    if ( !empty( $sm_metabox_ap_product_variation ) && is_array( $sm_metabox_ap_product_variation ) ) {
                        foreach ( $sm_metabox_ap_product_variation as $apcat_arr ) {
                            if ( !empty( $apcat_arr ) || $apcat_arr !== '' ) {
                                if ( $apcat_arr['ap_fees_product_variation'] !== '' && ($apcat_arr['ap_fees_ap_product_variation_min_qty'] !== '' || $apcat_arr['ap_fees_ap_product_variation_max_qty'] !== '') ) {
                                    $filled_product_variation[] = $apcat_arr;
                                }
                            }
                        }
                    }
                    //check APR exist
                    if ( isset( $filled_product_variation ) && !empty( $filled_product_variation ) ) {
                        foreach ( $filled_product_variation as $key => $productfees ) {
                            $fees_ap_fees_product_variation = ( isset( $productfees['ap_fees_product_variation'] ) ? $productfees['ap_fees_product_variation'] : '' );
                            $ap_fees_ap_product_variation_min_qty = ( isset( $productfees['ap_fees_ap_product_variation_min_qty'] ) ? $productfees['ap_fees_ap_product_variation_min_qty'] : '' );
                            $ap_fees_ap_product_variation_max_qty = ( isset( $productfees['ap_fees_ap_product_variation_max_qty'] ) ? $productfees['ap_fees_ap_product_variation_max_qty'] : '' );
                            $mmqw_group[$sm_post_id][] = $this->mmqw_save_old_layout_plugin_settings(
                                $sm_post_id . '_' . $key . '_' . $random_number,
                                $cost_on_product_variation_status,
                                'variable_product',
                                'is_equal_to',
                                $fees_ap_fees_product_variation,
                                $ap_fees_ap_product_variation_min_qty,
                                $ap_fees_ap_product_variation_max_qty
                            );
                        }
                    }
                    $filled_category_arr = array();
                    if ( !empty( $sm_metabox_ap_category ) && is_array( $sm_metabox_ap_category ) ) {
                        foreach ( $sm_metabox_ap_category as $apcat_arr ) {
                            if ( !empty( $apcat_arr ) || '' !== $apcat_arr ) {
                                if ( '' !== $apcat_arr['ap_fees_categories'] && ('' !== $apcat_arr['ap_fees_ap_cat_min_qty'] || '' !== $apcat_arr['ap_fees_ap_cat_max_qty']) ) {
                                    $filled_category_arr[] = $apcat_arr;
                                }
                            }
                        }
                    }
                    //check APR exist
                    if ( isset( $filled_category_arr ) && !empty( $filled_category_arr ) ) {
                        foreach ( $filled_category_arr as $key => $productfees ) {
                            $fees_ap_fees_categories = ( isset( $productfees['ap_fees_categories'] ) ? $productfees['ap_fees_categories'] : '' );
                            $ap_fees_ap_cat_min_qty = ( isset( $productfees['ap_fees_ap_cat_min_qty'] ) ? $productfees['ap_fees_ap_cat_min_qty'] : '' );
                            $ap_fees_ap_cat_max_qty = ( isset( $productfees['ap_fees_ap_cat_max_qty'] ) ? $productfees['ap_fees_ap_cat_max_qty'] : '' );
                            $mmqw_group[$sm_post_id][] = $this->mmqw_save_old_layout_plugin_settings(
                                $sm_post_id . '_' . $key . '_' . $random_number,
                                $cost_on_category_status,
                                'category',
                                'is_equal_to',
                                $fees_ap_fees_categories,
                                $ap_fees_ap_cat_min_qty,
                                $ap_fees_ap_cat_max_qty
                            );
                        }
                    }
                    $filled_country = array();
                    if ( !empty( $sm_metabox_ap_country ) && is_array( $sm_metabox_ap_country ) ) {
                        foreach ( $sm_metabox_ap_country as $apcat_arr ) {
                            if ( !empty( $apcat_arr ) || $apcat_arr !== '' ) {
                                if ( $apcat_arr['ap_fees_country'] !== '' && ($apcat_arr['ap_fees_ap_country_min_subtotal'] !== '' || $apcat_arr['ap_fees_ap_country_max_subtotal'] !== '') ) {
                                    $filled_country[] = $apcat_arr;
                                }
                            }
                        }
                    }
                    //check APR exist
                    if ( isset( $filled_country ) && !empty( $filled_country ) ) {
                        foreach ( $filled_country as $key => $productfees ) {
                            $fees_ap_fees_country = ( isset( $productfees['ap_fees_country'] ) ? $productfees['ap_fees_country'] : '' );
                            $ap_fees_ap_country_min_qty = ( isset( $productfees['ap_fees_ap_country_min_subtotal'] ) ? $productfees['ap_fees_ap_country_min_subtotal'] : '' );
                            $ap_fees_ap_country_max_qty = ( isset( $productfees['ap_fees_ap_country_max_subtotal'] ) ? $productfees['ap_fees_ap_country_max_subtotal'] : '' );
                            $mmqw_group[$sm_post_id][] = $this->mmqw_save_old_layout_plugin_settings(
                                $sm_post_id . '_' . $key . '_' . $random_number,
                                $cost_on_country_status,
                                'country',
                                'is_equal_to',
                                $fees_ap_fees_country,
                                $ap_fees_ap_country_min_qty,
                                $ap_fees_ap_country_max_qty
                            );
                        }
                    }
                }
            }
        }
        return $mmqw_group;
    }

    /**
     * Preprare array of old layout plugin settings to new layout
     *
     * @return array $mmqw_group
     * @since 1.1.0
     *
     */
    public function mmqw_save_old_layout_plugin_settings(
        $group_id,
        $group_status,
        $rule_condition,
        $rule_condition_is,
        $rule_condition_value,
        $min_qty,
        $max_qty
    ) {
        $mmqw_group = array(
            'mmqw_group_id'      => $group_id,
            'mmqw_group_status'  => $group_status,
            'mmqw_rule'          => array(array(
                'mmqw_rule_condition'       => $rule_condition,
                'mmqw_rule_condition_is'    => $rule_condition_is,
                'mmqw_rule_condition_value' => $rule_condition_value,
            )),
            'mmqw_group_min_qty' => $min_qty,
            'mmqw_group_max_qty' => $max_qty,
        );
        return $mmqw_group;
    }

    /**
     * Migrate old layout plugin data to new layout on admin init
     * 
     * @since 1.1.0
     *
     */
    public function mmqw_migrate_plugin_settings_on_init() {
        $mmqw_plugin_data_migrated = get_option( 'mmqw_plugin_data_migrated' );
        if ( !$mmqw_plugin_data_migrated && 'yes' !== $mmqw_plugin_data_migrated || 'no' === $mmqw_plugin_data_migrated ) {
            $plugin_rules = $this->mmqw_get_rules_data_to_migrate();
            if ( !empty( $plugin_rules ) && is_array( $plugin_rules ) ) {
                foreach ( $plugin_rules as $rule_id => $plugin_rule ) {
                    $mmqw_total_groups = count( $plugin_rule );
                    update_post_meta( $rule_id, 'mmqw_total_groups', $mmqw_total_groups );
                    update_post_meta( $rule_id, 'mmqw_rule_groups', $plugin_rule );
                }
            }
            // Update option after data migrated
            update_option( 'mmqw_plugin_data_migrated', 'yes' );
        }
    }

    /**
     * Migrate old layout plugin data to new layout on plugin update
     * 
     * @since 1.1.0
     *
     */
    public function mmqw_migrate_plugin_settings_on_update( $upgrader_object, $options ) {
        $mmqw_plugin_data_migrated = get_option( 'mmqw_plugin_data_migrated' );
        if ( !$mmqw_plugin_data_migrated && 'yes' !== $mmqw_plugin_data_migrated || 'no' === $mmqw_plugin_data_migrated ) {
            $current_plugin_path_name = MMQW_PLUGIN_BASENAME;
            $options_action = ( isset( $options['action'] ) && !empty( $options['action'] ) ? $options['action'] : '' );
            $options_type = ( isset( $options['type'] ) && !empty( $options['type'] ) ? $options['type'] : '' );
            if ( $options_action === 'update' && $options_type === 'plugin' ) {
                foreach ( $options['plugins'] as $each_plugin ) {
                    if ( $each_plugin === $current_plugin_path_name ) {
                        $plugin_rules = $this->mmqw_get_rules_data_to_migrate();
                        if ( !empty( $plugin_rules ) && is_array( $plugin_rules ) ) {
                            foreach ( $plugin_rules as $rule_id => $plugin_rule ) {
                                $mmqw_total_groups = count( $plugin_rule );
                                update_post_meta( $rule_id, 'mmqw_total_groups', $mmqw_total_groups );
                                update_post_meta( $rule_id, 'mmqw_rule_groups', $plugin_rule );
                            }
                        }
                        // Update option after data migrated
                        update_option( 'mmqw_plugin_data_migrated', 'yes' );
                    }
                }
            }
        }
    }

    /**
     * Get and save plugin setup wizard data
     * 
     * @since 2.0.0
     * 
     */
    public function mmqw_plugin_setup_wizard_submit() {
        check_ajax_referer( 'wizard_ajax_nonce', 'nonce' );
        $survey_list = filter_input( INPUT_GET, 'survey_list', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( !empty( $survey_list ) && 'Select One' !== $survey_list ) {
            update_option( 'mmqw_where_hear_about_us', $survey_list );
        }
        wp_die();
    }

    /**
     * Send setup wizard data to sendinblue
     * 
     * @since 2.0.0
     * 
     */
    public function mmqw_send_wizard_data_after_plugin_activation() {
        $send_wizard_data = filter_input( INPUT_GET, 'send-wizard-data', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( isset( $send_wizard_data ) && !empty( $send_wizard_data ) ) {
            if ( !get_option( 'mmqw_data_submited_in_sendiblue' ) ) {
                $mmqw_where_hear = get_option( 'mmqw_where_hear_about_us' );
                $get_user = mmqw_fs()->get_user();
                $data_insert_array = array();
                if ( isset( $get_user ) && !empty( $get_user ) ) {
                    $data_insert_array = array(
                        'user_email'              => $get_user->email,
                        'ACQUISITION_SURVEY_LIST' => $mmqw_where_hear,
                    );
                }
                $feedback_api_url = MMQW_STORE_URL . 'wp-json/dotstore-sendinblue-data/v2/dotstore-sendinblue-data?' . wp_rand();
                $query_url = $feedback_api_url . '&' . http_build_query( $data_insert_array );
                if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
                    $response = vip_safe_wp_remote_get(
                        $query_url,
                        3,
                        1,
                        20
                    );
                } else {
                    $response = wp_remote_get( $query_url );
                    // phpcs:ignore
                }
                if ( !is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
                    update_option( 'mmqw_data_submited_in_sendiblue', '1' );
                    delete_option( 'mmqw_where_hear_about_us' );
                }
            }
        }
    }

    /**
     * Get dynamic promotional bar of plugin
     *
     * @param   String  $plugin_slug  slug of the plugin added in the site option
     * @since   2.0.0
     * 
     * @return  null
     */
    public function mmqw_get_promotional_bar( $plugin_slug = '' ) {
        $promotional_bar_upi_url = MMQW_STORE_URL . 'wp-json/dpb-promotional-banner/v2/dpb-promotional-banner?' . wp_rand();
        $promotional_banner_request = wp_remote_get( $promotional_bar_upi_url );
        //phpcs:ignore
        if ( empty( $promotional_banner_request->errors ) ) {
            $promotional_banner_request_body = $promotional_banner_request['body'];
            $promotional_banner_request_body = json_decode( $promotional_banner_request_body, true );
            echo '<div class="dynamicbar_wrapper">';
            if ( !empty( $promotional_banner_request_body ) && is_array( $promotional_banner_request_body ) ) {
                foreach ( $promotional_banner_request_body as $promotional_banner_request_body_data ) {
                    $promotional_banner_id = $promotional_banner_request_body_data['promotional_banner_id'];
                    $promotional_banner_cookie = $promotional_banner_request_body_data['promotional_banner_cookie'];
                    $promotional_banner_image = $promotional_banner_request_body_data['promotional_banner_image'];
                    $promotional_banner_description = $promotional_banner_request_body_data['promotional_banner_description'];
                    $promotional_banner_button_group = $promotional_banner_request_body_data['promotional_banner_button_group'];
                    $dpb_schedule_campaign_type = $promotional_banner_request_body_data['dpb_schedule_campaign_type'];
                    $promotional_banner_target_audience = $promotional_banner_request_body_data['promotional_banner_target_audience'];
                    if ( !empty( $promotional_banner_target_audience ) ) {
                        $plugin_keys = array();
                        if ( is_array( $promotional_banner_target_audience ) ) {
                            foreach ( $promotional_banner_target_audience as $list ) {
                                $plugin_keys[] = $list['value'];
                            }
                        } else {
                            $plugin_keys[] = $promotional_banner_target_audience['value'];
                        }
                        $display_banner_flag = false;
                        if ( in_array( 'all_customers', $plugin_keys, true ) || in_array( $plugin_slug, $plugin_keys, true ) ) {
                            $display_banner_flag = true;
                        }
                    }
                    if ( true === $display_banner_flag ) {
                        if ( 'default' === $dpb_schedule_campaign_type ) {
                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $banner_cookie_visible_once = filter_input( INPUT_COOKIE, 'banner_show_once_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $flag = false;
                            if ( empty( $banner_cookie_show ) && empty( $banner_cookie_visible_once ) ) {
                                setcookie( 'banner_show_' . $promotional_banner_cookie, 'yes', time() + 86400 * 7 );
                                //phpcs:ignore
                                setcookie( 'banner_show_once_' . $promotional_banner_cookie, 'yes' );
                                //phpcs:ignore
                                $flag = true;
                            }
                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            if ( !empty( $banner_cookie_show ) || true === $flag ) {
                                $banner_cookie = filter_input( INPUT_COOKIE, 'banner_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                                $banner_cookie = ( isset( $banner_cookie ) ? $banner_cookie : '' );
                                if ( empty( $banner_cookie ) && 'yes' !== $banner_cookie ) {
                                    ?>
																										<div class="dpb-popup <?php 
                                    echo ( isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner' );
                                    ?>">
																											<?php 
                                    if ( !empty( $promotional_banner_image ) ) {
                                        ?>
																														<img src="<?php 
                                        echo esc_url( $promotional_banner_image );
                                        ?>"/>
																														<?php 
                                    }
                                    ?>
																											<div class="dpb-popup-meta">
																												<p>
																													<?php 
                                    echo wp_kses_post( str_replace( array('<p>', '</p>'), '', $promotional_banner_description ) );
                                    if ( !empty( $promotional_banner_button_group ) ) {
                                        foreach ( $promotional_banner_button_group as $promotional_banner_button_group_data ) {
                                            ?>
																																			<a href="<?php 
                                            echo esc_url( $promotional_banner_button_group_data['promotional_banner_button_link'] );
                                            ?>" target="_blank"><?php 
                                            echo esc_html( $promotional_banner_button_group_data['promotional_banner_button_text'] );
                                            ?></a>
																																			<?php 
                                        }
                                    }
                                    ?>
																												</p>
																											</div>
																											<a href="javascript:void(0);" data-bar-id="<?php 
                                    echo esc_attr( $promotional_banner_id );
                                    ?>" data-popup-name="<?php 
                                    echo ( isset( $promotional_banner_cookie ) ? esc_attr( $promotional_banner_cookie ) : 'default-banner' );
                                    ?>" class="dpbpop-close"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><path id="Icon_material-close" data-name="Icon material-close" d="M17.5,8.507,16.493,7.5,12.5,11.493,8.507,7.5,7.5,8.507,11.493,12.5,7.5,16.493,8.507,17.5,12.5,13.507,16.493,17.5,17.5,16.493,13.507,12.5Z" transform="translate(-7.5 -7.5)" fill="#acacac"/></svg></a>
																										</div>
																										<?php 
                                }
                            }
                        } else {
                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $banner_cookie_visible_once = filter_input( INPUT_COOKIE, 'banner_show_once_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $flag = false;
                            if ( empty( $banner_cookie_show ) && empty( $banner_cookie_visible_once ) ) {
                                setcookie( 'banner_show_' . $promotional_banner_cookie, 'yes' );
                                //phpcs:ignore
                                setcookie( 'banner_show_once_' . $promotional_banner_cookie, 'yes' );
                                //phpcs:ignore
                                $flag = true;
                            }
                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            if ( !empty( $banner_cookie_show ) || true === $flag ) {
                                $banner_cookie = filter_input( INPUT_COOKIE, 'banner_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                                $banner_cookie = ( isset( $banner_cookie ) ? $banner_cookie : '' );
                                if ( empty( $banner_cookie ) && 'yes' !== $banner_cookie ) {
                                    ?>
																										<div class="dpb-popup <?php 
                                    echo ( isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner' );
                                    ?>">
																											<?php 
                                    if ( !empty( $promotional_banner_image ) ) {
                                        ?>
																															<img src="<?php 
                                        echo esc_url( $promotional_banner_image );
                                        ?>"/>
																														<?php 
                                    }
                                    ?>
																											<div class="dpb-popup-meta">
																												<p>
																													<?php 
                                    echo wp_kses_post( str_replace( array('<p>', '</p>'), '', $promotional_banner_description ) );
                                    if ( !empty( $promotional_banner_button_group ) ) {
                                        foreach ( $promotional_banner_button_group as $promotional_banner_button_group_data ) {
                                            ?>
																																			<a href="<?php 
                                            echo esc_url( $promotional_banner_button_group_data['promotional_banner_button_link'] );
                                            ?>" target="_blank"><?php 
                                            echo esc_html( $promotional_banner_button_group_data['promotional_banner_button_text'] );
                                            ?></a>
																																			<?php 
                                        }
                                    }
                                    ?>
																												</p>
																											</div>
																											<a href="javascript:void(0);" data-bar-id="<?php 
                                    echo esc_attr( $promotional_banner_id );
                                    ?>" data-popup-name="<?php 
                                    echo ( isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner' );
                                    ?>" class="dpbpop-close"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><path id="Icon_material-close" data-name="Icon material-close" d="M17.5,8.507,16.493,7.5,12.5,11.493,8.507,7.5,7.5,8.507,11.493,12.5,7.5,16.493,8.507,17.5,12.5,13.507,16.493,17.5,17.5,16.493,13.507,12.5Z" transform="translate(-7.5 -7.5)" fill="#acacac"/></svg></a>
																										</div>
																										<?php 
                                }
                            }
                        }
                    }
                }
            }
            echo '</div>';
        }
    }

}
