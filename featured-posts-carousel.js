jQuery(function($) {
	// Front page carousel
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
	window.setInterval(function() {
	  if (currently_hovering == true) {
	    return false;
	  }
	  var current_index = $('div.featured-posts-carousel ul.thumbnails li.active').index();
	  var next_index = null;
	  switch (current_index) {
	    case 0:
	      next_index = 2;
	      break;
	    case 1:
	      next_index = 3;
	      break;
	    case 2:
	      next_index = 1;
	      break;
	  }
	  $('div.featured-posts-carousel ul.thumbnails li').removeClass('active');
	  $('div.featured-posts-carousel ul.articles li').removeClass('active');
	  $('div.featured-posts-carousel ul.thumbnails li:nth-child(' + next_index + ')').addClass('active');
	  $('div.featured-posts-carousel ul.articles li:nth-child(' + next_index + ')').addClass('active');
	}, 5000);
});
