<?php
/**
 * Dashboard Class - FIXAD baserat på faktisk struktur
 * 
 * File location: includes/class-lfs-dashboard.php
 * Handles dashboard functionality and AJAX requests
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', function () {
    $activity_post_type = 'life_activity';
    $template_post_type = 'lfs_activity_tpl'; // <-- justera om du använder annan slug

    $taxonomies = get_object_taxonomies($activity_post_type, 'names');
    foreach ($taxonomies as $tax) {
        register_taxonomy_for_object_type($tax, $template_post_type);
    }
}, 20);


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
 * ERSÄTT ajax_quick_log_template() i class-lfs-dashboard.php
 * METABOX-KOMPATIBEL VERSION
 */

public function ajax_quick_log_template() {
    check_ajax_referer('lfs_nonce', 'nonce');
    
    $template_id = isset($_POST['template_id']) ? intval($_POST['template_id']) : 0;
    
    if (!$template_id) {
        wp_send_json_error('Ogiltigt mall-ID');
    }
    
    // Hämta template
    $template = get_post($template_id);
    
    if (!$template) {
        wp_send_json_error('Mall hittades inte');
    }
    
    // === HELPER: Hämta metadata robust (hanterar både array och string) ===
    $get_meta_value = function($post_id, $key) {
    $tpl_key = (strpos($key, 'lfs_tpl_') === 0) ? $key : 'lfs_tpl_' . preg_replace('/^lfs_/', '', $key);
    $value = null;
    if (function_exists('rwmb_meta')) {
        $value = rwmb_meta($tpl_key, array(), $post_id);
        if (empty($value)) {
            $value = rwmb_meta($key, array(), $post_id);
        }
    }
    if (empty($value)) {
        $value = get_post_meta($post_id, $tpl_key, true);
        if (empty($value)) {
            $value = get_post_meta($post_id, $key, true);
        }
    }
    if (is_array($value) && !empty($value)) { $value = reset($value); }
    return $value;
};
    
    // === HÄMTA ALL METADATA FRÅN TEMPLATE ===
    
    $fp = intval($get_meta_value($template_id, 'lfs_tpl_fp'));
    $bp = intval($get_meta_value($template_id, 'lfs_tpl_bp'));
    $sp = intval($get_meta_value($template_id, 'lfs_tpl_sp'));
    
    $category = $get_meta_value($template_id, 'lfs_category');
    $type = $get_meta_value($template_id, 'lfs_type');
    $context = $get_meta_value($template_id, 'lfs_context');
    $duration = $get_meta_value($template_id, 'lfs_duration');
    $notes = $get_meta_value($template_id, 'lfs_notes');
    $energy_level = $get_meta_value($template_id, 'lfs_energy_level');
    $importance = $get_meta_value($template_id, 'lfs_importance');
    $project_id = $get_meta_value($template_id, 'lfs_related_project');
    
    // === DEBUG (ta bort efter testning) ===
    error_log('=== QUICK LOG TEMPLATE DEBUG ===');
    error_log('Template ID: ' . $template_id);
    error_log('FP: ' . $fp);
    error_log('BP: ' . $bp);
    error_log('SP: ' . $sp);
    error_log('Category: ' . $category);
    error_log('Type: ' . $type);
    error_log('Context: ' . $context);
    // === SLUT DEBUG ===
    
    // === SKAPA NY AKTIVITET ===
    
    $post_id = wp_insert_post(array(
        'post_type' => 'life_activity',
        'post_title' => $template->post_title,
        'post_content' => $template->post_content,
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
    ));
    
    if (is_wp_error($post_id)) {
        wp_send_json_error($post_id->get_error_message());
    }
    
    // === SPARA ALL METADATA TILL NYA AKTIVITETEN ===
    
    // Poäng (ALLTID spara, även om 0)
    update_post_meta($post_id, 'lfs_fp', $fp);
    update_post_meta($post_id, 'lfs_bp', $bp);
    update_post_meta($post_id, 'lfs_sp', $sp);
    update_post_meta($post_id, 'lfs_activity_datetime', current_time('timestamp'));
    update_post_meta($post_id, 'lfs_template_id', $template_id);
    
    // Övriga fält (spara även tomma för konsekvens)
    update_post_meta($post_id, 'lfs_category', $category);
    update_post_meta($post_id, 'lfs_type', $type);

    if (!empty($project_id)) { update_post_meta($post_id, 'lfs_related_project', $project_id); }
    if (!empty($milestone_id)) { update_post_meta($post_id, 'lfs_related_milestone', $milestone_id); }
if ($duration) {
        update_post_meta($post_id, 'lfs_duration', $duration);
    }
    if ($notes) {
        update_post_meta($post_id, 'lfs_notes', $notes);
    }
    if ($energy_level) {
        update_post_meta($post_id, 'lfs_energy_level', $energy_level);
    }
    if ($importance) {
        update_post_meta($post_id, 'lfs_importance', $importance);
    }
    if ($project_id) {
        update_post_meta($post_id, 'lfs_related_project', $project_id);
    }
    
    // === KOPIERA TAXONOMIER ===
    
    
    $taxonomies = get_object_taxonomies('life_activity', 'names');
    if (!empty($taxonomies)) {
        foreach ($taxonomies as $taxonomy) {
            $terms = wp_get_object_terms($template_id, $taxonomy, ['fields' => 'ids']);
            if (!is_wp_error($terms) && !empty($terms)) {
                wp_set_object_terms($post_id, $terms, $taxonomy, false);
            }
        }
    }

    
    // === KOPIERA ÄVEN TAXONOMIER FRÅN TEMPLATE POST TYPE ===
    
    // Om template har annan post type, kopiera därifrån också
    $template_taxonomies = get_object_taxonomies($template->post_type);
    if (!empty($template_taxonomies)) {
        foreach ($template_taxonomies as $taxonomy) {
            $terms = wp_get_object_terms($template_id, $taxonomy, array('fields' => 'ids'));
            if (!is_wp_error($terms) && !empty($terms)) {
                // Försök sätta på life_activity (kanske samma taxonomi används)
                wp_set_object_terms($post_id, $terms, $taxonomy);
            }
        }
    }
    
    // === UPPDATERA TOTALA POÄNG ===
    
    $current_fp = intval(get_option('lfs_current_fp', 0));
    $current_bp = intval(get_option('lfs_current_bp', 0));
    $current_sp = intval(get_option('lfs_current_sp', 0));
    
    update_option('lfs_current_fp', $current_fp + $fp);
    update_option('lfs_current_bp', $current_bp + $bp);
    update_option('lfs_current_sp', $current_sp + $sp);
    
    // Uppdatera veckovisa poäng
    $weekly_fp = intval(get_option('lfs_weekly_fp', 0));
    $weekly_bp = intval(get_option('lfs_weekly_bp', 0));
    $weekly_sp = intval(get_option('lfs_weekly_sp', 0));
    
    update_option('lfs_weekly_fp', $weekly_fp + $fp);
    update_option('lfs_weekly_bp', $weekly_bp + $bp);
    update_option('lfs_weekly_sp', $weekly_sp + $sp);
    
    // === DEBUG: Verifiera att metadata sparades ===
    error_log('=== SAVED TO NEW ACTIVITY ===');
    error_log('New Activity ID: ' . $post_id);
    error_log('Saved FP: ' . get_post_meta($post_id, 'lfs_fp', true));
    error_log('Saved BP: ' . get_post_meta($post_id, 'lfs_bp', true));
    error_log('Saved SP: ' . get_post_meta($post_id, 'lfs_sp', true));
    error_log('Saved Category: ' . get_post_meta($post_id, 'lfs_category', true));
    // === SLUT DEBUG ===
    
    // === RETURNERA SUCCESS ===
    
    wp_send_json_success(array(
        'id' => $post_id,
        'message' => 'Aktivitet loggad!',
        'points' => array(
            'fp' => $current_fp + $fp,
            'bp' => $current_bp + $bp,
            'sp' => $current_sp + $sp,
        ),
        'weekly_points' => array(
            'fp' => $weekly_fp + $fp,
            'bp' => $weekly_bp + $bp,
            'sp' => $weekly_sp + $sp,
        ),
        'points_added' => array(
            'fp' => $fp,
            'bp' => $bp,
            'sp' => $sp,
        ),
        'debug' => array(
            'template_id' => $template_id,
            'new_post_id' => $post_id,
            'fp' => $fp,
            'bp' => $bp,
            'sp' => $sp,
            'category' => $category,
            'type' => $type,
            'context' => $context,
        )
    ));
}
    
    /**
     * AJAX: Quick add activity
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
            'post_author' => get_current_user_id(),
        ));
        
        if (is_wp_error($post_id)) {
            wp_send_json_error($post_id->get_error_message());
        }
        
        // Spara metadata
        update_post_meta($post_id, 'lfs_fp', $fp);
        update_post_meta($post_id, 'lfs_bp', $bp);
        update_post_meta($post_id, 'lfs_sp', $sp);
        update_post_meta($post_id, 'lfs_activity_datetime', current_time('timestamp'));
        
        // Spara kategorier etc
        if ($category) {
            update_post_meta($post_id, 'lfs_category', $category);
        }
        if ($type) {
            update_post_meta($post_id, 'lfs_type', $type);
        }
        if ($context) {

            if (!empty($project_id)) { update_post_meta($post_id, 'lfs_related_project', $project_id); }
            if (!empty($milestone_id)) { update_post_meta($post_id, 'lfs_related_milestone', $milestone_id); }
        }
        
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
        
        // Lägg till poäng till systemet
        $calculations = LFS_Calculations::get_instance();
        $calculations->add_points($fp, $bp, $sp);
        
        // Hämta uppdaterad data för UI
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