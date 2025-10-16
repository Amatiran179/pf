(function($) {
    'use strict';

    $(document).ready(function() {

        console.log('🚀 PutraFiber Admin JS Initializing...');
        console.log('   - jQuery:', typeof jQuery !== 'undefined' ? '✅' : '❌');
        console.log('   - WP Media:', typeof wp !== 'undefined' && typeof wp.media !== 'undefined' ? '✅' : '❌');

        // ===================================================================
        // PRODUCT GALLERY UPLOADER
        // Selector: #upload-gallery-button (for Product CPT)
        // ===================================================================
        var productGalleryUploader;

        $(document).on('click', '#upload-gallery-button', function(e) {
            e.preventDefault();
            
            console.log('🖼️ Product gallery button clicked');

            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                alert('WordPress Media library not loaded. Please refresh the page.');
                console.error('❌ wp.media not available');
                return;
            }

            if (productGalleryUploader) {
                productGalleryUploader.open();
                return;
            }

            productGalleryUploader = wp.media({
                title: 'Select Gallery Images',
                button: { text: 'Add to Gallery' },
                multiple: true
            });

            productGalleryUploader.on('select', function() {
                var attachments = productGalleryUploader.state().get('selection').toJSON();
                var existingIds = $('#product_gallery').val() ? $('#product_gallery').val().split(',') : [];
                
                console.log('✅ Selected ' + attachments.length + ' images');

                attachments.forEach(function(attachment) {
                    if ($.inArray(attachment.id.toString(), existingIds) === -1) {
                        existingIds.push(attachment.id);
                        
                        var thumbnailUrl = attachment.sizes && attachment.sizes.thumbnail 
                            ? attachment.sizes.thumbnail.url 
                            : attachment.url;
                        
                        $('#gallery-preview').append(
                            '<div class="gallery-item" data-id="' + attachment.id + '">' +
                                '<img src="' + thumbnailUrl + '" alt="Gallery image">' +
                                '<button type="button" class="remove-gallery-item" title="Hapus">&times;</button>' +
                            '</div>'
                        );
                    }
                });

                $('#product_gallery').val(existingIds.join(','));
                console.log('✅ Product gallery updated');
            });

            productGalleryUploader.open();
        });

        // ===================================================================
        // REMOVE PRODUCT GALLERY ITEM
        // ===================================================================
        $(document).on('click', '.remove-gallery-item', function(e) {
            e.preventDefault();
            
            var $item = $(this).closest('.gallery-item');
            var itemId = $item.data('id');
            
            $item.fadeOut(200, function() {
                $(this).remove();
            });
            
            var currentIds = $('#product_gallery').val().split(',').filter(function(id) {
                return id && id != itemId;
            });
            $('#product_gallery').val(currentIds.join(','));
            
            console.log('🗑️ Removed gallery item:', itemId);
        });

        // ===================================================================
        // PRODUCT GALLERY SORTABLE
        // ===================================================================
        if ($.fn.sortable && $('#gallery-preview').length > 0) {
            $('#gallery-preview').sortable({
                placeholder: 'gallery-item-placeholder',
                cursor: 'move',
                update: function() {
                    var ids = [];
                    $('.gallery-item').each(function() {
                        ids.push($(this).data('id'));
                    });
                    $('#product_gallery').val(ids.join(','));
                    console.log('🔄 Product gallery reordered');
                }
            });
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
        // PORTFOLIO GALLERY UPLOADER - LEGACY SUPPORT
        // Selector: .portfolio-gallery-upload (for Portfolio CPT)
        // KEPT AS-IS to prevent breaking existing functionality
        // ===================================================================
        var portfolioGalleryUploader;

        $('.portfolio-gallery-upload').on('click', function(e) {
            e.preventDefault();
            
            console.log('🎨 Portfolio gallery button clicked');

            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                alert('WordPress Media library not loaded. Please refresh the page.');
                return;
            }

            if (portfolioGalleryUploader) {
                portfolioGalleryUploader.open();
                return;
            }

            portfolioGalleryUploader = wp.media({
                title: 'Select Gallery Images',
                button: { text: 'Add to Gallery' },
                multiple: true
            });

            portfolioGalleryUploader.on('select', function() {
                var attachments = portfolioGalleryUploader.state().get('selection').toJSON();
                var ids = [];
                var html = '';

                attachments.forEach(function(attachment) {
                    ids.push(attachment.id);
                    html += '<img src="' + attachment.url + '" style="width: 100px; height: 100px; object-fit: cover; margin: 5px;">';
                });

                $('#portfolio_gallery').val(ids.join(','));
                $('.portfolio-gallery-preview').html(html);
                
                console.log('✅ Portfolio gallery updated with ' + ids.length + ' images');
            });

            portfolioGalleryUploader.open();
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
        // INITIALIZATION COMPLETE
        // ===================================================================
        console.log('✅ PutraFiber Admin JS fully loaded');
        console.log('   Handlers registered:');
        console.log('   - Product Gallery Upload: #upload-gallery-button');
        console.log('   - Portfolio Gallery Upload: .portfolio-gallery-upload');
        console.log('   - Generic Media Upload: .putrafiber-upload-image');
        console.log('   - PDF Upload: .upload-pdf-button');

    });

})(jQuery);