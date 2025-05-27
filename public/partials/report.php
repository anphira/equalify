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

// get report id
$report_id = isset($_GET['id']) ? str_replace('-', '', $_GET['id']) : '';

// sanitize to ensure just alphanumerics
$report_id = preg_replace('/[^a-zA-Z0-9]/', '', $report_id);
?>


<?php 
if(Equalify_Public::equalify_allowed_access() ) :
	// if the report id is valid
	if($report_id) {
		?>
		<h2>Report for </h2>
		<button>Export as CSV</button>

		<h2>Overview</h2>
		<p>x warnings</p>
		<p>x violations</p>
		<p>x resolved </p>
		<p>x% of automated tests passed</p>

		<h2>Messages Table</h2>
		<table>
			<thead>
				<tr>
					<th>heading</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>content</td>
				</tr>
			</tbody>
		</table>

		<h2>Nodes Table</h2>
		<table>
			<thead>
				<tr>
					<th>Type</th>
					<th>Messages</th>
					<th>URL</th>
					<th>WCAG</th>
					<th>HTML</th>
					<th>Resolved</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>content</td>
					<td>content</td>
					<td>content</td>
					<td>content</td>
					<td>content</td>
					<td>content</td>
				</tr>
			</tbody>
		</table>
		<?php
	}
	// invalid report id
	else {
		echo '<h2>Invalid Report ID. Please return to the monitors page and select a valid report to view.</h2>';
	}
	?>

<?php
else :
	echo Equalify_Public::equalify_denied();
endif;