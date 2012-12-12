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
 * Display an admin notice alerting folks that the plugin can't be used due to
 * a PHP version issue.
 *
 * @since   0.1
 * @return  void
 */
function pmg_related_show_version_error()
{
    ?>
    <div class="error">
        <p><?php _e('PMG Related Posts by Taxonomy required PHP 5.3+', 'pmgrelated'); ?></p>
    </div>
    <?php
}
