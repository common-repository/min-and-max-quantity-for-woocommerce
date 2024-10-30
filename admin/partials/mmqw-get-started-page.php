<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-header.php' );
?>
<div class="mmqw-main-table res-cl mmqw-get-started-table">
	<div class="dots-getting-started-main">
        <div class="getting-started-content">
            <span><?php esc_html_e( 'How to Get Started', 'min-and-max-quantity-for-woocommerce' ); ?></span>
            <h3><?php esc_html_e( 'Welcome to Min/Max Quantity Plugin', 'min-and-max-quantity-for-woocommerce' ); ?></h3>
            <p><?php esc_html_e( 'Thank you for choosing our top-rated WooCommerce Min/Max Quantity plugin. Our user-friendly interface gives you complete control over product quantities, ensuring a seamless shopping experience for your customers.', 'min-and-max-quantity-for-woocommerce' ); ?></p>
            <p>
                <?php 
                echo sprintf(
                    esc_html__('To help you get started, watch the quick tour video on the right. For more help, explore our help documents or visit our %s for detailed video tutorials.', 'min-and-max-quantity-for-woocommerce'),
                    '<a href="' . esc_url('https://www.youtube.com/@Dotstore16') . '" target="_blank">' . esc_html__('YouTube channel', 'min-and-max-quantity-for-woocommerce') . '</a>',
                );
                ?>
            </p>
            <div class="getting-started-actions">
                <a href="<?php echo esc_url(add_query_arg(array('page' => 'mmqw-rules-list'), admin_url('admin.php'))); ?>" class="quick-start"><?php esc_html_e( 'Manage Min/Max Quantity', 'min-and-max-quantity-for-woocommerce' ); ?><span class="dashicons dashicons-arrow-right-alt"></span></a>
                <a href="https://docs.thedotstore.com/article/712-getting-started" target="_blank" class="setup-guide"><span class="dashicons dashicons-book-alt"></span><?php esc_html_e( 'Read the Setup Guide', 'min-and-max-quantity-for-woocommerce' ); ?></a>
            </div>
        </div>
        <div class="getting-started-video">
            <iframe width="960" height="600" src="<?php echo esc_url('https://www.youtube.com/embed/a-OTrGK1wAM'); ?>" title="<?php esc_attr_e( 'Plugin Tour', 'min-and-max-quantity-for-woocommerce' ); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
</div>
</div>
</div>
</div>
</div>
<?php
