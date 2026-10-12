/**
 * XtremeSlider — Admin JavaScript
 */
(function ($) {
    'use strict';

    $(document).ready(function () {
        /* ==================================================================
           0. One-Time Status Message URL Cleanup
           ================================================================== */
        if (window.history && window.history.replaceState && window.URL) {
            var url = new URL(window.location.href);
            var hasSaved = url.searchParams.has('saved');
            var hasError = url.searchParams.has('xs_error');

            if (hasSaved || hasError) {
                url.searchParams.delete('saved');
                url.searchParams.delete('xs_error');
                window.history.replaceState({}, document.title, url.toString());
            }
        }

        /* ==================================================================
           1. Layout Radio Cards
           ================================================================== */
        function applyLayoutVisibility(val) {
            if (val === 'cool') {
                $('#xs-gradient-section').slideDown(200);
            } else {
                $('#xs-gradient-section').slideUp(200);
            }
            if (val === '3d') {
                $('#xs-3d-bg-section').slideDown(200);
            } else {
                $('#xs-3d-bg-section').slideUp(200);
            }
            if (val === 'options') {
                $('.xs-hide-on-options').hide();
                $('.xs-field-non-options').hide();
                $('.xs-field-options-only').show();
                $('#xs-slides-grid').addClass('xs-slides-grid-options');
                $('#xs-options-bg-section').slideDown(200);
            } else {
                $('.xs-hide-on-options').show();
                $('.xs-field-non-options').show();
                $('.xs-field-options-only').hide();
                $('#xs-slides-grid').removeClass('xs-slides-grid-options');
                $('#xs-options-bg-section').slideUp(200);
            }
        }

        $('.xs-radio-card input[type="radio"]').on('change', function () {
            var $cards = $(this).closest('.xs-radio-cards');
            $cards.find('.xs-radio-card').removeClass('active');
            $(this).closest('.xs-radio-card').addClass('active');

            applyLayoutVisibility($(this).val());
        });

        // Apply initial layout visibility on page load
        applyLayoutVisibility($('input[name="layout"]:checked').val());

        /* ==================================================================
           2. Autoplay Toggle
           ================================================================== */
        $('#xs-autoplay-toggle').on('change', function () {
            if ($(this).is(':checked')) {
                $('.xs-autoplay-speed').slideDown(200);
            } else {
                $('.xs-autoplay-speed').slideUp(200);
            }
        });

        /* ==================================================================
           3. Speed Range Display
           ================================================================== */
        $('#xs-speed-range').on('input', function () {
            var seconds = parseInt($(this).val(), 10) / 1000;
            $('#xs-speed-label').text(seconds);
        });

        /* ==================================================================
           4. Media Uploader — Add Slides
           ================================================================== */
        var mediaFrame;
        var loadTitlesFeedbackTimer;

        $('#xs-add-slides').on('click', function (e) {
            e.preventDefault();

            var currentCount = $('#xs-slides-grid .xs-slide-card').length;
            var maxSlides = parseInt(xsAdmin.maxSlides, 10) || 10;

            if (currentCount >= maxSlides) {
                alert('Maximum ' + maxSlides + ' slides allowed.');
                return;
            }

            if (mediaFrame) {
                mediaFrame.open();
                return;
            }

            mediaFrame = wp.media({
                title: 'Select Slider Images',
                button: { text: 'Add to Slider' },
                multiple: true,
                library: { type: 'image' }
            });

            mediaFrame.on('select', function () {
                var attachments = mediaFrame.state().get('selection').toJSON();
                var remaining = maxSlides - $('#xs-slides-grid .xs-slide-card').length;

                $.each(attachments.slice(0, remaining), function (i, att) {
                    var imgUrl = att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url;
                    var fullUrl = att.url;
                    var fileName = att.filename || getFilenameFromUrl(fullUrl);

                    var card = '<div class="xs-slide-card" data-image-id="' + att.id + '" data-image-filename="' + escapeAttribute(fileName) + '">' +
                        '<div class="xs-slide-img"><img src="' + imgUrl + '" alt=""></div>' +
                        '<div class="xs-slide-fields">' +
                        '<input type="hidden" name="slides[image_id][]" value="' + att.id + '">' +
                        '<input type="hidden" name="slides[image_url][]" value="' + fullUrl + '">' +
                        '<input type="text" name="slides[title][]" value="" placeholder="Title (on image)">' +
                        '<input type="text" name="slides[caption][]" value="" placeholder="Caption (below image)">' +
                        '<input type="text" class="xs-field-non-options" name="slides[description][]" value="" placeholder="Description (optional)">' +
                        '<input type="text" class="xs-field-non-options" name="slides[link_url][]" value="" placeholder="Link URL (optional)">' +
                        '<div class="xs-field-options-only">' +
                            '<div class="xs-html-toolbar">' +
                                '<label class="xs-html-label">HTML Content</label>' +
                                '<div class="xs-html-tabs">' +
                                    '<button type="button" class="xs-html-tab active" data-mode="text">Code</button>' +
                                    '<button type="button" class="xs-html-tab" data-mode="preview">Preview</button>' +
                                '</div>' +
                            '</div>' +
                            '<textarea id="xs-html-editor-' + att.id + '-' + Date.now() + '" class="xs-slide-html" name="slides[html_content][]" rows="12" placeholder="Paste HTML here. It will be shown when this option is clicked on the frontend."></textarea>' +
                            '<iframe class="xs-html-preview" style="display:none;"></iframe>' +
                        '</div>' +
                        '</div>' +
                        '<button type="button" class="xs-slide-remove" title="Remove slide">&times;</button>' +
                        '</div>';

                    $('#xs-slides-grid').append(card);
                });

                // Re-apply visibility so fields in newly added slides match current layout
                applyLayoutVisibility($('input[name="layout"]:checked').val());
                updateSlideCount();
            });

            mediaFrame.open();
        });

        /* ==================================================================
           5. Remove Slide
           ================================================================== */
        $(document).on('click', '.xs-slide-remove', function () {
            $(this).closest('.xs-slide-card').fadeOut(200, function () {
                $(this).remove();
                updateSlideCount();
            });
        });

        /* ==================================================================
           6. Sortable Slides
           ================================================================== */
        if ($.fn.sortable) {
            $('#xs-slides-grid').sortable({
                items: '.xs-slide-card',
                cursor: 'grabbing',
                tolerance: 'pointer',
                placeholder: 'xs-slide-card ui-sortable-placeholder',
                update: function () {
                    updateSlideCount();
                }
            });
        }

        /* ==================================================================
           7. Slide Count Helper
           ================================================================== */
        function updateSlideCount() {
            var count = $('#xs-slides-grid .xs-slide-card').length;
            var max = parseInt(xsAdmin.maxSlides, 10) || 10;
            $('#xs-slide-count').text(count);

            if (count >= max) {
                $('#xs-add-slides').prop('disabled', true);
            } else {
                $('#xs-add-slides').prop('disabled', false);
            }

            $('#xs-load-titles').prop('disabled', count === 0);
        }

        /* ==================================================================
           8. Load Titles From Filenames
           ================================================================== */
        $('#xs-load-titles').on('click', function (e) {
            e.preventDefault();

            var $cards = $('#xs-slides-grid .xs-slide-card');

            if (!$cards.length) {
                showLoadTitlesFeedback(xsAdmin.loadTitles.empty, true);
                return;
            }

            var updated = 0;

            $cards.each(function () {
                var $card = $(this);
                var fileName = $card.attr('data-image-filename') || getFilenameFromUrl($card.find('input[name="slides[image_url][]"]').val()) || getFilenameFromUrl($card.find('.xs-slide-img img').attr('src'));
                var title = getTitleFromFilename(fileName);

                if (!title) {
                    return;
                }

                $card.find('input[name="slides[title][]"]').val(title);
                updated++;
            });

            if (!updated) {
                showLoadTitlesFeedback(xsAdmin.loadTitles.none, true);
                return;
            }

            var message = updated === 1 ? xsAdmin.loadTitles.single : xsAdmin.loadTitles.multiple.replace('%d', updated);
            showLoadTitlesFeedback(message, false);
        });

        function getFilenameFromUrl(url) {
            var cleanUrl = String(url || '').trim();

            if (!cleanUrl) {
                return '';
            }

            cleanUrl = cleanUrl.split('#')[0].split('?')[0];

            var fileName = cleanUrl.substring(cleanUrl.lastIndexOf('/') + 1);

            if (!fileName) {
                return '';
            }

            try {
                fileName = decodeURIComponent(fileName);
            } catch (error) {
                // Keep the raw filename if decoding fails.
            }

            return fileName.trim();
        }

        function getTitleFromFilename(fileName) {
            return String(fileName || '').replace(/\.[^.]+$/, '').trim();
        }

        function showLoadTitlesFeedback(message, isError) {
            var $feedback = $('#xs-load-titles-feedback');

            if (!$feedback.length) {
                return;
            }

            window.clearTimeout(loadTitlesFeedbackTimer);

            $feedback
                .text(message)
                .toggleClass('is-error', !!isError)
                .attr('hidden', false);

            loadTitlesFeedbackTimer = window.setTimeout(function () {
                $feedback.attr('hidden', true).removeClass('is-error').text('');
            }, 2400);
        }

        function escapeAttribute(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }

        /* ==================================================================
           9. Copy Shortcode
           ================================================================== */
        $(document).on('click', '.xs-copy-btn', function () {
            var $btn = $(this);
            var text = $btn.data('copy');

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function () {
                    showCopied($btn);
                });
            } else {
                // Fallback
                var $temp = $('<textarea>');
                $('body').append($temp);
                $temp.val(text).select();
                document.execCommand('copy');
                $temp.remove();
                showCopied($btn);
            }
        });

        function showCopied($btn) {
            var orig = $btn.html();
            $btn.addClass('copied').html('Copied!');
            setTimeout(function () {
                $btn.removeClass('copied').html(orig);
            }, 1500);
        }

        /* ==================================================================
           10. Fixed-Height Row Toggle
           ================================================================== */
        $(document).on('change', 'select[name="image_ratio"]', function () {
            var isFixed = $(this).val() === 'fixed';
            $('#xs-fixed-height-row').toggle(isFixed);
            $('#xs-fixed-height-row input[name="fixed_height"]').prop('disabled', !isFixed);
        });

        /* ==================================================================
           11. Color Pickers
           ================================================================== */
        if ($.fn.wpColorPicker) {
            $('.xs-color-picker').wpColorPicker({
                change: function () {
                    updateGradientPreview();
                },
                clear: function () {
                    updateGradientPreview();
                }
            });

            // Close picker when clicking its own swatch button while open (toggle).
            $(document).on('click', '.wp-color-result', function () {
                var $btn     = $(this);
                var $wrap    = $btn.closest('.wp-picker-container');
                var $holder  = $wrap.find('.wp-picker-holder');
                var isOpen   = $wrap.hasClass('wp-picker-open');

                // Close all other open pickers first.
                $('.wp-picker-container.wp-picker-open').not($wrap).each(function () {
                    $(this).find('.xs-color-picker').wpColorPicker('close');
                });

                // Toggle current: if already open, close it.
                if (isOpen) {
                    $wrap.find('.xs-color-picker').wpColorPicker('close');
                }
            });

            // Close any open picker when clicking outside of it.
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.wp-picker-container').length) {
                    $('.wp-picker-container.wp-picker-open').each(function () {
                        $(this).find('.xs-color-picker').wpColorPicker('close');
                    });
                }
            });

            // Close all pickers when the save button is clicked so they never
            // sit on top of the button area and re-open on the next interaction.
            $(document).on('click', '#xs-save-btn', function () {
                $('.wp-picker-container.wp-picker-open').each(function () {
                    $(this).find('.xs-color-picker').wpColorPicker('close');
                });
            });
        }

        function updateGradientPreview() {
            setTimeout(function () {
                var start = $('input[name="gradient_start"]').val();
                var end   = $('input[name="gradient_end"]').val();
                var $preview = $('#xs-gradient-preview');
                if (start || end) {
                    var from = start || end;
                    var to   = end   || start;
                    $preview.css('background', 'linear-gradient(135deg, ' + from + ', ' + to + ')');
                } else {
                    $preview.css('background', '');
                }

            }, 50);
        }

        /* ==================================================================
           12. HTML Content Editor Tabs (Code / Preview)
           ================================================================== */
        $(document).on('click', '.xs-html-tab', function (e) {
            e.preventDefault();
            var $btn       = $(this);
            var mode       = $btn.data('mode');
            var $container = $btn.closest('.xs-field-options-only');
            var $textarea  = $container.find('textarea.xs-slide-html');
            var $iframe    = $container.find('.xs-html-preview');
            if (!$textarea.length) return;

            $container.find('.xs-html-tab').removeClass('active');
            $btn.addClass('active');

            if (mode === 'preview') {
                var html = $textarea.val() || '';
                $textarea.hide();
                $iframe.show();
                var iframe = $iframe.get(0);
                var doc = iframe.contentDocument || iframe.contentWindow.document;
                doc.open();
                doc.write('<!DOCTYPE html><html><head><meta charset="utf-8"><style>body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;margin:16px;color:#0f0f0f;background:#fff;}</style></head><body>' + (html || '<p style="color:#9ca3af;">Nothing to preview yet.</p>') + '</body></html>');
                doc.close();
            } else {
                $iframe.hide();
                $textarea.show();
            }
        });

        /* ==================================================================
           13. Save Button Loading Spinner
           ================================================================== */
        $('form').on('submit', function () {
            var $btn = $(this).find('#xs-save-btn');
            if ($btn.length) {
                $btn.addClass('xs-btn-saving');
            }
        });

        updateSlideCount();

    });

})(jQuery);
