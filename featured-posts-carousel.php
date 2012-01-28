<?php
/*
Plugin Name: Featured Posts Carousel
Plugin URI: http://www.molotov.ca/
Description: A simple carousel with rotating posts and associated images.
Version: 0.1
Author: Étienne Després
Author URI: http://www.molotov.ca
License: GPL2
*/
/*  Copyright 2012  Étienne Després

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class FeaturedPostsCarousel {
  public $textdomain = 'featured-posts-carousel';
  public $metafield = 'featured_post';
  
  public function __construct() {
    load_plugin_textdomain($this->textdomain, false, basename(dirname(__FILE__) ) . '/languages');
    
    add_action('add_meta_boxes', array($this, 'add_meta_box'));
    add_action('save_post', array($this, 'save_post'));
    add_image_size('featured-posts-carousel', 400, 150, true);

  }
  
  public function add_meta_box() {
    // Add custom field "featured" for posts
    add_meta_box('featured-post-meta', __('Featured posts carousel', $this->textdomain), array($this, 'print_meta_box'), 'post', 'side');
  }
  
  public function print_meta_box($post) {
    ?>
    <input type="hidden" name="featured-posts-carousel-nonce" value="<?php print wp_create_nonce(basename(__FILE__)) ?>" />
    <input type="checkbox" name="<?php print $this->metafield ?>" value="1" <?php checked(get_post_meta($post->ID, $this->metafield, true), 1); ?> />
    <label for="<?php print $this->metafield ?>"><?php _e('Feature this post on home page') ?></label>
    <?php
  }
  
  public function save_post($post_id) {
    // Verify nonce
    if (!isset($_POST['featured-posts-carousel-nonce']) || !wp_verify_nonce($_POST['featured-posts-carousel-nonce'], basename(__FILE__))) {
      return $post_id;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return $post_id;
    }

    // Check permissions
    if ('page' == $_POST['post_type']) {
      if (!current_user_can('edit_page', $post_id)) {
        return $post_id;
      }
    } elseif (!current_user_can('edit_post', $post_id)) {
      return $post_id;
    }

    $old_value = get_post_meta($post_id, $this->metafield, true);
    $new_value = null;
    if (isset($_POST[$this->metafield])) {
      $new_value = $_POST[$this->metafield];
    }

    if ($new_value && $new_value != $old_value) {
      update_post_meta($post_id, $this->metafield, $new_value);
    } elseif ('' == $new_value && $old_value) {
      delete_post_meta($post_id, $this->metafield, $old_value);
    }
  }
  
  public function get_featured_posts() {
    $featured_posts = array();
    $featured_posts_query = new WP_Query(array( 'post_type' => 'post', 'posts_per_page' => 3, 'meta_key' => $this->metafield, 'meta_value' => 1 ));
    while ($featured_posts_query->have_posts()) {
      $featured_posts_query->the_post();
      $featured_posts[] = array(
        'title' => $featured_posts_query->post->post_title,
        'permalink' => get_permalink($featured_posts_query->post->ID),
        'thumbnail' => get_the_post_thumbnail($featured_posts_query->post->ID, 'featured-posts-carousel')
      );
    }
    return $featured_posts;
  }
  
  public function print_carousel() {
    $posts = $this->get_featured_posts();
    $i = $j = 0;
    ?>
    <div class="featured-posts-carousel">
      <ul class="thumbnails">
        <?php foreach($posts as $post): ?>
				<li<?php $i == 0 ? print ' class="active"' : '' ?>><?php print $post['thumbnail'] ?></li>
				<?php $i++ ?>
        <?php endforeach; ?>
      </ul>
      <ul class="articles">
        <?php foreach($posts as $post): ?>
				<li<?php $j == 0 ? print ' class="active"' : '' ?>><a href="<?php print $post['permalink'] ?>"><?php print $post['title'] ?></a></li>
				<?php $j++ ?>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php
  }
}

$featured_posts_carousel = new FeaturedPostsCarousel();

function featured_posts_carousel() {
  global $featured_posts_carousel;
  $featured_posts_carousel->print_carousel();
}

?>