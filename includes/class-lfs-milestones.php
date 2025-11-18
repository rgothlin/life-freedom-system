<?php
/**
 * Milestone Helper Class
 * 
 * Handles milestone-related calculations and queries
 */

if (!defined('ABSPATH')) {
    exit;
}

class LFS_Milestones {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Hooks
    }
    
    /**
     * Get all milestones for a project
     * 
     * @param int $project_id Project post ID
     * @return array Array of milestone post objects
     */
    public function get_project_milestones($project_id) {
        $args = array(
            'post_type' => 'lfs_milestone',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'lfs_milestone_project',
                    'value' => $project_id,
                ),
            ),
            'orderby' => 'meta_value',
            'meta_key' => 'lfs_milestone_deadline',
            'order' => 'ASC',
        );
        
        return get_posts($args);
    }
    
    /**
     * Calculate milestone progress based on FP goal
     * 
     * @param int $milestone_id Milestone post ID
     * @return array Progress data
     */
    public function calculate_milestone_progress($milestone_id) {
        $fp_goal = (int) get_post_meta($milestone_id, 'lfs_milestone_fp_required', true);
        
        if ($fp_goal === 0) {
            return array(
                'fp_goal' => 0,
                'fp_earned' => 0,
                'percent' => 0,
                'status' => 'no_goal',
            );
        }
        
        // Get all activities linked to this milestone
        $args = array(
            'post_type' => 'life_activity',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'lfs_related_milestone',
                    'value' => $milestone_id,
                ),
            ),
        );
        
        $activities = get_posts($args);
        
        $total_fp = 0;
        foreach ($activities as $activity) {
            $fp = (int) get_post_meta($activity->ID, 'lfs_fp', true);
            $total_fp += $fp;
        }
        
        $percent = ($total_fp / $fp_goal) * 100;
        $percent = min($percent, 100); // Cap at 100%
        
        return array(
            'fp_goal' => $fp_goal,
            'fp_earned' => $total_fp,
            'percent' => round($percent, 1),
            'status' => $percent >= 100 ? 'achieved' : 'in_progress',
            'activities_count' => count($activities),
        );
    }
    
    /**
     * Check if a milestone is completed
     * 
     * @param int $milestone_id Milestone post ID
     * @return bool
     */
    public function is_milestone_completed($milestone_id) {
        $status = get_post_meta($milestone_id, 'lfs_milestone_status', true);
        return $status === 'completed';
    }
    
    /**
     * Mark milestone as completed
     * 
     * @param int $milestone_id Milestone post ID
     * @return bool Success
     */
    public function complete_milestone($milestone_id) {
        update_post_meta($milestone_id, 'lfs_milestone_status', 'completed');
        update_post_meta($milestone_id, 'lfs_milestone_achieved_date', current_time('Y-m-d'));
        
        // Fire action for notifications, etc
        do_action('lfs_milestone_completed', $milestone_id);
        
        return true;
    }
    
    /**
     * Get upcoming milestones across all projects
     * 
     * @param int $limit Number of milestones to return
     * @return array
     */
    public function get_upcoming_milestones($limit = 3) {
        $args = array(
            'post_type' => 'lfs_milestone',
            'posts_per_page' => $limit,
            'meta_query' => array(
                array(
                    'key' => 'lfs_milestone_status',
                    'value' => array('upcoming', 'active'),
                    'compare' => 'IN',
                ),
            ),
            'orderby' => 'meta_value',
            'meta_key' => 'lfs_milestone_deadline',
            'order' => 'ASC',
        );
        
        $milestones = get_posts($args);
        
        $results = array();
        foreach ($milestones as $milestone) {
            $progress = $this->calculate_milestone_progress($milestone->ID);
            $project_id = get_post_meta($milestone->ID, 'lfs_milestone_project', true);
            $project = get_post($project_id);
            
            $results[] = array(
                'milestone' => $milestone,
                'progress' => $progress,
                'project' => $project,
            );
        }
        
        return $results;
    }
}