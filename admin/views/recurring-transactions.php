<?php
/**
 * Recurring Transactions View
 * 
 * File location: admin/views/recurring-transactions.php
 */

if (!defined('ABSPATH')) {
    exit;
}

$recurring = LFS_Recurring_Transactions::get_instance();
$financial = LFS_Financial::get_instance();

$upcoming = $recurring->get_upcoming_transactions(60); // Next 60 days
$active_recurring = $recurring->get_active_recurring_transactions();
$account_balances = $financial->get_account_balances();
?>

<div class="wrap lfs-recurring-page">
    <h1><?php _e('Återkommande transaktioner', 'life-freedom-system'); ?></h1>
    
    <!-- Upcoming Transactions (Next 60 days) -->
    <div class="lfs-upcoming-section">
        <h2><?php _e('Kommande betalningar (60 dagar)', 'life-freedom-system'); ?></h2>
        
        <?php if (!empty($upcoming)): ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Förfaller', 'life-freedom-system'); ?></th>
                        <th><?php _e('Titel', 'life-freedom-system'); ?></th>
                        <th><?php _e('Från konto', 'life-freedom-system'); ?></th>
                        <th><?php _e('Till konto', 'life-freedom-system'); ?></th>
                        <th><?php _e('Belopp', 'life-freedom-system'); ?></th>
                        <th><?php _e('Frekvens', 'life-freedom-system'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($upcoming as $transaction): ?>
                        <tr class="<?php echo $transaction['days_until'] <= 3 ? 'lfs-urgent' : ''; ?>">
                            <td>
                                <strong><?php echo esc_html($transaction['due_date']); ?></strong>
                                <br>
                                <span class="lfs-days-until">
                                    <?php 
                                    if ($transaction['days_until'] == 0) {
                                        _e('Idag', 'life-freedom-system');
                                    } elseif ($transaction['days_until'] == 1) {
                                        _e('Imorgon', 'life-freedom-system');
                                    } else {
                                        printf(__('Om %d dagar', 'life-freedom-system'), $transaction['days_until']);
                                    }
                                    ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo get_edit_post_link($transaction['id']); ?>">
                                    <?php echo esc_html($transaction['title']); ?>
                                </a>
                            </td>
                            <td><?php echo esc_html($transaction['from_account']); ?></td>
                            <td><?php echo esc_html($transaction['to_account']); ?></td>
                            <td>
                                <strong><?php echo number_format($transaction['amount'], 0, ',', ' '); ?> kr</strong>
                            </td>
                            <td>
                                <?php
                                $frequency_labels = array(
                                    'weekly' => __('Veckovis', 'life-freedom-system'),
                                    'monthly' => __('Månadsvis', 'life-freedom-system'),
                                    'quarterly' => __('Kvartalsvis', 'life-freedom-system'),
                                    'yearly' => __('Årsvis', 'life-freedom-system'),
                                );
                                echo isset($frequency_labels[$transaction['frequency']]) 
                                    ? $frequency_labels[$transaction['frequency']] 
                                    : ucfirst($transaction['frequency']);
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"><strong><?php _e('Total kommande utgifter (60 dagar):', 'life-freedom-system'); ?></strong></td>
                        <td colspan="2">
                            <strong class="lfs-total-upcoming">
                                <?php 
                                $total = array_sum(array_column($upcoming, 'amount'));
                                echo number_format($total, 0, ',', ' '); 
                                ?> kr
                            </strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        <?php else: ?>
            <p><?php _e('Inga kommande transaktioner.', 'life-freedom-system'); ?></p>
        <?php endif; ?>
    </div>
    
    <!-- Quick Create Form -->
    <div class="lfs-create-recurring-section">
        <h2><?php _e('Skapa ny återkommande transaktion', 'life-freedom-system'); ?></h2>
        
        <form id="lfsRecurringForm" class="lfs-recurring-form">
            <div class="lfs-form-row">
                <div class="lfs-form-group">
                    <label><?php _e('Titel', 'life-freedom-system'); ?> *</label>
                    <input type="text" name="title" required placeholder="t.ex. Hyra, Försäkring">
                </div>
                
                <div class="lfs-form-group">
                    <label><?php _e('Belopp (kr)', 'life-freedom-system'); ?> *</label>
                    <input type="number" name="amount" step="1" min="0" required>
                </div>
            </div>
            
            <div class="lfs-form-row">
                <div class="lfs-form-group">
                    <label><?php _e('Kategori', 'life-freedom-system'); ?> *</label>
                    <select name="category" required>
                        <option value="expense"><?php _e('Utgift', 'life-freedom-system'); ?></option>
                        <option value="transfer"><?php _e('Överföring', 'life-freedom-system'); ?></option>
                        <option value="savings"><?php _e('Sparande', 'life-freedom-system'); ?></option>
                        <option value="income"><?php _e('Inkomst', 'life-freedom-system'); ?></option>
                    </select>
                </div>
                
                <div class="lfs-form-group">
                    <label><?php _e('Frekvens', 'life-freedom-system'); ?> *</label>
                    <select name="frequency" id="frequencySelect" required>
                        <option value="weekly"><?php _e('Veckovis', 'life-freedom-system'); ?></option>
                        <option value="monthly" selected><?php _e('Månadsvis', 'life-freedom-system'); ?></option>
                        <option value="quarterly"><?php _e('Kvartalsvis', 'life-freedom-system'); ?></option>
                        <option value="yearly"><?php _e('Årsvis', 'life-freedom-system'); ?></option>
                        <option value="custom"><?php _e('Anpassad', 'life-freedom-system'); ?></option>
                    </select>
                </div>
            </div>
            
            <div class="lfs-form-row" id="customIntervalRow" style="display: none;">
                <div class="lfs-form-group">
                    <label><?php _e('Anpassat intervall (dagar)', 'life-freedom-system'); ?></label>
                    <input type="number" name="custom_interval" min="1" step="1" placeholder="t.ex. 14">
                </div>
            </div>
            
            <div class="lfs-form-row">
                <div class="lfs-form-group">
                    <label><?php _e('Från konto', 'life-freedom-system'); ?> *</label>
                    <select name="from_account" required>
                        <option value=""><?php _e('Välj konto', 'life-freedom-system'); ?></option>
                        <?php foreach ($account_balances as $account): ?>
                            <option value="<?php echo esc_attr($account['id']); ?>">
                                <?php echo esc_html($account['name']); ?> 
                                (<?php echo number_format($account['balance'], 0, ',', ' '); ?> kr)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="lfs-form-group">
                    <label><?php _e('Till konto', 'life-freedom-system'); ?></label>
                    <select name="to_account">
                        <option value=""><?php _e('Inget (utgift)', 'life-freedom-system'); ?></option>
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
                    <label><?php _e('Startdatum', 'life-freedom-system'); ?> *</label>
                    <input type="date" name="start_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="lfs-form-group">
                    <label><?php _e('Beskrivning', 'life-freedom-system'); ?></label>
                    <textarea name="description" rows="2" placeholder="Valfri anteckning..."></textarea>
                </div>
            </div>
            
            <button type="submit" class="button button-primary button-large">
                <span class="dashicons dashicons-update"></span>
                <?php _e('Skapa återkommande transaktion', 'life-freedom-system'); ?>
            </button>
        </form>
    </div>
    
    <!-- All Active Recurring Transactions -->
    <div class="lfs-all-recurring-section">
        <h2><?php _e('Alla aktiva återkommande transaktioner', 'life-freedom-system'); ?></h2>
        
        <?php if (!empty($active_recurring)): ?>
            <div class="lfs-recurring-grid">
                <?php foreach ($active_recurring as $recurring): ?>
                    <?php
                    $amount = floatval(get_post_meta($recurring->ID, 'lfs_recurring_amount', true));
                    $frequency = get_post_meta($recurring->ID, 'lfs_recurring_frequency', true);
                    $next_due = get_post_meta($recurring->ID, 'lfs_recurring_next_due', true);
                    $category = get_post_meta($recurring->ID, 'lfs_recurring_category', true);
                    $count = intval(get_post_meta($recurring->ID, 'lfs_recurring_generated_count', true));
                    $from_account_id = get_post_meta($recurring->ID, 'lfs_recurring_from_account', true);
                    $to_account_id = get_post_meta($recurring->ID, 'lfs_recurring_to_account', true);
                    
                    $from_account = $from_account_id ? get_term($from_account_id, 'lfs_account') : null;
                    $to_account = $to_account_id ? get_term($to_account_id, 'lfs_account') : null;
                    
                    $frequency_labels = array(
                        'weekly' => __('Veckovis', 'life-freedom-system'),
                        'monthly' => __('Månadsvis', 'life-freedom-system'),
                        'quarterly' => __('Kvartalsvis', 'life-freedom-system'),
                        'yearly' => __('Årsvis', 'life-freedom-system'),
                    );
                    ?>
                    <div class="lfs-recurring-card">
                        <div class="lfs-recurring-header">
                            <h3>
                                <a href="<?php echo get_edit_post_link($recurring->ID); ?>">
                                    <?php echo esc_html($recurring->post_title); ?>
                                </a>
                            </h3>
                            <span class="lfs-recurring-category lfs-category-<?php echo esc_attr($category); ?>">
                                <?php echo ucfirst($category); ?>
                            </span>
                        </div>
                        
                        <div class="lfs-recurring-amount">
                            <?php echo number_format($amount, 0, ',', ' '); ?> kr
                        </div>
                        
                        <div class="lfs-recurring-details">
                            <div class="lfs-detail-row">
                                <span class="dashicons dashicons-calendar"></span>
                                <strong><?php _e('Nästa:', 'life-freedom-system'); ?></strong>
                                <?php echo esc_html($next_due); ?>
                            </div>
                            
                            <div class="lfs-detail-row">
                                <span class="dashicons dashicons-backup"></span>
                                <strong><?php _e('Frekvens:', 'life-freedom-system'); ?></strong>
                                <?php echo isset($frequency_labels[$frequency]) ? $frequency_labels[$frequency] : ucfirst($frequency); ?>
                            </div>
                            
                            <div class="lfs-detail-row">
                                <span class="dashicons dashicons-arrow-right-alt"></span>
                                <strong><?php _e('Från/Till:', 'life-freedom-system'); ?></strong>
                                <?php 
                                echo $from_account ? esc_html($from_account->name) : '-';
                                echo ' → ';
                                echo $to_account ? esc_html($to_account->name) : __('Utgift', 'life-freedom-system');
                                ?>
                            </div>
                            
                            <div class="lfs-detail-row">
                                <span class="dashicons dashicons-saved"></span>
                                <strong><?php _e('Genererade:', 'life-freedom-system'); ?></strong>
                                <?php echo $count; ?> <?php _e('gånger', 'life-freedom-system'); ?>
                            </div>
                        </div>
                        
                        <div class="lfs-recurring-actions">
                            <button class="button lfs-generate-now" data-id="<?php echo $recurring->ID; ?>" title="Skapa transaktion nu (test)">
                                <span class="dashicons dashicons-controls-play"></span>
                                <?php _e('Generera Nu', 'life-freedom-system'); ?>
                            </button>
                            <button class="button lfs-toggle-recurring" data-id="<?php echo $recurring->ID; ?>" data-active="0">
                                <span class="dashicons dashicons-controls-pause"></span>
                                <?php _e('Pausa', 'life-freedom-system'); ?>
                            </button>
                            <a href="<?php echo get_edit_post_link($recurring->ID); ?>" class="button">
                                <span class="dashicons dashicons-edit"></span>
                                <?php _e('Redigera', 'life-freedom-system'); ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p><?php _e('Inga återkommande transaktioner ännu. Skapa din första ovan!', 'life-freedom-system'); ?></p>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
/**
 * LÄGG TILL DETTA I assets/js/admin.js
 * 
 * Eller ersätt hela <script>-blocket i recurring-transactions.php
 */

jQuery(document).ready(function($) {
    console.log('=== LFS Admin JS Loaded ===');
    
    // ==========================================
    // RECURRING TRANSACTIONS PAGE
    // ==========================================
    
    if ($('.lfs-recurring-page').length) {
        console.log('Recurring page detected');
        
        // Show/hide custom interval field
        $('#frequencySelect').on('change', function() {
            if ($(this).val() === 'custom') {
                $('#customIntervalRow').show();
            } else {
                $('#customIntervalRow').hide();
            }
        });
        
        // Create recurring transaction
        $('#lfsRecurringForm').on('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted');
            
            var formData = {
                action: 'lfs_create_recurring_transaction',
                nonce: lfsData.nonce,
                title: $(this).find('[name="title"]').val(),
                amount: $(this).find('[name="amount"]').val(),
                category: $(this).find('[name="category"]').val(),
                frequency: $(this).find('[name="frequency"]').val(),
                custom_interval: $(this).find('[name="custom_interval"]').val(),
                from_account: $(this).find('[name="from_account"]').val(),
                to_account: $(this).find('[name="to_account"]').val(),
                start_date: $(this).find('[name="start_date"]').val(),
                description: $(this).find('[name="description"]').val(),
            };
            
            console.log('Sending:', formData);
            
            $.post(lfsData.ajaxUrl, formData)
                .done(function(response) {
                    console.log('Response:', response);
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert('Fel: ' + response.data);
                    }
                })
                .fail(function(xhr, status, error) {
                    console.error('Failed:', error);
                    alert('AJAX Error: ' + error);
                });
        });
        
        // Generate transaction NOW
        $(document).on('click', '.lfs-generate-now', function(e) {
            e.preventDefault();
            console.log('=== GENERATE CLICKED ===');
            
            var button = $(this);
            var recurringId = button.data('id');
            
            console.log('Recurring ID:', recurringId);
            
            if (!recurringId) {
                alert('ERROR: Inget recurring ID!');
                console.error('No recurring ID found');
                return;
            }
            
            if (!confirm('Vill du skapa en transaktion NU från denna mall?\n\n(Detta är för testning)')) {
                console.log('User cancelled');
                return;
            }
            
            var originalHtml = button.html();
            button.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span> Skapar...');
            
            var ajaxData = {
                action: 'lfs_generate_transaction_now',
                nonce: lfsData.nonce,
                recurring_id: recurringId
            };
            
            console.log('Sending AJAX:', ajaxData);
            
            $.post(lfsData.ajaxUrl, ajaxData)
                .done(function(response) {
                    console.log('Generate response:', response);
                    
                    if (response.success) {
                        alert('✅ Transaktion skapad!\n\nID: ' + response.data.transaction_id + '\n\nGå till Ekonomi för att se den.');
                        location.reload();
                    } else {
                        alert('❌ Fel: ' + response.data);
                        button.prop('disabled', false).html(originalHtml);
                    }
                })
                .fail(function(xhr, status, error) {
                    console.error('Generate failed:', {
                        error: error,
                        status: status,
                        response: xhr.responseText
                    });
                    alert('AJAX Error: ' + error);
                    button.prop('disabled', false).html(originalHtml);
                });
        });
        
        // TOGGLE recurring transaction
        $(document).on('click', '.lfs-toggle-recurring', function(e) {
            e.preventDefault();
            console.log('=== TOGGLE CLICKED ===');
            
            var button = $(this);
            var recurringId = button.data('id');
            var isActiveStr = button.data('active');
            var isActive = (isActiveStr === 1 || isActiveStr === '1' || isActiveStr === true);
            
            console.log('Button data:', {
                id: recurringId,
                activeRaw: isActiveStr,
                activeParsed: isActive,
                willSetTo: !isActive
            });
            
            if (!recurringId) {
                alert('ERROR: Inget recurring ID hittades!');
                console.error('No recurring ID on button');
                return;
            }
            
            var ajaxData = {
                action: 'lfs_toggle_recurring_transaction',
                nonce: lfsData.nonce,
                recurring_id: recurringId,
                active: !isActive
            };
            
            console.log('Sending AJAX:', ajaxData);
            
            $.post(lfsData.ajaxUrl, ajaxData)
                .done(function(response) {
                    console.log('Toggle response:', response);
                    
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert('❌ Fel: ' + response.data);
                    }
                })
                .fail(function(xhr, status, error) {
                    console.error('Toggle failed:', {
                        error: error,
                        status: status,
                        response: xhr.responseText
                    });
                    alert('AJAX Error: ' + error);
                });
        });
        
        // Debug: Log button info on page load
        console.log('=== BUTTON DEBUG ===');
        console.log('Generate buttons:', $('.lfs-generate-now').length);
        console.log('Toggle buttons:', $('.lfs-toggle-recurring').length);
        
        $('.lfs-toggle-recurring').each(function(i) {
            console.log('Toggle button ' + i + ':', {
                id: $(this).data('id'),
                active: $(this).data('active'),
                text: $(this).text().trim()
            });
        });

        // Test: Attach a simple click handler
        $('.lfs-toggle-recurring').on('click', function() {
            console.log('SIMPLE CLICK DETECTED on toggle button');
        });

        // Test: Click on first button programmatically
        setTimeout(function() {
            console.log('Testing programmatic click...');
            $('.lfs-toggle-recurring').first().trigger('click');
        }, 2000);
    }
    
});
</script>

<style>
.lfs-recurring-page {
    max-width: 1400px;
}

.lfs-upcoming-section,
.lfs-create-recurring-section,
.lfs-all-recurring-section {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.lfs-upcoming-section h2,
.lfs-create-recurring-section h2,
.lfs-all-recurring-section h2 {
    margin-top: 0;
    border-bottom: 2px solid #0073aa;
    padding-bottom: 10px;
}

.lfs-urgent td {
    background: #fff3cd !important;
    border-left: 4px solid #ff9800;
}

.lfs-days-until {
    font-size: 0.9em;
    color: #666;
}

.lfs-total-upcoming {
    font-size: 1.2em;
    color: #0073aa;
}

/* Form Styling */
.lfs-recurring-form .lfs-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 15px;
}

.lfs-form-group {
    display: flex;
    flex-direction: column;
}

.lfs-form-group label {
    font-weight: 600;
    margin-bottom: 5px;
}

.lfs-form-group input,
.lfs-form-group select,
.lfs-form-group textarea {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.lfs-form-group textarea {
    resize: vertical;
}

/* Recurring Cards Grid */
.lfs-recurring-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.lfs-recurring-card {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    transition: all 0.3s ease;
}

.lfs-recurring-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.lfs-recurring-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.lfs-recurring-header h3 {
    margin: 0;
    font-size: 1.1em;
}

.lfs-recurring-category {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: 600;
    text-transform: uppercase;
}

.lfs-category-expense {
    background: #fee;
    color: #c00;
}

.lfs-category-transfer {
    background: #e3f2fd;
    color: #1976d2;
}

.lfs-category-savings {
    background: #e8f5e9;
    color: #2e7d32;
}

.lfs-category-income {
    background: #f3e5f5;
    color: #7b1fa2;
}

.lfs-recurring-amount {
    font-size: 1.8em;
    font-weight: bold;
    color: #0073aa;
    margin-bottom: 15px;
}

.lfs-recurring-details {
    border-top: 1px solid #ddd;
    padding-top: 15px;
    margin-bottom: 15px;
}

.lfs-detail-row {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    font-size: 0.9em;
}

.lfs-detail-row .dashicons {
    color: #666;
    margin-right: 8px;
}

.lfs-recurring-actions {
    display: flex;
    gap: 10px;
    border-top: 1px solid #ddd;
    padding-top: 15px;
}

.lfs-recurring-actions .button {
    flex: 1;
    justify-content: center;
}

@media (max-width: 782px) {
    .lfs-recurring-form .lfs-form-row {
        grid-template-columns: 1fr;
    }
    
    .lfs-recurring-grid {
        grid-template-columns: 1fr;
    }
}
</style>