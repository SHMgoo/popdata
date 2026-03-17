function PopPyramidViewController(api, animator, pyramidGraphContainerID, pyramidSliderContainerID, speed, defaultDate)
{	
	this.api = api;
	this.animator = animator;
	this.pyramidGraph;
	this.pyramidGraphContainerID = pyramidGraphContainerID;
	this.pyramidSliderContainerID = pyramidSliderContainerID;
	this.speed = speed; // in seconds
	this.defaultDate = defaultDate; // year for embed code
	
	this.minYear;
	this.maxYear;
	
	this.shareLink; // download and share link
	this.shareLinkOriginalHref; // download and share original HREF
		
	this.medianValue      = 0;
	this.medianAgesByYear = [];
	this.ages             = [];
	this.years            = [];
	this.yearsArray = function()
	{
		var years = [];
		for (var i = this.minYear; i <= this.maxYear; i++) {
			years.push(i);
		}
		return years;
	}
	this.maleAgesByYear   = [];
	this.femaleAgesByYear = [];

	this.isPlaying = false;
	this.lastChangedWithSeconds = 0;
	
	this.titleLabel = null;	
	this.interactionIsDisabled = false;
	
	this.$playButton = $('a#population-pyramid-play-button');
	this.pyramidCounter = new Counter(0, Counter.rate_per_millisecond(1, this.speed, Counter.milliseconds_per_unit('second')));
	
}
	PopPyramidViewController.prototype = {
		layoutViews: function(minYear, maxYear)
		{			
			var controller = this;
			this.minYear = minYear;
			this.maxYear = maxYear;	
	
			this.pyramidSlider = new Slider(this.pyramidSliderContainerID, this.minYear, this.maxYear, 16, 20, true, 45, 29);
				
			// build ages array for gutter label values
			for (var i = 0; i <= 100; i++) {
				if (i == 100) {
					this.ages.push(i+'+');
					continue;
				}
				this.ages.push(i);
			}
	
			this.pyramidGraph = new PyramidGraph(this.pyramidGraphContainerID, [20, 20, 40, 20], [10, 0, 5, 0], 45, 5, this);
			this.pyramidGraph.medianLineLabel.attr('text', '');
			this.pyramidGraph.medianLine.attr({ fill: '', stroke: '' });
		
			// set initial bars for the chart without data		
			for (var i = this.minYear; i <= this.maxYear; i++) {
				this.years.push(i);
	
				// prefill demography ages with 0
				var maleAges = [];
				var femaleAges = [];
				for(var j = 0; j < this.ages.length; j++) {
					maleAges.push(0);
					femaleAges.push(0);
				}
				this.maleAgesByYear.push(maleAges);
				this.femaleAgesByYear.push(femaleAges);
			}
	
			this.pyramidGraph.chart1MinValue = 0;
			this.pyramidGraph.chart2MinValue = this.pyramidGraph.chart1minValue;
			//this.pyramidGraph.render(this.ages, { fill: '#4889ac', stroke: 'none' }, { fill: '#d2825a', stroke: 'none' });
			this.pyramidGraph.render(this.ages, { fill: '#78909C', stroke: 'none' }, { fill: '#0095A8', stroke: 'none' });

			this.pyramidGraph.updateGraphWithData(this.maleAgesByYear[this.maleAgesByYear.length - 1], this.femaleAgesByYear[this.femaleAgesByYear.length - 1]);

			
			//this.pyramidSlider.render({ 'stroke-width': 2, stroke: '#002a48', fill: '90-#0e5990:49-#0f609e:50-#1773ad:51-#1b7fb5' }, { fill: 'r(0.5, 0.5)#fff-#fff:50-#2596d2-#a5d8f0:75-#2596d2', stroke: ''});
			//this.pyramidSlider.render({ 'stroke-width': 2, stroke: '#0095A8', fill: '#0095A8' }, { fill:  'r(0.5, 0.5)#fff-#fff:50-#2596d2-#a5d8f0:75-#2596d2', stroke: ''});
			//this.pyramidSlider.render({ 'stroke-width': 2, stroke: '#0095A8', fill: '#0095A8' }, { fill:  'r(0.5, 0.5)#fff-#fff:50-#2596d2-#a5d8f0:75-#2596d2', stroke: '#0095A8'});
			//this.pyramidSlider.render({ 'stroke-width': 1, stroke: '#0095A8', fill: '#0095A8' }, { fill:  'white', stroke: '#0095A8',  r: '7'});

            /* Outline of slider at bottom of pyramid */
			this.pyramidSlider.render({ 'stroke-width': 1, stroke: '#0095A8', fill: '#0095A8' }, { fill:  'white',  r: '7'});
			this.pyramidSlider.moveThumbToValue(this.years[this.years.length -1]);
			this.pyramidSlider.delegate = this;
			
			var sliderRaph = this.pyramidSlider.Raph;
			var tickMark = sliderRaph.path("M" + this.pyramidSlider.thumbMinX + ",10L" + this.pyramidSlider.thumbMinX + ",23");
			// because the first year may not be a decade year, we hide this mark - using it as a base
			tickMark.attr({ opacity: 0.0 });
			var tickMarks = [];
			for (var i = this.minYear; i <= this.maxYear; i++) {
				if (i/10 % 1 == 0) {
					tickMarks.push(tickMark.clone());
					tickMarks[tickMarks.length - 1].transform("t" + (this.pyramidSlider.thumbXFromValue(i) - this.pyramidSlider.thumbMinX) + ",0")
					.attr({ opacity: 1.0 })
					.toBack();
					sliderRaph.text((this.pyramidSlider.thumbXFromValue(i) - this.pyramidSlider.thumbMinX) + 32, 30, i.toString()).toBack();
				}
			}
			
			// event handling
			this.pyramidGraph.jQueryEquivalent.hover(
				function() {
					if (!controller.interactionIsDisabled) {
						controller.mouseIn();
					}
					
				},
				function() {
					if (!controller.interactionIsDisabled) {
						controller.mouseOut();
					}
				}
			);
			
			this.$playButton.click(function(event) {
				event.preventDefault();
				if (controller.isPlaying) {
					controller.pause()
	
				} else {
					controller.play();
				
				}
				return false;
			});
		},
		
		mouseIn: function()
		{
			// fade out large gutter labels
			// display row labels
			for (var i = 0; i < this.pyramidGraph.gutterValueLabels.length; i++) {
				if ( !this.pyramidGraph.interactionIsDisabled ) {
					var $label = this.pyramidGraph.gutterValueLabels[i];
					$label.attr({ opacity: 0.35 });
				}
			}
			//this.pyramidGraph.showLabels();
		},

		mouseOut: function()
		{
			// fade in large gutter labels
			// hide row labels
			for (var i = 0; i < this.pyramidGraph.gutterValueLabels.length; i++) {
				if ( !this.pyramidGraph.interactionIsDisabled ) {
					var $label = this.pyramidGraph.gutterValueLabels[i];
					$label.attr({ opacity: 0.75 });
				}
			}
			this.pyramidGraph.hideLabels();
		},
		
		play: function()
		{
			if	(this.isPlaying) this.pause();
			
			this.$playButton.addClass('playing');
			this.isPlaying = true;
			this.pyramidGraph.interactionIsDisabled = true;
		},
		
		pause: function()
		{
			this.$playButton.removeClass('playing');
			this.isPlaying = false;
			this.pyramidGraph.interactionIsDisabled = false;
		},
		
		getJSON: function ( )
		{
			var controller = this;
			return this.api.get('demographic', {daterange: controller.getDateRange()}, function (data, textStatus, jqXHR, promise) {
				promise.resolve(data);	
			});
		},
		
		getDateRange: function ( ) 
		{
			return this.minYear + '0701' + '-' + this.maxYear + '0701';
		},
		
		createChart: function()
		{
			var $controller   = this;
			var creatingChart = $.Deferred();
			var gettingJSON   = this.getJSON();
			
			gettingJSON.done(function( data ) {
	
				var componentData = data;
				var maxPercent    = config.components.pyramid.max_total_percentage; //Math.ceil(data.max_percent);
				$controller.pyramidGraph.chart1MaxValue = maxPercent;
				$controller.pyramidGraph.chart2MaxValue = maxPercent;
				
				var gutterLabelText = config.components.pyramid.x_axis_label_middle;
				$controller.pyramidGraph.xAxisMiddleLabel.attr('text', gutterLabelText);
				var modifier = config.components.pyramid.x_axis_label_modifier;
				$controller.pyramidGraph.xAxisLeftLabel.attr('text', maxPercent.toString() + modifier);
				$controller.pyramidGraph.xAxisRightLabel.attr('text', maxPercent.toString() + modifier);
				$controller.pyramidGraph.xAxisMiddleLabel.attr('text', config.x_axis_label_middle);
			
				var maleValues = data.male.values;
				var femaleValues = data.female.values;
				// Values are ordered from newest to oldest
				
				// because we aren't comparing against a zero indexed array
				// we will also want to iterate on the result of the max - min
				for (var i = 0; i < data.male.values.length; i++) {
					var year = getDateFromUnixEpoch(data.male.values[i].date).getUTCFullYear();
					var destinationIndex = percentForValueInRange($controller.minYear, $controller.maxYear, year) * ($controller.maxYear - $controller.minYear);
					var maleDataForYear = maleValues[i];
					var femaleDataForYear = femaleValues[i];
					if (typeof maleDataForYear !== 'undefined') {
						// we have valid data for the year
						// otherwise, we will use the default value of 0
						for (var j = 0; j <= 100; j++) {
							var maleAge = maleDataForYear.age_groups[j];
							$controller.maleAgesByYear[destinationIndex][j] = maleAge.total_percentage;
							
							var femaleAge = femaleDataForYear.age_groups[j];
							$controller.femaleAgesByYear[destinationIndex][j] = femaleAge.total_percentage;
						}
					}
				}
				$controller.pyramidGraph.updateGraphWithData($controller.maleAgesByYear[$controller.maleAgesByYear.length - 1], $controller.femaleAgesByYear[$controller.femaleAgesByYear.length - 1]);
				
				// we want to jump to a specific year
				if (typeof $controller.defaultDate !== 'undefined') {
					$controller.pyramidSlider.moveThumbToValue($controller.defaultDate);
				}
				$controller.animator.add(function ( ) { $controller.animatorDidFire(); });
				
				creatingChart.resolve();
			});
			
			return creatingChart;
		},
		
		createDataTables: function()
		{
			var controller     = this;
			var creatingTables = $.Deferred();
			var gettingCSV     = this.getCSV();
			gettingCSV.done( function( csv ) {
				html = '';
				$(csv).each(function() {	
					var year = this;
					var rows = year.data;
					html += '<h2>' + year.title + '</h2>';
					if ( config.components.pyramid.table_summary ) {
				// 508 compliance, create table summary
						html += '<table summary="' + config.components.pyramid.table_summary + '">';
	
					} else {
						html += '<table>';
						
					}
	
					//html += '<table>';
					altRow = true;
					for (var i = 0; i < rows.length; i++) {
						var row = rows[i];
						if ( i == 0 ) {
							html += '<thead><tr><th scope="row" class="cell0">' + row[0] + '</th><th class="cell1">' + row[1] + '</th><th class="cell2">' + row[2] + '</th></tr></thead>';
							
						} else {
							if ( i == 1 ) {
								
								html += '<tbody>';
							}
							
							if ( altRow ) {
								html += '<tr class="alt"><td class="cell0">' + row[0] + '</td><td class="cell1">' + row[1] + '</td><td class="cell2">' + row[2] + '</td></tr>';
								
							} else {
								html += '<tr><td class="cell0">' + row[0] + '</td><td class="cell1">' + row[1] + '</td><td class="cell2">' + row[2] + '</td></tr>';
								
							}
							
							if ( i == rows.length - 1 ) {
								html += '</tbody>';
							}
						}
						altRow = !altRow;
					}
					html += '</table>';
				});
				$('#' + controller.pyramidGraphContainerID).append(html);
				creatingTables.resolve();
			});
			return creatingTables;
		},
		
		getCSV: function () 
		{
			var controller  = this;
			var gettingCSV = $.Deferred();
			var gettingJSON = this.getJSON();
			var years = this.yearsArray().reverse();
			gettingJSON.done( function( data ) {
				var csv = [];
				for (var i = 0; i < years.length; i++) {
					
					var year = getDateFromUnixEpoch(data.male.values[i].date).getUTCFullYear();
					var maleValuesForYear = data.male.values[i].age_groups;
					var femaleValuesForYear = data.female.values[i].age_groups;
					 
					var rows = [];	
					
					var headerrow = [
						"Age",
						"Male % of Population",
						"Female % of Population"
					];
	
					rows.push(headerrow);
					
					for (var j = 0; j <= 100; j++) {

					    var malePer = ((maleValuesForYear[j].total_percentage*100/100).toFixed(2)).toString(); 
					    var femalePer = ((femaleValuesForYear[j].total_percentage*100/100).toFixed(2)).toString(); 
					    var ageRow = [
						( j == 100 ) ? j.toString() + '+' : j.toString(),
                                                        malePer + "%",
							femalePer + "%"
						];

						rows.push(ageRow);
					}
					var yearObject = {
						"title": years[i].toString(),
						"data": rows
					};
					csv.push(yearObject);				
				}
				gettingCSV.resolve( csv );
			});
			return gettingCSV;
		},

		/**
		 * Get SVG from chart
		 * 
		 * @return string Current SVG of chart
		 */
		getSVG: function ( )
		{
			var controller = this;
			return controller.pyramidGraph.Raph.toSVG();
		},
		
		getImage: function ( )
		{
			var controller = this;
			var gettingImage = $.Deferred();
			
			/*
			 * Process:
			 * 1. Get JSON 
			 * 2. Create image name from last updated date
			 * 3. Test to see if the image exists
			 *     3a. If the image exists, return image url
			 * 4. On done, create a new, larger div
			 * 5. Create new instance of the class
			 * 6. Call createChart
			 * 7. On done, get SVG
			 *     7a. Delete created elements (clean up)
			 * 8. Call svgtoimage
			 * 9. On done, return image url
			 */
			
			// Get JSON
			var gettingJSON = controller.getJSON();
			
			gettingJSON.done(function ( data ) {
			
				// Create image name
				// @todo - should get the current year instead of max year
				var name = 'population' + '_' + controller.maxYear + '_' + data.last_updated;
				var url = image_url(name);
				
				// Test to see if image exists
				var found_image = find_image(name);
				
				found_image.done(function ( ) {
					// The image exists!
					gettingImage.resolve(url);
				}).fail(function ( ) {
					// The image doesn't exist
					
					$('<div id="population-pyramid-wrapper-temp" style="display: none; position: relative;">\
							<div id="population-pyramid-graph-temp" style="width: 6.5in; height: 13in;"></div>\
							<div id="population-pyramid-slider-temp" style="display:none;"></div>\
						</div>').appendTo('body');
					
					// Create a new instance of the class
					var popPyramidViewController = new PopPyramidViewController(controller.api, controller.animator, 'population-pyramid-graph-temp', 'population-pyramid-slider-temp', controller.speed, controller.defaultDate);
					// Call layout views and prepare data
					popPyramidViewController.layoutViews(controller.minYear, controller.maxYear);

					var creatingChart = popPyramidViewController.createChart();
					
					creatingChart.done(function ( ) {
						
						// Make ready for image
						//popPyramidViewController.titleLabel.show();
						popPyramidViewController.pyramidGraph.showAllLabels();
						popPyramidViewController.interactionIsDisabled = true;
						popPyramidViewController.pyramidGraph.interactionIsDisabled = true;
						
						// Get SVG
						var svg = popPyramidViewController.getSVG();
						
						// Clean up
						$('#population-pyramid-wrapper-temp').remove();
						popPyramidViewController = null;
						
						// Call svgtoimage
						var creating_image = create_image(svg, name);
						
						creating_image.done(function ( ) {
							
							// Return image url
							gettingImage.resolve(url);
						});
					});
				});
			});
			
			return gettingImage;
		},
		
		/* slider delegate methods */	
		thumbDidBeginMove: function()
		{
			// slider thumb is being dragged
			this.pyramidGraph.interactionIsDisabled = true;
			this.pause();
		},
	
		thumbDidEndMove: function()
		{
			// slider thumb has stopped being dragged
			this.pyramidGraph.interactionIsDisabled = false;
		},
	
		thumbDidMoveToValue: function(year) 
		{
			// slider thumb was moved to value
			var yearIndex = $.inArray(year, this.years);
			if (typeof yearIndex !== 'undefined') {
				this.pyramidGraph.updateGraphWithData(this.maleAgesByYear[yearIndex], this.femaleAgesByYear[yearIndex], this.medianAgesByYear[yearIndex]);
				this.defaultDate = year;
				this.shareLink.prop('href', this.shareLinkOriginalHref + '&date=' + this.defaultDate);
			}
		},
		
		sliderClicked: function()
		{
			// slider was clicked
			// when the user clicks the slider while it is being played
			// we want to the slider to stop animating
			this.pause();
		},
		
		/* animator delegate */
		animatorDidFire: function ( totalSeconds )
		{
			if (this.isPlaying) {
				this.pyramidCounter.update();
				if (this.pyramidCounter.changed()) {
					var sliderValue = parseInt(this.pyramidSlider.value);
					var desiredValue = sliderValue + 1;
					if (desiredValue > this.pyramidSlider.maxValue) {
						desiredValue = this.pyramidSlider.minValue;
						
					}
					this.pyramidSlider.moveThumbToValue(desiredValue);
					this.lastChangedWithSeconds = totalSeconds;
					if (desiredValue == this.pyramidSlider.maxValue) {
						this.pause();
					}
				}
			}
		}
	}
