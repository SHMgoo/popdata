<?php
if ( _PAGE_ == 'index' || _PAGE_ == '/world' ):
?>
<script type="text/javascript">
	$( function( ) {
		var footNoteContainer = $('#footnotes');
		footNoteContainer.find('h3').text(config.footnotes.label);
		for (var key in config.footnotes) {

			footNoteContainer.append('<div id="' + key + '-footnote">');
			if (key !== 'label' && isDefined(config.footnotes[key].label)) { // make sure not overal footnotes title label
				footNoteContainer.append('<h4>' + config.footnotes[key].label + '</h4>');
			}
			if (key !== 'label' && isDefined(config.footnotes[key].content)) {
				footNoteContainer.append(config.footnotes[key].content);
			}
			footNoteContainer.append('</div>');
		}
	});
</script>
	<!-- Footnotes -->
	<div id="footnotes">
		<h3></h3>
	</div>
<?php
else:
?>
<script type="text/javascript">
	$( function( ) {
		var footNoteContainer = $('#footnotes');
		var component = "<?php echo $component; ?>";
		footNoteContainer.find('h3').text(
			(isDefined(config.footnotes[component].label)) ? config.footnotes[component].label : config.footnotes.label
		);
		if (isDefined(config.footnotes[component].content)) {
			footNoteContainer.append('<div id="' + component + '-footnote">' + config.footnotes[component].content + '</div>');
		}
	});
</script>
    <!-- Footnotes -->
	<div id="footnotes">
		<h3></h3>
	</div>
<?php
endif;
?>