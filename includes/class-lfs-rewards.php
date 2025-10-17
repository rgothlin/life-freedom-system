<?php
/**
 * Rewards Class
 * 
 * Handles reward redemption and management
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
    }
    
    /**
     * Redeem a reward
     */
    public function redeem_reward($reward_id, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        $calculations = LFS_Calculations::get_instance();
        
        // Check if user can afford
        if (!$calculations->can_afford_reward($reward_id)) {
            return new WP_Error('insufficient_points', __('Du har inte tillräckligt med poäng', 'life-freedom-system'));
        }
        
        // Check economic feasibility
        $cost = floatval(get_post_meta($reward_id, 'lfs_reward_cost', true));
        $balance = $calculations->get_reward_account_balance();
        
        if ($cost > $balance) {
            return new WP_Error('insufficient_balance', __('Inte tillräckligt på belöningskontot', 'life-freedom-system'));
        }
        
        // Update reward status
        update_post_meta($reward_id, 'lfs_reward_status', 'redeemed');
        update_post_meta($reward_id, 'lfs_reward_redeemed_date', current_time('timestamp'));
        
        // Deduct points if needed (optional - depends on if you want to "spend" points or just accumulate)
        // For now, points accumulate and rewards are just unlocked
        
        return true;
    }
    
    /**
     * Get rewards by level
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
        
        $args = array(
            'post_type' => 'lfs_reward',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'author' => $user_id,
            'meta_query' => array(
                array(
                    'key' => 'lfs_reward_status',
                    'value' => 'redeemed',
                    'compare' => '=',
                ),
            ),
            'orderby' => 'meta_value_num',
            'meta_key' => 'lfs_reward_redeemed_date',
            'order' => 'DESC',
        );
        
        $rewards = get_posts($args);
        $result = array();
        $total_spent = 0;
        
        foreach ($rewards as $reward) {
            $cost = floatval(get_post_meta($reward->ID, 'lfs_reward_cost', true));
            $total_spent += $cost;
            
            $result[] = array(
                'id' => $reward->ID,
                'title' => $reward->post_title,
                'cost' => $cost,
                'redeemed_date' => intval(get_post_meta($reward->ID, 'lfs_reward_redeemed_date', true)),
                'redeemed_date_formatted' => date('Y-m-d H:i', intval(get_post_meta($reward->ID, 'lfs_reward_redeemed_date', true))),
            );
        }
        
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
        
        wp_send_json_success(array(
            'message' => __('Belöning inlöst!', 'life-freedom-system'),
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
}