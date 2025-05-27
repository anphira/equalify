<?php

/**
 * Provide an admin page for the plugin
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
if(Equalify_Public::equalify_allowed_admin_access() ) :

	$api_key = get_option('equalify_api_key', ''); // get the current API key

	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'https://api.equalify.app/get/reports',
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'GET',CURLOPT_HTTPHEADER => array(
	    'apikey: ' . $api_key // Add the API key to the request headers
	  ),
	));

	$response = curl_exec($curl);

	curl_close($curl);
	$results = json_decode($response);
	
	$valid_id = -1;

	$queryParams = filter_input_array(INPUT_GET, [
	    'scan' => [
	        'filter' => FILTER_SANITIZE_STRING,
	        'flags' => FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW
	    ],
	    'store' => [
	        'filter' => FILTER_SANITIZE_STRING,
	        'flags' => FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW
	    ]
	]);

	if ( $queryParams != NULL && $queryParams['scan'] != NULL ) {

		// user is requesting the scan of a property

		// first validate that the property requested is an valid property ID
		for($i = 0; $i < count($results->result); $i++ ) {
			if( $results->result[$i]->filters[0]->value == $queryParams['scan'] ) {
				$valid_id = $i;
				break;
			}
		}
		if ( $valid_id >= 0 ) {

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://api.equalify.app/add/scans',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => json_encode([
				 "propertyIds" => [$queryParams['scan']]
				]),
			  CURLOPT_HTTPHEADER => array(
			    'apikey: ' . $api_key, // Add the API key to the request headers
		       	'Content-Type: application/json' // Important to specify JSON content type
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			$scan_results = json_decode($response);

			if($scan_results->status == "success") {
				echo '<h2>Scan ' . $results->result[$valid_id]->name . ' started</h2>';
				echo '<p>Please wait an hour and then check for your results.</p>';
			}
			// there was an issue starting the scan
			else {
				echo '<h2>Scan ' . $results->result[$valid_id]->name . ' encountered an error</h2>';
				echo '<p>Error details: ' . $scan_results->message . '</p>';
			}
			
		}

		// not a valid property id
		else {
			echo '<h2>Invalid Monitor</h2>';
			echo '<p>You have a requested an invalid monitor ID</p>';
		}
		
		echo '<p><a class="button" href="' . strtok($_SERVER['REQUEST_URI'], '?') . '">Return to monitor list</a></p>';

	}

	// store the requested item to the database
	elseif ( $queryParams != NULL && $queryParams['store'] != NULL ) {

		// user is requesting the store of a property

		// first validate that the property requested is a valid property ID
		for($i = 0; $i < count($results->result); $i++ ) {
			if( $results->result[$i]->id == $queryParams['store'] ) {
				$valid_id = $i;
				break;
			}
		}
		if ( $valid_id >= 0 ) {

			$curl = curl_init();
			$report_id = 'https://api.equalify.app/get/results?reportId=' . $results->result[$i]->id;

			curl_setopt_array($curl, array(
				CURLOPT_URL => $report_id,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
			    'apikey: ' . $api_key // Add the API key to the request headers
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			$report_results = json_decode($response);

			if(!empty($report_results->reportName)) {
				// got report, now storing into WP database

				// Construct the full table name using WordPress database prefix
				$table_name = $wpdb->prefix . 'equalify_reports';
				$report_date = $report_results->chart[0]->date;

				$warnings = 0;
				$violations = 0;
				$warning_count = 0;
				$violaton_count = 0;
				for($i = 0; $i < count($report_results->messages); $i++ ) {
					if( $report_results->messages[$i]->type == 'violation' ) {
						$violations++;
						$violaton_count += $report_results->messages[$i]->activeCount;
					}
					else {
						$warnings++;
						$warning_count += $report_results->messages[$i]->activeCount;
					}
				}

				$resolved_count = 0;
				for($i = 0; $i < count($report_results->nodes); $i++ ) {
					if( $report_results->nodes[$i]->equalified ) {
						$resolved_count++;
					}
				}

				// request to API for the CSV file
				$curl = curl_init();
				$report_id = 'https://api.equalify.app/get/results/csv?reportId=' . $results->result[$i]->id;

				curl_setopt_array($curl, array(
					CURLOPT_URL => $report_id,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_HTTPHEADER => array(
				    'apikey: ' . $api_key // Add the API key to the request headers
				  ),
				));

				$csv_response = curl_exec($curl);

				curl_close($curl);
				//$csv_results = json_decode($csv_response);

				//if(!empty($csv_response->reportName)) {
					var_dump($csv_response);
				//}

				// owner id
				$owner_id = Equalify_Public::equalify_get_owner_id();
				// group id
				$group_id = Equalify_Public::equalify_get_group_id();

				// todo: store last date of scan to database under the equalify_monitors table

				$report_to_store = array(
				        'report_id' => $results->result[$valid_id]->id, 
				        'report_date' => $report_results->chart[0]->date,
				        'property_id' => $results->result[$valid_id]->filters[0]->value,
				        'url_count' => count($report_results->urls),
				        'warning_count' => $warning_count,
				        'violation_count' => $violaton_count,
				        'resolved_count' => $resolved_count,
				        'good_count' => 0, // need api support todo
				        'equalify_urls' => json_encode($report_results->urls),
				        'equalify_nodes' => json_encode($report_results->nodes),
				        'equalify_messages' => json_encode($report_results->messages),
				        'equalify_csv' => $csv_response

				    );

				$wpdb->insert(
				    $wpdb->prefix . 'equalify_reports',
				    $report_to_store
				);
				if ($wpdb->last_error) {
				    error_log('Database insert failed: ' . $wpdb->last_error);
				    echo 'Database insert failed: ' . $wpdb->last_error;
				}
				else {
					echo '<h2>Monitor ' . $report_results->reportName . ' stored to database</h2>';
				}
			}
			// there was an issue starting the scan
			else {
				echo '<h2>Unable to store the report</h2>';
				echo '<p>Error details: ' . $report_results->message . '</p>';
			}
			
		}

		// not a valid property id
		else {
			echo '<h2>Invalid Monitor</h2>';
			echo '<p>You have a requested an invalid monitor ID</p>';
			echo '<p><a class="button" href="' . strtok($_SERVER['REQUEST_URI'], '?') . '">Return to monitor list</a></p>';
		}

	}

	// no valid GET parameter, therefore just display table
	//else {

		?>

		<h2><?php echo $results->total; ?> current monitors</h2>
		<p>All times are listed in UTC with format YYYY-MM-DD HH:mm.</p>

		<table id="table-urls">
			<thead>
				<tr>
					<th>Name</th>
					<th>Last Stored</th>
					<th>Scan</th>
					<th>Store</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				date_default_timezone_set('UTC');
				for($i = 0; $i < count($results->result); $i++ ) {
					echo '<tr><th scope="row">';
						echo $results->result[$i]->name;
					echo '</td><td>';
						//echo date('Y-m-d H:i', strtotime($results->result[$i]->updatedAt));
					echo '</td><td>';
						echo '<a class="button" href="?scan=' . $results->result[$i]->filters[0]->value . '">Scan ' . $results->result[$i]->name . '</a>';
					echo '</td><td>';
						echo '<a class="button" href="?store=' . $results->result[$i]->id . '">Store ' . $results->result[$i]->name . '</a>';
					echo '</td></tr>';
				}
				?>
			</tbody>
		</table>

	<?php
	//} // end no valid GET parameters

else :
	echo Equalify_Public::equalify_denied();
endif;