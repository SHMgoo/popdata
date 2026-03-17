<script type="text/javascript">
$(function ( )
{
		$('#highest-density-header h3').text(config.components.density.label);
		highestDensityViewController = new HighestDensityViewController(api, 'us-highest-density-container', $('#highest-density-header'));
		var creatingTables = highestDensityViewController.createTables();

<?php if ( _PAGE_ == 'embed' || _PAGE_ == '/embed'): ?>

	var loaded_share = load_share();

	loaded_share.done(function ( ) {

		$('#download-this').click(function ( ) {
			var gettingCSV = highestDensityViewController.getCSV();
			gettingCSV.done(function ( csv ) {
				download_csv(csv, component);
			});
			return false;
		});
	});
<?php endif; ?>

<?php if ( _PAGE_ == 'print' ): ?>

	creatingTables.done(function ( ) {

		$('#us-highest-density-container table').css('display', 'table');

	});

<?php endif; ?>

});
</script>
            <div id="highest-density-header" class="component-header horizontal-gradient top-bottom-border outset" style="padding-top: 10px;padding-bottom: 24px;">
                <h3></h3>
<!--
                <p class="share"><a href="#footnotes"></a> | <a href="<?php //echo _APPROOT_;?>embed.php?component=density" target="_blank"></a></p>
-->
                <p id="download_share"><a href="#footnotes">Learn More</a> | <a href="<?php echo _APPROOT_;?>embed.php?component=density" target="_blank">Download and Share</a></p>

            </div>
<?php
$container_id = 'us-highest-density-container';
include('includes/tabbed_tables.php');
?>
