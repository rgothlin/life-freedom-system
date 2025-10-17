/**
 * Life Freedom System - Admin JavaScript
 * 
 * File location: assets/js/admin.js
 */

(function ($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function () {

        // Auto-update total points in activity meta box
        if ($('#lfs_fp, #lfs_bp, #lfs_sp').length) {
            updateTotalPoints();

            $('#lfs_fp, #lfs_bp, #lfs_sp').on('input change', function () {
                updateTotalPoints();
            });
        }

        // Confirm before redeeming reward
        $('.lfs-redeem-reward-btn').on('click', function (e) {
            if (!confirm(lfsData.i18n.confirmRedeem || 'Är du säker på att du vill lösa in denna belöning?')) {
                e.preventDefault();
                return false;
            }
        });

        // Phase selector helper
        $('#lfs_current_phase').on('change', function () {
            var phase = $(this).val();
            var pointsPerKr = 0.5; // default

            switch (phase) {
                case 'survival':
                    pointsPerKr = 0.5;
                    break;
                case 'stabilizing':
                    pointsPerKr = 0.8;
                    break;
                case 'autonomy':
                    pointsPerKr = 1.0;
                    break;
            }

            if ($('#lfs_points_per_kr').length) {
                $('#lfs_points_per_kr').val(pointsPerKr);
            }
        });

        // Calculate reward account amount based on percentage
        $('#lfs_reward_account_percent, #lfs_monthly_income').on('input', function () {
            var income = parseFloat($('#lfs_monthly_income').val()) || 0;
            var percent = parseFloat($('#lfs_reward_account_percent').val()) || 0;
            var amount = (income * percent / 100).toFixed(0);

            if ($('.lfs-calculated-reward-amount').length) {
                $('.lfs-calculated-reward-amount').text(amount.replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' kr/månad');
            }
        });

        // Streak calculation trigger
        $('.lfs-calculate-streak').on('click', function (e) {
            e.preventDefault();

            var $btn = $(this);
            $btn.prop('disabled', true).text('Beräknar...');

            $.post(lfsData.ajaxUrl, {
                action: 'lfs_calculate_streak',
                nonce: lfsData.nonce
            }, function (response) {
                if (response.success) {
                    $('.lfs-streak-value').text(response.data.streak);
                    $btn.prop('disabled', false).text('Uppdatera streak');
                } else {
                    alert(lfsData.i18n.error);
                    $btn.prop('disabled', false).text('Uppdatera streak');
                }
            });
        });

        // Activity type color coding
        if ($('.activity_type-checklist').length) {
            $('.activity_type-checklist input[type="checkbox"]').each(function () {
                var $label = $(this).parent();
                var typeName = $label.text().trim().toLowerCase();

                if (typeName.includes('deep work')) {
                    $label.css('color', '#3498db');
                } else if (typeName.includes('paus')) {
                    $label.css('color', '#2ecc71');
                } else if (typeName.includes('träning')) {
                    $label.css('color', '#e74c3c');
                }
            });
        }

        // Enhanced date picker for activities
        if ($('#lfs_activity_datetime').length) {
            var now = new Date();
            if (!$('#lfs_activity_datetime').val()) {
                // Set default to current time when creating new activity
                var timestamp = Math.floor(now.getTime() / 1000);
                $('#lfs_activity_datetime').val(timestamp);
            }
        }

        // Dashboard refresh functionality
        $('.lfs-refresh-dashboard').on('click', function (e) {
            e.preventDefault();

            $.post(lfsData.ajaxUrl, {
                action: 'lfs_get_dashboard_data',
                nonce: lfsData.nonce
            }, function (response) {
                if (response.success) {
                    location.reload();
                }
            });
        });

        // Export functionality (placeholder for future feature)
        $('.lfs-export-data').on('click', function (e) {
            e.preventDefault();
            alert('Export-funktionalitet kommer snart!');
        });

        // Keyboard shortcuts
        $(document).on('keydown', function (e) {
            // Ctrl/Cmd + K = Quick add activity
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                if ($('#lfsCustomTitle').length) {
                    $('#lfsCustomTitle').focus();
                }
            }
        });

        // Notification when points reach milestones
        checkPointMilestones();

        // Auto-save draft activities (every 30 seconds)
        if ($('#post-type-life_activity').length) {
            setInterval(function () {
                if ($('#save-post').length && !$('#save-post').prop('disabled')) {
                    $('#save-post').click();
                }
            }, 30000);
        }

        // Tooltips
        $('[data-lfs-tooltip]').each(function () {
            $(this).attr('title', $(this).data('lfs-tooltip'));
        });

    }); // End document ready

    /**
     * Update total points display
     */
    function updateTotalPoints() {
        var fp = parseInt($('#lfs_fp').val()) || 0;
        var bp = parseInt($('#lfs_bp').val()) || 0;
        var sp = parseInt($('#lfs_sp').val()) || 0;
        var total = fp + bp + sp;

        if ($('.lfs-total-value').length) {
            $('.lfs-total-value').text(total);
        }

        // Color code based on total
        var $totalDisplay = $('.lfs-total-points');
        $totalDisplay.removeClass('lfs-low lfs-medium lfs-high');

        if (total < 50) {
            $totalDisplay.addClass('lfs-low');
        } else if (total < 100) {
            $totalDisplay.addClass('lfs-medium');
        } else {
            $totalDisplay.addClass('lfs-high');
        }
    }

    /**
     * Check if user has reached point milestones
     */
    function checkPointMilestones() {
        $.post(lfsData.ajaxUrl, {
            action: 'lfs_get_current_points',
            nonce: lfsData.nonce
        }, function (response) {
            if (response.success) {
                var points = response.data;

                // Check for milestone achievements
                var milestones = [
                    { value: 1000, name: '1000 totalt poäng' },
                    { value: 5000, name: '5000 totalt poäng' },
                    { value: 10000, name: '10000 totalt poäng' },
                    { value: 500, type: 'fp', name: '500 FP' },
                    { value: 1000, type: 'fp', name: '1000 FP' },
                    { value: 500, type: 'bp', name: '500 BP' },
                    { value: 500, type: 'sp', name: '500 SP' }
                ];

                milestones.forEach(function (milestone) {
                    var currentValue = milestone.type ? points[milestone.type] : points.total;
                    var storageKey = 'lfs_milestone_' + milestone.name.replace(/\s+/g, '_');

                    if (currentValue >= milestone.value && !localStorage.getItem(storageKey)) {
                        showMilestoneNotification(milestone.name);
                        localStorage.setItem(storageKey, 'achieved');
                    }
                });
            }
        });
    }

    /**
     * Show milestone notification
     */
    function showMilestoneNotification(milestoneName) {
        var $notification = $('<div class="lfs-milestone-notification">')
            .html('<span class="dashicons dashicons-awards"></span> <strong>Grattis!</strong> Du har nått: ' + milestoneName + ' 🎉')
            .hide()
            .appendTo('body')
            .fadeIn(500);

        setTimeout(function () {
            $notification.fadeOut(500, function () {
                $(this).remove();
            });
        }, 5000);
    }

    /**
     * Format number with spaces
     */
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }

    /**
     * Show loading spinner
     */
    function showLoading($element) {
        $element.append('<span class="spinner is-active" style="float: none; margin: 0 10px;"></span>');
    }

    /**
     * Hide loading spinner
     */
    function hideLoading($element) {
        $element.find('.spinner').remove();
    }

    /**
     * Show success message
     */
    function showSuccess(message) {
        var $notice = $('<div class="notice notice-success is-dismissible"><p>' + message + '</p></div>');
        $('.wrap h1').after($notice);

        setTimeout(function () {
            $notice.fadeOut(function () {
                $(this).remove();
            });
        }, 3000);
    }

    /**
     * Show error message
     */
    function showError(message) {
        var $notice = $('<div class="notice notice-error is-dismissible"><p>' + message + '</p></div>');
        $('.wrap h1').after($notice);
    }

    // Make functions globally accessible
    window.lfsShowSuccess = showSuccess;
    window.lfsShowError = showError;
    window.lfsFormatNumber = formatNumber;

})(jQuery);

// CSS for milestone notifications
jQuery(document).ready(function ($) {
    if (!$('#lfs-milestone-notification-styles').length) {
        $('head').append(`
            <style id="lfs-milestone-notification-styles">
                .lfs-milestone-notification {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 30px 50px;
                    border-radius: 12px;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
                    z-index: 999999;
                    font-size: 18px;
                    text-align: center;
                    animation: lfsSlideIn 0.5s ease-out;
                }
                
                .lfs-milestone-notification .dashicons {
                    font-size: 32px;
                    width: 32px;
                    height: 32px;
                    vertical-align: middle;
                    margin-right: 10px;
                }
                
                @keyframes lfsSlideIn {
                    from {
                        transform: translate(-50%, -60%);
                        opacity: 0;
                    }
                    to {
                        transform: translate(-50%, -50%);
                        opacity: 1;
                    }
                }
                
                .lfs-total-points.lfs-low {
                    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
                }
                
                .lfs-total-points.lfs-medium {
                    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
                }
                
                .lfs-total-points.lfs-high {
                    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
                }
            </style>
        `);
    }
});