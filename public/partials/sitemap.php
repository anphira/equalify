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
if(Equalify_Public::equalify_allowed_access() ) :
	?>
	<label for="urlTextarea">Enter one URL per line, make sure all URLs start with https://</label>
	<textarea id="urlTextarea" rows="10"></textarea>
	<button id="generateButton" onclick="generate_sitemap()">Generate XML Sitemap</button>
	<div id="output"></div>

	<script src="<?php echo EQUALIFY_URL; ?>public/js/equalify-public.js"></script>

<?php
else :
	echo Equalify_Public::equalify_denied();
endif;