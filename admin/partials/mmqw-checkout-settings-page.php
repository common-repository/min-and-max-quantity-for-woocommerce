<?php

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-header.php';
$allowed_tooltip_html = wp_kses_allowed_html( 'post' )['span'];
$mmqw_admin_object = new MMQW_Min_Max_Quantity_For_WooCommerce_Admin('', '');
$post_data = array();
/**
 * save all custom messages in class-min-max-quantity-for-woocommerce-admin
 */
if ( isset( $_POST['submitCheckoutSettings'] ) && !empty( $_POST['submitCheckoutSettings'] ) ) {
    $post_wpnonce = filter_input( INPUT_POST, 'mmqw_checkout_settings_save', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    $post_retrieved_nonce = ( isset( $post_wpnonce ) ? sanitize_text_field( wp_unslash( $post_wpnonce ) ) : '' );
    if ( !wp_verify_nonce( $post_retrieved_nonce, 'mmqw_checkout_settings_save_action' ) ) {
        die( 'Failed security check' );
    } else {
        $post_data['min_order_quantity'] = filter_input( INPUT_POST, 'min_order_quantity', FILTER_SANITIZE_NUMBER_INT );
        $post_data['max_order_quantity'] = filter_input( INPUT_POST, 'max_order_quantity', FILTER_SANITIZE_NUMBER_INT );
        $post_data['min_order_value'] = filter_input(
            INPUT_POST,
            'min_order_value',
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION
        );
        $post_data['max_order_value'] = filter_input(
            INPUT_POST,
            'max_order_value',
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION
        );
        $post_data['min_items_quantity'] = filter_input( INPUT_POST, 'min_items_quantity', FILTER_SANITIZE_NUMBER_INT );
        $post_data['max_items_quantity'] = filter_input( INPUT_POST, 'max_items_quantity', FILTER_SANITIZE_NUMBER_INT );
        $response = $mmqw_admin_object->mmqw_checkout_settings_save( $post_data );
    }
}
$submit_text = __( 'Save changes', 'min-and-max-quantity-for-woocommerce' );
$min_order_quantity = get_option( 'min_order_quantity' );
$max_order_quantity = get_option( 'max_order_quantity' );
$min_order_value = get_option( 'min_order_value' );
$max_order_value = get_option( 'max_order_value' );
$min_items_quantity = get_option( 'min_items_quantity' );
$max_items_quantity = get_option( 'max_items_quantity' );
if ( isset( $response ) && true === $response ) {
    echo '<div class="ms-msg">';
    esc_html_e( 'Settings saved successfully.', 'min-and-max-quantity-for-woocommerce' );
    echo '</div>';
}
?>
<form class="checkout-settings-form" method="POST" name="checkout-settings-frm" action="">
	<?php 
wp_nonce_field( 'mmqw_checkout_settings_save_action', 'mmqw_checkout_settings_save' );
?>
	<div class="mmqw-main-table res-cl">
		<h2><?php 
esc_html_e( 'Checkout Settings', 'min-and-max-quantity-for-woocommerce' );
?></h2>
		<table class="form-table table-outer min-max-option-table">
			<tbody>
				<tr valign="top" aria-colspan="2">
					<td class="fr-1 title_td" scope="row" colspan="2">
						<label for="product_detail_error_message_box"><b><?php 
esc_html_e( 'Min/Max Order QTY', 'min-and-max-quantity-for-woocommerce' );
?>
							</b></label>
					</td>
				</tr>
				<tr valign="top">
					<td class="fr-1" scope="row">
						<label
							for="min_order_quantity"><?php 
esc_html_e( 'Min Order QTY', 'min-and-max-quantity-for-woocommerce' );
?>
						</label>
						<?php 
echo wp_kses( wc_help_tip( esc_html__( 'Add minimum order quantity to add on order page. eg., 10.', 'min-and-max-quantity-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
					</td>
					<td class="fr-2">
						<input type="number" name="min_order_quantity" class="num-class qty-class"
							id="min_order_quantity" value="<?php 
echo esc_attr( $min_order_quantity );
?>" min="0"
							placeholder="<?php 
esc_attr_e( 'eg. 10', 'min-and-max-quantity-for-woocommerce' );
?>">
					</td>
				</tr>
				<tr valign="top">
					<td class="fr-1" scope="row">
						<label
							for="max_order_quantity"><?php 
esc_html_e( 'Max Order QTY', 'min-and-max-quantity-for-woocommerce' );
?>
						</label>
						<?php 
echo wp_kses( wc_help_tip( esc_html__( 'Add maximum order quantity to add on order page. eg., 10.', 'min-and-max-quantity-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
					</td>
					<td class="fr-2">
						<input type="number" name="max_order_quantity" class="num-class qty-class"
							id="max_order_quantity" value="<?php 
echo esc_attr( $max_order_quantity );
?>" min="0"
							placeholder="<?php 
esc_attr_e( 'eg. 10', 'min-and-max-quantity-for-woocommerce' );
?>">
					</td>
				</tr>
				<tr valign="top" aria-colspan="2">
					<td class="fr-1 title_td" scope="row" colspan="2">
						<label for="cart_page_error_message_box"><b><?php 
esc_html_e( 'Min/Max Order Value', 'min-and-max-quantity-for-woocommerce' );
?>
							</b></label>
					</td>
				</tr>
				<tr valign="top">
					<td class="fr-1" scope="row">
						<label
							for="min_order_value"><?php 
esc_html_e( 'Min Order Value', 'min-and-max-quantity-for-woocommerce' );
?>
						</label>
						<?php 
echo wp_kses( wc_help_tip( esc_html__( 'Add minimum order value to add on order page. eg., 10.', 'min-and-max-quantity-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
					</td>
					<td class="fr-2">
						<input type="number" name="min_order_value" class="num-class" id="min_order_value"
							value="<?php 
echo esc_attr( $min_order_value );
?>" min="0" step="0.01"
							placeholder="<?php 
esc_attr_e( 'eg. 10', 'min-and-max-quantity-for-woocommerce' );
?>">
					</td>
				</tr>
				<tr valign="top">
					<td class="fr-1" scope="row">
						<label
							for="max_order_value"><?php 
esc_html_e( 'Max Order Value', 'min-and-max-quantity-for-woocommerce' );
?>
						</label>
						<?php 
echo wp_kses( wc_help_tip( esc_html__( 'Add maximum order value to add on order page. eg., 10.', 'min-and-max-quantity-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
					</td>
					<td class="fr-2">
						<input type="number" name="max_order_value" class="num-class" id="max_order_value"
							value="<?php 
echo esc_attr( $max_order_value );
?>" min="0" step="0.01"
							placeholder="<?php 
esc_attr_e( 'eg. 10', 'min-and-max-quantity-for-woocommerce' );
?>">
					</td>
				</tr>
				<tr valign="top" aria-colspan="2">
					<td class="fr-1 title_td" scope="row" colspan="2">
						<label for="product_detail_error_message_box"><b><?php 
esc_html_e( 'Min/Max Order Item', 'min-and-max-quantity-for-woocommerce' );
?>
							</b></label>
					</td>
				</tr>
				<tr valign="top">
					<td class="fr-1" scope="row">
						<label
							for="min_items_quantity"><?php 
esc_html_e( 'Min Order Item', 'min-and-max-quantity-for-woocommerce' );
?>
						</label>
						<?php 
echo wp_kses( wc_help_tip( esc_html__( 'Add minimum items quantity to add on order page. eg., 10.', 'min-and-max-quantity-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
					</td>
					<td class="fr-2">
						<input type="number" name="min_items_quantity" class="num-class" id="min_items_quantity"
							value="<?php 
echo esc_attr( $min_items_quantity );
?>" min="0"
							placeholder="<?php 
esc_attr_e( 'eg. 10', 'min-and-max-quantity-for-woocommerce' );
?>">
					</td>
				</tr>
				<tr valign="top">
					<td class="fr-1" scope="row">
						<label
							for="max_items_quantity"><?php 
esc_html_e( 'Max Order Item', 'min-and-max-quantity-for-woocommerce' );
?>
						</label>
						<?php 
echo wp_kses( wc_help_tip( esc_html__( 'Add maximum items quantity to add on order page. eg., 10.', 'min-and-max-quantity-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
					</td>
					<td class="fr-2">
						<input type="number" name="max_items_quantity" class="num-class" id="max_items_quantity"
							value="<?php 
echo esc_attr( $max_items_quantity );
?>" min="0"
							placeholder="<?php 
esc_attr_e( 'eg. 10', 'min-and-max-quantity-for-woocommerce' );
?>">
					</td>
				</tr>
				<tr valign="top" aria-colspan="2">
					<td class="fr-1 title_td" scope="row" colspan="2">
						<label for="product_detail_error_message_box"><b><?php 
esc_html_e( 'Other Checkout Options', 'min-and-max-quantity-for-woocommerce' );
?></b></label>
					</td>
				</tr>
				<?php 
?>
					<tr valign="top">
						<td class="fr-1" scope="row">
							<label
								for="exclude_product_list"><?php 
esc_html_e( 'Exclude Product', 'min-and-max-quantity-for-woocommerce' );
?><span class="mmqw-pro-label"></span>
							</label>
							<?php 
echo wp_kses( wc_help_tip( esc_html__( 'Select product to exclude from min/max quantity validation.', 'min-and-max-quantity-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
						</td>
						<td class="fr-2">
							<select name="exclude_product_list[]" id="exclude_product_list" class="" disabled>
								<option value=""><?php 
esc_html_e( 'All Products', 'min-and-max-quantity-for-woocommerce' );
?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<td class="fr-1" scope="row">
							<label for="mmqw_checkout_proceed"><?php 
esc_html_e( 'Hide Checkout Button', 'min-and-max-quantity-for-woocommerce' );
?><span class="mmqw-pro-label"></span>
							</label>
							<?php 
echo wp_kses( wc_help_tip( esc_html__( 'Hide Proceed to Checkout Button on Cart page if min/max quantity validation fails.', 'min-and-max-quantity-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
						</td>
						<td class="fr-2">
							<label class="switch mmqw-pro-feature">
								<input type="checkbox" name="mmqw_checkout_proceed" id="mmqw_checkout_proceed" value="1" disabled>
								<span class="slider round"></span>
							</label>
						</td>
					</tr>
					<?php 
?>
				<tr valign="top" aria-colspan="2">
					<td class="fr-1" scope="row" colspan="2">
						<p class="submit">
							<input type="submit" name="submitCheckoutSettings"
								class="button button-primary button-large"
								value="<?php 
echo esc_attr( $submit_text );
?>">
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
