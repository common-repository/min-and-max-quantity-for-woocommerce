<?php
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

require_once (plugin_dir_path(__FILE__) . 'header/plugin-header.php');

$allowed_tooltip_html = wp_kses_allowed_html('post')['span'];
$mmqw_admin_object = new MMQW_Min_Max_Quantity_For_WooCommerce_Admin('', '');

/** @var  $submit_text create submit button text */
$submit_text = __('Save changes', 'min-and-max-quantity-for-woocommerce');

?>

<form method="POST" name="feefrm" action="">
    <?php wp_nonce_field('mmqw_display_rules_save_action', 'mmqw_display_rules_save'); ?>
    <div class="mmqw-main-table res-cl preimium-feature-block">
        <h2><?php esc_html_e('Display Rules', 'min-and-max-quantity-for-woocommerce'); ?><span class="mmqw-pro-label"></span></h2>
        <table class="form-table table-outer min-max-option-table min-max-display-rules-table">
            <tbody>
                <tr>
                    <td class="fr-1" scope="row">
                        <label
                            for="show_rules_on_cart"><?php esc_html_e('Show rules on cart page', 'min-and-max-quantity-for-woocommerce'); ?></label>
                        <?php echo wp_kses(wc_help_tip(esc_html__('Enable this option to show the rules on the cart page.', 'min-and-max-quantity-for-woocommerce')), array('span' => $allowed_tooltip_html)); ?>
                    </td>
                    <td class="fr-2">
                        <label class="switch">
                            <input type="checkbox" name="show_rules_on_cart" id="show_rules_on_cart" disabled>
                            <span class="slider round"></span>
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <td class="fr-1" scope="row">
                        <label
                            for="show_rules_on_product_page"><?php esc_html_e('Show rules on product page', 'min-and-max-quantity-for-woocommerce'); ?></label>
                        <?php echo wp_kses(wc_help_tip(esc_html__('Enable this option to show the rules on the product page.', 'min-and-max-quantity-for-woocommerce')), array('span' => $allowed_tooltip_html)); ?>
                    </td>
                    <td class="fr-2">
                        <label class="switch">
                            <input type="checkbox" name="show_rules_on_product_page" id="show_rules_on_product_page" disabled>
                            <span class="slider round"></span>
                        </label>
                    </td>
                </tr>
                <tr valign="top" class="mmqw-position-on-product-page">
                    <td class="fr-1" scope="row">
                        <label
                            for="position_on_product_page"><?php esc_html_e('Position on product page', 'min-and-max-quantity-for-woocommerce'); ?></label>
                        <?php echo wp_kses(wc_help_tip(esc_html__('Select the position to show the rules on the product page.', 'min-and-max-quantity-for-woocommerce')), array('span' => $allowed_tooltip_html)); ?>
                    </td>
                    <td class="fr-2">
                        <select name="position_on_product_page" id="position_on_product_page" class="multiselect2" disabled>
                            <option value="before_add_to_cart">
                                <?php esc_html_e('Before Add to cart', 'min-and-max-quantity-for-woocommerce'); ?>
                            </option>
                            <option value="after_add_to_cart">
                                <?php esc_html_e('After Add to cart', 'min-and-max-quantity-for-woocommerce'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                <tr valign="top" class="mmqw-text-before-rules">
                    <td class="fr-1" scope="row">
                        <label
                            for="text_before_rules"><?php esc_html_e('Text before rules', 'min-and-max-quantity-for-woocommerce'); ?></label>
                        <?php echo wp_kses(wc_help_tip(esc_html__('Add the text before the rules on the product page.', 'min-and-max-quantity-for-woocommerce')), array('span' => $allowed_tooltip_html)); ?>
                    </td>
                    <td class="fr-2">
                        <textarea name="text_before_rules" id="text_before_rules" disabled
                            placeholder="<?php esc_attr_e('Please note the following rules:', 'min-and-max-quantity-for-woocommerce'); ?>"
                            rows="4" cols="150"></textarea>
                    </td>
                </tr>
                <tr valign="top" aria-colspan="2">
                    <td class="fr-1" scope="row" colspan="2">
                        <p class="submit">
                            <input type="submit" name="submitDisplayRules" class="button button-primary button-large" disabled
                                value="<?php echo esc_attr($submit_text); ?>">
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</form>
</div>
</div>
</div>
</div>
<?php
