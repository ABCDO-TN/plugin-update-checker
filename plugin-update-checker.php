<?php
/**
 * Plugin Update Checker Library 5.6.1
 * http://w-shadow.com/
 *
 * Copyright 2025 Janis Elsts
 * Released under the MIT license. See license.txt for details.
 */

require dirname(__FILE__) . '/load-v5p6.php';
// 1. Include our settings manager
require_once __DIR__ . '/includes/puc-settings-manager.php';

// 2. Fetch the saved settings from database
$puc_config = get_option('puc_update_settings');

// 3. Check if we have a Repository URL (Without it, we can't update)
if ( !empty($puc_config['repo_url']) ) {

    // Use the PucFactory to build the update checker
    // Note: We use __FILE__ assuming this code is in the root of the plugin being updated.
    $myUpdateChecker = PucFactory::buildUpdateChecker(
        $puc_config['repo_url'],
        __FILE__, // Full path to the main plugin file
        basename(__DIR__) // Slug (directory name)
    );

    // 4. Check for Access Token (Optional)
    // If the token exists and is not empty, set authentication
    if ( !empty($puc_config['access_token']) ) {
        $myUpdateChecker->setAuthentication($puc_config['access_token']);
    }

    // 5. Handle "Branch" switching or specific settings based on "Type" (Optional)
    // If user selected 'theme', usually setup is slightly different, 
    // but buildUpdateChecker usually auto-detects. 
    // However, purely for validation:
    if ( isset($puc_config['update_type']) && $puc_config['update_type'] === 'theme' ) {
        // Logic specifically for themes if needed, usually handled automatically.
    }
}