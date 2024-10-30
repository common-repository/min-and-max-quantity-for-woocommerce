<?php
/**
 * Handles free plugin user dashboard
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get plugin header
require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-header.php' );

// Get product details from Freemius via API
$annual_plugin_price = '';
$monthly_plugin_price = '';
$plugin_details = array(
    'product_id' => 45263,
);

$api_url = add_query_arg(wp_rand(), '', MMQW_STORE_URL . 'wp-json/dotstore-product-fs-data/v2/dotstore-product-fs-data');
$final_api_url = add_query_arg($plugin_details, $api_url);

if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
    $api_response = vip_safe_wp_remote_get( $final_api_url, 3, 1, 20 );
} else {
    $api_response = wp_remote_get( $final_api_url ); // phpcs:ignore
}

if ( ( !is_wp_error($api_response)) && (200 === wp_remote_retrieve_response_code( $api_response ) ) ) {
	$api_response_body = wp_remote_retrieve_body($api_response);
	$plugin_pricing = json_decode( $api_response_body, true );

	if ( isset( $plugin_pricing ) && ! empty( $plugin_pricing ) ) {
		$first_element = reset( $plugin_pricing );
        if ( ! empty( $first_element['price_data'] ) ) {
            $first_price = reset( $first_element['price_data'] )['annual_price'];
        } else {
            $first_price = "0";
        }

        if( "0" !== $first_price ){
        	$annual_plugin_price = $first_price;
        	$monthly_plugin_price = round( intval( $first_price  ) / 12 );
        }
	}
}

// Set plugin key features content
$plugin_key_features = array(
    array(
        'title' => esc_html__( 'Default Quantity & Step Increments', 'min-and-max-quantity-for-woocommerce' ),
        'description' => esc_html__( 'Set predefined default quantities and step increments to simplify the product selection process for your customers.', 'min-and-max-quantity-for-woocommerce' ),
        'popup_image' => esc_url( MMQW_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-one-img.png' ),
        'popup_content' => array(
        	esc_html__( 'Set default input values and step increments for each product. Control the initial quantity and the increments for a smoother product selection experience.', 'min-and-max-quantity-for-woocommerce' ),
        	esc_html__( 'Specify a default starting quantity and step value to determine how the quantity adjusts as customers change.', 'min-and-max-quantity-for-woocommerce' ),
        ),
        'popup_examples' => array(
            esc_html__( 'For better control, sell products in specific increments, such as 5, 10, and 15.', 'min-and-max-quantity-for-woocommerce' ),
            esc_html__( 'Perfect for packaging, shipping, and production needs.', 'min-and-max-quantity-for-woocommerce' ),
            esc_html__( 'Guide customers to buy in the quantities that work best for your store.', 'min-and-max-quantity-for-woocommerce' ),
        )
    ),
    array(
        'title' => esc_html__( 'Flexible Quantity Selector Option', 'min-and-max-quantity-for-woocommerce' ),
        'description' => esc_html__( 'Choose between dropdowns, radio buttons, or the default input field to offer a smoother and more customizable product selection experience.', 'min-and-max-quantity-for-woocommerce' ),
        'popup_image' => esc_url( MMQW_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-two-img.png' ),
        'popup_content' => array(
        	esc_html__( 'Choose from various input types, including dropdowns, radio buttons, or the standard number input field. This allows for an intuitive selection of quantities tailored to your store’s preferences.', 'min-and-max-quantity-for-woocommerce' ),
        	esc_html__( 'Replace the default quantity selector with radio buttons or dropdowns for a more user-friendly experience.', 'min-and-max-quantity-for-woocommerce' )
        ),
        'popup_examples' => array(
            esc_html__( 'Ideal for simplifying quantity selection in stores with diverse product ranges.', 'min-and-max-quantity-for-woocommerce' ),
            esc_html__( ' Perfect for products sold in fixed bundles or requiring specific quantities.', 'min-and-max-quantity-for-woocommerce' ),
            esc_html__( ' Boost customer satisfaction with intuitive and easy-to-use quantity selectors.', 'min-and-max-quantity-for-woocommerce' ),
        )
    ),
    array(
        'title' => esc_html__( 'Manage Product-Specific Quantities', 'min-and-max-quantity-for-woocommerce' ),
        'description' => esc_html__( 'Customize quantity settings for individual products based on stock, sales price, product age, and more to optimize the user experience.', 'min-and-max-quantity-for-woocommerce' ),
        'popup_image' => esc_url( MMQW_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-three-img.png' ),
        'popup_content' => array(
        	esc_html__( 'Implement custom quantity rules based on unique product characteristics such as stock quantity, total sales, sale price, product age, attributes, and best-seller status.', 'min-and-max-quantity-for-woocommerce' ),
        	esc_html__( 'This allows for granular control over specific items in your store.', 'min-and-max-quantity-for-woocommerce' ),
        ),
        'popup_examples' => array(
            esc_html__( 'Perfect for controlling inventory levels during seasonal sales or limited-time offers.', 'min-and-max-quantity-for-woocommerce' ),
            esc_html__( 'Manage stock for best-sellers by limiting quantities to encourage wider customer access.', 'min-and-max-quantity-for-woocommerce' ),
            esc_html__( 'Tailor promotions for clearance items to boost sales while ensuring stock turnover.', 'min-and-max-quantity-for-woocommerce' ),
        )
    ),
    array(
        'title' => esc_html__( 'Handle Cart-Specific Quantities', 'min-and-max-quantity-for-woocommerce' ),
        'description' => esc_html__( 'Apply quantity rules to items in the shopping cart based on factors like applied coupons, shipping methods, and shipping zones.', 'min-and-max-quantity-for-woocommerce' ),
        'popup_image' => esc_url( MMQW_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-four-img.png' ),
        'popup_content' => array(
        	esc_html__( 'Customize product quantities based on specific cart conditions.', 'min-and-max-quantity-for-woocommerce' ),
        	esc_html__( 'This allows you to configure rules that respond to key cart details such as selected shipping methods, shipping zones, and active promotions, enhancing customer satisfaction.', 'min-and-max-quantity-for-woocommerce' ),
        ),
        'popup_examples' => array(
            esc_html__( 'Ideal for limiting quantity threshold when offering free shipping.', 'min-and-max-quantity-for-woocommerce' ),
            esc_html__( 'Adjust quantity limits based on specific shipping zones to manage regional demand.', 'min-and-max-quantity-for-woocommerce' ),
            esc_html__( 'Enable special discounts or promotions by linking quantity rules to coupon usage.', 'min-and-max-quantity-for-woocommerce' ),
        )
    ),
    array(
        'title' => esc_html__( 'Set Personalized User-Specific Rules', 'min-and-max-quantity-for-woocommerce' ),
        'description' => esc_html__( 'Create unique quantity rules tailored to individual users or user roles, offering a customized shopping experience.', 'min-and-max-quantity-for-woocommerce' ),
        'popup_image' => esc_url( MMQW_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-five-img.png' ),
        'popup_content' => array(
        	esc_html__( 'Establish quantity restrictions that apply to specific users or user roles.', 'min-and-max-quantity-for-woocommerce' ),
        	esc_html__( 'Create user-specific rules that impose special quantity conditions for certain customers, offering a unique shopping experience.', 'min-and-max-quantity-for-woocommerce' ),
        	esc_html__( 'Implement customized quantity rules tailored to individual customers or user roles, enhancing their shopping journey.', 'min-and-max-quantity-for-woocommerce' ),
        ),
        'popup_examples' => array(
            esc_html__( 'Offer bulk purchasing exclusively to wholesale buyers or VIP members.', 'min-and-max-quantity-for-woocommerce' ),
            esc_html__( 'Set special quantity limits for new users to encourage larger first-time orders.', 'min-and-max-quantity-for-woocommerce' ),
        )
    ),
    array(
        'title' => esc_html__( 'Time-Sensitive Quantity Rules', 'min-and-max-quantity-for-woocommerce' ),
        'description' => esc_html__( 'Set quantity limits that apply during specific time frames, creating urgency by defining start and end times.', 'min-and-max-quantity-for-woocommerce' ),
        'popup_image' => esc_url( MMQW_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-six-img.png' ),
        'popup_content' => array(
        	esc_html__( 'Set specific start and end times to control quantity limits within a defined timeframe.', 'min-and-max-quantity-for-woocommerce' ),
        	esc_html__( 'This ensures that your quantity limits apply only when desired, enhancing the effectiveness of time-sensitive offers.', 'min-and-max-quantity-for-woocommerce' )
        ),
        'popup_examples' => array(
            esc_html__( 'Ideal for time-sensitive promotions or enforcing special purchase limits during sales periods.', 'min-and-max-quantity-for-woocommerce' ),
            esc_html__( 'Limit product quantities during flash sales to control stock and drive urgency.', 'min-and-max-quantity-for-woocommerce' ),
            esc_html__( 'Set limits for seasonal promotions to ensure product availability while maximizing sales.', 'min-and-max-quantity-for-woocommerce' ),
        )
    ),
    array(
        'title' => esc_html__( 'Manage Product Exclusions Effectively', 'min-and-max-quantity-for-woocommerce' ),
        'description' => esc_html__( 'Exempt specific products from global cart restrictions to accommodate unique purchasing conditions, such as clearance or seasonal products.', 'min-and-max-quantity-for-woocommerce' ),
        'popup_image' => esc_url( MMQW_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-seven-img.png' ),
        'popup_content' => array(
        	esc_html__( 'Select specific products to be exempt from global cart restrictions. This feature allows special items or promotions to bypass certain rules while maintaining them for others.', 'min-and-max-quantity-for-woocommerce' ),
        	esc_html__( 'You can choose multiple products from the list to exclude them from the rules.', 'min-and-max-quantity-for-woocommerce' )
        ),
        'popup_examples' => array(
            esc_html__( 'Ideal for handling products with unique purchasing conditions while maintaining overall store policies.', 'min-and-max-quantity-for-woocommerce' ),
            esc_html__( 'Exclude products like limited-edition releases, clearance items, or seasonal offers to streamline sales.', 'min-and-max-quantity-for-woocommerce' ),
        )
    ),
    array(
        'title' => esc_html__( 'Display Applied Rules on Product & Cart Pages', 'min-and-max-quantity-for-woocommerce' ),
        'description' => esc_html__( 'Show applied rules on product and cart pages and manage checkout behavior. You can also hide the checkout button if conditions aren’t met.', 'min-and-max-quantity-for-woocommerce' ),
        'popup_image' => esc_url( MMQW_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-eight-img.png' ),
        'popup_content' => array(
        	esc_html__( 'This feature lets you display applicable rules on the product and cart pages. Global settings will be shown on the cart page, while product-specific rules will appear on the product page.', 'min-and-max-quantity-for-woocommerce' ),
        	esc_html__( 'Ensure customers are fully aware of the quantity rules, helping them understand the requirements for their purchases.', 'min-and-max-quantity-for-woocommerce' )
        ),
        'popup_examples' => array(
            esc_html__( 'Perfect for stores with strict purchase conditions, making it easy for customers to understand order requirements.', 'min-and-max-quantity-for-woocommerce' ),
            esc_html__( 'Inform customers about quantity limits, reducing the likelihood of cart abandonment at checkout.', 'min-and-max-quantity-for-woocommerce' ),
        )
    ),
);
?>
	<div class="fps-section-left">
		<div class="dotstore-upgrade-dashboard">
			<div class="premium-benefits-section">
				<h2><?php esc_html_e( 'Upgrade to Unlock Premium Features', 'min-and-max-quantity-for-woocommerce' ); ?></h2>
				<p><?php esc_html_e( 'Unlock premium features to take complete control of your product quantities and enhance customer experience!', 'min-and-max-quantity-for-woocommerce' ); ?></p>
			</div>
			<div class="premium-plugin-details">
				<div class="premium-key-fetures">
					<h3><?php esc_html_e( 'Discover Our Top Key Features', 'min-and-max-quantity-for-woocommerce' ) ?></h3>
					<ul>
						<?php 
						if ( isset( $plugin_key_features ) && ! empty( $plugin_key_features ) ) {
							foreach( $plugin_key_features as $key_feature ) {
								?>
								<li>
									<h4><?php echo esc_html( $key_feature['title'] ); ?><span class="premium-feature-popup"></span></h4>
									<p><?php echo esc_html( $key_feature['description'] ); ?></p>
									<div class="feature-explanation-popup-main">
										<div class="feature-explanation-popup-outer">
											<div class="feature-explanation-popup-inner">
												<div class="feature-explanation-popup">
													<span class="dashicons dashicons-no-alt popup-close-btn" title="<?php esc_attr_e('Close', 'min-and-max-quantity-for-woocommerce'); ?>"></span>
													<div class="popup-body-content">
														<div class="feature-content">
															<h4><?php echo esc_html( $key_feature['title'] ); ?></h4>
															<?php 
															if ( isset( $key_feature['popup_content'] ) && ! empty( $key_feature['popup_content'] ) ) {
																foreach( $key_feature['popup_content'] as $feature_content ) {
																	?>
																	<p><?php echo esc_html( $feature_content ); ?></p>
																	<?php
																}
															}
															?>
															<ul>
																<?php 
																if ( isset( $key_feature['popup_examples'] ) && ! empty( $key_feature['popup_examples'] ) ) {
																	foreach( $key_feature['popup_examples'] as $feature_example ) {
																		?>
																		<li><?php echo esc_html( $feature_example ); ?></li>
																		<?php
																	}
																}
																?>
															</ul>
														</div>
														<div class="feature-image">
															<img src="<?php echo esc_url( $key_feature['popup_image'] ); ?>" alt="<?php echo esc_attr( $key_feature['title'] ); ?>">
														</div>
													</div>
												</div>		
											</div>
										</div>
									</div>
								</li>
								<?php
							}
						}
						?>
					</ul>
				</div>
				<div class="premium-plugin-buy">
					<div class="premium-buy-price-box">
						<div class="price-box-top">
							<div class="pricing-icon">
								<img src="<?php echo esc_url( MMQW_PLUGIN_URL . 'admin/images/premium-upgrade-img/pricing-1.svg' ); ?>" alt="<?php esc_attr_e( 'Personal Plan', 'min-and-max-quantity-for-woocommerce' ); ?>">
							</div>
							<h4><?php esc_html_e( 'Personal', 'min-and-max-quantity-for-woocommerce' ) ?></h4>
						</div>
						<div class="price-box-middle">
							<?php
							if ( ! empty( $annual_plugin_price ) ) {
								?>
								<div class="monthly-price-wrap"><?php echo esc_html( '$' . $monthly_plugin_price ) ?><span class="seprater">/</span><span><?php esc_html_e( 'month', 'min-and-max-quantity-for-woocommerce' ) ?></span></div>
								<div class="yearly-price-wrap"><?php echo sprintf( esc_html__( 'Pay $%s today. Renews in 12 months.', 'min-and-max-quantity-for-woocommerce' ), esc_html( $annual_plugin_price ) ); ?></div>
								<?php	
							}
							?>
							<span class="for-site"><?php esc_html_e( '1 site', 'min-and-max-quantity-for-woocommerce' ) ?></span>
							<p class="price-desc"><?php esc_html_e( 'Great for website owners with a single WooCommerce Store', 'min-and-max-quantity-for-woocommerce' ) ?></p>
						</div>
						<div class="price-box-bottom">
							<a href="javascript:void(0);" class="upgrade-now"><?php esc_html_e( 'Get The Premium Version', 'min-and-max-quantity-for-woocommerce' ) ?></a>
							<p class="trusted-by"><?php esc_html_e( 'Trusted by 100,000+ store owners and WP experts!', 'min-and-max-quantity-for-woocommerce' ) ?></p>
						</div>
					</div>
					<div class="premium-satisfaction-guarantee premium-satisfaction-guarantee-2">
						<div class="money-back-img">
							<img src="<?php echo esc_url(MMQW_PLUGIN_URL . 'admin/images/premium-upgrade-img/14-Days-Money-Back-Guarantee.png'); ?>" alt="<?php esc_attr_e('14-Day money-back guarantee', 'min-and-max-quantity-for-woocommerce'); ?>">
						</div>
						<div class="money-back-content">
							<h2><?php esc_html_e( '14-Day Satisfaction Guarantee', 'min-and-max-quantity-for-woocommerce' ) ?></h2>
							<p><?php esc_html_e( 'You are fully protected by our 100% Satisfaction Guarantee. If over the next 14 days you are unhappy with our plugin or have an issue that we are unable to resolve, we\'ll happily consider offering a 100% refund of your money.', 'min-and-max-quantity-for-woocommerce' ); ?></p>
						</div>
					</div>
					<div class="plugin-customer-review">
						<h3><?php esc_html_e( 'Easy to Use and Did the Promise', 'min-and-max-quantity-for-woocommerce' ) ?></h3>
						<p>
							<?php echo wp_kses( __( 'I installed this plugin in order to control a category of small products that I want to sell above 10 units. It <strong>works very fine</strong> and is <strong>easy and quick to set up</strong>. I really liked it!', 'min-and-max-quantity-for-woocommerce' ), array(
					                'strong' => array(),
					            ) ); 
				            ?>
			            </p>
						<div class="review-customer">
							<div class="customer-img">
								<img src="<?php echo esc_url(MMQW_PLUGIN_URL . 'admin/images/premium-upgrade-img/customer-profile-img.jpeg'); ?>" alt="<?php esc_attr_e('Customer Profile Image', 'min-and-max-quantity-for-woocommerce'); ?>">
							</div>
							<div class="customer-name">
								<span><?php esc_html_e( 'Mauro M.', 'min-and-max-quantity-for-woocommerce' ) ?></span>
								<div class="customer-rating-bottom">
									<div class="customer-ratings">
										<span class="dashicons dashicons-star-filled"></span>
										<span class="dashicons dashicons-star-filled"></span>
										<span class="dashicons dashicons-star-filled"></span>
										<span class="dashicons dashicons-star-filled"></span>
										<span class="dashicons dashicons-star-filled"></span>
									</div>
									<div class="verified-customer">
										<span class="dashicons dashicons-yes-alt"></span>
										<?php esc_html_e( 'Verified Customer', 'min-and-max-quantity-for-woocommerce' ) ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="upgrade-to-pro-faqs">
				<h2><?php esc_html_e( 'FAQs', 'min-and-max-quantity-for-woocommerce' ); ?></h2>
				<div class="upgrade-faqs-main">
					<div class="upgrade-faqs-list">
						<div class="upgrade-faqs-header">
							<h3><?php esc_html_e( 'Do you offer support for the plugin? What’s it like?', 'min-and-max-quantity-for-woocommerce' ); ?></h3>
						</div>
						<div class="upgrade-faqs-body">
							<p>
							<?php 
								echo sprintf(
								    esc_html__('Yes! You can read our %s or submit a %s. We are very responsive and strive to do our best to help you.', 'min-and-max-quantity-for-woocommerce'),
								    '<a href="' . esc_url('https://docs.thedotstore.com/collection/706-min-max-quantity') . '" target="_blank">' . esc_html__('knowledge base', 'min-and-max-quantity-for-woocommerce') . '</a>',
								    '<a href="' . esc_url('https://www.thedotstore.com/support-ticket/') . '" target="_blank">' . esc_html__('support ticket', 'min-and-max-quantity-for-woocommerce') . '</a>',
								);

							?>
							</p>
						</div>
					</div>
					<div class="upgrade-faqs-list">
						<div class="upgrade-faqs-header">
							<h3><?php esc_html_e( 'What payment methods do you accept?', 'min-and-max-quantity-for-woocommerce' ); ?></h3>
						</div>
						<div class="upgrade-faqs-body">
							<p><?php esc_html_e( 'You can pay with your credit card using Stripe checkout. Or your PayPal account.', 'min-and-max-quantity-for-woocommerce' ) ?></p>
						</div>
					</div>
					<div class="upgrade-faqs-list">
						<div class="upgrade-faqs-header">
							<h3><?php esc_html_e( 'What’s your refund policy?', 'min-and-max-quantity-for-woocommerce' ); ?></h3>
						</div>
						<div class="upgrade-faqs-body">
							<p><?php esc_html_e( 'We have a 14-day money-back guarantee.', 'min-and-max-quantity-for-woocommerce' ) ?></p>
						</div>
					</div>
					<div class="upgrade-faqs-list">
						<div class="upgrade-faqs-header">
							<h3><?php esc_html_e( 'I have more questions…', 'min-and-max-quantity-for-woocommerce' ); ?></h3>
						</div>
						<div class="upgrade-faqs-body">
							<p>
							<?php 
								echo sprintf(
								    esc_html__('No problem, we’re happy to help! Please reach out at %s.', 'min-and-max-quantity-for-woocommerce'),
								    '<a href="' . esc_url('mailto:hello@thedotstore.com') . '" target="_blank">' . esc_html('hello@thedotstore.com') . '</a>',
								);

							?>
							</p>
						</div>
					</div>
				</div>
			</div>
			<div class="upgrade-to-premium-btn">
				<a href="javascript:void(0);" target="_blank" class="upgrade-now"><?php esc_html_e( 'Get The Premium Version', 'min-and-max-quantity-for-woocommerce' ) ?><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="crown" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-inline--fa fa-crown fa-w-20 fa-3x" width="22" height="20"><path fill="#000" d="M528 448H112c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h416c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zm64-320c-26.5 0-48 21.5-48 48 0 7.1 1.6 13.7 4.4 19.8L476 239.2c-15.4 9.2-35.3 4-44.2-11.6L350.3 85C361 76.2 368 63 368 48c0-26.5-21.5-48-48-48s-48 21.5-48 48c0 15 7 28.2 17.7 37l-81.5 142.6c-8.9 15.6-28.9 20.8-44.2 11.6l-72.3-43.4c2.7-6 4.4-12.7 4.4-19.8 0-26.5-21.5-48-48-48S0 149.5 0 176s21.5 48 48 48c2.6 0 5.2-.4 7.7-.8L128 416h384l72.3-192.8c2.5.4 5.1.8 7.7.8 26.5 0 48-21.5 48-48s-21.5-48-48-48z" class=""></path></svg></a>
			</div>
		</div>
	</div>
	</div>
</div>
</div>
<?php 
