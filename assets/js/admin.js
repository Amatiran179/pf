(function($) {
    'use strict';

    $(document).ready(function() {

        console.log('🚀 PutraFiber Admin JS Initializing...');
        console.log('   - jQuery:', typeof jQuery !== 'undefined' ? '✅' : '❌');
        console.log('   - WP Media:', typeof wp !== 'undefined' && typeof wp.media !== 'undefined' ? '✅' : '❌');

        // ===================================================================
        // GENERIC GALLERY UPLOADER (for Product & Portfolio)
        // ===================================================================
        var galleryUploader;

        $(document).on('click', '[id^="upload-"][id$="-gallery-button"]', function(e) {
            e.preventDefault();
            var $button = $(this);
            var galleryType = $button.attr('id').includes('portfolio') ? 'portfolio' : 'product';
            var $hiddenInput = $('#' + galleryType + '_gallery');
            var $previewContainer = $('#' + galleryType + '-gallery-preview');

            console.log('🖼️ Gallery upload triggered for:', galleryType);

            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                alert('WordPress Media library not loaded. Please refresh the page.');
                console.error('❌ wp.media not available');
                return;
            }

            if (galleryUploader) {
                galleryUploader.open();
                return;
            }

            galleryUploader = wp.media({
                title: 'Select ' + (galleryType.charAt(0).toUpperCase() + galleryType.slice(1)) + ' Gallery Images',
                button: { text: 'Add to Gallery' },
                multiple: true
            });

            galleryUploader.on('select', function() {
                var attachments = galleryUploader.state().get('selection').toJSON();
                var existingIds = $hiddenInput.val() ? $hiddenInput.val().split(',') : [];

                console.log('✅ Selected ' + attachments.length + ' images');

                attachments.forEach(function(attachment) {
                    if ($.inArray(attachment.id.toString(), existingIds) === -1) {
                        existingIds.push(attachment.id);
                        var thumbUrl = (attachment.sizes && attachment.sizes.thumbnail) ? attachment.sizes.thumbnail.url : attachment.url;
                        var itemHtml = '<div class="gallery-item" data-id="' + attachment.id + '"><img src="' + thumbUrl + '" alt="Gallery image"><button type="button" class="remove-gallery-item" title="Remove">&times;</button></div>';
                        $previewContainer.append(itemHtml);
                    }
                });

                $hiddenInput.val(existingIds.join(','));
                console.log('✅ ' + galleryType + ' gallery updated');
            });

            galleryUploader.open();
        });

        // ===================================================================
        // REMOVE PRODUCT GALLERY ITEM
        // -- GENERIC: Works for both Product & Portfolio --
        // ===================================================================
        $(document).on('click', '.remove-gallery-item', function(e) {
            e.preventDefault();

            var $item = $(this).closest('.gallery-item');
            var $previewContainer = $item.parent();
            var $hiddenInput = $previewContainer.siblings('input[type="hidden"]');
            var itemId = $item.data('id');

            $item.fadeOut(200, function() {
                $(this).remove();
            });

            if (!$hiddenInput.length) return;

            var currentIds = $hiddenInput.val().split(',').filter(function(id) {
                return id && id != itemId;
            });

            $hiddenInput.val(currentIds.join(','));

            var galleryType = $hiddenInput.attr('id').includes('portfolio') ? 'Portfolio' : 'Product';
            console.log('🗑️ Removed ' + galleryType + ' gallery item:', itemId);
        });

        // ===================================================================
        // PRODUCT GALLERY SORTABLE
        // ===================================================================
        if ($.fn.sortable && $('.gallery-preview-grid').length > 0) {
            $('.gallery-preview-grid').sortable({
                placeholder: 'gallery-item-placeholder',
                cursor: 'move',
                update: function() {
                    var $previewContainer = $(this);
                    var $hiddenInput = $previewContainer.siblings('input[type="hidden"]');
                    var ids = [];
                    $previewContainer.find('.gallery-item').each(function() {
                        ids.push($(this).data('id'));
                    });
                    $hiddenInput.val(ids.join(','));
                    var galleryType = $hiddenInput.attr('id').includes('portfolio') ? 'Portfolio' : 'Product';
                    console.log('🔄 ' + galleryType + ' gallery reordered');
                }
            }).disableSelection();
        }

        // ===================================================================
        // PRODUCT PDF UPLOAD
        // ===================================================================
        var pdfUploader;

        $(document).on('click', '.upload-pdf-button', function(e) {
            e.preventDefault();

            console.log('📄 PDF upload button clicked');

            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                alert('WordPress Media library not loaded. Please refresh the page.');
                return;
            }

            if (pdfUploader) {
                pdfUploader.open();
                return;
            }

            pdfUploader = wp.media({
                title: 'Select PDF File',
                button: { text: 'Use this PDF' },
                multiple: false,
                library: { type: 'application/pdf' }
            });

            pdfUploader.on('select', function() {
                var attachment = pdfUploader.state().get('selection').first().toJSON();
                $('#product_catalog_pdf').val(attachment.url);
                console.log('✅ PDF selected:', attachment.filename);
            });

            pdfUploader.open();
        });

        // ===================================================================
        // PRODUCT PRICE TYPE TOGGLE
        // ===================================================================
        $('input[name="product_price_type"]').on('change', function() {
            $('.price-input-wrapper').removeClass('active');
            if ($(this).val() === 'price') {
                $('#price-input-box').addClass('active');
            } else {
                $('#whatsapp-cta-box').addClass('active');
            }
            console.log('💰 Price type changed to:', $(this).val());
        });

        // ===================================================================
        // GENERIC MEDIA UPLOAD
        // For other meta boxes (featured images, icons, etc.)
        // ===================================================================
        var mediaUploader;

        $('.putrafiber-upload-image, .putrafiber-upload-icon').on('click', function(e) {
            e.preventDefault();

            var button = $(this);
            var inputField = button.siblings('input[type="hidden"]');
            var previewContainer = button.siblings('.image-preview, .icon-preview');

            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                alert('WordPress Media library not loaded. Please refresh the page.');
                return;
            }

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: 'Select Image',
                button: { text: 'Use this image' },
                multiple: false
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                inputField.val(attachment.url);
                previewContainer.html('<img src="' + attachment.url + '" style="max-width: 300px;">');
                console.log('✅ Image selected');
            });

            mediaUploader.open();
        });

        // ===================================================================
        // REMOVE IMAGE
        // ===================================================================
        $('.putrafiber-remove-image, .putrafiber-remove-icon').on('click', function(e) {
            e.preventDefault();
            $(this).siblings('input[type="hidden"]').val('');
            $(this).siblings('.image-preview, .icon-preview').html('');
            console.log('🗑️ Image removed');
        });

        // ===================================================================
        // TAXONOMY SEO IMAGE UPLOADER
        // ===================================================================
        var termOgUploader;
        var currentTermOgTarget;
        $(document).on('click', '.pf-term-og-upload', function(e) {
            e.preventDefault();

            currentTermOgTarget = $(this).closest('.form-field, td');

            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                alert('WordPress Media library not loaded. Please refresh the page.');
                return;
            }

            if (termOgUploader) {
                termOgUploader.open();
            } else {
                termOgUploader = wp.media({
                    title: 'Pilih Gambar SEO',
                    button: { text: 'Gunakan Gambar' },
                    multiple: false
                });

                termOgUploader.on('select', function() {
                    var attachment = termOgUploader.state().get('selection').first().toJSON();
                    if (!currentTermOgTarget) {
                        return;
                    }
                    currentTermOgTarget.find('.pf-term-og-image-id').val(attachment.id);
                    currentTermOgTarget.find('.pf-term-og-image-url').val(attachment.url);
                    currentTermOgTarget.find('.pf-term-og-preview').html('<img src="' + attachment.url + '" style="max-width:160px;border-radius:4px;margin-top:8px;">');
                });
            }

            termOgUploader.open();
        });

        $(document).on('click', '.pf-term-og-remove', function(e) {
            e.preventDefault();
            var $container = $(this).closest('.form-field, td');
            $container.find('.pf-term-og-image-id').val('');
            $container.find('.pf-term-og-image-url').val('');
            $container.find('.pf-term-og-preview').empty();
            if ($container.is(currentTermOgTarget)) {
                currentTermOgTarget = null;
            }
        });

        // ===================================================================
        // TAB NAVIGATION
        // ===================================================================
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();

            var tabId = $(this).attr('href');

            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');

            $('.tab-content').removeClass('active');
            $(tabId).addClass('active');

            console.log('📑 Tab switched to:', tabId);
        });

        // ===================================================================
        // SEO CHARACTER COUNTER
        // ===================================================================
        function updateCharacterCount() {
            var $titleInput = $('#meta_title');
            var $descInput = $('#meta_description');

            if ($titleInput.length === 0 || $descInput.length === 0) {
                return; // SEO fields not present on this page
            }

            var titleLength = $titleInput.val().length;
            var descLength = $descInput.val().length;

            $('#title-length').text(titleLength);
            $('#desc-length').text(descLength);

            // Color coding for title
            if (titleLength > 60) {
                $('#title-length').css('color', '#dc3545');
            } else if (titleLength > 50) {
                $('#title-length').css('color', '#ffc107');
            } else {
                $('#title-length').css('color', '#28a745');
            }

            // Color coding for description
            if (descLength > 160) {
                $('#desc-length').css('color', '#dc3545');
            } else if (descLength > 150) {
                $('#desc-length').css('color', '#ffc107');
            } else {
                $('#desc-length').css('color', '#28a745');
            }
        }

        $('#meta_title, #meta_description').on('keyup', updateCharacterCount);
        updateCharacterCount();

        // ===================================================================
        // FORM VALIDATION
        // ===================================================================
        $('form').on('submit', function(e) {
            var isValid = true;
            var $form = $(this);

            $form.find('[required]').each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).addClass('error');

                    if ($(this).siblings('.error-message').length === 0) {
                        $(this).after('<span class="error-message" style="color: #dc3545; font-size: 12px;">This field is required</span>');
                    }
                } else {
                    $(this).removeClass('error');
                    $(this).siblings('.error-message').remove();
                }
            });

            if (!isValid) {
                alert('Please fill in all required fields');
                return false;
            }

            return true;
        });

        // ===================================================================
        // COLOR PICKER
        // ===================================================================
        if ($.fn.wpColorPicker) {
            $('.color-picker').wpColorPicker();
            console.log('🎨 Color picker initialized');
        }

        // ===================================================================
        // SORTABLE LISTS
        // ===================================================================
        if ($.fn.sortable) {
            $('.sortable-list').sortable({
                placeholder: 'sortable-placeholder',
                cursor: 'move',
                update: function(event, ui) {
                    var order = $(this).sortable('toArray');
                    console.log('🔄 List reordered:', order);
                }
            });
        }

        // ===================================================================
        // CONFIRM DELETE
        // ===================================================================
        $('.delete-item').on('click', function(e) {
            var itemName = $(this).data('item-name') || 'this item';
            if (!confirm('Are you sure you want to delete ' + itemName + '?')) {
                e.preventDefault();
                return false;
            }
        });

        // ===================================================================
        // AUTO-SAVE DRAFT
        // ===================================================================
        var autoSaveTimer;
        $('.auto-save-field').on('input', function() {
            clearTimeout(autoSaveTimer);

            autoSaveTimer = setTimeout(function() {
                console.log('💾 Auto-saving...');
                // Auto-save logic can be implemented here
            }, 2000);
        });

        // ===================================================================
        // TOOLTIPS
        // ===================================================================
        if ($.fn.tooltip) {
            $('[data-tooltip]').tooltip();
            console.log('💬 Tooltips initialized');
        }

        // ===================================================================
        // ANALYTICS RESET ACTION
        // ===================================================================
        $(document).on('click', '.putrafiber-reset-analytics', function(e) {
            e.preventDefault();

            var $button = $(this);
            var confirmMessage = $button.data('confirm') || 'Reset analytics data?';
            var successMessage = $button.data('success') || (window.putrafiberAdminVars && putrafiberAdminVars.analyticsResetSuccess ? putrafiberAdminVars.analyticsResetSuccess : 'Analytics cleared.');
            var errorMessage = (window.putrafiberAdminVars && putrafiberAdminVars.analyticsResetError) ? putrafiberAdminVars.analyticsResetError : 'Terjadi kesalahan saat menghapus data analytics.';

            if (!window.confirm(confirmMessage)) {
                return;
            }

            var nonce = $button.data('nonce') || '';
            var ajaxUrl = (window.putrafiberAdminVars && putrafiberAdminVars.ajax_url) ? putrafiberAdminVars.ajax_url : (typeof ajaxurl !== 'undefined' ? ajaxurl : '');

            if (!ajaxUrl) {
                window.alert(errorMessage);
                return;
            }

            $button.addClass('is-busy').prop('disabled', true);

            $.post(ajaxUrl, {
                action: 'putrafiber_reset_analytics',
                nonce: nonce
            }).done(function(response) {
                if (response && response.success) {
                    if (successMessage) {
                        window.alert(successMessage);
                    }
                    window.location.reload();
                } else if (response && response.data && response.data.message) {
                    window.alert(response.data.message);
                } else {
                    window.alert(errorMessage);
                }
            }).fail(function() {
                window.alert(errorMessage);
            }).always(function() {
                $button.removeClass('is-busy').prop('disabled', false);
            });
        });

        // ===================================================================
        // INITIALIZATION COMPLETE
        // ===================================================================
        console.log('✅ PutraFiber Admin JS fully loaded');
        console.log('   Handlers registered:');
        console.log('   - Generic Gallery Upload: [id^="upload-"][id$="-gallery-button"]');
        console.log('   - Generic Gallery Sort/Remove: .gallery-preview-grid, .remove-gallery-item');
        console.log('   - Generic Media Upload: .putrafiber-upload-image');
        console.log('   - PDF Upload: .upload-pdf-button');

    });

})(jQuery);
