<?php
/**
 * Financial View
 * 
 * File location: admin/views/financial.php
 */

if (!defined('ABSPATH')) {
    exit;
}

$financial = LFS_Financial::get_instance();

$account_balances = $financial->get_account_balances();
$monthly_summary = $financial->get_monthly_summary();
$leak_check = $financial->check_for_leaks(30);

$current_month = date('m');
$current_year = date('Y');
?>

<div class="wrap lfs-financial-page">
    <h1><?php _e('Ekonomi', 'life-freedom-system'); ?></h1>
    
    <!-- Account Balances -->
    <div class="lfs-accounts-section">
        <h2><?php _e('Kontostatus', 'life-freedom-system'); ?></h2>
        
        <div class="lfs-accounts-grid">
            <?php foreach ($account_balances as $account): ?>
                <div class="lfs-account-card">
                    <h3><?php echo esc_html($account['name']); ?></h3>
                    <div class="lfs-account-balance <?php echo $account['balance'] < 0 ? 'lfs-negative' : ''; ?>">
                        <?php echo number_format($account['balance'], 0, ',', ' '); ?> kr
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($leak_check['has_leaks']): ?>
            <div class="lfs-warning-box">
                <span class="dashicons dashicons-warning"></span>
                <strong><?php _e('Varning!', 'life-freedom-system'); ?></strong>
                <?php printf(
                    __('Du har haft %d "läckor" (flyttat pengar från sparkonton) de senaste %d dagarna.', 'life-freedom-system'),
                    $leak_check['leak_count'],
                    $leak_check['days_checked']
                ); ?>
            </div>
        <?php else: ?>
            <div class="lfs-success-box">
                <span class="dashicons dashicons-yes-alt"></span>
                <strong><?php _e('Bra jobbat!', 'life-freedom-system'); ?></strong>
                <?php printf(
                    __('Inga läckor de senaste %d dagarna. Din budget håller!', 'life-freedom-system'),
                    $leak_check['days_checked']
                ); ?>
                <?php
                $days_since_leak = get_option('lfs_days_since_leak', 0);
                if ($days_since_leak > 0) {
                    echo ' ' . sprintf(__('Det är %d dagar sedan senaste läckan.', 'life-freedom-system'), $days_since_leak);
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Monthly Summary -->
    <div class="lfs-monthly-section">
        <div class="lfs-section-header">
            <h2><?php _e('Månadsöversikt', 'life-freedom-system'); ?></h2>
            <div class="lfs-month-selector">
                <select id="lfsMonthSelect">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo $m; ?>" <?php selected($m, $current_month); ?>>
                            <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <select id="lfsYearSelect">
                    <?php for ($y = date('Y'); $y >= date('Y') - 2; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php selected($y, $current_year); ?>>
                            <?php echo $y; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        
        <div class="lfs-summary-grid">
            <div class="lfs-summary-card lfs-income-card">
                <h3><?php _e('Total inkomst', 'life-freedom-system'); ?></h3>
                <div class="lfs-summary-amount">
                    <?php echo number_format($monthly_summary['total_income'], 0, ',', ' '); ?> kr
                </div>
                <div class="lfs-summary-breakdown">
                    <div>
                        <span><?php _e('Heltidsjobb:', 'life-freedom-system'); ?></span>
                        <strong><?php echo number_format($monthly_summary['salary_income'], 0, ',', ' '); ?> kr</strong>
                    </div>
                    <div>
                        <span><?php _e('Egna projekt:', 'life-freedom-system'); ?></span>
                        <strong><?php echo number_format($monthly_summary['project_income'], 0, ',', ' '); ?> kr</strong>
                    </div>
                </div>
            </div>
            
            <div class="lfs-summary-card lfs-expenses-card">
                <h3><?php _e('Utgifter', 'life-freedom-system'); ?></h3>
                <div class="lfs-summary-amount">
                    <?php echo number_format($monthly_summary['total_expenses'], 0, ',', ' '); ?> kr
                </div>
            </div>
            
            <div class="lfs-summary-card lfs-savings-card">
                <h3><?php _e('Sparande', 'life-freedom-system'); ?></h3>
                <div class="lfs-summary-amount">
                    <?php echo number_format($monthly_summary['total_savings'], 0, ',', ' '); ?> kr
                </div>
            </div>
            
            <div class="lfs-summary-card lfs-reward-card">
                <h3><?php _e('Till belöningskonto', 'life-freedom-system'); ?></h3>
                <div class="lfs-summary-amount">
                    <?php echo number_format($monthly_summary['to_reward_account'], 0, ',', ' '); ?> kr
                </div>
            </div>
        </div>
        
        <div class="lfs-budget-status">
            <?php if ($monthly_summary['budget_followed']): ?>
                <div class="lfs-success-box">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <strong><?php _e('Budget följd denna månad!', 'life-freedom-system'); ?></strong>
                    <?php _e('Du har inte flyttat pengar från sparkonton.', 'life-freedom-system'); ?>
                    <span class="lfs-sp-earned">+50 SP Bonus!</span>
                </div>
            <?php else: ?>
                <div class="lfs-warning-box">
                    <span class="dashicons dashicons-warning"></span>
                    <strong><?php _e('Budget ej följd', 'life-freedom-system'); ?></strong>
                    <?php printf(
                        __('Antal läckor: %d', 'life-freedom-system'),
                        $monthly_summary['leaks_count']
                    ); ?>
                </div>
            <?php endif; ?>
            
            <div class="lfs-sp-summary">
                <strong><?php _e('SP intjänade från ekonomi:', 'life-freedom-system'); ?></strong>
                <span class="lfs-sp-badge"><?php echo $monthly_summary['sp_earned']; ?> SP</span>
            </div>
        </div>
    </div>
    
    <!-- Quick Transaction -->
    <div class="lfs-transaction-section">
        <h2><?php _e('Lägg till transaktion', 'life-freedom-system'); ?></h2>
        
        <form id="lfsTransactionForm" class="lfs-transaction-form">
            <div class="lfs-form-row">
                <div class="lfs-form-group">
                    <label><?php _e('Titel', 'life-freedom-system'); ?></label>
                    <input type="text" name="title" required>
                </div>
                
                <div class="lfs-form-group">
                    <label><?php _e('Belopp (kr)', 'life-freedom-system'); ?></label>
                    <input type="number" name="amount" step="1" required>
                </div>
                
                <div class="lfs-form-group">
                    <label><?php _e('Datum', 'life-freedom-system'); ?></label>
                    <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            
            <div class="lfs-form-row">
                <div class="lfs-form-group">
                    <label><?php _e('Kategori', 'life-freedom-system'); ?></label>
                    <select name="category" required>
                        <option value=""><?php _e('Välj kategori', 'life-freedom-system'); ?></option>
                        <option value="salary"><?php _e('Lön (heltidsjobb)', 'life-freedom-system'); ?></option>
                        <option value="project_income"><?php _e('Inkomst eget projekt', 'life-freedom-system'); ?></option>
                        <option value="expense"><?php _e('Utgift', 'life-freedom-system'); ?></option>
                        <option value="transfer"><?php _e('Överföring', 'life-freedom-system'); ?></option>
                        <option value="savings"><?php _e('Sparande', 'life-freedom-system'); ?></option>
                    </select>
                </div>
                
                <div class="lfs-form-group">
                    <label><?php _e('Från konto', 'life-freedom-system'); ?></label>
                    <select name="from_account">
                        <option value=""><?php _e('Välj konto', 'life-freedom-system'); ?></option>
                        <?php foreach ($account_balances as $account): ?>
                            <option value="<?php echo esc_attr($account['id']); ?>">
                                <?php echo esc_html($account['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="lfs-form-group">
                    <label><?php _e('Till konto', 'life-freedom-system'); ?></label>
                    <select name="to_account">
                        <option value=""><?php _e('Välj konto', 'life-freedom-system'); ?></option>
                        <?php foreach ($account_balances as $account): ?>
                            <option value="<?php echo esc_attr($account['id']); ?>">
                                <?php echo esc_html($account['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="lfs-form-row">
                <div class="lfs-form-group">
                    <label>
                        <input type="checkbox" name="budget_followed" value="1" checked>
                        <?php _e('Budget följd (ingen läcka)', 'life-freedom-system'); ?>
                    </label>
                </div>
            </div>
            
            <button type="submit" class="button button-primary button-large">
                <?php _e('Lägg till transaktion', 'life-freedom-system'); ?>
            </button>
        </form>
    </div>
    
    <!-- Recent Transactions -->
    <div class="lfs-transactions-list">
        <h2><?php _e('Senaste transaktioner', 'life-freedom-system'); ?></h2>
        
        <?php
        $recent_transactions = get_posts(array(
            'post_type' => 'lfs_transaction',
            'posts_per_page' => 20,
            'orderby' => 'meta_value',
            'meta_key' => 'lfs_transaction_date',
            'order' => 'DESC',
        ));
        ?>
        
        <?php if ($recent_transactions): ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Datum', 'life-freedom-system'); ?></th>
                        <th><?php _e('Titel', 'life-freedom-system'); ?></th>
                        <th><?php _e('Kategori', 'life-freedom-system'); ?></th>
                        <th><?php _e('Belopp', 'life-freedom-system'); ?></th>
                        <th><?php _e('SP', 'life-freedom-system'); ?></th>
                        <th><?php _e('Budget följd', 'life-freedom-system'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_transactions as $transaction): ?>
                        <?php
                        $amount = get_post_meta($transaction->ID, 'lfs_transaction_amount', true);
                        $date = get_post_meta($transaction->ID, 'lfs_transaction_date', true);
                        $category = get_post_meta($transaction->ID, 'lfs_transaction_category', true);
                        $sp = get_post_meta($transaction->ID, 'lfs_transaction_sp', true);
                        $budget_followed = get_post_meta($transaction->ID, 'lfs_transaction_budget_followed', true);
                        ?>
                        <tr>
                            <td><?php echo esc_html($date); ?></td>
                            <td><strong><?php echo esc_html($transaction->post_title); ?></strong></td>
                            <td><?php echo esc_html($category); ?></td>
                            <td><?php echo number_format($amount, 0, ',', ' '); ?> kr</td>
                            <td><span class="lfs-badge lfs-badge-sp"><?php echo esc_html($sp); ?> SP</span></td>
                            <td>
                                <?php if ($budget_followed): ?>
                                    <span class="dashicons dashicons-yes-alt" style="color: #2ecc71;"></span>
                                <?php else: ?>
                                    <span class="dashicons dashicons-no-alt" style="color: #e74c3c;"></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p><?php _e('Inga transaktioner ännu.', 'life-freedom-system'); ?></p>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Transaction form submission
    $('#lfsTransactionForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'lfs_create_transaction',
            nonce: lfsData.nonce,
            title: $(this).find('[name="title"]').val(),
            amount: $(this).find('[name="amount"]').val(),
            date: $(this).find('[name="date"]').val(),
            category: $(this).find('[name="category"]').val(),
            from_account: $(this).find('[name="from_account"]').val(),
            to_account: $(this).find('[name="to_account"]').val(),
            budget_followed: $(this).find('[name="budget_followed"]').is(':checked')
        };
        
        $.post(lfsData.ajaxUrl, formData, function(response) {
            if (response.success) {
                alert(response.data.message);
                location.reload();
            } else {
                alert('Fel: ' + response.data);
            }
        });
    });
    
    // Month/Year selector
    $('#lfsMonthSelect, #lfsYearSelect').on('change', function() {
        var month = $('#lfsMonthSelect').val();
        var year = $('#lfsYearSelect').val();
        
        $.post(lfsData.ajaxUrl, {
            action: 'lfs_get_monthly_summary',
            nonce: lfsData.nonce,
            month: month,
            year: year
        }, function(response) {
            if (response.success) {
                // Update summary display
                location.reload(); // Simplified - could update DOM instead
            }
        });
    });
});
</script>

<style>
.lfs-accounts-section,
.lfs-monthly-section,
.lfs-transaction-section,
.lfs-transactions-list {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.lfs-accounts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.lfs-account-card {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    border: 2px solid #dee2e6;
}

.lfs-account-card h3 {
    margin: 0 0 10px;
    font-size: 14px;
    color: #666;
}

.lfs-account-balance {
    font-size: 24px;
    font-weight: 700;
    color: #2ecc71;
}

.lfs-account-balance.lfs-negative {
    color: #e74c3c;
}

.lfs-warning-box,
.lfs-success-box {
    padding: 15px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 20px;
}

.lfs-warning-box {
    background: #fff3cd;
    border: 2px solid #ffc107;
    color: #856404;
}

.lfs-success-box {
    background: #d4edda;
    border: 2px solid #28a745;
    color: #155724;
}

.lfs-warning-box .dashicons,
.lfs-success-box .dashicons {
    font-size: 24px;
    width: 24px;
    height: 24px;
}

.lfs-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.lfs-month-selector {
    display: flex;
    gap: 10px;
}

.lfs-month-selector select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.lfs-summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.lfs-summary-card {
    padding: 20px;
    border-radius: 8px;
    border: 2px solid;
}

.lfs-income-card {
    border-color: #2ecc71;
    background: rgba(46, 204, 113, 0.05);
}

.lfs-expenses-card {
    border-color: #e74c3c;
    background: rgba(231, 76, 60, 0.05);
}

.lfs-savings-card {
    border-color: #3498db;
    background: rgba(52, 152, 219, 0.05);
}

.lfs-reward-card {
    border-color: #9b59b6;
    background: rgba(155, 89, 182, 0.05);
}

.lfs-summary-card h3 {
    margin: 0 0 10px;
    font-size: 14px;
    color: #666;
}

.lfs-summary-amount {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 10px;
}

.lfs-income-card .lfs-summary-amount {
    color: #2ecc71;
}

.lfs-expenses-card .lfs-summary-amount {
    color: #e74c3c;
}

.lfs-savings-card .lfs-summary-amount {
    color: #3498db;
}

.lfs-reward-card .lfs-summary-amount {
    color: #9b59b6;
}

.lfs-summary-breakdown {
    font-size: 13px;
    color: #666;
}

.lfs-summary-breakdown > div {
    display: flex;
    justify-content: space-between;
    margin: 5px 0;
}

.lfs-budget-status {
    margin-top: 20px;
}

.lfs-sp-summary {
    margin-top: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.lfs-sp-badge {
    background: rgba(243, 156, 18, 0.1);
    color: #f39c12;
    padding: 5px 10px;
    border-radius: 4px;
    font-weight: 700;
}

.lfs-sp-earned {
    color: #f39c12;
    font-weight: 700;
    margin-left: 10px;
}

.lfs-transaction-form {
    max-width: 800px;
}

.lfs-form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 15px;
}

.lfs-form-group {
    display: flex;
    flex-direction: column;
}

.lfs-form-group label {
    font-weight: 600;
    margin-bottom: 5px;
    color: #333;
}

.lfs-form-group input,
.lfs-form-group select {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.lfs-transactions-list table {
    margin-top: 15px;
}

@media (max-width: 768px) {
    .lfs-accounts-grid,
    .lfs-summary-grid {
        grid-template-columns: 1fr;
    }
    
    .lfs-section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
}
</style>