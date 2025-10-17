<?php
/**
 * Settings View
 * 
 * File location: admin/views/settings.php
 */

if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
if (isset($_POST['lfs_save_settings']) && check_admin_referer('lfs_settings_nonce')) {
    update_option('lfs_current_phase', sanitize_text_field($_POST['lfs_current_phase']));
    update_option('lfs_points_per_kr', floatval($_POST['lfs_points_per_kr']));
    update_option('lfs_weekly_fp_goal', intval($_POST['lfs_weekly_fp_goal']));
    update_option('lfs_weekly_bp_goal', intval($_POST['lfs_weekly_bp_goal']));
    update_option('lfs_weekly_sp_goal', intval($_POST['lfs_weekly_sp_goal']));
    update_option('lfs_reward_account_percent', intval($_POST['lfs_reward_account_percent']));
    update_option('lfs_monthly_income', intval($_POST['lfs_monthly_income']));
    
    echo '<div class="notice notice-success is-dismissible"><p>' . __('Inställningar sparade!', 'life-freedom-system') . '</p></div>';
}

// Get current settings
$current_phase = get_option('lfs_current_phase', 'survival');
$points_per_kr = get_option('lfs_points_per_kr', 0.5);
$weekly_fp_goal = get_option('lfs_weekly_fp_goal', 500);
$weekly_bp_goal = get_option('lfs_weekly_bp_goal', 300);
$weekly_sp_goal = get_option('lfs_weekly_sp_goal', 400);
$reward_percent = get_option('lfs_reward_account_percent', 2);
$monthly_income = get_option('lfs_monthly_income', 0);
$streak_days = get_option('lfs_streak_days', 0);
$days_since_leak = get_option('lfs_days_since_leak', 0);

$calculations = LFS_Calculations::get_instance();
$current_points = $calculations->get_current_points();
?>

<div class="wrap lfs-settings-page">
    <h1><?php _e('Inställningar - Life Freedom System', 'life-freedom-system'); ?></h1>
    
    <!-- Current Status Overview -->
    <div class="lfs-status-overview">
        <h2><?php _e('Aktuell status', 'life-freedom-system'); ?></h2>
        
        <div class="lfs-status-grid">
            <div class="lfs-status-card">
                <h3><?php _e('Livsfas', 'life-freedom-system'); ?></h3>
                <div class="lfs-status-value">
                    <?php
                    $phases = array(
                        'survival' => __('Survival', 'life-freedom-system'),
                        'stabilizing' => __('Stabilisering', 'life-freedom-system'),
                        'autonomy' => __('Autonomi', 'life-freedom-system'),
                    );
                    echo esc_html($phases[$current_phase] ?? 'Survival');
                    ?>
                </div>
            </div>
            
            <div class="lfs-status-card">
                <h3><?php _e('Streak', 'life-freedom-system'); ?></h3>
                <div class="lfs-status-value"><?php echo esc_html($streak_days); ?> 🔥</div>
                <p><?php _e('dagar i rad', 'life-freedom-system'); ?></p>
            </div>
            
            <div class="lfs-status-card">
                <h3><?php _e('Senaste läcka', 'life-freedom-system'); ?></h3>
                <div class="lfs-status-value"><?php echo esc_html($days_since_leak); ?></div>
                <p><?php _e('dagar sedan', 'life-freedom-system'); ?></p>
            </div>
            
            <div class="lfs-status-card">
                <h3><?php _e('Totalt poäng', 'life-freedom-system'); ?></h3>
                <div class="lfs-status-value"><?php echo esc_html($current_points['total']); ?></div>
                <p>FP: <?php echo $current_points['fp']; ?> | BP: <?php echo $current_points['bp']; ?> | SP: <?php echo $current_points['sp']; ?></p>
            </div>
        </div>
    </div>
    
    <!-- Settings Form -->
    <form method="post" action="" class="lfs-settings-form">
        <?php wp_nonce_field('lfs_settings_nonce'); ?>
        
        <!-- Phase Settings -->
        <div class="lfs-settings-section">
            <h2><?php _e('Fas-inställningar', 'life-freedom-system'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="lfs_current_phase"><?php _e('Nuvarande livsfas', 'life-freedom-system'); ?></label>
                    </th>
                    <td>
                        <select name="lfs_current_phase" id="lfs_current_phase" class="regular-text">
                            <option value="survival" <?php selected($current_phase, 'survival'); ?>>
                                <?php _e('Survival - Överleva och stabilisera', 'life-freedom-system'); ?>
                            </option>
                            <option value="stabilizing" <?php selected($current_phase, 'stabilizing'); ?>>
                                <?php _e('Stabilisering - Bygga buffert', 'life-freedom-system'); ?>
                            </option>
                            <option value="autonomy" <?php selected($current_phase, 'autonomy'); ?>>
                                <?php _e('Autonomi - Full frihet', 'life-freedom-system'); ?>
                            </option>
                        </select>
                        <p class="description">
                            <?php _e('Din fas påverkar poängvärde och belöningsbudget.', 'life-freedom-system'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="lfs_points_per_kr"><?php _e('Poäng per krona', 'life-freedom-system'); ?></label>
                    </th>
                    <td>
                        <input type="number" name="lfs_points_per_kr" id="lfs_points_per_kr" 
                               value="<?php echo esc_attr($points_per_kr); ?>" 
                               step="0.1" min="0.1" max="2" class="small-text">
                        <p class="description">
                            <?php _e('Hur många kronor 10 poäng är värda. Survival: 0.5 (10p = 5kr), Stabilisering: 0.8 (10p = 8kr), Autonomi: 1.0 (10p = 10kr)', 'life-freedom-system'); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Weekly Goals -->
        <div class="lfs-settings-section">
            <h2><?php _e('Veckomål', 'life-freedom-system'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="lfs_weekly_fp_goal"><?php _e('FP-mål per vecka', 'life-freedom-system'); ?></label>
                    </th>
                    <td>
                        <input type="number" name="lfs_weekly_fp_goal" id="lfs_weekly_fp_goal" 
                               value="<?php echo esc_attr($weekly_fp_goal); ?>" 
                               step="50" min="0" class="small-text">
                        <p class="description"><?php _e('Rekommenderat: 400-600 FP/vecka', 'life-freedom-system'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="lfs_weekly_bp_goal"><?php _e('BP-mål per vecka', 'life-freedom-system'); ?></label>
                    </th>
                    <td>
                        <input type="number" name="lfs_weekly_bp_goal" id="lfs_weekly_bp_goal" 
                               value="<?php echo esc_attr($weekly_bp_goal); ?>" 
                               step="50" min="0" class="small-text">
                        <p class="description"><?php _e('Rekommenderat: 250-400 BP/vecka', 'life-freedom-system'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="lfs_weekly_sp_goal"><?php _e('SP-mål per vecka', 'life-freedom-system'); ?></label>
                    </th>
                    <td>
                        <input type="number" name="lfs_weekly_sp_goal" id="lfs_weekly_sp_goal" 
                               value="<?php echo esc_attr($weekly_sp_goal); ?>" 
                               step="50" min="0" class="small-text">
                        <p class="description"><?php _e('Rekommenderat: 300-500 SP/vecka', 'life-freedom-system'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Economic Settings -->
        <div class="lfs-settings-section">
            <h2><?php _e('Ekonomiska inställningar', 'life-freedom-system'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="lfs_monthly_income"><?php _e('Månadsinkomst (kr)', 'life-freedom-system'); ?></label>
                    </th>
                    <td>
                        <input type="number" name="lfs_monthly_income" id="lfs_monthly_income" 
                               value="<?php echo esc_attr($monthly_income); ?>" 
                               step="1000" min="0" class="regular-text">
                        <p class="description"><?php _e('Din totala månadsinkomst efter skatt', 'life-freedom-system'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="lfs_reward_account_percent"><?php _e('Belöningskonto % av inkomst', 'life-freedom-system'); ?></label>
                    </th>
                    <td>
                        <input type="number" name="lfs_reward_account_percent" id="lfs_reward_account_percent" 
                               value="<?php echo esc_attr($reward_percent); ?>" 
                               step="1" min="0" max="15" class="small-text"> %
                        <p class="description">
                            <?php _e('Rekommenderat: Survival 2-3%, Stabilisering 5%, Autonomi 7-10%', 'life-freedom-system'); ?>
                        </p>
                        <?php if ($monthly_income > 0): ?>
                            <p class="description">
                                <strong><?php _e('Detta blir:', 'life-freedom-system'); ?></strong>
                                <?php echo number_format(($monthly_income * $reward_percent / 100), 0, ',', ' '); ?> kr/månad
                            </p>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <?php submit_button(__('Spara inställningar', 'life-freedom-system'), 'primary', 'lfs_save_settings'); ?>
    </form>
    
    <!-- System Info -->
    <div class="lfs-settings-section lfs-system-info">
        <h2><?php _e('Systeminformation', 'life-freedom-system'); ?></h2>
        
        <table class="form-table">
            <tr>
                <th><?php _e('Plugin Version', 'life-freedom-system'); ?></th>
                <td><?php echo LFS_VERSION; ?></td>
            </tr>
            <tr>
                <th><?php _e('WordPress Version', 'life-freedom-system'); ?></th>
                <td><?php echo get_bloginfo('version'); ?></td>
            </tr>
            <tr>
                <th><?php _e('Antal aktiviteter', 'life-freedom-system'); ?></th>
                <td><?php echo wp_count_posts('life_activity')->publish; ?></td>
            </tr>
            <tr>
                <th><?php _e('Antal projekt', 'life-freedom-system'); ?></th>
                <td><?php echo wp_count_posts('lfs_project')->publish; ?></td>
            </tr>
            <tr>
                <th><?php _e('Antal belöningar', 'life-freedom-system'); ?></th>
                <td><?php echo wp_count_posts('lfs_reward')->publish; ?></td>
            </tr>
            <tr>
                <th><?php _e('Antal transaktioner', 'life-freedom-system'); ?></th>
                <td><?php echo wp_count_posts('lfs_transaction')->publish; ?></td>
            </tr>
        </table>
    </div>
</div>

<style>
.lfs-settings-page {
    max-width: 1200px;
}

.lfs-status-overview {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin: 20px 0 30px;
}

.lfs-status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.lfs-status-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    border: 2px solid #dee2e6;
}

.lfs-status-card h3 {
    margin: 0 0 10px;
    font-size: 14px;
    color: #666;
}

.lfs-status-value {
    font-size: 32px;
    font-weight: 700;
    color: #333;
    margin: 10px 0;
}

.lfs-status-card p {
    margin: 5px 0 0;
    font-size: 13px;
    color: #666;
}

.lfs-settings-section {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.lfs-settings-section h2 {
    margin: 0 0 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
}

.lfs-system-info table {
    margin-top: 0;
}

.lfs-system-info th {
    font-weight: 600;
    width: 250px;
}

@media (max-width: 768px) {
    .lfs-status-grid {
        grid-template-columns: 1fr;
    }
}
</style>