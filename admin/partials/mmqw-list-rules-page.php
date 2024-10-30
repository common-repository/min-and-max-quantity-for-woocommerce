<?php
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

////////////////////////
////// New Layout //////
////////////////////////

/**
 * MMQW_Rule_Listing_Page class.
 */
if (!class_exists('MMQW_Rule_Listing_Page')) {

	class MMQW_Rule_Listing_Page
	{

		/**
		 * Output the Admin UI
		 *
		 * @since 3.5
		 */
		const post_type = 'wc_mmqw';
		private static $admin_object = null;

		/**
		 * Display output
		 *
		 * @since 3.5
		 *
		 * @uses MMQW_Min_Max_Quantity_For_WooCommerce_Admin
		 * @uses mmqw_rule_save_method
		 * @uses mmqw_rule_add_new_rule_form
		 * @uses mmqw_rule_edit_method_screen
		 * @uses mmqw_rule_delete_method
		 * @uses mmqw_rule_duplicate_method
		 * @uses mmqw_rule_list_methods_screen
		 * @uses MMQW_Min_Max_Quantity_For_WooCommerce_Admin::mmqw_updated_message()
		 *
		 * @access   public
		 */
		public static function mmqw_rule_output()
		{
			self::$admin_object = new MMQW_Min_Max_Quantity_For_WooCommerce_Admin('', '');
			$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$post_id_request = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
			$cust_nonce = filter_input(INPUT_GET, 'cust_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$get_mmqw_add = filter_input(INPUT_GET, '_wpnonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

			$message = filter_input(INPUT_GET, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			if (isset($message) && !empty($message)) {
				self::$admin_object->mmqw_updated_message($message, "");
			}
			if (isset($action) && !empty($action)) {
				if ('add' === $action) {
					self::mmqw_rule_save_method();
					self::mmqw_rule_add_new_rule_form();
				} elseif ('edit' === $action) {
					if (isset($cust_nonce) && !empty($cust_nonce)) {
						$getnonce = wp_verify_nonce($cust_nonce, 'edit_' . $post_id_request);
						if (isset($getnonce) && 1 === $getnonce) {
							self::mmqw_rule_save_method();
							self::mmqw_rule_edit_method();
						} else {
							wp_safe_redirect(add_query_arg(
								array(
									'page' => 'mmqw-rules-list'
								),
								admin_url('admin.php')
							));
							exit;
						}
					} elseif (isset($get_mmqw_add) && !empty($get_mmqw_add)) {
						if (!wp_verify_nonce($get_mmqw_add, 'mmqw_add')) {
							$message = 'nonce_check';
						} else {
							self::mmqw_rule_save_method();
							self::mmqw_rule_edit_method();
						}
					}
				} elseif ('delete' === $action) {
					self::mmqw_rule_delete_method($post_id_request);
				} elseif ('duplicate' === $action) {
					self::mmqw_rule_duplicate_method($post_id_request);
				} else {
					self::mmqw_rule_list_methods_screen();
				}
			} else {
				self::mmqw_rule_list_methods_screen();
			}

		}

		/**
		 * Delete Min/Max Rule
		 *
		 * @param int $id
		 *
		 * @access   public
		 * @uses MMQW_Min_Max_Quantity_For_WooCommerce_Admin::mmqw_updated_message()
		 *
		 * @since    1.1.0
		 *
		 */
		public static function mmqw_rule_delete_method($id)
		{
			$cust_nonce = filter_input(INPUT_GET, 'cust_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

			$getnonce = wp_verify_nonce($cust_nonce, 'del_' . $id);
			if (isset($getnonce) && 1 === $getnonce) {
				wp_delete_post($id);
				wp_safe_redirect(add_query_arg(
					array(
						'page' => 'mmqw-rules-list',
						'message' => 'deleted'
					),
					admin_url('admin.php')
				));
				exit;
			} else {
				self::$admin_object->mmqw_updated_message('nonce_check', "");
			}
		}

		/**
		 * Duplicate Min/Max Rule
		 *
		 * @param int $id
		 *
		 * @access   public
		 * @uses MMQW_Min_Max_Quantity_For_WooCommerce_Admin::mmqw_updated_message()
		 *
		 * @since    1.1.0
		 *
		 */
		public static function mmqw_rule_duplicate_method($id)
		{
			$cust_nonce = filter_input(INPUT_GET, 'cust_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

			$getnonce = wp_verify_nonce($cust_nonce, 'duplicate_' . $id);
			$post_id = isset($id) ? absint($id) : '';
			$new_post_id = '';
			if (isset($getnonce) && 1 === $getnonce) {
				if (!empty($post_id) || "" !== $post_id) {
					$post = get_post($post_id);
					$current_user = wp_get_current_user();
					$new_post_author = $current_user->ID;
					if (isset($post) && null !== $post) {
						$args = array(
							'comment_status' => $post->comment_status,
							'ping_status' => $post->ping_status,
							'post_author' => $new_post_author,
							'post_content' => $post->post_content,
							'post_excerpt' => $post->post_excerpt,
							'post_name' => $post->post_name,
							'post_parent' => $post->post_parent,
							'post_password' => $post->post_password,
							'post_status' => 'draft',
							'post_title' => $post->post_title . '-duplicate',
							'post_type' => self::post_type,
							'to_ping' => $post->to_ping,
							'menu_order' => $post->menu_order
						);
						$new_post_id = wp_insert_post($args);
						$post_meta_data = get_post_meta($post_id);

						if (0 !== count($post_meta_data)) {
							foreach ($post_meta_data as $meta_key => $meta_data) {
								if ('_wp_old_slug' === $meta_key) {
									continue;
								}

								$meta_value = maybe_unserialize($meta_data[0]);

								update_post_meta($new_post_id, $meta_key, $meta_value);
							}
						}

						// Update post title
						$admin_rule_title = get_post_meta($post_id, 'fee_settings_unique_shipping_title', true);
						$new_title = $admin_rule_title . '-duplicate';
						update_post_meta($new_post_id, 'fee_settings_unique_shipping_title', $new_title);
					}
					$mmqw_add = wp_create_nonce('edit_' . $new_post_id);
					wp_safe_redirect(add_query_arg(
						array(
							'page' => 'mmqw-rules-list',
							'action' => 'edit',
							'id' => $new_post_id,
							'cust_nonce' => $mmqw_add,
							'message' => 'duplicated'
						),
						admin_url('admin.php')
					));
					exit();
				} else {
					wp_safe_redirect(add_query_arg(
						array(
							'page' => 'mmqw-rules-list',
							'message' => 'failed'
						),
						admin_url('admin.php')
					));
					exit();
				}
			} else {
				self::$admin_object->mmqw_updated_message('nonce_check', "");
			}
		}

		/**
		 * Save min/max rule when add or edit
		 *
		 * @since    1.1.0
		 *
		 * @uses MMQW_Min_Max_Quantity_For_WooCommerce_Admin::mmqw_rules_conditions_save()
		 */
		private static function mmqw_rule_save_method()
		{
			$mmqw_admin_object = new MMQW_Min_Max_Quantity_For_WooCommerce_Admin('', '');
			$mmqw_admin_object->mmqw_rules_conditions_save();
		}

		/**
		 * Edit min/max rule
		 *
		 * @since    1.1.0
		 */
		private static function mmqw_rule_edit_method()
		{
			include(plugin_dir_path(__FILE__) . 'mmqw-add-new-rule-page.php');
		}

		/**
		 * Add new min/max rule
		 *
		 * @since    1.1.0
		 */
		public static function mmqw_rule_add_new_rule_form()
		{
			include(plugin_dir_path(__FILE__) . 'mmqw-add-new-rule-page.php');
		}

		/**
		 * Listing of min/max rules
		 *
		 * @since    1.1.0
		 *
		 * @uses MMQW_Min_Max_Rule_list_Table class
		 * @uses MMQW_Min_Max_Rule_list_Table::process_bulk_action()
		 * @uses MMQW_Min_Max_Rule_list_Table::prepare_items()
		 * @uses MMQW_Min_Max_Rule_list_Table::search_box()
		 * @uses MMQW_Min_Max_Rule_list_Table::display()
		 *
		 * @access public
		 *
		 */
		public static function mmqw_rule_list_methods_screen()
		{
			if (!class_exists('MMQW_Min_Max_Rule_list_Table')) {
				require_once plugin_dir_path(dirname(__FILE__)) . 'list-tables/class-min-max-rule-list-table.php';
			}
			$link = add_query_arg(
				array(
					'page' => 'mmqw-rules-list',
					'action' => 'add'
				),
				admin_url('admin.php')
			);

			require_once(plugin_dir_path(__FILE__) . 'header/plugin-header.php');
			wp_nonce_field('sorting_conditional_min_max_action', 'sorting_conditional_min_max');
			$MMQW_Min_Max_Rule_list_Table = new MMQW_Min_Max_Rule_list_Table();
			?>
			<div class="wrap">
				<form method="post" enctype="multipart/form-data">
					<div class="mmqw-main-table">
						<h1><?php esc_html_e('Min/Max Rules', 'min-and-max-quantity-for-woocommerce'); ?></h1>
						<a class="page-title-action mmqw-btn-with-brand-color"
							href="<?php echo esc_url($link); ?>"><?php esc_html_e('Add New', 'min-and-max-quantity-for-woocommerce'); ?></a>
						<a class="shipping-methods-order page-title-action"
							style="display:none;"><?php esc_html_e('Save Order', 'min-and-max-quantity-for-woocommerce'); ?></a>
						<?php
						$MMQW_Min_Max_Rule_list_Table->process_bulk_action();
						$MMQW_Min_Max_Rule_list_Table->prepare_items();
						$request_s = filter_input(INPUT_POST, 's', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
						if (isset($request_s) && !empty($request_s)) {
							echo sprintf('<span class="subtitle">' . esc_html__('Search results for &#8220;%s&#8221;', 'min-and-max-quantity-for-woocommerce') . '</span>', esc_html($request_s));
						}
						$MMQW_Min_Max_Rule_list_Table->search_box(esc_html__('Search Min/Max Rule', 'min-and-max-quantity-for-woocommerce'), 'mmqw-min-max-rules');
						$MMQW_Min_Max_Rule_list_Table->views();
						$MMQW_Min_Max_Rule_list_Table->display();
						?>
					</div>
				</form>
			</div>
			</div>
			</div>
			</div>
			</div>
			<?php
		}
	}
}
?>