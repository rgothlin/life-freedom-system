<?php
/**
 * Financial Management Class
 * UPDATED: Fixed balance calculations to handle both incoming and outgoing transactions
 * 
 * File location: includes/class-lfs-financial.php
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
        add_action('save_post_lfs_transaction', array($this, 'calculate_transaction_sp'), 10, 3);
        add_action('wp_ajax_lfs_get_account_balances', array($this, 'ajax_get_account_balances'));
        add_action('wp_ajax_lfs_create_transaction', array($this, 'ajax_create_transaction'));
        add_action('wp_ajax_lfs_get_monthly_summary', array($this, 'ajax_get_monthly_summary'));
    }
    
    /**
     * Get balances for all accounts
     * FIXED: Now correctly handles both incoming and outgoing transactions
     */
    public function get_account_balances() {
        $accounts = get_terms(array(
            'taxonomy' => 'lfs_account',
            'hide_empty' => false,
        ));
        
        $balances = array();
        
        foreach ($accounts as $account) {
            $balance = 0;
            
            // Get all transactions where this account is RECEIVING money (to_account)
            $incoming_args = array(
                'post_type' => 'lfs_transaction',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => 'lfs_transaction_to',
                        'value' => $account->term_id,
                        'compare' => '=',
                    ),
                ),
            );
            
            $incoming_transactions = get_posts($incoming_args);
            
            foreach ($incoming_transactions as $transaction) {
                $amount = get_post_meta($transaction->ID, 'lfs_transaction_amount', true);
                $balance += floatval($amount); // ADD incoming money
            }
            
            // Get all transactions where this account is SENDING money (from_account)
            $outgoing_args = array(
                'post_type' => 'lfs_transaction',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'lfs_account',
                        'field' => 'term_id',
                        'terms' => $account->term_id,
                    ),
                ),
            );
            
            $outgoing_transactions = get_posts($outgoing_args);
            
            foreach ($outgoing_transactions as $transaction) {
                $amount = get_post_meta($transaction->ID, 'lfs_transaction_amount', true);
                $balance -= floatval($amount); // SUBTRACT outgoing money
            }
            
            $balances[] = array(
                'id' => $account->term_id,
                'name' => $account->name,
                'balance' => $balance,
            );
        }
        
        return $balances;
    }
    
    /**
     * Get balance for a specific account by name
     * 
     * @param string $account_name Name of the account (case-insensitive, partial match)
     * @return float Account balance
     */
    public function get_account_balance($account_name) {
        $balances = $this->get_account_balances();
        
        foreach ($balances as $account) {
            if (stripos($account['name'], $account_name) !== false) {
                return floatval($account['balance']);
            }
        }
        
        return 0;
    }
    
    /**
     * Get net worth (total across all accounts)
     */
    public function get_net_worth() {
        $balances = $this->get_account_balances();
        $total = 0;
        
        foreach ($balances as $account) {
            $total += $account['balance'];
        }
        
        return $total;
    }
    
    /**
     * Get monthly summary
     */
    public function get_monthly_summary($year = null, $month = null) {
        if (!$year) $year = date('Y');
        if (!$month) $month = date('m');
        
        $start_date = sprintf('%s-%s-01', $year, str_pad($month, 2, '0', STR_PAD_LEFT));
        $end_date = date('Y-m-t', strtotime($start_date));
        
        $args = array(
            'post_type' => 'lfs_transaction',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'lfs_transaction_date',
                    'value' => array($start_date, $end_date),
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
            'sp_earned' => 0,
            'budget_followed' => true,
            'leaks_count' => 0,
        );
        
        foreach ($transactions as $transaction) {
            $amount = floatval(get_post_meta($transaction->ID, 'lfs_transaction_amount', true));
            $category = get_post_meta($transaction->ID, 'lfs_transaction_category', true);
            $sp = intval(get_post_meta($transaction->ID, 'lfs_transaction_sp', true));
            $budget_followed = get_post_meta($transaction->ID, 'lfs_transaction_budget_followed', true) === '1';
            
            $summary['sp_earned'] += $sp;
            
            // Categorize transactions
            switch ($category) {
                case 'salary':
                case 'income':
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
                    // Check if transfer is to reward account
                    $to_account_id = get_post_meta($transaction->ID, 'lfs_transaction_to', true);
                    if ($to_account_id) {
                        $to_account = get_term($to_account_id, 'lfs_account');
                        if ($to_account && strpos(strtolower($to_account->name), 'belöning') !== false) {
                            $summary['to_reward_account'] += $amount;
                        }
                    }
                    break;
            }
            
            if (!$budget_followed) {
                $summary['budget_followed'] = false;
                $summary['leaks_count']++;
            }
        }
        
        // Bonus SP if no leaks this month
        if ($summary['budget_followed'] && count($transactions) > 0) {
            $summary['sp_earned'] += 50;
        }
        
        return $summary;
    }
    
    /**
     * Check for financial "leaks" (moving money from savings)
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
        
        $leak_count = count($transactions);
        $days_since_leak = 0;
        
        if ($leak_count === 0) {
            $last_leak = get_option('lfs_last_leak_date', false);
            if ($last_leak) {
                $days_since_leak = floor((time() - strtotime($last_leak)) / (60 * 60 * 24));
            }
        } else {
            // Update last leak date
            $latest_leak = $transactions[0];
            $leak_date = get_post_meta($latest_leak->ID, 'lfs_transaction_date', true);
            update_option('lfs_last_leak_date', $leak_date);
        }
        
        return array(
            'has_leaks' => $leak_count > 0,
            'leak_count' => $leak_count,
            'days_checked' => $days,
            'days_since_leak' => $days_since_leak,
        );
    }
    
    /**
     * Calculate SP for transaction
     */
    public function calculate_transaction_sp($post_id, $post, $update) {
        if (wp_is_post_revision($post_id) || $post->post_status !== 'publish') {
            return;
        }
        
        $amount = floatval(get_post_meta($post_id, 'lfs_transaction_amount', true));
        $category = get_post_meta($post_id, 'lfs_transaction_category', true);
        $budget_followed = get_post_meta($post_id, 'lfs_transaction_budget_followed', true) === '1';
        
        $sp = 0;
        
        // SP rules based on transaction type
        switch ($category) {
            case 'salary':
            case 'income':
                $sp = 10; // Base SP for receiving income
                break;
            
            case 'project_income':
                $sp = 25; // More SP for project income
                break;
            
            case 'savings':
                // SP based on amount saved
                $sp = min(floor($amount / 100), 50); // 1 SP per 100kr, max 50
                break;
            
            case 'transfer':
                // Check if transfer to reward account
                $to_account_id = get_post_meta($post_id, 'lfs_transaction_to', true);
                if ($to_account_id) {
                    $to_account = get_term($to_account_id, 'lfs_account');
                    if ($to_account && strpos(strtolower($to_account->name), 'belöning') !== false) {
                        $sp = 15;
                    }
                }
                break;
            
            case 'expense':
                // No SP for regular expenses
                $sp = 0;
                break;
        }
        
        // Penalty for breaking budget
        if (!$budget_followed) {
            $sp = -25; // Lose SP for leaks
        }
        
        update_post_meta($post_id, 'lfs_transaction_sp', $sp);
    }
    
    /**
     * Create new transaction
     */
    public function create_transaction($data) {
        $required = array('amount', 'date', 'category');
        
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                return new WP_Error('missing_field', sprintf(__('Fält %s krävs', 'life-freedom-system'), $field));
            }
        }
        
        $title = isset($data['title']) && !empty($data['title']) 
            ? $data['title'] 
            : __('Transaktion', 'life-freedom-system') . ' ' . $data['date'];
        
        $post_id = wp_insert_post(array(
            'post_type' => 'lfs_transaction',
            'post_title' => sanitize_text_field($title),
            'post_status' => 'publish',
        ));
        
        if (is_wp_error($post_id)) {
            return $post_id;
        }
        
        // Add meta
        update_post_meta($post_id, 'lfs_transaction_amount', floatval($data['amount']));
        update_post_meta($post_id, 'lfs_transaction_date', sanitize_text_field($data['date']));
        update_post_meta($post_id, 'lfs_transaction_category', sanitize_text_field($data['category']));
        
        // Set from account (taxonomy)
        if (isset($data['from_account']) && !empty($data['from_account'])) {
            wp_set_object_terms($post_id, intval($data['from_account']), 'lfs_account');
        }
        
        // Set to account (meta)
        if (isset($data['to_account']) && !empty($data['to_account'])) {
            update_post_meta($post_id, 'lfs_transaction_to', intval($data['to_account']));
        }
        
        // Budget followed flag
        $budget_followed = isset($data['budget_followed']) ? $data['budget_followed'] : true;
        update_post_meta($post_id, 'lfs_transaction_budget_followed', $budget_followed ? '1' : '0');
        
        // Trigger SP calculation
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