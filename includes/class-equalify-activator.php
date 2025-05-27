<?php

/**
 * Fired during plugin activation
 *
 * @link       https://easya11yguide.com
 * @since      1.0.0
 *
 * @package    Equalify
 * @subpackage Equalify/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Equalify
 * @subpackage Equalify/includes
 * @author     Easy A11y Guide <info@easya11yguide.com>
 */
class Equalify_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		global $wpdb;
		
		// Get the correct character set and collation for the database
		$charset_collate = $wpdb->get_charset_collate();
		
		// Construct the full table name using WordPress database prefix
		$table_name = $wpdb->prefix . 'equalify_reports';
		
		// SQL query to create the table
		// monitor_id relates to this site
		$sql = "CREATE TABLE $table_name (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			monitor_id BIGINT UNSIGNED NOT NULL,
			report_id VARCHAR(40) NOT NULL,
			report_date DATETIME NOT NULL,
			property_id VARCHAR(40) NOT NULL,
			url_count SMALLINT UNSIGNED NOT NULL,
			warning_count SMALLINT UNSIGNED NOT NULL,
			violation_count SMALLINT UNSIGNED NOT NULL,
			resolved_count SMALLINT UNSIGNED NOT NULL,
			good_count SMALLINT UNSIGNED NOT NULL,
			equalify_urls LONGTEXT NOT NULL,
			equalify_nodes LONGTEXT NOT NULL,
			equalify_messages LONGTEXT NOT NULL,
			equalify_csv LONGTEXT NOT NULL,
			PRIMARY KEY  (id),
			INDEX owner_index (owner_id),
			INDEX property_index (property_id)
		) $charset_collate;";
		
		// Include WordPress database file if not already included
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		// Execute the SQL query to create the table
		dbDelta( $sql );


		// Construct the full table name using WordPress database prefix
		$table_name = $wpdb->prefix . 'equalify_monitors';
		
		// SQL query to create the table
		// owner_id, group_id, and url_count are local to this site
		// last_scan is related to the equalify property
		// report_id, property_id, and property_name are from equalify
		$sql = "CREATE TABLE $table_name (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			owner_id BIGINT UNSIGNED NOT NULL,
			group_id BIGINT UNSIGNED NOT NULL,
			last_scan DATETIME NOT NULL, 
			date_created DATETIME NOT NULL,
			report_id VARCHAR(40) NOT NULL,
			property_id VARCHAR(40) NOT NULL,
			property_name VARCHAR(100) NOT NULL,
			url_count SMALLINT UNSIGNED NOT NULL,
			subscription_id BIGINT UNSIGNED NOT NULL,
			PRIMARY KEY  (id),
			INDEX owner_index (owner_id),
			INDEX property_index (property_id)
		) $charset_collate;";
		
		// Execute the SQL query to create the table
		dbDelta( $sql );
	}

}
