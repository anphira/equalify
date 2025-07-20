<?php

/**
 * Provide a sitemap builder function for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://easya11yguide.com
 * @since      1.0.0
 *
 * @package    Equalify
 * @subpackage Equalify/public/partials
 */
?>


<?php 
if(Equalify_Public::equalify_allowed_overview_access() ) :

	$api_key = get_option('equalify_api_key', ''); // get the current API key

	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'https://api.equalify.app/get/properties',
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
	    'delete' => [
	        'filter' => FILTER_SANITIZE_STRING,
	        'flags' => FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW
	    ],
	    'confirm' => [
	        'filter' => FILTER_SANITIZE_STRING,
	        'flags' => FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW
	    ]
	]);

	if ( $queryParams != NULL && isset($queryParams['delete']) ) {

		// user is requesting the deletion of a property, need them to confirm this destructive action

		// first validate that the property requested is an valid property ID
		for($i = 0; $i < count($results->result); $i++ ) {
			if( $results->result[$i]->id == $queryParams['delete'] ) {
				$valid_id = $i;
				break;
			}
		}
		if ( $valid_id >= 0 ) {

			// valid property, ask them to confirm delete
			echo '<h2>Please confirm that you wish to delete monitor: ' . $results->result[$valid_id]->name . '</h2>';
			echo '<p><a class="button" href="?confirm=' . $results->result[$valid_id]->id . '">Confirm delete of ' . $results->result[$valid_id]->name . '</a></p>';
			echo '<p><a class="button" href="' . strtok($_SERVER['REQUEST_URI'], '?') . '">Cancel and return to monitor list</a></p>';
		}

		// not a valid property id
		else {
			echo '<h2>Invalid Monitor</h2>';
			echo '<p>You have a requested an invalid monitor ID</p>';
			echo '<p><a class="button" href="' . strtok($_SERVER['REQUEST_URI'], '?') . '">Return to monitor list</a></p>';
		}

	}

	// performing delete
	elseif( $queryParams != NULL && isset($queryParams['confirm']) ) {

		// first validate that the property requested is an valid property ID
		for($i = 0; $i < count($results->result); $i++ ) {
			if( $results->result[$i]->id == $queryParams['confirm'] ) {
				$valid_id = $i;
				break;
			}
		}
		if ( $valid_id >= 0 ) {

			// need to get report ID
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://api.equalify.app/get/reports',
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

			$report_id = 0;
			$property_without_dashes = str_replace('-', '', $queryParams['confirm']);
			for($i = 0; $i < count($report_results->result); $i++ ) {
				if( $report_results->result[$i]->filters[0]->value == $property_without_dashes ) {
					$report_id = $report_results->result[$i]->id;
					break;
				}
			}

			// if have valid report ID
			if( $report_id != 0 ) {

				// need to delete report
				$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => 'https://api.equalify.app/delete/reports?reportId=' . $report_id,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'DELETE',
				  CURLOPT_HTTPHEADER => array(
				    'apikey: ' . $api_key // Add the API key to the request headers
				  ),
				));

				$response = curl_exec($curl);

				curl_close($curl);
				$delete_report_results = json_decode($response);

				// if report deleted successfully
				if( $delete_report_results->status == "success" ) {

					// delete property
					$curl = curl_init();

					curl_setopt_array($curl, array(
					  CURLOPT_URL => 'https://api.equalify.app/delete/properties?propertyId=' . $queryParams['confirm'],
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => '',
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 0,
					  CURLOPT_FOLLOWLOCATION => true,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => 'DELETE',
					  CURLOPT_HTTPHEADER => array(
					    'apikey: ' . $api_key // Add the API key to the request headers
					  ),
					));

					$response = curl_exec($curl);

					curl_close($curl);
					$delete_results = json_decode($response);

					if( $delete_results->status == "success" ) {
						echo '<h2>Property ' . $results->result[$valid_id]->name . ' successfully deleted</h2>';
						echo '<p><a class="button" href="' . strtok($_SERVER['REQUEST_URI'], '?') . '">Return to monitor list</a></p>';

						//TODO: delete the associated report

						//TODO: delete reports from database
					}
					else {
						echo '<h2>Unable to delete monitor ' . $results->result[$valid_id]->name . '</h2>';
						echo '<p>Error message received: ' . $delete_results->message . '</p>';
						echo '<p><a class="button" href="' . strtok($_SERVER['REQUEST_URI'], '?') . '">Return to monitor list</a></p>';
					}
				}

				// unable to delete report
				else {
					echo '<h2>Unable to delete monitor ' . $results->result[$valid_id]->name . '</h2>';
					echo '<p>Error message received: ' . $$delete_report_results->message . '</p>';
					echo '<p><a class="button" href="' . strtok($_SERVER['REQUEST_URI'], '?') . '">Return to monitor list</a></p>';
				}
			}

			// unable to get report ID
			else {
				echo '<h2>Unable to delete monitor ' . $results->result[$valid_id]->name . '</h2>';
				echo '<p>No matching monitor found.</p>';
				echo '<p><a class="button" href="' . strtok($_SERVER['REQUEST_URI'], '?') . '">Return to monitor list</a></p>';
			}
		}

		// not a valid property id
		else {
			echo '<h2>Invalid monitor</h2>';
			echo '<p>You have a requested an invalid monitor ID</p>';
			echo '<p><a class="button" href="' . strtok($_SERVER['REQUEST_URI'], '?') . '">Return to monitor list</a></p>';
		}
	}

	// no valid GET parameter, therefore just display table
	else {

		?>

		<h2><?php echo $results->total; ?> current monitors</h2>
		<p>All times are listed in UTC with format YYYY-MM-DD HH:mm.</p>

		<table id="table-urls">
			<thead>
				<tr>
					<th>Monitor Name</th>
					<th>XML Sitemap</th>
					<th>Created</th>
					<th>Updated</th>
					<th>Delete</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				date_default_timezone_set('UTC');
				for($i = 0; $i < count($results->result); $i++ ) {
					echo '<tr><td>';
						echo $results->result[$i]->name;
					echo '</td><td>';
						echo $results->result[$i]->propertyUrl;
					echo '</td><td>';
						echo date('Y-m-d H:i', strtotime($results->result[$i]->createdAt));
					echo '</td><td>';
						echo date('Y-m-d H:i', strtotime($results->result[$i]->updatedAt));
					echo '</td><td>';
						echo '<a class="button" href="?delete=' . $results->result[$i]->id . '">Delete ' . $results->result[$i]->name . '</a>';
					echo '</td></tr>';
				}
				?>
			</tbody>
		</table>

	<?php
	} // end no valid GET parameters

else :
	echo Equalify_Public::equalify_denied();
endif;