<?php
/**
 * Activity Templates Class
 * 
 * File location: includes/class-lfs-activity-templates.php
 * 
 * Handles creation and management of reusable activity templates
 */

if (!defined('ABSPATH')) {
    exit;
}

class LFS_Activity_Templates {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Register custom post type
        add_action('init', array($this, 'register_post_type'));
        
        // AJAX hooks
        add_action('wp_ajax_lfs_get_templates', array($this, 'ajax_get_templates'));
        add_action('wp_ajax_lfs_create_from_template', array($this, 'ajax_create_from_template'));
        add_action('wp_ajax_lfs_quick_log_template', array($this, 'ajax_quick_log_template'));
    }
    
    /**
     * Register Activity Template CPT
     */
    public function register_post_type() {
        register_post_type('lfs_activity_tpl', array(
            'labels' => array(
                'name' => __('Aktivitetsmallar', 'life-freedom-system'),
                'singular_name' => __('Aktivitetsmall', 'life-freedom-system'),
                'add_new' => __('Lägg till mall', 'life-freedom-system'),
                'add_new_item' => __('Lägg till ny mall', 'life-freedom-system'),
                'edit_item' => __('Redigera mall', 'life-freedom-system'),
                'new_item' => __('Ny mall', 'life-freedom-system'),
                'view_item' => __('Visa mall', 'life-freedom-system'),
                'search_items' => __('Sök mallar', 'life-freedom-system'),
                'not_found' => __('Inga mallar hittades', 'life-freedom-system'),
                'menu_name' => __('Aktivitetsmallar', 'life-freedom-system'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'life-freedom-system',
            'menu_icon' => 'dashicons-forms',
            'supports' => array('title'),
            'has_archive' => false,
            'rewrite' => false,
            'capability_type' => 'post',
            'show_in_rest' => false,
        ));
    }
    
    /**
     * Get all templates
     */
    public function get_templates($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        $args = array(
            'post_type' => 'lfs_activity_tpl',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'menu_order title',
            'order' => 'ASC',
            'author' => $user_id,
        );
        
        $templates = get_posts($args);
        $result = array();
        
        foreach ($templates as $template) {
            $result[] = array(
                'id' => $template->ID,
                'title' => $template->post_title,
                'fp' => intval(get_post_meta($template->ID, 'lfs_tpl_fp', true)),
                'bp' => intval(get_post_meta($template->ID, 'lfs_tpl_bp', true)),
                'sp' => intval(get_post_meta($template->ID, 'lfs_tpl_sp', true)),
                'category' => get_post_meta($template->ID, 'lfs_tpl_category', true),
                'type' => get_post_meta($template->ID, 'lfs_tpl_type', true),
                'context' => get_post_meta($template->ID, 'lfs_tpl_context', true),
                'time_spent' => intval(get_post_meta($template->ID, 'lfs_tpl_time_spent', true)),
                'color' => get_post_meta($template->ID, 'lfs_tpl_color', true),
                'icon' => get_post_meta($template->ID, 'lfs_tpl_icon', true),
            );
        }
        
        return $result;
    }
    
    /**
     * Get templates grouped by category
     */
    public function get_templates_grouped() {
        $templates = $this->get_templates();
        $grouped = array();
        
        foreach ($templates as $template) {
            $category = $template['category'] ?: 'Övrigt';
            
            if (!isset($grouped[$category])) {
                $grouped[$category] = array();
            }
            
            $grouped[$category][] = $template;
        }
        
        return $grouped;
    }
    
    /**
     * Create activity from template
     */
    public function create_from_template($template_id, $overrides = array()) {
        $template = get_post($template_id);
        
        if (!$template || $template->post_type !== 'lfs_activity_tpl') {
            return new WP_Error('invalid_template', __('Ogiltig mall', 'life-freedom-system'));
        }
        
        // Get template values
        $title = isset($overrides['title']) ? $overrides['title'] : $template->post_title;
        $fp = isset($overrides['fp']) ? intval($overrides['fp']) : intval(get_post_meta($template_id, 'lfs_tpl_fp', true));
        $bp = isset($overrides['bp']) ? intval($overrides['bp']) : intval(get_post_meta($template_id, 'lfs_tpl_bp', true));
        $sp = isset($overrides['sp']) ? intval($overrides['sp']) : intval(get_post_meta($template_id, 'lfs_tpl_sp', true));
        $category = isset($overrides['category']) ? $overrides['category'] : get_post_meta($template_id, 'lfs_tpl_category', true);
        $type = isset($overrides['type']) ? $overrides['type'] : get_post_meta($template_id, 'lfs_tpl_type', true);
        $context = isset($overrides['context']) ? $overrides['context'] : get_post_meta($template_id, 'lfs_tpl_context', true);
        $time_spent = isset($overrides['time_spent']) ? intval($overrides['time_spent']) : intval(get_post_meta($template_id, 'lfs_tpl_time_spent', true));
        $notes = isset($overrides['notes']) ? $overrides['notes'] : '';
        
        // Create activity
        $activity_id = wp_insert_post(array(
            'post_type' => 'life_activity',
            'post_title' => $title,
            'post_status' => 'publish',
            'post_content' => $notes,
        ));
        
        if (is_wp_error($activity_id)) {
            return $activity_id;
        }
        
        // Set meta
        update_post_meta($activity_id, 'lfs_fp', $fp);
        update_post_meta($activity_id, 'lfs_bp', $bp);
        update_post_meta($activity_id, 'lfs_sp', $sp);
        update_post_meta($activity_id, 'lfs_activity_datetime', current_time('timestamp'));
        
        if ($time_spent > 0) {
            update_post_meta($activity_id, 'lfs_time_spent', $time_spent);
        }
        
        // Set taxonomies
        if (!empty($category)) {
            $term = get_term_by('name', $category, 'activity_category');
            if ($term) {
                wp_set_object_terms($activity_id, $term->term_id, 'activity_category');
            }
        }
        
        if (!empty($type)) {
            $term = get_term_by('name', $type, 'activity_type');
            if ($term) {
                wp_set_object_terms($activity_id, $term->term_id, 'activity_type');
            }
        }
        
        if (!empty($context)) {
            $term = get_term_by('name', $context, 'work_context');
            if ($term) {
                wp_set_object_terms($activity_id, $term->term_id, 'work_context');
            }
        }
        
        // Link back to template
        update_post_meta($activity_id, 'lfs_created_from_template', $template_id);
        
        return $activity_id;
    }
    
    /**
     * Create default templates for new users
     */
    public function create_default_templates($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        $defaults = array(
            // FP Activities
            array(
                'title' => 'Deep Work (Eget projekt) 2h',
                'fp' => 70,
                'category' => 'Arbete',
                'type' => 'Deep Work',
                'context' => 'Eget projekt',
                'time_spent' => 120,
                'color' => '#3498db',
                'icon' => '🚀',
            ),
            array(
                'title' => 'Skapat innehåll (artikel/video)',
                'fp' => 80,
                'category' => 'Arbete',
                'type' => 'Innehåll',
                'context' => 'Eget projekt',
                'time_spent' => 90,
                'color' => '#3498db',
                'icon' => '✍️',
            ),
            array(
                'title' => 'Kontaktat potentiell kund',
                'fp' => 60,
                'category' => 'Arbete',
                'type' => 'Kontakt',
                'context' => 'Eget projekt',
                'time_spent' => 30,
                'color' => '#3498db',
                'icon' => '📞',
            ),
            array(
                'title' => 'Levererat kundarbete',
                'fp' => 100,
                'category' => 'Arbete',
                'type' => 'Leverans',
                'context' => 'Eget projekt',
                'color' => '#3498db',
                'icon' => '✅',
            ),
            
            // BP Activities
            array(
                'title' => 'Träning',
                'bp' => 35,
                'category' => 'Träning',
                'type' => 'Träning',
                'context' => 'Fritid',
                'time_spent' => 60,
                'color' => '#2ecc71',
                'icon' => '💪',
            ),
            array(
                'title' => 'Kvalitetstid med Camilla',
                'bp' => 30,
                'category' => 'Fritid',
                'type' => 'Relation',
                'context' => 'Hemma',
                'time_spent' => 120,
                'color' => '#2ecc71',
                'icon' => '❤️',
            ),
            array(
                'title' => 'Hemmauppgifter (städa/handla)',
                'bp' => 25,
                'category' => 'Hemma',
                'type' => 'Hemma',
                'context' => 'Hemma',
                'time_spent' => 60,
                'color' => '#2ecc71',
                'icon' => '🏠',
            ),
            array(
                'title' => 'Pauser tagna (3+ st)',
                'bp' => 20,
                'category' => 'Arbete',
                'type' => 'Paus',
                'context' => 'Heltidsjobb',
                'color' => '#2ecc71',
                'icon' => '☕',
            ),
            
            // SP Activities
            array(
                'title' => 'Deep Work (Heltidsjobb) 2h',
                'sp' => 30,
                'category' => 'Arbete',
                'type' => 'Deep Work',
                'context' => 'Heltidsjobb',
                'time_spent' => 120,
                'color' => '#f39c12',
                'icon' => '💼',
            ),
            array(
                'title' => 'Arbetat hemifrån',
                'sp' => 40,
                'category' => 'Arbete',
                'type' => 'Hemarbete',
                'context' => 'Heltidsjobb',
                'color' => '#f39c12',
                'icon' => '🏡',
            ),
            array(
                'title' => 'Snabba uppgifter',
                'sp' => 15,
                'category' => 'Arbete',
                'type' => 'Uppgift',
                'context' => 'Heltidsjobb',
                'time_spent' => 30,
                'color' => '#f39c12',
                'icon' => '⚡',
            ),
            array(
                'title' => 'Följt budget idag',
                'sp' => 10,
                'category' => 'Ekonomi',
                'type' => 'Budget',
                'context' => 'Hemma',
                'color' => '#f39c12',
                'icon' => '💰',
            ),
        );
        
        foreach ($defaults as $template_data) {
            $post_id = wp_insert_post(array(
                'post_type' => 'lfs_activity_tpl',
                'post_title' => $template_data['title'],
                'post_status' => 'publish',
                'post_author' => $user_id,
            ));
            
            if (!is_wp_error($post_id)) {
                update_post_meta($post_id, 'lfs_tpl_fp', isset($template_data['fp']) ? $template_data['fp'] : 0);
                update_post_meta($post_id, 'lfs_tpl_bp', isset($template_data['bp']) ? $template_data['bp'] : 0);
                update_post_meta($post_id, 'lfs_tpl_sp', isset($template_data['sp']) ? $template_data['sp'] : 0);
                update_post_meta($post_id, 'lfs_tpl_category', $template_data['category']);
                update_post_meta($post_id, 'lfs_tpl_type', $template_data['type']);
                update_post_meta($post_id, 'lfs_tpl_context', $template_data['context']);
                update_post_meta($post_id, 'lfs_tpl_time_spent', isset($template_data['time_spent']) ? $template_data['time_spent'] : 0);
                update_post_meta($post_id, 'lfs_tpl_color', isset($template_data['color']) ? $template_data['color'] : '');
                update_post_meta($post_id, 'lfs_tpl_icon', isset($template_data['icon']) ? $template_data['icon'] : '');
            }
        }
    }
    
    /**
     * AJAX: Get templates
     */
    public function ajax_get_templates() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $grouped = isset($_POST['grouped']) && $_POST['grouped'] === 'true';
        
        if ($grouped) {
            $templates = $this->get_templates_grouped();
        } else {
            $templates = $this->get_templates();
        }
        
        wp_send_json_success($templates);
    }
    
    /**
     * AJAX: Create from template
     */
    public function ajax_create_from_template() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $template_id = isset($_POST['template_id']) ? intval($_POST['template_id']) : 0;
        
        if (!$template_id) {
            wp_send_json_error(__('Mall-ID saknas', 'life-freedom-system'));
        }
        
        $overrides = array();
        
        if (isset($_POST['title'])) {
            $overrides['title'] = sanitize_text_field($_POST['title']);
        }
        if (isset($_POST['fp'])) {
            $overrides['fp'] = intval($_POST['fp']);
        }
        if (isset($_POST['bp'])) {
            $overrides['bp'] = intval($_POST['bp']);
        }
        if (isset($_POST['sp'])) {
            $overrides['sp'] = intval($_POST['sp']);
        }
        if (isset($_POST['notes'])) {
            $overrides['notes'] = sanitize_textarea_field($_POST['notes']);
        }
        if (isset($_POST['time_spent'])) {
            $overrides['time_spent'] = intval($_POST['time_spent']);
        }
        
        $result = $this->create_from_template($template_id, $overrides);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(array(
            'activity_id' => $result,
            'message' => __('Aktivitet skapad från mall!', 'life-freedom-system'),
        ));
    }
    
    /**
     * AJAX: Quick log from template (no overrides)
     */
    public function ajax_quick_log_template() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $template_id = isset($_POST['template_id']) ? intval($_POST['template_id']) : 0;
        
        if (!$template_id) {
            wp_send_json_error(__('Mall-ID saknas', 'life-freedom-system'));
        }
        
        $result = $this->create_from_template($template_id);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(array(
            'activity_id' => $result,
            'message' => __('Aktivitet loggad!', 'life-freedom-system'),
        ));
    }
}