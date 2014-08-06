jQuery(document).ready(function($){

	// Disable click on empty hrefs
	$( 'a[href=#]' ).not( $( '#wpadminbar a' ) ).click(function(){
		return false;
	});

	// Hide carousel controls when there's only one slide
	$('.eb-carousel').each(function() {
	  var items = $('.eb-carousel-inner .item').length;
		if(items <= 1) {
			$('.eb-ecarousel-control').hide();
			$('.carousel-indicators').hide();
		}
	});

	// Toggle the menu on mobile devices
	$('.menu-toggle').click(function(){
		$('.menu').toggleClass('expanded');
	});

    //Clear the text in the search box on focus
		var div = $('div.month-list'),
    ul = $('ul.months'),
    // unordered list's left margin
    ulPadding = 15;

		    //Get menu width
    var divWidth = div.width();

    //Remove scrollbars
    div.css({overflow: 'hidden'});

    //Find last image container
    var lastLi = ul.find('li:last-child');

    //When user move mouse over menu
    div.mousemove(function(e){

      //As images are loaded ul width increases,
      //so we recalculate it each time
      var ulWidth = lastLi[0].offsetLeft + lastLi.outerWidth() + ulPadding;

      var left = (e.pageX - div.offset().left) * (ulWidth-divWidth) / divWidth;
      div.scrollLeft(left);
    });
		$('.dropdown-toggle').click(function(){
			var showClass = $(this).attr("data-toggle");
			$('.' + showClass).toggle();
			return false;
		});
		$('tr td:last-child a[data-toggle="popover"]').popover({
			trigger: 'click',
			html: true,
			placement: 'left'
		});
		$('tr td:nth-child(6n) a[data-toggle="popover"]').popover({
			trigger: 'click',
			html: true,
			placement: 'left'
		});
		$('[data-toggle="popover"]').popover({
		    trigger: 'click',
				html: true,
				placement: 'right'
		});

		var $visiblePopover;

		$('body').on('click', '[data-toggle="popover"]', function(e) {
		    var $this = $(this);
		    // check if the one clicked is now shown
		    if ($this.data('popover').tip().hasClass('in')) {

		        // if another was showing, hide it
		        $visiblePopover && $visiblePopover.popover('hide');

		        // then store the current popover
		        $visiblePopover = $this;
		    } else { // if it was hidden, then nothing must be showing
		        $visiblePopover = '';
		    }
				return false;
		});

	// Bootstrap Collapse
	$(".collapse").collapse();
});
