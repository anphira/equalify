<?php

/**
 * Provide a monitor modification function for the plugin
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

// Get monitor ID from URL parameter
$monitor_id = isset($_GET['monitor']) ? intval($_GET['monitor']) : 0;

?>

<?php 
if(Equalify_Public::equalify_allowed_access()) :
    ?>
    <p><a href="<?php echo Equalify_Public::equalify_get_url('equalify_monitor_url'); ?>" class="button">Go back to monitors page</a></p>
    
    <?php
    // Check if monitor ID is provided and valid
    if ($monitor_id <= 0) {
        echo '<h2>Error: Invalid Monitor</h2>';
        echo '<p>No monitor ID provided or invalid monitor ID.</p>';
    } else {
        // Look up monitor in database
        $table_name = $wpdb->prefix . 'equalify_monitors';
        $monitor = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $monitor_id), ARRAY_A);
        
        if (!$monitor) {
            echo '<h2>Error: Monitor Not Found</h2>';
            echo '<p>The requested monitor does not exist.</p>';
        } else {
            // Check if user owns this monitor
            if ($monitor['owner_id'] != get_current_user_id()) {
                echo '<h2>Error: Access Denied</h2>';
                echo '<p>You do not have permission to modify this monitor.</p>';
            } else {
                // Check if WooCommerce is enabled
                $wc_enabled = get_option('equalify_woocommerce_enabled', false);
                
                if (!$wc_enabled) {
                    echo '<h2>Error: WooCommerce Integration Disabled</h2>';
                    echo '<p>Monitor modification requires WooCommerce integration to be enabled.</p>';
                } else {
                    // Validate subscription
                    $subscription_id = $monitor['subscription_id'];
                    
                    if (!$subscription_id || !function_exists('wcs_get_subscription')) {
                        echo '<h2>Error: Invalid Subscription</h2>';
                        echo '<p>No valid subscription found for this monitor.</p>';
                    } else {
                        $subscription = wcs_get_subscription($subscription_id);
                        
                        if (!$subscription || $subscription->get_status() !== 'active') {
                            echo '<h2>Error: Inactive Subscription</h2>';
                            echo '<p>The subscription associated with this monitor is not active.</p>';
                        } else {
                            // Get current subscription URL limit
                            $current_url_limit = 0;
                            $current_subscription_product_id = 0;
                            
                            // Find matching subscription plan
                            for ($i = 1; $i <= 10; $i++) {
                                $url_count = get_option("equalify_url_count_$i", 0);
                                $sub_id = get_option("equalify_subscription_id_$i", 0);
                                
                                if ($sub_id == $subscription_id) {
                                    $current_url_limit = $url_count;
                                    $current_subscription_product_id = $sub_id;
                                    break;
                                }
                            }
                            
                            if ($current_url_limit == 0) {
                                echo '<h2>Error: Subscription Plan Not Found</h2>';
                                echo '<p>Unable to determine URL limit for current subscription.</p>';
                            } else {
                                // Load current XML sitemap content
                                $sitemap_content = '';
                                $current_urls = array();
                                
                                if (!empty($monitor['xml_sitemap'])) {
                                    $upload_dir = wp_upload_dir();
                                    $sitemap_path = $upload_dir['basedir'] . '/equalify-sitemaps/' . basename($monitor['xml_sitemap']);
                                    
                                    if (file_exists($sitemap_path)) {
                                        $xml_content = file_get_contents($sitemap_path);
                                        $xml = simplexml_load_string($xml_content);
                                        
                                        if ($xml && isset($xml->url)) {
                                            foreach ($xml->url as $url_entry) {
                                                $current_urls[] = (string)$url_entry->loc;
                                            }
                                        }
                                    }
                                }
                                
                                $sitemap_content = implode("\n", $current_urls);
                                
                                // Handle form submission
                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                    $action = $_POST['action'] ?? '';
                                    $new_urls = $_POST['urls'] ?? '';
                                    
                                    // Count URLs
                                    $url_array = array_filter(array_map('trim', explode("\n", $new_urls)));
                                    $new_url_count = count($url_array);
                                    
                                    switch ($action) {
                                        case 'modify':
                                            // Update XML sitemap
                                            $result = Equalify_Public::update_monitor_sitemap($monitor_id, $url_array);
                                            if ($result) {
                                                echo '<h2>Monitor Updated Successfully</h2>';
                                                echo '<p>Your monitor sitemap has been updated with ' . $new_url_count . ' URLs.</p>';
                                            } else {
                                                echo '<h2>Error Updating Monitor</h2>';
                                                echo '<p>Failed to update the monitor sitemap.</p>';
                                            }
                                            break;
                                            
                                        case 'upgrade':
                                            // Find next higher plan
                                            $target_plan = Equalify_Public::find_subscription_plan($new_url_count, 'upgrade');
                                            if ($target_plan) {
                                                $switch_result = Equalify_Public::switch_subscription($subscription_id, $target_plan['subscription_id']);
                                                if ($switch_result) {
                                                    Equalify_Public::update_monitor_sitemap($monitor_id, $url_array);
                                                    echo '<h2>Subscription Upgraded Successfully</h2>';
                                                    echo '<p>Your subscription has been upgraded and monitor updated with ' . $new_url_count . ' URLs.</p>';
                                                } else {
                                                    echo '<h2>Error Upgrading Subscription</h2>';
                                                    echo '<p>Failed to upgrade subscription.</p>';
                                                }
                                            }
                                            break;
                                            
                                        case 'downgrade':
                                            // Find next lower plan
                                            $target_plan = Equalify_Public::find_subscription_plan($new_url_count, 'downgrade');
                                            if ($target_plan) {
                                                $switch_result = Equalify_Public::switch_subscription($subscription_id, $target_plan['subscription_id']);
                                                if ($switch_result) {
                                                    Equalify_Public::update_monitor_sitemap($monitor_id, $url_array);
                                                    echo '<h2>Subscription Downgraded Successfully</h2>';
                                                    echo '<p>Your subscription has been downgraded and monitor updated with ' . $new_url_count . ' URLs.</p>';
                                                } else {
                                                    echo '<h2>Error Downgrading Subscription</h2>';
                                                    echo '<p>Failed to downgrade subscription.</p>';
                                                }
                                            }
                                            break;
                                    }
                                    
                                    // Refresh monitor data after changes
                                    $monitor = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $monitor_id), ARRAY_A);
                                    
                                    // Reload sitemap content
                                    $current_urls = array();
                                    if (!empty($monitor['xml_sitemap'])) {
                                        $upload_dir = wp_upload_dir();
                                        $sitemap_path = $upload_dir['basedir'] . '/equalify-sitemaps/' . basename($monitor['xml_sitemap']);
                                        
                                        if (file_exists($sitemap_path)) {
                                            $xml_content = file_get_contents($sitemap_path);
                                            $xml = simplexml_load_string($xml_content);
                                            
                                            if ($xml && isset($xml->url)) {
                                                foreach ($xml->url as $url_entry) {
                                                    $current_urls[] = (string)$url_entry->loc;
                                                }
                                            }
                                        }
                                    }
                                    
                                    $sitemap_content = implode("\n", $current_urls);
                                }
                                
                                ?>
                                <h2>Modify Monitor: <strong><?php echo esc_html($monitor['property_name']); ?></strong></h2>
                                
                                <form id="modify_monitor_form" action="" method="post" enctype="application/x-www-form-urlencoded">
                                    <div>
                                        <p><strong><label for="urls">URLs for this monitor (required)</label></strong></p>
                                        <p id="urls_describe">Enter one URL per line, make sure all URLs start with https://</p>
                                        <p><textarea id="urls" name="urls" rows="15" required aria-describedby="urls_describe"><?php echo esc_textarea($sitemap_content); ?></textarea></p>
                                        <p id="url_count">Current URLs: <span id="current_count"><?php echo count($current_urls); ?></span></p>
                                        <p>Your subscription allows: <strong><?php echo $current_url_limit; ?></strong> URLs</p>
                                    </div>
                                    
                                    <div id="action_buttons">
                                        <!-- Buttons will be populated by JavaScript based on URL count -->
                                    </div>
                                </form>
                                
                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const urlsTextarea = document.getElementById('urls');
                                    const currentCountSpan = document.getElementById('current_count');
                                    const actionButtonsDiv = document.getElementById('action_buttons');
                                    const currentLimit = <?php echo $current_url_limit; ?>;
                                    
                                    // Subscription plans
                                    const plans = [
                                        <?php
                                        for ($i = 1; $i <= 10; $i++) {
                                            $url_count = get_option("equalify_url_count_$i", 0);
                                            $sub_id = get_option("equalify_subscription_id_$i", 0);
                                            if ($url_count > 0 && $sub_id > 0) {
                                                echo "{ url_count: $url_count, subscription_id: $sub_id },";
                                            }
                                        }
                                        ?>
                                    ].sort((a, b) => a.url_count - b.url_count);
                                    
                                    function updateButtons() {
                                        const urls = urlsTextarea.value.trim().split('\n').filter(url => url.trim() !== '');
                                        const urlCount = urls.length;
                                        currentCountSpan.textContent = urlCount;
                                        
                                        actionButtonsDiv.innerHTML = '';
                                        
                                        if (urlCount > currentLimit) {
                                            // Need upgrade
                                            const upgradePlan = plans.find(plan => plan.url_count >= urlCount && plan.url_count > currentLimit);
                                            if (upgradePlan) {
                                                actionButtonsDiv.innerHTML = `
                                                    <input type="hidden" name="action" value="upgrade">
                                                    <input type="submit" value="Upgrade to ${upgradePlan.url_count} URL plan" class="mb30">
                                                `;
                                            } else {
                                                actionButtonsDiv.innerHTML = '<p style="color: red;">No upgrade plan available for this URL count.</p>';
                                            }
                                        } else {
                                            // Check if downgrade is possible
                                            const downgradePlan = plans.slice().reverse().find(plan => plan.url_count >= urlCount && plan.url_count < currentLimit);
                                            
                                            if (downgradePlan) {
                                                actionButtonsDiv.innerHTML = `
                                                    <input type="hidden" name="action" value="downgrade">
                                                    <input type="submit" value="Downgrade to ${downgradePlan.url_count} URL plan" class="mb30">
                                                `;
                                            } else {
                                                // Just modify sitemap
                                                actionButtonsDiv.innerHTML = `
                                                    <input type="hidden" name="action" value="modify">
                                                    <input type="submit" value="Update Monitor" class="mb30">
                                                `;
                                            }
                                        }
                                    }
                                    
                                    urlsTextarea.addEventListener('input', updateButtons);
                                    updateButtons(); // Initial call
                                });
                                </script>
                                <?php
                            }
                        }
                    }
                }
            }
        }
    }
    ?>
    <div class="clear mb100"></div>

<?php
else :
    echo Equalify_Public::equalify_denied();
endif;