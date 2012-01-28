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

    // Include JavaScript
    
    //
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
}

$featured_posts_carousel = new FeaturedPostsCarousel();

?>