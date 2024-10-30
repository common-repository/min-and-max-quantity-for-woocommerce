<?php
// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Fired during plugin activation
 *
 * @link       http://www.multidots.com
 * @since      1.0.0
 *
 * @package    Min_Max_Quantity_For_WooCommerce
 * @subpackage Min_Max_Quantity_For_WooCommerce/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Min_Max_Quantity_For_WooCommerce
 * @subpackage Min_Max_Quantity_For_WooCommerce/includes
 * @author     thedotstore <hello@thedotstore.com>
 */
class Min_Max_Quantity_For_WooCommerce_Activator {
    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     *
     * @uses mmqw_data_migration_script()
     */
    public static function activate() {
        set_transient('_welcome_screen_mmqw_mode_activation_redirect_data', true, 30);
        add_option('mmqw_version', MMQW_PLUGIN_VERSION);

        if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')), true) && !is_plugin_active_for_network('woocommerce/woocommerce.php')) {
            wp_die("<strong>Minimum and Maximum Quantity for WooCommerce</strong> plugin requires <strong>WooCommerce</strong>. Return to <a href='" . esc_url(get_admin_url(null, 'plugins.php')) . "'>Plugins page</a>.");
        } else {
            update_option('chk_enable_logging', 'on');
        }
    }
}