<?php
/*
Plugin Name: Consent Banner
Description: Cookie Consent Banner with configuration panel and Google Tag Manager compatibility.
Version: 1.4
Author: Lyca
*/

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'WP_CONSENT_BANNER_OPTION_KEY', 'wp_consent_banner_options' );

/**
 * ====== REGISTRO DE OPCIONES ======
 */
add_action( 'admin_menu', 'wp_consent_banner_admin_menu' );
add_action( 'admin_init', 'wp_consent_banner_settings_init' );
add_action( 'admin_post_wp_consent_banner_reset', 'wp_consent_banner_reset_handler' );

function wp_consent_banner_admin_menu() {
    add_options_page(
        'WP Consent Banner',
        'Consent Banner',
        'manage_options',
        'wp-consent-banner',
        'wp_consent_banner_options_page_html'
    );
}

function wp_consent_banner_settings_init() {
    register_setting(
        'wp_consent_banner',
        WP_CONSENT_BANNER_OPTION_KEY,
        'wp_consent_banner_options_sanitize'
    );

    add_settings_section(
        'wp_consent_banner_section_main',
        'Banner Settings',
        function() {
            echo '<p>Adjust the default values of your cookie consent banner (colors, font sizes, icon position, etc.).</p>';
        },
        'wp-consent-banner'
    );

    // Campos base
    $fields = [
        'text' => 'Banner Text',
        'image' => 'Icon URL',
        'icon_position' => 'Minimized Icon Position',
        'show_minimized_icon' => 'Show Minimized Icon When Closed',
    ];

    foreach ($fields as $id => $label) {
        add_settings_field(
            $id,
            $label,
            "wp_consent_banner_field_{$id}_cb",
            'wp-consent-banner',
            'wp_consent_banner_section_main'
        );
    }

    // Colores extendidos
    $colors = [
        'bannerBg' => 'Banner Background Color',
        'bannerText' => 'Banner Text Color',

        // Botones principales
        'buttonPrimaryBg' => 'Primary Button Background (Allow All)',
        'buttonPrimaryText' => 'Primary Button Text',
        'buttonPrimaryHoverBg' => 'Primary Button Hover Background',
        'buttonPrimaryHoverText' => 'Primary Button Hover Text',

        'buttonSecondaryBg' => 'Secondary Buttons Background (Deny/Selection)',
        'buttonSecondaryText' => 'Secondary Buttons Text',
        'buttonSecondaryHoverBg' => 'Secondary Buttons Hover Background',
        'buttonSecondaryHoverText' => 'Secondary Buttons Hover Text',

        // Switches
        'switchOnBg' => 'Selection Switch Active Color',
        'switchOffBg' => 'Selection Switch Inactive Color',

        // Botón de cierre (X)
        'closeBtnBg' => 'Close Button Background',
        'closeBtnColor' => 'Close Button Text Color',
        'closeBtnHoverBg' => 'Close Button Hover Background',
        'closeBtnHoverColor' => 'Close Button Hover Text Color',

        // Icono minimizado
        'iconBg' => 'Minimized Icon Background'
    ];
    foreach ($colors as $id => $label) {
        add_settings_field(
            "color_{$id}",
            $label,
            "wp_consent_banner_field_color_cb",
            'wp-consent-banner',
            'wp_consent_banner_section_main',
            [ 'id' => $id ]
        );
    }

    // Tamaños de fuente
    $fonts = [
        'font_bannerText' => 'Banner Text Font Size',
        'font_optionText' => 'Options Font Size (Preferences, etc.)',
        'font_buttonText' => 'Buttons Font Size (Allow/Deny)'
    ];
    foreach ($fonts as $id => $label) {
        add_settings_field(
            $id,
            $label,
            "wp_consent_banner_field_font_cb",
            'wp-consent-banner',
            'wp_consent_banner_section_main',
            [ 'id' => $id ]
        );
    }
}

/**
 * Reset to defaults handler
 */
function wp_consent_banner_reset_handler() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    check_admin_referer('wp_consent_banner_reset');
    delete_option(WP_CONSENT_BANNER_OPTION_KEY);
    wp_redirect(admin_url('options-general.php?page=wp-consent-banner&reset=1'));
    exit;
}

/**
 * ====== CAMPOS ======
 */
function wp_consent_banner_default_options() {
    return [
        'text' => 'We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies.',
        'image' => plugin_dir_url(__FILE__) . 'dist/cookie_100.png',
        'icon_position' => 'left',
        'show_minimized_icon' => '1',

        // Colores por defecto
        'color_bannerBg' => '#f8f9fa',
        'color_bannerText' => '#000000',
        'color_buttonPrimaryBg' => '#28a745',
        'color_buttonPrimaryText' => '#ffffff',
        'color_buttonPrimaryHoverBg' => '#218838',
        'color_buttonPrimaryHoverText' => '#ffffff',
        'color_buttonSecondaryBg' => '#f2f2f2',
        'color_buttonSecondaryText' => '#000000',
        'color_buttonSecondaryHoverBg' => '#e0e0e0',
        'color_buttonSecondaryHoverText' => '#000000',
        'color_switchOnBg' => '#28a745',
        'color_switchOffBg' => '#cccccc',
        'color_closeBtnBg' => '#f2f2f2',
        'color_closeBtnColor' => '#000000',
        'color_closeBtnHoverBg' => '#e0e0e0',
        'color_closeBtnHoverColor' => '#000000',
        'color_iconBg' => '#28a745',

        // Tamaños de fuente
        'font_bannerText' => '16px',
        'font_optionText' => '15px',
        'font_buttonText' => '14px',

        // Textos personalizables
        'text_label_necessary' => 'Necessary',
        'text_label_preferences' => 'Preferences',
        'text_label_statistics' => 'Statistics',
        'text_label_marketing' => 'Marketing',
        'text_button_deny_all' => 'Deny All',
        'text_button_allow_selection' => 'Allow Selection',
        'text_button_allow_all' => 'Allow All',
    ];
}

function wp_consent_banner_get_options() {
    $defaults = wp_consent_banner_default_options();
    $saved = get_option(WP_CONSENT_BANNER_OPTION_KEY, []);
    return wp_parse_args($saved, $defaults);
}

function wp_consent_banner_field_text_cb() {
    $opts = wp_consent_banner_get_options();
    echo '<textarea name="' . WP_CONSENT_BANNER_OPTION_KEY . '[text]" rows="3" style="width:100%;">' . esc_textarea($opts['text']) . '</textarea>';
}

function wp_consent_banner_field_image_cb() {
    $opts = wp_consent_banner_get_options();
    echo '<input type="text" name="' . WP_CONSENT_BANNER_OPTION_KEY . '[image]" value="' . esc_attr($opts['image']) . '" style="width:100%;">';
}

function wp_consent_banner_field_icon_position_cb() {
    $opts = wp_consent_banner_get_options();
    $val = $opts['icon_position'];
    ?>
    <select name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[icon_position]">
        <option value="left" <?php selected($val, 'left'); ?>>Left</option>
        <option value="right" <?php selected($val, 'right'); ?>>Right</option>
    </select>
    <?php
}

function wp_consent_banner_field_show_minimized_icon_cb() {
    $opts = wp_consent_banner_get_options();
    ?>
    <input type="checkbox" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[show_minimized_icon]" value="1" <?php checked($opts['show_minimized_icon'], '1'); ?>>
    <span>Enabled</span>
    <?php
}

function wp_consent_banner_field_color_cb($args) {
    $opts = wp_consent_banner_get_options();
    $id = $args['id'];
    $val = $opts["color_{$id}"];
    echo '<input type="color" name="' . WP_CONSENT_BANNER_OPTION_KEY . '[color_' . esc_attr($id) . ']" value="' . esc_attr($val) . '">';
}

function wp_consent_banner_field_font_cb($args) {
    $opts = wp_consent_banner_get_options();
    $id = $args['id'];
    $val = $opts[$id];
    echo '<input type="text" name="' . WP_CONSENT_BANNER_OPTION_KEY . '[' . esc_attr($id) . ']" value="' . esc_attr($val) . '" placeholder="e.g. 16px" style="width:100px;">';
}

/**
 * Sanitización
 */
function wp_consent_banner_options_sanitize($input) {
    $defaults = wp_consent_banner_default_options();
    $out = [];
    foreach ($defaults as $key => $def) {
        if (isset($input[$key])) {
            if (strpos($key, 'color_') === 0) {
                $out[$key] = sanitize_hex_color($input[$key]);
            } else {
                $out[$key] = sanitize_text_field($input[$key]);
            }
        } else {
            $out[$key] = $def;
        }
    }
    return $out;
}

/**
 * Página de opciones
 */
function wp_consent_banner_options_page_html() {
    if (!current_user_can('manage_options')) return;
    $opts = wp_consent_banner_get_options();
    ?>
    <div class="wrap">
        <h1>Consent Banner</h1>
        <?php if (isset($_GET['reset']) && $_GET['reset'] == '1'): ?>
            <div class="notice notice-success is-dismissible"><p>Settings have been reset to default values.</p></div>
        <?php endif; ?>
        <form method="post" action="options.php">
            <?php settings_fields('wp_consent_banner'); ?>
            
            <h2>Banner Settings</h2>
            <p>Adjust the default values of your cookie consent banner (colors, font sizes, icon position, etc.).</p>
            
            <!-- Banner Text - Ocupa toda la fila -->
            <table class="form-table">
                <tr>
                    <th scope="row">Banner Text</th>
                    <td>
                        <textarea name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[text]" rows="3" style="width:100%;"><?php echo esc_textarea($opts['text']); ?></textarea>
                    </td>
                </tr>
            </table>
            
            <!-- Texto personalizables de categorías y botones -->
            <h3 style="font-weight: bold; font-size: 1.1em; text-decoration: underline; margin-top: 20px;">Customizable Text Labels:</h3>
            <table class="form-table">
                <tr>
                    <th scope="row">Cookie Category Labels</th>
                    <td>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px 30px; align-items: start;">
                            <div>
                                <label style="display: block; font-weight: 500; margin-bottom: 5px;">Necessary:</label>
                                <input type="text" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[text_label_necessary]" value="<?php echo esc_attr($opts['text_label_necessary']); ?>" style="width: 100%;">
                            </div>
                            <div>
                                <label style="display: block; font-weight: 500; margin-bottom: 5px;">Preferences:</label>
                                <input type="text" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[text_label_preferences]" value="<?php echo esc_attr($opts['text_label_preferences']); ?>" style="width: 100%;">
                            </div>
                            <div>
                                <label style="display: block; font-weight: 500; margin-bottom: 5px;">Statistics:</label>
                                <input type="text" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[text_label_statistics]" value="<?php echo esc_attr($opts['text_label_statistics']); ?>" style="width: 100%;">
                            </div>
                            <div>
                                <label style="display: block; font-weight: 500; margin-bottom: 5px;">Marketing:</label>
                                <input type="text" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[text_label_marketing]" value="<?php echo esc_attr($opts['text_label_marketing']); ?>" style="width: 100%;">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Button Text Labels</th>
                    <td>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px 30px; align-items: start;">
                            <div>
                                <label style="display: block; font-weight: 500; margin-bottom: 5px;">Deny All:</label>
                                <input type="text" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[text_button_deny_all]" value="<?php echo esc_attr($opts['text_button_deny_all']); ?>" style="width: 100%;">
                            </div>
                            <div>
                                <label style="display: block; font-weight: 500; margin-bottom: 5px;">Allow Selection:</label>
                                <input type="text" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[text_button_allow_selection]" value="<?php echo esc_attr($opts['text_button_allow_selection']); ?>" style="width: 100%;">
                            </div>
                            <div>
                                <label style="display: block; font-weight: 500; margin-bottom: 5px;">Allow All:</label>
                                <input type="text" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[text_button_allow_all]" value="<?php echo esc_attr($opts['text_button_allow_all']); ?>" style="width: 100%;">
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            
            <!-- Icon URL - Ocupa toda la fila -->
            <table class="form-table">
                <tr>
                    <th scope="row">Icon URL</th>
                    <td>
                        <input type="text" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[image]" value="<?php echo esc_attr($opts['image']); ?>" style="width:100%;">
                    </td>
                </tr>
            </table>
            
            <!-- Layout de 2 columnas desde aquí -->
            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                <!-- Columna izquierda -->
                <div style="flex: 1; min-width: 300px;">
                    <!-- Configuración básica -->
                    <table class="form-table">
                        <tr>
                            <th scope="row">Minimized Icon Position</th>
                            <td>
                                <select name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[icon_position]">
                                    <option value="left" <?php selected($opts['icon_position'], 'left'); ?>>Left</option>
                                    <option value="right" <?php selected($opts['icon_position'], 'right'); ?>>Right</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Show Minimized Icon When Closed</th>
                            <td>
                                <input type="checkbox" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[show_minimized_icon]" value="1" <?php checked($opts['show_minimized_icon'], '1'); ?>>
                                <span>Enabled</span>
                            </td>
                        </tr>
                    </table>
                    
                    <!-- Colores del Banner -->
                    <h3 style="font-weight: bold; font-size: 1.1em; text-decoration: underline; margin-top: 20px;">Banner Colors:</h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Banner Background Color</th>
                            <td><input type="color" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[color_bannerBg]" value="<?php echo esc_attr($opts['color_bannerBg']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Banner Text Color</th>
                            <td><input type="color" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[color_bannerText]" value="<?php echo esc_attr($opts['color_bannerText']); ?>"></td>
                        </tr>
                    </table>
                    
                    <!-- Botones principales -->
                    <h3 style="font-weight: bold; font-size: 1.1em; text-decoration: underline; margin-top: 20px;">Primary Button Colors:</h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Primary Button Background (Allow All)</th>
                            <td><input type="color" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[color_buttonPrimaryBg]" value="<?php echo esc_attr($opts['color_buttonPrimaryBg']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Primary Button Text</th>
                            <td><input type="color" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[color_buttonPrimaryText]" value="<?php echo esc_attr($opts['color_buttonPrimaryText']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Primary Button Hover Background</th>
                            <td><input type="color" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[color_buttonPrimaryHoverBg]" value="<?php echo esc_attr($opts['color_buttonPrimaryHoverBg']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Primary Button Hover Text</th>
                            <td><input type="color" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[color_buttonPrimaryHoverText]" value="<?php echo esc_attr($opts['color_buttonPrimaryHoverText']); ?>"></td>
                        </tr>
                    </table>
                    
                    <!-- Switches -->
                    <h3 style="font-weight: bold; font-size: 1.1em; text-decoration: underline; margin-top: 20px;">Selection Switch Colors:</h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Selection Switch Active Color</th>
                            <td><input type="color" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[color_switchOnBg]" value="<?php echo esc_attr($opts['color_switchOnBg']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Selection Switch Inactive Color</th>
                            <td><input type="color" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[color_switchOffBg]" value="<?php echo esc_attr($opts['color_switchOffBg']); ?>"></td>
                        </tr>
                    </table>
                </div>
                
                <!-- Columna derecha -->
                <div style="flex: 1; min-width: 300px;">
                    <!-- Botones secundarios -->
                    <h3 style="font-weight: bold; font-size: 1.1em; text-decoration: underline; margin-top: 20px;">Secondary Button Colors:</h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Secondary Buttons Background (Deny/Selection)</th>
                            <td><input type="color" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[color_buttonSecondaryBg]" value="<?php echo esc_attr($opts['color_buttonSecondaryBg']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Secondary Buttons Text</th>
                            <td><input type="color" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[color_buttonSecondaryText]" value="<?php echo esc_attr($opts['color_buttonSecondaryText']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Secondary Buttons Hover Background</th>
                            <td><input type="color" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[color_buttonSecondaryHoverBg]" value="<?php echo esc_attr($opts['color_buttonSecondaryHoverBg']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Secondary Buttons Hover Text</th>
                            <td><input type="color" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[color_buttonSecondaryHoverText]" value="<?php echo esc_attr($opts['color_buttonSecondaryHoverText']); ?>"></td>
                        </tr>
                    </table>
                    
                    <!-- Botón de cierre -->
                    <h3 style="font-weight: bold; font-size: 1.1em; text-decoration: underline; margin-top: 20px;">Close Button Colors:</h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Close Button Background</th>
                            <td><input type="color" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[color_closeBtnBg]" value="<?php echo esc_attr($opts['color_closeBtnBg']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Close Button Text Color</th>
                            <td><input type="color" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[color_closeBtnColor]" value="<?php echo esc_attr($opts['color_closeBtnColor']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Close Button Hover Background</th>
                            <td><input type="color" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[color_closeBtnHoverBg]" value="<?php echo esc_attr($opts['color_closeBtnHoverBg']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Close Button Hover Text Color</th>
                            <td><input type="color" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[color_closeBtnHoverColor]" value="<?php echo esc_attr($opts['color_closeBtnHoverColor']); ?>"></td>
                        </tr>
                    </table>
                    
                    <!-- Icono minimizado -->
                    <h3 style="font-weight: bold; font-size: 1.1em; text-decoration: underline; margin-top: 20px;">Minimized Icon:</h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Minimized Icon Background</th>
                            <td><input type="color" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[color_iconBg]" value="<?php echo esc_attr($opts['color_iconBg']); ?>"></td>
                        </tr>
                    </table>
                    
                    <!-- Tamaños de fuente -->
                    <h3 style="font-weight: bold; font-size: 1.1em; text-decoration: underline; margin-top: 20px;">Font Sizes:</h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Banner Text Font Size</th>
                            <td><input type="text" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[font_bannerText]" value="<?php echo esc_attr($opts['font_bannerText']); ?>" placeholder="e.g. 16px" style="width:100px;"></td>
                        </tr>
                        <tr>
                            <th scope="row">Options Font Size (Preferences, etc.)</th>
                            <td><input type="text" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[font_optionText]" value="<?php echo esc_attr($opts['font_optionText']); ?>" placeholder="e.g. 15px" style="width:100px;"></td>
                        </tr>
                        <tr>
                            <th scope="row">Buttons Font Size (Allow/Deny)</th>
                            <td><input type="text" name="<?php echo WP_CONSENT_BANNER_OPTION_KEY; ?>[font_buttonText]" value="<?php echo esc_attr($opts['font_buttonText']); ?>" placeholder="e.g. 14px" style="width:100px;"></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div style="margin-top: 30px;">
                <?php submit_button(); ?>
                <?php
                $reset_url = wp_nonce_url(
                    admin_url('admin-post.php?action=wp_consent_banner_reset'),
                    'wp_consent_banner_reset'
                );
                ?>
                <a href="<?php echo $reset_url; ?>" class="button button-secondary" style="margin-left: 10px;" onclick="return confirm('Are you sure you want to reset all settings to default values?');">Reset to Defaults</a>
            </div>
        </form>
    </div>
    <?php
}

/**
 * ====== FRONTEND: Inyectar CSS + JS ======
 */
add_action('wp_head', 'wp_consent_banner_inject_consent_mode', 1);
add_action('wp_enqueue_scripts', 'wp_consent_banner_enqueue_frontend');

/**
 * Inject Google Consent Mode default command in head
 * This must load before GTM to prevent "consent not configured" error
 */
function wp_consent_banner_inject_consent_mode() {
    ?>
    <script>
    // Google Consent Mode Default Command
    // This sets the default state BEFORE GTM loads, preventing "consent not configured" errors
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    
    // Set default consent state to denied (except Necessary which are always granted)
    gtag('consent', 'default', {
        'ad_storage': 'denied',
        'analytics_storage': 'denied',
        'ad_user_data': 'denied',
        'ad_personalization': 'denied',
        'functionality_storage': 'granted',  // Necessary - always granted
        'personalization_storage': 'denied',
        'security_storage': 'granted',         // Necessary - always granted
        'wait_for_update': 500
    });
    </script>
    <?php
}
function wp_consent_banner_enqueue_frontend() {
    $opts = wp_consent_banner_get_options();

    wp_enqueue_style(
        'wp-consent-banner-style',
        plugin_dir_url(__FILE__) . 'dist/consent-banner.css'
    );

    $config = [
        'text' => $opts['text'],
        'image' => $opts['image'],
        'iconPosition' => $opts['icon_position'],
        'showMinimizedIcon' => ($opts['show_minimized_icon'] === '1'),
        'colors' => array_filter($opts, fn($k) => strpos($k, 'color_') === 0, ARRAY_FILTER_USE_KEY),
        'fontSizes' => [
            'bannerText' => $opts['font_bannerText'],
            'optionText' => $opts['font_optionText'],
            'buttonText' => $opts['font_buttonText']
        ],
        'labels' => [
            'necessary' => $opts['text_label_necessary'],
            'preferences' => $opts['text_label_preferences'],
            'statistics' => $opts['text_label_statistics'],
            'marketing' => $opts['text_label_marketing']
        ],
        'buttonTexts' => [
            'denyAll' => $opts['text_button_deny_all'],
            'allowSelection' => $opts['text_button_allow_selection'],
            'allowAll' => $opts['text_button_allow_all']
        ]
    ];

    // Limpiar prefijos color_
    $config['colors'] = array_combine(
        array_map(fn($k) => str_replace('color_', '', $k), array_keys($config['colors'])),
        array_values($config['colors'])
    );

    wp_register_script(
        'wp-consent-banner-script',
        plugin_dir_url(__FILE__) . 'dist/consent-banner.js',
        [],
        null,
        true
    );

    wp_add_inline_script(
        'wp-consent-banner-script',
        'window.ConsentBannerConfig = ' . wp_json_encode($config) . ';',
        'before'
    );

    wp_enqueue_script('wp-consent-banner-script');
}