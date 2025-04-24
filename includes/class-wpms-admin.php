<?php
if (!defined('ABSPATH')) {
    exit;
}

class WPMS_Admin {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=product',
            __('Merge Categories', 'wp-merge-subcategories'),
            __('Merge Categories', 'wp-merge-subcategories'),
            'manage_woocommerce',
            'wp-merge-subcategories',
            array($this, 'render_admin_page')
        );
    }

    public function enqueue_admin_scripts($hook) {
        if ('product_page_wp-merge-subcategories' !== $hook) {
            return;
        }

        wp_enqueue_style('wpms-admin', WPMS_PLUGIN_URL . 'assets/css/admin.css', array(), WPMS_VERSION);
        wp_enqueue_script('wpms-admin', WPMS_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), WPMS_VERSION, true);
        wp_localize_script('wpms-admin', 'wpmsData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wpms_nonce')
        ));
    }

    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Merge WooCommerce Categories', 'wp-merge-subcategories'); ?></h1>
            
            <div class="wpms-notice">
                <h2><?php _e('Important Notice', 'wp-merge-subcategories'); ?></h2>
                <p><?php _e('Before proceeding, please make sure to:', 'wp-merge-subcategories'); ?></p>
                <ol>
                    <li><?php _e('Create a backup of your products by exporting them from WooCommerce', 'wp-merge-subcategories'); ?></li>
                    <li><?php _e('Test this process on a staging site first if possible', 'wp-merge-subcategories'); ?></li>
                    <li><?php _e('Review the categories that will be merged carefully', 'wp-merge-subcategories'); ?></li>
                </ol>
            </div>

            <div class="wpms-tools">
                <h2><?php _e('Category Tools', 'wp-merge-subcategories'); ?></h2>
                
                <div class="wpms-tool-section">
                    <h3><?php _e('Auto Merge Duplicates', 'wp-merge-subcategories'); ?></h3>
                    <p><?php _e('Automatically merge categories with the same name.', 'wp-merge-subcategories'); ?></p>
                    <button id="wpms-auto-merge" class="button button-primary">
                        <?php _e('Auto Merge Categories', 'wp-merge-subcategories'); ?>
                    </button>
                </div>

                <div class="wpms-tool-section">
                    <h3><?php _e('Manual Category Mapping', 'wp-merge-subcategories'); ?></h3>
                    <p><?php _e('Manually map products from one category to another.', 'wp-merge-subcategories'); ?></p>
                    <div id="wpms-mapping-interface">
                        <select id="wpms-source-category">
                            <option value=""><?php _e('Select source category', 'wp-merge-subcategories'); ?></option>
                        </select>
                        <select id="wpms-target-category">
                            <option value=""><?php _e('Select target category', 'wp-merge-subcategories'); ?></option>
                        </select>
                        <button id="wpms-map-categories" class="button button-secondary">
                            <?php _e('Map Categories', 'wp-merge-subcategories'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
} 