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

if(Equalify_Public::equalify_allowed_create_access() ) :

	// if action is a POST
	if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {

		$validated = Equalify_Public::validateSitemapMonitor($_POST);

		// if validated
		if( $validated['success'] ) {

			// if within the allowed url count
			if( Equalify_Public::equalify_allowed_url_count() >= $validated['url_count'] ) {

				// send curl request to create property
				$api_key = get_option('equalify_api_key', ''); // get the current API key

				$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => 'https://api.equalify.app/add/properties',
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'POST',
				  CURLOPT_POSTFIELDS =>json_encode([
			    	"propertyName" => $validated['property_name'],
			        "propertyUrl" => $validated['sitemap_url'],
			        "propertyDiscovery" => "sitemap"
			    	]),
				  CURLOPT_HTTPHEADER => array(
				    'apikey: ' . $api_key, // Add the API key to the request headers
        			'Content-Type: application/json' // Important to specify JSON content type
				  ),
				));

				$response = curl_exec($curl);

				curl_close($curl);
				$results = json_decode($response);

				// send curl request to create report
				if($results->status == "success") {
					$curl = curl_init();

					curl_setopt_array($curl, array(
					  CURLOPT_URL => 'https://api.equalify.app/add/reports',
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => '',
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 0,
					  CURLOPT_FOLLOWLOCATION => true,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => 'POST',
					  CURLOPT_POSTFIELDS => json_encode([
						"reportName" => $validated['property_name'],
						"filters" => [
					        [
					            "label" => $validated['property_name'],
					            "value" => $results->result,
					            "type" => "property"
					        ]
						]
						]),
					  CURLOPT_HTTPHEADER => array(
					    'apikey: ' . $api_key, // Add the API key to the request headers
	        			'Content-Type: application/json' // Important to specify JSON content type
					  ),
					));

					$response = curl_exec($curl);

					curl_close($curl);
					$report_results = json_decode($response);

					// the report creation was a success, send curl request to start first scan
					if($report_results->status == "success") {

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
							 "propertyIds" => [$results->result]
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
							echo '<h2>Monitor ' . $validated['property_name'] . ' created</h2>';
							echo '<p>Please wait an hour and then check the monitor page for your results.</p>';
						}
						// there was an issue starting the scan
						else {
							echo '<h2>Monitor ' . $validated['property_name'] . ' was created, but unable to start scan</h2>';
							echo '<p>Error details: ' . $scan_results->message . '</p>';
						}
					}

					// the report creation failed
					else {
						echo '<h2>Monitor ' . $validated['property_name'] . ' was created, but unable to start to create report</h2>';
						echo '<p>Error details: ' . $report_results->message . '</p>';
					}
				}

				// the property creation failed
				else {
					echo '<h2>Unable to create monitor ' . $validated['property_name'] . '</h2>';
					echo '<p>Error details: ' . $results->message . '</p>';
				}

			}
			else {
				echo '<h2>XML sitemap is too large</h2>';
				echo '<p>Your maximum allowed url count is ' . Equalify_Public::equalify_allowed_url_count() . '. Please submit a new sitemap that is less than or equal to this url count.</p>';
				echo '<p>The sitemap you submitted has a url count of ' . $validated['url_count'] . '</p>';

			}
		}

		// invalid inputs, give error message
		else {
			echo '<h2>Error with your form submission</h2>';
			echo '<p>' . $validated['message'] . '</p>';
			echo '<p>Please user our sitemap creator to create your sitemap.</p>';
		}
	}


	// not a POST, just display form
	else {
		?>
	
		<form id="property_create_form" action="" method="post" enctype="application/x-www-form-urlencoded">
			<div class="flexbox">
				<div>
					<p><strong><label for="property_name">Name for this monitor (required)</label></strong></p>
					<p id="property_describe">Only letters, numbers, and spaces are allowed.</p>
					<p><input type="text" id="property_name" name="property_name" aria-describeby="property_describe" required></p>
				</div>
				<div>
					<p><strong><label for="property_sitemap">URLs for this monitor (required)</label></strong></p>
					<p id="sitemap_describe">Enter one URL per line, make sure all URLs start with https://</p>
					<p><textarea id="urlTextarea" rows="10" required></textarea></p>
				</div>
			</div>
			<input type="submit" value="Create monitor" class="mb100">
		</form>

	<?php
	}

else :
	echo Equalify_Public::equalify_denied();
endif;