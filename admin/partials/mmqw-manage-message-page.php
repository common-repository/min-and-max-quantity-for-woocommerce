<?php
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

require_once(plugin_dir_path(__FILE__) . 'header/plugin-header.php');

$allowed_tooltip_html = wp_kses_allowed_html('post')['span'];
$mmqw_admin_object = new MMQW_Min_Max_Quantity_For_WooCommerce_Admin('', '');
$post_data = array();

/**
 * save all custom messages in class-min-max-quantity-for-woocommerce-admin
 */
if (isset($_POST['submitMessages']) && !empty($_POST['submitMessages'])) {

    $post_wpnonce = filter_input(INPUT_POST, 'mmqw_custom_messages_save', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $post_retrieved_nonce = isset($post_wpnonce) ? sanitize_text_field(wp_unslash($post_wpnonce)) : '';

    if (!wp_verify_nonce($post_retrieved_nonce, 'mmqw_custom_messages_save_action')) {
        die('Failed security check');
    } else {

        $post_data['min_order_quantity_reached'] = filter_input(INPUT_POST, 'min_order_quantity_reached', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $post_data['max_order_quantity_exceeded'] = filter_input(INPUT_POST, 'max_order_quantity_exceeded', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $post_data['min_order_value_reached'] = filter_input(INPUT_POST, 'min_order_value_reached', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $post_data['max_order_value_exceeded'] = filter_input(INPUT_POST, 'max_order_value_exceeded', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $post_data['min_order_item_reached'] = filter_input(INPUT_POST, 'min_order_item_reached', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $post_data['max_order_item_exceeded'] = filter_input(INPUT_POST, 'max_order_item_exceeded', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $response = $mmqw_admin_object->mmqw_custom_messages_save($post_data);
    }
}
/** @var  $submit_text create submit button text */
$submit_text = __('Save changes', 'min-and-max-quantity-for-woocommerce');

/** @var  get the min/max quantity required message $min_order_quantity_reached, $max_order_quantity_exceeded */
$min_order_quantity_reached = get_option('min_order_quantity_reached');
$max_order_quantity_exceeded = get_option('max_order_quantity_exceeded');

/** @var Get min/max order value reached message $min_order_value_reached, $max_order_value_exceeded */
$min_order_value_reached = get_option('min_order_value_reached');
$max_order_value_exceeded = get_option('max_order_value_exceeded');

/** @var Get min/max order item message $min_order_item_reached, $max_order_item_exceeded */
$min_order_item_reached = get_option('min_order_item_reached');
$max_order_item_exceeded = get_option('max_order_item_exceeded ');
?>

<form method="POST" name="feefrm" action="">
    <?php
    if (isset($response) && true === $response) {
        echo '<div class="ms-msg">';
        esc_html_e('Settings saved successfully.', 'min-and-max-quantity-for-woocommerce');
        echo '</div>';
    }
    ?>
    <?php wp_nonce_field('mmqw_custom_messages_save_action', 'mmqw_custom_messages_save'); ?>
    <div class="mmqw-main-table res-cl">
        <h2><?php esc_html_e('Manage Messages', 'min-and-max-quantity-for-woocommerce'); ?></h2>
        <table class="form-table table-outer min-max-option-table min-max-manage-msg-table">
            <tbody>
                <tr valign="top" aria-colspan="2">
                    <td class="fr-1 title_td" scope="row" colspan="2">
                        <label for="product_detail_error_message_box"><b><?php esc_html_e('Order quantity rules message', 'min-and-max-quantity-for-woocommerce'); ?>
                            </b></label>
                    </td>
                </tr>
                <tr valign="top">
                    <td class="fr-1" scope="row">
                        <label
                            for="min_order_quantity_reached"><?php esc_html_e('Min Order quantity not reached message', 'min-and-max-quantity-for-woocommerce'); ?>
                        </label>
                        <?php echo wp_kses(wc_help_tip(esc_html__('Add the minimum allows order quantity message for the cart page.', 'min-and-max-quantity-for-woocommerce')), array('span' => $allowed_tooltip_html)); ?>
                    </td>
                    <td class="fr-2">
                        <textarea name="min_order_quantity_reached" id="min_order_quantity_reached"
                            placeholder="<?php esc_attr_e('The minimum allows order quantity is {MIN_ORDER_QTY} and you have {ORDER_QTY} in your cart.', 'min-and-max-quantity-for-woocommerce'); ?>"
                            rows="4" cols="150"><?php echo esc_html($min_order_quantity_reached); ?></textarea>
                    </td>
                </tr>
                <tr valign="top">
                    <td class="fr-1" scope="row">
                        <label
                            for="max_order_quantity_exceeded"><?php esc_html_e('Max order quantity exceeded message', 'min-and-max-quantity-for-woocommerce'); ?>
                        </label>
                        <?php echo wp_kses(wc_help_tip(esc_html__('Add the maximum allows order quantity message for the cart page.', 'min-and-max-quantity-for-woocommerce')), array('span' => $allowed_tooltip_html)); ?>
                    </td>
                    <td class="fr-2">
                        <textarea name="max_order_quantity_exceeded" id="max_order_quantity_exceeded"
                            placeholder="<?php esc_attr_e('The maximum allows order quantity is {MAX_ORDER_QTY} and you have {ORDER_QTY} in your cart.', 'min-and-max-quantity-for-woocommerce'); ?>"
                            rows="4" cols="150"><?php echo esc_html($max_order_quantity_exceeded); ?></textarea>
                    </td>
                </tr>

                <tr valign="top" aria-colspan="2">
                    <td class="fr-1 title_td" scope="row" colspan="2">
                        <label for="cart_page_error_message_box"><b><?php esc_html_e('Order value rules message', 'min-and-max-quantity-for-woocommerce'); ?>
                            </b></label>
                    </td>
                </tr>
                <tr valign="top">
                    <td class="fr-1" scope="row">
                        <label
                            for="min_order_value_reached"><?php esc_html_e('Min order value not reached message', 'min-and-max-quantity-for-woocommerce'); ?>
                        </label>
                        <?php echo wp_kses(wc_help_tip(esc_html__('Add the minimum order value message for the cart page.', 'min-and-max-quantity-for-woocommerce')), array('span' => $allowed_tooltip_html)); ?>
                    </td>
                    <td class="fr-2">
                        <textarea name="min_order_value_reached" id="min_order_value_reached"
                            placeholder="<?php esc_attr_e('The minimum cart value required is {MIN_CART_VALUE} and you have {CART_VALUE} in your cart.', 'min-and-max-quantity-for-woocommerce'); ?>"
                            rows="4" cols="150"><?php echo esc_html($min_order_value_reached); ?></textarea>
                    </td>
                </tr>
                <tr valign="top">
                    <td class="fr-1" scope="row">
                        <label
                            for="max_order_value_exceeded"><?php esc_html_e('Max order value exceeded message', 'min-and-max-quantity-for-woocommerce'); ?>
                        </label>
                        <?php echo wp_kses(wc_help_tip(esc_html__('Add the maximum order value message for the cart page.', 'min-and-max-quantity-for-woocommerce')), array('span' => $allowed_tooltip_html)); ?>
                    </td>
                    <td class="fr-2">
                        <textarea name="max_order_value_exceeded" id="max_order_value_exceeded"
                            placeholder="<?php esc_attr_e('The maximum cart value required is {MAX_CART_VALUE} and you have {CART_VALUE} in your cart.', 'min-and-max-quantity-for-woocommerce'); ?>"
                            rows="4" cols="150"><?php echo esc_html($max_order_value_exceeded); ?></textarea>
                    </td>
                </tr>

                <tr valign="top" aria-colspan="2">
                    <td class="fr-1 title_td" scope="row" colspan="2">
                        <label for="cart_page_error_message_box"><b><?php esc_html_e('Order item rules message', 'min-and-max-quantity-for-woocommerce'); ?>
                            </b></label>
                    </td>
                </tr>
                <tr valign="top">
                    <td class="fr-1" scope="row">
                        <label
                            for="min_order_item_reached"><?php esc_html_e('Min order item not reached message', 'min-and-max-quantity-for-woocommerce'); ?>
                        </label>
                        <?php echo wp_kses(wc_help_tip(esc_html__('Add the minimum order item message for the cart page.', 'min-and-max-quantity-for-woocommerce')), array('span' => $allowed_tooltip_html)); ?>
                    </td>
                    <td class="fr-2">
                        <textarea name="min_order_item_reached" id="min_order_item_reached"
                            placeholder="<?php esc_attr_e('The minimum order item required is {MIN_ORDER_ITEM} for each product in your cart.', 'min-and-max-quantity-for-woocommerce'); ?>"
                            rows="4" cols="150"><?php echo esc_html($min_order_item_reached); ?></textarea>
                    </td>
                </tr>
                <tr valign="top">
                    <td class="fr-1" scope="row">
                        <label
                            for="max_order_item_exceeded"><?php esc_html_e('Max order item exceeded message', 'min-and-max-quantity-for-woocommerce'); ?>
                        </label>
                        <?php echo wp_kses(wc_help_tip(esc_html__('Add the maximum order item message for the cart page.', 'min-and-max-quantity-for-woocommerce')), array('span' => $allowed_tooltip_html)); ?>
                    </td>
                    <td class="fr-2">
                        <textarea name="max_order_item_exceeded" id="max_order_item_exceeded"
                            placeholder="<?php esc_attr_e('The maximum order item should be {MAX_ORDER_ITEM} for each product in your cart.', 'min-and-max-quantity-for-woocommerce'); ?>"
                            rows="4" cols="150"><?php echo esc_html($max_order_item_exceeded); ?></textarea>
                    </td>
                </tr>
                <tr valign="top" aria-colspan="2">
                    <td class="fr-1" scope="row" colspan="2">
                        <p class="submit">
                            <input type="submit" name="submitMessages" class="button button-primary button-large"
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
