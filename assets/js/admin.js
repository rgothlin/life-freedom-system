/**
 * Admin JavaScript - KOMPLETT VERSION
 * 
 * File location: assets/js/admin.js
 * Handles all admin interface interactions including reward redemption
 */

(function ($) {
    'use strict';

    $(document).ready(function () {

        // ===============================
        // DASHBOARD FUNCTIONALITY
        // ===============================

        // Quick add activity
        $('#lfs-quick-add-form').on('submit', function (e) {
            e.preventDefault();

            const $form = $(this);
            const $btn = $form.find('button[type="submit"]');
            const originalText = $btn.html();

            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Lägger till...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'lfs_quick_add_activity',
                    nonce: lfsData.nonce,
                    activity_name: $('#lfs-quick-activity-name').val(),
                    fp: $('#lfs-quick-fp').val(),
                    bp: $('#lfs-quick-bp').val(),
                    sp: $('#lfs-quick-sp').val()
                },
                success: function (response) {
                    if (response.success) {
                        // Update dashboard
                        updateDashboard(response.data);

                        // Reset form
                        $form[0].reset();

                        // Show success message
                        showNotification('✅ Aktivitet tillagd!', 'success');
                    } else {
                        showNotification('❌ ' + response.data, 'error');
                    }

                    $btn.prop('disabled', false).html(originalText);
                },
                error: function () {
                    showNotification('❌ Något gick fel. Försök igen.', 'error');
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Log activity template
        $('.lfs-log-template-btn').on('click', function () {
            const $btn = $(this);
            const templateId = $btn.data('template-id');
            const originalText = $btn.html();

            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Loggar...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'lfs_quick_log_template',
                    nonce: lfsData.nonce,
                    template_id: templateId
                },
                success: function (response) {
                    if (response.success) {
                        updateDashboard(response.data);
                        showNotification('✅ Aktivitet loggad!', 'success');
                    } else {
                        showNotification('❌ ' + response.data, 'error');
                    }

                    $btn.prop('disabled', false).html(originalText);
                },
                error: function () {
                    showNotification('❌ Något gick fel. Försök igen.', 'error');
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        });

        // ===============================
        // REWARDS FUNCTIONALITY (NEW!)
        // ===============================

        // Redeem reward button
        $(document).on('click', '.lfs-redeem-btn', function () {
            const $btn = $(this);
            const rewardId = $btn.data('reward-id');
            const $card = $btn.closest('.lfs-reward-card');
            const rewardTitle = $card.find('h3').text();
            const rewardCost = $card.find('.lfs-reward-cost strong').text();

            // Confirmation dialog
            if (!confirm('🎁 Är du säker på att du vill lösa in "' + rewardTitle + '"?\n\nKostnad: ' + rewardCost)) {
                return;
            }

            const originalText = $btn.html();
            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Löser in...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'lfs_redeem_reward',
                    nonce: lfsData.nonce,
                    reward_id: rewardId
                },
                success: function (response) {
                    if (response.success) {
                        // Update points and balance in UI
                        updatePointsDisplay(response.data.current_points, response.data.reward_balance);

                        // Show success message
                        showNotification('🎉 ' + response.data.message, 'success');

                        // Reload page after short delay to show updated rewards
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    } else {
                        showNotification('❌ ' + response.data, 'error');
                        $btn.prop('disabled', false).html(originalText);
                    }
                },
                error: function () {
                    showNotification('❌ Något gick fel. Försök igen.', 'error');
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        });

        // ===============================
        // HELPER FUNCTIONS
        // ===============================

        /**
         * Update dashboard with new data
         */
        function updateDashboard(data) {
            if (data.current_points) {
                $('#lfs-current-fp').text(data.current_points.fp);
                $('#lfs-current-bp').text(data.current_points.bp);
                $('#lfs-current-sp').text(data.current_points.sp);
            }

            if (data.weekly_points) {
                $('#lfs-weekly-fp').text(data.weekly_points.fp);
                $('#lfs-weekly-bp').text(data.weekly_points.bp);
                $('#lfs-weekly-sp').text(data.weekly_points.sp);
            }

            if (data.reward_balance !== undefined) {
                $('#lfs-reward-balance').text(numberFormat(data.reward_balance, 0, ',', ' ') + ' kr');
            }

            // Update progress bars if they exist
            updateProgressBars(data);
        }

        /**
         * Update points display (för rewards-sidan)
         */
        function updatePointsDisplay(points, balance) {
            $('#lfs-current-fp').text(points.fp);
            $('#lfs-current-bp').text(points.bp);
            $('#lfs-current-sp').text(points.sp);
            $('#lfs-reward-balance').text(numberFormat(balance, 0, ',', ' ') + ' kr');
        }

        /**
         * Update progress bars
         */
        function updateProgressBars(data) {
            if (!data.weekly_points || !data.weekly_goals) return;

            // FP Progress
            const fpProgress = (data.weekly_points.fp / data.weekly_goals.fp) * 100;
            $('.lfs-progress-bar.lfs-fp-bar .lfs-progress-fill').css('width', Math.min(fpProgress, 100) + '%');

            // BP Progress
            const bpProgress = (data.weekly_points.bp / data.weekly_goals.bp) * 100;
            $('.lfs-progress-bar.lfs-bp-bar .lfs-progress-fill').css('width', Math.min(bpProgress, 100) + '%');

            // SP Progress
            const spProgress = (data.weekly_points.sp / data.weekly_goals.sp) * 100;
            $('.lfs-progress-bar.lfs-sp-bar .lfs-progress-fill').css('width', Math.min(spProgress, 100) + '%');
        }

        /**
         * Show notification message
         */
        function showNotification(message, type) {
            // Remove existing notifications
            $('.lfs-notification').remove();

            const $notification = $('<div class="lfs-notification lfs-notification-' + type + '">' + message + '</div>');
            $('body').append($notification);

            // Show notification
            setTimeout(function () {
                $notification.addClass('lfs-notification-show');
            }, 10);

            // Hide after 3 seconds
            setTimeout(function () {
                $notification.removeClass('lfs-notification-show');
                setTimeout(function () {
                    $notification.remove();
                }, 300);
            }, 3000);
        }

        /**
         * Number formatting helper
         */
        function numberFormat(number, decimals, decPoint, thousandsSep) {
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            var n = !isFinite(+number) ? 0 : +number;
            var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
            var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep;
            var dec = (typeof decPoint === 'undefined') ? '.' : decPoint;
            var s = '';

            var toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k);
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

        // ===============================
        // DASHBOARD AUTO-REFRESH
        // ===============================

        /**
         * Auto-refresh dashboard data every 30 seconds
         */
        if ($('.lfs-dashboard').length) {
            setInterval(function () {
                refreshDashboard();
            }, 30000); // 30 seconds
        }

        function refreshDashboard() {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'lfs_get_dashboard_data',
                    nonce: lfsData.nonce
                },
                success: function (response) {
                    if (response.success) {
                        updateDashboard(response.data);
                    }
                }
            });
        }

        // ===============================
        // CHARTS (if Chart.js is loaded)
        // ===============================

        /**
         * Initialize charts on dashboard
         */
        if (typeof Chart !== 'undefined' && $('.lfs-chart-canvas').length) {
            initializeCharts();
        }

        function initializeCharts() {
            // Weekly Progress Chart
            const weeklyChartCanvas = document.getElementById('lfs-weekly-chart');
            if (weeklyChartCanvas) {
                const weeklyData = lfsData.weekly_data || [];

                new Chart(weeklyChartCanvas, {
                    type: 'line',
                    data: {
                        labels: weeklyData.labels || [],
                        datasets: [
                            {
                                label: 'FP',
                                data: weeklyData.fp || [],
                                borderColor: '#e74c3c',
                                backgroundColor: 'rgba(231, 76, 60, 0.1)',
                                tension: 0.4
                            },
                            {
                                label: 'BP',
                                data: weeklyData.bp || [],
                                borderColor: '#3498db',
                                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                                tension: 0.4
                            },
                            {
                                label: 'SP',
                                data: weeklyData.sp || [],
                                borderColor: '#2ecc71',
                                backgroundColor: 'rgba(46, 204, 113, 0.1)',
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }

        // ===============================
        // FINANCIAL PAGE
        // ===============================

        /**
         * Handle account balance updates
         */
        $('.lfs-update-balance-btn').on('click', function () {
            const $btn = $(this);
            const accountType = $btn.data('account-type');
            const $input = $('input[name="lfs_' + accountType + '_balance"]');
            const newBalance = $input.val();

            if (!newBalance) {
                showNotification('❌ Ange ett belopp', 'error');
                return;
            }

            const originalText = $btn.html();
            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span>');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'lfs_update_account_balance',
                    nonce: lfsData.nonce,
                    account_type: accountType,
                    balance: newBalance
                },
                success: function (response) {
                    if (response.success) {
                        showNotification('✅ Balans uppdaterad!', 'success');

                        // Update display
                        $('.lfs-' + accountType + '-balance').text(numberFormat(parseFloat(newBalance), 0, ',', ' ') + ' kr');
                    } else {
                        showNotification('❌ ' + response.data, 'error');
                    }

                    $btn.prop('disabled', false).html(originalText);
                },
                error: function () {
                    showNotification('❌ Något gick fel. Försök igen.', 'error');
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        });

        // ===============================
        // SETTINGS PAGE
        // ===============================

        /**
         * Update settings with live preview
         */
        $('input[name^="lfs_weekly_"], select[name="lfs_life_phase"]').on('change', function () {
            const $field = $(this);
            const fieldName = $field.attr('name');
            const fieldValue = $field.val();

            // Show saving indicator
            const $indicator = $('<span class="lfs-saving-indicator"> Sparar...</span>');
            $field.after($indicator);

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'lfs_update_setting',
                    nonce: lfsData.nonce,
                    setting_name: fieldName,
                    setting_value: fieldValue
                },
                success: function (response) {
                    if (response.success) {
                        $indicator.text(' ✓ Sparat!').addClass('lfs-saved');
                        setTimeout(function () {
                            $indicator.fadeOut(function () {
                                $(this).remove();
                            });
                        }, 2000);
                    } else {
                        $indicator.text(' ✗ Fel!').addClass('lfs-error');
                    }
                },
                error: function () {
                    $indicator.text(' ✗ Fel!').addClass('lfs-error');
                }
            });
        });

    });

})(jQuery);

/**
 * Add CSS for notifications and other UI elements
 */
document.addEventListener('DOMContentLoaded', function () {
    const style = document.createElement('style');
    style.textContent = `
        /* Notifications */
        .lfs-notification {
            position: fixed;
            top: 32px;
            right: 20px;
            background: #fff;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 999999;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            font-size: 14px;
            font-weight: 600;
            max-width: 350px;
        }
        
        .lfs-notification-show {
            transform: translateX(0);
        }
        
        .lfs-notification-success {
            border-left: 4px solid #2ecc71;
            color: #27ae60;
        }
        
        .lfs-notification-error {
            border-left: 4px solid #e74c3c;
            color: #c0392b;
        }
        
        .lfs-notification-info {
            border-left: 4px solid #3498db;
            color: #2980b9;
        }
        
        /* Spin animation */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .spin {
            animation: spin 1s linear infinite;
        }
        
        /* Saving indicator */
        .lfs-saving-indicator {
            margin-left: 10px;
            font-size: 12px;
            color: #7f8c8d;
        }
        
        .lfs-saving-indicator.lfs-saved {
            color: #2ecc71;
        }
        
        .lfs-saving-indicator.lfs-error {
            color: #e74c3c;
        }
        
        /* Button loading state */
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        /* Smooth transitions */
        .lfs-point-value,
        .lfs-balance-amount {
            transition: all 0.3s ease;
        }
        
        .lfs-point-value.updating,
        .lfs-balance-amount.updating {
            color: #3498db;
            transform: scale(1.1);
        }
    `;
    document.head.appendChild(style);
});