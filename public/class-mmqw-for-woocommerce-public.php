<?php

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.multidots.com
 * @since      1.0.0
 *
 * @package    Min_Max_Quantity_For_WooCommerce
 * @subpackage Min_Max_Quantity_For_WooCommerce/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Min_Max_Quantity_For_WooCommerce
 * @subpackage Min_Max_Quantity_For_WooCommerce/public
 * @author     thedotstore <hello@thedotstore.com>
 */
class MMQW_Min_Max_Quantity_For_WooCommerce_Public {
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
     * @since    1.0.0
     *
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version     The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function mmqw_enqueue_styles() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Min_Max_Quantity_For_WooCommerce_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Min_Max_Quantity_For_WooCommerce_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/mmqw-for-woocommerce-public.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function mmqw_enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/mmqw-for-woocommerce-public.js',
            array('jquery'),
            $this->version,
            false
        );
        wp_localize_script( $this->plugin_name, 'mmqw_plugin_vars', array(
            'one_quantity' => __( 'Quantity: ', 'min-and-max-quantity-for-woocommerce' ),
        ) );
    }

    /**
     * Set the Min/Max product quantity rules based on added rules by the admin
     *
     * @param $args
     * @param $product
     *
     * @return mixed
     */
    public function mmqw_quantity_input_args( $args, $product ) {
        global $sitepress;
        $default_lang = $this->mmqw_get_default_langugae_with_sitpress();
        /** @var Get all sorted min max rules $get_all_sm */
        $get_all_sm = $this->mmqw_get_all_min_max_rules();
        /** Execute the rules in order mentioned in admin */
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
                    $post_id_language_code = $this->mmqw_get_default_langugae_with_sitpress();
                }
                /** Check the rule language and set the Min/Max quantity argument as per the rules added by the admin */
                if ( $post_id_language_code === $default_lang ) {
                    $args = $this->mmqw_apply_rules( $sm_post_id, $args, $product );
                }
            }
        }
        /** Return the updated argument to the product page */
        return $args;
    }

    /**
     * Get the default language
     *
     * @return mixed
     */
    public function mmqw_get_default_langugae_with_sitpress() {
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
        return $default_lang;
    }

    /**
     * Get all the sorted rules
     *
     * @return array
     */
    public function mmqw_get_all_min_max_rules() {
        $default_lang = $this->mmqw_get_default_langugae_with_sitpress();
        $getSortOrder = get_option( 'sm_sortable_order_' . $default_lang );
        $sm_args = array(
            'post_type'        => 'wc_mmqw',
            'posts_per_page'   => -1,
            'orderby'          => 'menu_order',
            'order'            => 'ASC',
            'suppress_filters' => false,
        );
        $get_all_rules = new WP_Query($sm_args);
        /** @var get all the rules post object in variable $get_all_sm */
        $get_all_sm = $get_all_rules->posts;
        $sort_order = array();
        /** Sort the rules in same order as saved by the admin */
        if ( isset( $getSortOrder ) && !empty( $getSortOrder ) ) {
            foreach ( $getSortOrder as $sort ) {
                $sort_order[$sort] = array();
            }
        }
        /** Sort the rules in same order as saved by the admin */
        foreach ( $get_all_sm as $carrier_id => $carrier ) {
            $carrier_name = $carrier->ID;
            if ( array_key_exists( $carrier_name, $sort_order ) ) {
                $sort_order[$carrier_name][$carrier_id] = $get_all_sm[$carrier_id];
                unset($get_all_sm[$carrier_id]);
            }
        }
        /** Sort the rules in same order as saved by the admin */
        foreach ( $sort_order as $carriers ) {
            $get_all_sm = array_merge( $get_all_sm, $carriers );
        }
        /** Return the all the rules array with sorted array */
        return $get_all_sm;
    }

    /**
     * Add to cart vategory validation
     *
     * @param $sm_post_id
     * @param $product_id
     * @param $quantity
     * @param $rule_passed
     *
     * @return boolean
     */
    private function mmqw_add_to_cart_category_validation(
        $sm_post_id,
        $product_id,
        $quantity,
        $group_min_qty,
        $group_max_qty,
        $rule_condition,
        $rule_condition_is,
        $rule_condition_value,
        $rule_passed
    ) {
        if ( 'category' === $rule_condition ) {
            $variation = wc_get_product( $product_id );
            if ( "simple" === $variation->get_type() || "variable" === $variation->get_type() ) {
                $parent_product_cat = $variation->get_category_ids();
            } else {
                $product = wc_get_product( $variation->get_parent_id() );
                $parent_product_cat = $product->get_category_ids();
            }
            /** Get all current product category ids $all_cat_unique_ids */
            $all_cat_unique_ids = array_unique( $this->mmqw_get_array_flatten( $parent_product_cat ) );
            /** Min Max Rules selected category IDs $categories_ids */
            $categories_ids = $rule_condition_value;
            $min_qty = intval( $group_min_qty );
            $max_qty = intval( $group_max_qty );
            /** if max quantity is not set then add default value */
            if ( isset( $max_qty ) && empty( $max_qty ) ) {
                $max_qty = 9999;
            }
            if ( isset( $all_cat_unique_ids ) && is_array( $all_cat_unique_ids ) ) {
                $check_cat_count = count( array_intersect( $all_cat_unique_ids, $categories_ids ) );
                if ( 'is_equal_to' === $rule_condition_is ) {
                    if ( $min_qty >= 0 && $check_cat_count > 0 ) {
                        $cart_product_quantity = $this->mmqw_get_product_qry_from_cart_by_id( $product_id );
                        $cart_prod_quantity = ( 0 === $cart_product_quantity ? $cart_product_quantity : ($cart_product_quantity += $quantity) );
                        if ( $cart_prod_quantity > $max_qty ) {
                            $rule_passed = false;
                        } elseif ( $min_qty === $max_qty && $cart_prod_quantity >= $max_qty ) {
                            $rule_passed = false;
                        }
                    }
                } else {
                    if ( $min_qty >= 0 && $check_cat_count <= 0 ) {
                        $cart_product_quantity = $this->mmqw_get_product_qry_from_cart_by_id( $product_id );
                        $cart_prod_quantity = ( 0 === $cart_product_quantity ? $cart_product_quantity : ($cart_product_quantity += $quantity) );
                        if ( $cart_prod_quantity > $max_qty ) {
                            $rule_passed = false;
                        } elseif ( $min_qty === $max_qty && $cart_prod_quantity >= $max_qty ) {
                            $rule_passed = false;
                        }
                    }
                }
            }
        }
        return $rule_passed;
    }

    /**
     * Check and apply the rules
     *
     * @param $sm_post_id
     * @param $args
     * @param $product
     *
     * @return array
     */
    private function mmqw_apply_rules( $sm_post_id, $args, $product ) {
        $default_lang = $this->mmqw_get_default_langugae_with_sitpress();
        if ( empty( $sm_post_id ) ) {
            return $args;
        }
        $mmqw_rule_groups = get_post_meta( $sm_post_id, 'mmqw_rule_groups', true );
        if ( is_serialized( $mmqw_rule_groups ) ) {
            $mmqw_rule_groups = maybe_unserialize( $mmqw_rule_groups );
        } else {
            $mmqw_rule_groups = $mmqw_rule_groups;
        }
        if ( isset( $mmqw_rule_groups ) && !empty( $mmqw_rule_groups ) && is_array( $mmqw_rule_groups ) ) {
            foreach ( $mmqw_rule_groups as $mmqw_group ) {
                $mmqw_group_status = ( isset( $mmqw_group['mmqw_group_status'] ) ? $mmqw_group['mmqw_group_status'] : 'off' );
                if ( 'on' === $mmqw_group_status ) {
                    $mmqw_rules = ( isset( $mmqw_group['mmqw_rule'] ) ? $mmqw_group['mmqw_rule'] : array() );
                    $group_min_qty = ( isset( $mmqw_group['mmqw_group_min_qty'] ) ? $mmqw_group['mmqw_group_min_qty'] : '' );
                    $group_max_qty = ( isset( $mmqw_group['mmqw_group_max_qty'] ) ? $mmqw_group['mmqw_group_max_qty'] : '' );
                    if ( is_array( $mmqw_rules ) ) {
                        foreach ( $mmqw_rules as $mmqw_rule ) {
                            $rule_condition = ( isset( $mmqw_rule['mmqw_rule_condition'] ) ? $mmqw_rule['mmqw_rule_condition'] : '' );
                            $rule_condition_is = ( isset( $mmqw_rule['mmqw_rule_condition_is'] ) ? $mmqw_rule['mmqw_rule_condition_is'] : '' );
                            $rule_condition_value = ( isset( $mmqw_rule['mmqw_rule_condition_value'] ) ? $mmqw_rule['mmqw_rule_condition_value'] : array() );
                            $mmqw_rule_values = array_map( 'intval', $rule_condition_value );
                            if ( function_exists( 'icl_object_id' ) ) {
                                $current_product_id = icl_object_id(
                                    $product->get_id(),
                                    'product',
                                    true,
                                    $default_lang
                                );
                            } else {
                                $current_product_id = $product->get_id();
                            }
                            /** Quantity on Product section start here */
                            if ( 'product' === $rule_condition ) {
                                if ( 'is_equal_to' === $rule_condition_is ) {
                                    if ( $group_min_qty >= 0 && in_array( $current_product_id, $mmqw_rule_values, true ) ) {
                                        $args['min_value'] = intval( $group_min_qty );
                                        // Starting value
                                        $args['quantity'] = intval( $group_min_qty );
                                        /** Check if max value set and its greater than min value */
                                        if ( isset( $group_max_qty ) && $group_min_qty <= $group_max_qty ) {
                                            $args['max_value'] = $group_max_qty;
                                            // Ending value
                                        }
                                    }
                                } else {
                                    if ( $group_min_qty >= 0 && !in_array( $current_product_id, $mmqw_rule_values, true ) ) {
                                        $args['min_value'] = intval( $group_min_qty );
                                        // Starting value
                                        $args['quantity'] = intval( $group_min_qty );
                                        /** Check if max value set and its greater than min value */
                                        if ( isset( $group_max_qty ) && $group_min_qty <= $group_max_qty ) {
                                            $args['max_value'] = $group_max_qty;
                                            // Ending value
                                        }
                                    }
                                }
                            }
                            /** Quantity on Product section end here */
                            /** Quantity on Variable product section start here */
                            $args = $this->mmqw_apply_variation_rules(
                                $sm_post_id,
                                $args,
                                $product,
                                $rule_condition,
                                $rule_condition_is,
                                $mmqw_rule_values,
                                $group_min_qty,
                                $group_max_qty
                            );
                            /** Quantity on Variable product section end here */
                            /** Quantity on Category section start here */
                            $args = $this->mmqw_apply_category_rules(
                                $sm_post_id,
                                $args,
                                $product,
                                $rule_condition,
                                $rule_condition_is,
                                $mmqw_rule_values,
                                $group_min_qty,
                                $group_max_qty
                            );
                            /** Quantity on Category section end here */
                        }
                    }
                }
            }
        }
        return $args;
    }

    /**
     * Find unique id based on given array
     *
     * @param array $array
     *
     * @return array $result if $array is empty it will return false otherwise return array as $result
     * @since    1.0.0
     *
     */
    private function mmqw_get_array_flatten( $array ) {
        if ( !is_array( $array ) ) {
            return false;
        }
        $result = array();
        foreach ( $array as $key => $value ) {
            if ( is_array( $value ) ) {
                $result = array_merge( $result, $this->mmqw_get_array_flatten( $value ) );
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Check the product variation min max rules
     *
     * @param $args
     * @param $product
     *
     * @return mixed
     */
    public function mmqw_woocommerce_quantity_min_max_variation( $args, $product ) {
        global $sitepress;
        $default_lang = $this->mmqw_get_default_langugae_with_sitpress();
        /** @var Get all the rules array with sorted order $get_all_sm */
        $get_all_sm = $this->mmqw_get_all_min_max_rules();
        /** Execute the rules in order mentioned in admin */
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
                    $post_id_language_code = $this->mmqw_get_default_langugae_with_sitpress();
                }
                /** Check the rule language and set the Min/Max quantity argument as per the rules added by the admin */
                if ( $post_id_language_code === $default_lang ) {
                    $mmqw_rule_groups = get_post_meta( $sm_post_id, 'mmqw_rule_groups', true );
                    if ( is_serialized( $mmqw_rule_groups ) ) {
                        $mmqw_rule_groups = maybe_unserialize( $mmqw_rule_groups );
                    } else {
                        $mmqw_rule_groups = $mmqw_rule_groups;
                    }
                    if ( isset( $mmqw_rule_groups ) && !empty( $mmqw_rule_groups ) && is_array( $mmqw_rule_groups ) ) {
                        foreach ( $mmqw_rule_groups as $mmqw_group ) {
                            $mmqw_group_status = ( isset( $mmqw_group['mmqw_group_status'] ) && !empty( $mmqw_group['mmqw_group_status'] ) ? $mmqw_group['mmqw_group_status'] : 'off' );
                            if ( 'on' === $mmqw_group_status ) {
                                $mmqw_rules = ( isset( $mmqw_group['mmqw_rule'] ) && !empty( $mmqw_group['mmqw_rule'] ) ? $mmqw_group['mmqw_rule'] : array() );
                                $group_min_qty = ( isset( $mmqw_group['mmqw_group_min_qty'] ) && !empty( $mmqw_group['mmqw_group_min_qty'] ) ? $mmqw_group['mmqw_group_min_qty'] : '' );
                                $group_max_qty = ( isset( $mmqw_group['mmqw_group_max_qty'] ) && !empty( $mmqw_group['mmqw_group_max_qty'] ) ? $mmqw_group['mmqw_group_max_qty'] : '' );
                                if ( is_array( $mmqw_rules ) ) {
                                    foreach ( $mmqw_rules as $mmqw_rule ) {
                                        $rule_condition = ( isset( $mmqw_rule['mmqw_rule_condition'] ) && !empty( $mmqw_rule['mmqw_rule_condition'] ) ? $mmqw_rule['mmqw_rule_condition'] : '' );
                                        $rule_condition_is = ( isset( $mmqw_rule['mmqw_rule_condition_is'] ) && !empty( $mmqw_rule['mmqw_rule_condition_is'] ) ? $mmqw_rule['mmqw_rule_condition_is'] : '' );
                                        $rule_condition_value = ( isset( $mmqw_rule['mmqw_rule_condition_value'] ) && !empty( $mmqw_rule['mmqw_rule_condition_value'] ) ? $mmqw_rule['mmqw_rule_condition_value'] : array() );
                                        $mmqw_rule_values = array_map( 'intval', $rule_condition_value );
                                        $args = $this->mmqw_apply_variation_rules(
                                            $sm_post_id,
                                            $args,
                                            $product,
                                            $rule_condition,
                                            $rule_condition_is,
                                            $mmqw_rule_values,
                                            $group_min_qty,
                                            $group_max_qty
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        /** Return the updated argument to the product page */
        return $args;
    }

    /**
     * Check the product variation min max rule
     *
     * @param $sm_post_id
     * @param $args
     * @param $product
     *
     * @return mixed
     */
    private function mmqw_apply_variation_rules(
        $sm_post_id,
        $args,
        $product,
        $rule_condition,
        $rule_condition_is,
        $rule_condition_value,
        $group_min_qty,
        $group_max_qty
    ) {
        $current_variation_id = ( isset( $args['variation_id'] ) ? $args['variation_id'] : $product->get_id() );
        $variation_ids = $rule_condition_value;
        $min_qty = intval( $group_min_qty );
        $max_qty = intval( $group_max_qty );
        if ( 'variable_product' === $rule_condition ) {
            if ( 'is_equal_to' === $rule_condition_is ) {
                /** Check the min and max value for each product variation and set accordingly */
                if ( $min_qty >= 0 && in_array( $current_variation_id, $variation_ids, true ) ) {
                    $args['min_qty'] = $min_qty;
                    // Starting value
                    $args['min_value'] = $min_qty;
                    // Starting value
                    /** Check if max value set and its greater than min value */
                    if ( isset( $max_qty ) && $min_qty <= $max_qty ) {
                        $args['max_qty'] = $max_qty;
                        // Ending value
                        $args['max_value'] = $max_qty;
                        // Starting value
                    }
                }
            } else {
                /** Check the min and max value for each product variation and set accordingly */
                if ( $min_qty >= 0 && !in_array( $current_variation_id, $variation_ids, true ) ) {
                    $args['min_qty'] = $min_qty;
                    // Starting value
                    $args['min_value'] = $min_qty;
                    // Starting value
                    /** Check if max value set and its greater than min value */
                    if ( isset( $max_qty ) && $min_qty <= $max_qty ) {
                        $args['max_qty'] = $max_qty;
                        // Ending value
                        $args['max_value'] = $max_qty;
                        // Starting value
                    }
                }
            }
        }
        /** Quantity on Category section start here */
        $args = $this->mmqw_apply_category_rules(
            $sm_post_id,
            $args,
            $product,
            $rule_condition,
            $rule_condition_is,
            $rule_condition_value,
            $group_min_qty,
            $group_max_qty
        );
        /** Quantity on Category section end here */
        return $args;
    }

    /**
     * Apply the category rules
     *
     * @param $sm_post_id
     * @param $args
     * @param $product
     * @param $is_variation
     *
     * @return array
     */
    private function mmqw_apply_category_rules(
        $sm_post_id,
        $args,
        $product,
        $rule_condition,
        $rule_condition_is,
        $rule_condition_value,
        $group_min_qty,
        $group_max_qty
    ) {
        if ( 'category' === $rule_condition ) {
            $product_id = $product->get_id();
            if ( !is_product() && ('variable' === $product->get_type() || 'variation' === $product->get_type()) ) {
                $product_id = $product->get_parent_id();
            }
            $all_product_cate_id_list = wp_get_post_terms( $product_id, 'product_cat', array(
                'fields' => 'ids',
            ) );
            /** @var Get all current product category ids $all_cat_unique_ids */
            $all_cat_unique_ids = array_unique( $this->mmqw_get_array_flatten( $all_product_cate_id_list ) );
            /** @var Min Max Rules selected category IDs $categories_ids */
            $categories_ids = $rule_condition_value;
            $min_qty = intval( $group_min_qty );
            $max_qty = intval( $group_max_qty );
            if ( isset( $all_cat_unique_ids ) && is_array( $all_cat_unique_ids ) ) {
                $check_cat_count = count( array_intersect( $all_cat_unique_ids, $categories_ids ) );
                if ( 'is_equal_to' === $rule_condition_is ) {
                    if ( $min_qty >= 0 && $check_cat_count > 0 ) {
                        $args['min_qty'] = $min_qty;
                        // Starting value
                        $args['min_value'] = $min_qty;
                        // Starting value
                        $args['quantity'] = $min_qty;
                        /** Check if max value set and its greater than min value */
                        if ( isset( $max_qty ) && $min_qty <= $max_qty ) {
                            $args['max_qty'] = $max_qty;
                            // Ending value
                            $args['max_value'] = $max_qty;
                            // Ending value
                        }
                    }
                } else {
                    if ( $min_qty >= 0 && $check_cat_count <= 0 ) {
                        $args['min_qty'] = $min_qty;
                        // Starting value
                        $args['min_value'] = $min_qty;
                        // Starting value
                        $args['quantity'] = $min_qty;
                        /** Check if max value set and its greater than min value */
                        if ( isset( $max_qty ) && $min_qty <= $max_qty ) {
                            $args['max_qty'] = $max_qty;
                            // Ending value
                            $args['max_value'] = $max_qty;
                            // Ending value
                        }
                    }
                }
            }
        }
        return $args;
    }

    /**
     * Check Min/Max rules before proceed to checkout
     *
     * @since 3.1.3
     */
    public function mmqw_min_max_quantities_proceed_to_checkout_conditions() {
        global $woocommerce;
        $min_order_quantity = get_option( 'min_order_quantity' );
        $max_order_quantity = get_option( 'max_order_quantity' );
        $min_order_value = get_option( 'min_order_value' );
        $max_order_value = get_option( 'max_order_value' );
        $min_items_quantity = get_option( 'min_items_quantity' );
        $max_items_quantity = get_option( 'max_items_quantity' );
        $min_order_quantity_reached = ( !empty( get_option( 'min_order_quantity_reached' ) ) ? get_option( 'min_order_quantity_reached' ) : __( 'The minimum allows order quantity is {MIN_ORDER_QTY} and you have {ORDER_QTY} in your cart.', 'min-and-max-quantity-for-woocommerce' ) );
        $max_order_quantity_exceeded = ( !empty( get_option( 'max_order_quantity_exceeded' ) ) ? get_option( 'max_order_quantity_exceeded' ) : __( 'The maximum allows order quantity is {MAX_ORDER_QTY} and you have {ORDER_QTY} in your cart.', 'min-and-max-quantity-for-woocommerce' ) );
        $min_order_value_reached = ( !empty( get_option( 'min_order_value_reached' ) ) ? get_option( 'min_order_value_reached' ) : __( 'The minimum cart value required is {MIN_CART_VALUE} and you have {CART_VALUE} in your cart.', 'min-and-max-quantity-for-woocommerce' ) );
        $max_order_value_exceeded = ( !empty( get_option( 'max_order_value_exceeded' ) ) ? get_option( 'max_order_value_exceeded' ) : __( 'The maximum cart value required is {MAX_CART_VALUE} and you have {CART_VALUE} in your cart.', 'min-and-max-quantity-for-woocommerce' ) );
        $min_order_item_reached = ( !empty( get_option( 'min_order_item_reached' ) ) ? get_option( 'min_order_item_reached' ) : __( 'The minimum order item required is {MIN_ORDER_ITEM} for each product in your cart.', 'min-and-max-quantity-for-woocommerce' ) );
        $max_order_item_exceeded = ( !empty( get_option( 'max_order_item_exceeded' ) ) ? get_option( 'max_order_item_exceeded' ) : __( 'The maximum order item should be {MAX_ORDER_ITEM} for each product in your cart.', 'min-and-max-quantity-for-woocommerce' ) );
        //for cart total
        $total_cart_quantity = $woocommerce->cart->cart_contents_count;
        $total_cart_amount = '';
        if ( 'yes' === get_option( 'woocommerce_prices_include_tax', 'yes' ) ) {
            $total_cart_amount = floatval( WC()->cart->cart_contents_total ) + floatval( WC()->cart->get_cart_contents_tax() );
        } else {
            $total_cart_amount = floatval( WC()->cart->cart_contents_total );
        }
        // end: check exclude product list and modify the total_cart_quantity based on exclude_product_list
        /**
         * Check Min/Max order quantity
         */
        if ( !empty( $min_order_quantity ) && $total_cart_quantity < $min_order_quantity ) {
            $min_order_quantity_reached = str_replace( "{MIN_ORDER_QTY}", $min_order_quantity, $min_order_quantity_reached );
            $min_order_quantity_reached = str_replace( "{ORDER_QTY}", $total_cart_quantity, $min_order_quantity_reached );
            wc_clear_notices();
            wc_add_notice( __( $min_order_quantity_reached, 'min-and-max-quantity-for-woocommerce' ), 'error' );
            if ( is_page( 'checkout' ) ) {
                wc_clear_notices();
                wp_redirect( home_url( 'cart' ) );
                exit;
            }
        }
        if ( !empty( $max_order_quantity ) && $total_cart_quantity > $max_order_quantity ) {
            $max_order_quantity_exceeded = str_replace( "{MAX_ORDER_QTY}", $max_order_quantity, $max_order_quantity_exceeded );
            $max_order_quantity_exceeded = str_replace( "{ORDER_QTY}", $total_cart_quantity, $max_order_quantity_exceeded );
            wc_clear_notices();
            wc_add_notice( __( $max_order_quantity_exceeded, 'min-and-max-quantity-for-woocommerce' ), 'error' );
            if ( is_page( 'checkout' ) ) {
                wc_clear_notices();
                wp_redirect( home_url( 'cart' ) );
                exit;
            }
        }
        /** Country based Min Max Order quantity management section start here */
        $customer_shipping_country = '';
        if ( isset( WC()->session->get( 'customer' )['shipping_country'] ) ) {
            $customer_shipping_country = WC()->session->get( 'customer' )['shipping_country'];
        }
        if ( isset( $customer_shipping_country ) && !empty( $customer_shipping_country ) ) {
            $this->mmqw_apply_country_based_rules( $customer_shipping_country, $total_cart_quantity );
        }
        // end: check exclude product list and modify the total_cart_amount based on exclude_product_list for cart total
        /**
         * Check Min/Max order value
         */
        if ( !empty( $min_order_value ) && $total_cart_amount < $min_order_value ) {
            $min_order_value_reached = str_replace( "{MIN_CART_VALUE}", wc_price( $min_order_value ), $min_order_value_reached );
            $min_order_value_reached = str_replace( "{CART_VALUE}", wc_price( $total_cart_amount ), $min_order_value_reached );
            wc_clear_notices();
            wc_add_notice( __( $min_order_value_reached, 'min-and-max-quantity-for-woocommerce' ), 'error' );
            if ( is_page( 'checkout' ) ) {
                wc_clear_notices();
                wp_redirect( home_url( 'cart' ) );
                exit;
            }
        }
        if ( !empty( $max_order_value ) && $total_cart_amount > $max_order_value ) {
            $max_order_value_exceeded = str_replace( "{MAX_CART_VALUE}", wc_price( $max_order_value ), $max_order_value_exceeded );
            $max_order_value_exceeded = str_replace( "{CART_VALUE}", wc_price( $total_cart_amount ), $max_order_value_exceeded );
            wc_clear_notices();
            wc_add_notice( __( $max_order_value_exceeded, 'min-and-max-quantity-for-woocommerce' ), 'error' );
            if ( is_page( 'checkout' ) ) {
                wc_clear_notices();
                wp_redirect( home_url( 'cart' ) );
                exit;
            }
        }
        /**
         * Check the Min/Max item for each product
         */
        foreach ( $woocommerce->cart->get_cart() as $val ) {
            $_product = $val['data'];
            $cart_product_id = $_product->get_id();
            // end:check exclude product list and modify the cart_product_quantity based on exclude_product_list
            $cart_product_quantity = $this->mmqw_get_product_qry_from_cart_by_id( $cart_product_id );
            if ( !empty( $min_items_quantity ) && $cart_product_quantity < $min_items_quantity ) {
                $min_order_item_reached = str_replace( "{MIN_ORDER_ITEM}", $min_items_quantity, $min_order_item_reached );
                wc_clear_notices();
                wc_add_notice( __( $min_order_item_reached, 'min-and-max-quantity-for-woocommerce' ), 'error' );
            }
            if ( !empty( $max_items_quantity ) && $cart_product_quantity > $max_items_quantity ) {
                $max_order_item_exceeded = str_replace( "{MAX_ORDER_ITEM}", $max_items_quantity, $max_order_item_exceeded );
                wc_clear_notices();
                wc_add_notice( __( $max_order_item_exceeded, 'min-and-max-quantity-for-woocommerce' ), 'error' );
            }
        }
    }

    /**
     * Validation on add to cart process
     */
    public function mmqw_add_to_cart_validation(
        $passed,
        $product_id,
        $quantity,
        $variation_id = null
    ) {
        $default_lang = $this->mmqw_get_default_langugae_with_sitpress();
        $product_id = ( !empty( $variation_id ) && 0 !== $variation_id ? $variation_id : $product_id );
        $product = wc_get_product( $product_id );
        $product_type = $product->get_type();
        /** Get all sorted min max rules $get_all_sm */
        $get_all_sm = $this->mmqw_get_all_min_max_rules();
        /** Execute the rules in order mentioned in admin */
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
                    $post_id_language_code = $this->mmqw_get_default_langugae_with_sitpress();
                }
                /** Check the rule language and set the Min/Max quantity argument as per the rules added by the admin */
                if ( $post_id_language_code === $default_lang ) {
                    if ( function_exists( 'icl_object_id' ) ) {
                        $current_product_id = icl_object_id(
                            $product_id,
                            'product',
                            true,
                            $default_lang
                        );
                    } else {
                        $current_product_id = $product_id;
                    }
                    $mmqw_rule_groups = get_post_meta( $sm_post_id, 'mmqw_rule_groups', true );
                    if ( is_serialized( $mmqw_rule_groups ) ) {
                        $mmqw_rule_groups = maybe_unserialize( $mmqw_rule_groups );
                    } else {
                        $mmqw_rule_groups = $mmqw_rule_groups;
                    }
                    if ( isset( $mmqw_rule_groups ) && !empty( $mmqw_rule_groups ) && is_array( $mmqw_rule_groups ) ) {
                        foreach ( $mmqw_rule_groups as $mmqw_group ) {
                            $mmqw_group_status = ( isset( $mmqw_group['mmqw_group_status'] ) && !empty( $mmqw_group['mmqw_group_status'] ) ? $mmqw_group['mmqw_group_status'] : 'off' );
                            if ( 'on' === $mmqw_group_status ) {
                                $mmqw_rules = ( isset( $mmqw_group['mmqw_rule'] ) && !empty( $mmqw_group['mmqw_rule'] ) ? $mmqw_group['mmqw_rule'] : array() );
                                $group_min_qty = ( isset( $mmqw_group['mmqw_group_min_qty'] ) && !empty( $mmqw_group['mmqw_group_min_qty'] ) ? $mmqw_group['mmqw_group_min_qty'] : '' );
                                $group_max_qty = ( isset( $mmqw_group['mmqw_group_max_qty'] ) && !empty( $mmqw_group['mmqw_group_max_qty'] ) ? $mmqw_group['mmqw_group_max_qty'] : '' );
                                if ( is_array( $mmqw_rules ) ) {
                                    foreach ( $mmqw_rules as $mmqw_rule ) {
                                        $rule_condition = ( isset( $mmqw_rule['mmqw_rule_condition'] ) && !empty( $mmqw_rule['mmqw_rule_condition'] ) ? $mmqw_rule['mmqw_rule_condition'] : '' );
                                        $rule_condition_is = ( isset( $mmqw_rule['mmqw_rule_condition_is'] ) && !empty( $mmqw_rule['mmqw_rule_condition_is'] ) ? $mmqw_rule['mmqw_rule_condition_is'] : '' );
                                        $rule_condition_value = ( isset( $mmqw_rule['mmqw_rule_condition_value'] ) && !empty( $mmqw_rule['mmqw_rule_condition_value'] ) ? $mmqw_rule['mmqw_rule_condition_value'] : array() );
                                        $mmqw_rule_values = array_map( 'intval', $rule_condition_value );
                                        /** Quantity on Product section Start here */
                                        if ( "simple" === $product_type ) {
                                            if ( 'product' === $rule_condition ) {
                                                $product_ids = $mmqw_rule_values;
                                                $min_qty = intval( $group_min_qty );
                                                $max_qty = intval( $group_max_qty );
                                                /** if max quantity is not set then add default value */
                                                if ( isset( $max_qty ) && empty( $max_qty ) ) {
                                                    $max_qty = 9999;
                                                }
                                                if ( 'is_equal_to' === $rule_condition_is ) {
                                                    if ( $min_qty >= 0 && in_array( $current_product_id, $product_ids, true ) ) {
                                                        $cart_product_quantity = $this->mmqw_get_product_qry_from_cart_by_id( $current_product_id );
                                                        $cart_prod_quantity = ( 0 === $cart_product_quantity ? $cart_product_quantity : ($cart_product_quantity += $quantity) );
                                                        if ( $cart_prod_quantity > $max_qty ) {
                                                            $passed = false;
                                                            break;
                                                        } elseif ( $min_qty === $max_qty && $cart_prod_quantity > $max_qty ) {
                                                            $passed = false;
                                                            break;
                                                        }
                                                    }
                                                } else {
                                                    if ( $min_qty >= 0 && !in_array( $current_product_id, $product_ids, true ) ) {
                                                        $cart_product_quantity = $this->mmqw_get_product_qry_from_cart_by_id( $current_product_id );
                                                        $cart_prod_quantity = ( 0 === $cart_product_quantity ? $cart_product_quantity : ($cart_product_quantity += $quantity) );
                                                        if ( $cart_prod_quantity > $max_qty ) {
                                                            $passed = false;
                                                            break;
                                                        } elseif ( $min_qty === $max_qty && $cart_prod_quantity > $max_qty ) {
                                                            $passed = false;
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                            $passed = $this->mmqw_add_to_cart_category_validation(
                                                $sm_post_id,
                                                $current_product_id,
                                                $quantity,
                                                $group_min_qty,
                                                $group_max_qty,
                                                $rule_condition,
                                                $rule_condition_is,
                                                $mmqw_rule_values,
                                                $passed
                                            );
                                        } elseif ( "variation" === $product_type || "variable" === $product_type ) {
                                            if ( 'variable_product' === $rule_condition ) {
                                                $variation_ids = $mmqw_rule_values;
                                                $min_qty = intval( $group_min_qty );
                                                $max_qty = intval( $group_max_qty );
                                                /** if max quantity is not set then add default value */
                                                if ( isset( $max_qty ) && empty( $max_qty ) ) {
                                                    $max_qty = 9999;
                                                }
                                                if ( 'is_equal_to' === $rule_condition_is ) {
                                                    /** Check the min and max value for each product variation and set accordingly */
                                                    if ( $min_qty >= 0 && in_array( $current_product_id, $variation_ids, true ) ) {
                                                        $cart_product_quantity = $this->mmqw_get_product_qry_from_cart_by_id( $current_product_id );
                                                        $cart_prod_quantity = ( 0 === $cart_product_quantity ? $cart_product_quantity : ($cart_product_quantity += $quantity) );
                                                        if ( $cart_prod_quantity > $max_qty ) {
                                                            $passed = false;
                                                            break;
                                                        } elseif ( $min_qty === $max_qty && $cart_prod_quantity > $max_qty ) {
                                                            $passed = false;
                                                            break;
                                                        }
                                                    }
                                                } else {
                                                    /** Check the min and max value for each product variation and set accordingly */
                                                    if ( $min_qty >= 0 && !in_array( $current_product_id, $variation_ids, true ) ) {
                                                        $cart_product_quantity = $this->mmqw_get_product_qry_from_cart_by_id( $current_product_id );
                                                        $cart_prod_quantity = ( 0 === $cart_product_quantity ? $cart_product_quantity : ($cart_product_quantity += $quantity) );
                                                        if ( $cart_prod_quantity > $max_qty ) {
                                                            $passed = false;
                                                            break;
                                                        } elseif ( $min_qty === $max_qty && $cart_prod_quantity > $max_qty ) {
                                                            $passed = false;
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                            $passed = $this->mmqw_add_to_cart_category_validation(
                                                $sm_post_id,
                                                $current_product_id,
                                                $quantity,
                                                $group_min_qty,
                                                $group_max_qty,
                                                $rule_condition,
                                                $rule_condition_is,
                                                $mmqw_rule_values,
                                                $passed
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if ( !$passed ) {
            $notice_text = apply_filters( 'mmqw_product_notice_page_for_quantiy_limit', __( 'Sorry! you reached maximum limit for this product!', 'min-and-max-quantity-for-woocommerce' ) );
            wc_add_notice( __( $notice_text, 'min-and-max-quantity-for-woocommerce' ), 'error' );
        }
        return $passed;
    }

    /**
     * Apply country based rules
     */
    private function mmqw_apply_country_based_rules( $customer_shipping_country = '', $total_cart_quantity = '' ) {
        global $sitepress;
        $default_lang = $this->mmqw_get_default_langugae_with_sitpress();
        $rule_matched = false;
        $min_order_quantity_reached = ( !empty( get_option( 'min_order_quantity_reached' ) ) ? get_option( 'min_order_quantity_reached' ) : __( 'The minimum allows order quantity is {MIN_ORDER_QTY} and you have {ORDER_QTY} in your cart.', 'min-and-max-quantity-for-woocommerce' ) );
        $max_order_quantity_exceeded = ( !empty( get_option( 'max_order_quantity_exceeded' ) ) ? get_option( 'max_order_quantity_exceeded' ) : __( 'The maximum allows order quantity is {MAX_ORDER_QTY} and you have {ORDER_QTY} in your cart.', 'min-and-max-quantity-for-woocommerce' ) );
        /** @var Get all sorted min max rules $get_all_sm */
        $get_all_sm = $this->mmqw_get_all_min_max_rules();
        /** Execute the rules in order mentioned in admin */
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
                $mmqw_rule_groups = get_post_meta( $sm_post_id, 'mmqw_rule_groups', true );
                if ( is_serialized( $mmqw_rule_groups ) ) {
                    $mmqw_rule_groups = maybe_unserialize( $mmqw_rule_groups );
                } else {
                    $mmqw_rule_groups = $mmqw_rule_groups;
                }
                if ( isset( $mmqw_rule_groups ) && !empty( $mmqw_rule_groups ) && is_array( $mmqw_rule_groups ) ) {
                    foreach ( $mmqw_rule_groups as $mmqw_group ) {
                        $mmqw_group_status = ( isset( $mmqw_group['mmqw_group_status'] ) && !empty( $mmqw_group['mmqw_group_status'] ) ? $mmqw_group['mmqw_group_status'] : 'off' );
                        if ( 'on' === $mmqw_group_status ) {
                            $mmqw_rules = ( isset( $mmqw_group['mmqw_rule'] ) && !empty( $mmqw_group['mmqw_rule'] ) ? $mmqw_group['mmqw_rule'] : array() );
                            $min_order_quantity = ( isset( $mmqw_group['mmqw_group_min_qty'] ) && !empty( $mmqw_group['mmqw_group_min_qty'] ) ? $mmqw_group['mmqw_group_min_qty'] : '' );
                            $max_order_quantity = ( isset( $mmqw_group['mmqw_group_max_qty'] ) && !empty( $mmqw_group['mmqw_group_max_qty'] ) ? $mmqw_group['mmqw_group_max_qty'] : '' );
                            // Fetch the last satisfied args from session
                            $min_order_quantity = WC()->session->get( 'mmqw_group_min_qty' );
                            $max_order_quantity = WC()->session->get( 'mmqw_group_max_qty' );
                            if ( is_array( $mmqw_rules ) ) {
                                foreach ( $mmqw_rules as $mmqw_rule ) {
                                    $rule_condition = ( isset( $mmqw_rule['mmqw_rule_condition'] ) && !empty( $mmqw_rule['mmqw_rule_condition'] ) ? $mmqw_rule['mmqw_rule_condition'] : '' );
                                    $rule_condition_is = ( isset( $mmqw_rule['mmqw_rule_condition_is'] ) && !empty( $mmqw_rule['mmqw_rule_condition_is'] ) ? $mmqw_rule['mmqw_rule_condition_is'] : '' );
                                    $rule_condition_value = ( isset( $mmqw_rule['mmqw_rule_condition_value'] ) && !empty( $mmqw_rule['mmqw_rule_condition_value'] ) ? $mmqw_rule['mmqw_rule_condition_value'] : array() );
                                    if ( 'country' === $rule_condition ) {
                                        if ( 'is_equal_to' === $rule_condition_is ) {
                                            if ( $min_order_quantity > 0 && in_array( $customer_shipping_country, $rule_condition_value, true ) ) {
                                                $rule_matched = true;
                                            }
                                        } else {
                                            if ( $min_order_quantity > 0 && !in_array( $customer_shipping_country, $rule_condition_value, true ) ) {
                                                $rule_matched = true;
                                            }
                                        }
                                        if ( $rule_matched ) {
                                            if ( !empty( $min_order_quantity ) && $total_cart_quantity < $min_order_quantity ) {
                                                $min_order_quantity_reached = str_replace( "{MIN_ORDER_QTY}", $min_order_quantity, $min_order_quantity_reached );
                                                $min_order_quantity_reached = str_replace( "{ORDER_QTY}", $total_cart_quantity, $min_order_quantity_reached );
                                                wc_clear_notices();
                                                wc_add_notice( __( $min_order_quantity_reached, 'min-and-max-quantity-for-woocommerce' ), 'error' );
                                                if ( is_page( 'checkout' ) ) {
                                                    wc_clear_notices();
                                                    wp_redirect( home_url( 'cart' ) );
                                                    exit;
                                                }
                                            }
                                            if ( !empty( $max_order_quantity ) && $total_cart_quantity > $max_order_quantity ) {
                                                $max_order_quantity_exceeded = str_replace( "{MAX_ORDER_QTY}", $max_order_quantity, $max_order_quantity_exceeded );
                                                $max_order_quantity_exceeded = str_replace( "{ORDER_QTY}", $total_cart_quantity, $max_order_quantity_exceeded );
                                                wc_clear_notices();
                                                wc_add_notice( __( $max_order_quantity_exceeded, 'min-and-max-quantity-for-woocommerce' ), 'error' );
                                                if ( is_page( 'checkout' ) ) {
                                                    wc_clear_notices();
                                                    wp_redirect( home_url( 'cart' ) );
                                                    exit;
                                                }
                                            }
                                        }
                                    }
                                    $rule_matched = false;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Get the total quantity from the cart by given product ID
     *
     * @param $product_id
     *
     * @return int
     */
    private function mmqw_get_product_qry_from_cart_by_id( $product_id ) {
        global $woocommerce;
        foreach ( $woocommerce->cart->get_cart() as $val ) {
            $_product = $val['data'];
            if ( $product_id === $_product->get_id() ) {
                return $val['quantity'];
            }
        }
        return 0;
    }

}
