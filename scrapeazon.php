<?php
/*
 * Plugin Name: ScrapeAZon
 * Plugin URI: http://www.timetides.com/scrapeazon-plugin-wordpress
 * Description: Retrieves Amazon.com reviews for products you choose from the Amazon Product Advertising API and displays those reviews in pages, posts, or as a widget on your WordPress blog. ScrapeAZon 2.0 features a new responsive layout option, better integration with the WordPress Settings API, contextual help, and a new exponential backoff method to help prevent errors when accessing the Amazon Product Advertising API from high-traffic sites.
 * Version: 2.0.4
 * Author: James R. Hanback, Jr.
 * Author URI: http://www.timetides.com
 * License: GPL3
 * Text Domain: scrapeazon
 * Domain Path: /lang/
 */

/*
 * Copyright 2011-2014	James R. Hanback, Jr.  (email : james@jameshanback.com)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Load plugin files and configuration
$szPlugin = plugin_basename(__FILE__); 
$szPPath = plugin_dir_path(__FILE__);
$szPPath .= '/szclasses.php';
include_once($szPPath);

$szOpts = new szWPOptions;
$szReqs = new szRequirements;
$szShcd = new szShortcode;

$szOpts->szRequireStyles();

// Localization
add_action('plugins_loaded', array(&$szReqs, 'szLoadLocal'));

if (is_admin()) {
    add_action('admin_init', array(&$szOpts, 'szRegisterSettings'));
    add_action('admin_init', array(&$szReqs, 'szHideNotices'));
    add_action('admin_menu', array(&$szOpts, 'szAddAdminPage'));
    add_action('admin_notices', array(&$szReqs, 'szShowNotices'));
}

add_filter("plugin_action_links_$szPlugin", array(&$szOpts, 'szOptionsLink'));

// Add shortcode functionality
add_shortcode( 'scrapeazon', array(&$szShcd, 'szParseShortcode') );

// Add widget
add_action('widgets_init',create_function('', 'return register_widget("szWidget");'));

?>