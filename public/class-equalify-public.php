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
	    
	    if (!class_exists('WC_Subscriptions')) {
	        return apply_filters('equalify_create_new_monitor', '<a href="' . esc_url(get_option('equalify_create_url', '')) . '" class="button">Create new monitor</a>');
	    }
    
   		// Get user's active subscriptions
	    $subscriptions = wcs_get_users_subscriptions($user_id);
	    $subscription_items = array();
	    $subscription_ids = array();
	    
	    // First pass: collect all subscription data
	    foreach ($subscriptions as $subscription) {
	        if ($subscription->get_status() !== 'active') {
	            continue;
	        }
	        
	        $subscription_id = $subscription->get_id();
	        $subscription_ids[] = $subscription_id;
	        
	        foreach ($subscription->get_items() as $item_id => $item) {
	            $product_id = $item->get_product_id();
	            $quantity = $item->get_quantity();
	            
	            $subscription_items[] = array(
	                'subscription_id' => $subscription_id,
            		'line_item_id' => $item_id,
	                'product_id' => $product_id,
	                'quantity' => $quantity
	            );
	        }
	    }
	    
	    if (empty($subscription_ids)) {
	        return apply_filters('equalify_create_new_monitor', '<p>No active subscriptions found.</p>');
	    }
	    
	    // Single database query to get all existing monitors for user's subscriptions
	    $table_name = $wpdb->prefix . 'equalify_monitors';
	    $subscription_ids_placeholder = implode(',', array_fill(0, count($subscription_ids), '%d'));
	    $existing_monitors_data = $wpdb->get_results($wpdb->prepare(
	        "SELECT subscription_id, subscription_product_id, COUNT(*) as monitor_count 
	         FROM $table_name 
	         WHERE subscription_id IN ($subscription_ids_placeholder) 
	         GROUP BY subscription_id, subscription_product_id",
	        ...$subscription_ids
	    ), ARRAY_A);
	    
	    // Index existing monitors by subscription_id and product_id for quick lookup
	    $existing_counts = array();
	    foreach ($existing_monitors_data as $row) {
	        $key = $row['subscription_id'] . '_' . $row['subscription_product_id'];
	        $existing_counts[$key] = intval($row['monitor_count']);
	    }
	    
	    // Calculate unused monitors
	    $unused_monitors = array();
	    foreach ($subscription_items as $item) {
	    	
	        $key = $item['subscription_id'] . '_' . $item['product_id'];
	        $existing_count = isset($existing_counts[$key]) ? $existing_counts[$key] : 0;
	        $unused_count = $item['quantity'] - $existing_count;
	        
	        if ($unused_count > 0) {
	            $product = wc_get_product($item['product_id']);
	            $product_name = $product ? $product->get_name() : 'Unknown Product';
	            
	            for ($i = 1; $i <= $unused_count; $i++) {
	                $unused_monitors[] = array(
	                    'subscription_id' => $item['subscription_id'],
        				'line_item_id' => $item['line_item_id'],
	                    'product_id' => $item['product_id'],
	                    'product_name' => $product_name,
	                    'slot_number' => $existing_count + $i
	                );
	            }
	        }
	    }
	    
	    if (empty($unused_monitors)) {
	        $output = '<p>You have no unused monitor slots available.</p>';
	        $output .= '<p><a href="' . esc_url(get_option('equalify_purchase_url', '')) . '" class="button">Purchase Additional Monitors</a></p>';
	        return apply_filters('equalify_create_new_monitor', $output);
	    }
	    
	    $output = '<h3>Available Monitor Slots (' . count($unused_monitors) . ')</h3>';
	    foreach ($unused_monitors as $monitor) {
	        $create_url = add_query_arg(array(
	            'subscription_id' => $monitor['subscription_id'],
        		'line_item_id' => $monitor['line_item_id'],
	            'product_id' => $monitor['product_id']
	        ), get_option('equalify_create_url', ''));
	        
	        $output .= '<p><a href="' . esc_url($create_url) . '" class="button">Create ' . esc_html($monitor['product_name']) . '</a></p>';
	    }
	    
	    $output .= '<p class="mt40 mb40"><a href="' . esc_url(get_option('equalify_purchase_url', '')) . '" class="button button-secondary">Purchase Additional Monitors</a></p>';
	    
	    return apply_filters('equalify_create_new_monitor', $output);
	}

	/**
	 * Get monitor details including subscription and line item information.
	 *
	 * @since    1.0.0
	 */
	public static function equalify_get_monitor_details($monitor_id) {
	    global $wpdb;
	    
	    $table_name = $wpdb->prefix . 'equalify_monitors';
	    $monitor = $wpdb->get_row($wpdb->prepare(
	        "SELECT * FROM $table_name WHERE id = %d AND owner_id = %d",
	        $monitor_id,
	        get_current_user_id()
	    ), ARRAY_A);
	    
	    if (!$monitor) {
	        return false;
	    }
	    
	    // Add subscription details if WooCommerce is active
	    if (class_exists('WC_Subscriptions') && $monitor['subscription_id']) {
	        $subscription = wcs_get_subscription($monitor['subscription_id']);
	        if ($subscription) {
	            $line_items = $subscription->get_items();
	            if (isset($line_items[$monitor['line_item_id']])) {
	                $line_item = $line_items[$monitor['line_item_id']];
	                $monitor['subscription_status'] = $subscription->get_status();
	                $monitor['product_name'] = $line_item->get_name();
	                $monitor['line_item_quantity'] = $line_item->get_quantity();
	            }
	        }
	    }
	    
	    return $monitor;
	}

	/**
	 * Validate that the current user has access to the subscription and line item.
	 *
	 * @since    1.0.0
	 */
	private static function validate_user_subscription_access($subscription_id, $line_item_id) {
	    if (!class_exists('WC_Subscriptions')) {
	        return false;
	    }
	    
	    $subscription = wcs_get_subscription($subscription_id);
	    
	    if (!$subscription || $subscription->get_user_id() !== get_current_user_id()) {
	        return false;
	    }
	    
	    // Check if line item exists in this subscription
	    $line_items = $subscription->get_items();
	    return isset($line_items[$line_item_id]);
	}

	/**
	 * Function to create a monitor with subscription and line item context.
	 *
	 * @since    1.0.0
	 */
	public static function equalify_create_monitor_from_subscription($subscription_id, $line_item_id, $product_id, $property_name, $sitemap_url, $url_count) {
	    global $wpdb;
	    
	    // Validate that the subscription and line item belong to current user
	    if (!self::validate_user_subscription_access($subscription_id, $line_item_id)) {
	        return [
	            'success' => false,
	            'message' => 'Invalid subscription or line item access.'
	        ];
	    }
	    
	    // Check if this line item is already used
	    $table_name = $wpdb->prefix . 'equalify_monitors';
	    $existing = $wpdb->get_var($wpdb->prepare(
	        "SELECT id FROM $table_name WHERE subscription_id = %d AND line_item_id = %d",
	        $subscription_id,
	        $line_item_id
	    ));
	    
	    if ($existing) {
	        return [
	            'success' => false,
	            'message' => 'This subscription line item is already in use.'
	        ];
	    }
	    
	    // Create the monitor with line item information
	    $monitor_data = [
	        'owner_id' => get_current_user_id(),
	        'group_id' => get_current_user_id(),
	        'last_scan' => current_time('mysql'),
	        'date_created' => current_time('mysql'),
	        'report_id' => '', // Will be filled when first report is created
	        'property_id' => '', // Will be filled when property is created in Equalify API
	        'property_name' => $property_name,
	        'url_count' => $url_count,
	        'subscription_id' => $subscription_id,
	        'subscription_product_id' => $product_id,
	        'line_item_id' => $line_item_id,
	        'xml_sitemap' => $sitemap_url,
	        'email_report_to' => '', // Empty by default, can be updated later
	        'email_summary_to' => '' // Empty by default, can be updated later
	    ];
	    
	    $result = $wpdb->insert($table_name, $monitor_data);
	    
	    if ($result === false) {
	        return [
	            'success' => false,
	            'message' => 'Failed to create monitor in database.'
	        ];
	    }
	    
	    return [
	        'success' => true,
	        'monitor_id' => $wpdb->insert_id,
	        'message' => 'Monitor created successfully.'
	    ];
	}

	/**
	 * Function to get available unused monitors for the current user.
	 * Returns monitors from WooCommerce subscriptions that haven't been assigned yet.
	 *
	 * @since    1.0.0
	 */
	public static function equalify_get_unused_monitors() {
	    if (!class_exists('WC_Subscriptions')) {
	        return [];
	    }
	    
	    global $wpdb;
	    $current_user_id = get_current_user_id();
	    $unused_monitors = [];
	    
	    // Get all existing monitors for this user in a single query
	    $table_name = $wpdb->prefix . 'equalify_monitors';
	    $existing_monitors = $wpdb->get_results($wpdb->prepare(
	        "SELECT subscription_id, line_item_id FROM $table_name WHERE owner_id = %d",
	        $current_user_id
	    ), ARRAY_A);
	    
	    // Create lookup array for existing monitors
	    $existing_lookup = [];
	    foreach ($existing_monitors as $monitor) {
	        $key = $monitor['subscription_id'] . '_' . $monitor['line_item_id'];
	        $existing_lookup[$key] = true;
	    }
	    
	    // Get active subscriptions for current user
	    $subscriptions = wcs_get_users_subscriptions($current_user_id);
	    
	    foreach ($subscriptions as $subscription) {
	        if ($subscription->get_status() !== 'active') {
	            continue;
	        }
	        
	        // Get line items from subscription
	        $line_items = $subscription->get_items();
	        
	        foreach ($line_items as $line_item_id => $line_item) {
	            $product_id = $line_item->get_product_id();
	            $subscription_id = $subscription->get_id();
	            $quantity = $line_item->get_quantity();
	            
	            // Check if this line item is already assigned using lookup array
	            $lookup_key = $subscription_id . '_' . $line_item_id;
	            
	            if (!isset($existing_lookup[$lookup_key])) {
	                // Get URL count for this product
	                $url_count = get_post_meta($product_id, '_equalify_url_count', true);
	                
	                $unused_monitors[] = [
	                    'subscription_id' => $subscription_id,
	                    'line_item_id' => $line_item_id,
	                    'product_id' => $product_id,
	                    'product_name' => $line_item->get_name(),
	                    'url_count' => $url_count ?: 0,
	                    'quantity' => $quantity
	                ];
	            }
	        }
	    }
	    
	    return $unused_monitors;
	}


	/**
	 * Get URL count allowed for a specific product ID
	 *
	 * @param int $product_id WooCommerce product ID
	 * @return int URL count allowed for this product
	 * @since 1.0.0
	 */
	public static function equalify_get_url_count_for_product($product_id) {
	    for ($i = 1; $i <= 10; $i++) {
	        $stored_product_id = get_option("equalify_product_id_$i", 0);
	        if ($stored_product_id == $product_id) {
	            return get_option("equalify_url_count_$i", 0);
	        }
	    }
	    return 0;
	}

	/**
	 * Check if a subscription+product combination is available for creating a monitor
	 *
	 * @param int $subscription_id WooCommerce subscription ID
	 * @param int $product_id WooCommerce product ID
	 * @param int $user_id WordPress user ID
	 * @return bool True if available, false if already used
	 * @since 1.0.0
	 */
	public static function equalify_subscription_available($subscription_id, $product_id, $user_id) {
	    global $wpdb;
	    
	    $table_name = $wpdb->prefix . 'equalify_monitors';
	    $existing = $wpdb->get_var(
	        $wpdb->prepare(
	            "SELECT COUNT(*) FROM $table_name 
	             WHERE subscription_id = %d 
	             AND subscription_product_id = %d 
	             AND owner_id = %d",
	            $subscription_id,
	            $product_id,
	            $user_id
	        )
	    );
	    
	    return $existing == 0;
	}

	/**
	 * Get monitor by subscription and product IDs
	 *
	 * @param int $subscription_id WooCommerce subscription ID
	 * @param int $product_id WooCommerce product ID
	 * @param int $user_id WordPress user ID
	 * @return object|null Monitor object or null if not found
	 * @since 1.0.0
	 */
	public static function equalify_get_monitor_by_subscription($subscription_id, $product_id, $user_id) {
	    global $wpdb;
	    
	    $table_name = $wpdb->prefix . 'equalify_monitors';
	    return $wpdb->get_row(
	        $wpdb->prepare(
	            "SELECT * FROM $table_name 
	             WHERE subscription_id = %d 
	             AND subscription_product_id = %d 
	             AND owner_id = %d",
	            $subscription_id,
	            $product_id,
	            $user_id
	        )
	    );
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