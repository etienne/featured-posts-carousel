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
    register_activation_hook(__FILE__, array($this, 'install'));
    load_plugin_textdomain($this->textdomain, false, basename(dirname(__FILE__) ) . '/languages');
    add_image_size('featured-posts-carousel', 400, 150, true);
    add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    add_action('add_meta_boxes', array($this, 'add_meta_box'));
    add_action('save_post', array($this, 'save_post'));
    add_action('admin_init', array($this, 'admin_init'));
  }
  
  public function install() {
    add_option('featured_posts_carousel', array('count' => '3', 'delay' => '5'));
  }
  
  public function enqueue_assets() {
    wp_enqueue_script('featured-posts-carousel', plugins_url('featured-posts-carousel.js', __FILE__), array('jquery'), '0.1');
    wp_enqueue_style('featured-posts-carousel', plugins_url('featured-posts-carousel.css', __FILE__), array(), '0.1');
  }
  
  public function add_meta_box() {
    // Add custom field "featured" for posts
    add_meta_box('featured-post-meta', __('Featured posts carousel', $this->textdomain), array($this, 'print_meta_box'), 'post', 'side');
  }
  
  public function print_meta_box($post) {
    require 'views/meta_box.php';
  }
  
  public function save_post($post_id) {
    // Verify nonce
    if (!isset($_POST['featured-posts-carousel-nonce']) || !wp_verify_nonce($_POST['featured-posts-carousel-nonce'], 'metabox')) {
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
    
  public function admin_init() {
    register_setting('reading', 'featured_posts_carousel', array($this, 'validate_settings'));
    add_settings_section('featured_posts_carousel', __('Featured Posts Carousel', $this->textdomain), array($this, 'settings_section_text'), 'reading');
    add_settings_field('count', __('Show', $this->textdomain), array($this, 'count_field'), 'reading', 'featured_posts_carousel');
    add_settings_field('delay', __('Rotation delay', $this->textdomain), array($this, 'delay_field'), 'reading', 'featured_posts_carousel');
  }
  
  public function validate_settings($settings) {
    $valid_settings = array();
    foreach($settings as $setting => $value) {
      switch($setting) {
        case 'count':
          if (is_numeric($value)) {
            $valid_settings[$setting] = $value;
          }
          break;
        case 'delay':
          if (is_numeric($value)) {
            $valid_settings[$setting] = $value;
          }
          break;
      }
    }
    return $valid_settings;
  }
  
  public function settings_section_text() {
  }
  
  public function count_field() {
    $options = get_option('featured_posts_carousel');
    print "<input id='featured_posts_carousel_count' name='featured_posts_carousel[count]' size='5' type='text' value='{$options['count']}' /> " . __('posts', $this->textdomain);
  }

  public function delay_field() {
    $options = get_option('featured_posts_carousel');
    print "<input id='featured_posts_carousel_delay' name='featured_posts_carousel[delay]' size='5' type='text' value='{$options['delay']}' /> " . __('seconds', $this->textdomain);
  }
  
  public function get_featured_posts() {
    $options = get_option('featured_posts_carousel');
    $featured_posts = array();
    $featured_posts_query = new WP_Query(array( 'post_type' => 'post', 'posts_per_page' => $options['count'], 'meta_key' => $this->metafield, 'meta_value' => 1));
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
    require 'views/carousel.php';
  }
}

$featured_posts_carousel = new FeaturedPostsCarousel();

function featured_posts_carousel() {
  global $featured_posts_carousel;
  $featured_posts_carousel->print_carousel();
}

?>