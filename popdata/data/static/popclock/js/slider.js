function Slider(containerID, minValue, maxValue, trackHeight, thumbHeight, rounded, popoverWidth, popoverHeight, delegate)
{
	this.delegate         = delegate;
	this.containerID      = containerID;
	this.jQueryEquivalent = $('#' + this.containerID);
	this.width            = this.jQueryEquivalent.width();
	this.popoverWidth     = (!popoverWidth) ? 0 : popoverWidth;
	this.popoverHeight    = popoverHeight;
	this.trackHeight      = trackHeight;
	this.rounded          = rounded;
	
	this.thumbHeight    = thumbHeight;
	this.thumbRadius    = this.thumbHeight/2;
	this.thumbMinX      = (!this.popoverWidth) ? this.thumbRadius : this.thumbRadius + this.popoverWidth/2;
	this.thumbMaxX      = (!this.popoverWidth) ? this.jQueryEquivalent.width() - this.thumbRadius : this.jQueryEquivalent.width() - this.thumbRadius - this.popoverWidth/2;
	this.thumbConstantY = this.thumbRadius;
	
	this.RaphHeight = (trackHeight > thumbHeight) ? trackHeight : thumbHeight;
	this.RaphHeight = (this.popoverHeight) ? this.RaphHeight + this.popoverHeight : 0;
	this.Raph       = Raphael(this.containerID, this.jQueryEquivalent.width() + this.popoverWidth/2, this.RaphHeight + 10);
	
	this.popoverLabel;
	this.popoverOutline;
	this.popoverOutlineGlow;
	
	this.thumbWithPopover;
	
	this.minValue = minValue;
	this.maxValue = maxValue;
	this.range    = (this.maxValue > this.minValue) ? this.maxValue - this.minValue : this.minValue - this.maxValue;
	this.percent;
	this.value;
}
	Slider.prototype = {
		render: function(trackAttributes, thumbAttributes)
		{
			var $Slider = this;
			var bevel = (this.rounded) ? this.trackHeight/2 - trackAttributes['stroke-width'] : 0;
			var trackX = (this.trackHeight > this.thumbHeight)
			? 0 + trackAttributes['stroke-width']
			: (this.thumbHeight - this.trackHeight)/2 + trackAttributes['stroke-width']
			
			this.track = this.Raph.rect(
				trackAttributes['stroke-width'] + this.popoverWidth/2, 
				trackX, this.jQueryEquivalent.width() - (trackAttributes['stroke-width'] * 2) - this.popoverWidth, 
				this.trackHeight - (trackAttributes['stroke-width'] * 2), bevel
			);
			this.track.attr(trackAttributes);
			this.track.click(function( handler ) {
				$Slider.sliderClickedAtX(handler.x);

			});
			
			this.Raph.setStart();
			this.thumb = this.Raph.circle(this.thumbRadius + this.popoverWidth/2, this.thumbRadius, this.thumbRadius);
			this.popoverLabel = this.buildPopover(12, 45, 15, 5, 7, { 'stroke-width': 2, fill: '90-#053b65-#0a507b:80' });		
			this.thumbWithPopover = this.Raph.setFinish();
			
			this.thumb.attr(thumbAttributes);
			this.thumbWithPopover.drag($Slider.thumbMove, $Slider.thumbBeginMove, $Slider.thumbEndMove, $Slider);
		},
		
		/* need to figure out how to create this as an actual Raphael object */
		buildPopover: function (fontSize, width, topBottomPadding, cornerRadius, triangleHeight, attributes)
		{
			fontSize         = fontSize;
			width            = width; // the width will change depending on the width of the string
			topBottomPadding = topBottomPadding;
			cornerRadius     = cornerRadius;
			triangleHeight   = triangleHeight;
			attributes       = attributes;
			height           = (!triangleHeight) ? fontSize + topBottomPadding * 2 : fontSize + topBottomPadding * 2 - triangleHeight;
			
			// we start in the middle - at the tip of the triangle and work clockwise back around
			var point1X      = width/2;
			var point1Y      = (!attributes['stroke-width']) ? 0 : attributes['stroke-width'];
			
			var point2X      = (!triangleHeight) ? point1X : point1X + triangleHeight/2;
			var point2Y      = (!triangleHeight) ? point1Y : point1Y + triangleHeight;
			
			var point3X      = (!cornerRadius) ? width : width - cornerRadius;
			if (attributes['stroke-width']) {
				point3X = point3X - attributes['stroke-width'];
			}
			var point3Y      = point2Y;
			
			var point4XQuad  = (!cornerRadius) ? point3X : point3X + cornerRadius;
			var point4YQuad  = point3Y;
			
			var point5X      = point4XQuad;
			var point5Y      = (!cornerRadius) ? point4YQuad : point4YQuad + cornerRadius;
		
			var point6X      = point5X;
			var point6Y      = (!cornerRadius) ? height : height - cornerRadius;
			
			var point7XQuad  = point4XQuad;
			var point7YQuad  = (!cornerRadius) ? height : point6Y + cornerRadius;
		
			var point8X      = point3X;
			var point8Y      = point7YQuad;
			
			var point9X      = (!cornerRadius) ? 0 : cornerRadius;
			if (attributes['stroke-width']) {
				point9X = point9X + attributes['stroke-width'];
			}
			var point9Y      = point8Y;
			
			var point10XQuad = (!attributes['stroke-width']) ? 0 : attributes['stroke-width'];
			var point10YQuad = point9Y;
		
			var point11X     = point10XQuad;
			var point11Y     = point6Y;
			
			var point12X     = point11X;
			var point12Y     = point5Y;
			
			var point13XQuad = point12X;
			var point13YQuad = point4YQuad;
			
			var point14X     = (!cornerRadius) ? point13XQuad : point13XQuad + cornerRadius;
			var point14Y     = point3Y;
		
			var point15X     = (!triangleHeight) ? point1X : point1X - triangleHeight/2;
			var point15Y     = point2Y;
			
			
			this.popoverOutline = this.Raph.path(
				"M" + point1X + "," + point1Y +
				"L" + point2X + "," + point2Y +
				"L" + point3X + "," + point3Y +
				"Q" + point4XQuad + "," + point4YQuad + " " + point5X + "," + point5Y +
				"L" + point6X + "," + point6Y +
				"Q" + point7XQuad + "," + point7YQuad + " " + point8X + "," + point8Y +
				"L" + point9X + "," + point9Y +
				"Q" + point10XQuad + "," + point10YQuad + " " + point11X + "," + point11Y +
				"L" + point12X + "," + point12Y +
				"Q" + point13XQuad + "," + point13YQuad + " " + point14X + "," + point14Y +
				"L" + point14X + "," + point14Y + 
				"L" + point15X + "," + point15Y +
				"Z"
			);
			this.popoverOutline.attr(attributes);
			this.popoverOutline.transform("t" + this.thumbRadius + "," + (this.thumbRadius + 3));

			if ( Raphael.svg ) {
				// glow does not appear to move with the popover set
				// so, we'll use a data object to hold it, and remove it before adding one
				if (this.popoverOutline.data('glow')) {
					this.popoverOutline.data('glow').remove();
				}
				var g = this.popoverOutline.glow({width: 3, fill: true, opacity: 0.25, offsetx: 0, offsety: 2, color: "#000"});
				this.popoverOutline.data('glow', g);
			}
			var labelY = (!triangleHeight) ? topBottomPadding : triangleHeight + topBottomPadding;
			
			var popoverLabel = this.Raph.text(width/2, labelY, 'XXXX');
			popoverLabel.attr({ 'font-size': fontSize, 'font-weight': 'bold', fill: "#fff" });
			popoverLabel.transform("t" + this.thumbRadius + "," + (this.thumbRadius + 3));
			
			return popoverLabel;
			
		},
		
		thumbValueFromX: function(xPos)
		{
			var valueRange = this.maxValue - this.minValue;
			var desiredValueAdjustment = valueRange * percentForValueInRange(this.thumbMinX, this.thumbMaxX, xPos);
			var desiredValue = Math.floor(this.minValue + desiredValueAdjustment);
			
			return desiredValue;
		},
		
		thumbXFromValue: function(value)
		{
			var thumbXRange             = this.thumbMaxX - this.thumbMinX;
			var desiredThumbXAdjustment = thumbXRange * percentForValueInRange(this.minValue, this.maxValue, value);
			var desiredXPos             = this.thumbMinX + desiredThumbXAdjustment;
			
			if (desiredXPos < this.thumbMinX) {
				desiredXPos = this.thumbMinX;
				
			} else if (desiredXPos > this.thumbMaxX) {
				
				desiredXPos = this.thumbMaxX;
			}
			return desiredXPos;
		},

		// Will move thumb to corresponding X position based on passed in value
		// similar to thumbMove
		moveThumbToValue: function(value)
		{
			this.updateLayoutWithThumbX(this.thumbXFromValue(value));
			this.didMoveToValue(value);
		},
		
		updateLayoutWithThumbX: function(thumbX)
		{
			this.thumb.attr({ cx: thumbX, cy: this.thumbRadius });
			
			this.popoverLabel.transform("t" + (thumbX - this.popoverWidth/2) + "," + (this.thumbRadius + 3));
		
			this.popoverOutline.transform("");
			this.popoverOutline.transform("t" + (thumbX - this.popoverWidth/2) + "," + (this.thumbRadius + 3));

			if ( Raphael.svg ) {
				if (this.popoverOutline.data('glow')) {
					this.popoverOutline.data('glow').remove();
				}
				var g = this.popoverOutline.glow({width: 3, fill: true, opacity: 0.25, offsetx: 0, offsety: 2, color: "#000"});
				this.popoverOutline.data('glow', g);				
			}
		},
		
		getAdjustedXPos: function(x)
		{
			return x - this.jQueryEquivalent.offset().left;
		},
		
		/* Raphael Callbacks */
		thumbMove: function(dx, dy, x, y)
		{
			var $Slider = this;
			var newX = this.getAdjustedXPos(x);
			if (newX < this.thumbMinX) {
				newX = this.thumbMinX;
			} else if (newX > this.thumbMaxX) {
				newX = this.thumbMaxX;
			}
			this.updateLayoutWithThumbX(newX);
			this.thumbValueFromX(newX);
			//this.setPercentForXPos(newX);
			this.didMoveToValue(this.thumbValueFromX(newX));
		},
	
		thumbBeginMove: function(startX, startY, mouseEvent)
		{
			if (typeof this.delegate !== 'undefined' && typeof this.delegate.thumbDidBeginMove == 'function') {
				this.delegate.thumbDidBeginMove();
			}
		},
		
		thumbEndMove: function()
		{
			if (typeof this.delegate !== 'undefined' && typeof this.delegate.thumbDidEndMove == 'function') {
				this.delegate.thumbDidEndMove();
			}
		},
		
		
		sliderClickedAtX: function(xPos)
		{
			var newX = this.getAdjustedXPos(xPos);
			if (newX < this.thumbMinX) {
				newX = this.thumbMinX;
			} else if (newX > this.thumbMaxX) {
				newX = this.thumbMaxX;
			}
			this.updateLayoutWithThumbX(newX);
			this.thumbValueFromX(newX);
			this.didMoveToValue(this.thumbValueFromX(newX));
			
			if (typeof this.delegate !== 'undefined' && typeof this.delegate.sliderClicked == 'function') {
				this.delegate.sliderClicked();
			}

		},

		/* Delegate methods */
		didMoveToValue: function(value)
		{
			this.value = value;
			this.popoverLabel.attr('text', this.value);
			if (typeof this.delegate !== 'undefined' && typeof this.delegate.thumbDidMoveToValue == 'function') {
				this.delegate.thumbDidMoveToValue(this.value);
			}
		}	
	}