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
 * Admin area functionality.
 *
 * @since       0.1
 * @category    WordPress
 * @package     PMG_Related
 */
class PMG_Related_Admin extends PMG_Related_Base
{
    public function _setup()
    {
        add_action('admin_init', array($this, 'settings'));
        add_action('admin_menu', array($this, 'page'));
    }

    /**
     * Registers the setting for this plugin and adds all the fields/sections
     * we need.
     *
     * @access  public
     * @since   0.1
     * @uses    register_setting
     * @uses    add_settings_section
     * @uses    add_settings_field
     * @uses    get_post_types
     * @uses    get_taxonomies
     * @return  void
     */
    public function settings()
    {
        register_setting(
            self::SETTING,
            self::SETTING,
            array($this, 'cleaner')
        );

        add_settings_section(
            'relationships',
            __('Relationships', 'pmg-related'),
            array($this, 'section_cb'),
            self::SETTING
        );

        add_settings_section(
            'numberposts',
            __('Number of Related Posts to Display', 'pmg-related'),
            '__return_false',
            self::SETTING
        );

        foreach(self::get_types() as $pt => $obj)
        {
            $taxes = self::get_taxonomies_for_type($pt);

            if(!$taxes)
                continue;

            add_settings_field(
                "{$pt}-related",
                esc_html($obj->label),
                array($this, 'field_cb'),
                self::SETTING,
                'relationships',
                array('type' => $pt, 'type_obj' => $obj, 'taxonomies' => $taxes)
            );

            add_settings_field(
                "{$pt}-related-number",
                esc_attr($obj->label),
                array($this, 'number_cb'),
                self::SETTING,
                'numberposts',
                array('type' => $pt, 'label_for' => sprintf('%s[%s]', self::SETTING, "number_{$pt}"))
            );
        }
    }

    /**
     * Adds the settings page for this plugin.
     *
     * @access  public
     * @since   0.1
     * @uses    add_options_page
     * @return  void
     */
    public function page()
    {
        add_options_page(
            __('Related Posts', 'pmg-related'),
            __('Related Posts', 'pmg-related'),
            'manage_options',
            'pmg-related-posts',
            array($this, 'page_cb')
        );
    }

    /********** Settings Callbacks **********/

    /**
     * Settings validation callback.
     *
     * @access  public
     * @since   0.1
     * @param   array $dirty The un-validated options
     * @return  array The validated options
     */
    public function cleaner($dirty)
    {
        $clean = array();
        $types = self::get_types();

        foreach($types as $t => $_)
        {
            $taxes = array_keys(self::get_taxonomies_for_type($t));

            if(!$taxes)
                continue;

            if(!empty($dirty[$t]) && is_array($dirty[$t]))
            {
                $clean[$t] = array_intersect($taxes, $dirty[$t]);
            }
            else
            {
                $clean[$t] = array();
            }

            $clean["number_{$t}"] = absint($dirty["number_{$t}"]);
        }

        return $clean;
    }

    /**
     * Show some help text for our settings.
     *
     * @access  public
     * @since   0.1
     * @return  void
     */
    public function section_cb()
    {
        echo '<p class="description">',
            __('By which taxonomies are related posts determined?', 'pmg-related'),
            '</p>';
    }

    /**
     * Settings field callback. Fires on every "related" type field on the admin
     * page.
     *
     * @access  public
     * @since   0.1
     * @param   array $args The arguments passed from add_settings_field above
     * @return  void
     */
    public function field_cb($args)
    {
        $taxes = $args['taxonomies'];
        $type = $args['type'];

        $on = self::opt($type, array());

        foreach($taxes as $t => $obj)
        {
            printf(
                '<label for="%1$s[%2$s][%3$s]"><input type="checkbox" '.
                'name="%1$s[%2$s][]" id="%1$s[%2$s][%3$s]" value="%3$s" %4$s /> '.
                '%5$s</label><br />',
                esc_attr(self::SETTING),
                esc_attr($type),
                esc_attr($t),
                checked(true, in_array($t, $on), false),
                esc_html($obj->label)
            );
        }
    }

    /**
     * Field callback for the "number of posts" field.
     *
     * @access  public
     * @since   0.1
     * @param   array $args The arguments passed from add_settings_field above
     * @return  void
     */
    public function number_cb($args)
    {
        $type = $args['type'];
        
        $val = self::opt("number_{$type}", 3);

        printf(
            '<input type="number" name="%1$s" id="%1$s" value="%2$s" '.
            'class="small-text" min="1" max="10" />',
            esc_attr($args['label_for']),
            absint($val)
        );
    }

    /********** Admin Page Callback *********/

    /**
     * Displays the admin page.
     *
     * @access  public
     * @since   0.1
     * @return  void
     */
    public function page_cb()
    {
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php _e('Related Posts by Taxonomy', 'pmg-related'); ?></h2>
            <?php settings_errors(self::SETTING); ?>
            <form method="post" action="<?php echo admin_url('options.php'); ?>">
                <?php
                settings_fields(self::SETTING);
                do_settings_sections(self::SETTING);
                submit_button(__('Save Settings', 'pmg-related'));
                ?>
            </form>
        </div>
        <?php
    }
}
