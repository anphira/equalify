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
        $url_settings = array(
            'equalify_monitor_url',
            'equalify_admin_url',
            'equalify_create_url',
            'equalify_reports_url',
            'equalify_delete_url',
            'equalify_modify_url'
        );

        foreach ($url_settings as $setting) {
            register_setting(
                'equalify_settings_group',
                $setting,
                array(
                    'type' => 'string',
                    'sanitize_callback' => 'esc_url_raw',
                    'default' => '',
                )
            );
        }

        // Register WooCommerce checkbox
        register_setting(
            'equalify_settings_group',
            'equalify_woocommerce_enabled',
            array(
                'type' => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => false,
            )
        );

        // Register WooCommerce subscription fields (10 sets)
        for ($i = 1; $i <= 10; $i++) {
            register_setting(
                'equalify_settings_group',
                "equalify_url_count_{$i}",
                array(
                    'type' => 'integer',
                    'sanitize_callback' => 'absint',
                    'default' => 0,
                )
            );
            
            register_setting(
                'equalify_settings_group',
                "equalify_subscription_id_{$i}",
                array(
                    'type' => 'integer',
                    'sanitize_callback' => 'absint',
                    'default' => 0,
                )
            );
        }

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

        // Add URL settings section
        add_settings_section(
            'equalify_url_section',
            'Page URLs',
            array($this, 'url_section_callback'),
            'equalify-settings'
        );

        // Add URL settings fields
        $url_fields = array(
            'equalify_monitor_url' => 'Monitor Page URL',
            'equalify_admin_url' => 'Admin Page URL',
            'equalify_create_url' => 'Create Page URL',
            'equalify_reports_url' => 'Reports Page URL',
            'equalify_delete_url' => 'Delete Page URL',
            'equalify_modify_url' => 'Modify Page URL'
        );

        foreach ($url_fields as $field => $label) {
            add_settings_field(
                $field,
                $label,
                array($this, 'url_field_callback'),
                'equalify-settings',
                'equalify_url_section',
                array('field' => $field)
            );
        }

        // Add WooCommerce section
        add_settings_section(
            'equalify_woocommerce_section',
            'WooCommerce Integration',
            array($this, 'woocommerce_section_callback'),
            'equalify-settings'
        );

        // Add WooCommerce checkbox
        add_settings_field(
            'equalify_woocommerce_enabled',
            'Enable WooCommerce',
            array($this, 'woocommerce_checkbox_callback'),
            'equalify-settings',
            'equalify_woocommerce_section'
        );

        // Add subscription fields
        for ($i = 1; $i <= 10; $i++) {
            add_settings_field(
                "equalify_subscription_set_{$i}",
                "Subscription Level {$i}",
                array($this, 'subscription_set_callback'),
                'equalify-settings',
                'equalify_woocommerce_section',
                array('set_number' => $i)
            );
        }
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
     * Callback function for the URL section
     *
     * @since    1.0.0
     */
    public function url_section_callback() {
        echo '<p>Configure the URLs for pages containing Equalify shortcodes.</p>';
    }

    /**
     * Callback function for the WooCommerce section
     *
     * @since    1.0.0
     */
    public function woocommerce_section_callback() {
        echo '<p>Configure WooCommerce subscription integration for monitor billing.</p>';
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
     * Callback function to render WooCommerce checkbox
     *
     * @since    1.0.0
     */
    public function woocommerce_checkbox_callback() {
        $enabled = get_option('equalify_woocommerce_enabled', false);
        ?>
        <input 
            type="checkbox" 
            name="equalify_woocommerce_enabled" 
            id="equalify_woocommerce_enabled" 
            value="1"
            <?php checked($enabled, true); ?>
            onchange="toggleWooCommerceFields(this.checked)"
        />
        <label for="equalify_woocommerce_enabled">Enable WooCommerce subscription integration</label>
        <?php
    }

    /**
     * Callback function to render subscription set fields
     *
     * @since    1.0.0
     * @param    array    $args    Field arguments.
     */
    public function subscription_set_callback($args) {
        $set_number = $args['set_number'];
        $url_count = get_option("equalify_url_count_{$set_number}", 0);
        $subscription_id = get_option("equalify_subscription_id_{$set_number}", 0);
        $woocommerce_enabled = get_option('equalify_woocommerce_enabled', false);
        $disabled = $woocommerce_enabled ? '' : 'disabled';
        ?>
        <div class="subscription-set">
            <label for="equalify_url_count_<?php echo $set_number; ?>">URL Count:</label>
            <input 
                type="number" 
                name="equalify_url_count_<?php echo $set_number; ?>" 
                id="equalify_url_count_<?php echo $set_number; ?>" 
                value="<?php echo esc_attr($url_count); ?>"
                min="0"
                class="small-text woocommerce-field"
                <?php echo $disabled; ?>
            />
            
            <label for="equalify_subscription_id_<?php echo $set_number; ?>">Subscription Product ID:</label>
            <input 
                type="number" 
                name="equalify_subscription_id_<?php echo $set_number; ?>" 
                id="equalify_subscription_id_<?php echo $set_number; ?>" 
                value="<?php echo esc_attr($subscription_id); ?>"
                min="0"
                class="small-text woocommerce-field"
                <?php echo $disabled; ?>
            />
        </div>
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
            
            <script>
            function toggleWooCommerceFields(enabled) {
                const fields = document.querySelectorAll('.woocommerce-field');
                fields.forEach(function(field) {
                    field.disabled = !enabled;
                });
            }
            
            // Initialize field states on page load
            document.addEventListener('DOMContentLoaded', function() {
                const checkbox = document.getElementById('equalify_woocommerce_enabled');
                toggleWooCommerceFields(checkbox.checked);
            });
            </script>
            
            <style>
            .subscription-set {
                margin-bottom: 10px;
            }
            .subscription-set label {
                display: inline-block;
                width: 160px;
                margin-right: 10px;
            }
            .subscription-set input {
                margin-right: 20px;
            }
            </style>
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