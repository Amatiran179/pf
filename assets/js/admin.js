(function($) {
    'use strict';

    $(document).ready(function() {

        // Media Upload
        var mediaUploader;

        $('.putrafiber-upload-image, .putrafiber-upload-icon').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var inputField = button.siblings('input[type="hidden"]');
            var previewContainer = button.siblings('.image-preview, .icon-preview');

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: 'Select Image',
                button: {
                    text: 'Use this image'
                },
                multiple: false
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                inputField.val(attachment.url);
                previewContainer.html('<img src="' + attachment.url + '" style="max-width: 300px;">');
            });

            mediaUploader.open();
        });

        // Remove Image
        $('.putrafiber-remove-image, .putrafiber-remove-icon').on('click', function(e) {
            e.preventDefault();
            $(this).siblings('input[type="hidden"]').val('');
            $(this).siblings('.image-preview, .icon-preview').html('');
        });

        // Gallery Upload
        var galleryUploader;

        $('.portfolio-gallery-upload').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);

            if (galleryUploader) {
                galleryUploader.open();
                return;
            }

            galleryUploader = wp.media({
                title: 'Select Gallery Images',
                button: {
                    text: 'Add to Gallery'
                },
                multiple: true
            });

            galleryUploader.on('select', function() {
                var attachments = galleryUploader.state().get('selection').toJSON();
                var ids = [];
                var html = '';

                attachments.forEach(function(attachment) {
                    ids.push(attachment.id);
                    html += '<img src="' + attachment.url + '" style="width: 100px; height: 100px; object-fit: cover; margin: 5px;">';
                });

                $('#portfolio_gallery').val(ids.join(','));
                $('.portfolio-gallery-preview').html(html);
            });

            galleryUploader.open();
        });

        // Tab Navigation
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            
            var tabId = $(this).attr('href');
            
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            $('.tab-content').removeClass('active');
            $(tabId).addClass('active');
        });

        // Character Counter for SEO
        function updateCharacterCount() {
            var titleLength = $('#meta_title').val().length;
            var descLength = $('#meta_description').val().length;
            
            $('#title-length').text(titleLength);
            $('#desc-length').text(descLength);
            
            // Color coding
            if (titleLength > 60) {
                $('#title-length').css('color', '#dc3545');
            } else if (titleLength > 50) {
                $('#title-length').css('color', '#ffc107');
            } else {
                $('#title-length').css('color', '#28a745');
            }
            
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

        // Form Validation
        $('form').on('submit', function() {
            var isValid = true;
            
            $(this).find('[required]').each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).addClass('error');
                    $(this).after('<span class="error-message">This field is required</span>');
                } else {
                    $(this).removeClass('error');
                    $(this).siblings('.error-message').remove();
                }
            });
            
            if (!isValid) {
                alert('Please fill in all required fields');
            }
            
            return isValid;
        });

        // Color Picker
        if ($.fn.wpColorPicker) {
            $('.color-picker').wpColorPicker();
        }

        // Sortable
        if ($.fn.sortable) {
            $('.sortable-list').sortable({
                placeholder: 'sortable-placeholder',
                update: function(event, ui) {
                    var order = $(this).sortable('toArray');
                    console.log('New order:', order);
                }
            });
        }

        // Confirm Delete
        $('.delete-item').on('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });

        // Auto-save Draft
        var autoSaveTimer;
        $('.auto-save-field').on('input', function() {
            clearTimeout(autoSaveTimer);
            
            autoSaveTimer = setTimeout(function() {
                // Auto-save logic here
                console.log('Auto-saving...');
            }, 2000);
        });

        // Tooltips
        if ($.fn.tooltip) {
            $('[data-tooltip]').tooltip();
        }

    });

})(jQuery);
