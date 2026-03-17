/*
The progress bar creates a raphael SVG (XML) element
the bar can have a min and max value - which can be set using the setValue method
	requires the percent for value in range method to be defined
setPercent should NOT be called directly
*/
function ProgressBar(containerID, width, height, trackColor, barColor, reversed)
{
	this.width       = width;
	this.height      = height;
	this.containerID = containerID	
	this.Raph        = Raphael(this.containerID, this.width, this.height);

	this.delegate;
	this.value       = 0;
	this.trackColor  = trackColor;
	/*this.trackColor  = '#FAF9F7';*/
	this.barColor    = barColor;
	this.minValue    = 0;
	this.maxValue    = 1;
	this.reversed    = (!reversed)
		? false
		: reversed;
	
	this.totalGain      = 0; // used in interval
	this.secondsPerRate = 0; // used in interval
	
	this.initialTotal;
	
	this.track;
	this.bar;
	this.render = function()
	{
		this.track = this.Raph.rect(0,0,this.width,this.height);
		this.track.attr('fill', this.trackColor);
		this.track.attr('stroke', '');
		this.bar = this.Raph.rect(0,0,this.width,this.height,this.height/2);
		this.bar.attr('fill', this.barColor);
		this.bar.attr('stroke', '');
	}
}
	ProgressBar.prototype.setPercent = function(percent)
	{
		if (percent > 1) {
			percent = 1;
			
		} else if (percent < 0) {
			percent = 0;
			
		}
		
		if (this.reversed) {
			percent = 1 - percent;
		}
		
		// we redraw the bar each time
		this.bar.remove();
		var newWidth = this.width * percent;
		if (percent < 0.000001) {
			newWidth = 0;
			
		} /*else if (newWidth < this.height) {
			newWidth = this.height;
			
		}*/
		this.bar = this.Raph.rect(0,0,newWidth,this.height) // removed ,this.height/2 from here to remove rounded corners
		.attr('fill', this.barColor)
		.attr('stroke', '');		
	}
	
	ProgressBar.prototype.setValue = function(value)
	{
		this.value = value;
		var valueToPercent = percentForValueInRange(this.minValue, this.maxValue, this.value);
		this.setPercent(valueToPercent);
	}