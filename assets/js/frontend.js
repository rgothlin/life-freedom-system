/**
 * Life Freedom System - Frontend JavaScript
 * 
 * File location: assets/js/frontend.js
 * For future use if you want frontend functionality
 */

(function ($) {
    'use strict';

    $(document).ready(function () {

        // Animate progress bars on scroll
        if ($('.lfs-frontend-progress-bar').length) {
            $(window).on('scroll', function () {
                $('.lfs-frontend-progress-bar').each(function () {
                    if (isElementInViewport($(this))) {
                        var $fill = $(this).find('.lfs-frontend-progress-fill');
                        var width = $fill.data('width');
                        $fill.css('width', width + '%');
                    }
                });
            });

            // Trigger on load
            $(window).trigger('scroll');
        }

        // Counter animation for point values
        if ($('.lfs-frontend-point-value').length) {
            $('.lfs-frontend-point-value').each(function () {
                var $this = $(this);
                var countTo = parseInt($this.text());

                $({ countNum: 0 }).animate({
                    countNum: countTo
                }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function () {
                        $this.text(Math.floor(this.countNum));
                    },
                    complete: function () {
                        $this.text(this.countNum);
                    }
                });
            });
        }

    });

    /**
     * Check if element is in viewport
     */
    function isElementInViewport(el) {
        var rect = el[0].getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

})(jQuery);