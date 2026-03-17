	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script src="./js/jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.min.js"></script>
	<script src="./js/jquery.ajaxhooks.custom.min.js" type="text/javascript"></script>
	<!-- <script src="./js/raphael/raphael-min.js"></script> -->

	<script src="./js/main.min.js"></script>
	<script src="./js/api.min.js"></script>
	<script src="./js/animator.min.js"></script>
	<script src="./js/counter.js"></script>
	<script src="./js/odometer.js"></script>
	<script src="./js/html_progress_bar.min.js"></script>
	<script src="./js/population.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="./population_counters/population_counters_controller.js"></script>
	<script type="text/javascript">
	var config = <?php echo json_encode($config); ?>;
	var api = new API(config.api.cache, config.api.methods);
	var animator = new Animator(100);
	</script>
