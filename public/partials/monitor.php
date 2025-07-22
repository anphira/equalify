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

    // Display unused monitors and create options
    if(Equalify_Public::equalify_allowed_create_access()) {
        echo Equalify_Public::equalify_create_new_monitor();
    }

    // Get monitors for current user
    $table_name = $wpdb->prefix . 'equalify_monitors';
    $monitors_data = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE owner_id = %d ORDER BY date_created DESC",
        get_current_user_id()
    ), ARRAY_A);
    
    $table_name = $wpdb->prefix . 'equalify_reports';
    $reports_data = $wpdb->get_results("SELECT report_id, url_count, equalify_csv FROM $table_name", ARRAY_A);
    
    // Check if query was successful
    if ($monitors_data === null || $reports_data === null) {
        echo '<p>Error retrieving monitor data.</p>';
    } elseif (!empty($monitors_data)) {
        ?>
        <table class="mt30 mb30">
            <thead>
                <tr>
                    <th>Monitor</th>
                    <th>Subscription</th>
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
                    
                    // Find matching report data
                    foreach ($reports_data as $report) {
                        if($report['report_id'] == $monitor['report_id']) {
                            $csv = $report['equalify_csv'];
                            $url_count = $report['url_count'];
                            break;
                        }
                    }
                    
                    // Get subscription information
                    $subscription_info = '';
                    if (class_exists('WC_Subscriptions') && $monitor['subscription_id']) {
                        $subscription = wcs_get_subscription($monitor['subscription_id']);
                        if ($subscription) {
                            $line_items = $subscription->get_items();
                            if (isset($line_items[$monitor['line_item_id']])) {
                                $line_item = $line_items[$monitor['line_item_id']];
                                $subscription_info = 'Order #' . $monitor['subscription_id'] . 
                                                   '<br>Item: ' . esc_html($line_item->get_name()) .
                                                   '<br>Status: ' . esc_html($subscription->get_status());
                            }
                        }
                    }
                    
                    echo '<tr>';
                        echo '<td>' . esc_html($monitor['property_name']) . '</td>';
                        echo '<td>' . $subscription_info . '</td>';
                        echo '<td>' . intval($url_count) . '<br><a href="' . 
                             esc_url(Equalify_Public::equalify_get_url('equalify_modify_url')) . 
                             '?id=' . intval($monitor['id']) . '" class="button">Modify<span class="screen-reader-text"> ' . 
                             esc_html($monitor['property_name']) . '</span></a></td>';
                        echo '<td>Created: ' . esc_html(date('Y-m-d', strtotime($monitor['date_created']))) . 
                             '<br>Scanned: ' . esc_html(date('Y-m-d', strtotime($monitor['last_scan']))) . '</td>';
                        echo '<td>';
                        if ($monitor['report_id']) {
                            echo '<a href="' . esc_url(Equalify_Public::equalify_get_url('equalify_reports_url')) . 
                                 '?id=' . esc_attr($monitor['report_id']) . '">View Reports<span class="screen-reader-text"> for ' . 
                                 esc_html($monitor['property_name']) . '</span></a>';
                            if ($csv) {
                                echo '<br><a href="' . esc_url($csv) . '">Download Latest CSV<span class="screen-reader-text"> for ' . 
                                     esc_html($monitor['property_name']) . '</span></a>';
                            }
                        } else {
                            echo '<em>No reports available yet</em>';
                        }
                        echo '</td>';
                    echo '</tr>';
                } ?>
            </tbody>
        </table>
    <?php 
    } else {
        echo '<p>You have no monitors currently. Use the options above to create your first monitor.</p>';
    }
    ?>

<?php
else :
    echo Equalify_Public::equalify_denied();
endif;