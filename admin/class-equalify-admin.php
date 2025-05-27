<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://easya11yguide.com
 * @since      1.0.0
 *
 * @package    Equalify
 * @subpackage Equalify/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Equalify
 * @subpackage Equalify/admin
 * @author     Easy A11y Guide <info@easya11yguide.com>
 */
class Equalify_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {
		$this->plugin_name = EQUALIFY_FILE;
		$this->version = EQUALIFY_VERSION;
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
	}

    /**
     * Singleton instance getter
     *
     * @return Equalify_Admin
	 * @since    1.0.0
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Adds the settings page to the WordPress admin menu.
	 *
	 * @since    1.0.0
     */
    public function add_settings_page() {
        add_options_page(
            'Equalify Settings', // Page title
            'Equalify', // Menu title
            'manage_options', // Capability required
            'equalify-settings', // Menu slug
            array($this, 'render_settings_page') // Callback function to render the page
        );
    }

    /**
     * Registers settings for the Equalify plugin
	 *
	 * @since    1.0.0
     */
    public function register_settings() {
        // Register a new setting in the WordPress options table
        register_setting(
            'equalify_settings_group', // Option group
            'equalify_api_key', // Option name
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => '',
            )
        );


    
        // Register URL settings
        register_setting(
            'equalify_settings_group',
            'equalify_monitor_url',
            array(
                'type' => 'string',
                'sanitize_callback' => 'esc_url_raw',
                'default' => '',
            )
        );
        
        register_setting(
            'equalify_settings_group',
            'equalify_admin_url',
            array(
                'type' => 'string',
                'sanitize_callback' => 'esc_url_raw',
                'default' => '',
            )
        );
        
        register_setting(
            'equalify_settings_group',
            'equalify_create_url',
            array(
                'type' => 'string',
                'sanitize_callback' => 'esc_url_raw',
                'default' => '',
            )
        );
        
        register_setting(
            'equalify_settings_group',
            'equalify_reports_url',
            array(
                'type' => 'string',
                'sanitize_callback' => 'esc_url_raw',
                'default' => '',
            )
        );

        // Add a settings section
        add_settings_section(
            'equalify_main_section', // Section ID
            'Equalify API Configuration', // Section title
            array($this, 'section_callback'), // Callback function
            'equalify-settings' // Page slug
        );

        // Add settings field for API key
        add_settings_field(
            'equalify_api_key', // Field ID
            'Equalify API Key', // Field label
            array($this, 'api_key_field_callback'), // Callback function
            'equalify-settings', // Page slug
            'equalify_main_section' // Section ID
        );

        // Add URL settings fields
        add_settings_field(
            'equalify_monitor_url',
            'Monitor Page URL',
            array($this, 'url_field_callback'),
            'equalify-settings',
            'equalify_main_section',
            array('field' => 'equalify_monitor_url')
        );
        
        add_settings_field(
            'equalify_admin_url',
            'Admin Page URL',
            array($this, 'url_field_callback'),
            'equalify-settings',
            'equalify_main_section',
            array('field' => 'equalify_admin_url')
        );
        
        add_settings_field(
            'equalify_create_url',
            'Create Page URL',
            array($this, 'url_field_callback'),
            'equalify-settings',
            'equalify_main_section',
            array('field' => 'equalify_create_url')
        );
        
        add_settings_field(
            'equalify_reports_url',
            'Reports Page URL',
            array($this, 'url_field_callback'),
            'equalify-settings',
            'equalify_main_section',
            array('field' => 'equalify_reports_url')
        );
    }

    /**
     * Callback function for the settings section
	 *
	 * @since    1.0.0
     */
    public function section_callback() {
        echo '<p>Enter your Equalify API key to connect the plugin.</p>';
    }

    /**
     * Callback function to render URL input fields
     *
     * @since    1.0.0
     * @param    array    $args    Field arguments.
     */
    public function url_field_callback($args) {
        $field = $args['field'];
        $value = get_option($field, '');
        ?>
        <input 
            type="url" 
            name="<?php echo esc_attr($field); ?>" 
            id="<?php echo esc_attr($field); ?>" 
            value="<?php echo esc_url($value); ?>"
            class="regular-text"
        />
        <?php
    }

    /**
     * Callback function to render the API key input field
	 *
	 * @since    1.0.0
     */
    public function api_key_field_callback() {
        $api_key = get_option('equalify_api_key', '');
        if(isset($api_key)) {
            echo '<p>Equalify API Key has been saved in database.</p>';
        }
        ?>
        <input 
            type="text" 
            name="equalify_api_key" 
            id="equalify_api_key" 
            class="regular-text"
        />
        <?php
    }

    /**
     * Renders the settings page
	 *
	 * @since    1.0.0
     */
    public function render_settings_page() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                // Output security fields for the registered setting
                settings_fields('equalify_settings_group');
                
                // Output setting sections and their fields
                do_settings_sections('equalify-settings');
                
                // Output save settings button
                submit_button('Save Equalify Settings');
                ?>
            </form>
        </div>
        <?php
    }

    

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/equalify-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/equalify-admin.js', array( 'jquery' ), $this->version, false );

	}

}

const CIPHER_METHOD = 'aes-256-cbc';

/**
 * Encrypt a value using the AUTH_KEY
 * 
 * @param string $value The value to encrypt
 * @return string Base64 encoded encrypted string
 *
function equalify_encrypt(string $value) {
    // Validate input is alphanumeric
    if (!ctype_alnum($value)) {
        return false;
    }

    // Generate a random initialization vector
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(CIPHER_METHOD));
    
    // Encrypt the value
    $encrypted = openssl_encrypt(
        $value, 
        CIPHER_METHOD, 
        AUTH_KEY, 
        0, 
        $iv
    );
    
    // Combine IV and encrypted data, then base64 encode
    return base64_encode($iv . $encrypted);
}*/

/**
 * Decrypt a value using the AUTH_KEY
 * 
 * @param string $encryptedValue Base64 encoded encrypted string
 * @return string|false Decrypted value or false on failure
 *
function equalify_decrypt(string $encryptedValue): string|false {
    // Decode the base64 string
    $decoded = base64_decode($encryptedValue);
    
    // Get the IV length
    $ivLength = openssl_cipher_iv_length(CIPHER_METHOD);
    
    // Extract IV and encrypted data
    $iv = substr($decoded, 0, $ivLength);
    $encrypted = substr($decoded, $ivLength);
    
    // Decrypt and return
    return openssl_decrypt(
        $encrypted, 
        CIPHER_METHOD, 
        AUTH_KEY, 
        0, 
        $iv
    );
}*/
