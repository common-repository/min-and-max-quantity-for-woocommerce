(function ($) {
	'use strict';
	jQuery('.multiselect2').select2();

	/**
	 * On load initialize the variables and activate the current menu
	 */
	$(window).on('load', function () {
		// MMQW message
		setTimeout(function () {
			jQuery('.ms-msg').fadeOut(300);
		}, 2000);

		// Activate the current menu
		$('a[href="admin.php?page=mmqw-rules-list"]').parents().addClass('current wp-has-current-submenu');
        $('a[href="admin.php?page=mmqw-rules-list"]').addClass('current');

		/**
		 * On rule save change button click check the validation of the input fields
		 */
		$('.mmqw-main-table input[name="submitFee"]').on('click', function (e) {
			let totalGroups = $('.mmqw-rules-groups-main .mmqw-rules-group-main').length;
			$('#mmqw_total_groups').val(totalGroups);
			validation(e);
		});

		function validation(e) {
			var mmqw_rules_validation = true;
			var min_max_quantity_error = true;
			if ($('.mmqw-rules-group-body tr').length) {
				$('.mmqw-rules-group-body tr').each(function () {

					//check candition value empty or not
					if ($(this).find('.min_max_select').length) {
						let selected_val_count = $(this).find('.min_max_select').find('option:selected').length;
						if (selected_val_count === 0) {
							$(this).find('.select2-container .selection .select2-selection').addClass('mmqw-error');
							mmqw_rules_validation = false;
						} else {
							$(this).find('.select2-container .selection .select2-selection').removeClass('mmqw-error');
						}
					}

					//check min qty value empty or not
					if ($(this).find('.mmqw-min-qty-field').length) {
						let min_qty_value = $(this).find('.mmqw-min-qty-field').val();
						if (min_qty_value === '' || min_qty_value === null) {
							$(this).find('.mmqw-min-qty-field').addClass('mmqw-error');
							mmqw_rules_validation = false;
						} else {
							$(this).find('.mmqw-min-qty-field').removeClass('mmqw-error');
						}
					}

					//check min qty value empty or not
					if ($(this).find('.mmqw-min-qty-field').length && $(this).find('.mmqw-max-qty-field').length) {
						let min_qty_value = $(this).find('.mmqw-min-qty-field').val();
						let max_qty_value = $(this).find('.mmqw-max-qty-field').val();
						if ((min_qty_value !== '' || min_qty_value !== null) && (max_qty_value !== '' || max_qty_value !== null)) {
							if (parseInt(min_qty_value) > parseInt(max_qty_value)) {
								$(this).find('.mmqw-max-qty-field').addClass('mmqw-error');
								min_max_quantity_error = false;
								displayMsg('mmqw-rules-group-body', coditional_vars.min_max_qty_error, e);
							} else {
								$(this).find('.mmqw-max-qty-field').removeClass('mmqw-error');
							}
						}
					}

					if (mmqw_rules_validation === false || min_max_quantity_error === false) {
						$(this).parents('.mmqw-rules-group-body').prev('.mmqw-rules-group-title').addClass('mmqw-error');
					} else {
						$(this).parents('.mmqw-rules-group-body').prev('.mmqw-rules-group-title').removeClass('mmqw-error');
					}
				});
			}

			if (mmqw_rules_validation === false) {
				e.preventDefault();
				if ($('#warning_msg_5').length <= 0) {
					var div = document.createElement('div');
					div = setAllAttributes(div, {
						'class': 'warning_msg',
						'id': 'warning_msg_5'
					});
					div.textContent = coditional_vars.warning_msg5;
					$(div).insertBefore('.mmqw-section-left .mmqw-main-table');
				}
				if ($('#warning_msg_5').length) {
					$('html, body').animate({ scrollTop: 0 }, 'slow');
					setTimeout(function () {
						$('#warning_msg_5').remove();
					}, 7000);
				}
			}
		}

		/**
		 * Display warning based on error types
		 *
		 * @param msg_id
		 * @param msg_content
		 */
		function displayMsg(msg_id, msg_content, event) {
			event.preventDefault();
			if ($('.' + msg_id).length > 0) {
				var msg_div = document.createElement('div');
				msg_div = setAllAttributes(msg_div, {
					'class': 'warning_msg',
					'id': msg_id + '_error'
				});

				msg_div.textContent = msg_content;
				if ($('#' + msg_id + '_error').length === 0) {
					$(msg_div).insertBefore('.mmqw-section-left .mmqw-main-table');
				}

				$('html, body').animate({ scrollTop: 0 }, 'slow');
				setTimeout(function () {
					$('#' + msg_id + '_error').remove();
				}, 7000);
			}
		}

		/**
		 * Call the inout number type validation
		 */
		numberValidateForAdvanceRules();

		/**
		 * Check the inout number type validation
		 */
		function numberValidateForAdvanceRules() {
			$('.number-field').keypress(function (e) {
				var regex = new RegExp('^[0-9-%.]+$');
				var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
				if (regex.test(str)) {
					return true;
				}
				e.preventDefault();
				return false;
			});
			$('.qty-class').keypress(function (e) {
				var regex = new RegExp('^[0-9]+$');
				var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
				if (regex.test(str)) {
					return true;
				}
				e.preventDefault();
				return false;
			});
		}

		/**
		 * Call product list based on product search
		 */
		setTimeout(function () {
			getProductListBasedOnThreeCharAfterUpdate();
		}, 2000);

		/**
		 * Return product list based on product search
		 */
		function getProductListBasedOnThreeCharAfterUpdate() {
			$('.pricing_rules .mmqw_product, ' +
				'.pricing_rules .mmqw_product_variation').each(function () {
					var with_variable;
					var select_name = $(this).attr('id');
					if ($(this).hasClass('mmqw_product_variation')) {
						with_variable = 'true';
					} else {
						with_variable = 'false';
					}

					$('#' + select_name).select2({
						ajax: {
							url: coditional_vars.ajaxurl,
							dataType: 'json',
							delay: 250,
							data: function (params) {
								return {
									value: params.term,
									action: 'mmqw_simple_and_variation_product_list_ajax',
									with_variable: with_variable
								};
							},
							processResults: function (data) {
								var options = [];
								if (data) {
									$.each(data, function (index, text) {
										options.push({ id: text[0], text: allowSpeicalCharacter(text[1]) });
									});

								}
								return {
									results: options
								};
							},
							cache: true
						},
						minimumInputLength: 3
					});
				});
		}

		/**
		 * Call variable product list based on product search
		 */
		varproductFilter();

		/**
		 * Return variable product list based on product search
		 */
		function varproductFilter() {
			$('.mmqw_product_rule_condition_val').each(function () {
				var select_name = $(this).attr('id');
				$('#' + select_name).select2({
					ajax: {
						url: coditional_vars.ajaxurl,
						dataType: 'json',
						delay: 250,
						data: function (params) {
							return {
								value: params.term,
								action: 'mmqw_product_fees_conditions_variable_values_product_ajax'
							};
						},
						processResults: function (data) {
							var options = [];
							if (data) {
								$.each(data, function (index, text) {
									options.push({ id: text[0], text: allowSpeicalCharacter(text[1]) });
								});

							}
							return {
								results: options
							};
						},
						cache: true
					},
					minimumInputLength: 3
				});
			});
		}

		/**
		 * Remove tr on delete icon click
		 */
		$('body').on('click', '.delete-row', function () {
			$(this).parent().parent().remove();
		});

		/**
		 * Set all the attributes
		 *
		 * @param element
		 * @param attributes
		 * @returns {*}
		 */
		function setAllAttributes(element, attributes) {
			Object.keys(attributes).forEach(function (key) {
				element.setAttribute(key, attributes[key]);
				// use val
			});
			return element;
		}

		/**
		 * Replace the special character code to symbol
		 *
		 * @param str
		 * @returns {string}
		 */
		function allowSpeicalCharacter(str) {
			return str.replace('&#8211;', '–').replace('&gt;', '>').replace('&lt;', '<').replace('&#197;', 'Å');
		}

		/**
		 * Save the order of the rules
		 */
		saveAllIdOrderWise('on_load');

		/**
		 * Start code for save all method as per sequence in list
		 *
		 */
		function saveAllIdOrderWise(position) {
			var smOrderArray = [];

			$('table#shipping-methods-listing tbody tr').each(function () {
				smOrderArray.push(this.id);
			});
			$.ajax({
				type: 'GET',
				url: coditional_vars.ajaxurl,
				data: {
					'action': 'mmqw_sm_sort_order',
					'smOrderArray': smOrderArray
				},
				success: function () {
					if ('on_click' === jQuery.trim(position)) {
						alert(coditional_vars.success_msg1);
					}
				}
			});
		}

		$(document).on('click', '.shipping-methods-order', function () {
			saveAllIdOrderWise('on_click');
		});

		/**
		 * Start: Change shipping status form list section
		 * */
		$(document).on('click', '#shipping_status_id', function () {
			var current_shipping_id = $(this).attr('data-smid');
			var current_value = $(this).prop('checked');
			$.ajax({
				type: 'GET',
				url: coditional_vars.ajaxurl,
				data: {
					'action': 'mmqw_change_status_from_list_section',
					'current_shipping_id': current_shipping_id,
					'current_value': current_value
				}, beforeSend: function () {
					var div = document.createElement('div');
					div = setAllAttributes(div, {
						'class': 'loader-overlay',
					});

					var img = document.createElement('img');
					img = setAllAttributes(img, {
						'id': 'before_ajax_id',
						'src': coditional_vars.ajax_icon
					});

					div.appendChild(img);
					var tBodyTrLast = document.querySelector('.mmqw-main-table');
					tBodyTrLast.appendChild(div);
				}, complete: function () {
					jQuery('.mmqw-main-table .loader-overlay').remove();
				}, success: function (response) {
					console.log(jQuery.trim(response));
				}
			});
		});

		$(document).on('change', '.mmqw_rule_condition', function () {
			let selectedOption = $(this).find(':selected').val();
			if (selectedOption.includes('_in_pro')) {
				let groupNumber = $(this).attr('group-number');
				$(this).find(':selected').prop('selected', false);
				condition_values(this, groupNumber);
				$('body').addClass('mmqw-modal-visible');
			} else {
				let groupNumber = $(this).attr('group-number');
				condition_values(this, groupNumber);
			}

		});

		$(document).on('change', '.mmqw_rule_condition_type', function () {
			let selectedOption = $(this).find(':selected').val();
			if (selectedOption.includes('_in_pro')) {
				$(this).find(':selected').prop('selected', false);
				$('body').addClass('mmqw-modal-visible');
			}
		});

		// Script for add new min/max group
		var mmqw_total_groups = $('#mmqw_total_groups').val();
		$(document).on('click', '#mmqw-add-new-group', function () {
			let postID = $(this).attr('post-id');
			$.ajax({
				type: 'POST',
				url: coditional_vars.ajaxurl,
				data: {
					'action': 'mmqw_add_new_group_html_ajax',
					'security': coditional_vars.mmqw_ajax_nonce,
					'mmqw_total_groups_no': parseInt(mmqw_total_groups) + 1,
					'post_id': postID
				},
				beforeSend: function () {
					var div = document.createElement('div');
					div = setAllAttributes(div, {
						'class': 'loader-overlay',
					});

					var img = document.createElement('img');
					img = setAllAttributes(img, {
						'id': 'before_ajax_id',
						'src': coditional_vars.ajax_icon
					});

					div.appendChild(img);
					var tBodyTrLast = document.querySelector('.mmqw-rules-section-main');
					tBodyTrLast.appendChild(div);
				},
				complete: function () {
					$('.mmqw-main-table .loader-overlay').remove();
					$('.multiselect2').select2();

					/** tiptip js implementation */
					$('.woocommerce-help-tip').tipTip({
						'attribute': 'data-tip',
						'fadeIn': 50,
						'fadeOut': 50,
						'delay': 200,
						'keepAlive': true
					});
				},
				success: function (response) {
					if ('' !== response) {
						$('.mmqw-rules-groups-main').append(response);

						setTimeout(function () {
							getProductListBasedOnThreeCharAfterUpdate();
						}, 2000);
					}
				}
			});
			mmqw_total_groups++;
		});

		// Script for add new condition
		var total_group_rows_count;
		var new_rule_btn_click_count = 0;
		$(document).on('click', '.mmqw-add-new-rule', function () {
			let groupParentIdArr = $(this).parents('.mmqw-rules-group-main').attr('id').split('-');
			var groupParentId = groupParentIdArr[3];
			var mmqw_add_row = $('#tbl-min-max-rules-' + groupParentId + ' tbody').get(0);

			if (0 === new_rule_btn_click_count) {
				var total_rows = $('#mmqw_total_rows_group_' + groupParentId).val();
				if (total_rows >= 1) {
					total_group_rows_count = total_rows;
				} else {
					total_group_rows_count = 0;
				}
			}

			var tr = document.createElement('tr');
			tr = setAllAttributes(tr, { 'id': 'mmqw_group_' + groupParentId + '_row_' + total_group_rows_count });
			mmqw_add_row.appendChild(tr);

			// generate th of condition
			var th = document.createElement('th');
			th = setAllAttributes(th, {
				'class': 'titledesc th_mmqw_rule_condition'
			});
			tr.appendChild(th);
			var conditions = document.createElement('select');
			conditions = setAllAttributes(conditions, {
				'rel-id': total_group_rows_count,
				'group-number': groupParentId,
				'id': 'mmqw_qroup_' + groupParentId + '_conditions_condition_' + total_group_rows_count,
				'name': 'mmqw_group[' + groupParentId + '][mmqw_rule][' + total_group_rows_count + '][mmqw_rule_condition]',
				'class': 'mmqw_rule_condition'
			});
			conditions = insertOptions(conditions, get_all_condition());
			th.appendChild(conditions);
			// th ends

			// generate td for equal or no equal to
			var td = document.createElement('td');
			td = setAllAttributes(td, {
				class: 'select_condition_for_in_notin'
			});
			tr.appendChild(td);
			var conditions_is = document.createElement('select');
			conditions_is = setAllAttributes(conditions_is, {
				'name': 'mmqw_group[' + groupParentId + '][mmqw_rule][' + total_group_rows_count + '][mmqw_rule_condition_is]',
				'class': 'mmqw_rule_condition_is mmqw_rule_group_' + groupParentId + '_condition_is_' + total_group_rows_count,
				'id': 'mmqw_qroup_' + groupParentId + '_conditions_condition_is_' + total_group_rows_count,
			});
			conditions_is = insertOptions(conditions_is, condition_types(false));
			td.appendChild(conditions_is);
			// td ends

			// td for condition values
			td = document.createElement('td');
			td = setAllAttributes(td, {
				'id': 'group_' + groupParentId + '_column_' + total_group_rows_count,
				'class': 'condition-value'
			});

			tr.appendChild(td);
			condition_values(jQuery('#mmqw_qroup_' + groupParentId + '_conditions_condition_' + total_group_rows_count), groupParentId);

			var condition_key = document.createElement('input');
			condition_key = setAllAttributes(condition_key, {
				'type': 'hidden',
				'name': 'mmqw_group_' + groupParentId + '_condition_key[' + total_group_rows_count + '][]',
				'value': '',
			});
			td.appendChild(condition_key);
			jQuery('.product_fees_conditions_values_' + total_group_rows_count).trigger('chosen:updated');
			jQuery('.multiselect2').select2();
			// td ends

			// td for delete button
			td = document.createElement('td');
			td = setAllAttributes(td, {
				'class': 'delete-td-row'
			});
			tr.appendChild(td);
			var delete_button = document.createElement('a');
			delete_button = setAllAttributes(delete_button, {
				'id': 'fee-delete-field',
				'rel-id': total_group_rows_count,
				'title': coditional_vars.delete,
				'class': 'delete-row',
				'href': 'javascript:void(0);'
			});
			var deleteicon = document.createElement('i');
			deleteicon = setAllAttributes(deleteicon, {
				'class': 'dashicons dashicons-trash'
			});
			delete_button.appendChild(deleteicon);
			td.appendChild(delete_button);
			// td ends

			total_group_rows_count++;
			new_rule_btn_click_count++;
		});

		function insertOptions(parentElement, options) {
			var option;
			for (var i = 0; i < options.length; i++) {
				if (options[i].type === 'optgroup') {
					var optgroup = document.createElement('optgroup');
					optgroup = setAllAttributes(optgroup, options[i].attributes);
					for (var j = 0; j < options[i].options.length; j++) {
						option = document.createElement('option');
						option = setAllAttributes(option, options[i].options[j].attributes);
						option.textContent = options[i].options[j].name;
						optgroup.appendChild(option);
					}
					parentElement.appendChild(optgroup);
				} else {
					option = document.createElement('option');
					option = setAllAttributes(option, options[i].attributes);
					option.textContent = allowSpeicalCharacter(options[i].name);
					parentElement.appendChild(option);
				}

			}
			return parentElement;

		}

		function get_all_condition() {
			return [
				{
					'type': 'optgroup',
					'attributes': { 'label': coditional_vars.product_specific },
					'options': [
						{ 'name': coditional_vars.qty_on_product, 'attributes': { 'value': 'product' } },
						{ 'name': coditional_vars.qty_on_variable_product, 'attributes': { 'value': 'variable_product' } },
						{ 'name': coditional_vars.qty_on_category_product, 'attributes': { 'value': 'category' } },
						{ 'name': coditional_vars.qty_on_total_sales_in_pro, 'attributes': { 'value': 'total_sales_in_pro' } },
						{ 'name': coditional_vars.qty_on_stock_quantity_in_pro, 'attributes': { 'value': 'stock_quantity_in_pro' } },
						{ 'name': coditional_vars.qty_on_sale_price_in_pro, 'attributes': { 'value': 'sale_price_in_pro' } },
						{ 'name': coditional_vars.qty_on_product_age_in_pro, 'attributes': { 'value': 'product_age_in_pro' } },
						{ 'name': coditional_vars.qty_on_best_sellers_in_pro, 'attributes': { 'value': 'best_sellers_in_pro' } },
						{ 'name': coditional_vars.qty_on_product_attributes_in_pro, 'attributes': { 'value': 'product_attributes_in_pro' } },

					]
				},
				{
					'type': 'optgroup',
					'attributes': { 'label': coditional_vars.location_specific },
					'options': [
						{ 'name': coditional_vars.qty_on_country, 'attributes': { 'value': 'country' } },
					]
				},
				{
					'type': 'optgroup',
					'attributes': { 'label': coditional_vars.user_specific },
					'options': [
						{ 'name': coditional_vars.qty_on_user_role_in_pro, 'attributes': { 'value': 'user_role_in_pro' } },
						{ 'name': coditional_vars.qty_on_user_in_pro, 'attributes': { 'value': 'user_in_pro' } },
					]
				},

				{
					'type': 'optgroup',
					'attributes': { 'label': coditional_vars.limit_section },
					'options': [
						{ 'name': coditional_vars.time_frame_in_pro, 'attributes': { 'value': 'limit_section_time_frame_in_pro' } },
					]
				},
				{
					'type': 'optgroup',
					'attributes': { 'label': coditional_vars.cart_specific },
					'options': [
						{ 'name': coditional_vars.qty_on_cart_coupon_in_pro, 'attributes': { 'value': 'cart_coupon_in_pro' } },
						{ 'name': coditional_vars.qty_on_shipping_method_in_pro, 'attributes': { 'value': 'shipping_method_in_pro' } },
						{ 'name': coditional_vars.qty_on_shipping_zone_in_pro, 'attributes': { 'value': 'shipping_zone_in_pro' } },

					]
				},
			];
		}

		function condition_values(element, groupNumber) {
			if (null === groupNumber || '' === groupNumber) {
				groupNumber = 1;
			}
			var condition = $(element).val();
			var count = $(element).attr('rel-id');
			var column = jQuery('#group_' + groupNumber + '_column_' + count).get(0);
			jQuery(column).empty();
			var loader = document.createElement('img');
			loader = setAllAttributes(loader, { 'src': coditional_vars.plugin_url + 'images/ajax-loader.gif' });
			column.appendChild(loader);

			$.ajax({
				type: 'GET',
				url: coditional_vars.ajaxurl,
				data: {
					'action': 'mmqw_rules_conditions_values_ajax',
					'condition': condition,
					'count': count,
					'group': groupNumber
				},
				contentType: 'application/json',
				success: function (response) {
					var condition_values;
					jQuery('.mmqw_rule_group_' + groupNumber + '_condition_is_' + count).empty();
					var column = jQuery('#group_' + groupNumber + '_column_' + count).get(0);
					var condition_is = jQuery('.mmqw_rule_group_' + groupNumber + '_condition_is_' + count).get(0);
					if (condition === 'cart_total' || condition === 'quantity') {
						condition_is = insertOptions(condition_is, condition_types(true));
					} else {
						condition_is = insertOptions(condition_is, condition_types(false));
					}
					jQuery('.mmqw_rule_group_' + groupNumber + '_condition_is_' + count).trigger('change');
					jQuery(column).empty();

					var condition_values_id = 'mmqw_group_' + groupNumber + '_rule_condition_value_' + count;
					var placeholder_msg = coditional_vars.validation_length1;
					var extra_class = '';
					if (condition === 'product') {
						extra_class = 'mmqw_product mmqw_product_rule_condition_val';
					} else if (condition === 'variable_product') {
						extra_class = 'mmqw_product_variation mmqw_var_product_rule_condition_val';
					} else {
						placeholder_msg = coditional_vars.select_some_options;
					}

					if (isJson(response)) {
						condition_values = document.createElement('select');
						condition_values = setAllAttributes(condition_values, {
							'name': 'mmqw_group[' + groupNumber + '][mmqw_rule][' + count + '][mmqw_rule_condition_value][]',
							'class': 'min_max_select mmqw_rule_condition_value mmqw_rule_condition_value_' + count + ' multiselect2 ' + extra_class,
							'multiple': 'multiple',
							'id': condition_values_id,
							'data-placeholder': placeholder_msg
						});
						column.appendChild(condition_values);
						var data = JSON.parse(response);
						condition_values = insertOptions(condition_values, data);
					} else {
						condition_values = document.createElement(jQuery.trim(response));
						condition_values = setAllAttributes(condition_values, {
							'name': 'mmqw_group[' + groupNumber + '][mmqw_rule][' + count + '][mmqw_rule_condition_value][]',
							'class': 'mmqw_rule_condition_value',
							'type': 'text',

						});
						column.appendChild(condition_values);
					}
					column = $('#group_' + groupNumber + '_column_' + count).get(0);
					var input_node = document.createElement('input');
					input_node = setAllAttributes(input_node, {
						'type': 'hidden',
						'name': 'mmqw_group_' + groupNumber + '_condition_key[' + count + '][]',
						'value': ''
					});
					column.appendChild(input_node);

					jQuery('.multiselect2').select2();

					varproductFilter();
					getProductListBasedOnThreeCharAfterUpdate();
				}
			});
		}

		function condition_types(text) {
			if (text === true) {
				return [
					{ 'name': coditional_vars.equal_to, 'attributes': { 'value': 'is_equal_to' } },
					{ 'name': coditional_vars.less_or_equal_to, 'attributes': { 'value': 'less_equal_to' } },
					{ 'name': coditional_vars.less_than, 'attributes': { 'value': 'less_then' } },
					{ 'name': coditional_vars.greater_or_equal_to, 'attributes': { 'value': 'greater_equal_to' } },
					{ 'name': coditional_vars.greater_than, 'attributes': { 'value': 'greater_then' } },
					{ 'name': coditional_vars.not_equal_to, 'attributes': { 'value': 'not_in' } },
				];
			} else {
				return [
					{ 'name': coditional_vars.equal_to, 'attributes': { 'value': 'is_equal_to' } },
					{ 'name': coditional_vars.not_equal_to, 'attributes': { 'value': 'not_in' } },
				];

			}
		}

		function isJson(str) {
			try {
				JSON.parse(str);
			} catch (err) {
				return false;
			}
			return true;
		}
	});
})(jQuery);

/**
 * On document ready
 */
jQuery(document).ready(function () {
	jQuery('.multiselect2').select2();

	/** tiptip js implementation */
	jQuery('.woocommerce-help-tip').tipTip({
		'attribute': 'data-tip',
		'fadeIn': 50,
		'fadeOut': 50,
		'delay': 200,
		'keepAlive': true
	});

	// script for plugin rating
	jQuery(document).on('click', '.dotstore-sidebar-section .content_box .et-star-rating label', function (e) {
		e.stopImmediatePropagation();
		var rurl = jQuery('#et-review-url').val();
		window.open(rurl, '_blank');
	});

	// script for min/max group accordion
	jQuery(document).on('click', '.mmqw-rules-group-main .mmqw-rules-group-title', function () {
		jQuery(this).next().slideToggle(200);
		jQuery(this).parent().toggleClass('active');
	});

	jQuery(document).on('click', '.mmqw-select-opt', function () {
		var del_txt = jQuery('#mmqw-delete-group').attr('data-val');
		if (jQuery('.mmqw-select-opt:checked:visible').length > 0) {
			jQuery('#mmqw-delete-group').removeAttr('disabled');
			jQuery('#mmqw-delete-group').text(del_txt + ' (' + jQuery('.mmqw-select-opt:checked:visible').length + ')');
		} else {
			jQuery('#mmqw-delete-group').attr('disabled', 'disabled');
			jQuery('#mmqw-delete-group').text(del_txt);
		}
	});
	jQuery(document).on('click', '#mmqw-delete-group', function () {
		let del_txt = jQuery('#mmqw-delete-group').attr('data-val');
		let con = confirm('Are you sure you want to delete.');
		if (con) {
			jQuery('.mmqw-select-opt:checked:visible').each(function () {
				let group = jQuery(this).parent().parent();
				group.remove();
			});
			jQuery('#mmqw-delete-group').attr('disabled', 'disabled');
			jQuery('#mmqw-delete-group').text(del_txt);
		}
	});

	/** Plugin Setup Wizard Script START */
	// Hide & show wizard steps based on the url params 
  	var urlParams = new URLSearchParams(window.location.search);
  	if (urlParams.has('require_license')) {
    	jQuery('.ds-plugin-setup-wizard-main .tab-panel').hide();
    	jQuery( '.ds-plugin-setup-wizard-main #step5' ).show();
  	} else {
  		jQuery( '.ds-plugin-setup-wizard-main #step1' ).show();
  	}
  	
    // Plugin setup wizard steps script
    jQuery(document).on('click', '.ds-plugin-setup-wizard-main .tab-panel .btn-primary:not(.ds-wizard-complete)', function () {
        var curruntStep = jQuery(this).closest('.tab-panel').attr('id');
        var nextStep = 'step' + ( parseInt( curruntStep.slice(4,5) ) + 1 ); // Masteringjs.io

        if( 'step5' !== curruntStep ) {
        	// Youtube videos stop on next step
			jQuery('iframe[src*="https://www.youtube.com/embed/"]').each(function(){
			   jQuery(this).attr('src', jQuery(this).attr('src'));
			   return false;
			});

         	jQuery( '#' + curruntStep ).hide();
            jQuery( '#' + nextStep ).show();   
        }
    });

    // Get allow for marketing or not
    if ( jQuery( '.ds-plugin-setup-wizard-main .ds_count_me_in' ).is( ':checked' ) ) {
    	jQuery('#fs_marketing_optin input[name="allow-marketing"][value="true"]').prop('checked', true);
    } else {
    	jQuery('#fs_marketing_optin input[name="allow-marketing"][value="false"]').prop('checked', true);
    }

	// Get allow for marketing or not on change	    
    jQuery(document).on( 'change', '.ds-plugin-setup-wizard-main .ds_count_me_in', function() {
		if ( this.checked ) {
			jQuery('#fs_marketing_optin input[name="allow-marketing"][value="true"]').prop('checked', true);
		} else {
	    	jQuery('#fs_marketing_optin input[name="allow-marketing"][value="false"]').prop('checked', true);
	    }
	});

    // Complete setup wizard
    jQuery(document).on( 'click', '.ds-plugin-setup-wizard-main .tab-panel .ds-wizard-complete', function() {
		if ( jQuery( '.ds-plugin-setup-wizard-main .ds_count_me_in' ).is( ':checked' ) ) {
			jQuery( '.fs-actions button'  ).trigger('click');
		} else {
	    	jQuery('.fs-actions #skip_activation')[0].click();
	    }
	});

    // Send setup wizard data on Ajax callback
	jQuery(document).on( 'click', '.ds-plugin-setup-wizard-main .fs-actions button', function() {
		var wizardData = {
            'action': 'mmqw_plugin_setup_wizard_submit',
            'survey_list': jQuery('.ds-plugin-setup-wizard-main .ds-wizard-where-hear-select').val(),
            'nonce': coditional_vars.setup_wizard_ajax_nonce
        };

        jQuery.ajax({
            url: coditional_vars.ajaxurl,
            data: wizardData,
            success: function ( success ) {
                console.log(success);
            }
        });
	});
	/** Plugin Setup Wizard Script End */

	/** Dynamic Promotional Bar START */
    jQuery(document).on('click', '.dpbpop-close', function () {
        var popupName = jQuery(this).attr('data-popup-name');
        setCookie( 'banner_' + popupName, 'yes', 60 * 24 * 7);
        jQuery('.' + popupName).hide();
    });

	jQuery(document).on('click', '.dpb-popup .dpb-popup-meta a', function () {
		var promotional_id = jQuery(this).parents().find('.dpbpop-close').attr('data-bar-id');

		//Create a new Student object using the values from the textfields
		var apiData = {
			'bar_id' : promotional_id
		};

		jQuery.ajax({
			type: 'POST',
			url: coditional_vars.dpb_api_url + 'wp-content/plugins/dots-dynamic-promotional-banner/bar-response.php',
			data: JSON.stringify(apiData),// now data come in this function
	        dataType: 'json',
	        cors: true,
	        contentType:'application/json',
	        
			success: function (data) {
				console.log(data);
			},
			error: function () {
			}
		 });
    });
    /** Dynamic Promotional Bar END */

	/** Upgrade Dashboard Script START */
    // Dashboard features popup script
    jQuery(document).on('click', '.dotstore-upgrade-dashboard .premium-key-fetures .premium-feature-popup', function (event) {
        let $trigger = jQuery('.feature-explanation-popup, .feature-explanation-popup *');
        if(!$trigger.is(event.target) && $trigger.has(event.target).length === 0){
            jQuery('.feature-explanation-popup-main').not(jQuery(this).find('.feature-explanation-popup-main')).hide();
            jQuery(this).parents('li').find('.feature-explanation-popup-main').show();
            jQuery('body').addClass('feature-explanation-popup-visible');
        }
    });
    jQuery(document).on('click', '.dotstore-upgrade-dashboard .popup-close-btn', function () {
        jQuery(this).parents('.feature-explanation-popup-main').hide();
        jQuery('body').removeClass('feature-explanation-popup-visible');
    });
    /** Upgrade Dashboard Script End */

    // Script for Beacon configuration
    var helpBeaconCookie = getCookie( 'mmqw-help-beacon-hide' );
    if ( ! helpBeaconCookie ) {
        Beacon('init', 'afe1c188-3c3b-4c5f-9dbd-87329301c920');
        Beacon('config', {
            display: {
                style: 'icon',
                iconImage: 'message',
                zIndex: '99999'
            }
        });

        // Add plugin articles IDs to display in beacon
        Beacon('suggest', ['63ee0d1efe98c84503a26e14', '642a8f66ebec2468d735043a', '642bc1ad8fe95055b525b171', '642bc907ebec2468d7350986', '642bd50328050744a30f4704']);

        // Add custom close icon form beacon
        setTimeout(function() {
            if ( jQuery( '.hsds-beacon .BeaconFabButtonFrame' ).length > 0 ) {
                let newElement = document.createElement('span');
                newElement.classList.add('dashicons', 'dashicons-no-alt', 'dots-beacon-close');
                let container = document.getElementsByClassName('BeaconFabButtonFrame');
                container[0].appendChild( newElement );
            }
        }, 3000);

        // Hide beacon
        jQuery(document).on('click', '.dots-beacon-close', function(){
            Beacon('destroy');
            setCookie( 'mmqw-help-beacon-hide' , 'true', 24 * 60 );
        });
    }

    /** Script for Freemius upgrade popup */
    jQuery(document).on('click', '.dots-header .dots-upgrade-btn, .dotstore-upgrade-dashboard .upgrade-now', function(e){
        e.preventDefault();
        upgradeToProFreemius( '' );
    });
    jQuery(document).on('click', '.upgrade-to-pro-modal-main .upgrade-now', function(e){
        e.preventDefault();
        jQuery('body').removeClass('mmqw-modal-visible');
        let couponCode = jQuery('.upgrade-to-pro-discount-code').val();
        upgradeToProFreemius( couponCode );
    });

    // Script for upgrade to pro modal
    jQuery(document).on('click', '#dotsstoremain .mmqw-pro-label, .mmqw-section-left .preimium-feature-block', function(){
		jQuery('body').addClass('mmqw-modal-visible');
	});
    jQuery(document).on('click', '#dotsstoremain .modal-close-btn', function(){
		jQuery('body').removeClass('mmqw-modal-visible');
	});
});

// Set cookies
function setCookie(name, value, minutes) {
    var expires = '';
    if (minutes) {
        var date = new Date();
        date.setTime(date.getTime() + (minutes * 60 * 1000));
        expires = '; expires=' + date.toUTCString();
    }
    document.cookie = name + '=' + (value || '') + expires + '; path=/';
}

// Get cookies
function getCookie(name) {
    let nameEQ = name + '=';
    let ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i].trim();
        if (c.indexOf(nameEQ) === 0) {
            return c.substring(nameEQ.length, c.length);
        }
    }
    return null;
}

/** Script for Freemius upgrade popup */
function upgradeToProFreemius( couponCode ) {
    let handler;
    handler = FS.Checkout.configure({
        plugin_id: '12041',
        plan_id: '20457',
        public_key:'pk_9edf804dccd14eabfd00ff503acaf',
        coupon: couponCode,
    });
    handler.open({
        name: 'Minimum and Maximum Quantity for WooCommerce',
        subtitle: 'Minimum and Maximum Quantity for WooCommerce',
        licenses: jQuery('input[name="licence"]:checked').val(),
        purchaseCompleted: function( response ) {
            console.log (response);
        },
        success: function (response) {
            console.log (response);
        }
    });
}
