<?php
/*
Plugin Name: WP Redirector
Plugin URI: https://github.com/dsktschy/wp-redirector
Description: WP Redirector redirects all requests to template pages to the specified URL.
Version: 1.0.0
Author: dsktschy
Author URI: https://github.com/dsktschy
License: GPL2
*/

// Add fields to the setting page
add_filter('admin_init', function() {
  add_settings_field(
    WpRedirector::$fieldId,
    preg_match('/^ja/', get_option('WPLANG')) ?
      'テンプレートページへのリクエストに対するリダイレクトURL' :
      'Redirection URL for requests to template pages',
    ['WpRedirector', 'echoField'],
    WpRedirector::$fieldPage,
    'default',
    ['id' => WpRedirector::$fieldId]
  );
  register_setting(WpRedirector::$fieldPage, WpRedirector::$fieldId);
});

// Redirect all requests to template pages if specified
add_action('template_redirect', function() {
  $option = get_option(WpRedirector::$fieldId);
  if ($option === '') return;
  $url = array_map(
    ['WpRedirector', 'encodeSpace'],
    array_map('trim', explode(',', $option))
  )[0];
  if ($url === '') return;
  header('Location: ' . esc_url($url), true, 301);
  exit;
});

// Class as a namespace
class WpRedirector {
  static public $fieldId = 'wp_redirector';
  static public $fieldPage = 'general';
  // Outputs an input element with initial value
  static public function echoField(array $args)
  {
    $id = $args['id'];
    $value = esc_html(get_option($id));
    echo "<input name=\"$id\" id=\"$id\" type=\"text\" value=\"$value\" class=\"regular-text code\">";
  }
  // Encode spaces
  static public function encodeSpace($url)
  {
    return str_replace(' ', '%20', $url);
  }
}
