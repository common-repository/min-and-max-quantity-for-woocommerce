<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * MMQW_Min_Max_Rule_list_Table class.
 *
 * @extends WP_List_Table
 */
if ( ! class_exists( 'MMQW_Min_Max_Rule_list_Table' ) ) {

	class MMQW_Min_Max_Rule_list_Table extends WP_List_Table {

		const post_type 							= 'wc_mmqw';
		public static $mmqw_found_items 			= 0;
		public static $mmqw_found_active_items 		= 0;
		private static $admin_object 				= null;

		/**
		 * get_columns function.
		 *
		 * @return  array
		 * @since 1.0.0
		 *
		 */
		public function get_columns() {
			return array(
				'cb'                => '<input type="checkbox" />',
				'min_max_rule_name'	=> esc_html__( 'Min/Max Rule Title', 'min-and-max-quantity-for-woocommerce' ),
				'status'            => esc_html__( 'Status', 'min-and-max-quantity-for-woocommerce' ),
				'date'              => esc_html__( 'Date', 'min-and-max-quantity-for-woocommerce' ),
			);
		}

		/**
		 * get_sortable_columns function.
		 *
		 * @return array
		 * @since 1.0.0
		 *
		 */
		protected function get_sortable_columns() {
			$columns = array(
				'min_max_rule_name' => array( 'min_max_rule_name', true ),
				'status'			=> array( 'status', true ),
				'date'				=> array( 'date', true ),
			);

			return $columns;
		}

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct( array(
				'singular' => 'post',
				'plural'   => 'mmqw_list',
				'ajax'     => false
			) );
			self::$admin_object = new MMQW_Min_Max_Quantity_For_WooCommerce_Admin( '', '' );
		}

		/**
		 * Get Methods to display
		 *
		 * @since 1.0.0
		 */
		public function prepare_items() {
			$this->prepare_column_headers();
			$per_page = $this->get_items_per_page( 'mmqw_count_per_page' );

			$get_search  = filter_input( INPUT_POST, 's', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$get_orderby = filter_input( INPUT_GET, 'orderby', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$get_order   = filter_input( INPUT_GET, 'order', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$get_status  = filter_input( INPUT_GET, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

			$args = array(
				'posts_per_page' => $per_page,
				'order' 		 => 'ASC',
				'orderby' 		 => 'menu_order',
				'offset'         => ( $this->get_pagenum() - 1 ) * $per_page,
			);

			if ( isset( $get_search ) && ! empty( $get_search ) ) {
				$args['s'] = trim( wp_unslash( $get_search ) );
			}

			if ( isset( $get_orderby ) && ! empty( $get_orderby ) ) {
				if ( 'min_max_rule_name' === $get_orderby ) {
					$args['orderby'] = 'title';
				} elseif ( 'date' === $get_orderby ) {
					$args['orderby'] = 'date';
				}
			}
			if ( isset( $get_order ) && ! empty( $get_order ) ) {
				if ( 'asc' === strtolower( $get_order ) ) {
					$args['order'] = 'ASC';
				} elseif ( 'desc' === strtolower( $get_order ) ) {
					$args['order'] = 'DESC';
				}
			}

            if( !empty($get_status) ){
                if( 'enable' === strtolower($get_status) ){
                    $args['post_status'] = 'publish';
                } elseif( 'disable' === strtolower($get_status) ) {
                    $args['post_status'] = 'draft';
                } else {
                    $args['post_status'] = 'all';
                }
            }

			$this->items = $this->mmqw_find( $args, $get_orderby );
			$this->mmqw_active_find( $args );

			$total_items = $this->mmqw_count();

			$total_pages = ceil( $total_items / $per_page );

			$this->set_pagination_args( array(
				'total_items' => $total_items,
				'total_pages' => $total_pages,
				'per_page'    => $per_page,
			) );
		}

        public function views() {
        
            $status_links   = array();
            $all_args       = array( 'page' => 'mmqw-rules-list', 'status' => 'all' );
            $num_posts      = wp_count_posts( self::post_type, 'readable' );
        
            if ( empty( $num_posts ) )
                return;
        
            $total_posts  = array_sum( (array) $num_posts );

            // Subtract post types that are not included in the admin all list.
            foreach ( get_post_stati( array( 'show_in_admin_all_list' => false ) ) as $state ) {
                $total_posts -= $num_posts->$state;
            }

            $all_inner_html = sprintf(
                /* translators: %s: Number of posts. */
                _nx(
                    'All <span class="count">(%s)</span>',
                    'All <span class="count">(%s)</span>',
                    $total_posts,
                    'posts',
                    'min-and-max-quantity-for-woocommerce'
                ),
                number_format_i18n( $total_posts )
            );

            $get_request_status  = filter_input( INPUT_GET, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $request_status = isset($get_request_status) && !empty($get_request_status) ? sanitize_text_field($get_request_status) : '';
            $status_links['all'] = array(
                'url'     => esc_url( add_query_arg( $all_args, admin_url( 'admin.php' ) ) ),
                'label'   => $all_inner_html,
                'current' => empty($request_status) || ( 'all' === strtolower($request_status) ),
            );

            foreach ( get_post_stati( array( 'show_in_admin_status_list' => true ), 'objects' ) as $status ) {
                $class = '';
    
                $status_name = $status->name;
    
                if ( empty( $num_posts->$status_name ) ) {
                    continue;
                }
    
                if( !empty($request_status) ){
                    if( 'enable' === strtolower($request_status) ){
                        $check_status = 'publish';
                        $query_status = 'enable';
                    } elseif( 'disable' === strtolower($request_status) ) {
                        $check_status = 'draft';
                        $query_status = 'disable';
                    } else {
                        $check_status = 'all';
                    } 
                }

                if ( isset( $check_status ) && $status_name === $check_status ) {
                    $class = 'current';
                }

                if( 'publish' === strtolower($status_name) ){
                    $query_status = 'enable';
                } elseif( 'draft' === strtolower($status_name) ) {
                    $query_status = 'disable';
                }

                $status_args = array(
					'page'   => 'mmqw-rules-list',
					'status' => isset($query_status) && !empty($query_status) ? $query_status : 'all',
				);

                $status_label = sprintf(
                    translate_nooped_plural( $status->label_count, $num_posts->$status_name ),
                    number_format_i18n( $num_posts->$status_name )
                );
                
                $status_links[ $status_name ] = array(
                    'url'     => esc_url( add_query_arg( $status_args, admin_url( 'admin.php' ) ) ),
                    'label'   => $status_label,
                    'current' => isset( $check_status ) && $status_name === $check_status,
                );
            }

            $views = $this->get_views_links( $status_links );
            $allow_tags = array( 
                'li' => array( 
                    'class' => array()
                ),
                'a' => array(
                    'href' => array(),
                    'title' => array(),
                    'class' => array(),
                    'aria-current' => array()
                ),
            );
            
            if ( isset( $views ) && ! empty( $views ) && is_array( $views ) ) {
            	echo "<div>";
	            echo "<ul class='subsubsub'>";
	            foreach ( $views as $class => $view ) {
	                $views[ $class ] = "<li class='$class'>$view";
	            }
	            echo wp_kses( implode( " |</li>", $views ) . "</li>", $allow_tags );
	            echo '</ul>';
	            echo '</div>';	
            }
        }

		/**
		 * No rule found
		 * 
		 */
		public function no_items() {
			esc_html_e( 'No min/max rule found.', 'min-and-max-quantity-for-woocommerce' );
		}

		/**
		 * Checkbox column
		 *
		 * @param string
		 *
		 * @return mixed
		 * @since 1.0.0
		 *
		 */
		public function column_cb( $item ) {
			if ( ! $item->ID ) {
				return;
			}

			return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', 'method_id_cb', esc_attr( $item->ID ) );
		}

		/**
		 * Output the shipping name column.
		 *
		 * @param object $item
		 *
		 * @return string
		 * @since 1.0.0
		 *
		 */
		public function column_min_max_rule_name( $item ) {
			$edit_method_url = add_query_arg( array(
				'page'   => 'mmqw-rules-list',
				'action' => 'edit',
				'id'   => $item->ID
			), admin_url( 'admin.php' ) );
			$editurl = $edit_method_url;
			$min_max_title = !empty( get_post_meta( $item->ID, 'fee_settings_unique_shipping_title', true ) ) ? get_post_meta( $item->ID, 'fee_settings_unique_shipping_title', true ) : ( !empty( $item->post_title ) ? $item->post_title : '' );

			if ( strlen($min_max_title) > 38 ) {
			$method_name = '<strong>
							<a href="' . wp_nonce_url( $editurl, 'edit_' . $item->ID, 'cust_nonce' ) . '" class="row-title" title="'. esc_attr( $min_max_title ) .'">' . esc_html( substr( $min_max_title, 0, 38) ) . '...</a>
						</strong>';
			} else {
            $method_name = '<strong>
							<a href="' . wp_nonce_url( $editurl, 'edit_' . $item->ID, 'cust_nonce' ) . '" class="row-title" >' . esc_html( $min_max_title ) . '</a>
						</strong>';
			}

			echo wp_kses( $method_name, Min_Max_Quantity_For_WooCommerce::mmqw_allowed_html_tags() );
		}

		/**
		 * Generates and displays row action links.
		 *
		 * @param object $item Link being acted upon.
		 * @param string $column_name Current column name.
		 * @param string $primary Primary column name.
		 *
		 * @return string Row action output for links.
		 * @since 1.0.0
		 *
		 */
		protected function handle_row_actions( $item, $column_name, $primary ) {
			if ( $primary !== $column_name ) {
				return '';
			}

			$edit_method_url = add_query_arg( array(
				'page'   => 'mmqw-rules-list',
				'action' => 'edit',
				'id'   => $item->ID
			), admin_url( 'admin.php' ) );
			$editurl         = $edit_method_url;

			$delete_method_url = add_query_arg( array(
				'page'   => 'mmqw-rules-list',
				'action' => 'delete',
				'id'   => $item->ID
			), admin_url( 'admin.php' ) );
			$delurl            = $delete_method_url;

			$duplicate_method_url = add_query_arg( array(
				'page'   => 'mmqw-rules-list',
				'action' => 'duplicate',
				'id'   => $item->ID
			), admin_url( 'admin.php' ) );
			$duplicateurl         = $duplicate_method_url;

			$actions              = array();
			$actions['edit']      = '<a href="' . wp_nonce_url( $editurl, 'edit_' . $item->ID, 'cust_nonce' ) . '">' . __( 'Edit', 'min-and-max-quantity-for-woocommerce' ) . '</a>';
			$actions['delete']    = '<a href="' . wp_nonce_url( $delurl, 'del_' . $item->ID, 'cust_nonce' ) . '">' . __( 'Delete', 'min-and-max-quantity-for-woocommerce' ) . '</a>';
			$actions['duplicate'] = '<a href="' . wp_nonce_url( $duplicateurl, 'duplicate_' . $item->ID, 'cust_nonce' ) . '">' . __( 'Duplicate', 'min-and-max-quantity-for-woocommerce' ) . '</a>';

			return $this->row_actions( $actions );
		}

		/**
		 * Output the method enabled column.
		 *
		 * @param object $item
		 *
		 * @return string
		 */
		public function column_status( $item ) {
			if ( 0 === $item->ID ) {
				return esc_html__( 'Everywhere', 'min-and-max-quantity-for-woocommerce' );
			}
			$item_status 			= get_post_meta( $item->ID, 'sm_status', true );
			$min_max_status     	= get_post_status( $item->ID );
			$min_max_status_chk 	= ( ( ! empty( $min_max_status ) && 'publish' === $min_max_status ) || empty( $min_max_status ) ) ? 'checked' : '';
			if ( 'on' === $item_status ) {
				$status = '<label class="switch">
								<input type="checkbox" name="shipping_status" id="shipping_status_id" value="on" '.esc_attr( $min_max_status_chk ).' data-smid="'. esc_attr( $item->ID ) .'">
								<div class="slider round"></div>
							</label>';
			} else {
				$status = '<label class="switch">
								<input type="checkbox" name="shipping_status" id="shipping_status_id" value="on" '.esc_attr( $min_max_status_chk ).' data-smid="'. esc_attr( $item->ID ) .'">
								<div class="slider round"></div>
							</label>';
			}

			return $status;
		}

		/**
		 * Output the method amount column.
		 *
		 * @param object $item
		 *
		 * @return mixed $item->post_date;
		 * @since 1.0.0
		 *
		 */
		public function column_date( $item ) {
			if ( 0 === $item->ID ) {
				return esc_html__( 'Everywhere', 'min-and-max-quantity-for-woocommerce' );
			}

            $date = date_create($item->post_date);

			return date_format( $date,"j M Y" );
		}

		/**
		 * Display bulk action in filter
		 *
		 * @return array $actions
		 * @since 1.0.0
		 *
		 */
		public function get_bulk_actions() {
			$actions = array(
				'disable' => esc_html__( 'Disable', 'min-and-max-quantity-for-woocommerce' ),
				'enable'  => esc_html__( 'Enable', 'min-and-max-quantity-for-woocommerce' ),
				'delete'  => esc_html__( 'Delete', 'min-and-max-quantity-for-woocommerce' )
			);

			return $actions;
		}

		/**
		 * Process bulk actions
		 *
		 * @since 1.0.0
		 */
		public function process_bulk_action() {
			$delete_nonce     = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$get_method_id_cb = filter_input( INPUT_POST, 'method_id_cb', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );
			$method_id_cb     = ! empty( $get_method_id_cb ) ? array_map( 'sanitize_text_field', wp_unslash( $get_method_id_cb ) ) : array();

			$action = $this->current_action();

			if ( ! isset( $method_id_cb ) ) {
				return;
			}

			$deletenonce = wp_verify_nonce( $delete_nonce, 'bulk-mmqw-minmax' );

			if ( ! isset( $deletenonce ) && 1 !== $deletenonce ) {
				return;
			}

			$items = array_filter( array_map( 'absint', $method_id_cb ) );

			if ( ! $items ) {
				return;
			}

			if ( 'delete' === $action ) {
				foreach ( $items as $id ) {
					wp_delete_post( $id );
				}
				self::$admin_object->mmqw_updated_message( 'deleted', '' );
			} elseif ( 'enable' === $action ) {

				foreach ( $items as $id ) {
					$post_args   = array(
						'ID'          => $id,
						'post_status' => 'publish',
						'post_type'   => self::post_type,
					);
					wp_update_post( $post_args );
					update_post_meta( $id, 'sm_status', 'on' );
				}
				self::$admin_object->mmqw_updated_message( 'enabled', '' );
			} elseif ( 'disable' === $action ) {
				foreach ( $items as $id ) {
					$post_args   = array(
						'ID'          => $id,
						'post_status' => 'draft',
						'post_type'   => self::post_type,
					);
					wp_update_post( $post_args );
					update_post_meta( $id, 'sm_status', 'off' );
				}
				self::$admin_object->mmqw_updated_message( 'disabled', '' );
			}
		}

		/**
		 * Find post data
		 *
		 * @param mixed $args
		 *
		 * @return array $posts
		 * @since 1.0.0
		 *
		 */
		public static function mmqw_find( $args = '', $get_orderby = '' ) {
			$defaults = array(
				'post_status'    => 'any',
				'posts_per_page' => - 1,
				'offset'         => 0,
				'orderby'        => $get_orderby,
				'order'          => 'ASC'
			);

			$args = wp_parse_args( $args, $defaults );

			$args['post_type'] = self::post_type;

			$wc_mmqw_query = new WP_Query( $args );
			$posts          = $wc_mmqw_query->query( $args );

			self::$mmqw_found_items = $wc_mmqw_query->found_posts;

			return $posts;
		}

		/**
		 * Find post data
		 *
		 * @param mixed $args
		 *
		 * @return array $posts
		 * @since 1.0.0
		 *
		 */
		public static function mmqw_active_find( $args = '' ) {
			$defaults = array(
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			);

			$args = wp_parse_args( $args, $defaults );

			$args['post_type'] = self::post_type;
			$args['offset'] = 0;

			$wc_mmqw_query = new WP_Query( $args );
			$mmqw_active_items = $wc_mmqw_query->found_posts > 0 ? $wc_mmqw_query->found_posts : 0;
			self::$mmqw_found_active_items = $mmqw_active_items;

			return $mmqw_active_items;
		}

		/**
		 * Count post data
		 *
		 * @return string
		 * @since 1.0.0
		 *
		 */
		public static function mmqw_count() {
			return self::$mmqw_found_items;
		}

		/**
		 * Set column_headers property for table list
		 *
		 * @since 1.0.0
		 */
		protected function prepare_column_headers() {
			$this->_column_headers = array(
				$this->get_columns(),
				array(),
				$this->get_sortable_columns(),
			);
		}
	}
}