<?php

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-header.php';
$mmqw_admin_object = new MMQW_Min_Max_Quantity_For_WooCommerce_Admin('', '');
$mmqw_object = new Min_Max_Quantity_For_WooCommerce('', '');
$allowed_tooltip_html = wp_kses_allowed_html( 'post' )['span'];
$get_action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
/*
 * edit all posted data method define in class-min-max-quantity-for-woocommerce-admin
 */
if ( isset( $get_action ) && 'edit' === $get_action ) {
    $get_id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
    $get_wpnonce = filter_input( INPUT_GET, 'cust_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    $get_retrieved_nonce = ( isset( $get_wpnonce ) ? sanitize_text_field( wp_unslash( $get_wpnonce ) ) : '' );
    $get_duplicate_wpnonce = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    $get_duplicate_nonce = ( isset( $get_duplicate_wpnonce ) ? sanitize_text_field( wp_unslash( $get_duplicate_wpnonce ) ) : '' );
    if ( !wp_verify_nonce( $get_retrieved_nonce, 'edit_' . $get_id ) && !wp_verify_nonce( $get_duplicate_nonce, 'edit_' . $get_id ) ) {
        die( 'Failed security check' );
    }
    $get_post_id = ( isset( $get_id ) ? sanitize_text_field( wp_unslash( $get_id ) ) : '' );
    $sm_status = get_post_status( $get_post_id );
    $mmqw_total_groups = get_post_meta( $get_post_id, 'mmqw_total_groups', true );
    $sm_title = __( get_the_title( $get_post_id ), 'min-and-max-quantity-for-woocommerce' );
    $fee_settings_unique_shipping_title = get_post_meta( $get_post_id, 'fee_settings_unique_shipping_title', true );
    $mmqw_rule_groups = get_post_meta( $get_post_id, 'mmqw_rule_groups', true );
    if ( is_serialized( $mmqw_rule_groups ) ) {
        $mmqw_rule_groups = maybe_unserialize( $mmqw_rule_groups );
    } else {
        $mmqw_rule_groups = $mmqw_rule_groups;
    }
} else {
    $get_post_id = '';
    $sm_status = '';
    $mmqw_total_groups = 1;
    $sm_title = '';
    $fee_settings_unique_shipping_title = '';
    $sm_metabox = array();
    $mmqw_rule_groups = array();
}
$sm_status = ( !empty( $sm_status ) && 'publish' === $sm_status || empty( $sm_status ) ? 'checked' : '' );
$sm_title = ( !empty( $sm_title ) ? esc_attr( stripslashes( $sm_title ) ) : '' );
$submit_text = __( 'Save changes', 'min-and-max-quantity-for-woocommerce' );
if ( empty( $fee_settings_unique_shipping_title ) && !empty( $sm_title ) ) {
    $fee_settings_unique_shipping_title = $sm_title;
}
?>
<div class="mmqw-main-table res-cl mmqw-add-rule-page">
    <h2><?php 
esc_html_e( 'Configuration', 'min-and-max-quantity-for-woocommerce' );
?></h2>
    <form method="POST" name="feefrm" action="">
        <?php 
wp_nonce_field( 'mmqw_save_action', 'mmqw_conditions_save' );
?>
        <input type="hidden" name="post_type" value="wc_mmqw">
        <input type="hidden" name="fee_post_id" value="<?php 
echo esc_attr( $get_post_id );
?>">
        <table class="form-table table-outer shipping-method-table">
            <tbody>
                <tr valign="top">
                    <th class="titledesc" scope="row">
                        <label
                            for="onoffswitch"><?php 
esc_html_e( 'Status', 'min-and-max-quantity-for-woocommerce' );
?></label>
                        <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Enable or Disable this Min Max rule using this button (This rule only apply if it is enabled).', 'min-and-max-quantity-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                    </th>
                    <td class="forminp">
                        <label class="switch">
                            <input type="checkbox" name="sm_status" value="on" <?php 
echo esc_attr( $sm_status );
?>>
                            <div class="slider round"></div>
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row">
                        <label
                            for="fee_settings_unique_shipping_title"><?php 
esc_html_e( 'Min/Max Rule Title', 'min-and-max-quantity-for-woocommerce' );
?>
                            <span class="required-star">*</span>
                        </label>
                        <?php 
echo wp_kses( wc_help_tip( esc_html__( 'This name will use only for admin purpose.', 'min-and-max-quantity-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                    </th>
                    <td class="forminp">
                        <input type="text" name="fee_settings_unique_shipping_title" class="text-class"
                            id="fee_settings_unique_shipping_title"
                            value="<?php 
echo esc_attr( $fee_settings_unique_shipping_title );
?>" required=""
                            placeholder="<?php 
esc_attr_e( 'Rule title', 'min-and-max-quantity-for-woocommerce' );
?>">
                    </td>
                </tr>
            </tbody>
        </table>
        <?php 
// Advanced Pricing Section start
?>
        <div id="apm_wrap" class="mmqw-condition-rules">
            <div class="ap_title">
                <h2><?php 
esc_html_e( 'Advanced Rules', 'min-and-max-quantity-for-woocommerce' );
?></h2>
            </div>
            <div class="pricing_rules mmqw-rules-section-main">
                <div class="mmqw-rules-groups-main">
                    <?php 
$minVal = 113241;
$maxVal = 999999;
$random_number = wp_rand( $minVal, $maxVal );
$html = '';
if ( isset( $mmqw_rule_groups ) && !empty( $mmqw_rule_groups ) && is_array( $mmqw_rule_groups ) ) {
    $group_number = 0;
    foreach ( $mmqw_rule_groups as $key => $mmqw_group ) {
        $mmqw_group_id = ( isset( $mmqw_group['mmqw_group_id'] ) && !empty( $mmqw_group['mmqw_group_id'] ) ? $mmqw_group['mmqw_group_id'] : '' );
        $mmqw_group_status = ( isset( $mmqw_group['mmqw_group_status'] ) && !empty( $mmqw_group['mmqw_group_status'] ) ? $mmqw_group['mmqw_group_status'] : 'off' );
        $mmqw_rules = ( isset( $mmqw_group['mmqw_rule'] ) && !empty( $mmqw_group['mmqw_rule'] ) ? $mmqw_group['mmqw_rule'] : array() );
        $mmqw_group_min_qty = ( isset( $mmqw_group['mmqw_group_min_qty'] ) && !empty( $mmqw_group['mmqw_group_min_qty'] ) ? $mmqw_group['mmqw_group_min_qty'] : '' );
        $mmqw_group_max_qty = ( isset( $mmqw_group['mmqw_group_max_qty'] ) && !empty( $mmqw_group['mmqw_group_max_qty'] ) ? $mmqw_group['mmqw_group_max_qty'] : '' );
        $group_status = ( !empty( $mmqw_group_status ) && 'on' === $mmqw_group_status && "" !== $mmqw_group_status ? 'checked' : '' );
        ?>
                            <div id="mmqw-rules-group-<?php 
        echo esc_attr( $group_number );
        ?>" class="mmqw-rules-group-main">
                                <input type="hidden" name="mmqw_group[<?php 
        echo esc_attr( $group_number );
        ?>][mmqw_group_id]"
                                    value="<?php 
        echo esc_attr( $mmqw_group_id );
        ?>">
                                <div class="mmqw-select">
                                    <input type="checkbox" name="mmqw_minus" class="mmqw-select-opt">
                                </div>
                                <div class="mmqw-group-status">
                                    <div class="switch_status_div">
                                        <label class="switch switch_in_pricing_rules">
                                            <input type="checkbox"
                                                name="mmqw_group[<?php 
        echo esc_attr( $group_number );
        ?>][mmqw_group_status]"
                                                value="on" <?php 
        echo esc_attr( $group_status );
        ?>>
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
        esc_html_e( $group_number + 1, 'min-and-max-quantity-for-woocommerce' );
        ?></h2>
                                </div>
                                <div class="mmqw-rules-group-body">
                                    <div class="shipping-method-rules mmqw-conditions-section">
                                        <div class="sub-title">
                                            <div class="mmqw-conditions-title">
                                                <h2><?php 
        esc_html_e( 'Conditions', 'min-and-max-quantity-for-woocommerce' );
        ?>
                                                </h2>
                                                <div class="tap">
                                                    <a class="button button-secondary mmqw-add-new-rule"
                                                        href="javascript:void(0);"><?php 
        esc_html_e( '+ Add Rule', 'min-and-max-quantity-for-woocommerce' );
        ?></a>
                                                </div>
                                                <?php 
        ?>
                                                    <div class="mmqw-rule-condition">
                                                        <p class="mmqw-conditions-description">
                                                            <?php 
        esc_html_e( 'below', 'min-and-max-quantity-for-woocommerce' );
        ?>
                                                        </p>
                                                        <select
                                                            name="mmqw_group[<?php 
        echo esc_attr( $group_number );
        ?>][mmqw_rule_condition_type]"
                                                            class="mmqw_rule_condition_type">
                                                            <option value="any" selected>
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
                                            <table
                                                id="tbl-min-max-rules-<?php 
        esc_html_e( $group_number, 'min-and-max-quantity-for-woocommerce' );
        ?>"
                                                class="tbl_product_fee table-outer tap-cas form-table shipping-method-table">
                                                <tbody>
                                                    <?php 
        if ( is_array( $mmqw_rules ) ) {
            $rule_number = 0;
            foreach ( $mmqw_rules as $key => $mmqw_rule ) {
                $mmqw_rule_condition = ( isset( $mmqw_rule['mmqw_rule_condition'] ) && !empty( $mmqw_rule['mmqw_rule_condition'] ) ? $mmqw_rule['mmqw_rule_condition'] : '' );
                $mmqw_rule_condition_is = ( isset( $mmqw_rule['mmqw_rule_condition_is'] ) && !empty( $mmqw_rule['mmqw_rule_condition_is'] ) ? $mmqw_rule['mmqw_rule_condition_is'] : '' );
                $mmqw_rule_condition_value = ( isset( $mmqw_rule['mmqw_rule_condition_value'] ) && !empty( $mmqw_rule['mmqw_rule_condition_value'] ) ? $mmqw_rule['mmqw_rule_condition_value'] : array() );
                $product_condition_class = '';
                if ( 'product' === $mmqw_rule_condition ) {
                    $product_condition_class = 'min_max_select mmqw_rule_condition_value mmqw_rule_condition_value_' . $rule_number . ' multiselect2 mmqw_product mmqw_product_rule_condition_val';
                } elseif ( 'variable_product' === $mmqw_rule_condition ) {
                    $product_condition_class = 'min_max_select mmqw_rule_condition_value mmqw_rule_condition_value_' . $rule_number . ' multiselect2 mmqw_product_variation mmqw_var_product_rule_condition_val';
                }
                ?>
                                                            <tr id="mmqw_group_<?php 
                echo esc_attr( $group_number );
                ?>_row_<?php 
                echo esc_attr( $rule_number );
                ?>"
                                                                valign="top">
                                                                <th class="titledesc th_mmqw_rule_condition" scope="row">
                                                                    <select
                                                                        id="mmqw_qroup_<?php 
                echo esc_attr( $group_number );
                ?>_conditions_condition_<?php 
                echo esc_attr( $rule_number );
                ?>"
                                                                        group-number="<?php 
                echo esc_attr( $group_number );
                ?>"
                                                                        class="mmqw_rule_condition"
                                                                        rel-id="<?php 
                echo esc_attr( $rule_number );
                ?>"
                                                                        name="mmqw_group[<?php 
                echo esc_attr( $group_number );
                ?>][mmqw_rule][<?php 
                echo esc_attr( $rule_number );
                ?>][mmqw_rule_condition]">
                                                                        <optgroup
                                                                            label="<?php 
                esc_attr_e( 'Product Specific', 'min-and-max-quantity-for-woocommerce' );
                ?>">
                                                                            <option value="product" <?php 
                echo ( 'product' === $mmqw_rule_condition ? 'selected' : '' );
                ?>>
                                                                                <?php 
                esc_html_e( 'Product', 'min-and-max-quantity-for-woocommerce' );
                ?>
                                                                            </option>
                                                                            <option value="variable_product" <?php 
                echo ( 'variable_product' === $mmqw_rule_condition ? 'selected' : '' );
                ?>>
                                                                                <?php 
                esc_html_e( 'Variable Product', 'min-and-max-quantity-for-woocommerce' );
                ?>
                                                                            </option>
                                                                            <option value="category" <?php 
                echo ( 'category' === $mmqw_rule_condition ? 'selected' : '' );
                ?>>
                                                                                <?php 
                esc_html_e( 'Category', 'min-and-max-quantity-for-woocommerce' );
                ?>
                                                                            </option>

                                                                            <?php 
                ?>
                                                                                <option value="total_sales_in_pro" <?php 
                echo ( 'total_sales' === $mmqw_rule_condition ? 'selected' : '' );
                ?>>
                                                                                    <?php 
                esc_html_e( 'Total Sales 🔒', 'min-and-max-quantity-for-woocommerce' );
                ?>
                                                                                </option>
                                                                                <option value="stock_quantity_in_pro" <?php 
                echo ( 'stock_quantity' === $mmqw_rule_condition ? 'selected' : '' );
                ?>>
                                                                                    <?php 
                esc_html_e( 'Stock Quantity 🔒', 'min-and-max-quantity-for-woocommerce' );
                ?>
                                                                                </option>
                                                                                <option value="sale_price_in_pro" <?php 
                echo ( 'sale_price' === $mmqw_rule_condition ? 'selected' : '' );
                ?>>
                                                                                    <?php 
                esc_html_e( 'Sale Price 🔒', 'min-and-max-quantity-for-woocommerce' );
                ?>
                                                                                </option>
                                                                                <option value="product_age_in_pro" <?php 
                echo ( 'product_age' === $mmqw_rule_condition ? 'selected' : '' );
                ?>>
                                                                                    <?php 
                esc_html_e( 'Product Age 🔒', 'min-and-max-quantity-for-woocommerce' );
                ?>
                                                                                </option>
                                                                                <option value="best_sellers_in_pro" <?php 
                echo ( 'best_sellers' === $mmqw_rule_condition ? 'selected' : '' );
                ?>>
                                                                                    <?php 
                esc_html_e( 'Best Sellers 🔒', 'min-and-max-quantity-for-woocommerce' );
                ?>
                                                                                </option>
                                                                                <option value="product_attributes_in_pro" <?php 
                echo ( 'product_attributes' === $mmqw_rule_condition ? 'selected' : '' );
                ?>>
                                                                                    <?php 
                esc_html_e( 'Product Attributes 🔒', 'min-and-max-quantity-for-woocommerce' );
                ?>
                                                                                </option>
                                                                            <?php 
                ?>
                                                                        </optgroup>
                                                                        <optgroup
                                                                            label="<?php 
                esc_attr_e( 'Location Specific', 'min-and-max-quantity-for-woocommerce' );
                ?>">
                                                                            <option value="country" <?php 
                echo ( 'country' === $mmqw_rule_condition ? 'selected' : '' );
                ?>>
                                                                                <?php 
                esc_html_e( 'Country', 'min-and-max-quantity-for-woocommerce' );
                ?>
                                                                            </option>
                                                                        </optgroup>

                                                                        <?php 
                ?>
                                                                            <optgroup
                                                                                label="<?php 
                esc_attr_e( 'User Specific', 'min-and-max-quantity-for-woocommerce' );
                ?>">
                                                                                <option value="user_role_in_pro" <?php 
                echo ( 'user_role' === $mmqw_rule_condition ? 'selected' : '' );
                ?>>
                                                                                    <?php 
                esc_html_e( 'User Role 🔒', 'min-and-max-quantity-for-woocommerce' );
                ?>
                                                                                </option>
                                                                                <option value="user_in_pro" <?php 
                echo ( 'user' === $mmqw_rule_condition ? 'selected' : '' );
                ?>>
                                                                                    <?php 
                esc_html_e( 'User 🔒', 'min-and-max-quantity-for-woocommerce' );
                ?>
                                                                                </option>
                                                                            </optgroup>
                                                                            <optgroup
                                                                                label="<?php 
                esc_attr_e( 'Limit Section', 'min-and-max-quantity-for-woocommerce' );
                ?>">
                                                                                <option value="limit_section_time_frame_in_pro" <?php 
                echo ( 'limit_section_time_frame' === $mmqw_rule_condition ? 'selected' : '' );
                ?>>
                                                                                    <?php 
                esc_html_e( 'Time Frame 🔒', 'min-and-max-quantity-for-woocommerce' );
                ?>
                                                                                </option>
                                                                            </optgroup>
                                                                            <optgroup
                                                                                label="<?php 
                esc_attr_e( 'Cart Specific', 'min-and-max-quantity-for-woocommerce' );
                ?>">
                                                                                <option value="cart_coupon_in_pro" <?php 
                echo ( 'cart_coupon' === $mmqw_rule_condition ? 'selected' : '' );
                ?>>
                                                                                    <?php 
                esc_html_e( 'Cart Coupon 🔒', 'min-and-max-quantity-for-woocommerce' );
                ?>
                                                                                </option>
                                                                                <option value="shipping_zone_in_pro" <?php 
                echo ( 'shipping_zone' === $mmqw_rule_condition ? 'selected' : '' );
                ?>>
                                                                                    <?php 
                esc_html_e( 'Shipping Zone 🔒', 'min-and-max-quantity-for-woocommerce' );
                ?>
                                                                                </option>
                                                                                <option value="shipping_method_in_pro" <?php 
                echo ( 'shipping_method' === $mmqw_rule_condition ? 'selected' : '' );
                ?>>
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
                                                                    <select
                                                                        name="mmqw_group[<?php 
                echo esc_attr( $group_number );
                ?>][mmqw_rule][<?php 
                echo esc_attr( $rule_number );
                ?>][mmqw_rule_condition_is]"
                                                                        id="mmqw_qroup_<?php 
                echo esc_attr( $group_number );
                ?>_conditions_condition_is_<?php 
                echo esc_attr( $rule_number );
                ?>"
                                                                        class="mmqw_rule_condition_is mmqw_rule_group_<?php 
                echo esc_attr( $group_number );
                ?>_condition_is_<?php 
                echo esc_attr( $rule_number );
                ?>">
                                                                        <option value="is_equal_to" <?php 
                echo ( 'is_equal_to' === $mmqw_rule_condition_is ? 'selected' : '' );
                ?>>
                                                                            <?php 
                esc_html_e( 'Equal to ( = )', 'min-and-max-quantity-for-woocommerce' );
                ?>
                                                                        </option>
                                                                        <option value="not_in" <?php 
                echo ( 'not_in' === $mmqw_rule_condition_is ? 'selected' : '' );
                ?>>
                                                                            <?php 
                esc_html_e( 'Not Equal to ( != )', 'min-and-max-quantity-for-woocommerce' );
                ?>
                                                                        </option>
                                                                        <?php 
                ?>

                                                                    </select>
                                                                </td>
                                                                <td class="condition-value"
                                                                    id="group_<?php 
                echo esc_attr( $group_number );
                ?>_column_<?php 
                echo esc_attr( $rule_number );
                ?>"
                                                                    class="condition-value">
                                                                    <?php 
                if ( 'product' === $mmqw_rule_condition ) {
                    $html = $mmqw_admin_object->mmqw_get_product_list(
                        $group_number,
                        $rule_number,
                        $mmqw_rule_condition_value,
                        'edit'
                    );
                } elseif ( 'variable_product' === $mmqw_rule_condition ) {
                    $html = $mmqw_admin_object->mmqw_get_variable_product_list(
                        $group_number,
                        $rule_number,
                        $mmqw_rule_condition_value,
                        'edit'
                    );
                } elseif ( 'category' === $mmqw_rule_condition ) {
                    $html = $mmqw_admin_object->mmqw_get_category_list( $group_number, $rule_number, $mmqw_rule_condition_value );
                } elseif ( 'country' === $mmqw_rule_condition ) {
                    $html = $mmqw_admin_object->mmqw_get_country_list( $group_number, $rule_number, $mmqw_rule_condition_value );
                }
                echo wp_kses( $html, $mmqw_object::mmqw_allowed_html_tags() );
                ?>
                                                                    <input type="hidden"
                                                                        name="mmqw_group_<?php 
                echo esc_attr( $group_number );
                ?>_condition_key[<?php 
                echo esc_attr( $rule_number );
                ?>][]"
                                                                        value="">
                                                                </td>
                                                                <td class="delete-td-row">
                                                                    <a id="fee-delete-field" class="delete-row"
                                                                        href="javascript:void(0);" title="Delete">
                                                                        <i class="dashicons dashicons-trash"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            <?php 
                $rule_number++;
            }
            ?>
                                                        <input type="hidden"
                                                            name="mmqw_total_rows_group_<?php 
            echo esc_attr( $group_number );
            ?>"
                                                            id="mmqw_total_rows_group_<?php 
            echo esc_attr( $group_number );
            ?>"
                                                            value="<?php 
            echo esc_attr( $rule_number );
            ?>">
                                                        <?php 
        }
        ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="shipping-method-rules mmqw-actions-section" style="margin-top: 15px;">
                                        <div class="sub-title">
                                            <div class="mmqw-conditions-title">
                                                <h2><?php 
        esc_html_e( 'Action', 'min-and-max-quantity-for-woocommerce' );
        ?>
                                                </h2>
                                            </div>
                                        </div>
                                        <div class="tap">
                                            <table id="tbl-shipping-method"
                                                class="tbl_product_fee table-outer tap-cas form-table shipping-method-table">
                                                <tbody>
                                                    <tr valign="top">
                                                        <td>
                                                            <label
                                                                for="mmqw_group_<?php 
        echo esc_attr( $group_number );
        ?>_min_qty"><?php 
        esc_html_e( 'Min Quantity', 'min-and-max-quantity-for-woocommerce' );
        ?></label>
                                                            <input type="number"
                                                                name="mmqw_group[<?php 
        echo esc_attr( $group_number );
        ?>][mmqw_group_min_qty]"
                                                                class="text-class qty-class mmqw-min-qty-field"
                                                                id="mmqw_group_<?php 
        echo esc_attr( $group_number );
        ?>_min_qty"
                                                                placeholder="Min quantity"
                                                                value="<?php 
        echo esc_attr( $mmqw_group_min_qty );
        ?>" min="0">
                                                            <?php 
        echo wp_kses( wc_help_tip( esc_html__( 'You can set a minimum product quantity to apply on product detail page and cart page.', 'min-and-max-quantity-for-woocommerce' ) ), array(
            'span' => $allowed_tooltip_html,
        ) );
        ?>
                                                        </td>
                                                        <td>
                                                            <label
                                                                for="mmqw_group_<?php 
        echo esc_attr( $group_number );
        ?>_max_qty"><?php 
        esc_html_e( 'Max Quantity', 'min-and-max-quantity-for-woocommerce' );
        ?></label>
                                                            <input type="number"
                                                                name="mmqw_group[<?php 
        echo esc_attr( $group_number );
        ?>][mmqw_group_max_qty]"
                                                                class="text-class qty-class mmqw-max-qty-field"
                                                                id="mmqw_group_<?php 
        echo esc_attr( $group_number );
        ?>_max_qty"
                                                                placeholder="Max quantity"
                                                                value="<?php 
        echo esc_attr( $mmqw_group_max_qty );
        ?>" min="0">
                                                            <?php 
        echo wp_kses( wc_help_tip( esc_html__( 'You can set a maximum product quantity to apply on product detail page and cart page.', 'min-and-max-quantity-for-woocommerce' ) ), array(
            'span' => $allowed_tooltip_html,
        ) );
        ?>
                                                        </td>
                                                        <?php 
        ?>
                                                            <td>
                                                                <label
                                                                    for="mmqw_group_<?php 
        echo esc_attr( $group_number );
        ?>_step_qty"><?php 
        esc_html_e( 'Step Quantity', 'min-and-max-quantity-for-woocommerce' );
        ?><span
                                                                        class="mmqw-pro-label"></span></label>
                                                                <input type="number" disabled
                                                                    name="mmqw_group[<?php 
        echo esc_attr( $group_number );
        ?>][mmqw_group_step_qty_in_pro]"
                                                                    class="text-class qty-class mmqw-step-qty-field"
                                                                    id="mmqw_group_<?php 
        echo esc_attr( $group_number );
        ?>_step_qty"
                                                                    placeholder="Step quantity" min="0">
                                                                <?php 
        echo wp_kses( wc_help_tip( esc_html__( 'You can set a step product quantity to apply on product detail page and cart page.', 'min-and-max-quantity-for-woocommerce' ) ), array(
            'span' => $allowed_tooltip_html,
        ) );
        ?>
                                                            </td>
                                                            <td>
                                                                <label
                                                                    for="mmqw_group_<?php 
        echo esc_attr( $group_number );
        ?>_default_qty"><?php 
        esc_html_e( 'Default Quantity', 'min-and-max-quantity-for-woocommerce' );
        ?><span
                                                                        class="mmqw-pro-label"></span></label>
                                                                <input type="number" disabled
                                                                    name="mmqw_group[<?php 
        echo esc_attr( $group_number );
        ?>][mmqw_group_default_qty_in_pro]"
                                                                    class="text-class qty-class mmqw-default-qty-field"
                                                                    id="mmqw_group_<?php 
        echo esc_attr( $group_number );
        ?>_default_qty"
                                                                    placeholder="Default quantity" step=""
                                                                    min="<?php 
        echo esc_attr( $mmqw_group_min_qty );
        ?>"
                                                                    max="<?php 
        echo esc_attr( $mmqw_group_max_qty );
        ?>">
                                                                <?php 
        echo wp_kses( wc_help_tip( esc_html__( 'You can set a default product quantity to apply on product detail page.', 'min-and-max-quantity-for-woocommerce' ) ), array(
            'span' => $allowed_tooltip_html,
        ) );
        ?>
                                                            </td>
                                                            <td>
                                                                <label
                                                                    for="mmqw_group_<?php 
        echo esc_attr( $group_number );
        ?>_default_qty_selector"><?php 
        esc_html_e( 'Quantity Selector', 'min-and-max-quantity-for-woocommerce' );
        ?><span
                                                                        class="mmqw-pro-label"></span></label>
                                                                <select
                                                                    name="mmqw_group[<?php 
        echo esc_attr( $group_number );
        ?>][mmqw_group_default_qty_selector_in_pro]"
                                                                    disabled class="mmqw_group_default_qty_selector">
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
        $group_number++;
    }
} else {
    $group_number = 0;
    ?>
                        <div id="mmqw-rules-group-<?php 
    echo esc_attr( $group_number );
    ?>" class="mmqw-rules-group-main">
                            <input type="hidden" name="mmqw_group[<?php 
    echo esc_attr( $group_number );
    ?>][mmqw_group_id]"
                                value="<?php 
    echo esc_attr( $get_post_id . '_' . $group_number . '_' . $random_number );
    ?>">
                            <div class="mmqw-select">
                                <input type="checkbox" name="mmqw_minus" class="mmqw-select-opt">
                            </div>
                            <div class="mmqw-group-status">
                                <div class="switch_status_div">
                                    <label class="switch switch_in_pricing_rules">
                                        <input type="checkbox"
                                            name="mmqw_group[<?php 
    echo esc_attr( $group_number );
    ?>][mmqw_group_status]"
                                            value="on" checked="">
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
    esc_html_e( $group_number + 1, 'min-and-max-quantity-for-woocommerce' );
    ?></h2>
                            </div>
                            <div class="mmqw-rules-group-body">
                                <div class="shipping-method-rules mmqw-conditions-section">
                                    <div class="sub-title">
                                        <div class="mmqw-conditions-title">
                                            <h2><?php 
    esc_html_e( 'Conditions', 'min-and-max-quantity-for-woocommerce' );
    ?>
                                            </h2>
                                            <div class="tap">
                                                <a class="button button-secondary mmqw-add-new-rule"
                                                    href="javascript:void(0);"><?php 
    esc_html_e( '+ Add Rule', 'min-and-max-quantity-for-woocommerce' );
    ?></a>
                                            </div>
                                            <?php 
    ?>
                                                <div class="mmqw-rule-condition">
                                                    <p class="mmqw-conditions-description">
                                                        <?php 
    esc_html_e( 'below', 'min-and-max-quantity-for-woocommerce' );
    ?>
                                                    </p>
                                                    <select
                                                        name="mmqw_group[<?php 
    echo esc_attr( $group_number );
    ?>][mmqw_rule_condition_type]"
                                                        class="mmqw_rule_condition_type">
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
                                        <table
                                            id="tbl-min-max-rules-<?php 
    esc_html_e( $group_number, 'min-and-max-quantity-for-woocommerce' );
    ?>"
                                            class="tbl_product_fee table-outer tap-cas form-table shipping-method-table">
                                            <tbody>
                                                <?php 
    $rule_number = 0;
    ?>
                                                <tr id="mmqw_group_<?php 
    echo esc_attr( $group_number );
    ?>_row_<?php 
    echo esc_attr( $rule_number );
    ?>"
                                                    valign="top">
                                                    <th class="titledesc th_mmqw_rule_condition" scope="row">
                                                        <select
                                                            id="mmqw_qroup_<?php 
    echo esc_attr( $group_number );
    ?>_conditions_condition_<?php 
    echo esc_attr( $rule_number );
    ?>"
                                                            group-number="<?php 
    echo esc_attr( $group_number );
    ?>"
                                                            class="mmqw_rule_condition"
                                                            rel-id="<?php 
    echo esc_attr( $rule_number );
    ?>"
                                                            name="mmqw_group[<?php 
    echo esc_attr( $group_number );
    ?>][mmqw_rule][<?php 
    echo esc_attr( $rule_number );
    ?>][mmqw_rule_condition]">
                                                            <optgroup
                                                                label="<?php 
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
                                                            <optgroup
                                                                label="<?php 
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
                                                                <optgroup
                                                                    label="<?php 
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
                                                                <optgroup
                                                                    label="<?php 
    esc_attr_e( 'Limit Section', 'min-and-max-quantity-for-woocommerce' );
    ?>">
                                                                    <option value="limit_section_time_frame_in_pro">
                                                                        <?php 
    esc_html_e( 'Time Frame 🔒', 'min-and-max-quantity-for-woocommerce' );
    ?>
                                                                    </option>
                                                                </optgroup>
                                                                <optgroup
                                                                    label="<?php 
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
                                                        <select
                                                            name="mmqw_group[<?php 
    echo esc_attr( $group_number );
    ?>][mmqw_rule][<?php 
    echo esc_attr( $rule_number );
    ?>][mmqw_rule_condition_is]"
                                                            id="mmqw_qroup_<?php 
    echo esc_attr( $group_number );
    ?>_conditions_condition_is_<?php 
    echo esc_attr( $rule_number );
    ?>"
                                                            class="mmqw_rule_condition_is mmqw_rule_group_<?php 
    echo esc_attr( $group_number );
    ?>_condition_is_<?php 
    echo esc_attr( $rule_number );
    ?>">
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
                                                    <td class="condition-value"
                                                        id="group_<?php 
    echo esc_attr( $group_number );
    ?>_column_<?php 
    echo esc_attr( $rule_number );
    ?>"
                                                        class="condition-value">
                                                        <?php 
    $html = $mmqw_admin_object->mmqw_get_product_list( $group_number, $rule_number );
    echo wp_kses( $html, $mmqw_object::mmqw_allowed_html_tags() );
    ?>
                                                        <input type="hidden"
                                                            name="mmqw_group_<?php 
    echo esc_attr( $group_number );
    ?>_condition_key[<?php 
    echo esc_attr( $rule_number );
    ?>][]"
                                                            value="">
                                                    </td>
                                                    <td class="delete-td-row">
                                                        <a id="fee-delete-field" class="delete-row"
                                                            href="javascript:void(0);" title="Delete">
                                                            <i class="dashicons dashicons-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <input type="hidden"
                                                    name="mmqw_total_rows_group_<?php 
    echo esc_attr( $group_number );
    ?>"
                                                    id="mmqw_total_rows_group_<?php 
    echo esc_attr( $group_number );
    ?>"
                                                    value="1">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="shipping-method-rules mmqw-actions-section" style="margin-top: 15px;">
                                    <div class="sub-title">
                                        <div class="mmqw-conditions-title">
                                            <h2><?php 
    esc_html_e( 'Action', 'min-and-max-quantity-for-woocommerce' );
    ?>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="tap">
                                        <table id="tbl-shipping-method"
                                            class="tbl_product_fee table-outer tap-cas form-table shipping-method-table">
                                            <tbody>
                                                <tr valign="top">
                                                    <td>
                                                        <label
                                                            for="mmqw_group_<?php 
    echo esc_attr( $group_number );
    ?>_min_qty"><?php 
    esc_html_e( 'Min Quantity', 'min-and-max-quantity-for-woocommerce' );
    ?></label>
                                                        <input type="number"
                                                            name="mmqw_group[<?php 
    echo esc_attr( $group_number );
    ?>][mmqw_group_min_qty]"
                                                            class="text-class qty-class mmqw-min-qty-field"
                                                            id="mmqw_group_<?php 
    echo esc_attr( $group_number );
    ?>_min_qty"
                                                            placeholder="Min quantity" value="" min="0">
                                                        <?php 
    echo wp_kses( wc_help_tip( esc_html__( 'You can set a minimum product quantity to apply on product detail page and cart page.', 'min-and-max-quantity-for-woocommerce' ) ), array(
        'span' => $allowed_tooltip_html,
    ) );
    ?>
                                                    </td>
                                                    <td>
                                                        <label
                                                            for="mmqw_group_<?php 
    echo esc_attr( $group_number );
    ?>_max_qty"><?php 
    esc_html_e( 'Max Quantity', 'min-and-max-quantity-for-woocommerce' );
    ?></label>
                                                        <input type="number"
                                                            name="mmqw_group[<?php 
    echo esc_attr( $group_number );
    ?>][mmqw_group_max_qty]"
                                                            class="text-class qty-class mmqw-max-qty-field"
                                                            id="mmqw_group_<?php 
    echo esc_attr( $group_number );
    ?>_max_qty"
                                                            placeholder="Max quantity" value="" min="0">
                                                        <?php 
    echo wp_kses( wc_help_tip( esc_html__( 'You can set a maximum product quantity to apply on product detail page and cart page.', 'min-and-max-quantity-for-woocommerce' ) ), array(
        'span' => $allowed_tooltip_html,
    ) );
    ?>
                                                    </td>
                                                    <?php 
    ?>
                                                        <td>
                                                            <label
                                                                for="mmqw_group_<?php 
    echo esc_attr( $group_number );
    ?>_step_qty"><?php 
    esc_html_e( 'Step Quantity', 'min-and-max-quantity-for-woocommerce' );
    ?><span
                                                                    class="mmqw-pro-label"></span></label>
                                                            <input type="number"
                                                                name="mmqw_group[<?php 
    echo esc_attr( $group_number );
    ?>][mmqw_group_step_qty_in_pro]"
                                                                disabled class="text-class qty-class mmqw-step-qty-field"
                                                                id="mmqw_group_<?php 
    echo esc_attr( $group_number );
    ?>_step_qty"
                                                                placeholder="Step quantity" value="" min="0">
                                                            <?php 
    echo wp_kses( wc_help_tip( esc_html__( 'You can set a step product quantity to apply on product detail page and cart page.', 'min-and-max-quantity-for-woocommerce' ) ), array(
        'span' => $allowed_tooltip_html,
    ) );
    ?>
                                                        </td>
                                                        <td>
                                                            <label
                                                                for="mmqw_group_<?php 
    echo esc_attr( $group_number );
    ?>_default_qty"><?php 
    esc_html_e( 'Default Quantity', 'min-and-max-quantity-for-woocommerce' );
    ?><span
                                                                    class="mmqw-pro-label"></span></label>
                                                            <input type="number"
                                                                name="mmqw_group[<?php 
    echo esc_attr( $group_number );
    ?>][mmqw_group_default_qty_in_pro]"
                                                                disabled class="text-class qty-class mmqw-default-qty-field"
                                                                id="mmqw_group_<?php 
    echo esc_attr( $group_number );
    ?>_default_qty"
                                                                step="" placeholder="Default quantity" value="" min="" max="">
                                                            <?php 
    echo wp_kses( wc_help_tip( esc_html__( 'You can set a default product quantity to apply on product detail page.', 'min-and-max-quantity-for-woocommerce' ) ), array(
        'span' => $allowed_tooltip_html,
    ) );
    ?>
                                                        </td>
                                                        <td>
                                                            <label
                                                                for="mmqw_group_<?php 
    echo esc_attr( $group_number );
    ?>_default_qty_selector"><?php 
    esc_html_e( 'Quantity Selector', 'min-and-max-quantity-for-woocommerce' );
    ?><span
                                                                    class="mmqw-pro-label"></span></label>
                                                            <select
                                                                name="mmqw_group[<?php 
    echo esc_attr( $group_number );
    ?>][mmqw_group_default_qty_selector_in_pro]"
                                                                disabled class="mmqw_group_default_qty_selector">
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
}
?>
                </div>
                <?php 
$mmqw_total_groups_count = ( isset( $mmqw_total_groups ) && !empty( $mmqw_total_groups ) ? $mmqw_total_groups : 1 );
?>
                <input type="hidden" name="mmqw_total_groups" id="mmqw_total_groups"
                    value="<?php 
echo esc_attr( $mmqw_total_groups_count );
?>">
                <div class="handle-group-buttons">
                    <a id="mmqw-add-new-group" class="button button-secondary button-large" href="javascript:void(0);"
                        post-id="<?php 
echo esc_attr( $get_post_id );
?>"><?php 
esc_html_e( '+ Add Group', 'min-and-max-quantity-for-woocommerce' );
?></a>
                    <a id="mmqw-delete-group" class="button button-primary button-large" href="javascript:void(0);"
                        disabled
                        data-val="<?php 
esc_attr_e( 'Delete', 'min-and-max-quantity-for-woocommerce' );
?>"><?php 
esc_html_e( 'Delete', 'min-and-max-quantity-for-woocommerce' );
?></a>
                </div>
            </div>
        </div>
        <?php 
// Advanced Pricing Section end
?>
        <p class="submit">
            <input type="submit" name="submitFee" class="button button-primary button-large"
                value="<?php 
echo esc_attr( $submit_text );
?>">
        </p>
    </form>
</div>
</div>
</div>
</div>
</div>
