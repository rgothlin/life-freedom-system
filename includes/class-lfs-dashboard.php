<?php
/**
 * Dashboard Class - Förbättrad version med realtidsuppdatering
 * 
 * File location: admin/class-lfs-dashboard.php
 * Handles dashboard functionality and AJAX requests
 */

if (!defined('ABSPATH')) {
    exit;
}

class LFS_Dashboard {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // AJAX hooks
        add_action('wp_ajax_lfs_quick_add_activity', array($this, 'ajax_quick_add_activity'));
        add_action('wp_ajax_lfs_quick_log_template', array($this, 'ajax_quick_log_template'));
        add_action('wp_ajax_lfs_get_dashboard_data', array($this, 'ajax_get_dashboard_data'));
    }
    
    /**
     * Get dashboard overview data
     */
    public function get_dashboard_data() {
        $calculations = LFS_Calculations::get_instance();
        
        $data = array(
            'current_points' => $calculations->get_current_points(),
            'weekly_points' => $calculations->get_weekly_points(),
            'weekly_goals' => array(
                'fp' => intval(get_option('lfs_weekly_fp_goal', 500)),
                'bp' => intval(get_option('lfs_weekly_bp_goal', 300)),
                'sp' => intval(get_option('lfs_weekly_sp_goal', 400)),
            ),
            'reward_balance' => $calculations->get_reward_account_balance(),
            'streak' => intval(get_option('lfs_streak_days', 0)),
            'phase' => get_option('lfs_current_phase', 'survival'),
            'recent_activities' => $this->get_recent_activities(7),
            'available_rewards' => $this->get_available_rewards(),
            'next_milestone' => $this->get_next_milestone(),
        );
        
        return $data;
    }
    
    /**
     * Get recent activities
     */
    private function get_recent_activities($limit = 7) {
        $args = array(
            'post_type' => 'life_activity',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'orderby' => 'meta_value_num',
            'meta_key' => 'lfs_activity_datetime',
            'order' => 'DESC',
        );
        
        $activities = get_posts($args);
        $result = array();
        
        foreach ($activities as $activity) {
            $result[] = array(
                'id' => $activity->ID,
                'title' => $activity->post_title,
                'fp' => intval(get_post_meta($activity->ID, 'lfs_fp', true)),
                'bp' => intval(get_post_meta($activity->ID, 'lfs_bp', true)),
                'sp' => intval(get_post_meta($activity->ID, 'lfs_sp', true)),
                'datetime' => intval(get_post_meta($activity->ID, 'lfs_activity_datetime', true)),
                'date_formatted' => date('Y-m-d H:i', intval(get_post_meta($activity->ID, 'lfs_activity_datetime', true))),
            );
        }
        
        return $result;
    }
    
    /**
     * Get available rewards
     */
    private function get_available_rewards() {
        $calculations = LFS_Calculations::get_instance();
        $current_points = $calculations->get_current_points();
        
        $args = array(
            'post_type' => 'lfs_reward',
            'post_status' => 'publish',
            'posts_per_page' => 5,
            'meta_query' => array(
                array(
                    'key' => 'lfs_reward_status',
                    'value' => 'available',
                    'compare' => '=',
                ),
            ),
        );
        
        $rewards = get_posts($args);
        $result = array();
        
        foreach ($rewards as $reward) {
            $fp_req = intval(get_post_meta($reward->ID, 'lfs_reward_fp_required', true));
            $bp_req = intval(get_post_meta($reward->ID, 'lfs_reward_bp_required', true));
            $sp_req = intval(get_post_meta($reward->ID, 'lfs_reward_sp_required', true));
            $total_req = intval(get_post_meta($reward->ID, 'lfs_reward_total_required', true));
            
            $can_afford = $calculations->can_afford_reward($reward->ID);
            
            $result[] = array(
                'id' => $reward->ID,
                'title' => $reward->post_title,
                'cost' => floatval(get_post_meta($reward->ID, 'lfs_reward_cost', true)),
                'type' => get_post_meta($reward->ID, 'lfs_reward_type', true),
                'fp_required' => $fp_req,
                'bp_required' => $bp_req,
                'sp_required' => $sp_req,
                'total_required' => $total_req,
                'can_afford' => $can_afford,
            );
        }
        
        return $result;
    }
    
    /**
     * Get next milestone
     */
    private function get_next_milestone() {
        $calculations = LFS_Calculations::get_instance();
        $current_points = $calculations->get_current_points();
        
        $args = array(
            'post_type' => 'lfs_milestone',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => 'lfs_milestone_status',
                    'value' => 'active',
                    'compare' => '=',
                ),
            ),
        );
        
        $milestones = get_posts($args);
        
        if (empty($milestones)) {
            return null;
        }
        
        $milestone = $milestones[0];
        $fp_req = intval(get_post_meta($milestone->ID, 'lfs_milestone_fp_required', true));
        $bp_req = intval(get_post_meta($milestone->ID, 'lfs_milestone_bp_required', true));
        $sp_req = intval(get_post_meta($milestone->ID, 'lfs_milestone_sp_required', true));
        
        return array(
            'id' => $milestone->ID,
            'title' => $milestone->post_title,
            'type' => get_post_meta($milestone->ID, 'lfs_milestone_type', true),
            'fp_required' => $fp_req,
            'bp_required' => $bp_req,
            'sp_required' => $sp_req,
            'fp_progress' => $fp_req > 0 ? round(($current_points['fp'] / $fp_req) * 100) : 0,
            'bp_progress' => $bp_req > 0 ? round(($current_points['bp'] / $bp_req) * 100) : 0,
            'sp_progress' => $sp_req > 0 ? round(($current_points['sp'] / $sp_req) * 100) : 0,
            'reward' => floatval(get_post_meta($milestone->ID, 'lfs_milestone_reward', true)),
        );
    }
    
    /**
     * Get activity templates for quick add
     */
    public function get_activity_templates() {
        return array(
            array(
                'name' => __('Deep Work (Eget projekt)', 'life-freedom-system'),
                'fp' => 70,
                'bp' => 0,
                'sp' => 0,
                'category' => 'Arbete',
                'type' => 'Deep Work',
                'context' => 'Eget projekt',
            ),
            array(
                'name' => __('Skapat innehåll', 'life-freedom-system'),
                'fp' => 80,
                'bp' => 0,
                'sp' => 0,
                'category' => 'Arbete',
                'type' => 'Innehåll',
                'context' => 'Eget projekt',
            ),
            array(
                'name' => __('Kontaktat potentiell kund', 'life-freedom-system'),
                'fp' => 60,
                'bp' => 0,
                'sp' => 0,
                'category' => 'Arbete',
                'type' => 'Kontakt',
                'context' => 'Eget projekt',
            ),
            array(
                'name' => __('Träning', 'life-freedom-system'),
                'fp' => 0,
                'bp' => 35,
                'sp' => 0,
                'category' => 'Träning',
                'type' => 'Träning',
                'context' => 'Fritid',
            ),
            array(
                'name' => __('Kvalitetstid med Camilla', 'life-freedom-system'),
                'fp' => 0,
                'bp' => 30,
                'sp' => 0,
                'category' => 'Fritid',
                'type' => 'Relation',
                'context' => 'Hemma',
            ),
            array(
                'name' => __('Hemmauppgifter', 'life-freedom-system'),
                'fp' => 0,
                'bp' => 25,
                'sp' => 0,
                'category' => 'Hemma',
                'type' => 'Hemma',
                'context' => 'Hemma',
            ),
            array(
                'name' => __('Deep Work (Heltidsjobb)', 'life-freedom-system'),
                'fp' => 0,
                'bp' => 0,
                'sp' => 30,
                'category' => 'Arbete',
                'type' => 'Deep Work',
                'context' => 'Heltidsjobb',
            ),
            array(
                'name' => __('Arbetat hemifrån', 'life-freedom-system'),
                'fp' => 0,
                'bp' => 0,
                'sp' => 40,
                'category' => 'Arbete',
                'type' => 'Hemarbete',
                'context' => 'Heltidsjobb',
            ),
            array(
                'name' => __('Pauser tagna', 'life-freedom-system'),
                'fp' => 0,
                'bp' => 20,
                'sp' => 0,
                'category' => 'Arbete',
                'type' => 'Paus',
                'context' => 'Heltidsjobb',
            ),
        );
    }
    
    /**
     * Get formatted chart data for JavaScript
     */
    private function get_formatted_chart_data($days = 7) {
        $calculations = LFS_Calculations::get_instance();
        $chart_data = $calculations->get_daily_points_for_chart($days);
        
        $formatted = array(
            'labels' => array(),
            'fp' => array(),
            'bp' => array(),
            'sp' => array()
        );
        
        foreach ($chart_data as $day) {
            $formatted['labels'][] = $day['label'];
            $formatted['fp'][] = $day['fp'];
            $formatted['bp'][] = $day['bp'];
            $formatted['sp'][] = $day['sp'];
        }
        
        return $formatted;
    }
    
    /**
     * AJAX: Quick log från template (NY METOD)
     */
    public function ajax_quick_log_template() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $template_id = isset($_POST['template_id']) ? intval($_POST['template_id']) : 0;
        
        if (!$template_id) {
            wp_send_json_error(__('Ogiltigt mall-ID', 'life-freedom-system'));
        }
        
        // Hämta template data
        $template = get_post($template_id);
        
        if (!$template || $template->post_type !== 'lfs_activity_tpl') {
            wp_send_json_error(__('Mall hittades inte', 'life-freedom-system'));
        }
        
        // Hämta template metadata
        $fp = intval(get_post_meta($template_id, '_lfs_fp', true));
        $bp = intval(get_post_meta($template_id, '_lfs_bp', true));
        $sp = intval(get_post_meta($template_id, '_lfs_sp', true));
        $category = get_post_meta($template_id, '_lfs_category', true);
        $type = get_post_meta($template_id, '_lfs_type', true);
        $context = get_post_meta($template_id, '_lfs_context', true);
        
        // Skapa aktivitet från template
        $post_id = wp_insert_post(array(
            'post_type' => 'life_activity',
            'post_title' => $template->post_title,
            'post_status' => 'publish',
        ));
        
        if (is_wp_error($post_id)) {
            wp_send_json_error($post_id->get_error_message());
        }
        
        // Spara metadata
        update_post_meta($post_id, 'lfs_fp', $fp);
        update_post_meta($post_id, 'lfs_bp', $bp);
        update_post_meta($post_id, 'lfs_sp', $sp);
        update_post_meta($post_id, 'lfs_activity_datetime', current_time('timestamp'));
        update_post_meta($post_id, 'lfs_template_id', $template_id);
        
        // Lägg till taxonomies
        if (!empty($category)) {
            wp_set_object_terms($post_id, $category, 'activity_category');
        }
        if (!empty($type)) {
            wp_set_object_terms($post_id, $type, 'activity_type');
        }
        if (!empty($context)) {
            wp_set_object_terms($post_id, $context, 'work_context');
        }
        
        // Hämta uppdaterad data för UI
        $calculations = LFS_Calculations::get_instance();
        
        wp_send_json_success(array(
            'id' => $post_id,
            'message' => __('Aktivitet loggad!', 'life-freedom-system'),
            'points' => $calculations->get_current_points(),
            'weekly_points' => $calculations->get_weekly_points(),
            'weekly_goals' => array(
                'fp' => intval(get_option('lfs_weekly_fp_goal', 500)),
                'bp' => intval(get_option('lfs_weekly_bp_goal', 300)),
                'sp' => intval(get_option('lfs_weekly_sp_goal', 400)),
            ),
            'chart_data' => $this->get_formatted_chart_data(7),
            'points_added' => array(
                'fp' => $fp,
                'bp' => $bp,
                'sp' => $sp
            )
        ));
    }
    
    /**
     * AJAX: Quick add activity (FÖRBÄTTRAD)
     */
    public function ajax_quick_add_activity() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
        $fp = isset($_POST['fp']) ? intval($_POST['fp']) : 0;
        $bp = isset($_POST['bp']) ? intval($_POST['bp']) : 0;
        $sp = isset($_POST['sp']) ? intval($_POST['sp']) : 0;
        $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
        $context = isset($_POST['context']) ? sanitize_text_field($_POST['context']) : '';
        
        if (empty($title)) {
            wp_send_json_error(__('Titel krävs', 'life-freedom-system'));
        }
        
        // Validera poäng
        if ($fp < 0 || $fp > 100) {
            wp_send_json_error(__('FP måste vara mellan 0 och 100', 'life-freedom-system'));
        }
        if ($bp < 0 || $bp > 50) {
            wp_send_json_error(__('BP måste vara mellan 0 och 50', 'life-freedom-system'));
        }
        if ($sp < 0 || $sp > 100) {
            wp_send_json_error(__('SP måste vara mellan 0 och 100', 'life-freedom-system'));
        }
        
        // Skapa aktivitet
        $post_id = wp_insert_post(array(
            'post_type' => 'life_activity',
            'post_title' => $title,
            'post_status' => 'publish',
        ));
        
        if (is_wp_error($post_id)) {
            wp_send_json_error($post_id->get_error_message());
        }
        
        // Spara metadata
        update_post_meta($post_id, 'lfs_fp', $fp);
        update_post_meta($post_id, 'lfs_bp', $bp);
        update_post_meta($post_id, 'lfs_sp', $sp);
        update_post_meta($post_id, 'lfs_activity_datetime', current_time('timestamp'));
        
        // Lägg till taxonomies
        if (!empty($category)) {
            wp_set_object_terms($post_id, $category, 'activity_category');
        }
        if (!empty($type)) {
            wp_set_object_terms($post_id, $type, 'activity_type');
        }
        if (!empty($context)) {
            wp_set_object_terms($post_id, $context, 'work_context');
        }
        
        // Hämta uppdaterad data för UI
        $calculations = LFS_Calculations::get_instance();
        
        wp_send_json_success(array(
            'id' => $post_id,
            'message' => sprintf(__('Aktivitet "%s" tillagd!', 'life-freedom-system'), $title),
            'points' => $calculations->get_current_points(),
            'weekly_points' => $calculations->get_weekly_points(),
            'weekly_goals' => array(
                'fp' => intval(get_option('lfs_weekly_fp_goal', 500)),
                'bp' => intval(get_option('lfs_weekly_bp_goal', 300)),
                'sp' => intval(get_option('lfs_weekly_sp_goal', 400)),
            ),
            'chart_data' => $this->get_formatted_chart_data(7),
            'points_added' => array(
                'fp' => $fp,
                'bp' => $bp,
                'sp' => $sp
            )
        ));
    }
    
    /**
     * AJAX: Get dashboard data
     */
    public function ajax_get_dashboard_data() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $data = $this->get_dashboard_data();
        $data['chart_data'] = $this->get_formatted_chart_data(7);
        
        wp_send_json_success($data);
    }
}