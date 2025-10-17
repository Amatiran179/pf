<?php
/**
 * Lightweight analytics & WhatsApp click tracking for PutraFiber theme.
 *
 * @package PutraFiber
 */
if (!defined('ABSPATH')) exit;

/**
 * Retrieve analytics data with defaults applied.
 *
 * @return array
 */
function putrafiber_get_analytics_data() {
    $defaults = array(
        'visits_total'      => 0,
        'pages'             => array(),
        'referrers'         => array(),
        'wa_clicks_total'   => 0,
        'wa_clicks_by_page' => array(),
        'wa_clicks_by_link' => array(),
        'last_updated'      => '',
    );

    $stored = get_option('putrafiber_analytics', array());

    if (!is_array($stored)) {
        $stored = array();
    }

    return wp_parse_args($stored, $defaults);
}

/**
 * Persist analytics data back to the database.
 *
 * @param array $data Analytics payload.
 */
function putrafiber_update_analytics_data($data) {
    if (!is_array($data)) {
        return;
    }

    update_option('putrafiber_analytics', $data, false);
}

/**
 * Resolve current page URL for logging.
 *
 * @return string
 */
function putrafiber_get_current_request_url() {
    if (php_sapi_name() === 'cli') {
        return '';
    }

    $request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '';
    if ($request_uri === '') {
        return '';
    }

    $home = home_url();
    $home = trailingslashit($home);

    if (strpos($request_uri, 'http') === 0) {
        return esc_url_raw($request_uri);
    }

    return esc_url_raw(rtrim($home, '/') . $request_uri);
}

/**
 * Trim associative array to a maximum number of items, keeping highest counts.
 *
 * @param array $bucket
 * @param int   $limit
 */
function putrafiber_trim_analytics_bucket(&$bucket, $limit = 250) {
    if (!is_array($bucket) || count($bucket) <= $limit) {
        return;
    }

    arsort($bucket);
    $bucket = array_slice($bucket, 0, $limit, true);
}

/**
 * Trim page analytics to prevent unbounded growth.
 *
 * @param array $pages
 * @param int   $limit
 */
function putrafiber_trim_page_analytics(&$pages, $limit = 250) {
    if (!is_array($pages) || count($pages) <= $limit) {
        return;
    }

    uasort($pages, function ($a, $b) {
        $a_count = isset($a['count']) ? (int) $a['count'] : 0;
        $b_count = isset($b['count']) ? (int) $b['count'] : 0;

        if ($a_count === $b_count) {
            return 0;
        }

        return ($a_count < $b_count) ? 1 : -1;
    });

    $pages = array_slice($pages, 0, $limit, true);
}

/**
 * Record a page visit (front-end only).
 */
function putrafiber_track_visit() {
    if (is_admin() || wp_doing_ajax() || (is_user_logged_in() && current_user_can('manage_options'))) {
        return;
    }

    if (apply_filters('putrafiber_disable_analytics', false)) {
        return;
    }

    $url = putrafiber_get_current_request_url();
    if ($url === '') {
        return;
    }

    $analytics = putrafiber_get_analytics_data();

    $analytics['visits_total'] = isset($analytics['visits_total']) ? (int) $analytics['visits_total'] + 1 : 1;

    if (!isset($analytics['pages'][$url])) {
        $analytics['pages'][$url] = array(
            'count'      => 0,
            'referrers'  => array(),
            'last_visit' => '',
        );
    }

    $analytics['pages'][$url]['count'] = isset($analytics['pages'][$url]['count'])
        ? (int) $analytics['pages'][$url]['count'] + 1
        : 1;
    $analytics['pages'][$url]['last_visit'] = current_time('mysql');

    $raw_referer = wp_get_raw_referer();
    $referer_key = $raw_referer ? esc_url_raw($raw_referer) : 'direct';

    if (!isset($analytics['pages'][$url]['referrers'][$referer_key])) {
        $analytics['pages'][$url]['referrers'][$referer_key] = 0;
    }
    $analytics['pages'][$url]['referrers'][$referer_key]++;
    putrafiber_trim_analytics_bucket($analytics['pages'][$url]['referrers'], 50);

    if (!isset($analytics['referrers'][$referer_key])) {
        $analytics['referrers'][$referer_key] = 0;
    }
    $analytics['referrers'][$referer_key]++;

    putrafiber_trim_page_analytics($analytics['pages']);
    putrafiber_trim_analytics_bucket($analytics['referrers']);

    $analytics['last_updated'] = current_time('mysql');

    putrafiber_update_analytics_data($analytics);
}
add_action('template_redirect', 'putrafiber_track_visit', 20);

/**
 * Ajax handler for WhatsApp click tracking.
 */
function putrafiber_track_whatsapp_click() {
    check_ajax_referer('pf_ajax_nonce', 'security');
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Unauthorized', 403);
    }

    $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
    $nonce_valid = $nonce ? wp_verify_nonce($nonce, 'putrafiber_analytics') : false;

    if (!$nonce_valid && $nonce) {
        $nonce_valid = wp_verify_nonce($nonce, 'putrafiber_nonce');
    }

    if (!$nonce_valid) {
        wp_send_json_error(array('message' => __('Invalid analytics nonce.', 'putrafiber')), 403);
    }

    $target = isset($_POST['target']) ? pf_clean_url($_POST['target']) : '';
    $source = isset($_POST['source']) ? pf_clean_url($_POST['source']) : '';

    if ($target === '') {
        wp_send_json_error(array('message' => __('Missing WhatsApp link.', 'putrafiber')), 400);
    }

    $analytics = putrafiber_get_analytics_data();

    $analytics['wa_clicks_total'] = isset($analytics['wa_clicks_total']) ? (int) $analytics['wa_clicks_total'] + 1 : 1;

    if (!isset($analytics['wa_clicks_by_link'][$target])) {
        $analytics['wa_clicks_by_link'][$target] = 0;
    }
    $analytics['wa_clicks_by_link'][$target]++;

    $source_key = $source !== '' ? $source : 'direct';
    if (!isset($analytics['wa_clicks_by_page'][$source_key])) {
        $analytics['wa_clicks_by_page'][$source_key] = 0;
    }
    $analytics['wa_clicks_by_page'][$source_key]++;

    putrafiber_trim_analytics_bucket($analytics['wa_clicks_by_link']);
    putrafiber_trim_analytics_bucket($analytics['wa_clicks_by_page']);

    $analytics['last_updated'] = current_time('mysql');

    putrafiber_update_analytics_data($analytics);

    wp_send_json_success();
}
add_action('wp_ajax_putrafiber_track_wa_click', 'putrafiber_track_whatsapp_click');
add_action('wp_ajax_nopriv_putrafiber_track_wa_click', 'putrafiber_track_whatsapp_click');

/**
 * Render analytics summary widget on the WordPress dashboard.
 */
function putrafiber_render_analytics_widget() {
    $analytics = putrafiber_get_analytics_data();
    $total_visits = isset($analytics['visits_total']) ? (int) $analytics['visits_total'] : 0;
    $unique_links = isset($analytics['pages']) ? count($analytics['pages']) : 0;
    $total_wa = isset($analytics['wa_clicks_total']) ? (int) $analytics['wa_clicks_total'] : 0;

    $top_pages = isset($analytics['pages']) ? $analytics['pages'] : array();
    uasort($top_pages, function ($a, $b) {
        $a_count = isset($a['count']) ? (int) $a['count'] : 0;
        $b_count = isset($b['count']) ? (int) $b['count'] : 0;

        if ($a_count === $b_count) {
            return 0;
        }

        return ($a_count < $b_count) ? 1 : -1;
    });
    $top_pages = array_slice($top_pages, 0, 5, true);

    $top_referrers = isset($analytics['referrers']) ? $analytics['referrers'] : array();
    arsort($top_referrers);
    $top_referrers = array_slice($top_referrers, 0, 5, true);

    $wa_by_page = isset($analytics['wa_clicks_by_page']) ? $analytics['wa_clicks_by_page'] : array();
    arsort($wa_by_page);
    $wa_by_page = array_slice($wa_by_page, 0, 5, true);

    $wa_by_link = isset($analytics['wa_clicks_by_link']) ? $analytics['wa_clicks_by_link'] : array();
    arsort($wa_by_link);
    $wa_by_link = array_slice($wa_by_link, 0, 5, true);

    $last_updated = !empty($analytics['last_updated']) ? $analytics['last_updated'] : '';
    $last_updated_display = $last_updated ? date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($last_updated)) : __('Belum ada data', 'putrafiber');
    $reset_nonce = wp_create_nonce('putrafiber_reset_analytics');
    ?>
    <div class="putrafiber-analytics-widget">
        <div class="analytics-toolbar">
            <div class="analytics-meta">
                <h3><?php esc_html_e('Ringkasan Pengunjung & Interaksi', 'putrafiber'); ?></h3>
                <span class="analytics-updated"><?php printf(esc_html__('Terakhir diperbarui: %s', 'putrafiber'), esc_html($last_updated_display)); ?></span>
            </div>
            <button type="button" class="button button-secondary putrafiber-reset-analytics" data-nonce="<?php echo esc_attr($reset_nonce); ?>" data-confirm="<?php esc_attr_e('Hapus seluruh data analytics? Tindakan ini tidak dapat dibatalkan.', 'putrafiber'); ?>" data-success="<?php esc_attr_e('Data analytics berhasil dihapus.', 'putrafiber'); ?>">
                <?php esc_html_e('Reset Statistik', 'putrafiber'); ?>
            </button>
        </div>

        <div class="analytics-metric-grid">
            <div class="analytics-metric">
                <span class="metric-label"><?php esc_html_e('Total Kunjungan', 'putrafiber'); ?></span>
                <span class="metric-value"><?php echo number_format_i18n($total_visits); ?></span>
            </div>
            <div class="analytics-metric">
                <span class="metric-label"><?php esc_html_e('Link Dilihat', 'putrafiber'); ?></span>
                <span class="metric-value"><?php echo number_format_i18n($unique_links); ?></span>
            </div>
            <div class="analytics-metric">
                <span class="metric-label"><?php esc_html_e('Klik WhatsApp', 'putrafiber'); ?></span>
                <span class="metric-value"><?php echo number_format_i18n($total_wa); ?></span>
            </div>
        </div>

        <div class="analytics-card-grid">
            <div class="analytics-card">
                <h4><?php esc_html_e('Link Teratas', 'putrafiber'); ?></h4>
                <?php if (!empty($top_pages)) : ?>
                    <div class="analytics-table-wrapper">
                        <table class="widefat striped analytics-table">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e('Link', 'putrafiber'); ?></th>
                                    <th><?php esc_html_e('Kunjungan', 'putrafiber'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_pages as $url => $data) : ?>
                                    <tr>
                                        <td><a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($url); ?></a></td>
                                        <td><?php echo number_format_i18n(isset($data['count']) ? $data['count'] : 0); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <p class="analytics-empty"><?php esc_html_e('Belum ada data kunjungan.', 'putrafiber'); ?></p>
                <?php endif; ?>
            </div>

            <div class="analytics-card">
                <h4><?php esc_html_e('Referer Teratas', 'putrafiber'); ?></h4>
                <?php if (!empty($top_referrers)) : ?>
                    <div class="analytics-table-wrapper">
                        <table class="widefat striped analytics-table">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e('Referer', 'putrafiber'); ?></th>
                                    <th><?php esc_html_e('Jumlah', 'putrafiber'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_referrers as $referrer => $count) : ?>
                                    <tr>
                                        <td>
                                            <?php if ($referrer === 'direct') : ?>
                                                <?php esc_html_e('Direct / Tidak diketahui', 'putrafiber'); ?>
                                            <?php else : ?>
                                                <a href="<?php echo esc_url($referrer); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($referrer); ?></a>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo number_format_i18n($count); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <p class="analytics-empty"><?php esc_html_e('Belum ada data referer.', 'putrafiber'); ?></p>
                <?php endif; ?>
            </div>

            <div class="analytics-card">
                <h4><?php esc_html_e('Sumber Klik WhatsApp', 'putrafiber'); ?></h4>
                <?php if (!empty($wa_by_page)) : ?>
                    <div class="analytics-table-wrapper">
                        <table class="widefat striped analytics-table">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e('Halaman', 'putrafiber'); ?></th>
                                    <th><?php esc_html_e('Klik', 'putrafiber'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($wa_by_page as $page => $count) : ?>
                                    <tr>
                                        <td>
                                            <?php if ($page === 'direct') : ?>
                                                <?php esc_html_e('Tidak diketahui', 'putrafiber'); ?>
                                            <?php else : ?>
                                                <a href="<?php echo esc_url($page); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($page); ?></a>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo number_format_i18n($count); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <p class="analytics-empty"><?php esc_html_e('Belum ada data klik WhatsApp berdasarkan halaman.', 'putrafiber'); ?></p>
                <?php endif; ?>
            </div>

            <div class="analytics-card">
                <h4><?php esc_html_e('Link WhatsApp Terpopuler', 'putrafiber'); ?></h4>
                <?php if (!empty($wa_by_link)) : ?>
                    <div class="analytics-table-wrapper">
                        <table class="widefat striped analytics-table">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e('Link', 'putrafiber'); ?></th>
                                    <th><?php esc_html_e('Klik', 'putrafiber'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($wa_by_link as $link => $count) : ?>
                                    <tr>
                                        <td><a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($link); ?></a></td>
                                        <td><?php echo number_format_i18n($count); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <p class="analytics-empty"><?php esc_html_e('Belum ada data klik WhatsApp.', 'putrafiber'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Register dashboard widget.
 */
function putrafiber_register_analytics_widget() {
    wp_add_dashboard_widget(
        'putrafiber_dashboard_analytics',
        __('Statistik PutraFiber', 'putrafiber'),
        'putrafiber_render_analytics_widget'
    );
}
add_action('wp_dashboard_setup', 'putrafiber_register_analytics_widget');

/**
 * Allow administrators to reset analytics buckets from the dashboard.
 */
function putrafiber_reset_analytics() {
    check_ajax_referer('pf_ajax_nonce', 'security');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => __('Anda tidak memiliki akses untuk menghapus data analytics.', 'putrafiber')), 403);
    }

    $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
    if (!$nonce || !wp_verify_nonce($nonce, 'putrafiber_reset_analytics')) {
        wp_send_json_error(array('message' => __('Nonce analytics tidak valid.', 'putrafiber')), 403);
    }

    delete_option('putrafiber_analytics');

    wp_send_json_success(array('message' => __('Data analytics berhasil dihapus.', 'putrafiber')));
}
add_action('wp_ajax_putrafiber_reset_analytics', 'putrafiber_reset_analytics');
