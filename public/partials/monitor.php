<?php

/**
 * Provide a list of the monitors that the user has access to
 * 
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://easya11yguide.com
 * @since      1.0.0
 *
 * @package    Equalify
 * @subpackage Equalify/public/partials
 */
global $wpdb;
?>


<?php 
if(Equalify_Public::equalify_allowed_access() ) :
	// owner id
	$owner_id = Equalify_Public::equalify_get_owner_id();
	// group id
	$group_id = Equalify_Public::equalify_get_group_id();

	if(Equalify_Public::equalify_allowed_create_access()) {
		echo Equalify_Public::equalify_create_new_monitor();
	}

	if(true) {

		// Construct the full table name using WordPress database prefix
		$table_name = $wpdb->prefix . 'equalify_monitors';
		 // Query to get all data from the table
	    $monitors_data = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
		$table_name = $wpdb->prefix . 'equalify_reports';
	    $reports_data = $wpdb->get_results("SELECT report_id, url_count, equalify_csv FROM $table_name", ARRAY_A);
	    
	    // Check if query was successful
	    if ($monitors_data === null || $reports_data === null) {
	        // error
	    } else {
			?>
			<table class="mt30 mb30">
				<thead>
					<tr>
						<th>Monitor</th>
						<th>URLs</th>
						<th>Date (YYYY-MM-DD)</th>
						<th>Options</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($monitors_data as $monitor) {
					    $url_count = '';
					    $csv = '';
					    if( $monitor['owner_id'] == get_current_user_id() ) {
					    	foreach ( $reports_data as $report ) {
					    		if($report['report_id'] == $monitor['report_id']) {
					    			$csv = $report['equalify_csv'];
					    			$url_count = $report['url_count'];
					    			break;
					    		}
					    	}
							echo '<tr scope="row">';
								echo '<td>' . $monitor['property_name'] . '</td>';
								echo '<td>' . $url_count . '<br><a href="' . Equalify_Public::equalify_get_url('equalify_edit_url') .'" class="button">Modify<span class="screen-reader-text"> ' . $monitor['property_name'] . '</span></a></td>';
								echo '<td>Created: ' . date('Y-m-d', strtotime($monitor['date_created'])) . '<br>Scanned: ' . date('Y-m-d', strtotime($monitor['last_scan'])) . '</td>';
								echo '<td><a href="' . Equalify_Public::equalify_get_url('equalify_reports_url') . '?id=' . $monitor['report_id'] . '">View Reports<span class="screen-reader-text"> for ' . $monitor['property_name'] . '</span></a><br><a href="' . $csv . '">Download Latest CSV<span class="screen-reader-text"> for ' . $monitor['property_name'] . '</span></a></td>';
							echo '</tr>';
						}
					} ?>
				</tbody>
			</table>
		<?php }
	}
	else {
		echo '<p>You have no monitors currently. Use the "Create new monitor" link to create your first monitor.</p>';
	}

	?>

<?php
else :
	echo Equalify_Public::equalify_denied();
endif;