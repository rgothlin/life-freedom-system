<?php
/**
 * Financial Class
 * 
 * Handles financial tracking and account management
 */

if (!defined('ABSPATH')) {
    exit;
}

class LFS_Financial {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // AJAX hooks
        add_action('wp_ajax_lfs_get_account_balances', array($this, 'ajax_get_account_balances'));
        add_action('wp_ajax_lfs_create_transaction', array($this, 'ajax_create_transaction'));
        add_action('wp_ajax_lfs_get_monthly_summary', array($this, 'ajax_get_monthly_summary'));
    }
    
    /**
     * Get all account balances
     */
    public function get_account_balances() {
        $accounts = get_terms(array(
            'taxonomy' => 'lfs_account',
            'hide_empty' => false,
        ));
        
        $balances = array();
        
        foreach ($accounts as $account) {
            $balance = $this->calculate_account_balance($account->term_id);
            
            $balances[] = array(
                'id' => $account->term_id,
                'name' => $account->name,
                'balance' => $balance,
            );
        }
        
        return $balances;
    }
    
    /**
     * Calculate balance for specific account
     */
    private function calculate_account_balance($account_id) {
        // Get all transactions for this account
        $args = array(
            'post_type' => 'lfs_transaction',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'tax_query' => array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'lfs_account',
                    'field' => 'term_id',
                    'terms' => $account_id,
                    'operator' => 'IN',
                ),
            ),
        );
        
        $transactions = get_posts($args);
        $balance = 0;
        
        foreach ($transactions as $transaction) {
            $amount = floatval(get_post_meta($transaction->ID, 'lfs_transaction_amount', true));
            $from_terms = wp_get_post_terms($transaction->ID, 'lfs_account');
            $to_account_meta = get_post_meta($transaction->ID, 'lfs_transaction_to', true);
            
            // Check if this account received money
            if ($to_account_meta == $account_id) {
                $balance += $amount;
            }
            
            // Check if this account sent money
            if (!empty($from_terms)) {
                foreach ($from_terms as $term) {
                    if ($term->term_id == $account_id) {
                        $balance -= $amount;
                    }
                }
            }
        }
        
        return $balance;
    }
    
    /**
     * Get monthly financial summary
     */
    public function get_monthly_summary($year = null, $month = null) {
        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }
        
        $start_date = strtotime("$year-$month-01");
        $end_date = strtotime(date('Y-m-t', $start_date) . ' 23:59:59');
        
        $args = array(
            'post_type' => 'lfs_transaction',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'lfs_transaction_date',
                    'value' => array(date('Y-m-d', $start_date), date('Y-m-d', $end_date)),
                    'compare' => 'BETWEEN',
                    'type' => 'DATE',
                ),
            ),
        );
        
        $transactions = get_posts($args);
        
        $summary = array(
            'total_income' => 0,
            'salary_income' => 0,
            'project_income' => 0,
            'total_expenses' => 0,
            'total_savings' => 0,
            'to_reward_account' => 0,
            'budget_followed' => true,
            'leaks_count' => 0,
            'sp_earned' => 0,
        );
        
        foreach ($transactions as $transaction) {
            $amount = floatval(get_post_meta($transaction->ID, 'lfs_transaction_amount', true));
            $category = get_post_meta($transaction->ID, 'lfs_transaction_category', true);
            $budget_followed = get_post_meta($transaction->ID, 'lfs_transaction_budget_followed', true);
            $sp = intval(get_post_meta($transaction->ID, 'lfs_transaction_sp', true));
            
            $summary['sp_earned'] += $sp;
            
            switch ($category) {
                case 'salary':
                    $summary['salary_income'] += $amount;
                    $summary['total_income'] += $amount;
                    break;
                
                case 'project_income':
                    $summary['project_income'] += $amount;
                    $summary['total_income'] += $amount;
                    break;
                
                case 'expense':
                    $summary['total_expenses'] += $amount;
                    break;
                
                case 'savings':
                    $summary['total_savings'] += $amount;
                    break;
                
                case 'transfer':
                    $to_account = wp_get_post_terms($transaction->ID, 'lfs_account');
                    if (!empty($to_account) && $to_account[0]->name === 'Belöningskonto') {
                        $summary['to_reward_account'] += $amount;
                    }
                    break;
            }
            
            if (!$budget_followed) {
                $summary['budget_followed'] = false;
                $summary['leaks_count']++;
            }
        }
        
        return $summary;
    }
    
    /**
     * Check if there were any "leaks" (moving money from savings accounts)
     */
    public function check_for_leaks($days = 30) {
        $start_date = date('Y-m-d', strtotime("-{$days} days"));
        
        $args = array(
            'post_type' => 'lfs_transaction',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'lfs_transaction_date',
                    'value' => $start_date,
                    'compare' => '>=',
                    'type' => 'DATE',
                ),
                array(
                    'key' => 'lfs_transaction_budget_followed',
                    'value' => '0',
                    'compare' => '=',
                ),
            ),
        );
        
        $transactions = get_posts($args);
        
        return array(
            'has_leaks' => !empty($transactions),
            'leak_count' => count($transactions),
            'days_checked' => $days,
        );
    }
    
    /**
     * Create transaction
     */
    public function create_transaction($data) {
        $required = array('amount', 'date', 'category');
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return new WP_Error('missing_field', sprintf(__('Fält %s krävs', 'life-freedom-system'), $field));
            }
        }
        
        $post_id = wp_insert_post(array(
            'post_type' => 'lfs_transaction',
            'post_title' => isset($data['title']) ? $data['title'] : __('Transaktion', 'life-freedom-system') . ' ' . $data['date'],
            'post_status' => 'publish',
        ));
        
        if (is_wp_error($post_id)) {
            return $post_id;
        }
        
        // Add meta
        update_post_meta($post_id, 'lfs_transaction_amount', floatval($data['amount']));
        update_post_meta($post_id, 'lfs_transaction_date', sanitize_text_field($data['date']));
        update_post_meta($post_id, 'lfs_transaction_category', sanitize_text_field($data['category']));
        
        if (isset($data['from_account'])) {
            wp_set_object_terms($post_id, intval($data['from_account']), 'lfs_account');
        }
        
        if (isset($data['to_account'])) {
            update_post_meta($post_id, 'lfs_transaction_to', intval($data['to_account']));
        }
        
        if (isset($data['budget_followed'])) {
            update_post_meta($post_id, 'lfs_transaction_budget_followed', $data['budget_followed'] ? '1' : '0');
        }
        
        // Trigger SP calculation (via save_post hook)
        do_action('save_post_lfs_transaction', $post_id, get_post($post_id), true);
        
        return $post_id;
    }
    
    /**
     * AJAX: Get account balances
     */
    public function ajax_get_account_balances() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $balances = $this->get_account_balances();
        wp_send_json_success($balances);
    }
    
    /**
     * AJAX: Create transaction
     */
    public function ajax_create_transaction() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $data = array(
            'title' => isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '',
            'amount' => isset($_POST['amount']) ? floatval($_POST['amount']) : 0,
            'date' => isset($_POST['date']) ? sanitize_text_field($_POST['date']) : date('Y-m-d'),
            'category' => isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '',
            'from_account' => isset($_POST['from_account']) ? intval($_POST['from_account']) : null,
            'to_account' => isset($_POST['to_account']) ? intval($_POST['to_account']) : null,
            'budget_followed' => isset($_POST['budget_followed']) ? (bool)$_POST['budget_followed'] : true,
        );
        
        $result = $this->create_transaction($data);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(array(
            'id' => $result,
            'message' => __('Transaktion skapad!', 'life-freedom-system'),
        ));
    }
    
    /**
     * AJAX: Get monthly summary
     */
    public function ajax_get_monthly_summary() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $year = isset($_POST['year']) ? intval($_POST['year']) : null;
        $month = isset($_POST['month']) ? intval($_POST['month']) : null;
        
        $summary = $this->get_monthly_summary($year, $month);
        wp_send_json_success($summary);
    }
}