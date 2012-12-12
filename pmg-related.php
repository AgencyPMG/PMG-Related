<?php
/**
 * Plugin Name: Related Posts by Taxonomies
 * Plugin URI: http://pmg.co/category/wordpress
 * Description: Related posts (or any custom post type) based on taxonomies.
 * Version: 0.1
 * Text Domain: pmg-related
 * Author: Christopher Davis
 * Author URI: http://pmg.co/people/chris
 * License: GPL-2.0+
 *
 * Copyright 2012 Performance Media Group <http://pmg.co>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category    WordPress
 * @package     PMG_Related
 * @author      Christopher Davis <http://pmg.co/people/chris>
 * @copyright   2012 Christopher Davis
 * @license     http://opensource.org/licenses/GPL-2.0 GPL-2.0+
 */

!defined('ABSPATH') && exit;

define('PMG_RELATED_PATH', plugin_dir_path(__FILE__));

if(version_compare(phpversion(), '5.3', '<'))
{
    require PMG_RELATED_PATH . 'inc/error.php';
    add_action('admin_notices', 'pmg_related_show_version_error');
    return;
}

require PMG_RELATED_PATH . 'inc/base.php';
require PMG_RELATED_PATH . 'inc/api.php';

if(is_admin())
{
    require PMG_RELATED_PATH . 'inc/admin.php';
    PMG_Related_Admin::init();
}

add_action('pmg_related_display', 'pmg_related_display');
