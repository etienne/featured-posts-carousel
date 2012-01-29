<?php if (!isset($this)) return false; ?>
<input type="hidden" name="featured-posts-carousel-nonce" value="<?php print wp_create_nonce(basename(__FILE__)) ?>" />
<input type="checkbox" name="<?php print $this->metafield ?>" value="1" <?php checked(get_post_meta($post->ID, $this->metafield, true), 1); ?> />
<label for="<?php print $this->metafield ?>"><?php _e('Feature this post on home page') ?></label>
