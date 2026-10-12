; (function ($) {
    'use strict'

    function categoryPreloader() {
        jQuery('body').find('.sp-wcsp-slider-section.wcsp-preloader').each(function (e) {
            var wcsp_id = $(this).attr('id'),
                parents_class = jQuery('#' + wcsp_id).parent('.sp-wcsp-slider-area'),
                parents_siblings_id = parents_class.find('.sp-wcsp-preloader').attr('id');
            jQuery(document).ready(function () {
                jQuery('#' + parents_siblings_id).animate({ opacity: 0 }, 600).remove();
                jQuery('#' + wcsp_id).animate({ opacity: 1 }, 600)
            })
        })

    }

    // Initialize the carousel when the document is ready
    $(document).ready(function () {
        categoryPreloader();
    });

    // Register the handler with Elementor's frontend event.
    $(window).on('elementor/frontend/init', function () {
        // For General Widget.
        elementorFrontend.hooks.addAction('frontend/element_ready/sp_woo_category_slider_pro_shortcode.default', categoryPreloader);
        // For Deprecated widget.
        elementorFrontend.hooks.addAction('frontend/element_ready/sp_woo_category_slider_free_shortcode.default', categoryPreloader);
    });
})(jQuery)
