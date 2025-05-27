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

		add_shortcode( 'equalify_sitemap', array( $this, 'equalify_sitemap' ) );
		add_shortcode( 'equalify_overview', array( $this, 'equalify_overview' ) );
		add_shortcode( 'equalify_create', array( $this, 'equalify_create' ) );
		add_shortcode( 'equalify_report', array( $this, 'equalify_report' ) );
		add_shortcode( 'equalify_admin', array( $this, 'equalify_admin' ) );
		add_shortcode( 'equalify_monitor', array( $this, 'equalify_monitor' ) );
		add_shortcode( 'equalify_delete', array( $this, 'equalify_delete' ) );
		add_shortcode( 'equalify_modify', array( $this, 'equalify_modify' ) );

	}

	/**
	 * Renders the partial for sitemap.
	 *
	 * @since    1.0.0
	 */
	public function equalify_sitemap() {
		ob_start();
		include ( 'partials/sitemap.php' );
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
	 * Function for if the current user can create more properties.
	 * By default this only website administrators.
	 * Override this filter in your child theme to customize.
	 *
	 * @since    1.0.0
	 */
	public static function equalify_create_new_monitor() {
		return apply_filters('equalify_create_new_monitor', '<a href="/monitor/create/" class="button">Create new monitor</a>' );
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

}