<script type="text/javascript">
$(function ( )
{
<?php if ( _PAGE_ == 'index' || _PAGE_ == 'embed' ): ?>

	$('#population-growth-header').find('h3').text(config.components.growth.label);
	popGrowthViewController = new PopGrowth(api, 'population-growth-wrapper', 'population-growth-chart', 'population-growth-overlay', config.components.growth.min_year, config.components.growth.max_year);
	var creatingChart = popGrowthViewController.createChart();

<?php else: ?>
	$('#population-growth-header').find('h3').text(config.components.growth.label);
	popGrowthViewController = new PopGrowth(api, 'population-growth-wrapper', 'population-growth-chart', 'population-growth-overlay', config.components.growth.min_year, config.components.growth.max_year);
	var creatingTables = popGrowthViewController.createDataTables();
	creatingTables.done(function(){
		$('#retrieving').hide();
	});
<?php endif; ?>

<?php if ( _PAGE_ == 'embed' ): ?>

	popGrowthViewController.overlayProgressBarWidth = 50;
	popGrowthViewController.overlayProgressBarHeight = 10;

	creatingChart.done(function ( ) {

		var gettingImage = popGrowthViewController.getImage();

		gettingImage.done(function ( url ) {

			// Set the image for print
			share_image = url;
			share_fb_href += '&image=' + url;

			var loaded_share = load_share();

			//Replaced. The getCSV returned data in the wrong order. Took the logic
			//from population_growth_controller.js getCSV.
			//loaded_share.done(function ( ) {
			//	$('#download-this').click(function ( ) {
			//		var gettingCSV = popGrowthViewController.getCSV();
			//		gettingCSV.done(function ( csv ) {
			//			download_csv(csv, component);
			//		});
			//		return false;
			//	});
			//});
			loaded_share.done(function ( ) {
				$('#download-this').click(function ( ) {
					var csv = [];
					var data = [];
					var gettingJSON = popGrowthViewController.getJSON();
					var years = popGrowthViewController.yearsArray().reverse();
					gettingJSON.done( function( data ) {
						var csv = [];     
                        
						data.west.values.reverse();
						data.midwest.values.reverse();
						data.northeast.values.reverse();
						data.south.values.reverse();
						        
						for (var i = 0; i < years.length; i++) {
							var headerrow = [
								"Region",
								"Population",
								"Percentage"
							];
							var northeast = [
								"Northeast",
								addCommas(data.northeast.values[i].population),
								intPercentString(data.northeast.values[i].percentage, 2) + "%"
							];
							var midwest = [
								"Midwest",
								addCommas(data.midwest.values[i].population),
								intPercentString(data.midwest.values[i].percentage, 2) + "%"
			
							];
							var west = [
								"West",
								addCommas(data.west.values[i].population),
								intPercentString(data.west.values[i].percentage, 2) + "%"
			
							];
							var south = [
								"South",
								addCommas(data.south.values[i].population),
								intPercentString(data.south.values[i].percentage, 2) + "%"
			
							];
							var yearObject = {
								"title": years[i].toString(),
								"data": [headerrow, northeast, midwest, west, south]
							};
							csv.push(yearObject);
						}
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
	<!--<p id="retrieving">Retrieving data</p>-->
    <div id="population-growth-wrapper"></div>

<?php else: ?>
            <div id="population-growth-header" class="component-header horizontal-gradient bottom-border outset">
                <h3 aria-label="Graph showing population growth by region"></h3>
                <p class="share"><a href="#growth-footnote"></a> | <a href="./embed.php?component=growth"></a> | <a aria-label="United States Population Growth by Region graph"  href="./data_tables.php?component=growth"></a></p>
            </div>
            <!-- population distribution chart w/ overlay -->
       	    <div id="population-growth-wrapper">
                <div id="population-growth-overlay" class="vertical-gradient shadow beveled">
                	<h4>Regional Populations</h4>
                    <table>
                    	<thead>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <!-- raphy chart uses the inline style attribute for its width and height setting -->
                <div id="population-growth-chart">                
                	<?php if ( _PAGE_ == 'print' && isset($_GET['image']) ): ?>
                		<img src="<?php echo htmlentities($_GET['image']); ?>" />
                	<?php endif; ?>
                	<!-- stacked line chart, generated using Raphael -->
                </div>
                <p id="pop-growth-legend"><span class="northeast-legend">&nbsp;</span>Northeast <span class="midwest-legend">&nbsp;</span>Midwest <span class="west-legend">&nbsp;</span>West <span class="south-legend">&nbsp;</span>South</p>
            </div>
            <!-- population distribution tables -->
<?php endif; ?>
