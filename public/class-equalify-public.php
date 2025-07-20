<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://easya11yguide.com
 * @since      1.0.0
 *
 * @package    Equalify
 * @subpackage Equalify/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Equalify
 * @subpackage Equalify/public
 * @author     Easy A11y Guide <info@easya11yguide.com>
 */
class Equalify_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Registers all shortcodes at once
	 *
	 * @return [type] [description]
	 * @since    1.0.0
	 */
	public function register_shortcodes() {

		add_shortcode( 'equalify_link', array( $this, 'equalify_link' ) );
		add_shortcode( 'equalify_overview', array( $this, 'equalify_overview' ) );
		add_shortcode( 'equalify_create', array( $this, 'equalify_create' ) );
		add_shortcode( 'equalify_report', array( $this, 'equalify_report' ) );
		add_shortcode( 'equalify_admin', array( $this, 'equalify_admin' ) );
		add_shortcode( 'equalify_monitor', array( $this, 'equalify_monitor' ) );
		add_shortcode( 'equalify_delete', array( $this, 'equalify_delete' ) );
		add_shortcode( 'equalify_modify', array( $this, 'equalify_modify' ) );

	}

	/**
	 * Renders the partial for link.
	 *
	 * @since    1.0.0
	 */
	public function equalify_link() {
		ob_start();
		include ( 'partials/link.php' );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/**
	 * Renders the partial for overview.
	 *
	 * @since    1.0.0
	 */
	public function equalify_overview() {
		ob_start();
		include ( 'partials/overview.php' );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/**
	 * Renders the partial for create.
	 *
	 * @since    1.0.0
	 */
	public function equalify_create() {
		ob_start();
		include ( 'partials/create.php' );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/**
	 * Renders the partial for report.
	 *
	 * @since    1.0.0
	 */
	public function equalify_report() {
		ob_start();
		include ( 'partials/report.php' );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/**
	 * Renders the partial for admin.
	 *
	 * @since    1.0.0
	 */
	public function equalify_admin() {
		ob_start();
		include ( 'partials/admin.php' );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/**
	 * Renders the partial for monitor listing.
	 *
	 * @since    1.0.0
	 */
	public function equalify_monitor() {
		ob_start();
		include ( 'partials/monitor.php' );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/**
	 * Renders the partial for delete monitor.
	 *
	 * @since    1.0.0
	 */
	public function equalify_delete() {
		ob_start();
		include ( 'partials/delete.php' );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/**
	 * Renders the partial for modify monitor.
	 *
	 * @since    1.0.0
	 */
	public function equalify_modify() {
		ob_start();
		include ( 'partials/modify.php' );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Equalify_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Equalify_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/equalify-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Equalify_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Equalify_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/equalify-public.js', array( 'jquery' ), $this->version, false );

	}


	/**
	 * Function for it the current user is allowed to visit this page.
	 * By default this only requires the user to be logged in.
	 * Override this filter in your child theme to customize.
	 *
	 * @since    1.0.0
	 */
	public static function equalify_allowed_access() {
		return apply_filters('equalify_allowed_access', is_user_logged_in());
	}


	/**
	 * Function for it the current user is allowed to access overview. 
	 * By default this only website administrators.
	 * Override this filter in your child theme to customize.
	 *
	 * @since    1.0.0
	 */
	public static function equalify_allowed_overview_access() {
		return apply_filters('equalify_allowed_overview_access', current_user_can('administrator') );
	}


	/**
	 * Function for it the current user is allowed to create properties.
	 * By default this only website administrators.
	 * Override this filter in your child theme to customize.
	 *
	 * @since    1.0.0
	 */
	public static function equalify_allowed_create_access() {
		return apply_filters('equalify_allowed_create_access', current_user_can('administrator') );
	}


	/**
	 * Function for it the current user is allowed to admin properties.
	 * By default this only website administrators.
	 * Override this filter in your child theme to customize.
	 *
	 * @since    1.0.0
	 */
	public static function equalify_allowed_admin_access() {
		return apply_filters('equalify_allowed_admin_access', current_user_can('administrator') );
	}


	/**
	 * Function for content to display if the user does not have access.
	 * Override this filter in your child theme to customize.
	 *
	 * @since    1.0.0
	 */
	public static function equalify_denied() {
		return apply_filters( 'equalify_denied', '<p>The content you are trying to reach is unavailable for your user role. Please make sure you are logged in with the correct user account.</p>');
	}

	/**
	 * Function for maximum number of URLs allowed in a sitemap.
	 * By default, this is set to 1000.
	 * Override this filter in your child theme to customize.
	 *
	 * @since    1.0.0
	 */
	public static function equalify_allowed_url_count() {
		return apply_filters('equalify_allowed_url_count', 1000 );
	}

	/**
	 * Function to get the owner_id for the report being stored in the database.
	 * By default, this is set to the creator of the property.
	 * Override this filter in your child theme to customize.
	 *
	 * @since    1.0.0
	 */
	public static function equalify_get_owner_id() {
		return apply_filters('equalify_get_owner_id', get_current_user_id() );
	}

	/**
	 * Function to get the group_id for the report being stored in the database.
	 * By default, this is set to the creator of the property.
	 * Override this filter in your child theme to customize.
	 *
	 * @since    1.0.0
	 */
	public static function equalify_get_group_id() {
		return apply_filters('equalify_get_group_id', get_current_user_id() );
	}

	// Validate form submission and input
	public static function validateSitemapMonitor($post_item) {

	    // Validate property name
	    $property_name = $post_item['property_name'] ?? '';
	    if (!preg_match('/^[a-zA-Z0-9 ]+$/', $property_name)) {
	    	echo $property_name;
	        return [
	            'success' => false,
	            'message' => 'Property name must contain only alphanumeric characters and spaces.'
	        ];
	    }

	    // Validate sitemap URL
	    $sitemap_url = $post_item['property_sitemap'] ?? '';
	    if (!filter_var($sitemap_url, FILTER_VALIDATE_URL)) {
	        return [
	            'success' => false,
	            'message' => 'Invalid sitemap URL.'
	        ];
	    }

	    // Attempt to fetch and parse the sitemap
	    try {
	        // Download the sitemap content
	        $sitemap_content = @file_get_contents($sitemap_url);
	        if ($sitemap_content === false) {
	            return [
	                'success' => false,
	                'message' => 'Unable to fetch sitemap content.'
	            ];
	        }

	        // Attempt to parse XML
	        $xml = @simplexml_load_string($sitemap_content);
	        if ($xml === false) {
	            return [
	                'success' => false,
	                'message' => 'Invalid XML sitemap format.'
	            ];
	        }

	        // Count URLs
	        $url_count = 0;

	        // Check for standard sitemap structure
	        if (isset($xml->url)) {
	            $url_count = count($xml->url);
	        } 
	        // Check for sitemap index structure
	        elseif (isset($xml->sitemap)) {
	            $url_count = count($xml->sitemap);
	        }
	        // Check for urlset structure (common in sitemaps)
	        elseif ($xml->getName() === 'urlset') {
	            $url_count = count($xml->children());
	        }
	        else {
	            return [
	                'success' => false,
	                'message' => 'Unable to count URLs in sitemap.'
	            ];
	        }

	        // Return successful validation with URL count
	        return [
	            'success' => true,
	            'message' => 'Sitemap validated successfully.',
	            'property_name' => $property_name,
	            'sitemap_url' => $sitemap_url,
	            'url_count' => $url_count
	        ];

	    } catch (Exception $e) {
	        return [
	            'success' => false,
	            'message' => 'Error processing sitemap: ' . $e->getMessage()
	        ];
	    }
	}




	/**
	 * Function for if the current user can create more monitors.
	 * Shows available subscriptions and allows users to create monitors.
	 * Override this filter in your child theme to customize.
	 *
	 * @since    1.0.0
	 */
	public static function equalify_create_new_monitor() {
	    global $wpdb;
	    
	    $current_user_id = get_current_user_id();
	    if (!$current_user_id) {
	        return apply_filters('equalify_create_new_monitor', '<p>Please log in to view your monitors.</p>');
	    }
	    
	    $content = '';
	    
	    // Check if WooCommerce Subscriptions is enabled
	    $woocommerce_enabled = get_option('equalify_woocommerce_enabled', false);
	    
	    if (!$woocommerce_enabled || !function_exists('wcs_get_users_subscriptions')) {
	        return apply_filters('equalify_create_new_monitor', '<a href="' . esc_url(get_option('equalify_create_url', '')) . '" class="button">Create new monitor</a>');
	    }
	    
	    // Get user's subscriptions
	    $user_subscriptions = wcs_get_users_subscriptions($current_user_id);
	    $available_subscriptions = [];
	    $used_subscriptions = [];
	    
	    // Get existing monitors to check which subscriptions are already used
	    $table_name = $wpdb->prefix . 'equalify_monitors';
	    $existing_monitors = $wpdb->get_results(
	        $wpdb->prepare(
	            "SELECT subscription_id, subscription_product_id FROM $table_name WHERE owner_id = %d",
	            $current_user_id
	        ),
	        ARRAY_A
	    );
	    
	    // Create array of used subscription+product combinations
	    foreach ($existing_monitors as $monitor) {
	        $used_subscriptions[] = $monitor['subscription_id'] . '_' . $monitor['subscription_product_id'];
	    }
	    
	    // Process user subscriptions
	    foreach ($user_subscriptions as $subscription) {
	        $subscription_id = $subscription->get_id();
	        $subscription_status = $subscription->get_status();
	        
	        // Only show active subscriptions
	        if ($subscription_status !== 'active') {
	            continue;
	        }
	        
	        foreach ($subscription->get_items() as $item_id => $item) {
	            $product_name = $item->get_name();
	            $product_id = $item->get_product_id();
	            $combo_key = $subscription_id . '_' . $product_id;
	            
	            // Check if this subscription+product combination is already used
	            if (!in_array($combo_key, $used_subscriptions)) {
	                $available_subscriptions[] = [
	                    'subscription_id' => $subscription_id,
	                    'product_id' => $product_id,
	                    'product_name' => $product_name,
	                    'combo_key' => $combo_key
	                ];
	            }
	        }
	    }
	    
	    // Generate content based on available subscriptions
	    if (!empty($available_subscriptions)) {
	        $content .= '<div class="equalify-available-subscriptions mb50">';
	        $content .= '<h3>Available Subscriptions</h3>';
	        $content .= '<p>You have unused subscriptions that can be used to create new monitors:</p>';
	        
	        foreach ($available_subscriptions as $sub) {
	            $create_url = add_query_arg([
	                'subscription_id' => $sub['subscription_id'],
	                'product_id' => $sub['product_id']
	            ], get_option('equalify_create_url', ''));
	            
	            $content .= '<div class="subscription-item">';
	            $content .= '<p><strong>' . esc_html($sub['product_name']) . '</strong></p>';
	            $content .= '<p>Subscription ID: ' . esc_html($sub['subscription_id']) . ' | Product ID: ' . esc_html($sub['product_id']) . '</p>';
	            $content .= '<a href="' . esc_url($create_url) . '" class="button">Use subscription ' . esc_html($sub['product_name']) . '</a>';
	            $content .= '</div>';
	        }
	        
	        $content .= '</div>';
	    }
	    
	    // Always show option to purchase new subscription
	    $content .= '<div class="equalify-purchase-new">';
	    $content .= '<h3>Need More Monitors?</h3>';
	    $content .= '<p>Purchase a new subscription to create additional monitors:</p>';
	    $content .= '<a href="' . esc_url(get_option('equalify_purchase_url', '')) . '" class="button button-primary">Purchase New Subscription</a>';
	    $content .= '</div>';
	    
	    return apply_filters('equalify_create_new_monitor', $content);
	}

	/**
	 * Gets a URL setting value
	 *
	 * @param string $setting_name The name of the setting to retrieve
	 * @param string $default Default value if setting is not found
	 * @return string The URL value or default
	 * @since    1.0.0
	 */
	public static function equalify_get_url($setting_name, $default = '') {
	    return esc_url(get_option($setting_name, $default));
	}

	public static function update_monitor_sitemap($monitor_id, $urls) {
	    // Update XML sitemap file and database
	}

	public static function find_subscription_plan($url_count, $direction) {
	    // Find appropriate plan for upgrade/downgrade
	}

	public static function switch_subscription($subscription_id, $new_product_id) {
	    // Handle WooCommerce subscription switching
	}

}