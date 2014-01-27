<?php
/*
Plugin Name: Open Flash Chart Core
Plugin Script: open-flash-chargs-core.php
Plugin URI: http://sudarmuthu.com/wordpress/open-flash-chart-core
Description: Does little else but load the core Open Flash Chart PHP library for any Plugin that wants to utilize it.
Version: 0.6
License: GPL
Author: Sudar
Author URI: http://sudarmuthu.com/

=== RELEASE NOTES ===
2008-12-28 - v0.1 - First Version
2009-01-27 - v0.2 - Second Version
2009-01-30 - v0.3 - Third Version
2009-01-31 - v0.4 - Fourth Version
2013-04-23 - v0.5 - Fixed a security issue in the library
2013-04-23 - v0.5.1 - Fixed couple of typos in the readme file
2014-01-27 - v0.6 - Fixed warnings

*/

/**
* Guess the wp-content and plugin urls/paths
*/
if ( !defined('WP_CONTENT_URL') )
    define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if ( !defined('WP_CONTENT_DIR') )
    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );

if (!defined('PLUGIN_URL'))
    define('PLUGIN_URL', WP_CONTENT_URL . '/plugins/');
if (!defined('PLUGIN_PATH'))
    define('PLUGIN_PATH', WP_CONTENT_DIR . '/plugins/');

define('SMOFCDIR', dirname(__FILE__) . '/');
define('SM_OFC_PHP_INC', SMOFCDIR . 'open-flash-chart-2/php-ofc-library/');

define('SM_OFC_INC_URL', PLUGIN_URL . dirname(plugin_basename(__FILE__)) . '/open-flash-chart-2/');

/**
 * Stores whether openflashchart has already been loaded by another extension.
 */
$openflashchart_loaded = null;

/**
 * What is the openflashchart_version for the version that we're loading?
 */
$openflashchart_version = 2;

/**
 * Ensure that the open-flash-chart.php class hasn't been loaded before trying to load it again.
 */
if (!class_exists('open_flash_chart')) {
	require_once(SM_OFC_PHP_INC . "open-flash-chart.php");
} else {
	$openflashchart_loaded = true;
}

/**
 * Register Scripts
 *
 * @since 0.6
 */
function smofc_register_scripts() {
    // Register scripts
    wp_register_script('json', SM_OFC_INC_URL . "js/json/json2.js", false, '');
    wp_register_script('swfobject',SM_OFC_INC_URL . "js/swfobject.js", false, '');
}
add_action( 'init', 'smofc_register_scripts' );

/**
 * Draw normal options page.
 */
function openflashchart_core_options_page() {
	global $openflashchart_loaded;

	if ($openflashchart_loaded)	{
			echo '<div id="message" class="updated fade">Another extension has already loaded an older version of the Open Flash Chart, so to avoid conflicts we will not load our version. This means that you don\'t have the latest version of openflashchart available to your extensions. Contact the author of the conflicting plugin, and let them know about this plugin.</div>';
	}
?>

<div style="margin:50px auto;">
	<h3>Open Flash Chart PHP core API library, version 2</h3>
	<p>Version 2 is available to your other extentions. Read more details about <a href="http://teethgrinder.co.uk/open-flash-chart-2/">Open Flash Chart 2</a>.</p>
    <h3>Constants</h3>
    <p>Following are the values of the constants exposed by this Plugin. You can use these constants in your Plugin to refer to Open Flash Chart Libraries</p>
    <p>SM_OFC_PHP_INC: <?php echo SM_OFC_PHP_INC ?></p>
    <p>SM_OFC_INC_URL: <?php echo SM_OFC_INC_URL ?></p>
</div>

<?php
// Display credits in footer
add_action( 'in_admin_footer', 'openflashchart_admin_footer' );
}

/**
 * Add menu item to Options menu.
 */
function openflashchart_core_options() {
	if (function_exists('add_options_page')) {
		add_options_page('openflashchart Core', 'openflashchart Core', 'manage_options', 'openflashchart-api-core', 'openflashchart_core_options_page');
	}
}

/**
 * Adds the settings link in the Plugin page. Based on http://striderweb.com/nerdaphernalia/2008/06/wp-use-action-links/
 * @staticvar <type> $this_plugin
 * @param <type> $links
 * @param <type> $file 
 */
function openflashchart_filter_plugin_actions($links, $file) {
    static $this_plugin;
    if( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);

    if( $file == $this_plugin ) {
        $settings_link = '<a href="options-general.php?page=openflashchart-api-core">' . _('Manage') . '</a>';
        array_unshift( $links, $settings_link ); // before other links
    }
    return $links;
}

/**
 * Hook called when the Plugin is installed
 */
function openflashchart_core_install() {
    //These urls can be used by other Plugins
    update_option("SM_OFC_PHP_INC", SM_OFC_PHP_INC); // PHP include path
    update_option("SM_OFC_INC_URL", SM_OFC_INC_URL); // include url
}

/**
 * Hook called when the Plugin is uninstalled
 */
function openflashchart_core_uninstall() {
    delete_option("SM_OFC_PHP_INC");
    delete_option("SM_OFC_INC_URL");
}

/**
 * Adds Footer links. Based on http://striderweb.com/nerdaphernalia/2008/06/give-your-wordpress-plugin-credit/
 */
function openflashchart_admin_footer() {
	$plugin_data = get_plugin_data( __FILE__ );
    printf('%1$s ' . __("plugin") .' | ' . __("Version") . ' %2$s | '. __('by') . ' %3$s<br />', $plugin_data['Title'], $plugin_data['Version'], $plugin_data['Author']);
}

/**
 * Trigger the adding of the menu option.
 */
add_action('admin_menu', 'openflashchart_core_options');
register_activation_hook(__FILE__,'openflashchart_core_install');
add_filter( 'plugin_action_links', 'openflashchart_filter_plugin_actions', 10, 2 );

// when uninstalled
if (function_exists("register_uninstall_hook")) {
    register_uninstall_hook(__FILE__, 'openflashchart_core_uninstall');
}
?>
