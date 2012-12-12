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
 * Display related posts.
 *
 * @since   0.1
 * @uses    WP_Query
 * @param   object $post The post object for which to find related posts.
 * @return  void
 */
function pmg_related($post)
{
    $allowed_types = PMG_Related_Base::get_types();

    // "whitelist" post types.
    if(!isset($allowed_types[$post->post_type]))
        return false;

    $relatedby = PMG_Related_Base::opt($post->post_type);

    // no taxonomies to relate things.
    if(!$relatedby)
        return false;

    // let uses hook in early, ignoring the rest of what happens here.
    if($res = apply_filters('pmg_related_display_pre', false, $post, $relatedby))
        return $res;

    // build the taxonomy query
    $tq = array('relation' => 'OR');
    foreach($relatedby as $rb)
    {
        $taxes = PMG_Related_Base::get_taxonomies_for_type($post->post_type);

        if(!isset($taxes[$rb]))
            continue;

        $terms = get_the_terms($post->ID, $rb);

        if(is_wp_error($terms) || !$terms)
            continue;

        $tq[] = array(
            'taxonomy'  => $rb,
            'field'     => 'id',
            'terms'     => array_keys($terms),
        );
    }

    $related = new WP_Query(array(
        'post__not_in'   => array($post->ID),
        'post_type'      => apply_filters(
            'pmg_related_post_type', $post->post_type, $post),
        'tax_query'      => $tq,
        'orderby'        => 'rand',
        'posts_per_page' => apply_filters(
            'pmg_related_post_number', PMG_Related_Base::opt("number_{$post->post_type}", 3), $post),
    ));

    // wrapped in output buffering so we can filter it later.
    ob_start();
    do_action('pmg_related_display', $related, $post, $relatedby);
    $res = ob_get_clean();

    // you could use this to change things or cache things.
    echo apply_filters('pmg_related_display_post', $res, $post, $relatedby);

    // back to normal
    wp_reset_query();
    wp_reset_postdata();
}

/**
 * A default display functionality for the related posts.
 *
 * @since   0.1
 * @param   WP_Query $related
 * @return  void
 */
function pmg_related_display($related)
{
    if(!is_singular())
        return;

    if(!$related->have_posts())
        return;

    ?>
    <h2 class="related-header"><?php _e('Related Posts', 'pmg-related'); ?></h2>
    <ul class="related-posts-list">
        <?php while($related->have_posts()): $related->the_post(); ?>
        <li <?php post_class('related-post-item'); ?>>
            <a href="<?php the_permalink(); ?>"
               title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
        </li>
        <?php endwhile; ?>
    </ul>
    <?php
}
