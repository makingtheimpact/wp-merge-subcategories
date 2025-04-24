<?php
/**
 * Plugin Name: WP Merge Subcategories
 * Plugin URI: https://github.com/makingtheimpact/wp-merge-subcategories
 * Description: Automatically merge duplicate WooCommerce product categories and subcategories with advanced mapping tools.
 * Version: 1.0.0
 * Author: Lisa Li, Making The Impact LLC
 * Author URI: https://makingtheimpact.com
 * Text Domain: wp-merge-subcategories
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 * HPOS: true
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WPMS_VERSION', '1.0.0');
define('WPMS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WPMS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once WPMS_PLUGIN_DIR . 'includes/class-wpms-admin.php';
require_once WPMS_PLUGIN_DIR . 'includes/class-wpms-merger.php';

// Initialize the plugin
function wpms_init() {
    if (class_exists('WooCommerce')) {
        // Add HPOS compatibility
        add_action('before_woocommerce_init', function() {
            if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
            }
        });
        
        new WPMS_Admin();
        new WPMS_Merger();
    }
}
add_action('plugins_loaded', 'wpms_init'); 