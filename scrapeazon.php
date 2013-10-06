<?php
/*
Plugin Name: ScrapeAZon
Plugin URI: http://www.timetides.com/scrapeazon-plugin-wordpress
Description: Retrieves Amazon.com reviews for products you choose to display on your Wordpress blog.
Version: 1.1.0
Author: James R. Hanback, Jr.
Author URI: http://www.timetides.com
License: GPL3

/*  Copyright 2011	James R. Hanback, Jr.  (email : james@jameshanback.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

# Load plugin files and configuration
$scrplugin = plugin_basename(__FILE__); 
$scrpath = plugin_dir_path(__FILE__);
$scrpath .= '/scrapeazon-functions.php';
include_once($scrpath);

// Create admin page and settings, if required
if (is_admin()) {
   add_action('admin_menu','scrapeazon_admin_add_page');
   add_action('admin_init','scrapeazon_register_settings');
}

add_filter("plugin_action_links_$scrplugin", 'scrapeazon_settings_link' );

// Add shortcode functionality
add_shortcode( 'scrapeazon', 'scrapeazon_shortcode' );
?>