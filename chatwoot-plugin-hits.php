<?php
/**
 * Plugin Name:     Chatwoot Plugin Hits
 * Plugin URI:      https://www.chatwoot.com/
 * Description:     Chatwoot Plugin for WordPress (Hits version). This plugin helps you integrate a second instance of Chatwoot live-chat widget.
 * Author:          antpb
 * Author URI:      chatwoot.com
 * Text Domain:     chatwoot-plugin-hits
 * Version:         0.1.0
 *
 * @package         chatwoot-plugin-hits
 */

add_action('admin_enqueue_scripts', 'chatwoot_hits_admin_styles');
/**
 * Load Chatwoot Hits Admin CSS.
 *
 * @since 0.1.0
 *
 * @return void
 */
function chatwoot_hits_admin_styles() {
  wp_enqueue_style('chatwoot-hits-admin-styles', plugin_dir_url(__FILE__) . '/admin.css');
}

add_action('wp_enqueue_scripts', 'chatwoot_hits_assets');
/**
 * Load Chatwoot Hits Assets.
 *
 * @since 0.1.0
 *
 * @return void
 */
function chatwoot_hits_assets() {
  if (!chatwoot_hits_should_execute()) {
    return;
  }
  wp_enqueue_script('chatwoot-hits-client', plugins_url('/js/chatwoot.js', __FILE__));
}

add_action('wp_enqueue_scripts', 'chatwoot_hits_load');
/**
 * Initialize embed code options for Hits version.
 *
 * @since 0.1.0
 *
 * @return void
 */
function chatwoot_hits_load() {
  if (!chatwoot_hits_should_execute()) {
    return;
  }
  $chatwoot_hits_url = get_option('chatwootHitsSiteURL');
  $chatwoot_hits_token = get_option('chatwootHitsSiteToken');
  $chatwoot_hits_widget_locale = get_option('chatwootHitsWidgetLocale');
  $chatwoot_hits_widget_type = get_option('chatwootHitsWidgetType');
  $chatwoot_hits_widget_position = get_option('chatwootHitsWidgetPosition');
  $chatwoot_hits_launcher_text = get_option('chatwootHitsLauncherText');

  wp_localize_script('chatwoot-hits-client', 'chatwoot_hits_token', $chatwoot_hits_token);
  wp_localize_script('chatwoot-hits-client', 'chatwoot_hits_url', $chatwoot_hits_url);
  wp_localize_script('chatwoot-hits-client', 'chatwoot_hits_widget_locale', $chatwoot_hits_widget_locale);
  wp_localize_script('chatwoot-hits-client', 'chatwoot_hits_widget_type', $chatwoot_hits_widget_type);
  wp_localize_script('chatwoot-hits-client', 'chatwoot_hits_widget_position', $chatwoot_hits_widget_position);
  wp_localize_script('chatwoot-hits-client', 'chatwoot_hits_launcher_text', $chatwoot_hits_launcher_text);
}

/**
 * Determine if the Chatwoot Hits widget should execute on the current page.
 *
 * @return bool True if the plugin should execute, false otherwise.
 */
function chatwoot_hits_should_execute() {
  $allowed_urls = array_filter(array_map('trim', explode("\n", get_option('chatwootHitsAllowedURLs', ''))));
  $current_url = $_SERVER['REQUEST_URI'];

  foreach ($allowed_urls as $url) {
    if (substr($url, -1) !== '/') {
      $url .= '/';
    }
    if (strpos($current_url, $url) === 0) {
      return true;
    }
  }
  return false;
}

add_action('admin_menu', 'chatwoot_hits_setup_menu');
/**
 * Set up Settings options page for Hits version.
 *
 * @since 0.1.0
 *
 * @return void
 */
function chatwoot_hits_setup_menu() {
  add_options_page('Chatwoot Hits Options', 'Chatwoot Hits Settings', 'manage_options', 'chatwoot-hits-plugin-options', 'chatwoot_hits_options_page');
}

add_action('admin_init', 'chatwoot_hits_register_settings');
/**
 * Register Settings for Hits version.
 *
 * @since 0.1.0
 *
 * @return void
 */
function chatwoot_hits_register_settings() {
  add_option('chatwootHitsSiteToken', '');
  add_option('chatwootHitsSiteURL', '');
  add_option('chatwootHitsWidgetLocale', 'en');
  add_option('chatwootHitsWidgetType', 'standard');
  add_option('chatwootHitsWidgetPosition', 'right');
  add_option('chatwootHitsLauncherText', '');
  add_option('chatwootHitsAllowedURLs', '');

  register_setting('chatwoot-hits-plugin-options', 'chatwootHitsSiteToken');
  register_setting('chatwoot-hits-plugin-options', 'chatwootHitsSiteURL');
  register_setting('chatwoot-hits-plugin-options', 'chatwootHitsWidgetLocale');
  register_setting('chatwoot-hits-plugin-options', 'chatwootHitsWidgetType');
  register_setting('chatwoot-hits-plugin-options', 'chatwootHitsWidgetPosition');
  register_setting('chatwoot-hits-plugin-options', 'chatwootHitsLauncherText');
  register_setting('chatwoot-hits-plugin-options', 'chatwootHitsAllowedURLs');
}

/**
 * Render settings page for Hits version.
 *
 * @since 0.1.0
 *
 * @return void
 */
function chatwoot_hits_options_page() {
  ?>
  <div>
    <h2>Chatwoot Hits Settings</h2>
    <form method="post" action="options.php" class="chatwoot-hits--form">
      <?php settings_fields('chatwoot-hits-plugin-options'); ?>
      <div class="form--input">
        <label for="chatwootHitsSiteToken">Chatwoot Hits Website Token</label>
        <input type="text" name="chatwootHitsSiteToken" value="<?php echo get_option('chatwootHitsSiteToken'); ?>" />
      </div>
      <div class="form--input">
        <label for="chatwootHitsSiteURL">Chatwoot Hits Installation URL</label>
        <input type="text" name="chatwootHitsSiteURL" value="<?php echo get_option('chatwootHitsSiteURL'); ?>" />
      </div>
      <div class="form--input">
        <label for="chatwootHitsAllowedURLs">Allowed URLs (one per line)</label>
        <textarea name="chatwootHitsAllowedURLs" rows="5" cols="50"><?php echo esc_textarea(get_option('chatwootHitsAllowedURLs')); ?></textarea>
      </div>
      <div class="form--input">
        <label for="chatwootHitsWidgetType">Widget Design</label>
        <select name="chatwootHitsWidgetType">
          <option value="standard" <?php selected(get_option('chatwootHitsWidgetType'), 'standard'); ?>>Standard</option>
          <option value="expanded_bubble" <?php selected(get_option('chatwootHitsWidgetType'), 'expanded_bubble'); ?>>Expanded Bubble</option>
        </select>
      </div>
      <div class="form--input">
        <label for="chatwootHitsWidgetPosition">Widget Position</label>
        <select name="chatwootHitsWidgetPosition">
          <option value="left" <?php selected(get_option('chatwootHitsWidgetPosition'), 'left'); ?>>Left</option>
          <option value="right" <?php selected(get_option('chatwootHitsWidgetPosition'), 'right'); ?>>Right</option>
        </select>
      </div>
      <div class="form--input">
        <label for="chatwootHitsLauncherText">Launcher Text (Optional)</label>
        <input type="text" name="chatwootHitsLauncherText" value="<?php echo get_option('chatwootHitsLauncherText'); ?>" />
      </div>
      <?php submit_button(); ?>
    </form>
  </div>
  <?php
}
