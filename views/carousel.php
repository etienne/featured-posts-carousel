<?php
if (!isset($this)) return false;
$options = get_option('featured_posts_carousel');
?>
<div class="featured-posts-carousel" data-delay="<?php print $options['delay'] ?>">
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
