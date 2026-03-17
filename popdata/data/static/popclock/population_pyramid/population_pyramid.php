<script type="text/javascript">
<?php 
	if(isset($_GET['date'])){
		$get_date = htmlspecialchars($_GET['date']);
	}
?>
$(function ( )
{
	popPyramidViewController = new PopPyramidViewController(api, animator, 'population-pyramid-graph', 'population-pyramid-slider', 0.3<?php if ( isset($get_date) ): echo ', ' . $get_date ; endif; ?>);

<?php if ( _PAGE_ == 'index' || _PAGE_ == 'embed' ): ?>
	popPyramidViewController.shareLink = $('#population-pyramid-wrapper').parent().find('nav.share a:eq(1)'); // download and share link
	popPyramidViewController.shareLinkOriginalHref = popPyramidViewController.shareLink.prop('href'); // download and share original HREF
<?php endif; ?>

// we don't need to display a chart for print - image will be added later
<?php if ( _PAGE_ == 'index' || _PAGE_ == 'embed' ): ?>
	popPyramidViewController.layoutViews(config.components.pyramid.min_year, config.components.pyramid.max_year);
	var creatingChart = popPyramidViewController.createChart();
<?php endif; ?>

<?php if ( _PAGE_ == 'data_tables' || _PAGE_ == 'print' ): ?>

	popPyramidViewController.minYear = config.components.pyramid.min_year;
	popPyramidViewController.maxYear = config.components.pyramid.max_year;
	var creatingDataTables = popPyramidViewController.createDataTables();
	creatingDataTables.done(function(){
		$('#retrieving').hide();
	});

<?php endif; ?>

<?php if ( _PAGE_ == 'embed' ): ?>

	creatingChart.done(function ( ) {

		var gettingImage = popPyramidViewController.getImage();

		gettingImage.done(function ( url ) {

			// Set the image for print
			share_image = url;
			share_fb_href += '&image=' + url;

			var date = popPyramidViewController.defaultDate;
			var loaded_share = load_share(date instanceof Date ? $.datepicker.formatDate('yymmdd', date) : date);

			loaded_share.done(function ( ) {

				$('#download-this').click(function ( ) {
					var gettingCSV = popPyramidViewController.getCSV();
					gettingCSV.done(function ( csv ) {
						download_csv(csv, component);
					});
					return false;
				});
			});
		});
	});
<?php endif; ?>

});
</script>
<?php if ( _PAGE_ == 'data_tables' ): ?>
	<!--<p id="retrieving">Retrieving data2</p>-->
    <div id="population-pyramid-graph"></div>

<?php else: ?>
            <div id="population-age-header" class="component-header horizontal-gradient bottom-border outset">
                <h3>United States Population by Age and Sex</h3>
                <p class="share"><a href="#pyramid-footnote"></a> | <a href="./embed.php?component=pyramid"></a> | <a aria-label="United States Population by Age and Sex graph" href="./data_tables.php?component=pyramid"></a></p>
            </div>
            <div id="population-pyramid-wrapper">
                	<?php if ( _PAGE_ == 'print' && isset($_GET['image']) ): ?>
                		<img src="<?php echo htmlentities($_GET['image']); ?>"/>
                	<?php endif; ?>
                <div id="population-pyramid-graph">

                	<!-- split bar chart, generated using Raphael -->
                </div>
				<a aria-label="Play and pause button for the Population by Age and Sex animated graph" id="population-pyramid-play-button" href="">play/pause</a>
                <div id="population-pyramid-slider"><!-- year slider, generated using Raphael --></div>
            </div>
<?php endif; ?>
