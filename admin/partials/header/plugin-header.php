<?php

/**
 * If this file is called directly, abort.
 * 
 * @category HeaderTemplate
 * @package  MinimumMaximumQuantity
 * @author   theDotstore <support@thedotstore.com>
 * @license  GPL-2.0+ (http://www.gnu.org/licenses/gpl-2.0.txt)
 * @link     https://www.thedotstore.com/
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
global $mmqw_fs;
$version_label = '';
$plugin_slug = '';
$version_label = __( 'Free', 'min-and-max-quantity-for-woocommerce' );
$plugin_slug = 'basic_min_max_quantity';
$plugin_name = 'Min/Max Quantity';
$plugin_version = 'v' . MMQW_PLUGIN_VERSION;
$current_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
$min_max_rules_list = ( isset( $current_page ) && 'mmqw-rules-list' === $current_page ? 'active' : '' );
$min_max_checkout_settings = ( isset( $current_page ) && ('mmqw-checkout-settings' === $current_page || 'mmqw-manage-messages' === $current_page || 'mmqw-display-rules' === $current_page) ? 'active' : '' );
$min_max_general_settings = ( isset( $current_page ) && 'mmqw-checkout-settings' === $current_page ? 'active' : '' );
$min_max_get_started = ( isset( $current_page ) && 'mmqw-get-started' === $current_page ? 'active' : '' );
$mmqw_manage_messages = ( isset( $current_page ) && 'mmqw-manage-messages' === $current_page ? 'active' : '' );
$mmqw_display_rules = ( isset( $current_page ) && 'mmqw-display-rules' === $current_page ? 'active' : '' );
$mmqw_account_page = ( isset( $current_page ) && 'mmqw-rules-list-account' === $current_page ? 'active' : '' );
$mmqw_free_dashboard = ( isset( $current_page ) && 'mmqw-upgrade-dashboard' === $current_page ? 'active' : '' );
$mmqw_display_submenu = ( !empty( $min_max_checkout_settings ) && 'active' === $min_max_checkout_settings ? 'display:inline-block' : 'display:none' );
$admin_object = new MMQW_Min_Max_Quantity_For_WooCommerce_Admin('', '');
?>
<div id="dotsstoremain">
    <div class="all-pad">
        <?php 
$admin_object->mmqw_get_promotional_bar( $plugin_slug );
?>
        <hr class="wp-header-end" />
        <header class="dots-header">
            <div class="dots-plugin-details">
                <div class="dots-header-left">
                    <div class="dots-logo-main">
                        <img src="<?php 
echo esc_url( MMQW_PLUGIN_URL . 'admin/images/min-max-logo.png' );
?>">
                    </div>
                    <div class="plugin-name">
                        <div class="title"><?php 
esc_html_e( $plugin_name, 'min-and-max-quantity-for-woocommerce' );
?>
                        </div>
                    </div>
                    <span class="version-label <?php 
echo esc_attr( $plugin_slug );
?>"><?php 
esc_html_e( $version_label, 'min-and-max-quantity-for-woocommerce' );
?></span>
                    <span class="version-number"><?php 
echo esc_html__( $plugin_version, 'min-and-max-quantity-for-woocommerce' );
?></span>
                </div>
                <div class="dots-header-right">
                    <div class="button-dots">
                        <a target="_blank" href="<?php 
echo esc_url( 'http://www.thedotstore.com/support/' );
?>"><?php 
esc_html_e( 'Support', 'min-and-max-quantity-for-woocommerce' );
?></a>
                    </div>
                    <div class="button-dots">
                        <a target="_blank" href="<?php 
echo esc_url( 'https://www.thedotstore.com/feature-requests/' );
?>"><?php 
esc_html_e( 'Suggest', 'min-and-max-quantity-for-woocommerce' );
?></a>
                    </div>
                    <div class="button-dots <?php 
echo ( mmqw_fs()->is__premium_only() && mmqw_fs()->can_use_premium_code() ? '' : 'last-link-button' );
?>">
                        <a target="_blank" href="<?php 
echo esc_url( 'https://docs.thedotstore.com/collection/706-min-max-quantity' );
?>"><?php 
esc_html_e( 'Help', 'min-and-max-quantity-for-woocommerce' );
?></a>
                    </div>
                    <div class="button-dots">
                        <?php 
?>
                            <a class="dots-upgrade-btn" target="_blank" href="javascript:void(0);"><?php 
esc_html_e( 'Upgrade Now', 'min-and-max-quantity-for-woocommerce' );
?></a>
                            <?php 
?>
                    </div>
                </div>
            </div>
            <div class="dots-bottom-menu-main">
                <div class="dots-menu-main">
                    <nav>
                        <ul>
                            <li>
                                <a class="dotstore_plugin <?php 
echo esc_attr( $min_max_rules_list );
?>"
                                    href="<?php 
echo esc_url( add_query_arg( array(
    'page' => 'mmqw-rules-list',
), admin_url( 'admin.php' ) ) );
?>"><?php 
esc_html_e( 'Manage Rules', 'min-and-max-quantity-for-woocommerce' );
?></a>
                            </li>
                            <li>
                                <a class="dotstore_plugin <?php 
echo esc_attr( $min_max_checkout_settings );
?>"
                                    href="<?php 
echo esc_url( add_query_arg( array(
    'page' => 'mmqw-checkout-settings',
), admin_url( 'admin.php' ) ) );
?>"><?php 
esc_html_e( 'Settings', 'min-and-max-quantity-for-woocommerce' );
?></a>
                            </li>
                            <?php 
?>
                                <li>
                                    <a class="dotstore_plugin dots_get_premium <?php 
echo esc_attr( $mmqw_free_dashboard );
?>" href="<?php 
echo esc_url( add_query_arg( array(
    'page' => 'mmqw-upgrade-dashboard',
), admin_url( 'admin.php' ) ) );
?>"><?php 
esc_html_e( 'Get Premium', 'min-and-max-quantity-for-woocommerce' );
?></a>
                                </li>
                                <?php 
?>
                        </ul>
                    </nav>
                </div>
                <div class="dots-getting-started">
                    <a href="<?php 
echo esc_url( add_query_arg( array(
    'page' => 'mmqw-get-started',
), admin_url( 'admin.php' ) ) );
?>" class="<?php 
echo esc_attr( $min_max_get_started );
?>"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M12 4.75a7.25 7.25 0 100 14.5 7.25 7.25 0 000-14.5zM3.25 12a8.75 8.75 0 1117.5 0 8.75 8.75 0 01-17.5 0zM12 8.75a1.5 1.5 0 01.167 2.99c-.465.052-.917.44-.917 1.01V14h1.5v-.845A3 3 0 109 10.25h1.5a1.5 1.5 0 011.5-1.5zM11.25 15v1.5h1.5V15h-1.5z" fill="#a0a0a0"></path></svg></a>
                </div>
            </div>
        </header>
        <!-- Upgrade to pro popup -->
        <?php 
if ( !(mmqw_fs()->is__premium_only() && mmqw_fs()->can_use_premium_code()) ) {
    require_once MMQW_PLUGIN_PATH . 'admin/partials/dots-upgrade-popup.php';
}
?>
        <div class="dots-settings-inner-main">
            <div class="mmqw-section-left">
                <div class="dotstore-submenu-items" style="<?php 
echo esc_attr( $mmqw_display_submenu );
?>">
                    <ul>
                        <li><a class="<?php 
echo esc_attr( $min_max_general_settings );
?>"
                                href="<?php 
echo esc_url( add_query_arg( array(
    'page' => 'mmqw-checkout-settings',
), admin_url( 'admin.php' ) ) );
?>"><?php 
esc_html_e( 'General', 'min-and-max-quantity-for-woocommerce' );
?></a>
                        </li>
                        <li><a class="<?php 
echo esc_attr( $mmqw_manage_messages );
?>"
                                href="<?php 
echo esc_url( add_query_arg( array(
    'page' => 'mmqw-manage-messages',
), admin_url( 'admin.php' ) ) );
?>"><?php 
esc_html_e( 'Messages', 'min-and-max-quantity-for-woocommerce' );
?></a>
                        </li>
                        <li><a class="<?php 
echo esc_attr( $mmqw_display_rules );
?>"
                                href="<?php 
echo esc_url( add_query_arg( array(
    'page' => 'mmqw-display-rules',
), admin_url( 'admin.php' ) ) );
?>"><?php 
esc_html_e( 'Display Rules', 'min-and-max-quantity-for-woocommerce' );
?></a>
                        </li>
                    </ul>
                </div>