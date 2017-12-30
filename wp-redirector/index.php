<?php
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
  $url = explode(',', str_replace(' ', '', $option))[0];
  header('Location: ' . esc_url($url), true, 301);
  exit;
});

// Class as a namespace
class WpRedirector {
  static public $fieldId = 'redirect_url';
  static public $fieldPage = 'general';
  static public function echoField(array $args)
  {
    $id = $args['id'];
    $value = esc_html(get_option($id));
    echo "<input name=\"$id\" id=\"$id\" type=\"text\" value=\"$value\" class=\"regular-text code\">";
  }
}
