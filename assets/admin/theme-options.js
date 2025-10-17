(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    initSectionsBuilder();
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

    renderAll();
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

      if (section.type === 'custom') {
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

      sections[index][field] = target.value;

      if (sections[index].type === 'custom' && field === 'title') {
        sections[index].label = target.value || customLabel;
        var nameEl = card.querySelector('.pf-section-card__name');
        if (nameEl) {
          nameEl.textContent = target.value ? target.value : customLabel;
        }
      }

      updateOutputs();
    }

    function handleListClick(event) {
      var target = event.target;
      if (!target) {
        return;
      }

      if (target.getAttribute('data-action') === 'remove') {
        event.preventDefault();
        var card = target.closest('.pf-section-card');
        if (!card) {
          return;
        }

        var index = parseInt(card.dataset.index, 10);
        if (isNaN(index) || !sections[index]) {
          return;
        }

        if (window.confirm('Hapus section kustom ini?')) {
          sections.splice(index, 1);
          renderAll();
          updateOutputs();
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

    function updateOutputs() {
      if (builderInput) {
        builderInput.value = JSON.stringify(sections);
      }

      if (orderInput) {
        var enabledIds = sections.filter(function (section) { return section.enabled; }).map(function (section) { return section.id; });
        orderInput.value = enabledIds.join(',');
      }
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
