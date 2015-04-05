<?php
/**
 * Video sitemap
 *
 * Create a xml video sitemap file
 *
 * @package   video-sitemap
 * @author    bawd <churchill.c.j@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.accruemarketing.com/
 * @copyright 4-4-2015 BAWD
 *
 * @wordpress-plugin
 * Plugin Name: Simple video sitemap generator
 * Plugin URI:  http://buildawebdoctor.com
 * Description: Create a xml video sitemap file
 * Version:     1.0.0
 * Author:      Accure
 * Author URI:  http://www.accruemarketing.com/
 * Text Domain: video-sitemap-locale
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if (!defined("WPINC")) {
	die;
}

require_once(plugin_dir_path(__FILE__) . "VideoSitemap.php");

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook(__FILE__, array("VideoSitemap", "activate"));
register_deactivation_hook(__FILE__, array("VideoSitemap", "deactivate"));

VideoSitemap::get_instance();
