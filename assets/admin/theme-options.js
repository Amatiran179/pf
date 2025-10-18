(function () {
  'use strict';

  function dispatchFieldChange(element) {
    if (!element) {
      return;
    }

    element.dispatchEvent(new Event('input', { bubbles: true }));
    element.dispatchEvent(new Event('change', { bubbles: true }));
  }

  document.addEventListener('DOMContentLoaded', function () {
    initSectionsBuilder();
    initCardBuilders();
    initColorPresets();
  });

  function initSectionsBuilder() {
    var builderEl = document.querySelector('[data-pf-sections-builder]');
    if (!builderEl) {
      return;
    }

    var listEl = builderEl.querySelector('.pf-sections-builder__list');
    var addButton = builderEl.querySelector('.pf-sections-builder__add');
    var builderInput = builderEl.querySelector('input[name="putrafiber_options[front_sections_builder]"]');
    var orderInput = builderEl.querySelector('input[name="putrafiber_options[front_sections_order]"]');
    var catalog = parseJSON(builderEl.getAttribute('data-catalog')) || {};
    var defaultState = parseJSON(builderEl.getAttribute('data-default-state')) || {};
    var customLabel = builderEl.getAttribute('data-custom-label') || 'Section Kustom';
    var storedSections = parseJSON(builderEl.getAttribute('data-sections')) || [];
    var legacyOrder = orderInput && orderInput.value ? orderInput.value : '';

    var sections = normaliseSections(storedSections);
    if (!sections.length) {
      sections = buildFallbackSections();
    }

    var sortableInstance = null;
    var allowedLayouts = ['full', 'split-left', 'split-right'];
    var allowedHeadingTags = ['h2', 'h3', 'h4'];
    var mediaFrame = null;

    renderAll();
    updateInput();
    syncLegacyCheckboxes();

    if (addButton) {
      addButton.addEventListener('click', function (event) {
        event.preventDefault();
        sections.push(createCustomSection());
        renderAll();
        updateOutputs();
      });
    }

    if (listEl) {
      listEl.addEventListener('change', handleListChange);
      listEl.addEventListener('input', handleListInput);
      listEl.addEventListener('click', handleListClick);
    }

    function parseBool(value) {
      return value === true || value === '1' || value === 1;
    }

    function normaliseLayout(value) {
      var layout = (value || '').toString().toLowerCase();
      return allowedLayouts.indexOf(layout) !== -1 ? layout : 'full';
    }

    function normaliseHeadingTag(value) {
      var tag = (value || '').toString().toLowerCase();
      return allowedHeadingTags.indexOf(tag) !== -1 ? tag : 'h2';
    }

    function openMediaPicker(onSelect) {
      if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
        window.alert('WordPress media library belum siap. Silakan muat ulang halaman.');
        return;
      }

      if (mediaFrame && typeof mediaFrame.off === 'function') {
        mediaFrame.off('select');
      }

      mediaFrame = wp.media({
        title: 'Pilih gambar section',
        button: { text: 'Gunakan gambar ini' },
        multiple: false
      });

      mediaFrame.on('select', function () {
        if (typeof onSelect !== 'function') {
          return;
        }

        var attachment = mediaFrame.state().get('selection').first();
        if (!attachment) {
          onSelect(null);
          return;
        }

        onSelect(attachment.toJSON());
      });

      mediaFrame.open();
    }

    function normaliseSections(items) {
      if (!Array.isArray(items)) {
        return [];
      }

      return items.reduce(function (acc, item) {
        if (!item || typeof item !== 'object') {
          return acc;
        }

        var id = typeof item.id === 'string' ? item.id : '';
        if (!id) {
          return acc;
        }

        var type = item.type === 'custom' ? 'custom' : 'core';
        var base = {
          id: id,
          type: type,
          enabled: parseBool(item.enabled)
        };

        if (type === 'core') {
          var meta = catalog[id] || {};
          base.label = meta.label || item.label || id;
          base.description = meta.description || item.description || '';
        } else {
          base.label = item.label || customLabel;
          base.title = item.title || '';
          base.subtitle = item.subtitle || '';
          base.content = item.content || '';
          base.background = item.background || '';
          base.text_color = item.text_color || '';
          base.button_text = item.button_text || '';
          base.button_url = item.button_url || '';
          base.layout = normaliseLayout(item.layout);
          base.media = typeof item.media === 'string' ? item.media : '';
          base.media_alt = typeof item.media_alt === 'string' ? item.media_alt : '';
          base.anchor = typeof item.anchor === 'string' ? slugify(item.anchor) : '';
          base.heading_tag = normaliseHeadingTag(item.heading_tag);
        }

        acc.push(base);
        return acc;
      }, []);
    }

    function buildFallbackSections() {
      var fallback = [];
      var added = [];
      var orderPieces = [];

      if (legacyOrder) {
        legacyOrder.split(',').forEach(function (slug) {
          var trimmed = slug.trim();
          if (trimmed && orderPieces.indexOf(trimmed) === -1) {
            orderPieces.push(trimmed);
          }
        });
      }

      if (!orderPieces.length) {
        Object.keys(defaultState).forEach(function (slug) {
          if (orderPieces.indexOf(slug) === -1) {
            orderPieces.push(slug);
          }
        });
      }

      if (!orderPieces.length) {
        Object.keys(catalog).forEach(function (slug) {
          if (orderPieces.indexOf(slug) === -1) {
            orderPieces.push(slug);
          }
        });
      }

      function pushSlug(slug) {
        if (!slug || added.indexOf(slug) !== -1) {
          return;
        }

        added.push(slug);
        var meta = catalog[slug] || {};
        var state = defaultState[slug] || {};
        var isEnabled = typeof state.enabled !== 'undefined' ? parseBool(state.enabled) : true;

        fallback.push({
          id: slug,
          type: 'core',
          label: meta.label || slug,
          description: meta.description || '',
          enabled: isEnabled
        });
      }

      orderPieces.forEach(pushSlug);
      Object.keys(defaultState).forEach(pushSlug);
      Object.keys(catalog).forEach(pushSlug);

      return fallback;
    }

    function createCustomSection() {
      return {
        id: generateUniqueId(),
        type: 'custom',
        label: customLabel,
        title: '',
        subtitle: '',
        content: '',
        background: '',
        text_color: '',
        button_text: '',
        button_url: '',
        layout: 'full',
        media: '',
        media_alt: '',
        anchor: '',
        heading_tag: 'h2',
        enabled: true
      };
    }

    function generateUniqueId() {
      var base = 'custom-' + Date.now().toString(36);
      var candidate = base;
      var suffix = 1;
      var existing = sections.map(function (section) { return section.id; });

      while (existing.indexOf(candidate) !== -1) {
        candidate = base + '-' + suffix;
        suffix += 1;
      }

      return candidate;
    }

    function renderAll() {
      if (!listEl) {
        return;
      }

      listEl.innerHTML = '';

      sections.forEach(function (section, index) {
        listEl.appendChild(buildCard(section, index));
      });

      initColorPickers(listEl);

      initSortable();
      syncLegacyCheckboxes();
      updateOutputs();
    }

    function buildCard(section, index) {
      var card = document.createElement('div');
      card.className = 'pf-section-card';
      if (section.type === 'custom') {
        card.classList.add('pf-section-card--custom');
      }
      card.dataset.index = index;
      card.dataset.id = section.id;
      card.dataset.type = section.type;

      var descriptionHtml = '';
      if (section.type === 'core' && section.description) {
        descriptionHtml = '<span class="pf-section-card__description">' + escapeHtml(section.description) + '</span>';
      }

      var toggleText = section.enabled ? 'Aktif' : 'Nonaktif';

      card.innerHTML = '' +
        '<div class="pf-section-card__header">' +
          '<button type="button" class="pf-section-card__handle" aria-label="Pindahkan section">' +
            '<span class="dashicons dashicons-move"></span>' +
          '</button>' +
          '<div class="pf-section-card__heading">' +
            '<span class="pf-section-card__name">' + escapeHtml(section.label || section.id) + '</span>' +
            descriptionHtml +
          '</div>' +
          '<label class="pf-section-card__toggle">' +
            '<input type="checkbox" data-action="toggle" ' + (section.enabled ? 'checked' : '') + '>' +
            '<span data-role="toggle-label">' + toggleText + '</span>' +
          '</label>' +
        '</div>';

      card.dataset.layout = normaliseLayout(section.layout);

      if (section.type === 'custom') {
        var previewMarkup = '';
        var previewAlt = section.media_alt || section.title || section.label || '';
        if (section.media) {
          previewMarkup = '<img src="' + escapeAttr(section.media) + '" alt="' + escapeAttr(previewAlt) + '" decoding="async" loading="lazy">';
        } else {
          previewMarkup = '<span class="pf-section-media__placeholder">Belum ada gambar</span>';
        }

        card.innerHTML += '' +
          '<div class="pf-section-card__body">' +
            '<div class="pf-section-field">' +
              '<label>Judul Section</label>' +
              '<input type="text" data-field="title" value="' + escapeAttr(section.title || '') + '">' +
            '</div>' +
            '<div class="pf-section-field">' +
              '<label>Subjudul</label>' +
              '<input type="text" data-field="subtitle" value="' + escapeAttr(section.subtitle || '') + '">' +
            '</div>' +
            '<div class="pf-section-field">' +
              '<label>Konten</label>' +
              '<textarea rows="4" data-field="content">' + escapeTextarea(section.content || '') + '</textarea>' +
              '<p class="description">Gunakan enter untuk paragraf baru. HTML sederhana diperbolehkan.</p>' +
            '</div>' +
            '<div class="pf-section-field pf-section-field--inline">' +
              '<label>Tata Letak</label>' +
              '<select data-field="layout">' +
                '<option value="full"' + (section.layout === 'full' ? ' selected' : '') + '>Konten penuh</option>' +
                '<option value="split-left"' + (section.layout === 'split-left' ? ' selected' : '') + '>Gambar kiri, konten kanan</option>' +
                '<option value="split-right"' + (section.layout === 'split-right' ? ' selected' : '') + '>Konten kiri, gambar kanan</option>' +
              '</select>' +
            '</div>' +
            '<div class="pf-section-field">' +
              '<label>Gambar pendukung</label>' +
              '<div class="pf-section-media" data-role="media-fields">' +
                '<div class="pf-section-media__preview" data-role="media-preview">' + previewMarkup + '</div>' +
                '<div class="pf-section-media__actions">' +
                  '<button type="button" class="button pf-section-media__upload" data-action="media-upload">Pilih gambar</button>' +
                  '<button type="button" class="button-link-delete pf-section-media__remove" data-action="media-remove"' + (section.media ? '' : ' disabled') + '>Hapus gambar</button>' +
                '</div>' +
                '<input type="hidden" data-field="media" value="' + escapeAttr(section.media || '') + '">' +
              '</div>' +
              '<p class="description">Direkomendasikan ukuran minimal 1200x800px agar tampilan tetap tajam.</p>' +
            '</div>' +
            '<div class="pf-section-field pf-section-field--inline">' +
              '<label>Alt text gambar</label>' +
              '<input type="text" data-field="media_alt" value="' + escapeAttr(section.media_alt || '') + '">' +
            '</div>' +
            '<div class="pf-section-field pf-section-field--inline">' +
              '<label>ID Anchor</label>' +
              '<input type="text" data-field="anchor" placeholder="contoh: keunggulan" value="' + escapeAttr(section.anchor || '') + '">' +
              '<p class="description">Opsional: gunakan huruf kecil tanpa spasi untuk navigasi cepat (misal #keunggulan).</p>' +
            '</div>' +
            '<div class="pf-section-field pf-section-field--inline">' +
              '<label>Heading SEO</label>' +
              '<select data-field="heading_tag">' +
                '<option value="h2"' + (section.heading_tag === 'h2' ? ' selected' : '') + '>H2</option>' +
                '<option value="h3"' + (section.heading_tag === 'h3' ? ' selected' : '') + '>H3</option>' +
                '<option value="h4"' + (section.heading_tag === 'h4' ? ' selected' : '') + '>H4</option>' +
              '</select>' +
            '</div>' +
            '<div class="pf-section-field pf-section-field--inline">' +
              '<label>Warna latar</label>' +
              '<input type="text" data-field="background" placeholder="#0f75ff" value="' + escapeAttr(section.background || '') + '">' +
            '</div>' +
            '<div class="pf-section-field pf-section-field--inline">' +
              '<label>Warna teks</label>' +
              '<input type="text" data-field="text_color" placeholder="#ffffff" value="' + escapeAttr(section.text_color || '') + '">' +
            '</div>' +
            '<div class="pf-section-field pf-section-field--inline">' +
              '<label>Teks tombol</label>' +
              '<input type="text" data-field="button_text" value="' + escapeAttr(section.button_text || '') + '">' +
            '</div>' +
            '<div class="pf-section-field">' +
              '<label>URL tombol</label>' +
              '<input type="url" data-field="button_url" placeholder="https://" value="' + escapeAttr(section.button_url || '') + '">' +
            '</div>' +
          '</div>' +
          '<div class="pf-section-card__footer">' +
            '<button type="button" class="button-link-delete" data-action="remove">Hapus section</button>' +
          '</div>';
      }

      return card;
    }

    function initSortable() {
      if (typeof Sortable === 'undefined' || !listEl) {
        return;
      }

      if (sortableInstance) {
        sortableInstance.destroy();
      }

      sortableInstance = Sortable.create(listEl, {
        animation: 160,
        handle: '.pf-section-card__handle',
        onEnd: function (evt) {
          if (evt.oldIndex === evt.newIndex) {
            syncCardIndices();
            return;
          }

          var moved = sections.splice(evt.oldIndex, 1)[0];
          sections.splice(evt.newIndex, 0, moved);
          syncCardIndices();
          updateOutputs();
        }
      });
    }

    function syncCardIndices() {
      if (!listEl) {
        return;
      }

      Array.prototype.forEach.call(listEl.children, function (card, index) {
        card.dataset.index = index;
      });
    }

    function handleListChange(event) {
      var target = event.target;
      if (!target) {
        return;
      }

      if (target.getAttribute('data-action') === 'toggle') {
        var card = target.closest('.pf-section-card');
        if (!card) {
          return;
        }

        var index = parseInt(card.dataset.index, 10);
        if (isNaN(index) || !sections[index]) {
          return;
        }

        sections[index].enabled = target.checked;
        var label = card.querySelector('[data-role="toggle-label"]');
        if (label) {
          label.textContent = target.checked ? 'Aktif' : 'Nonaktif';
        }

        if (sections[index].type === 'core') {
          syncLegacyCheckbox(sections[index].id, sections[index].enabled);
        }

        updateOutputs();
      }
    }

    function handleListInput(event) {
      var target = event.target;
      if (!target) {
        return;
      }

      var card = target.closest('.pf-section-card');
      if (!card) {
        return;
      }

      var index = parseInt(card.dataset.index, 10);
      if (isNaN(index) || !sections[index]) {
        return;
      }

      var field = target.getAttribute('data-field');
      if (!field) {
        return;
      }

      var newValue = target.value;

      if (field === 'layout') {
        newValue = normaliseLayout(newValue);
        target.value = newValue;
        card.setAttribute('data-layout', newValue);
      } else if (field === 'anchor') {
        newValue = slugify(newValue);
        target.value = newValue;
      } else if (field === 'heading_tag') {
        newValue = normaliseHeadingTag(newValue);
        target.value = newValue;
      }

      sections[index][field] = newValue;

      if (sections[index].type === 'custom') {
        if (field === 'title') {
          sections[index].label = newValue || customLabel;
          var nameEl = card.querySelector('.pf-section-card__name');
          if (nameEl) {
            nameEl.textContent = newValue ? newValue : customLabel;
          }
        } else if (field === 'media') {
          var preview = card.querySelector('[data-role="media-preview"]');
          if (preview) {
            if (newValue) {
              preview.innerHTML = '<img src="' + escapeAttr(newValue) + '" alt="' + escapeAttr(sections[index].media_alt || sections[index].title || sections[index].label || '') + '" decoding="async" loading="lazy">';
            } else {
              preview.innerHTML = '<span class="pf-section-media__placeholder">Belum ada gambar</span>';
            }
          }

          var removeButton = card.querySelector('[data-action="media-remove"]');
          if (removeButton) {
            removeButton.disabled = !newValue;
          }
        } else if (field === 'media_alt') {
          var previewImg = card.querySelector('[data-role="media-preview"] img');
          if (previewImg) {
            previewImg.alt = newValue;
          }
        }
      }

      updateOutputs();
    }

    function handleListClick(event) {
      var target = event.target;
      if (!target) {
        return;
      }

      var action = target.getAttribute('data-action');
      if (!action) {
        return;
      }

      var card = target.closest('.pf-section-card');
      if (!card) {
        return;
      }

      var index = parseInt(card.dataset.index, 10);
      if (isNaN(index) || !sections[index]) {
        return;
      }

      if (action === 'remove') {
        event.preventDefault();
        if (window.confirm('Hapus section kustom ini?')) {
          sections.splice(index, 1);
          renderAll();
          updateOutputs();
        }
        return;
      }

      if (sections[index].type !== 'custom') {
        return;
      }

      if (action === 'media-upload') {
        event.preventDefault();
        openMediaPicker(function (attachment) {
          if (!attachment) {
            return;
          }

          var mediaUrl = attachment.url || '';
          var mediaAlt = attachment.alt || attachment.title || '';

          sections[index].media = mediaUrl;
          if (!sections[index].media_alt && mediaAlt) {
            sections[index].media_alt = mediaAlt;
            var altInput = card.querySelector('input[data-field="media_alt"]');
            if (altInput && !altInput.value) {
              altInput.value = mediaAlt;
            }
          }

          var preview = card.querySelector('[data-role="media-preview"]');
          if (preview) {
            if (mediaUrl) {
              preview.innerHTML = '<img src="' + escapeAttr(mediaUrl) + '" alt="' + escapeAttr(sections[index].media_alt || sections[index].title || sections[index].label || '') + '" decoding="async" loading="lazy">';
            } else {
              preview.innerHTML = '<span class="pf-section-media__placeholder">Belum ada gambar</span>';
            }
          }

          var mediaInput = card.querySelector('input[data-field="media"]');
          if (mediaInput) {
            mediaInput.value = mediaUrl;
            dispatchFieldChange(mediaInput);
          }

          var removeButton = card.querySelector('[data-action="media-remove"]');
          if (removeButton) {
            removeButton.disabled = !mediaUrl;
          }

          var altInputField = card.querySelector('input[data-field="media_alt"]');
          if (altInputField) {
            dispatchFieldChange(altInputField);
          }
        });
      } else if (action === 'media-remove') {
        event.preventDefault();
        sections[index].media = '';

        var hiddenInput = card.querySelector('input[data-field="media"]');
        if (hiddenInput) {
          hiddenInput.value = '';
          dispatchFieldChange(hiddenInput);
        }

        var previewEl = card.querySelector('[data-role="media-preview"]');
        if (previewEl) {
          previewEl.innerHTML = '<span class="pf-section-media__placeholder">Belum ada gambar</span>';
        }

        var removeBtn = card.querySelector('[data-action="media-remove"]');
        if (removeBtn) {
          removeBtn.disabled = true;
        }
      }
    }

    function syncLegacyCheckbox(slug, enabled) {
      var checkbox = document.querySelector('input[name="putrafiber_options[enable_' + slug + '_section]"]');
      if (checkbox) {
        checkbox.checked = !!enabled;
      }
    }

    function syncLegacyCheckboxes() {
      sections.forEach(function (section) {
        if (section.type === 'core') {
          syncLegacyCheckbox(section.id, section.enabled);
        }
      });
    }

    function initColorPickers(scope) {
      if (typeof jQuery === 'undefined' || !jQuery.fn || typeof jQuery.fn.wpColorPicker !== 'function') {
        return;
      }

      var $scope = jQuery(scope || document.body);
      $scope.find('input[data-field="background"], input[data-field="text_color"]').each(function () {
        var $input = jQuery(this);
        if ($input.hasClass('pf-color-picker-ready')) {
          return;
        }

        $input.addClass('pf-color-picker-ready');
        $input.wpColorPicker({
          change: function (event, ui) {
            if (!event.target) {
              return;
            }
            event.target.value = ui.color.toString();
            dispatchFieldChange(event.target);
          },
          clear: function (event) {
            if (!event.target) {
              return;
            }
            dispatchFieldChange(event.target);
          }
        });
      });
    }

    function updateOutputs() {
      if (builderInput) {
        builderInput.value = JSON.stringify(sections);
        dispatchFieldChange(builderInput);
      }

      if (orderInput) {
        var enabledIds = sections.filter(function (section) { return section.enabled; }).map(function (section) { return section.id; });
        orderInput.value = enabledIds.join(',');
        dispatchFieldChange(orderInput);
      }
    }
  }

  function initCardBuilders() {
    var builders = document.querySelectorAll('[data-pf-card-builder]');
    if (!builders.length) {
      return;
    }

    Array.prototype.forEach.call(builders, function (builderEl) {
      createCardBuilder(builderEl);
    });
  }

  function createCardBuilder(builderEl) {
    var listEl = builderEl.querySelector('.pf-card-builder__list');
    var addButton = builderEl.querySelector('.pf-card-builder__add');
    var importButton = builderEl.querySelector('.pf-card-builder__import');
    var inputEl = builderEl.querySelector('input[type="hidden"][name]');
    var config = parseJSON(builderEl.getAttribute('data-config')) || {};
    var section = config.section || 'features';
    var allowedFields = Array.isArray(config.fields) ? config.fields : [];
    var cards = normaliseCards(readStoredCards());
    var sortableInstance = null;

    renderAll();

    if (addButton) {
      addButton.addEventListener('click', function (event) {
        event.preventDefault();
        cards.push(createEmptyCard());
        renderAll();
        updateInput();
      });
    }

    if (importButton) {
      importButton.addEventListener('click', function (event) {
        event.preventDefault();
        importLegacy(importButton.getAttribute('data-source'));
      });
    }

    if (listEl) {
      listEl.addEventListener('input', handleFieldEvent);
      listEl.addEventListener('change', handleFieldEvent);
      listEl.addEventListener('click', handleListClick);
    }

    function readStoredCards() {
      if (!inputEl) {
        return [];
      }

      var raw = inputEl.value;
      if (!raw) {
        return [];
      }

      var parsed = parseJSON(raw);
      if (Array.isArray(parsed)) {
        return parsed;
      }

      return [];
    }

    function createEmptyCard() {
      var defaults = {
        title: '',
        subtitle: '',
        description: '',
        icon_type: 'icon',
        icon: '',
        image: '',
        image_alt: '',
        image_size: 'auto',
        badge: '',
        highlight: '',
        list: [],
        list_effect: '',
        accent_color: '',
        background: '',
        text_color: '',
        link_text: '',
        link_url: '',
        button_label: '',
        animation: '',
        custom_class: '',
        excerpt: '',
        category_label: '',
        date_label: '',
        reading_time: '',
        author_label: '',
        position: 0
      };

      var card = {};
      allowedFields.forEach(function (field) {
        if (typeof defaults[field] !== 'undefined') {
          if (Array.isArray(defaults[field])) {
            card[field] = [].concat(defaults[field]);
          } else {
            card[field] = defaults[field];
          }
        } else {
          card[field] = '';
        }
      });

      return card;
    }

    function normaliseCards(initialCards) {
      if (!Array.isArray(initialCards) || !initialCards.length) {
        return [];
      }

      return initialCards.map(function (rawCard) {
        var card = createEmptyCard();
        if (!rawCard || typeof rawCard !== 'object') {
          return card;
        }

        allowedFields.forEach(function (field) {
          if (typeof rawCard[field] === 'undefined') {
            return;
          }

          if (field === 'list') {
            if (Array.isArray(rawCard.list)) {
              card.list = rawCard.list.filter(function (item) {
                return typeof item === 'string' && item.trim() !== '';
              }).map(function (item) {
                return item.trim();
              });
            } else if (typeof rawCard.list === 'string') {
              card.list = rawCard.list.split(/\r?\n/).map(function (item) {
                return item.trim();
              }).filter(function (item) {
                return item !== '';
              });
            }
          } else if (field === 'position') {
            card.position = parseInt(rawCard.position, 10);
            if (isNaN(card.position) || card.position < 0) {
              card.position = 0;
            }
          } else {
            card[field] = rawCard[field];
          }
        });

        return card;
      });
    }

    function renderAll() {
      if (!listEl) {
        return;
      }

      listEl.innerHTML = '';

      if (!cards.length) {
        var empty = document.createElement('p');
        empty.className = 'pf-card-builder__empty';
        empty.textContent = builderEmptyMessage();
        listEl.appendChild(empty);
        destroySortable();
        return;
      }

      cards.forEach(function (card, index) {
        listEl.appendChild(buildCardItem(card, index));
      });

      initSortable();
    }

    function builderEmptyMessage() {
      switch (section) {
        case 'services':
          return 'Belum ada kartu layanan. Tambahkan dari tombol di bawah.';
        case 'blog':
          return 'Belum ada artikel khusus. Tambah kartu untuk menampilkan konten manual.';
        default:
          return 'Belum ada kartu fitur. Klik tombol tambah untuk memulai.';
      }
    }

    function buildCardItem(card, index) {
      var item = document.createElement('div');
      item.className = 'pf-card-item';
      item.dataset.index = index;
      if (card.icon_type) {
        item.setAttribute('data-icon-type', card.icon_type);
      }

      var header = document.createElement('div');
      header.className = 'pf-card-item__header';
      header.innerHTML = '' +
        '<button type="button" class="pf-card-item__handle" aria-label="Pindahkan kartu">' +
          '<span class="dashicons dashicons-move"></span>' +
        '</button>' +
        '<div class="pf-card-item__summary">' +
          '<span class="pf-card-item__title">' + escapeHtml(cardTitle(card, index)) + '</span>' +
          '<span class="pf-card-item__meta">' + escapeHtml(cardMeta(card)) + '</span>' +
        '</div>' +
        '<button type="button" class="button-link-delete pf-card-item__remove" data-action="remove-card">Hapus</button>';
      item.appendChild(header);

      var body = document.createElement('div');
      body.className = 'pf-card-item__body';
      body.innerHTML = buildFieldsHtml(card);
      item.appendChild(body);

      return item;
    }

    function cardTitle(card, index) {
      if (card && card.title) {
        return card.title;
      }

      if (section === 'blog' && card && card.subtitle) {
        return card.subtitle;
      }

      return 'Kartu ' + (index + 1);
    }

    function cardMeta(card) {
      if (section === 'blog') {
        if (card && card.date_label) {
          return card.date_label;
        }
        if (typeof card.position !== 'undefined' && card.position > 0) {
          return 'Posisi #' + card.position;
        }
        return 'Artikel landing page';
      }

      if (card && card.icon_type === 'image') {
        return 'Gambar custom';
      }
      if (card && card.icon_type === 'image-large') {
        return 'Gambar lebar';
      }

      return 'Ikon: ' + (card.icon || 'default');
    }

    function buildFieldsHtml(card) {
      return allowedFields.map(function (field) {
        return buildField(field, card);
      }).join('');
    }

    function buildField(field, card) {
      var value = typeof card[field] !== 'undefined' ? card[field] : '';
      var meta = getFieldMeta(field);
      var description = meta.description ? '<p class="description">' + meta.description + '</p>' : '';

      if (field === 'list' && Array.isArray(value)) {
        value = value.join('\n');
      }

      if (meta.type === 'textarea') {
        return '' +
          '<div class="pf-card-field">' +
            '<label>' + meta.label + '</label>' +
          '<textarea rows="' + meta.rows + '" data-field="' + field + '" data-field-type="textarea" placeholder="' + escapeAttr(meta.placeholder || '') + '">' + escapeTextarea(value || '') + '</textarea>' +
            description +
          '</div>';
      }

      if (meta.type === 'select') {
        var options = meta.options.map(function (option) {
          var selected = option.value === value ? ' selected' : '';
          return '<option value="' + escapeAttr(option.value) + '"' + selected + '>' + option.label + '</option>';
        }).join('');
        return '' +
          '<div class="pf-card-field">' +
            '<label>' + meta.label + '</label>' +
            '<select data-field="' + field + '">' + options + '</select>' +
            description +
          '</div>';
      }

      if (meta.type === 'media') {
        var preview = value ? '<div class="pf-card-media__preview"><img src="' + escapeAttr(value) + '" alt=""></div>' : '<div class="pf-card-media__preview pf-card-media__preview--empty">Belum ada gambar</div>';
        return '' +
          '<div class="pf-card-field pf-card-field--media">' +
            '<label>' + meta.label + '</label>' +
            preview +
            '<div class="pf-card-media__actions">' +
              '<button type="button" class="button pf-card-media__choose" data-action="choose-media">Pilih gambar</button>' +
              '<button type="button" class="button-link pf-card-media__remove" data-action="clear-media">Hapus</button>' +
            '</div>' +
            '<input type="hidden" data-field="' + field + '" value="' + escapeAttr(value || '') + '">' +
            description +
          '</div>';
      }

      if (meta.type === 'number') {
        return '' +
          '<div class="pf-card-field pf-card-field--inline">' +
            '<label>' + meta.label + '</label>' +
            '<input type="number" data-field="' + field + '" data-field-type="number" min="' + meta.min + '" value="' + escapeAttr(value || 0) + '" placeholder="' + escapeAttr(meta.placeholder || '') + '">' +
            description +
          '</div>';
      }

      var inputType = meta.type === 'url' ? 'url' : 'text';
      return '' +
        '<div class="pf-card-field' + (meta.inline ? ' pf-card-field--inline' : '') + '">' +
          '<label>' + meta.label + '</label>' +
          '<input type="' + inputType + '" data-field="' + field + '" value="' + escapeAttr(value || '') + '" placeholder="' + escapeAttr(meta.placeholder || '') + '">' +
          description +
        '</div>';
    }

    function getFieldMeta(field) {
      var meta = {
        label: field,
        type: 'text',
        placeholder: ''
      };

      switch (field) {
        case 'title':
          meta.label = 'Judul';
          meta.placeholder = 'Contoh: Water Adventure';
          break;
        case 'subtitle':
          meta.label = 'Subjudul';
          meta.placeholder = 'Opsional, muncul di bawah judul';
          break;
        case 'description':
          meta.label = 'Deskripsi';
          meta.type = 'textarea';
          meta.rows = 4;
          meta.placeholder = 'Tuliskan detail manfaat atau isi utama.';
          meta.description = 'HTML dasar (strong, em, ul, ol, a) diperbolehkan.';
          break;
        case 'excerpt':
          meta.label = 'Ringkasan';
          meta.type = 'textarea';
          meta.rows = 4;
          meta.placeholder = 'Ringkasan artikel custom.';
          meta.description = 'Gunakan enter untuk paragraf baru.';
          break;
        case 'icon_type':
          meta.label = 'Jenis media';
          meta.type = 'select';
          meta.options = [
            { value: 'icon', label: 'Gunakan ikon bawaan' },
            { value: 'image', label: 'Gambar custom (ukuran fleksibel)' },
            { value: 'image-large', label: 'Gambar besar (melebar)' }
          ];
          meta.description = 'Pilih apakah kartu menampilkan ikon atau gambar.';
          break;
        case 'icon':
          meta.label = 'Nama ikon';
          meta.placeholder = 'Misal: spark, wave, gear';
          meta.description = 'Gunakan daftar ikon tema seperti spark, drop, shield, wave.';
          break;
        case 'image':
          meta.label = 'Gambar';
          meta.type = 'media';
          meta.description = 'Gambar akan menggantikan ikon. Resolusi tinggi direkomendasikan.';
          break;
        case 'image_alt':
          meta.label = 'Alt text gambar';
          meta.placeholder = 'Deskripsi singkat gambar';
          meta.description = 'Wajib diisi untuk aksesibilitas.';
          break;
        case 'image_size':
          meta.label = 'Ukuran media';
          meta.type = 'select';
          meta.options = [
            { value: 'auto', label: 'Otomatis' },
            { value: 'small', label: 'Kecil' },
            { value: 'medium', label: 'Sedang' },
            { value: 'large', label: 'Besar' },
            { value: 'cover', label: 'Cover (isi penuh)' },
            { value: 'contain', label: 'Contain' },
            { value: 'wide', label: 'Lebar' },
            { value: 'tall', label: 'Tinggi' },
            { value: 'square', label: 'Persegi' },
            { value: 'circle', label: 'Lingkaran' }
          ];
          break;
        case 'badge':
          meta.label = 'Badge/Label kecil';
          meta.placeholder = 'Contoh: Favorit';
          meta.description = 'Muncul di pojok atas kartu.';
          break;
        case 'highlight':
          meta.label = 'Highlight';
          meta.placeholder = 'Kalimat pendek yang menonjol';
          break;
        case 'list':
          meta.label = 'Bullet list';
          meta.type = 'textarea';
          meta.rows = 3;
          meta.placeholder = 'Tulis satu poin per baris';
          meta.description = 'Setiap baris akan menjadi bullet terpisah.';
          break;
        case 'list_effect':
          meta.label = 'Gaya bullet';
          meta.type = 'select';
          meta.options = [
            { value: '', label: 'Default' },
            { value: 'check', label: 'Checklist hijau' },
            { value: 'spark', label: 'Spark animasi' },
            { value: 'wave', label: 'Gelombang' },
            { value: 'bullet', label: 'Bullet klasik' },
            { value: 'arrow', label: 'Panah dinamis' }
          ];
          break;
        case 'accent_color':
          meta.label = 'Warna aksen';
          meta.placeholder = '#f4c542 atau rgba(244,197,66,0.8)';
          meta.description = 'Digunakan untuk garis hias atau icon.';
          break;
        case 'background':
          meta.label = 'Warna latar kartu';
          meta.placeholder = 'Contoh: #ffffff atau linear-gradient(...)';
          meta.description = 'Kosongkan untuk menggunakan default tema.';
          break;
        case 'text_color':
          meta.label = 'Warna teks';
          meta.placeholder = '#0b1320';
          break;
        case 'link_text':
          meta.label = 'Teks tautan';
          meta.placeholder = 'Contoh: Pelajari selengkapnya';
          break;
        case 'link_url':
          meta.label = 'URL tautan';
          meta.type = 'url';
          meta.placeholder = 'https://';
          break;
        case 'button_label':
          meta.label = 'Label tombol';
          meta.placeholder = 'Contoh: Konsultasi';
          break;
        case 'animation':
          meta.label = 'Animasi kartu';
          meta.type = 'select';
          meta.options = [
            { value: '', label: 'Ikuti pengaturan global' },
            { value: 'auto', label: 'Otomatis (acak)' },
            { value: 'rise', label: 'Melayang naik' },
            { value: 'zoom', label: 'Zoom lembut' },
            { value: 'tilt', label: 'Tilt dinamis' },
            { value: 'float', label: 'Mengambang' },
            { value: 'pulse', label: 'Pulse' },
            { value: 'fade', label: 'Fade in' },
            { value: 'slide', label: 'Slide kanan' },
            { value: 'none', label: 'Tanpa animasi' }
          ];
          break;
        case 'custom_class':
          meta.label = 'Kelas CSS khusus';
          meta.placeholder = 'pisahkan dengan spasi';
          meta.description = 'Gunakan untuk styling lanjutan.';
          break;
        case 'category_label':
          meta.label = 'Label kategori';
          meta.placeholder = 'Misal: Insight, Tips';
          break;
        case 'date_label':
          meta.label = 'Label tanggal';
          meta.placeholder = 'Format bebas, misal Januari 2024';
          break;
        case 'reading_time':
          meta.label = 'Waktu baca';
          meta.placeholder = 'Contoh: 4 menit';
          break;
        case 'author_label':
          meta.label = 'Penulis';
          meta.placeholder = 'Nama penulis atau tim';
          break;
        case 'position':
          meta.label = 'Posisi khusus';
          meta.type = 'number';
          meta.min = 0;
          meta.placeholder = '0';
          meta.description = 'Nilai 1 akan menempatkan kartu sebelum artikel WordPress pertama.';
          break;
      }

      if (field === 'accent_color' || field === 'background' || field === 'text_color') {
        meta.inline = true;
      }

      return meta;
    }

    function handleFieldEvent(event) {
      var target = event.target;
      if (!target || !target.getAttribute('data-field')) {
        return;
      }

      var cardEl = target.closest('.pf-card-item');
      if (!cardEl) {
        return;
      }

      var index = parseInt(cardEl.getAttribute('data-index'), 10);
      if (isNaN(index) || !cards[index]) {
        return;
      }

      var field = target.getAttribute('data-field');
      var fieldType = target.getAttribute('data-field-type') || target.tagName.toLowerCase();
      var value = target.value;

      if (fieldType === 'textarea' && field === 'list') {
        cards[index][field] = value.split(/\r?\n/).map(function (line) {
          return line.trim();
        }).filter(function (line) {
          return line !== '';
        });
      } else if (fieldType === 'number') {
        var parsed = parseInt(value, 10);
        if (isNaN(parsed) || parsed < 0) {
          parsed = 0;
        }
        cards[index][field] = parsed;
        target.value = parsed;
      } else if (field === 'list') {
        cards[index][field] = value.split(/\r?\n/).map(function (line) {
          return line.trim();
        }).filter(function (line) {
          return line !== '';
        });
      } else {
        cards[index][field] = value;
      }

      if (field === 'title' || (section === 'blog' && (field === 'date_label' || field === 'position'))) {
        updateSummary(cardEl, cards[index], index);
      }

      if (field === 'icon_type') {
        cardEl.setAttribute('data-icon-type', value);
      }

      updateInput();
    }

    function handleListClick(event) {
      var target = event.target;
      if (!target) {
        return;
      }

      if (target.getAttribute('data-action') === 'remove-card') {
        event.preventDefault();
        var cardEl = target.closest('.pf-card-item');
        if (!cardEl) {
          return;
        }
        var index = parseInt(cardEl.getAttribute('data-index'), 10);
        if (isNaN(index) || !cards[index]) {
          return;
        }
        if (window.confirm('Hapus kartu ini?')) {
          cards.splice(index, 1);
          renderAll();
          updateInput();
        }
        return;
      }

      if (target.getAttribute('data-action') === 'choose-media') {
        event.preventDefault();
        openMediaFrame(target.closest('.pf-card-item'), target);
        return;
      }

      if (target.getAttribute('data-action') === 'clear-media') {
        event.preventDefault();
        var container = target.closest('.pf-card-field');
        if (!container) {
          return;
        }
        var cardEl = target.closest('.pf-card-item');
        var index = parseInt(cardEl.getAttribute('data-index'), 10);
        if (isNaN(index) || !cards[index]) {
          return;
        }
        var hidden = container.querySelector('input[data-field="image"]');
        if (hidden) {
          hidden.value = '';
        }
        var preview = container.querySelector('.pf-card-media__preview');
        if (preview) {
          preview.innerHTML = '';
          preview.classList.add('pf-card-media__preview--empty');
          preview.textContent = 'Belum ada gambar';
        }
        cards[index].image = '';
        updateInput();
      }
    }

    function updateSummary(cardEl, card, index) {
      var titleEl = cardEl.querySelector('.pf-card-item__title');
      if (titleEl) {
        titleEl.textContent = cardTitle(card, index);
      }
      var metaEl = cardEl.querySelector('.pf-card-item__meta');
      if (metaEl) {
        metaEl.textContent = cardMeta(card);
      }
    }

    function updateInput() {
      if (!inputEl) {
        return;
      }

      if (!cards.length) {
        inputEl.value = '[]';
        dispatchFieldChange(inputEl);
        return;
      }

      var payload = cards.map(function (card) {
        var output = {};
        allowedFields.forEach(function (field) {
          if (typeof card[field] === 'undefined') {
            return;
          }
          if (field === 'list') {
            output[field] = Array.isArray(card.list) ? card.list.filter(function (item) {
              return item && item.trim() !== '';
            }).map(function (item) {
              return item.trim();
            }) : [];
          } else if (field === 'position') {
            var position = parseInt(card.position, 10);
            output[field] = isNaN(position) || position < 0 ? 0 : position;
          } else {
            output[field] = card[field];
          }
        });
        return output;
      });

      inputEl.value = JSON.stringify(payload);
      dispatchFieldChange(inputEl);
    }

    function initSortable() {
      if (typeof Sortable === 'undefined' || !listEl) {
        return;
      }

      destroySortable();

      sortableInstance = Sortable.create(listEl, {
        animation: 150,
        handle: '.pf-card-item__handle',
        draggable: '.pf-card-item',
        filter: '.pf-card-builder__empty',
        onEnd: function (evt) {
          if (evt.oldIndex === evt.newIndex) {
            syncCardIndices();
            return;
          }
          var moved = cards.splice(evt.oldIndex, 1)[0];
          cards.splice(evt.newIndex, 0, moved);
          renderAll();
          updateInput();
        }
      });
    }

    function destroySortable() {
      if (sortableInstance) {
        sortableInstance.destroy();
        sortableInstance = null;
      }
    }

    function syncCardIndices() {
      if (!listEl) {
        return;
      }
      Array.prototype.forEach.call(listEl.children, function (child, index) {
        if (child.classList.contains('pf-card-item')) {
          child.dataset.index = index;
        }
      });
    }

    function importLegacy(source) {
      if (!source) {
        return;
      }

      var textarea = document.querySelector('textarea[name="putrafiber_options[front_' + source + '_items]"]');
      if (!textarea) {
        window.alert('Tidak ditemukan data lama untuk dikonversi.');
        return;
      }

      var raw = textarea.value;
      if (!raw) {
        window.alert('Kolom data lama masih kosong.');
        return;
      }

      var lines = raw.split(/\r?\n/);
      var imported = [];

      lines.forEach(function (line) {
        var trimmed = line.trim();
        if (!trimmed) {
          return;
        }
        var parts = trimmed.split('|');
        var title = parts[0] ? parts[0].trim() : '';
        var description = parts[1] ? parts[1].trim() : '';
        var icon = parts[2] ? parts[2].trim() : '';
        if (!title && !description) {
          return;
        }

        var card = createEmptyCard();
        if (allowedFields.indexOf('title') !== -1) {
          card.title = title;
        }
        if (allowedFields.indexOf('description') !== -1) {
          card.description = description;
        }
        if (allowedFields.indexOf('icon') !== -1) {
          card.icon = icon;
        }
        imported.push(card);
      });

      if (!imported.length) {
        window.alert('Tidak ada baris valid yang ditemukan.');
        return;
      }

      if (cards.length && !window.confirm('Ganti semua kartu dengan data hasil konversi?')) {
        return;
      }

      cards = imported;
      renderAll();
      updateInput();
    }

    var mediaFrame = null;

    function openMediaFrame(cardEl, trigger) {
      if (!cardEl) {
        return;
      }
      var index = parseInt(cardEl.getAttribute('data-index'), 10);
      if (isNaN(index) || !cards[index]) {
        return;
      }

      if (!window.wp || !wp.media) {
        window.alert('Fitur media WordPress tidak tersedia.');
        return;
      }

      if (mediaFrame) {
        mediaFrame.off('select');
      }

      mediaFrame = wp.media({
        title: 'Pilih gambar kartu',
        multiple: false,
        library: { type: 'image' }
      });

      mediaFrame.on('select', function () {
        var attachment = mediaFrame.state().get('selection').first();
        if (!attachment) {
          return;
        }
        var url = attachment.get('url');
        var alt = attachment.get('alt') || attachment.get('title') || '';
        cards[index].image = url;
        if (allowedFields.indexOf('image_alt') !== -1 && !cards[index].image_alt) {
          cards[index].image_alt = alt;
        }

        var container = trigger ? trigger.closest('.pf-card-field') : cardEl.querySelector('.pf-card-field--media');
        if (container) {
          var hidden = container.querySelector('input[data-field="image"]');
          if (hidden) {
            hidden.value = url;
          }
          var preview = container.querySelector('.pf-card-media__preview');
          if (preview) {
            preview.classList.remove('pf-card-media__preview--empty');
            preview.innerHTML = '<img src="' + escapeAttr(url) + '" alt="">';
          }
          var altInput = cardEl.querySelector('input[data-field="image_alt"]');
          if (altInput && !altInput.value) {
            altInput.value = alt;
            cards[index].image_alt = alt;
          }
        }

        updateInput();
      });

      mediaFrame.open();
    }
  }

  function initColorPresets() {
    var presetEl = document.querySelector('[data-pf-color-presets]');
    if (!presetEl) {
      return;
    }

    var listEl = presetEl.querySelector('.pf-color-presets__list');
    var saveButton = presetEl.querySelector('.pf-color-presets__save');
    var nameInput = presetEl.querySelector('#pf-color-preset-name');
    var presetsInput = presetEl.querySelector('input[name="putrafiber_options[front_color_presets]"]');
    var activeInput = presetEl.querySelector('input[name="putrafiber_options[front_active_preset]"]');

    var presets = parseJSON(presetEl.getAttribute('data-presets')) || [];
    var activeId = presetEl.getAttribute('data-active') || '';
    var currentColors = parseJSON(presetEl.getAttribute('data-current')) || {};

    var colorFields = {
      front_primary_color: document.querySelector('input[name="putrafiber_options[front_primary_color]"]'),
      front_gold_color: document.querySelector('input[name="putrafiber_options[front_gold_color]"]'),
      front_dark_color: document.querySelector('input[name="putrafiber_options[front_dark_color]"]'),
      front_water_color: document.querySelector('input[name="putrafiber_options[front_water_color]"]')
    };

    presets = normalisePresets(presets);

    if (!presets.length) {
      var fallbackId = 'preset-default';
      var initialColors = mergeColors(currentColors, collectColors());
      presets.push(createPreset(fallbackId, 'Warna Aktif', initialColors));
      activeId = fallbackId;
    }

    if (!activeId && presets.length) {
      activeId = presets[0].id;
    }

    renderPresetList();
    updateInputs();

    if (saveButton) {
      saveButton.addEventListener('click', function (event) {
        event.preventDefault();
        var name = nameInput ? nameInput.value.trim() : '';
        var colors = collectColors();
        var presetName = name || generatePresetName();
        var presetId = ensureUniqueId(slugify(presetName));

        presets.push(createPreset(presetId, presetName, colors));
        activeId = presetId;
        if (nameInput) {
          nameInput.value = '';
        }
        applyPreset(colors);
        renderPresetList();
        updateInputs();
      });
    }

    if (listEl) {
      listEl.addEventListener('change', function (event) {
        var target = event.target;
        if (target && target.matches('input[type="radio"]')) {
          activeId = target.value;
          var preset = findPreset(activeId);
          if (preset) {
            applyPreset(preset.colors);
          }
          updateInputs();
          renderPresetList();
        }
      });

      listEl.addEventListener('click', function (event) {
        var target = event.target;
        if (!target) {
          return;
        }

        if (target.classList.contains('pf-color-presets__rename')) {
          event.preventDefault();
          var item = target.closest('.pf-color-presets__item');
          if (!item) {
            return;
          }
          var presetId = item.getAttribute('data-id');
          var preset = findPreset(presetId);
          if (!preset) {
            return;
          }

          var newName = window.prompt('Nama preset baru:', preset.name);
          if (newName && newName.trim()) {
            preset.name = newName.trim();
            renderPresetList();
            updateInputs();
          }
        }

        if (target.classList.contains('pf-color-presets__delete')) {
          event.preventDefault();
          var itemEl = target.closest('.pf-color-presets__item');
          if (!itemEl) {
            return;
          }
          var id = itemEl.getAttribute('data-id');

          if (window.confirm('Hapus preset warna ini?')) {
            presets = presets.filter(function (preset) { return preset.id !== id; });
            if (presets.length === 0) {
              var fallback = createPreset('preset-default', 'Warna Aktif', collectColors());
              presets.push(fallback);
              activeId = fallback.id;
            } else if (activeId === id) {
              activeId = presets[0].id;
              applyPreset(presets[0].colors);
            }

            renderPresetList();
            updateInputs();
          }
        }
      });
    }

    function normalisePresets(items) {
      if (!Array.isArray(items)) {
        return [];
      }

      return items.reduce(function (acc, item) {
        if (!item || typeof item !== 'object') {
          return acc;
        }

        var id = typeof item.id === 'string' ? item.id : '';
        if (!id) {
          return acc;
        }

        var name = typeof item.name === 'string' ? item.name : '';
        var colors = {};
        if (item.colors && typeof item.colors === 'object') {
          Object.keys(item.colors).forEach(function (key) {
            colors[key] = item.colors[key];
          });
        }

        acc.push({
          id: id,
          name: name || id,
          colors: colors
        });
        return acc;
      }, []);
    }

    function createPreset(id, name, colors) {
      return {
        id: id,
        name: name || id,
        colors: colors || {}
      };
    }

    function generatePresetName() {
      var base = 'Preset ' + (presets.length + 1);
      var unique = base;
      var counter = 1;
      while (findPresetByName(unique)) {
        unique = base + ' ' + counter;
        counter += 1;
      }
      return unique;
    }

    function findPreset(id) {
      return presets.find(function (preset) { return preset.id === id; }) || null;
    }

    function findPresetByName(name) {
      return presets.some(function (preset) { return preset.name === name; });
    }

    function ensureUniqueId(id) {
      var base = id || 'preset-' + Date.now().toString(36);
      var candidate = base;
      var counter = 1;
      while (findPreset(candidate)) {
        candidate = base + '-' + counter;
        counter += 1;
      }
      return candidate;
    }

    function renderPresetList() {
      if (!listEl) {
        return;
      }

      var swatchLabels = {
        front_primary_color: 'Primary',
        front_gold_color: 'Gold',
        front_dark_color: 'Dark',
        front_water_color: 'Water'
      };

      listEl.innerHTML = '';

      presets.forEach(function (preset) {
        var item = document.createElement('div');
        item.className = 'pf-color-presets__item';
        item.setAttribute('data-id', preset.id);

        var radio = '<label class="pf-color-presets__select">' +
          '<input type="radio" name="pf-color-preset" value="' + escapeAttr(preset.id) + '" ' + (preset.id === activeId ? 'checked' : '') + '>' +
          '<span class="pf-color-presets__name">' + escapeHtml(preset.name || preset.id) + '</span>' +
          '</label>';

        var swatches = Object.keys(swatchLabels).map(function (key) {
          var value = preset.colors[key];
          if (!value) {
            return '';
          }
          return '<span class="pf-color-presets__swatch" title="' + escapeAttr(swatchLabels[key]) + '" style="background:' + escapeAttr(value) + ';"></span>';
        }).join('');

        var actions = '<div class="pf-color-presets__actions">' +
          '<button type="button" class="button-link pf-color-presets__rename">Ganti nama</button>' +
          '<button type="button" class="button-link-delete pf-color-presets__delete">Hapus</button>' +
          '</div>';

        item.innerHTML = radio +
          '<div class="pf-color-presets__swatches">' + swatches + '</div>' +
          actions;

        listEl.appendChild(item);
      });
    }

    function collectColors() {
      var colors = {};
      Object.keys(colorFields).forEach(function (key) {
        if (!colorFields[key]) {
          return;
        }
        colors[key] = colorFields[key].value;
      });
      return colors;
    }

    function mergeColors(base, override) {
      var result = {};
      [base || {}, override || {}].forEach(function (source) {
        Object.keys(source).forEach(function (key) {
          if (typeof source[key] !== 'undefined' && source[key] !== null && source[key] !== '') {
            result[key] = source[key];
          }
        });
      });
      return result;
    }

    function applyPreset(colors) {
      if (!colors) {
        return;
      }

      Object.keys(colorFields).forEach(function (key) {
        var field = colorFields[key];
        if (!field || typeof colors[key] === 'undefined') {
          return;
        }

        field.value = colors[key];

        if (typeof jQuery !== 'undefined' && jQuery.fn && typeof jQuery.fn.wpColorPicker === 'function') {
          jQuery(field).wpColorPicker('color', colors[key]);
        }

        field.dispatchEvent(new Event('input', { bubbles: true }));
        field.dispatchEvent(new Event('change', { bubbles: true }));
      });
    }

    function updateInputs() {
      if (presetsInput) {
        presetsInput.value = JSON.stringify(presets);
      }

      if (activeInput) {
        activeInput.value = activeId;
      }
    }
  }

  function parseJSON(value) {
    if (!value || typeof value !== 'string') {
      return null;
    }
    try {
      return JSON.parse(value);
    } catch (err) {
      return null;
    }
  }

  function slugify(value) {
    return (value || '')
      .toString()
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '') || 'preset';
  }

  function escapeHtml(value) {
    return (value || '')
      .toString()
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function escapeAttr(value) {
    return escapeHtml(value)
      .replace(/\n/g, '&#10;')
      .replace(/\r/g, '&#13;');
  }

  function escapeTextarea(value) {
    return escapeHtml(value).replace(/\r?\n/g, '\n');
  }
})();
