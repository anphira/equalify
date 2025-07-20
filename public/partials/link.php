<?php

/**
 * Provide a link to the overview page for logged in users
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
	echo '<h2>Welcome to accessibility monitoring</h2>';
	echo '<a href="/monitor/overview/" class="button mb50">View my monitors</a>';
endif;