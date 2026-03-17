<script type="text/javascript">
$(function ( )
{
   $('#most-populous-header h3').text(config.components.populous.label);
   mostPopulousViewController = new MostPopulousViewController(api, 'us-most-populous-container', $('#most-populous-header'));
   var creatingTables = mostPopulousViewController.createTables();

   // temporary - testing getCSV method
   creatingTables.done(function () {
            mostPopulousViewController.getCSV();
   });

<?php if ( _PAGE_ == 'embed' || _PAGE_ == '/embed'): ?>

   var loaded_share = load_share();
   loaded_share.done(function ( ) {
      $('#download-this').click(function ( ) {
            var gettingCSV = mostPopulousViewController.getCSV();
            gettingCSV.done(function ( csv ) {
                download_csv(csv, component);
         });
            return false;
      });
   });

<?php endif; ?>

<?php if ( _PAGE_ == 'print' ): ?>

   creatingTables.done(function ( ) {

      $('#us-most-populous-container table').css('display', 'table');

   });

<?php endif; ?>

}); 

</script>
   <div id="most-populous-header" class="component-header horizontal-gradient top-bottom-border outset">
      <h3></h3>
      <p class="share"><a href="#populous-footnote"></a> | <a href="./embed.php?component=populous"></a></p>
   </div>
<?php
$container_id = 'us-most-populous-container';
include('./includes/tabbed_tables.php');
?>
