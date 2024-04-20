<?php
/**
 * Plugin Name: Advanced Classic Editor
 * Description: Coverts your classic WordPress editor to an advanced visual editor
 * Plugin URI: https://dedidata.com
 * Author: DediData
 * Author URI: https://dedidata.com
 * Version: 3.1.3
 * Requires at least: 6.0
 * Tested up to: 6.4
 * Requires PHP: 7.0
 * License: GPL v3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: best-editor
 *
 * @package Best_Editor
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( '\DediData\Plugin_Autoloader' ) ) {
	require 'includes/DediData/class-plugin-autoloader.php';
}
// Set name spaces we use in this plugin
new \DediData\Plugin_Autoloader( array( 'DediData', 'BestEditor' ) );
/**
 * The function BEST_EDITOR returns an instance of the Best_Editor class.
 *
 * @return object an instance of the \BestEditor\Best_Editor class.
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
function BEST_EDITOR() { // phpcs:ignore Squiz.Functions.GlobalFunction.Found, WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return \BestEditor\Best_Editor::get_instance( __FILE__ );
}
BEST_EDITOR();
