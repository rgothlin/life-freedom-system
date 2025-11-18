<?php
/**
 * Recurring Transactions Class
 * Hanterar återkommande transaktioner och automatisk generering
 * 
 * File location: includes/class-lfs-recurring-transactions.php
 */

if (!defined('ABSPATH')) {
    exit;
}

class LFS_Recurring_Transactions {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Hook into WordPress cron
        add_action('lfs_process_recurring_transactions', array($this, 'process_due_transactions'));
        
        // Schedule daily cron job if not already scheduled
        if (!wp_next_scheduled('lfs_process_recurring_transactions')) {
            wp_schedule_event(time(), 'daily', 'lfs_process_recurring_transactions');
        }
        
        // AJAX handlers
        add_action('wp_ajax_lfs_create_recurring_transaction', array($this, 'ajax_create_recurring_transaction'));
        add_action('wp_ajax_lfs_get_upcoming_transactions', array($this, 'ajax_get_upcoming_transactions'));
        add_action('wp_ajax_lfs_toggle_recurring_transaction', array($this, 'ajax_toggle_recurring_transaction'));
        add_action('wp_ajax_lfs_generate_transaction_now', array($this, 'ajax_generate_transaction_now')); // NEW: Manual generation
    }
    
    /**
     * Get all active recurring transactions
     */
    public function get_active_recurring_transactions() {
        $args = array(
            'post_type' => 'lfs_recurring_trans',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => 'lfs_recurring_active',
                    'value' => '1',
                    'compare' => '=',
                ),
                array(
                    'key' => 'lfs_recurring_active',
                    'compare' => 'NOT EXISTS', // Include posts without meta (defaults to active)
                ),
            ),
        );
        
        return get_posts($args);
    }
    
    /**
     * Calculate next due date based on frequency
     */
    private function calculate_next_due_date($last_date, $frequency, $custom_interval = null) {
        $timestamp = is_numeric($last_date) ? $last_date : strtotime($last_date);
        
        switch ($frequency) {
            case 'weekly':
                return date('Y-m-d', strtotime('+1 week', $timestamp));
            
            case 'monthly':
                return date('Y-m-d', strtotime('+1 month', $timestamp));
            
            case 'quarterly':
                return date('Y-m-d', strtotime('+3 months', $timestamp));
            
            case 'yearly':
                return date('Y-m-d', strtotime('+1 year', $timestamp));
            
            case 'custom':
                if ($custom_interval && $custom_interval > 0) {
                    return date('Y-m-d', strtotime("+{$custom_interval} days", $timestamp));
                }
                return date('Y-m-d', strtotime('+1 month', $timestamp));
            
            default:
                return date('Y-m-d', strtotime('+1 month', $timestamp));
        }
    }
    
    /**
     * Process recurring transactions that are due
     * This runs daily via WP Cron
     */
    public function process_due_transactions() {
        $today = date('Y-m-d');
        $recurring_transactions = $this->get_active_recurring_transactions();
        
        foreach ($recurring_transactions as $recurring) {
            $next_due = get_post_meta($recurring->ID, 'lfs_recurring_next_due', true);
            
            // Check if transaction is due
            if ($next_due && strtotime($next_due) <= strtotime($today)) {
                $this->generate_transaction_from_recurring($recurring->ID);
            }
        }
    }
    
    /**
     * Generate a real transaction from a recurring transaction
     */
    public function generate_transaction_from_recurring($recurring_id) {
        // Validate recurring transaction
        $recurring = get_post($recurring_id);
        
        if (!$recurring || $recurring->post_type !== 'lfs_recurring_trans') {
            return new WP_Error('invalid_recurring', __('Ogiltig återkommande transaktion', 'life-freedom-system'));
        }
        
        // Get recurring transaction data
        $amount = floatval(get_post_meta($recurring_id, 'lfs_recurring_amount', true));
        $category = get_post_meta($recurring_id, 'lfs_recurring_category', true);
        $from_account = get_post_meta($recurring_id, 'lfs_recurring_from_account', true);
        $to_account = get_post_meta($recurring_id, 'lfs_recurring_to_account', true);
        $frequency = get_post_meta($recurring_id, 'lfs_recurring_frequency', true);
        $custom_interval = get_post_meta($recurring_id, 'lfs_recurring_custom_interval', true);
        
        // Validate required data
        if (empty($amount) || empty($category)) {
            return new WP_Error('missing_data', __('Saknade data för att skapa transaktion', 'life-freedom-system'));
        }
        
        // Get Financial instance
        if (!class_exists('LFS_Financial')) {
            return new WP_Error('missing_class', __('LFS_Financial class saknas', 'life-freedom-system'));
        }
        
        $financial = LFS_Financial::get_instance();
        
        if (!$financial || !method_exists($financial, 'create_transaction')) {
            return new WP_Error('missing_method', __('create_transaction metod saknas', 'life-freedom-system'));
        }
        
        // Prepare transaction data
        $transaction_data = array(
            'title' => $recurring->post_title . ' (' . date('Y-m-d') . ')',
            'amount' => $amount,
            'date' => date('Y-m-d'),
            'category' => $category,
            'budget_followed' => true, // Recurring transactions count as planned
        );
        
        // Add from_account if exists
        if (!empty($from_account) && $from_account > 0) {
            $transaction_data['from_account'] = intval($from_account);
        }
        
        // Add to_account if exists
        if (!empty($to_account) && $to_account > 0) {
            $transaction_data['to_account'] = intval($to_account);
        }
        
        // Create the transaction
        $transaction_id = $financial->create_transaction($transaction_data);
        
        if (is_wp_error($transaction_id)) {
            return $transaction_id;
        }
        
        // Link transaction to recurring transaction
        update_post_meta($transaction_id, 'lfs_generated_from_recurring', $recurring_id);
        
        // Update next due date
        $current_due = get_post_meta($recurring_id, 'lfs_recurring_next_due', true);
        if (empty($current_due)) {
            $current_due = date('Y-m-d');
        }
        
        $next_due = $this->calculate_next_due_date($current_due, $frequency, $custom_interval);
        
        update_post_meta($recurring_id, 'lfs_recurring_next_due', $next_due);
        update_post_meta($recurring_id, 'lfs_recurring_last_generated', date('Y-m-d H:i:s'));
        
        // Increment counter
        $count = intval(get_post_meta($recurring_id, 'lfs_recurring_generated_count', true));
        update_post_meta($recurring_id, 'lfs_recurring_generated_count', $count + 1);
        
        return $transaction_id;
    }
    
    /**
     * Get upcoming transactions (next 30 days)
     */
    public function get_upcoming_transactions($days = 30) {
        $recurring_transactions = $this->get_active_recurring_transactions();
        $today = strtotime(date('Y-m-d'));
        $end_date = strtotime("+{$days} days");
        
        $upcoming = array();
        
        foreach ($recurring_transactions as $recurring) {
            $next_due = get_post_meta($recurring->ID, 'lfs_recurring_next_due', true);
            
            if (!$next_due) {
                continue;
            }
            
            $due_timestamp = strtotime($next_due);
            
            if ($due_timestamp >= $today && $due_timestamp <= $end_date) {
                $amount = floatval(get_post_meta($recurring->ID, 'lfs_recurring_amount', true));
                $from_account_id = get_post_meta($recurring->ID, 'lfs_recurring_from_account', true);
                $to_account_id = get_post_meta($recurring->ID, 'lfs_recurring_to_account', true);
                
                $from_account = $from_account_id ? get_term($from_account_id, 'lfs_account') : null;
                $to_account = $to_account_id ? get_term($to_account_id, 'lfs_account') : null;
                
                $days_until = floor(($due_timestamp - $today) / (60 * 60 * 24));
                
                $upcoming[] = array(
                    'id' => $recurring->ID,
                    'title' => $recurring->post_title,
                    'amount' => $amount,
                    'due_date' => $next_due,
                    'days_until' => $days_until,
                    'from_account' => $from_account ? $from_account->name : '-',
                    'to_account' => $to_account ? $to_account->name : '-',
                    'category' => get_post_meta($recurring->ID, 'lfs_recurring_category', true),
                    'frequency' => get_post_meta($recurring->ID, 'lfs_recurring_frequency', true),
                );
            }
        }
        
        // Sort by due date
        usort($upcoming, function($a, $b) {
            return strtotime($a['due_date']) - strtotime($b['due_date']);
        });
        
        return $upcoming;
    }
    
    /**
     * Create new recurring transaction
     */
    public function create_recurring_transaction($data) {
        $required = array('title', 'amount', 'frequency', 'start_date', 'from_account');
        
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                return new WP_Error('missing_field', sprintf(__('Fält %s krävs', 'life-freedom-system'), $field));
            }
        }
        
        $post_id = wp_insert_post(array(
            'post_type' => 'lfs_recurring_trans',
            'post_title' => sanitize_text_field($data['title']),
            'post_status' => 'publish',
            'post_content' => isset($data['description']) ? sanitize_textarea_field($data['description']) : '',
        ));
        
        if (is_wp_error($post_id)) {
            return $post_id;
        }
        
        // Save meta
        update_post_meta($post_id, 'lfs_recurring_amount', floatval($data['amount']));
        update_post_meta($post_id, 'lfs_recurring_frequency', sanitize_text_field($data['frequency']));
        update_post_meta($post_id, 'lfs_recurring_start_date', sanitize_text_field($data['start_date']));
        update_post_meta($post_id, 'lfs_recurring_next_due', sanitize_text_field($data['start_date']));
        update_post_meta($post_id, 'lfs_recurring_category', isset($data['category']) ? sanitize_text_field($data['category']) : 'expense');
        update_post_meta($post_id, 'lfs_recurring_from_account', intval($data['from_account']));
        update_post_meta($post_id, 'lfs_recurring_active', '1');
        update_post_meta($post_id, 'lfs_recurring_generated_count', 0);
        
        if (isset($data['to_account']) && !empty($data['to_account'])) {
            update_post_meta($post_id, 'lfs_recurring_to_account', intval($data['to_account']));
        }
        
        if ($data['frequency'] === 'custom' && isset($data['custom_interval'])) {
            update_post_meta($post_id, 'lfs_recurring_custom_interval', intval($data['custom_interval']));
        }
        
        return $post_id;
    }
    
    /**
     * Toggle recurring transaction active/inactive
     */
    public function toggle_recurring_transaction($recurring_id, $active = true) {
        $recurring = get_post($recurring_id);
        
        if (!$recurring || $recurring->post_type !== 'lfs_recurring_trans') {
            return new WP_Error('invalid_recurring', __('Ogiltig återkommande transaktion', 'life-freedom-system'));
        }
        
        update_post_meta($recurring_id, 'lfs_recurring_active', $active ? '1' : '0');
        
        return true;
    }
    
    /**
     * AJAX: Create recurring transaction
     */
    public function ajax_create_recurring_transaction() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        // Debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('=== RECURRING TRANSACTION AJAX ===');
            error_log('POST data: ' . print_r($_POST, true));
        }
        
        $data = array(
            'title' => isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '',
            'amount' => isset($_POST['amount']) ? floatval($_POST['amount']) : 0,
            'frequency' => isset($_POST['frequency']) ? sanitize_text_field($_POST['frequency']) : 'monthly',
            'start_date' => isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : date('Y-m-d'),
            'category' => isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'expense',
            'from_account' => isset($_POST['from_account']) ? intval($_POST['from_account']) : 0,
            'to_account' => isset($_POST['to_account']) && !empty($_POST['to_account']) ? intval($_POST['to_account']) : null,
            'description' => isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '',
            'custom_interval' => isset($_POST['custom_interval']) ? intval($_POST['custom_interval']) : null,
        );
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Processed data: ' . print_r($data, true));
        }
        
        $result = $this->create_recurring_transaction($data);
        
        if (is_wp_error($result)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ERROR: ' . $result->get_error_message());
            }
            wp_send_json_error($result->get_error_message());
        }
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('SUCCESS: Created recurring transaction ID: ' . $result);
        }
        
        wp_send_json_success(array(
            'id' => $result,
            'message' => __('Återkommande transaktion skapad!', 'life-freedom-system'),
        ));
    }
    
    /**
     * AJAX: Get upcoming transactions
     */
    public function ajax_get_upcoming_transactions() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $days = isset($_POST['days']) ? intval($_POST['days']) : 30;
        $upcoming = $this->get_upcoming_transactions($days);
        
        wp_send_json_success($upcoming);
    }
    
    /**
     * AJAX: Toggle recurring transaction
     */
    public function ajax_toggle_recurring_transaction() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $recurring_id = isset($_POST['recurring_id']) ? intval($_POST['recurring_id']) : 0;
        $active = isset($_POST['active']) ? (bool)$_POST['active'] : true;
        
        if (!$recurring_id) {
            wp_send_json_error(__('Ogiltigt ID', 'life-freedom-system'));
        }
        
        $result = $this->toggle_recurring_transaction($recurring_id, $active);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(array(
            'message' => $active 
                ? __('Återkommande transaktion aktiverad', 'life-freedom-system')
                : __('Återkommande transaktion pausad', 'life-freedom-system'),
        ));
    }
    
    /**
     * AJAX: Generate transaction now (manual test)
     */
    public function ajax_generate_transaction_now() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $recurring_id = isset($_POST['recurring_id']) ? intval($_POST['recurring_id']) : 0;
        
        if (!$recurring_id) {
            wp_send_json_error(__('Ogiltigt ID', 'life-freedom-system'));
        }
        
        $transaction_id = $this->generate_transaction_from_recurring($recurring_id);
        
        if (is_wp_error($transaction_id)) {
            wp_send_json_error($transaction_id->get_error_message());
        }
        
        wp_send_json_success(array(
            'transaction_id' => $transaction_id,
            'message' => __('Transaktion skapad! Gå till Ekonomi för att se den.', 'life-freedom-system'),
        ));
    }
}