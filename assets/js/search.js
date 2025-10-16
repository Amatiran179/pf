/**
 * Search Overlay - Simple & Clean
 * @package PutraFiber
 */

(function() {
    'use strict';

    // Wait for DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        const searchToggle = document.querySelector('.search-toggle');
        const searchOverlay = document.querySelector('.search-overlay');
        const searchClose = document.querySelector('.search-close');
        const searchBg = document.querySelector('.search-overlay-bg');
        const searchField = document.querySelector('.search-overlay .search-field');

        if (!searchToggle || !searchOverlay) {
            return;
        }

        // Ensure overlay is hidden on page load
        searchOverlay.style.display = 'none';
        searchOverlay.classList.remove('active');

        // Open search overlay
        searchToggle.addEventListener('click', function(e) {
            e.preventDefault();
            openSearch();
        });

        // Close search overlay
        if (searchClose) {
            searchClose.addEventListener('click', function(e) {
                e.preventDefault();
                closeSearch();
            });
        }

        // Close on background click
        if (searchBg) {
            searchBg.addEventListener('click', closeSearch);
        }

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && searchOverlay.classList.contains('active')) {
                closeSearch();
            }
        });

        // Functions
        function openSearch() {
            searchOverlay.style.display = 'block';
            document.body.style.overflow = 'hidden';

            // Trigger animation
            setTimeout(function() {
                searchOverlay.classList.add('active');
            }, 10);

            // Focus search field
            setTimeout(function() {
                if (searchField) {
                    searchField.focus();
                }
            }, 300);
        }

        function closeSearch() {
            searchOverlay.classList.remove('active');
            document.body.style.overflow = '';
            
            // Hide after animation
            setTimeout(function() {
                searchOverlay.style.display = 'none';
            }, 300);
        }
    }

})();