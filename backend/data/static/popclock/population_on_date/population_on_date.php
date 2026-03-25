<script type="text/javascript">
$(function ( )
{
<?php if (isset($_GET['date'])): ?>
	var default_date = new Date(<?php print($year); ?>, <?php print($month); ?>, <?php print($day); ?>);

<?php else: ?>
	var default_date = new Date(config.components.pop_on_date.default_year, config.components.pop_on_date.default_month - 1, config.components.pop_on_date.default_day);

<?php endif; ?>

	popOnDateViewController = new PopOnDateViewController(api, 'us-pop-on-date', 'date', '.select-date', '.pop-count', default_date);
	popOnDateViewController.shareLink = popOnDateViewController.jQueryEquivalent.find('.share a:eq(1)'); // download and share link
	popOnDateViewController.shareLinkOriginalHref = popOnDateViewController.shareLink.attr('href'); // download and share original HREF
	var preparingDataForDate = popOnDateViewController.prepareDataForDate();

<?php if ( _PAGE_ == 'embed' || _PAGE_ == '/embed'): ?>

	preparingDataForDate.done(function ( ) {

		var date = popOnDateViewController.defaultDate;

		var replaceDate = date instanceof Date ? $.datepicker.formatDate('mm/dd/yy', date) : date;
		share_pt_text = share_pt_text.replace('%date%', replaceDate).replace('%population%', popOnDateViewController.population);
		share_fb_text = share_fb_text.replace('%date%', replaceDate).replace('%population%', popOnDateViewController.population);
		share_tw_text = share_tw_text.replace('%date%', replaceDate).replace('%population%', popOnDateViewController.population);
		share_em_text = share_em_text.replace('%date%', replaceDate).replace('%population%', popOnDateViewController.population);

		var loaded_share = load_share(date instanceof Date ? $.datepicker.formatDate('yymmdd', date) : date);

		loaded_share.done(function ( ) {

			$('#download-this').addClass('disabled');
			$('#download-this').click(function ( event ) {
				event.preventDefault();
				return false;
			});
			popOnDateViewController.printLink = popOnDateViewController.jQueryEquivalent.parent().find('#print-this')
			popOnDateViewController.printLinkOriginalHref = popOnDateViewController.printLink.attr('href');
		});
	});

<?php endif; ?>

});
</script>
	<div id="us-pop-on-date" class="component-header vertical-gradient shadow full-border beveled ">
		<form><input id="date" type="text" tabindex="-1" disabled="disabled" /></form>
        <a id="select-date-image" class="select-date" href="#dateselect">select date</a>
		<p id="seldate"></p>
		<p class="share"><a href="#pop_on_date-footnote"></a> | <a href="./embed.php?component=pop_on_date"></a></p>
		<div class="ffix">&nbsp;</div>
	</div>
