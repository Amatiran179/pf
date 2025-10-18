(function (wp) {
    if (!wp || !wp.blocks || !wp.element) {
        return;
    }

    var registerBlockType = wp.blocks.registerBlockType;
    var __ = wp.i18n.__;
    var el = wp.element.createElement;
    var Fragment = wp.element.Fragment;
    var InspectorControls = wp.blockEditor ? wp.blockEditor.InspectorControls : wp.editor.InspectorControls;
    var RichText = wp.blockEditor ? wp.blockEditor.RichText : wp.editor.RichText;
    var MediaUpload = wp.blockEditor ? wp.blockEditor.MediaUpload : wp.editor.MediaUpload;
    var MediaUploadCheck = wp.blockEditor && wp.blockEditor.MediaUploadCheck ? wp.blockEditor.MediaUploadCheck : wp.editor.MediaUploadCheck;
    var URLInputButton = wp.blockEditor ? wp.blockEditor.URLInputButton : wp.editor.URLInputButton;
    var PanelColorSettings = wp.blockEditor ? wp.blockEditor.PanelColorSettings : wp.editor.PanelColorSettings;
    var PanelBody = wp.components.PanelBody;
    var RangeControl = wp.components.RangeControl;
    var SelectControl = wp.components.SelectControl;
    var TextControl = wp.components.TextControl;
    var Button = wp.components.Button;

    var ICON_OPTIONS = [
        { label: __('Cahaya / Spark', 'putrafiber'), value: 'spark' },
        { label: __('Perisai', 'putrafiber'), value: 'shield' },
        { label: __('Tetesan Air', 'putrafiber'), value: 'drop' },
        { label: __('Bintang', 'putrafiber'), value: 'star' },
        { label: __('Gelombang', 'putrafiber'), value: 'wave' },
        { label: __('Globe', 'putrafiber'), value: 'globe' },
        { label: __('Gear', 'putrafiber'), value: 'gear' },
        { label: __('Trophy', 'putrafiber'), value: 'trophy' },
        { label: __('Kompas', 'putrafiber'), value: 'compass' }
    ];

    function ensureArray(value) {
        return Array.isArray(value) ? value : [];
    }

    function HeroEdit(props) {
        var attributes = props.attributes;
        var setAttributes = props.setAttributes;
        var badges = ensureArray(attributes.badges);
        var overlayValue = typeof attributes.overlayStrength === 'number' ? attributes.overlayStrength : 60;

        function updateAttr(key, value) {
            var next = {};
            next[key] = value;
            setAttributes(next);
        }

        function updateBadge(index, key, value) {
            var next = badges.slice();
            var current = next[index] ? Object.assign({}, next[index]) : {};
            current[key] = value;
            next[index] = current;
            setAttributes({ badges: next });
        }

        function addBadge() {
            var next = badges.slice();
            next.push({ title: '', description: '', icon: 'spark' });
            setAttributes({ badges: next });
        }

        function removeBadge(index) {
            var next = badges.filter(function (_, itemIndex) { return itemIndex !== index; });
            setAttributes({ badges: next });
        }

        var layoutClass = 'pf-block-editor--layout-' + (attributes.layout || 'left');
        var overlayStyle = { opacity: Math.max(0, Math.min(overlayValue, 100)) / 100 };
        var previewStyle = attributes.backgroundImage ? { backgroundImage: 'url(' + attributes.backgroundImage + ')' } : {};

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Pengaturan Hero', 'putrafiber'), initialOpen: true },
                    el(SelectControl, {
                        label: __('Layout Konten', 'putrafiber'),
                        value: attributes.layout || 'left',
                        options: [
                            { label: __('Konten di kiri', 'putrafiber'), value: 'left' },
                            { label: __('Konten rata tengah', 'putrafiber'), value: 'center' }
                        ],
                        onChange: function (value) { updateAttr('layout', value); }
                    }),
                    el(RangeControl, {
                        label: __('Intensitas Overlay', 'putrafiber'),
                        min: 0,
                        max: 90,
                        value: overlayValue,
                        onChange: function (value) { updateAttr('overlayStrength', value); }
                    })
                ),
                el(PanelBody, { title: __('Gambar Latar', 'putrafiber'), initialOpen: false },
                    el(MediaUploadCheck, null,
                        el(MediaUpload, {
                            onSelect: function (media) {
                                if (!media) {
                                    updateAttr('backgroundImage', '');
                                    updateAttr('backgroundAlt', '');
                                    return;
                                }
                                updateAttr('backgroundImage', media.url || '');
                                updateAttr('backgroundAlt', media.alt || '');
                            },
                            allowedTypes: ['image'],
                            value: attributes.backgroundImage,
                            render: function (args) {
                                return el(Button, {
                                    isSecondary: true,
                                    onClick: args.open
                                }, attributes.backgroundImage ? __('Ganti gambar', 'putrafiber') : __('Pilih gambar', 'putrafiber'));
                            }
                        })
                    ),
                    attributes.backgroundImage ? el(Button, {
                        isLink: true,
                        onClick: function () {
                            updateAttr('backgroundImage', '');
                            updateAttr('backgroundAlt', '');
                        }
                    }, __('Hapus gambar', 'putrafiber')) : null,
                    el(TextControl, {
                        label: __('Teks alternatif', 'putrafiber'),
                        value: attributes.backgroundAlt || '',
                        onChange: function (value) { updateAttr('backgroundAlt', value); }
                    })
                ),
                el(PanelBody, { title: __('Badge Highlight', 'putrafiber'), initialOpen: false },
                    badges.map(function (badge, index) {
                        return el('div', { className: 'pf-block-editor__badge', key: 'badge-' + index },
                            el(TextControl, {
                                label: __('Judul badge', 'putrafiber'),
                                value: badge.title || '',
                                onChange: function (value) { updateBadge(index, 'title', value); }
                            }),
                            el(TextControl, {
                                label: __('Deskripsi singkat', 'putrafiber'),
                                value: badge.description || '',
                                onChange: function (value) { updateBadge(index, 'description', value); }
                            }),
                            el(SelectControl, {
                                label: __('Ikon', 'putrafiber'),
                                value: badge.icon || 'spark',
                                options: ICON_OPTIONS,
                                onChange: function (value) { updateBadge(index, 'icon', value); }
                            }),
                            el(Button, {
                                isLink: true,
                                onClick: function () { removeBadge(index); }
                            }, __('Hapus badge', 'putrafiber'))
                        );
                    }),
                    el(Button, {
                        isSecondary: true,
                        onClick: addBadge
                    }, __('Tambah badge', 'putrafiber'))
                )
            ),
            el('div', { className: 'pf-block-editor pf-block-editor--hero ' + layoutClass },
                el('div', { className: 'pf-block-editor__media', style: previewStyle },
                    attributes.backgroundImage ? el('span', { className: 'pf-block-editor__media-label' }, __('Gambar aktif', 'putrafiber')) : el('span', { className: 'pf-block-editor__media-placeholder' }, __('Belum ada gambar', 'putrafiber')),
                    el('span', { className: 'pf-block-editor__overlay', style: overlayStyle })
                ),
                el('div', { className: 'pf-block-editor__body' },
                    el(RichText, {
                        tagName: 'span',
                        className: 'pf-block-editor__eyebrow',
                        placeholder: __('Sorotan hero', 'putrafiber'),
                        value: attributes.highlight,
                        onChange: function (value) { updateAttr('highlight', value); }
                    }),
                    el(RichText, {
                        tagName: 'h2',
                        className: 'pf-block-editor__title',
                        placeholder: __('Judul hero', 'putrafiber'),
                        value: attributes.title,
                        onChange: function (value) { updateAttr('title', value); }
                    }),
                    el(RichText, {
                        tagName: 'p',
                        className: 'pf-block-editor__description',
                        placeholder: __('Deskripsi singkat hero', 'putrafiber'),
                        value: attributes.description,
                        onChange: function (value) { updateAttr('description', value); }
                    }),
                    el('div', { className: 'pf-block-editor__buttons' },
                        el(TextControl, {
                            label: __('Label tombol utama', 'putrafiber'),
                            value: attributes.primaryText || '',
                            onChange: function (value) { updateAttr('primaryText', value); }
                        }),
                        el(URLInputButton, {
                            label: __('URL tombol utama', 'putrafiber'),
                            url: attributes.primaryUrl || '',
                            onChange: function (value) { updateAttr('primaryUrl', value); }
                        }),
                        el(TextControl, {
                            label: __('Label tombol sekunder', 'putrafiber'),
                            value: attributes.secondaryText || '',
                            onChange: function (value) { updateAttr('secondaryText', value); }
                        }),
                        el(URLInputButton, {
                            label: __('URL tombol sekunder', 'putrafiber'),
                            url: attributes.secondaryUrl || '',
                            onChange: function (value) { updateAttr('secondaryUrl', value); }
                        })
                    ),
                    badges.length ? el('div', { className: 'pf-block-editor__badge-preview' },
                        badges.map(function (badge, index) {
                            return el('div', { className: 'pf-block-editor__badge-chip', key: 'chip-' + index },
                                el('span', { className: 'pf-block-editor__badge-chip-title' }, badge.title || __('Badge', 'putrafiber')),
                                badge.description ? el('span', { className: 'pf-block-editor__badge-chip-desc' }, badge.description) : null
                            );
                        })
                    ) : null
                )
            )
        );
    }

    function CTAEdit(props) {
        var attributes = props.attributes;
        var setAttributes = props.setAttributes;
        var overlayValue = typeof attributes.overlayStrength === 'number' ? attributes.overlayStrength : 55;

        function updateAttr(key, value) {
            var next = {};
            next[key] = value;
            setAttributes(next);
        }

        var layoutClass = 'pf-block-editor--cta-' + (attributes.layout || 'split');
        var overlayStyle = { opacity: Math.max(0, Math.min(overlayValue, 100)) / 100 };
        var previewStyle = attributes.backgroundImage ? { backgroundImage: 'url(' + attributes.backgroundImage + ')' } : {};

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Pengaturan CTA', 'putrafiber'), initialOpen: true },
                    el(SelectControl, {
                        label: __('Layout', 'putrafiber'),
                        value: attributes.layout || 'split',
                        options: [
                            { label: __('Gambar & konten berdampingan', 'putrafiber'), value: 'split' },
                            { label: __('Stacked / bertumpuk', 'putrafiber'), value: 'stacked' }
                        ],
                        onChange: function (value) { updateAttr('layout', value); }
                    }),
                    el(RangeControl, {
                        label: __('Intensitas overlay', 'putrafiber'),
                        min: 0,
                        max: 90,
                        value: overlayValue,
                        onChange: function (value) { updateAttr('overlayStrength', value); }
                    })
                ),
                el(PanelBody, { title: __('Gambar latar', 'putrafiber'), initialOpen: false },
                    el(MediaUploadCheck, null,
                        el(MediaUpload, {
                            onSelect: function (media) {
                                if (!media) {
                                    updateAttr('backgroundImage', '');
                                    updateAttr('backgroundAlt', '');
                                    return;
                                }
                                updateAttr('backgroundImage', media.url || '');
                                updateAttr('backgroundAlt', media.alt || '');
                            },
                            allowedTypes: ['image'],
                            value: attributes.backgroundImage,
                            render: function (args) {
                                return el(Button, { isSecondary: true, onClick: args.open }, attributes.backgroundImage ? __('Ganti gambar', 'putrafiber') : __('Pilih gambar', 'putrafiber'));
                            }
                        })
                    ),
                    attributes.backgroundImage ? el(Button, {
                        isLink: true,
                        onClick: function () {
                            updateAttr('backgroundImage', '');
                            updateAttr('backgroundAlt', '');
                        }
                    }, __('Hapus gambar', 'putrafiber')) : null,
                    el(TextControl, {
                        label: __('Teks alternatif', 'putrafiber'),
                        value: attributes.backgroundAlt || '',
                        onChange: function (value) { updateAttr('backgroundAlt', value); }
                    })
                ),
                el(PanelColorSettings, {
                    title: __('Warna', 'putrafiber'),
                    colorSettings: [
                        {
                            label: __('Warna latar', 'putrafiber'),
                            value: attributes.backgroundColor,
                            onChange: function (value) { updateAttr('backgroundColor', value || ''); }
                        },
                        {
                            label: __('Warna teks', 'putrafiber'),
                            value: attributes.textColor,
                            onChange: function (value) { updateAttr('textColor', value || ''); }
                        }
                    ]
                })
            ),
            el('div', { className: 'pf-block-editor pf-block-editor--cta ' + layoutClass },
                el('div', { className: 'pf-block-editor__media', style: previewStyle },
                    attributes.backgroundImage ? el('span', { className: 'pf-block-editor__media-label' }, __('Gambar aktif', 'putrafiber')) : el('span', { className: 'pf-block-editor__media-placeholder' }, __('Belum ada gambar', 'putrafiber')),
                    el('span', { className: 'pf-block-editor__overlay', style: overlayStyle })
                ),
                el('div', { className: 'pf-block-editor__body' },
                    el(RichText, {
                        tagName: 'span',
                        className: 'pf-block-editor__eyebrow',
                        placeholder: __('Eyebrow / lead text', 'putrafiber'),
                        value: attributes.eyebrow,
                        onChange: function (value) { updateAttr('eyebrow', value); }
                    }),
                    el(RichText, {
                        tagName: 'h2',
                        className: 'pf-block-editor__title',
                        placeholder: __('Judul CTA', 'putrafiber'),
                        value: attributes.title,
                        onChange: function (value) { updateAttr('title', value); }
                    }),
                    el(RichText, {
                        tagName: 'p',
                        className: 'pf-block-editor__description',
                        placeholder: __('Deskripsi CTA', 'putrafiber'),
                        value: attributes.description,
                        onChange: function (value) { updateAttr('description', value); }
                    }),
                    el('div', { className: 'pf-block-editor__buttons' },
                        el(TextControl, {
                            label: __('Label tombol utama', 'putrafiber'),
                            value: attributes.primaryText || '',
                            onChange: function (value) { updateAttr('primaryText', value); }
                        }),
                        el(URLInputButton, {
                            label: __('URL tombol utama', 'putrafiber'),
                            url: attributes.primaryUrl || '',
                            onChange: function (value) { updateAttr('primaryUrl', value); }
                        }),
                        el(TextControl, {
                            label: __('Label tombol sekunder', 'putrafiber'),
                            value: attributes.secondaryText || '',
                            onChange: function (value) { updateAttr('secondaryText', value); }
                        }),
                        el(URLInputButton, {
                            label: __('URL tombol sekunder', 'putrafiber'),
                            url: attributes.secondaryUrl || '',
                            onChange: function (value) { updateAttr('secondaryUrl', value); }
                        })
                    )
                )
            )
        );
    }

    function TestimonialsEdit(props) {
        var attributes = props.attributes;
        var setAttributes = props.setAttributes;
        var items = ensureArray(attributes.testimonials);
        var columns = typeof attributes.columns === 'number' ? attributes.columns : 3;

        function updateAttr(key, value) {
            var next = {};
            next[key] = value;
            setAttributes(next);
        }

        function updateItem(index, key, value) {
            var next = items.slice();
            var current = next[index] ? Object.assign({}, next[index]) : {};
            current[key] = value;
            next[index] = current;
            setAttributes({ testimonials: next });
        }

        function addItem() {
            var next = items.slice();
            next.push({ quote: '', name: '', role: '', rating: 5 });
            setAttributes({ testimonials: next });
        }

        function removeItem(index) {
            var next = items.filter(function (_, itemIndex) { return itemIndex !== index; });
            setAttributes({ testimonials: next });
        }

        var layoutClass = 'pf-block-editor--testimonials-' + (attributes.layout || 'grid');

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Pengaturan Testimoni', 'putrafiber'), initialOpen: true },
                    el(SelectControl, {
                        label: __('Layout', 'putrafiber'),
                        value: attributes.layout || 'grid',
                        options: [
                            { label: __('Grid responsif', 'putrafiber'), value: 'grid' },
                            { label: __('Carousel sederhana', 'putrafiber'), value: 'carousel' }
                        ],
                        onChange: function (value) { updateAttr('layout', value); }
                    }),
                    el(RangeControl, {
                        label: __('Jumlah kolom (grid)', 'putrafiber'),
                        min: 1,
                        max: 4,
                        value: columns,
                        onChange: function (value) { updateAttr('columns', value); }
                    })
                ),
                el(PanelColorSettings, {
                    title: __('Warna', 'putrafiber'),
                    colorSettings: [
                        {
                            label: __('Warna latar', 'putrafiber'),
                            value: attributes.backgroundColor,
                            onChange: function (value) { updateAttr('backgroundColor', value || ''); }
                        },
                        {
                            label: __('Warna teks', 'putrafiber'),
                            value: attributes.textColor,
                            onChange: function (value) { updateAttr('textColor', value || ''); }
                        },
                        {
                            label: __('Aksen rating', 'putrafiber'),
                            value: attributes.accentColor,
                            onChange: function (value) { updateAttr('accentColor', value || ''); }
                        }
                    ]
                })
            ),
            el('div', { className: 'pf-block-editor pf-block-editor--testimonials ' + layoutClass },
                el('div', { className: 'pf-block-editor__header' },
                    el(RichText, {
                        tagName: 'h2',
                        className: 'pf-block-editor__title',
                        placeholder: __('Judul testimoni', 'putrafiber'),
                        value: attributes.heading,
                        onChange: function (value) { updateAttr('heading', value); }
                    }),
                    el(RichText, {
                        tagName: 'p',
                        className: 'pf-block-editor__description',
                        placeholder: __('Subjudul / deskripsi', 'putrafiber'),
                        value: attributes.subheading,
                        onChange: function (value) { updateAttr('subheading', value); }
                    })
                ),
                el('div', { className: 'pf-block-editor__testimonial-list pf-block-editor__testimonial-list--cols-' + columns },
                    items.length ? items.map(function (item, index) {
                        var ratingValue = typeof item.rating === 'number' ? item.rating : 5;
                        return el('div', { className: 'pf-block-editor__testimonial', key: 'testimonial-' + index },
                            el(RichText, {
                                tagName: 'div',
                                className: 'pf-block-editor__testimonial-quote',
                                multiline: 'p',
                                placeholder: __('Isi testimoni...', 'putrafiber'),
                                value: item.quote,
                                onChange: function (value) { updateItem(index, 'quote', value); }
                            }),
                            el(TextControl, {
                                label: __('Nama klien', 'putrafiber'),
                                value: item.name || '',
                                onChange: function (value) { updateItem(index, 'name', value); }
                            }),
                            el(TextControl, {
                                label: __('Peran / jabatan', 'putrafiber'),
                                value: item.role || '',
                                onChange: function (value) { updateItem(index, 'role', value); }
                            }),
                            el(RangeControl, {
                                label: __('Rating', 'putrafiber'),
                                min: 0,
                                max: 5,
                                value: ratingValue,
                                onChange: function (value) { updateItem(index, 'rating', value); }
                            }),
                            el(Button, {
                                isLink: true,
                                onClick: function () { removeItem(index); }
                            }, __('Hapus testimoni', 'putrafiber'))
                        );
                    }) : el('p', { className: 'pf-block-editor__empty' }, __('Belum ada testimoni. Tambahkan minimal satu.', 'putrafiber'))
                ),
                el(Button, { isSecondary: true, onClick: addItem }, __('Tambah testimoni', 'putrafiber'))
            )
        );
    }

    registerBlockType('putrafiber/hero-highlight', {
        title: __('Hero Landing PutraFiber', 'putrafiber'),
        icon: 'slides',
        category: 'putrafiber',
        supports: { align: ['wide', 'full'], anchor: true },
        edit: HeroEdit,
        save: function () { return null; }
    });

    registerBlockType('putrafiber/cta-banner', {
        title: __('CTA Premium PutraFiber', 'putrafiber'),
        icon: 'megaphone',
        category: 'putrafiber',
        supports: { align: ['wide', 'full'], anchor: true },
        edit: CTAEdit,
        save: function () { return null; }
    });

    registerBlockType('putrafiber/testimonial-showcase', {
        title: __('Testimoni PutraFiber', 'putrafiber'),
        icon: 'groups',
        category: 'putrafiber',
        supports: { align: ['wide', 'full'], anchor: true },
        edit: TestimonialsEdit,
        save: function () { return null; }
    });
})(window.wp);
