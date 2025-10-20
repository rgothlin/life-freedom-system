<?php
/**
 * Rewards View - KOMPLETT FUNGERANDE VERSION
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

// --- Nivåfilter + grupperade belöningar (från filtrerad lista) ---
$level_filter = isset($_GET['level']) ? sanitize_text_field($_GET['level']) : null;
// $rewards_by_level = $rewards_manager->get_rewards_by_level($level_filter);

// --- Custom grouping logic with status priority for this view ---
$rewards_by_level = $rewards_manager->get_rewards_by_level($level_filter);
$grouped_rewards = [
    'available' => [],
    'pending' => [],
    'redeemed' => [],
];
if (!empty($rewards_by_level)) {
    foreach ($rewards_by_level as $r) {
        // 1) Om redan inlöst -> historik (redeemed)
        if (isset($r['status']) && $r['status'] === 'redeemed') {
            $grouped_rewards['redeemed'][] = $r;
            continue;
        }
        // 2) Annars: om den kan lösas in nu -> available, annars pending
        if (!empty($r['can_afford'])) {
            $grouped_rewards['available'][] = $r;
        } else {
            $grouped_rewards['pending'][] = $r;
        }
    }
}

// Ensure jQuery is loaded
wp_enqueue_script('jquery');
?>

<div class="wrap lfs-rewards-page">
    <h1><?php _e('Belöningar', 'life-freedom-system'); ?></h1>
    
    <!-- Balance Overview -->
    <div class="lfs-balance-overview">
        <div class="lfs-balance-card">
            <h3><?php _e('Tillgängligt på belöningskonto', 'life-freedom-system'); ?></h3>
            <div class="lfs-balance-amount" id="lfs-reward-balance"><?php echo number_format($reward_balance, 0, ',', ' '); ?> kr</div>
        </div>
        
        <div class="lfs-points-summary">
            <div class="lfs-point-item">
                <span class="lfs-point-label">FP:</span>
                <span class="lfs-point-value" id="lfs-current-fp"><?php echo esc_html($current_points['fp']); ?></span>
            </div>
            <div class="lfs-point-item">
                <span class="lfs-point-label">BP:</span>
                <span class="lfs-point-value" id="lfs-current-bp"><?php echo esc_html($current_points['bp']); ?></span>
            </div>
            <div class="lfs-point-item">
                <span class="lfs-point-label">SP:</span>
                <span class="lfs-point-value" id="lfs-current-sp"><?php echo esc_html($current_points['sp']); ?></span>
            </div>
        </div>
    </div>

    <!-- Level Filter -->
    <div class="lfs-filter-bar" style="margin: 10px 0 25px; display: flex; flex-wrap: wrap; gap: 8px;">
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
    
    <!-- TILLGÄNGLIGA BELÖNINGAR - Kan lösas in NU -->
    <div class="lfs-rewards-section">
        <h2>
            <span class="dashicons dashicons-yes-alt" style="color: #2ecc71;"></span>
            <?php _e('Tillgängliga belöningar', 'life-freedom-system'); ?>
            <span class="lfs-section-count">(<?php echo count($grouped_rewards['available']); ?>)</span>
        </h2>
        <p class="lfs-section-description"><?php _e('Dessa belöningar kan du lösa in direkt!', 'life-freedom-system'); ?></p>
        
        <?php if (!empty($grouped_rewards['available'])): ?>
            <div class="lfs-rewards-grid">
                <?php foreach ($grouped_rewards['available'] as $reward): ?>
                    <div class="lfs-reward-card lfs-reward-available" data-reward-id="<?php echo $reward['id']; ?>">
                        <?php if ($reward['thumbnail']): ?>
                            <div class="lfs-reward-image">
                                <img src="<?php echo esc_url($reward['thumbnail']); ?>" alt="<?php echo esc_attr($reward['title']); ?>">
                            </div>
                        <?php endif; ?>
                        
                        <div class="lfs-reward-content">
                            <h3><?php echo esc_html($reward['title']); ?></h3>
                            
                            <?php if ($reward['is_recurring']): ?>
                                <span class="lfs-recurring-badge">
                                    <span class="dashicons dashicons-update"></span>
                                    <?php 
                                    switch($reward['recurring_frequency']) {
                                        case 'daily':
                                            _e('Daglig', 'life-freedom-system');
                                            break;
                                        case 'weekly':
                                            _e('Veckovis', 'life-freedom-system');
                                            break;
                                        case 'monthly':
                                            _e('Månadsvis', 'life-freedom-system');
                                            break;
                                    }
                                    ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($reward['content']): ?>
                                <p class="lfs-reward-description"><?php echo wp_kses_post($reward['content']); ?></p>
                            <?php endif; ?>
                            
                            <div class="lfs-reward-meta">
                                <div class="lfs-reward-cost">
                                    <?php if ($reward['cost'] > 0): ?>
                                        <strong><?php echo number_format($reward['cost'], 0, ',', ' '); ?> kr</strong>
                                    <?php else: ?>
                                        <strong class="lfs-free"><?php _e('Gratis!', 'life-freedom-system'); ?></strong>
                                    <?php endif; ?>
                                </div>
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
                            
                            <?php if (!isset($reward['status']) || $reward['status'] !== 'redeemed'): ?>
                                <button class="button button-primary lfs-redeem-btn" data-reward-id="<?php echo $reward['id']; ?>">
                                    <span class="dashicons dashicons-yes"></span>
                                    <?php _e('Lös in nu!', 'life-freedom-system'); ?>
                                </button>
                            <?php else: ?>
                                <button class="button lfs-locked-btn" disabled>
                                    <span class="dashicons dashicons-lock"></span>
                                    <?php _e('Inlöst', 'life-freedom-system'); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="lfs-no-rewards">
                <p><?php _e('Inga tillgängliga belöningar just nu. Fortsätt samla poäng!', 'life-freedom-system'); ?></p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- VÄNTANDE BELÖNINGAR - Motiverande mål -->
    <div class="lfs-rewards-section">
        <h2>
            <span class="dashicons dashicons-flag" style="color: #f39c12;"></span>
            <?php _e('Målbelöningar', 'life-freedom-system'); ?>
            <span class="lfs-section-count">(<?php echo count($grouped_rewards['pending']); ?>)</span>
        </h2>
        <p class="lfs-section-description"><?php _e('Dessa belöningar jobbar du mot – fortsätt samla poäng!', 'life-freedom-system'); ?></p>
        
        <?php if (!empty($grouped_rewards['pending'])): ?>
            <div class="lfs-rewards-grid">
                <?php foreach ($grouped_rewards['pending'] as $reward): ?>
                    <div class="lfs-reward-card lfs-reward-pending" data-reward-id="<?php echo $reward['id']; ?>">
                        <?php if ($reward['thumbnail']): ?>
                            <div class="lfs-reward-image lfs-reward-locked">
                                <img src="<?php echo esc_url($reward['thumbnail']); ?>" alt="<?php echo esc_attr($reward['title']); ?>">
                                <div class="lfs-locked-overlay">
                                    <span class="dashicons dashicons-lock"></span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="lfs-reward-content">
                            <h3><?php echo esc_html($reward['title']); ?></h3>
                            
                            <?php if ($reward['is_recurring']): ?>
                                <span class="lfs-recurring-badge">
                                    <span class="dashicons dashicons-update"></span>
                                    <?php 
                                    switch($reward['recurring_frequency']) {
                                        case 'daily':
                                            _e('Daglig', 'life-freedom-system');
                                            break;
                                        case 'weekly':
                                            _e('Veckovis', 'life-freedom-system');
                                            break;
                                        case 'monthly':
                                            _e('Månadsvis', 'life-freedom-system');
                                            break;
                                    }
                                    ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($reward['content']): ?>
                                <p class="lfs-reward-description"><?php echo wp_kses_post($reward['content']); ?></p>
                            <?php endif; ?>
                            
                            <div class="lfs-reward-meta">
                                <div class="lfs-reward-cost">
                                    <?php if ($reward['cost'] > 0): ?>
                                        <strong><?php echo number_format($reward['cost'], 0, ',', ' '); ?> kr</strong>
                                    <?php else: ?>
                                        <strong class="lfs-free"><?php _e('Gratis!', 'life-freedom-system'); ?></strong>
                                    <?php endif; ?>
                                </div>
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
                            
                            <button class="button lfs-locked-btn" disabled>
                                <span class="dashicons dashicons-lock"></span>
                                <?php _e('Fortsätt samla poäng', 'life-freedom-system'); ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="lfs-no-rewards">
                <p><?php _e('Du har råd med alla belöningar! Fantastiskt jobbat! 🎉', 'life-freedom-system'); ?></p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- HISTORIK - Inlösta belöningar -->
    <div class="lfs-rewards-history">
        <h2>
            <span class="dashicons dashicons-archive"></span>
            <?php _e('Historik', 'life-freedom-system'); ?>
        </h2>
        
        <?php if (!empty($redeemed_data['rewards'])): ?>
            <div class="lfs-total-spent">
                <strong><?php _e('Totalt spenderat:', 'life-freedom-system'); ?></strong>
                <?php echo number_format($redeemed_data['total_spent'], 0, ',', ' '); ?> kr
            </div>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Belöning', 'life-freedom-system'); ?></th>
                        <th><?php _e('Typ', 'life-freedom-system'); ?></th>
                        <th><?php _e('Kostnad', 'life-freedom-system'); ?></th>
                        <th><?php _e('Datum', 'life-freedom-system'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($redeemed_data['rewards'] as $reward): ?>
                        <tr>
                            <td><strong><?php echo esc_html($reward['title']); ?></strong></td>
                            <td>
                                <?php if ($reward['is_recurring']): ?>
                                    <span class="lfs-badge" style="background: #3498db;">
                                        <span class="dashicons dashicons-update" style="font-size: 12px; width: 12px; height: 12px;"></span>
                                        <?php _e('Återkommande', 'life-freedom-system'); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="lfs-badge" style="background: #95a5a6;"><?php _e('Permanent', 'life-freedom-system'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($reward['cost'] > 0): ?>
                                    <?php echo number_format($reward['cost'], 0, ',', ' '); ?> kr
                                <?php else: ?>
                                    <span style="color: #2ecc71;"><?php _e('Gratis', 'life-freedom-system'); ?></span>
                                <?php endif; ?>
                            </td>
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

<style>
.lfs-rewards-page {
    max-width: 1400px;
}

.lfs-balance-overview {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

.lfs-balance-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.lfs-balance-card h3 {
    margin: 0 0 10px;
    font-size: 16px;
    opacity: 0.9;
}

.lfs-balance-amount {
    font-size: 42px;
    font-weight: bold;
}

.lfs-points-summary {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 15px;
}

.lfs-point-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 18px;
}

.lfs-point-label {
    font-weight: bold;
    color: #666;
}

.lfs-point-value {
    font-size: 24px;
    font-weight: bold;
    color: #2c3e50;
}

.lfs-filter-bar { display: flex; flex-wrap: wrap; gap: 8px; margin: 10px 0 25px; }
.lfs-filter-btn {
    padding: 8px 14px;
    background: #fff;
    border: 2px solid #ddd;
    border-radius: 6px;
    text-decoration: none;
    color: #333;
    transition: all 0.2s;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.lfs-filter-btn:hover {
    border-color: #764ba2;
    color: #764ba2;
}
.lfs-filter-btn.active {
    background: #764ba2;
    border-color: #764ba2;
    color: #fff;
}

/* SECTION HEADERS */
.lfs-rewards-section {
    margin-bottom: 40px;
}

.lfs-rewards-section h2 {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 24px;
    margin-bottom: 5px;
}

.lfs-rewards-section h2 .dashicons {
    font-size: 28px;
    width: 28px;
    height: 28px;
}

.lfs-section-count {
    font-size: 18px;
    color: #7f8c8d;
    font-weight: normal;
}

.lfs-section-description {
    color: #7f8c8d;
    margin-bottom: 20px;
    font-size: 14px;
}

/* REWARDS GRID */
.lfs-rewards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.lfs-reward-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}

.lfs-reward-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.lfs-reward-available {
    border: 2px solid #2ecc71;
}

.lfs-reward-pending {
    border: 2px solid #f39c12;
    opacity: 0.8;
}

.lfs-reward-image {
    width: 100%;
    height: 200px;
    overflow: hidden;
    position: relative;
}

.lfs-reward-image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.lfs-reward-locked .lfs-locked-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.lfs-reward-locked .lfs-locked-overlay .dashicons {
    font-size: 48px;
    width: 48px;
    height: 48px;
    color: #fff;
}

.lfs-reward-content {
    padding: 20px;
}

.lfs-reward-content h3 {
    margin: 0 0 10px;
    font-size: 18px;
}

.lfs-recurring-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #3498db;
    color: #fff;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 10px;
}

.lfs-recurring-badge .dashicons {
    font-size: 14px;
    width: 14px;
    height: 14px;
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
    margin-bottom: 15px;
}

.lfs-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    background: #ecf0f1;
    color: #2c3e50;
}

.lfs-badge-fp {
    background: #e74c3c;
    color: #fff;
}

.lfs-badge-bp {
    background: #3498db;
    color: #fff;
}

.lfs-badge-sp {
    background: #2ecc71;
    color: #fff;
}

.lfs-redeem-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    font-size: 16px;
    padding: 10px;
}

.lfs-redeem-btn .dashicons {
    font-size: 20px;
    width: 20px;
    height: 20px;
}

.lfs-locked-btn {
    width: 100%;
    background: #95a5a6;
    color: #fff;
    border: none;
    cursor: not-allowed;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    font-size: 16px;
    padding: 10px;
}

.lfs-locked-btn .dashicons {
    font-size: 20px;
    width: 20px;
    height: 20px;
}

.lfs-rewards-history {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.lfs-rewards-history h2 {
    display: flex;
    align-items: center;
    gap: 10px;
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
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.spin {
    animation: spin 1s linear infinite;
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

<script type="text/javascript">
console.log('=== LFS Rewards JavaScript Loading ===');

// Definiera globala variabler om de saknas
if (typeof ajaxurl === 'undefined') {
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    console.log('ajaxurl was undefined, now set to:', ajaxurl);
}

if (typeof lfsData === 'undefined') {
    var lfsData = {
        nonce: '<?php echo wp_create_nonce('lfs_nonce'); ?>',
        ajaxurl: ajaxurl
    };
    console.log('lfsData was undefined, now set to:', lfsData);
}

// Huvudfunktion som körs när sidan är klar
(function($) {
    'use strict';
    
    console.log('jQuery version:', $.fn.jquery);
    console.log('Number of redeem buttons:', $('.lfs-redeem-btn').length);
    
    // Vänta tills DOM är redo
    $(document).ready(function() {
        console.log('Document ready!');
        
        // Använd event delegation och förhindra duplicerade klick
        var isProcessing = false;
        
        $(document).off('click', '.lfs-redeem-btn').on('click', '.lfs-redeem-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Förhindra duplicerade klick
            if (isProcessing) {
                console.log('Already processing, ignoring click');
                return false;
            }
            
            console.log('=== REDEEM BUTTON CLICKED ===');
            
            var $btn = $(this);
            var rewardId = $btn.data('reward-id');
            var $card = $btn.closest('.lfs-reward-card');
            var rewardTitle = $card.find('h3').first().text();
            var rewardCostElement = $card.find('.lfs-reward-cost strong').first().text();
            
            console.log('Reward ID:', rewardId);
            console.log('Reward Title:', rewardTitle);
            console.log('Reward Cost:', rewardCostElement);
            
            // TILLFÄLLIGT BORTTAGEN FÖR TESTNING - Bekräftelse
            // Uncomment denna rad när du vill ha bekräftelse tillbaka:
            /*
            if (!confirm('🎁 Är du säker på att du vill lösa in "' + rewardTitle + '"?\n\nKostnad: ' + rewardCostElement)) {
                console.log('User cancelled');
                return false;
            }
            */
            
            console.log('Proceeding with redemption...');
            isProcessing = true;
            
            var originalHtml = $btn.html();
            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Löser in...');
            
            console.log('Sending AJAX to:', ajaxurl);
            console.log('Data:', {
                action: 'lfs_redeem_reward',
                nonce: lfsData.nonce,
                reward_id: rewardId
            });
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'lfs_redeem_reward',
                    nonce: lfsData.nonce,
                    reward_id: rewardId
                },
                success: function(response) {
                    console.log('=== AJAX SUCCESS ===');
                    console.log('Response:', response);
                    
                    isProcessing = false;
                    
                    if (response.success) {
                        // Uppdatera UI
                        if (response.data.current_points) {
                            $('#lfs-current-fp').text(response.data.current_points.fp);
                            $('#lfs-current-bp').text(response.data.current_points.bp);
                            $('#lfs-current-sp').text(response.data.current_points.sp);
                        }
                        
                        if (typeof response.data.reward_balance !== 'undefined') {
                            $('#lfs-reward-balance').text(numberFormat(response.data.reward_balance, 0, ',', ' ') + ' kr');
                        }
                        
                        alert('✅ ' + response.data.message);
                        
                        // Reload sidan
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        console.error('Error in response:', response.data);
                        alert('❌ ' + (response.data || 'Ett fel uppstod'));
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('=== AJAX ERROR ===');
                    console.error('Status:', status);
                    console.error('Error:', error);
                    console.error('Response Text:', xhr.responseText);
                    
                    isProcessing = false;
                    
                    alert('❌ Något gick fel. Se konsolen för detaljer.\n\nFel: ' + error);
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
            
            return false;
        });
        
        // Helper function
        function numberFormat(number, decimals, decPoint, thousandsSep) {
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            var n = !isFinite(+number) ? 0 : +number;
            var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
            var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep;
            var dec = (typeof decPoint === 'undefined') ? '.' : decPoint;
            var s = '';
            var toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }
    });
    
})(jQuery);

console.log('=== LFS Rewards JavaScript Loaded Successfully ===');
</script>