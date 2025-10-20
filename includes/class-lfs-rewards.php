<?php
/**
 * Rewards Class - UPPDATERAD VERSION
 * 
 * File location: includes/class-lfs-rewards.php
 * Hanterar belöningar med recurring functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class LFS_Rewards {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // AJAX hooks
        add_action('wp_ajax_lfs_redeem_reward', array($this, 'ajax_redeem_reward'));
        add_action('wp_ajax_lfs_get_rewards_by_level', array($this, 'ajax_get_rewards_by_level'));
        
        // Cron för att återställa recurring rewards
        add_action('lfs_reset_daily_rewards', array($this, 'reset_daily_rewards'));
        
        // Registrera cron om det inte redan är schemalagt
        if (!wp_next_scheduled('lfs_reset_daily_rewards')) {
            wp_schedule_event(strtotime('tomorrow 00:00:00'), 'daily', 'lfs_reset_daily_rewards');
        }

        // General recurring reset (daily check)
        add_action('lfs_reset_recurring_rewards', array($this, 'reset_recurring_rewards'));
        if (!wp_next_scheduled('lfs_reset_recurring_rewards')) {
            wp_schedule_event(strtotime('tomorrow 00:10:00'), 'daily', 'lfs_reset_recurring_rewards');
        }
    }

/**
 * Normalize stored reward status to canonical values.
 * Allowed canonical: 'available', 'redeemed'
 */
private function normalize_status($raw) {
    $raw = is_string($raw) ? strtolower(trim($raw)) : '';
    if (in_array($raw, array('redeemed', 'inlöst', 'inlost'))) return 'redeemed';
    if (in_array($raw, array('available', 'tillgänglig', 'tillganglig'))) return 'available';
    return $raw ? $raw : 'available';
}

/**
 * Set reward status to available and clear redeemed date (+optional taxonomy mirror).
 */
private function set_status_available($reward_id) {
    update_post_meta($reward_id, 'lfs_reward_status', 'available');
    delete_post_meta($reward_id, 'lfs_reward_redeemed_date');
    if (taxonomy_exists('lfs_reward_state')) {
        $term = get_term_by('slug', 'available', 'lfs_reward_state');
        if ($term && !is_wp_error($term)) {
            wp_set_object_terms($reward_id, (int) $term->term_id, 'lfs_reward_state', false);
        }
    }
}

/**
 * Sum of points earned today across all activities.
 * If you need per-user sums, hook 'lfs/points_query_args' to add 'author'.
 * Returns array('fp'=>int,'bp'=>int,'sp'=>int,'total'=>int)
 */
private function get_points_earned_today() {
    $now  = current_time('timestamp');
    $from = strtotime(date('Y-m-d 00:00:00', $now), $now);
    $to   = strtotime(date('Y-m-d 23:59:59', $now), $now);

    $meta_query = array(
        'relation' => 'AND',
        array(
            'key'     => 'lfs_activity_datetime',
            'value'   => array($from, $to),
            'compare' => 'BETWEEN',
            'type'    => 'NUMERIC',
        ),
    );

    $args = array(
        'post_type'      => 'life_activity',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => $meta_query,
    );
    /**
     * Allow narrowing the daily points query, e.g., per-user:
     * add_filter('lfs/points_query_args', function($args){ $args['author'] = get_current_user_id(); return $args; });
     */
    $args = apply_filters('lfs/points_query_args', $args);

    $ids = get_posts($args);
    $fp = $bp = $sp = 0;
    if (!empty($ids)) {
        foreach ($ids as $id) {
            $fp += intval(get_post_meta($id, 'lfs_fp', true));
            $bp += intval(get_post_meta($id, 'lfs_bp', true));
            $sp += intval(get_post_meta($id, 'lfs_sp', true));
        }
    }
    return array('fp'=>$fp, 'bp'=>$bp, 'sp'=>$sp, 'total'=>($fp+$bp+$sp));
}




    
    /**
     * Redeem a reward
     */
    public function redeem_reward($reward_id) {
        $calculations = LFS_Calculations::get_instance();
        
        // Kontrollera att belöningen existerar
        $reward = get_post($reward_id);
        if (!$reward || $reward->post_type !== 'lfs_reward') {
            return new WP_Error('invalid_reward', __('Ogiltig belöning', 'life-freedom-system'));
        }
        
        // Kontrollera status
        $status = get_post_meta($reward_id, 'lfs_reward_status', true);
        if ($status === 'redeemed' && get_post_meta($reward_id, 'lfs_reward_recurring', true) !== 'yes') {
            return new WP_Error('already_redeemed', __('Denna belöning är redan inlöst', 'life-freedom-system'));
        }
        
        // Kontrollera att användaren har råd
        if (!$calculations->can_afford_reward($reward_id)) {
            return new WP_Error('cannot_afford', __('Du har inte tillräckligt med poäng eller pengar för denna belöning', 'life-freedom-system'));
        }
        
        // Hämta kostnader
        $cost = floatval(get_post_meta($reward_id, 'lfs_reward_cost', true));
        $fp_req = intval(get_post_meta($reward_id, 'lfs_reward_fp_required', true));
        $bp_req = intval(get_post_meta($reward_id, 'lfs_reward_bp_required', true));
        $sp_req = intval(get_post_meta($reward_id, 'lfs_reward_sp_required', true));
        $total_req = intval(get_post_meta($reward_id, 'lfs_reward_total_required', true));
        
        // Dra av poäng
        if ($total_req > 0) {
            // Om totalt poäng krävs, dra av från alla tre proportionellt
            $current = $calculations->get_current_points();
            $total_current = $current['fp'] + $current['bp'] + $current['sp'];
            
            if ($total_current >= $total_req) {
                $fp_deduct = round(($current['fp'] / $total_current) * $total_req);
                $bp_deduct = round(($current['bp'] / $total_current) * $total_req);
                $sp_deduct = $total_req - $fp_deduct - $bp_deduct;
                
                update_option('lfs_current_fp', max(0, $current['fp'] - $fp_deduct));
                update_option('lfs_current_bp', max(0, $current['bp'] - $bp_deduct));
                update_option('lfs_current_sp', max(0, $current['sp'] - $sp_deduct));
            }
        } else {
            // Dra av specifika poäng
            if ($fp_req > 0) {
                $current_fp = intval(get_option('lfs_current_fp', 0));
                update_option('lfs_current_fp', max(0, $current_fp - $fp_req));
            }
            if ($bp_req > 0) {
                $current_bp = intval(get_option('lfs_current_bp', 0));
                update_option('lfs_current_bp', max(0, $current_bp - $bp_req));
            }
            if ($sp_req > 0) {
                $current_sp = intval(get_option('lfs_current_sp', 0));
                update_option('lfs_current_sp', max(0, $current_sp - $sp_req));
            }
        }
        
        // Dra av från belöningskontot om det kostar pengar
        if ($cost > 0) {
            $current_balance = floatval(get_option('lfs_reward_account_balance', 0));
            update_option('lfs_reward_account_balance', max(0, $current_balance - $cost));
        }
        
        // Uppdatera belöningens status (kanoniskt)
        update_post_meta($reward_id, 'lfs_reward_status', 'redeemed');
        update_post_meta($reward_id, 'lfs_reward_redeemed_date', current_time('timestamp'));
        
        // Om det är en recurring reward, logga inlösningen separat
        if (get_post_meta($reward_id, 'lfs_reward_recurring', true) === 'yes') {
            $this->log_recurring_redemption($reward_id);
        }
        
        return true;
    }
    
    /**
     * Logga recurring reward-inlösning
     */
    private function log_recurring_redemption($reward_id) {
        $history = get_option('lfs_recurring_redemption_history', array());
        
        $history[] = array(
            'reward_id' => $reward_id,
            'reward_title' => get_the_title($reward_id),
            'date' => time(),
            'cost' => floatval(get_post_meta($reward_id, 'lfs_reward_cost', true)),
        );
        
        update_option('lfs_recurring_redemption_history', $history);
    }
    
    /**
     * Återställ dagliga recurring rewards (körs via cron)
     */
    public function reset_daily_rewards() {
    $rewards = get_posts(array(
        'post_type'      => 'lfs_reward',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'meta_key'       => 'lfs_reward_daily',
        'meta_value'     => 'yes',
    ));
    if (empty($rewards)) { return; }
    foreach ($rewards as $reward) {
    $status  = $this->normalize_status(get_post_meta($reward->ID, 'lfs_reward_status', true));
    if ($status !== 'redeemed') { continue; }
    $earned  = $this->get_points_earned_today(); // total for today
    $needed  = $this->get_required_points_total($reward->ID);
    if ($needed <= 0 || $earned['total'] >= $needed) {
        $this->set_status_available($reward->ID);
    }
}
    }
    
    /**
     * Get rewards grouped by status
     */
    public function get_rewards_grouped() {
        $calculations = LFS_Calculations::get_instance();
        
        $args = array(
            'post_type' => 'lfs_reward',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'meta_value_num',
            'meta_key' => 'lfs_reward_cost',
            'order' => 'ASC',
        );
        
        $all_rewards = get_posts($args);
        
        $grouped = array(
            'available' => array(),
            'pending' => array(),
            'redeemed' => array(),
        );
        
        foreach ($all_rewards as $reward) {
            $can_afford = $calculations->can_afford_reward($reward->ID);
            $status = get_post_meta($reward->ID, 'lfs_reward_status', true);
            $is_recurring = get_post_meta($reward->ID, 'lfs_reward_recurring', true) === 'yes';
            
            $reward_data = array(
                'id' => $reward->ID,
                'title' => $reward->post_title,
                'content' => $reward->post_content,
                'cost' => floatval(get_post_meta($reward->ID, 'lfs_reward_cost', true)),
                'type' => get_post_meta($reward->ID, 'lfs_reward_type', true),
                'fp_required' => intval(get_post_meta($reward->ID, 'lfs_reward_fp_required', true)),
                'bp_required' => intval(get_post_meta($reward->ID, 'lfs_reward_bp_required', true)),
                'sp_required' => intval(get_post_meta($reward->ID, 'lfs_reward_sp_required', true)),
                'total_required' => intval(get_post_meta($reward->ID, 'lfs_reward_total_required', true)),
                'status' => $status,
                'can_afford' => $can_afford,
                'is_recurring' => $is_recurring,
                'recurring_frequency' => get_post_meta($reward->ID, 'lfs_reward_recurring_frequency', true),
                'thumbnail' => get_the_post_thumbnail_url($reward->ID, 'medium'),
                'level' => wp_get_post_terms($reward->ID, 'reward_level', array('fields' => 'names')),
            );
            
            // Gruppera baserat på om användaren har råd och status
            if ($status === 'redeemed' && !$is_recurring) {
                $grouped['redeemed'][] = $reward_data;
            } elseif ($can_afford) {
                $grouped['available'][] = $reward_data;
            } else {
                $grouped['pending'][] = $reward_data;
            }
        }
        
        return $grouped;
    }
    
    /**
     * Get rewards by level (för filter)
     */
    public function get_rewards_by_level($level = null) {
        $args = array(
            'post_type' => 'lfs_reward',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'meta_value_num',
            'meta_key' => 'lfs_reward_cost',
            'order' => 'ASC',
        );
        
        if ($level) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'reward_level',
                    'field' => 'slug',
                    'terms' => $level,
                ),
            );
        }
        
        $rewards = get_posts($args);
        $calculations = LFS_Calculations::get_instance();
        $result = array();
        
        foreach ($rewards as $reward) {
            $can_afford = $calculations->can_afford_reward($reward->ID);
            $status = get_post_meta($reward->ID, 'lfs_reward_status', true);
            $is_recurring = get_post_meta($reward->ID, 'lfs_reward_recurring', true) === 'yes';
            
            $result[] = array(
                'id' => $reward->ID,
                'title' => $reward->post_title,
                'content' => $reward->post_content,
                'cost' => floatval(get_post_meta($reward->ID, 'lfs_reward_cost', true)),
                'type' => get_post_meta($reward->ID, 'lfs_reward_type', true),
                'fp_required' => intval(get_post_meta($reward->ID, 'lfs_reward_fp_required', true)),
                'bp_required' => intval(get_post_meta($reward->ID, 'lfs_reward_bp_required', true)),
                'sp_required' => intval(get_post_meta($reward->ID, 'lfs_reward_sp_required', true)),
                'total_required' => intval(get_post_meta($reward->ID, 'lfs_reward_total_required', true)),
                'status' => $status,
                'can_afford' => $can_afford,
                'is_recurring' => $is_recurring,
                'recurring_frequency' => get_post_meta($reward->ID, 'lfs_reward_recurring_frequency', true),
                'thumbnail' => get_the_post_thumbnail_url($reward->ID, 'medium'),
            );
        }
        
        return $result;
    }
    
    /**
     * Get redeemed rewards history
     */
    public function get_redeemed_rewards($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        // Hämta både permanenta inlösta och recurring history
        $permanent_args = array(
            'post_type' => 'lfs_reward',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'author' => $user_id,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'lfs_reward_status',
                    'value' => 'redeemed',
                    'compare' => '=',
                ),
                array(
                    'key' => 'lfs_reward_recurring',
                    'value' => 'yes',
                    'compare' => '!=',
                ),
            ),
            'orderby' => 'meta_value_num',
            'meta_key' => 'lfs_reward_redeemed_date',
            'order' => 'DESC',
        );
        
        $permanent_rewards = get_posts($permanent_args);
        $result = array();
        $total_spent = 0;
        
        // Lägg till permanenta belöningar
        foreach ($permanent_rewards as $reward) {
            $cost = floatval(get_post_meta($reward->ID, 'lfs_reward_cost', true));
            $total_spent += $cost;
            
            $result[] = array(
                'id' => $reward->ID,
                'title' => $reward->post_title,
                'cost' => $cost,
                'is_recurring' => false,
                'redeemed_date' => intval(get_post_meta($reward->ID, 'lfs_reward_redeemed_date', true)),
                'redeemed_date_formatted' => date('Y-m-d H:i', intval(get_post_meta($reward->ID, 'lfs_reward_redeemed_date', true))),
            );
        }
        
        // Lägg till recurring history
        $recurring_history = get_option('lfs_recurring_redemption_history', array());
        foreach ($recurring_history as $entry) {
            $total_spent += $entry['cost'];
            $result[] = array(
                'id' => $entry['reward_id'],
                'title' => $entry['reward_title'],
                'cost' => $entry['cost'],
                'is_recurring' => true,
                'redeemed_date' => $entry['date'],
                'redeemed_date_formatted' => date('Y-m-d H:i', $entry['date']),
            );
        }
        
        // Sortera efter datum
        usort($result, function($a, $b) {
            return $b['redeemed_date'] - $a['redeemed_date'];
        });
        
        return array(
            'rewards' => $result,
            'total_spent' => $total_spent,
        );
    }
    
    /**
     * AJAX: Redeem reward
     */
    public function ajax_redeem_reward() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $reward_id = isset($_POST['reward_id']) ? intval($_POST['reward_id']) : 0;
        
        if (!$reward_id) {
            wp_send_json_error(__('Ogiltigt belönings-ID', 'life-freedom-system'));
        }
        
        $result = $this->redeem_reward($reward_id);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        // Hämta uppdaterade data
        $calculations = LFS_Calculations::get_instance();
        
        wp_send_json_success(array(
            'message' => __('Belöning inlöst!', 'life-freedom-system'),
            'current_points' => $calculations->get_current_points(),
            'reward_balance' => $calculations->get_reward_account_balance(),
        ));
    }
    
    /**
     * AJAX: Get rewards by level
     */
    public function ajax_get_rewards_by_level() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $level = isset($_POST['level']) ? sanitize_text_field($_POST['level']) : null;
        
        $rewards = $this->get_rewards_by_level($level);
        wp_send_json_success($rewards);
    }


public function reset_recurring_rewards() {
    $rewards = get_posts(array(
        'post_type'      => 'lfs_reward',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'meta_query'     => array(
            'relation' => 'AND',
            array('key' => 'lfs_reward_recurring', 'value' => 'yes', 'compare' => '='),
            array('key' => 'lfs_reward_status',    'value' => 'redeemed', 'compare' => '='),
        ),
    ));
    if (empty($rewards)) { return; }

    $now = current_time('timestamp');
    foreach ($rewards as $reward) {
        $freq    = get_post_meta($reward->ID, 'lfs_reward_recurring_frequency', true);
        $last_ts = (int) get_post_meta($reward->ID, 'lfs_reward_redeemed_date', true);
        if (!$last_ts) { $last_ts = $now; }

        $should_reset = false;
        switch ($freq) {
            case 'daily':
                $should_reset = (date('Y-m-d', $now) !== date('Y-m-d', $last_ts));
                break;
            case 'weekly':
                $should_reset = ($now - $last_ts) >= DAY_IN_SECONDS * 7;
                break;
            case 'monthly':
                $should_reset = ($now - $last_ts) >= DAY_IN_SECONDS * 30;
                break;
            case 'custom':
                $days = max(1, (int) get_post_meta($reward->ID, 'lfs_reward_recurring_interval_days', true));
                $should_reset = ($now - $last_ts) >= DAY_IN_SECONDS * $days;
                break;
        }

        if ($should_reset) {
    $earned = $this->get_points_earned_today();
    $needed = $this->get_required_points_total($reward->ID);
    if ($needed <= 0 || $earned['total'] >= $needed) {
        $this->set_status_available($reward->ID);
    }
}
    }
}


/**
 * Get required points for a reward: prefer total_required, otherwise sum fp/bp/sp requirements.
 */
private function get_required_points_total($reward_id) {
    $total_req = intval(get_post_meta($reward_id, 'lfs_reward_total_required', true));
    if ($total_req > 0) return $total_req;
    $fp_req = intval(get_post_meta($reward_id, 'lfs_reward_fp_required', true));
    $bp_req = intval(get_post_meta($reward_id, 'lfs_reward_bp_required', true));
    $sp_req = intval(get_post_meta($reward_id, 'lfs_reward_sp_required', true));
    return max(0, $fp_req + $bp_req + $sp_req);
}
}