<?php
/**
 * Dashboard View - COMPLETE VERSION
 * 
 * File location: admin/views/dashboard.php
 */

if (!defined('ABSPATH')) {
    exit;
}

$dashboard = LFS_Dashboard::get_instance();
$calculations = LFS_Calculations::get_instance();
$template_manager = LFS_Activity_Templates::get_instance();

$data = $dashboard->get_dashboard_data();
$current_points = $data['current_points'];
$weekly_points = $data['weekly_points'];
$weekly_goals = $data['weekly_goals'];
$templates = $template_manager->get_templates();
?>

<div class="wrap lfs-dashboard">
    <h1><?php _e('Life Freedom System - Dashboard', 'life-freedom-system'); ?></h1>
    
    <!-- Overview Cards -->
    <div class="lfs-cards-grid">
        <!-- FP Card -->
        <div class="lfs-card lfs-card-fp">
            <div class="lfs-card-header">
                <h3><?php _e('Freedom Points (FP)', 'life-freedom-system'); ?></h3>
                <span class="lfs-card-icon">🚀</span>
            </div>
            <div class="lfs-card-body">
                <div class="lfs-points-display">
                    <span class="lfs-points-number"><?php echo esc_html($current_points['fp']); ?></span>
                    <span class="lfs-points-label">FP</span>
                </div>
                <div class="lfs-progress-bar">
                    <div class="lfs-progress-fill lfs-progress-fp" style="width: <?php echo min(100, ($weekly_points['fp'] / max(1, $weekly_goals['fp'])) * 100); ?>%"></div>
                </div>
                <p class="lfs-progress-text">
                    <?php printf(__('Veckans mål: %d / %d FP (%d%%)', 'life-freedom-system'), 
                        $weekly_points['fp'], 
                        $weekly_goals['fp'],
                        min(100, round(($weekly_points['fp'] / max(1, $weekly_goals['fp'])) * 100))
                    ); ?>
                </p>
            </div>
        </div>
        
        <!-- BP Card -->
        <div class="lfs-card lfs-card-bp">
            <div class="lfs-card-header">
                <h3><?php _e('Balance Points (BP)', 'life-freedom-system'); ?></h3>
                <span class="lfs-card-icon">⚖️</span>
            </div>
            <div class="lfs-card-body">
                <div class="lfs-points-display">
                    <span class="lfs-points-number"><?php echo esc_html($current_points['bp']); ?></span>
                    <span class="lfs-points-label">BP</span>
                </div>
                <div class="lfs-progress-bar">
                    <div class="lfs-progress-fill lfs-progress-bp" style="width: <?php echo min(100, ($weekly_points['bp'] / max(1, $weekly_goals['bp'])) * 100); ?>%"></div>
                </div>
                <p class="lfs-progress-text">
                    <?php printf(__('Veckans mål: %d / %d BP (%d%%)', 'life-freedom-system'), 
                        $weekly_points['bp'], 
                        $weekly_goals['bp'],
                        min(100, round(($weekly_points['bp'] / max(1, $weekly_goals['bp'])) * 100))
                    ); ?>
                </p>
            </div>
        </div>
        
        <!-- SP Card -->
        <div class="lfs-card lfs-card-sp">
            <div class="lfs-card-header">
                <h3><?php _e('Stability Points (SP)', 'life-freedom-system'); ?></h3>
                <span class="lfs-card-icon">🛡️</span>
            </div>
            <div class="lfs-card-body">
                <div class="lfs-points-display">
                    <span class="lfs-points-number"><?php echo esc_html($current_points['sp']); ?></span>
                    <span class="lfs-points-label">SP</span>
                </div>
                <div class="lfs-progress-bar">
                    <div class="lfs-progress-fill lfs-progress-sp" style="width: <?php echo min(100, ($weekly_points['sp'] / max(1, $weekly_goals['sp'])) * 100); ?>%"></div>
                </div>
                <p class="lfs-progress-text">
                    <?php printf(__('Veckans mål: %d / %d SP (%d%%)', 'life-freedom-system'), 
                        $weekly_points['sp'], 
                        $weekly_goals['sp'],
                        min(100, round(($weekly_points['sp'] / max(1, $weekly_goals['sp'])) * 100))
                    ); ?>
                </p>
            </div>
        </div>
        
        <!-- Reward Balance Card -->
        <div class="lfs-card lfs-card-reward">
            <div class="lfs-card-header">
                <h3><?php _e('Belöningskonto', 'life-freedom-system'); ?></h3>
                <span class="lfs-card-icon">🎁</span>
            </div>
            <div class="lfs-card-body">
                <div class="lfs-points-display">
                    <span class="lfs-points-number"><?php echo number_format($data['reward_balance'], 0, ',', ' '); ?></span>
                    <span class="lfs-points-label">kr</span>
                </div>
                <p class="lfs-streak-text">
                    <strong><?php _e('Streak:', 'life-freedom-system'); ?></strong> 
                    <?php echo esc_html($data['streak']); ?> <?php _e('dagar', 'life-freedom-system'); ?> 🔥
                </p>
            </div>
        </div>
    </div>
    
    <!-- Main Content Area -->
    <div class="lfs-main-content">
        <!-- Left Column -->
        <div class="lfs-column lfs-column-left">
            
            <!-- Weekly Chart -->
            <div class="lfs-widget">
                <h2><?php _e('Denna vecka', 'life-freedom-system'); ?></h2>
                <canvas id="lfsWeeklyChart" width="400" height="200"></canvas>
            </div>
            
            <!-- Point Distribution -->
            <div class="lfs-widget">
                <h2><?php _e('Poängfördelning (30 dagar)', 'life-freedom-system'); ?></h2>
                <canvas id="lfsDistributionChart" width="400" height="200"></canvas>
            </div>
            
            <!-- Activity Types -->
            <div class="lfs-widget">
                <h2><?php _e('Aktivitetstyper', 'life-freedom-system'); ?></h2>
                <canvas id="lfsActivityTypesChart" width="400" height="250"></canvas>
            </div>
            
        </div>
        
        <!-- Right Column -->
        <div class="lfs-column lfs-column-right">
            
            <!-- Quick Add Activity -->
            <div class="lfs-widget lfs-quick-add">
                <h2><?php _e('Snabbloggning', 'life-freedom-system'); ?></h2>
                
                <?php if (!empty($templates)): ?>
                    <div class="lfs-template-buttons">
                        <?php foreach ($templates as $template): 
                            $bg_color = !empty($template['color']) ? $template['color'] : '#3498db';
                            $icon = !empty($template['icon']) ? $template['icon'] : '✓';
                        ?>
                            <button class="lfs-template-btn lfs-quick-log-btn" 
                                    data-template-id="<?php echo esc_attr($template['id']); ?>"
                                    style="border-left: 4px solid <?php echo esc_attr($bg_color); ?>;">
                                <?php if ($icon): ?>
                                    <span class="lfs-template-icon"><?php echo esc_html($icon); ?></span>
                                <?php endif; ?>
                                <span class="lfs-template-title"><?php echo esc_html($template['title']); ?></span>
                                <span class="lfs-template-points">
                                    <?php if ($template['fp'] > 0): ?>
                                        <span class="lfs-fp-badge"><?php echo $template['fp']; ?> FP</span>
                                    <?php endif; ?>
                                    <?php if ($template['bp'] > 0): ?>
                                        <span class="lfs-bp-badge"><?php echo $template['bp']; ?> BP</span>
                                    <?php endif; ?>
                                    <?php if ($template['sp'] > 0): ?>
                                        <span class="lfs-sp-badge"><?php echo $template['sp']; ?> SP</span>
                                    <?php endif; ?>
                                </span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="lfs-template-actions">
                        <a href="<?php echo admin_url('edit.php?post_type=lfs_activity_tpl'); ?>" class="button">
                            <?php _e('Hantera mallar', 'life-freedom-system'); ?>
                        </a>
                        <a href="<?php echo admin_url('post-new.php?post_type=lfs_activity_tpl'); ?>" class="button button-primary">
                            <?php _e('+ Ny mall', 'life-freedom-system'); ?>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="lfs-no-templates">
                        <p><?php _e('Inga mallar skapade ännu. Skapa din första mall för att komma igång snabbt!', 'life-freedom-system'); ?></p>
                        <a href="<?php echo admin_url('post-new.php?post_type=lfs_activity_tpl'); ?>" class="button button-primary">
                            <?php _e('Skapa din första mall', 'life-freedom-system'); ?>
                        </a>
                        <p class="lfs-template-help">
                            <em><?php _e('Tips: Du kan också skapa standardmallar automatiskt genom att klicka på knappen nedan.', 'life-freedom-system'); ?></em>
                        </p>
                        <button id="lfsCreateDefaultTemplates" class="button">
                            <?php _e('Skapa standardmallar', 'life-freedom-system'); ?>
                        </button>
                    </div>
                <?php endif; ?>
                
                <div class="lfs-custom-activity">
                    <h3><?php _e('Egen aktivitet', 'life-freedom-system'); ?></h3>
                    <form id="lfsCustomActivityForm">
                        <input type="text" id="lfsCustomTitle" placeholder="<?php esc_attr_e('Aktivitetsnamn...', 'life-freedom-system'); ?>" required>
                        <div class="lfs-points-inputs">
                            <label>
                                <span>FP:</span>
                                <input type="number" id="lfsCustomFP" min="0" max="100" step="5" value="0">
                            </label>
                            <label>
                                <span>BP:</span>
                                <input type="number" id="lfsCustomBP" min="0" max="50" step="5" value="0">
                            </label>
                            <label>
                                <span>SP:</span>
                                <input type="number" id="lfsCustomSP" min="0" max="100" step="5" value="0">
                            </label>
                        </div>
                        <button type="submit" class="button button-primary"><?php _e('Lägg till', 'life-freedom-system'); ?></button>
                    </form>
                </div>
            </div>
            
            <!-- Recent Activities -->
            <div class="lfs-widget">
                <h2><?php _e('Senaste aktiviteterna', 'life-freedom-system'); ?></h2>
                <div class="lfs-recent-activities">
                    <?php if (!empty($data['recent_activities'])): ?>
                        <?php foreach ($data['recent_activities'] as $activity): ?>
                            <div class="lfs-activity-item">
                                <div class="lfs-activity-title">
                                    <strong><?php echo esc_html($activity['title']); ?></strong>
                                    <span class="lfs-activity-date"><?php echo esc_html($activity['date_formatted']); ?></span>
                                </div>
                                <div class="lfs-activity-points">
                                    <?php if ($activity['fp'] > 0): ?>
                                        <span class="lfs-badge lfs-badge-fp"><?php echo $activity['fp']; ?> FP</span>
                                    <?php endif; ?>
                                    <?php if ($activity['bp'] > 0): ?>
                                        <span class="lfs-badge lfs-badge-bp"><?php echo $activity['bp']; ?> BP</span>
                                    <?php endif; ?>
                                    <?php if ($activity['sp'] > 0): ?>
                                        <span class="lfs-badge lfs-badge-sp"><?php echo $activity['sp']; ?> SP</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p><?php _e('Inga aktiviteter ännu. Lägg till din första!', 'life-freedom-system'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Available Rewards -->
            <div class="lfs-widget">
                <h2><?php _e('Tillgängliga belöningar', 'life-freedom-system'); ?></h2>
                <div class="lfs-available-rewards">
                    <?php if (!empty($data['available_rewards'])): ?>
                        <?php foreach (array_slice($data['available_rewards'], 0, 3) as $reward): ?>
                            <div class="lfs-reward-item <?php echo $reward['can_afford'] ? 'lfs-reward-affordable' : ''; ?>">
                                <div class="lfs-reward-title">
                                    <strong><?php echo esc_html($reward['title']); ?></strong>
                                    <?php if ($reward['cost'] > 0): ?>
                                        <span class="lfs-reward-cost"><?php echo number_format($reward['cost'], 0, ',', ' '); ?> kr</span>
                                    <?php else: ?>
                                        <span class="lfs-reward-cost lfs-reward-free"><?php _e('Gratis', 'life-freedom-system'); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="lfs-reward-requirements">
                                    <?php if ($reward['total_required'] > 0): ?>
                                        <span class="lfs-badge"><?php echo $reward['total_required']; ?> <?php _e('totalt poäng', 'life-freedom-system'); ?></span>
                                    <?php else: ?>
                                        <?php if ($reward['fp_required'] > 0): ?>
                                            <span class="lfs-badge lfs-badge-fp"><?php echo $reward['fp_required']; ?> FP</span>
                                        <?php endif; ?>
                                        <?php if ($reward['bp_required'] > 0): ?>
                                            <span class="lfs-badge lfs-badge-bp"><?php echo $reward['bp_required']; ?> BP</span>
                                        <?php endif; ?>
                                        <?php if ($reward['sp_required'] > 0): ?>
                                            <span class="lfs-badge lfs-badge-sp"><?php echo $reward['sp_required']; ?> SP</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="<?php echo admin_url('admin.php?page=lfs-rewards'); ?>" class="button">
                            <?php _e('Se alla belöningar →', 'life-freedom-system'); ?>
                        </a>
                    <?php else: ?>
                        <p><?php _e('Inga belöningar definierade än.', 'life-freedom-system'); ?></p>
                        <a href="<?php echo admin_url('post-new.php?post_type=lfs_reward'); ?>" class="button">
                            <?php _e('Skapa belöningar', 'life-freedom-system'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Next Milestone -->
            <?php if ($data['next_milestone']): ?>
                <div class="lfs-widget lfs-milestone-widget">
                    <h2><?php _e('Nästa milstolpe', 'life-freedom-system'); ?></h2>
                    <div class="lfs-milestone-card">
                        <h3><?php echo esc_html($data['next_milestone']['title']); ?></h3>
                        <p class="lfs-milestone-reward">
                            <strong><?php _e('Belöning:', 'life-freedom-system'); ?></strong> 
                            <?php echo number_format($data['next_milestone']['reward'], 0, ',', ' '); ?> kr
                        </p>
                        <div class="lfs-milestone-progress">
                            <?php if ($data['next_milestone']['fp_required'] > 0): ?>
                                <div class="lfs-progress-item">
                                    <span class="lfs-progress-label">FP</span>
                                    <div class="lfs-progress-bar">
                                        <div class="lfs-progress-fill lfs-progress-fp" style="width: <?php echo min(100, $data['next_milestone']['fp_progress']); ?>%"></div>
                                    </div>
                                    <span class="lfs-progress-percent"><?php echo $data['next_milestone']['fp_progress']; ?>%</span>
                                </div>
                            <?php endif; ?>
                            <?php if ($data['next_milestone']['bp_required'] > 0): ?>
                                <div class="lfs-progress-item">
                                    <span class="lfs-progress-label">BP</span>
                                    <div class="lfs-progress-bar">
                                        <div class="lfs-progress-fill lfs-progress-bp" style="width: <?php echo min(100, $data['next_milestone']['bp_progress']); ?>%"></div>
                                    </div>
                                    <span class="lfs-progress-percent"><?php echo $data['next_milestone']['bp_progress']; ?>%</span>
                                </div>
                            <?php endif; ?>
                            <?php if ($data['next_milestone']['sp_required'] > 0): ?>
                                <div class="lfs-progress-item">
                                    <span class="lfs-progress-label">SP</span>
                                    <div class="lfs-progress-bar">
                                        <div class="lfs-progress-fill lfs-progress-sp" style="width: <?php echo min(100, $data['next_milestone']['sp_progress']); ?>%"></div>
                                    </div>
                                    <span class="lfs-progress-percent"><?php echo $data['next_milestone']['sp_progress']; ?>%</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<?php
// Prepare chart data
$weekly_data = $calculations->get_daily_points_for_chart(7);
$labels = array();
$fp_data = array();
$bp_data = array();
$sp_data = array();

foreach ($weekly_data as $day) {
    $labels[] = $day['label'];
    $fp_data[] = $day['fp'];
    $bp_data[] = $day['bp'];
    $sp_data[] = $day['sp'];
}

$activity_types = $calculations->get_activity_type_distribution(30);
$type_labels = array();
$type_counts = array();
$type_totals = array();

foreach ($activity_types as $name => $data_item) {
    $type_labels[] = $name;
    $type_counts[] = $data_item['count'];
    $type_totals[] = $data_item['total'];
}
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
    
    // Simple check - only run once
    if (window.lfsChartInitialized) {
        return;
    }
    window.lfsChartInitialized = true;
    
    console.log('Initializing LFS Dashboard...');
    
    // Global state
    let isProcessing = false;
    let weeklyChart, distributionChart, activityTypesChart;
    
    /**
     * UI UPDATE FUNCTIONS
     */
    function updateDashboardUI(data) {
        console.log('Uppdaterar dashboard UI:', data);
        
        if (data.points) {
            $('.lfs-card-fp .lfs-points-number').text(data.points.fp);
            $('.lfs-card-bp .lfs-points-number').text(data.points.bp);
            $('.lfs-card-sp .lfs-points-number').text(data.points.sp);
        }
        
        if (data.weekly_points && data.weekly_goals) {
            updateProgressBars(data.weekly_points, data.weekly_goals);
        }
        
        if (data.chart_data && weeklyChart) {
            updateWeeklyChart(data.chart_data);
        }
        
        if (data.points_added) {
            showSuccessNotification(data.points_added, data.message);
        }
    }
    
    function updateProgressBars(weeklyPoints, weeklyGoals) {
        let fpPercent = weeklyGoals.fp > 0 ? Math.min(100, (weeklyPoints.fp / weeklyGoals.fp) * 100) : 0;
        $('.lfs-card-fp .lfs-progress-fill').css('width', fpPercent + '%');
        
        let bpPercent = weeklyGoals.bp > 0 ? Math.min(100, (weeklyPoints.bp / weeklyGoals.bp) * 100) : 0;
        $('.lfs-card-bp .lfs-progress-fill').css('width', bpPercent + '%');
        
        let spPercent = weeklyGoals.sp > 0 ? Math.min(100, (weeklyPoints.sp / weeklyGoals.sp) * 100) : 0;
        $('.lfs-card-sp .lfs-progress-fill').css('width', spPercent + '%');
    }
    
    function updateWeeklyChart(chartData) {
        if (!weeklyChart) return;
        
        weeklyChart.data.datasets[0].data = chartData.fp;
        weeklyChart.data.datasets[1].data = chartData.bp;
        weeklyChart.data.datasets[2].data = chartData.sp;
        weeklyChart.update('none');
    }
    
    function showSuccessNotification(pointsAdded, customMessage) {
        let message = customMessage || '✅ Aktivitet loggad!';
        
        if (pointsAdded && (pointsAdded.fp > 0 || pointsAdded.bp > 0 || pointsAdded.sp > 0)) {
            let parts = [];
            if (pointsAdded.fp > 0) parts.push('+' + pointsAdded.fp + ' FP');
            if (pointsAdded.bp > 0) parts.push('+' + pointsAdded.bp + ' BP');
            if (pointsAdded.sp > 0) parts.push('+' + pointsAdded.sp + ' SP');
            
            if (parts.length > 0) {
                message += ' ' + parts.join(', ');
            }
        }
        
        let $notification = $('<div class="lfs-notification lfs-notification-success">' + message + '</div>');
        $('body').append($notification);
        
        setTimeout(function() {
            $notification.addClass('lfs-notification-show');
        }, 10);
        
        setTimeout(function() {
            $notification.removeClass('lfs-notification-show');
            setTimeout(function() {
                $notification.remove();
            }, 300);
        }, 3000);
    }
    
    /**
     * AJAX EVENT HANDLERS
     */
    
    // Quick log från template
    $(document).on('click', '.lfs-quick-log-btn', function(e) {
        e.preventDefault();
        
        if (isProcessing) {
            console.log('Request pågår redan');
            return;
        }
        
        isProcessing = true;
        
        let $btn = $(this);
        let templateId = $btn.data('template-id');
        
        console.log('Quick log template:', templateId);
        
        $btn.prop('disabled', true).addClass('lfs-loading');
        
        $.ajax({
            url: lfsData.ajaxUrl,
            method: 'POST',
            data: {
                action: 'lfs_quick_log_template',
                nonce: lfsData.nonce,
                template_id: templateId
            },
            success: function(response) {
                console.log('Response:', response);
                
                if (response.success) {
                    $btn.removeClass('lfs-loading').addClass('lfs-success');
                    
                    updateDashboardUI(response.data);
                    
                    setTimeout(function() {
                        $btn.removeClass('lfs-success').prop('disabled', false);
                        isProcessing = false;
                    }, 1500);
                } else {
                    alert('Fel: ' + (response.data || 'Okänt fel'));
                    $btn.removeClass('lfs-loading').prop('disabled', false);
                    isProcessing = false;
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                alert('Fel: ' + error);
                $btn.removeClass('lfs-loading').prop('disabled', false);
                isProcessing = false;
            }
        });
    });
    
    // Custom activity form
    $('#lfsCustomActivityForm').on('submit', function(e) {
        e.preventDefault();
        
        if (isProcessing) return;
        isProcessing = true;
        
        let $form = $(this);
        let $submitBtn = $form.find('button[type="submit"]');
        
        $submitBtn.prop('disabled', true).addClass('lfs-loading');
        
        $.ajax({
            url: lfsData.ajaxUrl,
            method: 'POST',
            data: {
                action: 'lfs_quick_add_activity',
                nonce: lfsData.nonce,
                title: $('#lfsCustomTitle').val(),
                fp: $('#lfsCustomFP').val(),
                bp: $('#lfsCustomBP').val(),
                sp: $('#lfsCustomSP').val()
            },
            success: function(response) {
                if (response.success) {
                    updateDashboardUI(response.data);
                    $form[0].reset();
                    $submitBtn.removeClass('lfs-loading').prop('disabled', false);
                    isProcessing = false;
                } else {
                    alert('Fel: ' + (response.data || 'Okänt fel'));
                    $submitBtn.removeClass('lfs-loading').prop('disabled', false);
                    isProcessing = false;
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                alert('Fel: ' + error);
                $submitBtn.removeClass('lfs-loading').prop('disabled', false);
                isProcessing = false;
            }
        });
    });
    
    // Create default templates
    $('#lfsCreateDefaultTemplates').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm('Vill du skapa standardmallar? Detta kommer lägga till flera fördefinierade aktivitetsmallar.')) {
            return;
        }
        
        let $btn = $(this);
        $btn.prop('disabled', true).text('Skapar mallar...');
        
        $.ajax({
            url: lfsData.ajaxUrl,
            method: 'POST',
            data: {
                action: 'lfs_create_default_templates',
                nonce: lfsData.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Standardmallar skapade! Laddar om sidan...');
                    location.reload();
                } else {
                    alert('Fel: ' + (response.data || 'Okänt fel'));
                    $btn.prop('disabled', false).text('Skapa standardmallar');
                }
            },
            error: function(xhr, status, error) {
                alert('Fel: ' + error);
                $btn.prop('disabled', false).text('Skapa standardmallar');
            }
        });
    });
    
    /**
     * CHART INITIALIZATION - MUST BE AFTER ALL OTHER CODE
     * Wait for canvas to be fully rendered
     */
    
    // Use requestAnimationFrame to ensure DOM is painted
    requestAnimationFrame(function() {
        
        // Weekly Chart
        var weeklyCanvas = document.getElementById('lfsWeeklyChart');
        if (weeklyCanvas && typeof Chart !== 'undefined') {
            console.log('Initializing weekly chart...');
            var weeklyCtx = weeklyCanvas.getContext('2d');
            weeklyChart = new Chart(weeklyCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: [
                        {
                            label: 'FP',
                            data: <?php echo json_encode($fp_data); ?>,
                            borderColor: '#3498db',
                            backgroundColor: 'rgba(52, 152, 219, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'BP',
                            data: <?php echo json_encode($bp_data); ?>,
                            borderColor: '#2ecc71',
                            backgroundColor: 'rgba(46, 204, 113, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'SP',
                            data: <?php echo json_encode($sp_data); ?>,
                            borderColor: '#f39c12',
                            backgroundColor: 'rgba(243, 156, 18, 0.1)',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false, // CRITICAL: Disable animation
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            console.log('Weekly chart initialized successfully');
        }
        
        // Distribution Chart
        var distributionCanvas = document.getElementById('lfsDistributionChart');
        if (distributionCanvas && typeof Chart !== 'undefined') {
            console.log('Initializing distribution chart...');
            var distributionCtx = distributionCanvas.getContext('2d');
            distributionChart = new Chart(distributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['FP', 'BP', 'SP'],
                    datasets: [{
                        data: [
                            <?php echo $current_points['fp']; ?>,
                            <?php echo $current_points['bp']; ?>,
                            <?php echo $current_points['sp']; ?>
                        ],
                        backgroundColor: [
                            '#3498db',
                            '#2ecc71',
                            '#f39c12'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false, // CRITICAL: Disable animation
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
            console.log('Distribution chart initialized successfully');
        }
        
        // Activity Types Chart
        var activityTypesCanvas = document.getElementById('lfsActivityTypesChart');
        if (activityTypesCanvas && typeof Chart !== 'undefined') {
            console.log('Initializing activity types chart...');
            var activityTypesCtx = activityTypesCanvas.getContext('2d');
            activityTypesChart = new Chart(activityTypesCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($type_labels); ?>,
                    datasets: [
                        {
                            label: 'Antal aktiviteter',
                            data: <?php echo json_encode($type_counts); ?>,
                            backgroundColor: 'rgba(52, 152, 219, 0.7)',
                            yAxisID: 'y'
                        },
                        {
                            label: 'Totalt poäng',
                            data: <?php echo json_encode($type_totals); ?>,
                            backgroundColor: 'rgba(46, 204, 113, 0.7)',
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false, // CRITICAL: Disable animation
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Antal'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Poäng'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
            console.log('Activity types chart initialized successfully');
        }
        
    }); // End requestAnimationFrame
    
    console.log('LFS Dashboard initialized successfully');
});
</script>

<!-- CSS -->
<style>
.lfs-notification {
    position: fixed;
    top: 32px;
    right: 20px;
    background: white;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 100000;
    opacity: 0;
    transform: translateX(400px);
    transition: all 0.3s ease;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 10px;
}

.lfs-notification-show {
    opacity: 1;
    transform: translateX(0);
}

.lfs-notification-success {
    border-left: 4px solid #27ae60;
    color: #27ae60;
}

.lfs-loading {
    position: relative;
    pointer-events: none;
    opacity: 0.6;
}

.lfs-loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #fff;
    border-top-color: transparent;
    border-radius: 50%;
    animation: lfs-spin 0.6s linear infinite;
}

.lfs-success {
    background: #27ae60 !important;
    color: white !important;
    position: relative;
}

.lfs-success::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 24px;
    color: white;
}

@keyframes lfs-spin {
    to { transform: rotate(360deg); }
}

.lfs-progress-fill {
    transition: width 0.6s ease;
}

.lfs-template-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    margin-bottom: 8px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    width: 100%;
    text-align: left;
}

.lfs-template-btn:hover {
    background: #f8f9fa;
    transform: translateX(2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.lfs-template-btn:active {
    transform: translateX(0);
}

.lfs-template-icon {
    font-size: 1.5rem;
    line-height: 1;
}

.lfs-template-title {
    flex: 1;
    font-weight: 500;
}

.lfs-template-points {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.lfs-fp-badge,
.lfs-bp-badge,
.lfs-sp-badge {
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 600;
}

.lfs-fp-badge {
    background: rgba(52, 152, 219, 0.2);
    color: #3498db;
}

.lfs-bp-badge {
    background: rgba(46, 204, 113, 0.2);
    color: #2ecc71;
}

.lfs-sp-badge {
    background: rgba(243, 156, 18, 0.2);
    color: #f39c12;
}

.lfs-no-templates {
    text-align: center;
    padding: 20px;
}

.lfs-template-help {
    margin: 15px 0;
    font-size: 0.9rem;
    color: #666;
}

.lfs-template-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.lfs-custom-activity {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid #eee;
}

.lfs-points-inputs {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin: 10px 0;
}

.lfs-points-inputs label {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.lfs-points-inputs span {
    font-weight: 600;
    font-size: 0.9rem;
}

.lfs-points-inputs input {
    width: 100%;
    padding: 8px;
}
</style>


<!-- LFS patch: break Chart.js resize loop & wrap canvases (auto) -->
<script>
(function() {
  if (typeof window !== 'undefined') {
    // Wrap canvases inside .lfs-dashboard that are not already wrapped
    var root = document.querySelector('.lfs-dashboard') || document;
    var canvases = root.querySelectorAll('canvas[id^="lfs"]');
    canvases.forEach(function(cv){
      if (!cv.parentElement || !cv.parentElement.classList || !cv.parentElement.classList.contains('lfs-chart-wrap')) {
        var wrap = document.createElement('div');
        wrap.className = 'lfs-chart-wrap';
        cv.parentNode.insertBefore(wrap, cv);
        wrap.appendChild(cv);
      }
    });
  }

  // If Chart is present, set safe defaults to reduce resize thrash
  function applyChartDefaults(){
    if (typeof Chart === 'undefined') return;
    try {
      // Do not override if already set
      if (Chart.defaults && Chart.defaults.maintainAspectRatio !== false) {
        Chart.defaults.maintainAspectRatio = false;
      }
      if (Chart.defaults && typeof Chart.defaults.resizeDelay === 'undefined') {
        Chart.defaults.resizeDelay = 150;
      }
      // Disable animations on admin dashboard charts for stability/perf
      if (Chart.defaults && Chart.defaults.animation !== false) {
        Chart.defaults.animation = false;
      }
    } catch(e){ /* no-op */ }
  }

  if (document.readyState === 'complete' || document.readyState === 'interactive') {
    applyChartDefaults();
  } else {
    document.addEventListener('DOMContentLoaded', applyChartDefaults);
  }
})();
</script>
<!-- /LFS patch -->
