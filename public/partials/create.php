<?php

/**
 * Provide monitor creation functionality with subscription line item support
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

		// Validate subscription context from form
        $form_subscription_id = isset($_POST['subscription_id']) ? intval($_POST['subscription_id']) : 0;
        $form_line_item_id = isset($_POST['line_item_id']) ? intval($_POST['line_item_id']) : 0;
        $form_product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

        $validated = Equalify_Public::validateSitemapMonitor($_POST);

        // Validate URLs from textarea
		$urls_input = isset($_POST['urls_textarea']) ? trim($_POST['urls_textarea']) : '';
		
		if (empty($urls_input)) {
			echo '<h2>Error with your form submission</h2>';
			echo '<p>Please enter at least one URL in the textarea.</p>';
		} else {
			// Split URLs by line breaks and validate each one
			$urls = array_filter(array_map('trim', explode("\n", $urls_input)));
			$valid_urls = array();
			$invalid_urls = array();
			
			foreach ($urls as $url) {
				if (filter_var($url, FILTER_VALIDATE_URL) && (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0)) {
					$valid_urls[] = $url;
				} else {
					$invalid_urls[] = $url;
				}
			}
			
			if (!empty($invalid_urls)) {
				echo '<h2>Error with your form submission</h2>';
				echo '<p>The Following URLs are invalid:</p>';
				echo '<ul>';
				foreach ($invalid_urls as $invalid_url) {
					echo '<li>' . esc_html($invalid_url) . '</li>';
				}
				echo '</ul>';
			} else {
				// Create XML sitemap
				$sitemap_content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
				$sitemap_content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
				
				foreach ($valid_urls as $url) {
					$sitemap_content .= '  <url>' . "\n";
					$sitemap_content .= '    <loc>' . esc_url($url) . '</loc>' . "\n";
					$sitemap_content .= '  </url>' . "\n";
				}
				
				$sitemap_content .= '</urlset>';
				
				// Create directory if it doesn't exist
				$upload_dir = wp_upload_dir();
				$sitemap_dir = $upload_dir['basedir'] . '/equalify-sitemaps';
				
				if (!file_exists($sitemap_dir)) {
					wp_mkdir_p($sitemap_dir);
				}
				
				// Generate filename using property name and timestamp
				$property_name_clean = sanitize_file_name($_POST['property_name']);
				$filename = $property_name_clean . '-' . time() . '.xml';
				$file_path = $sitemap_dir . '/' . $filename;
				$file_url = $upload_dir['baseurl'] . '/equalify-sitemaps/' . $filename;
				
				// Save sitemap file
				if (file_put_contents($file_path, $sitemap_content) !== false) {
					// Create validation array with sitemap data
					$validated = array(
						'success' => true,
						'property_name' => sanitize_text_field($_POST['property_name']),
						'sitemap_url' => $file_url,
						'url_count' => count($valid_urls)
					);
				} else {
					echo '<h2>Error creating sitemap file</h2>';
					echo '<p>Unable to save the sitemap file. Please try again.</p>';
					$validated = array('success' => false);
				}
			}
		}

		// Continue with existing validation logic
		if (isset($validated) && $validated['success']) {

			// Check URL count against subscription limit if subscription is provided
			$url_limit = ($subscription_id > 0 && isset($allowed_url_count)) ? $allowed_url_count : Equalify_Public::equalify_allowed_url_count();

			// if within the allowed url count
			if( $url_limit >= $validated['url_count'] ) {

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
				echo '<h2>URL count exceeds limit</h2>';
				echo '<p>Your maximum allowed URL count is ' . $url_limit . '. Please submit URLs that are less than or equal to this count.</p>';
				echo '<p>You submitted ' . $validated['url_count'] . ' URLs.</p>';
			}

		// invalid inputs, give error message
		} else if (isset($validated)) {
			echo '<h2>Error with your form submission</h2>';
			echo '<p>' . $validated['message'] . '</p>';
		}
	}

	// not a POST, display form
	else { 

		// Get subscription context from URL parameters
	    $subscription_id = isset($_GET['subscription_id']) ? intval($_GET['subscription_id']) : 0;
	    $line_item_id = isset($_GET['line_item_id']) ? intval($_GET['line_item_id']) : 0;
	    $product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
		
		// Validate subscription context if provided
        if ($subscription_id && $line_item_id && $product_id) {
			if (!Equalify_Public::validate_user_subscription_access($subscription_id, $line_item_id)) {
                echo '<h2>Invalid Access</h2>';
                echo '<p>You do not have access to this subscription.</p>';
                echo '<p><a href="' . esc_url(Equalify_Public::equalify_get_url('equalify_monitor_url')) . '" class="button">Return to Monitors</a></p>';
            }
		// no subscription provided
		else {
			echo '<div class="notice notice-error"><p><strong>Error:</strong> You need an available subscription to create a monitor, please visit <a href="/monitor/">the Monitor page</a> to purchase or check your available monitors.</p></div>';
			return;
		}

		// Get subscription details
        $subscription = wcs_get_subscription($subscription_id);
        $line_items = $subscription->get_items();
        $line_item = $line_items[$line_item_id];
        $url_limit = get_post_meta($product_id, '_equalify_url_count', true);
		?>
	
		<p class="large-text">Create your new monitor. There are 2 different email reports that get sent. One is a <strong>full report</strong> that details all of the issues and includes an attached CSV file. The second is a <strong>summary only</strong> that includes the primary issue and some overview information about the monitor.</p>
		<form id="property_create_form" action="" method="post" enctype="application/x-www-form-urlencoded">
			<input type="hidden" name="subscription_id" value="<?php echo intval($subscription_id); ?>">
            <input type="hidden" name="line_item_id" value="<?php echo intval($line_item_id); ?>">
            <input type="hidden" name="product_id" value="<?php echo intval($product_id); ?>">
            
			<div class="flexbox">
				<div>
					<p><strong><label for="property_name">Name for this monitor (required)</label></strong></p>
					<p id="property_describe">Only letters, numbers, and spaces are allowed.</p>
					<p><input type="text" id="property_name" name="property_name" aria-describeby="property_describe" required></p>

					<p><strong><label for="full_report">Full report email address (required)</label></strong></p>
					<p><input type="email" id="full_report" name="full_report" required></p>

					<p><strong><label for="summary_only">Summary only email address (required)</label></strong></p>
					<p><input type="email" id="summary_only" name="summary_only" required></p>
				</div>
				<div>
					<p><strong><label for="urls_textarea">URLs for this monitor (required)</label></strong></p>
					<p id="urls_describe">Enter one URL per line, make sure all URLs start with https://</p>
					<p><textarea aria-describedby="urls_describe" id="urls_textarea" name="urls_textarea" rows="10" required></textarea></p>
					<p id="url_count_display">0 URLs entered</p>
				</div>
			</div>
			<input type="submit" id="submit_button" value="Create monitor" class="mb100">
		</form>

		<script>
		document.addEventListener('DOMContentLoaded', function() {
			const textarea = document.getElementById('urls_textarea');
			const countDisplay = document.getElementById('url_count_display');
			const submitButton = document.getElementById('submit_button');
			
			function validateAndCountUrls() {
				const content = textarea.value.trim();
				
				if (!content) {
					countDisplay.textContent = '0 URLs entered';
					countDisplay.style.color = '';
					submitButton.value = 'Create monitor';
					return;
				}
				
				const urls = content.split('\n').map(url => url.trim()).filter(url => url.length > 0);
				const validUrls = [];
				const invalidUrls = [];
				
				urls.forEach(url => {
					if (isValidUrl(url)) {
						validUrls.push(url);
					} else {
						invalidUrls.push(url);
					}
				});
				
				if (invalidUrls.length > 0) {
					countDisplay.textContent = validUrls.length + ' valid URLs, ' + invalidUrls.length + ' invalid URLs';
					countDisplay.style.color = 'red';
					submitButton.value = 'Create monitor (fix invalid URLs)';
				} else {
					countDisplay.textContent = validUrls.length + ' URLs entered';
					countDisplay.style.color = 'green';
					submitButton.value = 'Create monitor for ' + validUrls.length + ' URLs';
				}
			}
			
			function isValidUrl(string) {
				try {
					const url = new URL(string);
					return url.protocol === 'http:' || url.protocol === 'https:';
				} catch (e) {
					return false;
				}
			}
			
			textarea.addEventListener('input', validateAndCountUrls);
			textarea.addEventListener('change', validateAndCountUrls);
		});
		</script>

	<?php
	}

else :
	echo Equalify_Public::equalify_denied();
endif;