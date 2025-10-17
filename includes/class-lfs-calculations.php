<?php
/**
 * Calculations Class
 * 
 * Handles all point calculations and automatic updates
 * 
 * File location: includes/class-lfs-calculations.php
 */

if (!defined('ABSPATH')) {
    exit;
}

class LFS_Calculations {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Save post hooks for automatic calculations
        add_action('save_post_life_activity', array($this, 'update_project_points'), 20, 3);
        add_action('save_post_lfs_transaction', array($this, 'calculate_transaction_sp'), 20, 3);
        
        // AJAX hooks for live calculations
        add_action('wp_ajax_lfs_get_current_points', array($this, 'ajax_get_current_points'));
        add_action('wp_ajax_lfs_get_weekly_progress', array($this, 'ajax_get_weekly_progress'));
        add_action('wp_ajax_lfs_check_reward_eligibility', array($this, 'ajax_check_reward_eligibility'));
        add_action('wp_ajax_lfs_calculate_streak', array($this, 'ajax_calculate_streak'));
    }
    
    /**
     * Get current total points for each type
     */
    public function get_current_points($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        $args = array(
            'post_type' => 'life_activity',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'author' => $user_id,
        );
        
        $activities = get_posts($args);
        
        $totals = array(
            'fp' => 0,
            'bp' => 0,
            'sp' => 0,
            'total' => 0,
        );
        
        foreach ($activities as $activity) {
            $fp = intval(get_post_meta($activity->ID, 'lfs_fp', true));
            $bp = intval(get_post_meta($activity->ID, 'lfs_bp', true));
            $sp = intval(get_post_meta($activity->ID, 'lfs_sp', true));
            
            $totals['fp'] += $fp;
            $totals['bp'] += $bp;
            $totals['sp'] += $sp;
        }
        
        // Add SP from transactions
        $transaction_args = array(
            'post_type' => 'lfs_transaction',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'author' => $user_id,
        );
        
        $transactions = get_posts($transaction_args);
        foreach ($transactions as $transaction) {
            $sp = intval(get_post_meta($transaction->ID, 'lfs_transaction_sp', true));
            $totals['sp'] += $sp;
        }
        
        $totals['total'] = $totals['fp'] + $totals['bp'] + $totals['sp'];
        
        return $totals;
    }
    
    /**
     * Get weekly points
     */
    public function get_weekly_points($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        $week_start = strtotime('monday this week');
        $week_end = strtotime('sunday this week 23:59:59');
        
        $args = array(
            'post_type' => 'life_activity',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'author' => $user_id,
            'meta_query' => array(
                array(
                    'key' => 'lfs_activity_datetime',
                    'value' => array($week_start, $week_end),
                    'compare' => 'BETWEEN',
                    'type' => 'NUMERIC',
                ),
            ),
        );
        
        $activities = get_posts($args);
        
        $totals = array(
            'fp' => 0,
            'bp' => 0,
            'sp' => 0,
        );
        
        foreach ($activities as $activity) {
            $totals['fp'] += intval(get_post_meta($activity->ID, 'lfs_fp', true));
            $totals['bp'] += intval(get_post_meta($activity->ID, 'lfs_bp', true));
            $totals['sp'] += intval(get_post_meta($activity->ID, 'lfs_sp', true));
        }
        
        return $totals;
    }
    
    /**
     * Get points for a specific date range
     */
    public function get_points_by_date_range($start_date, $end_date, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        $args = array(
            'post_type' => 'life_activity',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'author' => $user_id,
            'meta_query' => array(
                array(
                    'key' => 'lfs_activity_datetime',
                    'value' => array(strtotime($start_date), strtotime($end_date)),
                    'compare' => 'BETWEEN',
                    'type' => 'NUMERIC',
                ),
            ),
        );
        
        $activities = get_posts($args);
        
        $totals = array(
            'fp' => 0,
            'bp' => 0,
            'sp' => 0,
        );
        
        foreach ($activities as $activity) {
            $totals['fp'] += intval(get_post_meta($activity->ID, 'lfs_fp', true));
            $totals['bp'] += intval(get_post_meta($activity->ID, 'lfs_bp', true));
            $totals['sp'] += intval(get_post_meta($activity->ID, 'lfs_sp', true));
        }
        
        return $totals;
    }
    
    /**
     * Update project total FP when activity is saved
     */
    public function update_project_points($post_id, $post, $update) {
        // Avoid autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check if activity has related project
        $project_id = get_post_meta($post_id, 'lfs_related_project', true);
        
        if ($project_id) {
            // Get all activities for this project
            $args = array(
                'post_type' => 'life_activity',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => 'lfs_related_project',
                        'value' => $project_id,
                        'compare' => '=',
                    ),
                ),
            );
            
            $activities = get_posts($args);
            $total_fp = 0;
            
            foreach ($activities as $activity) {
                $total_fp += intval(get_post_meta($activity->ID, 'lfs_fp', true));
            }
            
            // Update project total
            update_post_meta($project_id, 'lfs_project_total_fp', $total_fp);
            
            // Calculate progress percentage
            $fp_goal = intval(get_post_meta($project_id, 'lfs_project_fp_goal', true));
            if ($fp_goal > 0) {
                $progress = min(100, round(($total_fp / $fp_goal) * 100));
                update_post_meta($project_id, 'lfs_project_progress', $progress);
            }
        }
    }
    
    /**
     * Calculate SP for transactions
     */
    public function calculate_transaction_sp($post_id, $post, $update) {
        // Avoid autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        $amount = floatval(get_post_meta($post_id, 'lfs_transaction_amount', true));
        $category = get_post_meta($post_id, 'lfs_transaction_category', true);
        $budget_followed = get_post_meta($post_id, 'lfs_transaction_budget_followed', true);
        
        $sp = 0;
        
        // Calculate SP based on category
        switch ($category) {
            case 'project_income':
                // 50 SP per 1000 kr from own projects
                $sp = floor($amount / 1000) * 50;
                break;
            
            case 'savings':
                // 1 SP per 100 kr saved
                $sp = floor($amount / 100);
                break;
            
            case 'transfer':
                // Check if transfer is to reward account
                $to_account_id = get_post_meta($post_id, 'lfs_transaction_to', true);
                if ($to_account_id) {
                    $to_account = get_term($to_account_id, 'lfs_account');
                    if ($to_account && $to_account->name === 'Belöningskonto') {
                        $sp = floor($amount / 100);
                    }
                }
                break;
        }
        
        // Bonus SP if budget was followed
        if ($budget_followed) {
            $sp += 20;
        }
        
        update_post_meta($post_id, 'lfs_transaction_sp', $sp);
    }
    
    /**
     * Convert points to currency based on current phase
     */
    public function points_to_currency($points, $phase = null) {
        if (!$phase) {
            $phase = get_option('lfs_current_phase', 'survival');
        }
        
        $points_per_kr = floatval(get_option('lfs_points_per_kr', 0.5));
        
        return ($points / 10) * (1 / $points_per_kr);
    }
    
    /**
     * Get reward account balance
     */
    public function get_reward_account_balance() {
        $current_points = $this->get_current_points();
        $phase = get_option('lfs_current_phase', 'survival');
        
        // Calculate theoretical balance based on points
        $balance = $this->points_to_currency($current_points['total'], $phase);
        
        // Subtract redeemed rewards
        $args = array(
            'post_type' => 'lfs_reward',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'lfs_reward_status',
                    'value' => 'redeemed',
                    'compare' => '=',
                ),
            ),
        );
        
        $redeemed_rewards = get_posts($args);
        $spent = 0;
        
        foreach ($redeemed_rewards as $reward) {
            $spent += floatval(get_post_meta($reward->ID, 'lfs_reward_cost', true));
        }
        
        return max(0, $balance - $spent);
    }
    
    /**
     * Check if user can afford a reward
     */
    public function can_afford_reward($reward_id) {
        $current_points = $this->get_current_points();
        
        $fp_required = intval(get_post_meta($reward_id, 'lfs_reward_fp_required', true));
        $bp_required = intval(get_post_meta($reward_id, 'lfs_reward_bp_required', true));
        $sp_required = intval(get_post_meta($reward_id, 'lfs_reward_sp_required', true));
        $total_required = intval(get_post_meta($reward_id, 'lfs_reward_total_required', true));
        
        // If total_required is set, check if any combination of points meets it
        if ($total_required > 0) {
            return $current_points['total'] >= $total_required;
        }
        
        // Otherwise check each point type individually
        return ($current_points['fp'] >= $fp_required &&
                $current_points['bp'] >= $bp_required &&
                $current_points['sp'] >= $sp_required);
    }
    
    /**
     * Calculate current streak
     */
    public function calculate_streak($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        $streak = 0;
        $current_date = current_time('Y-m-d');
        
        for ($i = 0; $i < 365; $i++) {
            $check_date = date('Y-m-d', strtotime("-{$i} days", strtotime($current_date)));
            $check_start = strtotime($check_date . ' 00:00:00');
            $check_end = strtotime($check_date . ' 23:59:59');
            
            $args = array(
                'post_type' => 'life_activity',
                'post_status' => 'publish',
                'posts_per_page' => 1,
                'author' => $user_id,
                'meta_query' => array(
                    array(
                        'key' => 'lfs_activity_datetime',
                        'value' => array($check_start, $check_end),
                        'compare' => 'BETWEEN',
                        'type' => 'NUMERIC',
                    ),
                ),
            );
            
            $activities = get_posts($args);
            
            if (empty($activities)) {
                break;
            }
            
            $streak++;
        }
        
        update_option('lfs_streak_days', $streak);
        
        return $streak;
    }
    
    /**
     * Get activities grouped by day for chart
     */
    public function get_daily_points_for_chart($days = 7, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        $data = array();
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $date_start = strtotime($date . ' 00:00:00');
            $date_end = strtotime($date . ' 23:59:59');
            
            // Day label for chart
            if ($days <= 7) {
                $label = date('D', strtotime($date)); // Mon, Tue, etc.
            } else {
                $label = date('M j', strtotime($date)); // Jan 15, etc.
            }
            
            $args = array(
                'post_type' => 'life_activity',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'author' => $user_id,
                'meta_query' => array(
                    array(
                        'key' => 'lfs_activity_datetime',
                        'value' => array($date_start, $date_end),
                        'compare' => 'BETWEEN',
                        'type' => 'NUMERIC',
                    ),
                ),
            );
            
            $activities = get_posts($args);
            
            $fp = 0;
            $bp = 0;
            $sp = 0;
            
            foreach ($activities as $activity) {
                $fp += intval(get_post_meta($activity->ID, 'lfs_fp', true));
                $bp += intval(get_post_meta($activity->ID, 'lfs_bp', true));
                $sp += intval(get_post_meta($activity->ID, 'lfs_sp', true));
            }
            
            $data[] = array(
                'label' => $label,
                'date' => $date,
                'fp' => $fp,
                'bp' => $bp,
                'sp' => $sp,
                'total' => $fp + $bp + $sp,
            );
        }
        
        return $data;
    }
    
    /**
     * Get activity distribution by type
     */
    public function get_activity_type_distribution($days = 30, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        $start_date = date('Y-m-d', strtotime("-{$days} days"));
        $start_timestamp = strtotime($start_date . ' 00:00:00');
        
        $args = array(
            'post_type' => 'life_activity',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'author' => $user_id,
            'meta_query' => array(
                array(
                    'key' => 'lfs_activity_datetime',
                    'value' => $start_timestamp,
                    'compare' => '>=',
                    'type' => 'NUMERIC',
                ),
            ),
        );
        
        $activities = get_posts($args);
        $distribution = array();
        
        foreach ($activities as $activity) {
            $types = wp_get_post_terms($activity->ID, 'activity_type');
            
            $type_name = 'Okategoriserad';
            if (!empty($types) && !is_wp_error($types)) {
                $type_name = $types[0]->name;
            }
            
            if (!isset($distribution[$type_name])) {
                $distribution[$type_name] = array(
                    'count' => 0,
                    'fp' => 0,
                    'bp' => 0,
                    'sp' => 0,
                    'total' => 0,
                );
            }
            
            $distribution[$type_name]['count']++;
            $distribution[$type_name]['fp'] += intval(get_post_meta($activity->ID, 'lfs_fp', true));
            $distribution[$type_name]['bp'] += intval(get_post_meta($activity->ID, 'lfs_bp', true));
            $distribution[$type_name]['sp'] += intval(get_post_meta($activity->ID, 'lfs_sp', true));
            $distribution[$type_name]['total'] = $distribution[$type_name]['fp'] + 
                                                  $distribution[$type_name]['bp'] + 
                                                  $distribution[$type_name]['sp'];
        }
        
        return $distribution;
    }
    
    /**
     * Get monthly stats
     */
    public function get_monthly_stats($year = null, $month = null, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }
        
        $start_date = "{$year}-{$month}-01 00:00:00";
        $end_date = date('Y-m-t 23:59:59', strtotime($start_date));
        
        $points = $this->get_points_by_date_range($start_date, $end_date, $user_id);
        
        // Count activities
        $args = array(
            'post_type' => 'life_activity',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'author' => $user_id,
            'meta_query' => array(
                array(
                    'key' => 'lfs_activity_datetime',
                    'value' => array(strtotime($start_date), strtotime($end_date)),
                    'compare' => 'BETWEEN',
                    'type' => 'NUMERIC',
                ),
            ),
        );
        
        $activities = get_posts($args);
        
        return array(
            'year' => $year,
            'month' => $month,
            'points' => $points,
            'activity_count' => count($activities),
            'activities' => $activities,
        );
    }
    
    /**
     * Get progress towards milestone
     */
    public function get_milestone_progress($milestone_id) {
        $current_points = $this->get_current_points();
        
        $fp_required = intval(get_post_meta($milestone_id, 'lfs_milestone_fp_required', true));
        $bp_required = intval(get_post_meta($milestone_id, 'lfs_milestone_bp_required', true));
        $sp_required = intval(get_post_meta($milestone_id, 'lfs_milestone_sp_required', true));
        
        $fp_progress = $fp_required > 0 ? min(100, round(($current_points['fp'] / $fp_required) * 100)) : 100;
        $bp_progress = $bp_required > 0 ? min(100, round(($current_points['bp'] / $bp_required) * 100)) : 100;
        $sp_progress = $sp_required > 0 ? min(100, round(($current_points['sp'] / $sp_required) * 100)) : 100;
        
        $overall_progress = round(($fp_progress + $bp_progress + $sp_progress) / 3);
        
        return array(
            'fp_progress' => $fp_progress,
            'bp_progress' => $bp_progress,
            'sp_progress' => $sp_progress,
            'overall_progress' => $overall_progress,
            'is_complete' => ($fp_progress >= 100 && $bp_progress >= 100 && $sp_progress >= 100),
        );
    }
    
    /**
     * AJAX: Get current points
     */
    public function ajax_get_current_points() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $points = $this->get_current_points();
        wp_send_json_success($points);
    }
    
    /**
     * AJAX: Get weekly progress
     */
    public function ajax_get_weekly_progress() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $weekly_points = $this->get_weekly_points();
        $goals = array(
            'fp' => intval(get_option('lfs_weekly_fp_goal', 500)),
            'bp' => intval(get_option('lfs_weekly_bp_goal', 300)),
            'sp' => intval(get_option('lfs_weekly_sp_goal', 400)),
        );
        
        $progress = array(
            'fp' => array(
                'current' => $weekly_points['fp'],
                'goal' => $goals['fp'],
                'percentage' => $goals['fp'] > 0 ? round(($weekly_points['fp'] / $goals['fp']) * 100) : 0,
            ),
            'bp' => array(
                'current' => $weekly_points['bp'],
                'goal' => $goals['bp'],
                'percentage' => $goals['bp'] > 0 ? round(($weekly_points['bp'] / $goals['bp']) * 100) : 0,
            ),
            'sp' => array(
                'current' => $weekly_points['sp'],
                'goal' => $goals['sp'],
                'percentage' => $goals['sp'] > 0 ? round(($weekly_points['sp'] / $goals['sp']) * 100) : 0,
            ),
        );
        
        wp_send_json_success($progress);
    }
    
    /**
     * AJAX: Check reward eligibility
     */
    public function ajax_check_reward_eligibility() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $reward_id = isset($_POST['reward_id']) ? intval($_POST['reward_id']) : 0;
        
        if (!$reward_id) {
            wp_send_json_error('Invalid reward ID');
        }
        
        $can_afford = $this->can_afford_reward($reward_id);
        $balance = $this->get_reward_account_balance();
        $cost = floatval(get_post_meta($reward_id, 'lfs_reward_cost', true));
        
        wp_send_json_success(array(
            'can_afford' => $can_afford,
            'balance' => $balance,
            'cost' => $cost,
            'enough_money' => $balance >= $cost,
        ));
    }
    
    /**
     * AJAX: Calculate streak
     */
    public function ajax_calculate_streak() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $streak = $this->calculate_streak();
        
        wp_send_json_success(array(
            'streak' => $streak,
            'message' => sprintf(__('Din streak är %d dagar!', 'life-freedom-system'), $streak),
        ));
    }
}