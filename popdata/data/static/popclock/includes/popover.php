<script type="text/javascript">
var popoverTopIsSet = false;
function show_popover ( ) {
	$popoverOverlay.show();
	$popoverContainer.show();

	position_popover();
}

function position_popover ( ) {
	var $window = $(window);

	
	var top = $window.scrollTop() + Math.max(0, $window.height() / 2 - $popoverContainer.outerHeight(true) / 2);
	var left = Math.max(0, $window.width() / 2 - $popoverContainer.outerWidth(true) / 2);

	// Center container
	if ( ! popoverTopIsSet ) 
		$popoverContainer.css('top', top);
	$popoverContainer.css('left', left);
	
}

function resize_popover ( width, height ) {
	// Set frame width and height
	$popoverFrame.width(width).height(height);

	position_popover();
	popoverTopIsSet = true;
}

function hide_popover ( ) {
	$popoverFrame.attr('src', 'about:blank');
	$popoverOverlay.hide();
	$popoverContainer.hide();
	popoverTopIsSet = false;
}

$(function ( )
{
	// Sharing popup
	$popoverOverlay = $('#popover-overlay');
	$popoverContainer = $('#popover');
	$popoverFrame = $popoverContainer.find('iframe');

	$popoverContainer.find('a').click(function ( ) {
		hide_popover();
		return false;
	});

	$popoverOverlay.click(function ( ) {
		hide_popover();
		return false;
	});

	$('.share').each(function() {
		var $theContainer = $(this);
		$theContainer.find('a:eq(1)').click(function ( event ) {
			event.preventDefault();
			$popoverFrame.attr('src', this.href);
			show_popover();
			return false;
		});
	});

	$(window).resize(resize_popover);
});
</script>
<div id="popover-overlay">&nbsp;</div>
<div id="popover">
	<a href="#close">close</a>
	<iframe src="about:blank"></iframe>
</div>
