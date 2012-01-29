jQuery(function($) {
	var currently_hovering = false;
  
	$('div.featured-posts-carousel ul.articles a').hover(function() {
	  $('div.featured-posts-carousel ul.articles li').removeClass('active');
	  $(this).parent().addClass('active');
	  $('div.featured-posts-carousel ul.thumbnails li').removeClass('active');
	  var article_index = $(this).parent().index() + 1;
	  $('div.featured-posts-carousel ul.thumbnails li:nth-child(' + article_index + ')').addClass('active');
	}).mouseenter(function () {
	  currently_hovering = true;
	}).mouseleave(function () {
	  currently_hovering = false;
	})
    
	// Rotation
  var interval = $('div.featured-posts-carousel').attr('data-delay') * 1000;
	window.setInterval(function() {
	  if (currently_hovering == true) {
	    return false;
	  }
	  var current_index = $('div.featured-posts-carousel ul.thumbnails li.active').index();
    var count = $('div.featured-posts-carousel ul.thumbnails li').length;
	  var next_index = current_index + 2;
    if (next_index > count) {
      next_index = 1;
    }
	  $('div.featured-posts-carousel ul.thumbnails li').removeClass('active');
	  $('div.featured-posts-carousel ul.articles li').removeClass('active');
	  $('div.featured-posts-carousel ul.thumbnails li:nth-child(' + next_index + ')').addClass('active');
	  $('div.featured-posts-carousel ul.articles li:nth-child(' + next_index + ')').addClass('active');
	}, interval);
});
