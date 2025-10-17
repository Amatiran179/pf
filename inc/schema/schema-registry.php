<?php
/**
 * Schema CTA registry.
 *
 * @package PutraFiber
 */
if (!defined('ABSPATH')) exit;

if (!class_exists('PutraFiber_Schema_Registry')) {
    class PutraFiber_Schema_Registry
    {
        /**
         * Retrieve CTA map definition.
         *
         * @return array<string,callable>
         */
        public static function cta_map()
        {
            $map = array(
                'wa_primary' => function (array $context, array $cta, $is_primary = false) {
                    $number = isset($cta['number']) ? preg_replace('/[^0-9]/', '', (string) $cta['number']) : '';
                    if ($number === '') {
                        return array();
                    }

                    if (strpos($number, '0') === 0) {
                        $number = '62' . substr($number, 1);
                    }

                    $label   = isset($cta['label']) ? pf_schema_sanitize_text($cta['label']) : __('WhatsApp', 'putrafiber');
                    $message = isset($cta['message']) ? pf_schema_sanitize_text($cta['message']) : '';
                    $url     = isset($cta['url']) ? pf_schema_sanitize_url($cta['url']) : '';
                    $action  = pf_schema_build_contact_action($number, isset($cta['utm']) ? (array) $cta['utm'] : array());
                    $organization_id = self::organization_id($context);

                    $contact = array(
                        '@type'           => 'ContactPoint',
                        '@id'             => trailingslashit(home_url('/')) . '#contact-whatsapp-primary',
                        'contactType'     => 'customer service',
                        'name'            => $label,
                        'telephone'       => '+' . ltrim($number, '+'),
                        'availableLanguage' => array('id-ID'),
                        'url'             => $url !== '' ? $url : 'https://wa.me/' . $number,
                        'contactPointOf'  => array('@id' => $organization_id),
                    );

                    if ($message !== '') {
                        $contact['description'] = $message;
                    }

                    if (!empty($action)) {
                        $contact['potentialAction'] = $action;
                    }

                    if ($is_primary) {
                        $contact['additionalProperty'] = array(
                            '@type' => 'PropertyValue',
                            'name'  => 'ctaPriority',
                            'value' => 'primary',
                        );
                    }

                    return array(array_filter($contact, array(__CLASS__, 'filter_nulls')));
                },
                'wa_secondary' => function (array $context, array $cta, $is_primary = false) {
                    $number = isset($cta['number']) ? preg_replace('/[^0-9]/', '', (string) $cta['number']) : '';
                    if ($number === '') {
                        return array();
                    }

                    if (strpos($number, '0') === 0) {
                        $number = '62' . substr($number, 1);
                    }

                    $label   = isset($cta['label']) ? pf_schema_sanitize_text($cta['label']) : __('WhatsApp', 'putrafiber');
                    $url     = isset($cta['url']) ? pf_schema_sanitize_url($cta['url']) : '';
                    $action  = pf_schema_build_contact_action($number, isset($cta['utm']) ? (array) $cta['utm'] : array());
                    $organization_id = self::organization_id($context);

                    $contact = array(
                        '@type'           => 'ContactPoint',
                        '@id'             => trailingslashit(home_url('/')) . '#contact-whatsapp-secondary',
                        'contactType'     => 'sales',
                        'name'            => $label,
                        'telephone'       => '+' . ltrim($number, '+'),
                        'availableLanguage' => array('id-ID'),
                        'url'             => $url !== '' ? $url : 'https://wa.me/' . $number,
                        'contactPointOf'  => array('@id' => $organization_id),
                    );

                    if (!empty($action)) {
                        $contact['potentialAction'] = $action;
                    }

                    if ($is_primary) {
                        $contact['additionalProperty'] = array(
                            '@type' => 'PropertyValue',
                            'name'  => 'ctaPriority',
                            'value' => 'primary',
                        );
                    }

                    return array(array_filter($contact, array(__CLASS__, 'filter_nulls')));
                },
                'download_brosur' => function (array $context, array $cta) {
                    $url = isset($cta['url']) ? pf_schema_sanitize_url($cta['url']) : '';
                    if ($url === '') {
                        return array();
                    }

                    $label    = isset($cta['label']) ? pf_schema_sanitize_text($cta['label']) : __('Download Brosur', 'putrafiber');
                    $encoding = (stripos($url, '.pdf') !== false) ? 'application/pdf' : 'application/octet-stream';

                    $document = array(
                        '@type'          => 'DigitalDocument',
                        '@id'            => trailingslashit(home_url('/')) . '#digital-document-brochure',
                        'name'           => $label,
                        'encodingFormat' => $encoding,
                        'url'            => $url,
                    );

                    if (!empty($context['webpage_id'])) {
                        $document['isPartOf'] = array('@id' => $context['webpage_id']);
                    }

                    if (!empty($context['organization_id'])) {
                        $document['about'] = array('@id' => $context['organization_id']);
                    }

                    return array(array_filter($document, array(__CLASS__, 'filter_nulls')));
                },
                'kunjungi_lokasi' => function (array $context, array $cta) {
                    $url = isset($cta['url']) ? pf_schema_sanitize_url($cta['url']) : '';
                    if ($url === '') {
                        return array();
                    }

                    $place = array(
                        '@type'       => 'Place',
                        '@id'         => trailingslashit(home_url('/')) . '#visit-location',
                        'name'        => isset($cta['label']) ? pf_schema_sanitize_text($cta['label']) : self::site_name($context),
                        'hasMap'      => $url,
                        'areaServed'  => isset($context['service_area']) ? $context['service_area'] : array(),
                    );

                    if (!empty($context['organization_address'])) {
                        $place['address'] = $context['organization_address'];
                    }

                    if (!empty($context['geo'])) {
                        $place['geo'] = $context['geo'];
                    }

                    return array(array_filter($place, array(__CLASS__, 'filter_nulls')));
                },
                'katalog_hubungi_cs' => function (array $context, array $cta, $is_primary = false) {
                    $url = isset($cta['url']) ? pf_schema_sanitize_url($cta['url']) : '';
                    if ($url === '') {
                        return array();
                    }

                    $label = isset($cta['label']) ? pf_schema_sanitize_text($cta['label']) : __('Hubungi CS', 'putrafiber');

                    $catalog = array(
                        '@type'         => 'OfferCatalog',
                        '@id'           => trailingslashit(home_url('/')) . '#offer-catalog',
                        'name'          => $label,
                        'url'           => $url,
                        'itemListOrder' => 'http://schema.org/ItemListOrderAscending',
                    );

                    if ($is_primary) {
                        $catalog['potentialAction'] = array(
                            '@type'  => 'ViewAction',
                            'target' => $url,
                        );
                    }

                    return array(array_filter($catalog, array(__CLASS__, 'filter_nulls')));
                },
                'portfolio_wisata' => function (array $context, array $cta) {
                    $permalink = isset($cta['permalink']) ? pf_schema_sanitize_url($cta['permalink']) : '';
                    if ($permalink === '') {
                        return array();
                    }

                    $title = isset($cta['title']) ? pf_schema_sanitize_text($cta['title']) : '';
                    $area  = isset($cta['area']) ? $cta['area'] : (isset($context['service_area']) ? $context['service_area'] : array());

                    $node = array(
                        '@type'    => 'TouristAttraction',
                        '@id'      => $permalink . '#tourist-attraction',
                        'name'     => $title !== '' ? $title : self::site_name($context),
                        'url'      => $permalink,
                    );

                    if (!empty($context['webpage_id'])) {
                        $node['isPartOf'] = array('@id' => $context['webpage_id']);
                    }

                    if (!empty($area)) {
                        $node['areaServed'] = $area;
                    }

                    return array(array_filter($node, array(__CLASS__, 'filter_nulls')));
                },
            );

            return apply_filters('putrafiber_cta_schema_map', $map);
        }

        /**
         * Resolve primary CTA based on priority configuration.
         *
         * @param array $ctas Active CTA list keyed by slug.
         * @return array
         */
        public static function resolve_primary(array $ctas)
        {
            if (empty($ctas)) {
                return array();
            }

            $default_priorities = array(
                'wa_primary',
                'wa_secondary',
                'download_brosur',
                'kunjungi_lokasi',
                'katalog_hubungi_cs',
                'portfolio_wisata',
            );

            $options = get_option('putrafiber_options', array());
            $stored  = array();

            if (!empty($options['cta_priority_order']) && is_array($options['cta_priority_order'])) {
                foreach ($options['cta_priority_order'] as $key) {
                    $sanitized = sanitize_key($key);
                    if ($sanitized !== '') {
                        $stored[] = $sanitized;
                    }
                }
            }

            $priorities = !empty($stored) ? $stored : $default_priorities;
            $priorities = apply_filters('putrafiber_cta_primary_resolve', $priorities);

            foreach ($priorities as $priority) {
                if (isset($ctas[$priority])) {
                    return array($priority => $ctas[$priority]);
                }
            }

            $first_key = array_key_first($ctas);

            return $first_key !== null ? array($first_key => $ctas[$first_key]) : array();
        }

        /**
         * Remove empty values from schema nodes.
         *
         * @param mixed $value Value to test.
         * @return bool
         */
        protected static function filter_nulls($value)
        {
            if (is_array($value)) {
                return !empty($value);
            }

            return $value !== null && $value !== '';
        }

        /**
         * Resolve organization @id fallback.
         *
         * @param array $context Schema context.
         * @return string
         */
        protected static function organization_id(array $context)
        {
            if (!empty($context['organization_id'])) {
                return $context['organization_id'];
            }

            return trailingslashit(home_url('/')) . '#organization';
        }

        /**
         * Resolve site name fallback.
         *
         * @param array $context Schema context.
         * @return string
         */
        protected static function site_name(array $context)
        {
            $name = isset($context['site_name']) ? $context['site_name'] : get_bloginfo('name');
            return pf_schema_sanitize_text($name);
        }
    }
}
