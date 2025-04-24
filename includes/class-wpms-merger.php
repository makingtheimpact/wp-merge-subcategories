<?php
if (!defined('ABSPATH')) {
    exit;
}

class WPMS_Merger {
    private $batch_size = 50;
    private $current_batch = 0;
    private $total_batches = 0;
    private $categories_to_process = array();

    public function __construct() {
        add_action('wp_ajax_wpms_auto_merge', array($this, 'handle_auto_merge'));
        add_action('wp_ajax_wpms_map_categories', array($this, 'handle_map_categories'));
        add_action('wpms_process_merge_batch', array($this, 'process_merge_batch'));
        add_action('admin_init', array($this, 'check_for_pending_merges'));
    }

    public function check_for_pending_merges() {
        if (get_option('wpms_pending_merges')) {
            $this->process_merge_batch();
        }
    }

    public function handle_auto_merge() {
        check_ajax_referer('wpms_nonce', 'nonce');

        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error('Unauthorized');
        }

        $categories = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ));

        $this->categories_to_process = array();
        $merged = array();
        $skipped = array();

        foreach ($categories as $category) {
            if (in_array($category->term_id, $merged)) {
                continue;
            }

            $duplicates = get_terms(array(
                'taxonomy' => 'product_cat',
                'name' => $category->name,
                'hide_empty' => false,
                'exclude' => array($category->term_id),
            ));

            if (!empty($duplicates)) {
                foreach ($duplicates as $duplicate) {
                    $this->categories_to_process[] = array(
                        'target_id' => $category->term_id,
                        'source_id' => $duplicate->term_id,
                        'target_name' => $category->name,
                        'source_name' => $duplicate->name
                    );
                    $merged[] = $duplicate->term_id;
                }
            } else {
                $skipped[] = $category->name;
            }
        }

        $this->total_batches = ceil(count($this->categories_to_process) / $this->batch_size);
        $this->current_batch = 0;

        // Store the data in options
        update_option('wpms_pending_merges', $this->categories_to_process);
        update_option('wpms_current_batch', 0);
        update_option('wpms_total_batches', $this->total_batches);

        // Process first batch immediately
        $this->process_merge_batch();

        wp_send_json_success(array(
            'message' => sprintf(
                __('Starting merge process. %d categories to merge, %d categories skipped.', 'wp-merge-subcategories'),
                count($merged),
                count($skipped)
            ),
            'total_batches' => $this->total_batches,
            'categories_to_merge' => $this->categories_to_process
        ));
    }

    public function process_merge_batch() {
        $categories_to_process = get_option('wpms_pending_merges', array());
        $current_batch = get_option('wpms_current_batch', 0);
        $total_batches = get_option('wpms_total_batches', 0);

        if (empty($categories_to_process)) {
            return;
        }

        $start = $current_batch * $this->batch_size;
        $end = min($start + $this->batch_size, count($categories_to_process));
        
        for ($i = $start; $i < $end; $i++) {
            if (isset($categories_to_process[$i])) {
                $this->merge_categories(
                    $categories_to_process[$i]['target_id'],
                    $categories_to_process[$i]['source_id']
                );
            }
        }

        $current_batch++;
        update_option('wpms_current_batch', $current_batch);

        if ($current_batch < $total_batches) {
            wp_schedule_single_event(time() + 5, 'wpms_process_merge_batch');
        } else {
            delete_option('wpms_pending_merges');
            delete_option('wpms_current_batch');
            delete_option('wpms_total_batches');
        }
    }

    public function handle_map_categories() {
        check_ajax_referer('wpms_nonce', 'nonce');

        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error('Unauthorized');
        }

        $source_id = intval($_POST['source_id']);
        $target_id = intval($_POST['target_id']);

        if (!$source_id || !$target_id) {
            wp_send_json_error('Invalid category IDs');
        }

        $result = $this->merge_categories($target_id, $source_id);

        if ($result) {
            wp_send_json_success(array(
                'message' => __('Categories merged successfully', 'wp-merge-subcategories')
            ));
        } else {
            wp_send_json_error('Failed to merge categories');
        }
    }

    private function merge_categories($target_id, $source_id) {
        // Get all products in the source category
        $products = get_posts(array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $source_id
                )
            )
        ));

        // Move products to target category
        foreach ($products as $product) {
            wp_set_object_terms($product->ID, $target_id, 'product_cat', true);
        }

        // Delete the source category
        wp_delete_term($source_id, 'product_cat');
        
        return true;
    }
} 