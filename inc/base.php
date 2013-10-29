<?php
/**
 * PMG Related
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

/**
 * Base class for the plugin.
 *
 * @since       0.1
 * @category    WordPress
 * @package     PMG_Related
 */
abstract class PMG_Related_Base
{
    /**
     * Settings key
     *
     * @since   0.1
     */
    const SETTING = 'pmg_related_options';

    /**
     * Container for instances
     *
     * @access  private
     * @var     object (subclasses of PMG_Related_Base)
     * @static
     */
    private static $reg = array();

    public static function instance()
    {
        $cls = get_called_class();
        !isset(self::$reg[$cls]) && self::$reg[$cls] = new $cls();
        return self::$reg[$cls];
    }

    public static function init()
    {
        add_action('plugins_loaded', array(static::instance(), '_setup'));
    }

    abstract public function _setup();

    final public static function opt($key, $default='')
    {
        $opts = get_option(self::SETTING, array());
        return !empty($opts[$key]) ? $opts[$key] : $default;
    }

    final public static function get_types()
    {
        return apply_filters('pmg_related_post_types', get_post_types(array(
            'public'  => true,
        ), 'objects'));
    }

    final public static function get_taxonomies_for_type($type)
    {
        return apply_filters("pmg_related_{$type}_taxonomies", get_taxonomies(array(
            'object_type' => array($type),
        ), 'objects'));
    }
}
