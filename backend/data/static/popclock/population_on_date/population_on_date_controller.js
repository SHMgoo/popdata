function PopOnDateViewController(api, usPopContainerID, dateInputID, clickEventSelector, labelSelector, defaultDate)
{
	var $controller = this;
	this.api = api;
	this.usPopContainerID = usPopContainerID;
	this.jQueryEquivalent = $('#' + this.usPopContainerID);
	
	this.shareLink;// = this.jQueryEquivalent.find('nav.share a:eq(1)');
	this.shareLinkOriginalHref;// = this.shareLink.prop('href');
	
	this.printLink;
	this.printLinkOriginalHref;
	
	// insert HTML to modify
	var labelString = function() {
		return config.components.pop_on_date.label.replace('%@', '\<span class="select-date">selected date</span>').replace('%@', '<span id="pop-tense">was</span>').replace('%@', '<span class="pop-count">Not Available</span>');
	}
	this.jQueryEquivalent.find('p:eq(0)').append(labelString).append('<br /><a id="select-date-image-under" class="select-date" href="#dateselect">select date</a>');
	
	this.dateDisplayLabel = this.jQueryEquivalent.find('p span:eq(0)');
	
	this.dateInputID = dateInputID;
	this.dateInputJQueryEquivalent = $('#' + this.dateInputID);
	this.clickEventSelector = clickEventSelector;
	
	this.labelSelector = labelSelector;
	this.displayLabel = $(this.labelSelector);
	
	this.now = Date.now();
        //console.log('cache='+config.components.us.cache);


	this.minDate = new Date(parseInt(config.components.pop_on_date.min_year), parseInt(config.components.pop_on_date.min_month) - 1, parseInt(config.components.pop_on_date.min_day));
	this.defaultDate = defaultDate;

console.log('defaultDate='+defaultDate);	
	this.population = 0;

	this.datePicker = this.dateInputJQueryEquivalent.datepicker({
		minDate: $controller.minDate,
		maxDate: -1,
		changeYear: true,
		changeMonth: true,
		
		dateFormat: "MM d, yy",
		onClose: function() {
			$controller.prepareDataForDate();
		}
	});
	this.datePicker.datepicker('setDate', this.defaultDate);


	$controller.dateDisplayLabel.text($controller.dateInputJQueryEquivalent.val());	
		
	this.jQueryEquivalent.find(this.clickEventSelector).click(function(event) {
		event.preventDefault();
		$controller.dateInputJQueryEquivalent.triggerHandler('focus');		
		return false;
	});
}
	
	PopOnDateViewController.prototype = {
		
		prepareDataForDate: function()
		{
			var $controller = this;
			var preparingDataForDate = $.Deferred();

			// Date
			var $dateObject = this.datePicker.datepicker('getDate');
			// Make sure defaultDate is updated
			this.defaultDate = (!$dateObject) ? this.defaultDate : $dateObject;
			// Parse date
			var year = $dateObject.getFullYear();
			var month = $dateObject.getMonth() + 1;
			if (month < 10) {
				month = '0' + month;
			}
			var day = $dateObject.getDate();
			if (day < 10) {
				day = '0' + day;
			}
			var formattedDate = year + '' + month + '' + day;
	
			// Share Link (if applicable)
			if (this.shareLink && this.shareLink.length == 1) {
				this.shareLink.attr('href', this.shareLinkOriginalHref + '&date=' + $.datepicker.formatDate('yymmdd', this.defaultDate));
			}
			// Print link
			$('#print-this').attr('href', this.printLinkOriginalHref + '&date=' + $.datepicker.formatDate('yymmdd', this.defaultDate));
			
			var gettingJSON = this.getJSON(formattedDate);
			
			gettingJSON.done(function( data ) {
				
				var usMainData = data.us;
//console.log('data.us.cache = '+data.us.cache);
                        
                             /* If the API was not available, change the 
                              * default day to today.
                              */ 
				if (usMainData && usMainData.population && typeof usMainData.population == 'number' && usMainData.population % 1 == 0) {
					$controller.population = addCommas(usMainData.population);
				} else {
    		                        $controller.population = 'Not Available';
				}

            /* If the API was not available (cache==true), then use today's date             * and the EDT population from the most recent midnight.
             * EDT is used, even when it's currently EST, in order to be 
             * consistent with a monthly report from the POP division.
             */
	             	$controller.displayLabel.text($controller.population);

		       //console.log($controller.dateInputJQueryEquivalent.val());

                       if (usMainData.cache == false) {
                          console.log('API was used for select a date');
	                  $controller.dateDisplayLabel.text($controller.dateInputJQueryEquivalent.val());
                       } else {
                           //console.log('cache was used for the pop, so reset date');
	                   //Disable the datepicker
	                   $("#date").css("display","none");	

                           $("#pop-tense").text("is");

                           var d = new Date();  
                           var mon = d.getMonth();
                           var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                           var year = d.getFullYear();
                           var day  = d.getDate();
                           var fullMonth = months[d.getMonth()];
                           var fullDate = fullMonth+' '+day+', '+year;
		           $controller.dateDisplayLabel.text(fullDate); 
                           //console.log('API not found, using cache for select-a-date');
                       }
				
		           preparingDataForDate.resolve();
			});
			
			return preparingDataForDate;
		},
	
		getJSON: function ( date )
		{
			return this.api.get('us', {date: date}, function(data, textStatus, JQXHR, promise) {
				promise.resolve(data);
			});
		}
	}
