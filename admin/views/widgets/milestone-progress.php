<?php
/**
 * Milestone Progress Widget
 */

if (!defined('ABSPATH')) {
    exit;
}

$milestones_helper = LFS_Milestones::get_instance();
$upcoming = $milestones_helper->get_upcoming_milestones(3);

if (empty($upcoming)) {
    return;
}
?>

<div class="lfs-widget lfs-milestone-progress">
    <h3>🎯 Kommande Milstolpar</h3>
    
    <?php foreach ($upcoming as $data): 
        $milestone = $data['milestone'];
        $progress = $data['progress'];
        $project = $data['project'];
        
        $deadline = get_post_meta($milestone->ID, 'lfs_milestone_deadline', true);
        $days_left = '';
        if ($deadline) {
            $deadline_ts = strtotime($deadline);
            $now = current_time('timestamp');
            $days = floor(($deadline_ts - $now) / DAY_IN_SECONDS);
            if ($days > 0) {
                $days_left = sprintf(_n('%d dag kvar', '%d dagar kvar', $days, 'life-freedom-system'), $days);
            } elseif ($days === 0) {
                $days_left = __('Deadline idag!', 'life-freedom-system');
            } else {
                $days_left = sprintf(__('%d dagar försenad', 'life-freedom-system'), abs($days));
            }
        }
        
        $progress_class = '';
        if ($progress['percent'] >= 80) {
            $progress_class = 'high';
        } elseif ($progress['percent'] >= 50) {
            $progress_class = 'medium';
        } else {
            $progress_class = 'low';
        }
    ?>
    
    <div class="milestone-card">
        <div class="milestone-header">
            <h4>
                <a href="<?php echo get_edit_post_link($milestone->ID); ?>">
                    <?php echo esc_html($milestone->post_title); ?>
                </a>
            </h4>
            <?php if ($project): ?>
                <span class="project-tag">
                    <?php echo esc_html($project->post_title); ?>
                </span>
            <?php endif; ?>
        </div>
        
        <div class="progress-bar-container">
            <div class="progress-bar <?php echo esc_attr($progress_class); ?>" 
                 style="width: <?php echo esc_attr($progress['percent']); ?>%">
                <span class="progress-text">
                    <?php echo esc_html($progress['percent']); ?>%
                </span>
            </div>
        </div>
        
        <div class="milestone-stats">
            <span class="stat">
                <strong><?php echo esc_html($progress['fp_earned']); ?></strong> / 
                <?php echo esc_html($progress['fp_goal']); ?> FP
            </span>
            <span class="stat">
                <?php echo esc_html($progress['activities_count']); ?> aktiviteter
            </span>
            <?php if ($days_left): ?>
                <span class="stat deadline">
                    📅 <?php echo esc_html($days_left); ?>
                </span>
            <?php endif; ?>
        </div>
        
        <?php
        // Show linked rewards
        $rewards = get_posts(array(
            'post_type' => 'lfs_reward',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'lfs_reward_milestone',
                    'value' => $milestone->ID,
                ),
            ),
        ));
        
        if (!empty($rewards)):
        ?>
        <div class="milestone-rewards">
            <span class="rewards-label">🎁 Belöningar:</span>
            <?php foreach ($rewards as $reward): ?>
                <span class="reward-tag">
                    <?php echo esc_html($reward->post_title); ?>
                </span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <?php endforeach; ?>
</div>

<style>
.lfs-milestone-progress {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.milestone-card {
    background: #f9f9f9;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 15px;
    border-left: 4px solid #3498db;
}

.milestone-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.milestone-header h4 {
    margin: 0;
    font-size: 16px;
}

.project-tag {
    background: #3498db;
    color: white;
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 12px;
}

.progress-bar-container {
    background: #e0e0e0;
    height: 30px;
    border-radius: 15px;
    overflow: hidden;
    margin-bottom: 10px;
}

.progress-bar {
    height: 100%;
    background: #95a5a6;
    transition: width 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 12px;
}

.progress-bar.low { background: #e74c3c; }
.progress-bar.medium { background: #f39c12; }
.progress-bar.high { background: #27ae60; }

.milestone-stats {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    font-size: 13px;
}

.milestone-stats .stat {
    color: #666;
}

.milestone-stats .stat.deadline {
    color: #e74c3c;
    font-weight: bold;
}

.milestone-rewards {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #ddd;
}

.rewards-label {
    font-weight: bold;
    font-size: 12px;
    margin-right: 5px;
}

.reward-tag {
    background: #f39c12;
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 11px;
    margin-right: 5px;
}
</style>