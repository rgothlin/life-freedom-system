<?php
/**
 * Rewards View
 * 
 * File location: admin/views/rewards.php
 */

if (!defined('ABSPATH')) {
    exit;
}

$rewards_manager = LFS_Rewards::get_instance();
$calculations = LFS_Calculations::get_instance();

$current_points = $calculations->get_current_points();
$reward_balance = $calculations->get_reward_account_balance();
$redeemed_data = $rewards_manager->get_redeemed_rewards();

// Get rewards by level
$level_filter = isset($_GET['level']) ? sanitize_text_field($_GET['level']) : null;
$rewards = $rewards_manager->get_rewards_by_level($level_filter);
?>

<div class="wrap lfs-rewards-page">
    <h1><?php _e('Belöningar', 'life-freedom-system'); ?></h1>
    
    <!-- Balance Overview -->
    <div class="lfs-balance-overview">
        <div class="lfs-balance-card">
            <h3><?php _e('Tillgängligt på belöningskonto', 'life-freedom-system'); ?></h3>
            <div class="lfs-balance-amount"><?php echo number_format($reward_balance, 0, ',', ' '); ?> kr</div>
        </div>
        
        <div class="lfs-points-summary">
            <div class="lfs-point-item">
                <span class="lfs-point-label">FP:</span>
                <span class="lfs-point-value"><?php echo esc_html($current_points['fp']); ?></span>
            </div>
            <div class="lfs-point-item">
                <span class="lfs-point-label">BP:</span>
                <span class="lfs-point-value"><?php echo esc_html($current_points['bp']); ?></span>
            </div>
            <div class="lfs-point-item">
                <span class="lfs-point-label">SP:</span>
                <span class="lfs-point-value"><?php echo esc_html($current_points['sp']); ?></span>
            </div>
        </div>
    </div>
    
    <!-- Level Filter -->
    <div class="lfs-filter-bar">
        <a href="<?php echo admin_url('admin.php?page=lfs-rewards'); ?>" 
           class="lfs-filter-btn <?php echo !$level_filter ? 'active' : ''; ?>">
            <?php _e('Alla nivåer', 'life-freedom-system'); ?>
        </a>
        <a href="<?php echo admin_url('admin.php?page=lfs-rewards&level=niva-0-gratis'); ?>" 
           class="lfs-filter-btn <?php echo $level_filter === 'niva-0-gratis' ? 'active' : ''; ?>">
            <?php _e('Nivå 0 - Gratis', 'life-freedom-system'); ?>
        </a>
        <a href="<?php echo admin_url('admin.php?page=lfs-rewards&level=niva-1-daglig'); ?>" 
           class="lfs-filter-btn <?php echo $level_filter === 'niva-1-daglig' ? 'active' : ''; ?>">
            <?php _e('Nivå 1 - Daglig', 'life-freedom-system'); ?>
        </a>
        <a href="<?php echo admin_url('admin.php?page=lfs-rewards&level=niva-2-vecka'); ?>" 
           class="lfs-filter-btn <?php echo $level_filter === 'niva-2-vecka' ? 'active' : ''; ?>">
            <?php _e('Nivå 2 - Vecka', 'life-freedom-system'); ?>
        </a>
        <a href="<?php echo admin_url('admin.php?page=lfs-rewards&level=niva-3-manad'); ?>" 
           class="lfs-filter-btn <?php echo $level_filter === 'niva-3-manad' ? 'active' : ''; ?>">
            <?php _e('Nivå 3 - Månad', 'life-freedom-system'); ?>
        </a>
        <a href="<?php echo admin_url('admin.php?page=lfs-rewards&level=niva-4-milstolpe'); ?>" 
           class="lfs-filter-btn <?php echo $level_filter === 'niva-4-milstolpe' ? 'active' : ''; ?>">
            <?php _e('Nivå 4 - Milstolpe', 'life-freedom-system'); ?>
        </a>
    </div>
    
    <!-- Available Rewards -->
    <div class="lfs-rewards-grid">
        <?php if (!empty($rewards)): ?>
            <?php foreach ($rewards as $reward): ?>
                <?php if ($reward['status'] === 'available'): ?>
                    <div class="lfs-reward-card <?php echo $reward['can_afford'] ? 'lfs-reward-affordable' : 'lfs-reward-locked'; ?>">
                        <?php if ($reward['thumbnail']): ?>
                            <div class="lfs-reward-image">
                                <img src="<?php echo esc_url($reward['thumbnail']); ?>" alt="<?php echo esc_attr($reward['title']); ?>">
                            </div>
                        <?php endif; ?>
                        
                        <div class="lfs-reward-content">
                            <h3><?php echo esc_html($reward['title']); ?></h3>
                            
                            <?php if ($reward['content']): ?>
                                <div class="lfs-reward-description">
                                    <?php echo wp_kses_post($reward['content']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="lfs-reward-meta">
                                <div class="lfs-reward-cost">
                                    <?php if ($reward['cost'] > 0): ?>
                                        <strong><?php echo number_format($reward['cost'], 0, ',', ' '); ?> kr</strong>
                                    <?php else: ?>
                                        <strong class="lfs-free"><?php _e('Gratis', 'life-freedom-system'); ?></strong>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="lfs-reward-requirements">
                                    <?php if ($reward['total_required'] > 0): ?>
                                        <span class="lfs-badge"><?php echo $reward['total_required']; ?> <?php _e('totalt', 'life-freedom-system'); ?></span>
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
                            
                            <?php if ($reward['can_afford']): ?>
                                <button class="button button-primary lfs-redeem-btn" 
                                        data-reward-id="<?php echo esc_attr($reward['id']); ?>"
                                        data-cost="<?php echo esc_attr($reward['cost']); ?>">
                                    <?php _e('Lös in', 'life-freedom-system'); ?> ✓
                                </button>
                            <?php else: ?>
                                <button class="button lfs-locked-btn" disabled>
                                    <?php _e('Låst', 'life-freedom-system'); ?> 🔒
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="lfs-no-rewards">
                <p><?php _e('Inga belöningar hittades för denna nivå.', 'life-freedom-system'); ?></p>
                <a href="<?php echo admin_url('post-new.php?post_type=lfs_reward'); ?>" class="button button-primary">
                    <?php _e('Skapa din första belöning', 'life-freedom-system'); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Redeemed Rewards History -->
    <div class="lfs-rewards-history">
        <h2><?php _e('Inlösta belöningar', 'life-freedom-system'); ?></h2>
        
        <?php if (!empty($redeemed_data['rewards'])): ?>
            <div class="lfs-total-spent">
                <strong><?php _e('Totalt spenderat:', 'life-freedom-system'); ?></strong>
                <?php echo number_format($redeemed_data['total_spent'], 0, ',', ' '); ?> kr
            </div>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Belöning', 'life-freedom-system'); ?></th>
                        <th><?php _e('Kostnad', 'life-freedom-system'); ?></th>
                        <th><?php _e('Inlöst', 'life-freedom-system'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($redeemed_data['rewards'] as $reward): ?>
                        <tr>
                            <td><strong><?php echo esc_html($reward['title']); ?></strong></td>
                            <td><?php echo number_format($reward['cost'], 0, ',', ' '); ?> kr</td>
                            <td><?php echo esc_html($reward['redeemed_date_formatted']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p><?php _e('Du har inte löst in några belöningar än.', 'life-freedom-system'); ?></p>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('.lfs-redeem-btn').on('click', function() {
        var $btn = $(this);
        var rewardId = $btn.data('reward-id');
        var cost = $btn.data('cost');
        
        if (!confirm('<?php _e('Vill du lösa in denna belöning?', 'life-freedom-system'); ?>')) {
            return;
        }
        
        $btn.prop('disabled', true).text('<?php _e('Löser in...', 'life-freedom-system'); ?>');
        
        $.post(lfsData.ajaxUrl, {
            action: 'lfs_redeem_reward',
            nonce: lfsData.nonce,
            reward_id: rewardId
        }, function(response) {
            if (response.success) {
                alert(response.data.message);
                location.reload();
            } else {
                alert('<?php _e('Fel:', 'life-freedom-system'); ?> ' + response.data);
                $btn.prop('disabled', false).text('<?php _e('Lös in', 'life-freedom-system'); ?> ✓');
            }
        });
    });
});
</script>

<style>
.lfs-balance-overview {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin: 20px 0 30px;
}

.lfs-balance-card,
.lfs-points-summary {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.lfs-balance-card h3 {
    margin: 0 0 10px;
    font-size: 14px;
    color: #666;
}

.lfs-balance-amount {
    font-size: 36px;
    font-weight: 700;
    color: #9b59b6;
}

.lfs-points-summary {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 10px;
}

.lfs-point-item {
    display: flex;
    justify-content: space-between;
    font-size: 16px;
}

.lfs-point-label {
    font-weight: 600;
    color: #666;
}

.lfs-point-value {
    font-weight: 700;
    color: #333;
}

.lfs-filter-bar {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.lfs-filter-btn {
    padding: 10px 20px;
    background: #fff;
    border: 2px solid #ddd;
    border-radius: 6px;
    text-decoration: none;
    color: #333;
    transition: all 0.2s;
}

.lfs-filter-btn:hover {
    border-color: #9b59b6;
    color: #9b59b6;
}

.lfs-filter-btn.active {
    background: #9b59b6;
    border-color: #9b59b6;
    color: #fff;
}

.lfs-rewards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.lfs-reward-card {
    background: #fff;
    border: 2px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s;
}

.lfs-reward-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.lfs-reward-affordable {
    border-color: #2ecc71;
}

.lfs-reward-locked {
    opacity: 0.7;
}

.lfs-reward-image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.lfs-reward-content {
    padding: 20px;
}

.lfs-reward-content h3 {
    margin: 0 0 10px;
    font-size: 18px;
}

.lfs-reward-description {
    font-size: 14px;
    color: #666;
    margin-bottom: 15px;
}

.lfs-reward-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.lfs-reward-cost {
    font-size: 20px;
}

.lfs-reward-cost .lfs-free {
    color: #2ecc71;
}

.lfs-reward-requirements {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.lfs-redeem-btn {
    width: 100%;
}

.lfs-locked-btn {
    width: 100%;
    background: #95a5a6;
    color: #fff;
}

.lfs-rewards-history {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.lfs-rewards-history h2 {
    margin: 0 0 15px;
}

.lfs-total-spent {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
    margin-bottom: 20px;
    font-size: 16px;
}

.lfs-no-rewards {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
}

@media (max-width: 768px) {
    .lfs-balance-overview {
        grid-template-columns: 1fr;
    }
    
    .lfs-rewards-grid {
        grid-template-columns: 1fr;
    }
}
</style>