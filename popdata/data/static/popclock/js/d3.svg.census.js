d3.svg.census = d3.svg.census || {};

/**
 * US CENSUS POP CLOCK
 * FORMATTING
 */
d3.svg.census.short_pop = d3.format('.3s');

var date_parse = d3.time.format("%Y").parse;


function translation(x,y) {
  return 'translate(' + x + ',' + y + ')';
}

function addThousandsSeparator(value) {
    return value.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
}

// polyfill for Number.isInteger - required for Safari and IE
Number.isInteger = Number.isInteger || function(value) {
    return typeof value === "number" && 
           isFinite(value) && 
           Math.floor(value) === value;
};


function formatBubbleValue(val)
{
  var resultVal=val;
  var suffix="";

  if (val>1000000000)
  {
    resultVal=val/1000000000;
    suffix="B";
  }
  else if (val>1000000)
  {
    resultVal=val/1000000;
    suffix="M";
  }
  else if (val>1000)
  {
    resultVal=val/1000;
    suffix="K";
  }


  //resultVal=parseFloat(Math.round(resultVal*100)/100).toFixed(1)+suffix;

  /*  Add one more digit of precision. This seems to help improve the
   * consistency of the imp/exp hover numbers between browsers.
   */
 // resultVal=parseFloat(Math.round(resultVal*1000)/1000).toFixed(1)+suffix;
  resultVal=parseFloat(resultVal).toFixed(1)+suffix;
  
  
  return resultVal;
} // formatBubbleValue()

/* Used to display  bubbles in the Current and Projected Population graph
 * on the World page. 
 */  
function formatBubbleValue2(val)
{
  var resultVal=val;
  var suffix="";

  if (val>1000000000)
  {
    resultVal=val/1000000000;
    suffix="B";
  }
  else if (val>1000000)
  {
    resultVal=val/1000000;
    suffix="M";
  }
  else if (val>1000)
  {
    resultVal=val/1000;
    suffix="K";
  }


  //resultVal=parseFloat(Math.round(resultVal*1000)/1000).toFixed(1)+suffix;
 
  //resultVal=parseFloat(resultVal).toFixed(1)+suffix;
  
  /* Updated 11/18/2020 to make the 'Basic Facts' consistent with the 
   *   'Current and Projected Population' bubble numbers graph.
   */     

   var prefix = d3.formatPrefix(resultVal);
   var result= d3.round(prefix.scale(resultVal),1);
   var roundedNumber = result.toFixed(1) + suffix;

   return roundedNumber;

} // formatBubbleValue2()


function formatBarNumber(val)
{
  var resultVal=val;

  resultVal="$"+formatBubbleValue2(val);

  return resultVal;
} // formatBarNumber()


var imExpList =[];



var apiPath = window.location.pathname.substr(0, window.location.pathname.indexOf('world'));
apiTrade = apiPath + 'apiData_trade.php',
$.ajax({
  type: 'GET',
  //url:  '/popclock/apiData_trade.php',
  url:  apiTrade,
  data: {
    get : "COUNTRY,EXPALL2021,IMPALL2021"
  },
  dataType: 'json',
  cache: true,
  success: function(impExp, textStatus, jqXHR){
console.log('returned from apiData_trade.php'); 
    var arrayLength = impExp.length;
    for (var i = 0; i < arrayLength; i++) {

if (impExp[i][0] == 'Madagascar') {
console.log('country= '+impExp[i][0]); 
console.log('import = '+impExp[i][2]); 
console.log('export = '+impExp[i][1]); 
}
      imExpList.push({
        'country': impExp[i][0],
        'exports': impExp[i][1],
        'imports': impExp[i][2] 
      });
    }
  }
});


function getUSExportImport(country, exim){
  var ei=new Object();
  ei.e="";
  ei.i="";
  var result="";
  console.log('country='+country); 
  if (country.indexOf("Gambia")>-1) {
    country="Gambia";
  }
  else if (country.indexOf("Cura")>-1) {
    country="Curacao";
  }
  else if (country.indexOf("ivoire")>-1) {
    country="Côte d'Ivoire";
  }
  else if (country.indexOf("Gaza")>-1) {
    country="Gaza Strip Administered by Israel";
  }
  else if (country.indexOf("Micronesia")>-1) {
    country="Micronesia";
  }
  else if (country.indexOf("Kitts")>-1) {
    country="St Kitts and Nevis";
  }
  else if (country.indexOf("Miquelon")>-1) {
    country="St Pierre and Miquelon";
  }
  else if (country.indexOf("Lucia")>-1) {
    country="St Lucia";
  }
  else if (country.indexOf("Vincent")>-1) {
    country="St Vincent and the Grenadines";
  }
  else if (country.indexOf("British")>-1) {
    country="British Virgin Islands";
  }
  else if (country.indexOf("Bank")>-1) {
    country="West Bank Administered by Israel";
  }
  else if (country.indexOf("Helena")>-1) {
    country="St Helena";
  }
  else if (country.indexOf("Czechia")>-1) {
    country="Czech Republic";
  }
  else if (country.indexOf("Bahamas")>-1) {
    country="Bahamas";
  }
  var result = $.grep(imExpList, function(e){ 
     return e.country == country; 
  });
  if(result.length === 1){
    // found one result
    ei.e = formatBubbleValue(result[0].exports);
    ei.i = formatBubbleValue(result[0].imports);
  }

console.log('ei.e = '+ei.e);
console.log('ei.i = '+ei.i);
  if (exim=="export")
    result=ei.e;
  else
    result=ei.i;

  if ( (result!="")&&(result!="N/A") )
    result="$"+result;

  return result;
} // getUSExportImport()



/**
 * US CENSUS POP CLOCK
 * POPULATION PYRAMID
 */
d3.svg.census.pyramid = function () {

	var  margin = {top: 10, right: 40, bottom: 40, left: 40, middle: 28}
		,width = 500
		,height = 300
		,padding = 0.5
		,tick_format = null
		,ticks = 4
		//,x_accessor = function(d){return d[0];}
		//,y_accessor = function(d){return d[1];}
		,total_population = null
		,percentage = function(d) { return d / total_population; }
		,x_domain = null
		,y_domain = null
		,label_format = d3.format('.1%')
	;

	function pyramid (selection){
		selection.each( function( datum, index ) {

			var svg = d3.select(this).selectAll("svg").data([datum]);

			var g = svg.enter().append("svg")
				.attr({width: width, height: height})
				.append("g")
				.attr("transform", "translate(" + margin.left + "," + margin.top + ")" )
			  	// middle block
			  	.append('g')
			    	.attr('transform', "translate(" + margin.left + "," + margin.top + ")")
				;

			total_population = d3.sum(datum, function(d) { return d.male + d.female; });

			// the width of each side of the chart
			var region_width = (width - margin.left - margin.right)/2 - margin.middle;

			// these are the x-coordinates of the y-axes
			var pointA = region_width + margin.left,
			    pointB = width - region_width - margin.left;

			var max_value = Math.max(
			  d3.max(datum, function(d) { return percentage(d.male); }),
			  d3.max(datum, function(d) { return percentage(d.female); })
			);

			var x_scale = d3.scale.linear()
			  .domain([0, max_value])
			  .range([0, region_width])
			  .nice();

			var x_scale_left = d3.scale.linear()
			  .domain([0, max_value])
			  .range([region_width, 0]);

			var x_scale_right = d3.scale.linear()
			  .domain([0, max_value])
			  .range([0, region_width]);

			var y_scale = d3.scale.ordinal()
			  .domain(datum.map(function(d) { return d.group; }))
			  .rangeRoundBands([height - margin.top - margin.bottom , 0 ], padding);

			var y_axis_left = d3.svg.axis()
			  .scale(y_scale)
			  .orient('right')
			  .tickSize(4,0)
			  .tickPadding(margin.middle-4);

			var y_axis_right = d3.svg.axis()
			  .scale(y_scale)
			  .orient('left')
			  .tickSize(4,0)
			  .tickFormat('');

			var x_axis_right = d3.svg.axis()
			  .scale(x_scale)
			  .orient('bottom')
			  .tickFormat(d3.format('%'))
			  .outerTickSize(0)
			  .ticks(ticks)
			  ;

			var x_axis_left = d3.svg.axis()
			  .scale(x_scale.copy().range([pointA-margin.left, 0]))
			  .orient('bottom')
			  .tickFormat(d3.format('%'))
			  .outerTickSize(0)
			  .ticks(ticks)
			  ;

			var leftBarGroup = svg.append('g').attr('class', 'left-bars')
			  .attr('transform', translation(pointA, 0) + 'scale(-1,1)');
			var rightBarGroup = svg.append('g').attr('class', 'right-bars')
			  .attr('transform', translation(pointB, 0));

			svg.append('g')
			  .attr('class', 'axis y left')
			  .attr('transform', translation(pointA, 0))
			  .call(y_axis_left)
			  .selectAll('text')
			  .style('text-anchor', 'middle');

			svg.append('g')
			  .attr('class', 'axis y right')
			  .attr('transform', translation(pointB, 0))
			  .call(y_axis_right);

			svg.append('g')
			  .attr('class', 'axis x left')
			  .attr('transform', translation(margin.left, height - margin.bottom))
			  .call(x_axis_left);

			svg.append('g')
			  .attr('class', 'axis x right')
			  .attr('transform', translation(pointB, height - margin.bottom))
			  .call(x_axis_right);
			
			rightBarGroup.selectAll('.bar.right')
			  .data(datum)
			  .enter().append('text')
			  .text(function(d){return label_format(percentage(d.female));})
			  .attr({
			  		 x: function(d){return x_scale(percentage(d.female)) + 2;}
			  		,y: function(d){ return y_scale(d.group) + y_scale.rangeBand() - 1;}
			  });

			leftBarGroup.selectAll('.bar.left')
			  .data(datum)
			  .enter().append('text')
			  .text(function(d){return label_format(percentage(d.male));})
			  .attr({
			  		 x: function(d){return -x_scale(percentage(d.male)) - 28;}
			  		,y: function(d){return y_scale(d.group) + y_scale.rangeBand() - 1;}
			  })
			  .attr('transform', 'scale(-1,1)')
			  ;

			leftBarGroup.selectAll('.bar.left')
			  .data(datum)
			  .enter().append('rect')
			    .attr('class', 'bar left')
			    .attr('x', 0)
			    .attr('y', function(d) { return y_scale(d.group); })
			    .attr('width', function(d) { return x_scale(percentage(d.male)); })
			    .attr('height', y_scale.rangeBand());
		

			rightBarGroup.selectAll('.bar.right')
			  .data(datum)
			  .enter().append('rect')
			    .attr('class', 'bar right')
			    .attr('x', 0)
			    .attr('y', function(d) { return y_scale(d.group); })
			    .attr('width', function(d) { return x_scale(percentage(d.female)); })
			    .attr('height', y_scale.rangeBand());


			svg.selectAll('.x.axis.right text')
			.style('text-anchor', 'end');

			svg.selectAll('.x.axis.left text')
			.style('text-anchor', 'start');

			// x axis label is on the y axis to simplify placement
			svg.select('.y.axis.left').append('text')
				.text('% of Population')
				.attr('transform', translation(-8, height - margin.bottom + 16))
				;

			svg.select('.x.axis.right text').style({'display':'none'});
			svg.select('.x.axis.left text').style({'display':'none'});

			var icons = svg.append('g').attr('class','icons');

			var male_icon = icons.append('g').attr('class', 'male icon');

			male_icon.append('path').attr('d', 'M18.255,8.511L18.169,8.508C18.148,8.508,18.135,8.511999999999999,18.126,8.511999999999999L11.354000000000001,8.508V8.511C11.345,8.511,11.341000000000001,8.508,11.331000000000001,8.508L11.245000000000001,8.511C8.974,8.511,7.148000000000001,10.388,7.172000000000001,12.739999999999998L7.1690000000000005,12.907999999999998V22.543999999999997C7.1690000000000005,24.487,9.972000000000001,24.487,9.972000000000001,22.543999999999997V13.570999999999996H10.723L10.72,13.590999999999996V39.019C10.72,41.54,14.472000000000001,41.617,14.472000000000001,39.019V24.041H15.095V39.019C15.095,41.54,18.85,41.617,18.85,39.019V13.589V13.57H19.531000000000002V22.543C19.531000000000002,24.486,22.334000000000003,24.486,22.334000000000003,22.543V12.907V12.738C22.353,10.387,20.527,8.511,18.255,8.511Z')
			.attr({'transform': 'matrix(0.75,0,0,0.75,' + ( 10 ) + ', 0)'});

			male_icon.append('circle').attr({
				'cx' : '14.631'
				,'cy' : '4.203'
				,'r' : '3.265'
				,'transform': 'matrix(0.75,0,0,0.75,' + ( 10 ) + ', 0)'	
			}) 

			var female_icon = icons.append('g').attr('class', 'female icon');

			female_icon.append('path').attr('d', 'M14.294,27.337V39.199C14.294,41.516,10.823,41.516,10.823,39.199V27.337H8.091L11.143999999999998,13.745H10.37L8.312,21.444C7.624,23.57,4.700999999999999,22.7,5.369999999999999,20.419999999999998L7.752999999999999,11.877999999999998C8.096,10.669999999999998,9.623999999999999,8.526999999999997,12.270999999999999,8.526999999999997H14.709L14.709,8.526999999999997H17.333C19.953999999999997,8.526999999999997,21.488,10.686999999999998,21.884999999999998,11.877999999999997L23.985999999999997,20.343999999999994C24.629999999999995,22.619999999999994,21.729999999999997,23.561999999999994,21.042999999999996,21.343999999999994L19.262999999999995,13.745999999999995H18.426999999999996L21.22,27.34H18.779999999999998V39.213C18.779999999999998,41.517,15.322999999999997,41.501,15.322999999999997,39.213V27.34L14.294,27.337L14.294,27.337Z')
			.attr({'transform': 'matrix(0.75,0,0,0.75,' + ( width - margin.right + 10 ) + ',0)'});

			female_icon.append('circle').attr({
				'cx' : '14.771'
				,'cy' : '4.202'
				,'r' : '3.265'
				,'transform': 'matrix(0.75,0,0,0.75,' + ( width - margin.right + 10 ) + ',0)'	
			}) 

			svg.on('mousemove', mousemove);

			svg.on('mouseout', clear_active);

			function clear_active() {
			 	leftBarGroup.selectAll('.bar.left').classed('active', false);
			 	rightBarGroup.selectAll('.bar.right').classed('active', false);
			 	leftBarGroup.selectAll('text').classed('active', false);
			 	rightBarGroup.selectAll('text').classed('active', false);
			 	svg.select('.axis.y.left').selectAll('text').classed('active',false);
			}


			function mousemove() {
			 	var y = d3.mouse(this)[1];
			 	clear_active();
			 	if (y < height - margin.bottom - 20 && y > margin.top){
			 		var bisect = d3.bisector(d3.descending);
					var i = bisect.right(y_scale.range(), y);
					d3.select(leftBarGroup.selectAll('.bar.left')[0][i]).classed("active", function (d, e) {
					    return !d3.select(this).classed("active");
  					});
					d3.select(leftBarGroup.selectAll('text')[0][i]).classed("active", function (d, e) {
					    return !d3.select(this).classed("active");
					});
					d3.select(rightBarGroup.selectAll('.bar.right')[0][i]).classed("active", function (d, e) {
					    return !d3.select(this).classed("active");
  					});				
					d3.select(rightBarGroup.selectAll('text')[0][i]).classed("active", function (d, e) {
					    return !d3.select(this).classed("active");
  					});	
  					d3.select(svg.select('.axis.y.left').selectAll('text')[0][i]).classed("active", function (d, e) {
					    return !d3.select(this).classed("active");
  					});	
				}
			}
			
		});
	}

	return pyramid;
}

d3.svg.census.slider = function module() {
  "use strict";

  // Public variables width default settings
  var min = 0,
      max = 100,
      step = 1, 
      animate = true,
      orientation = "horizontal",
      axis = false,
      margin = 50,
      value,
      scale; 

  // Private variables
  var axisScale,
      dispatch = d3.dispatch("slide"),
      formatPercent = d3.format(".2%"),
      tickFormat = d3.format(".0"),
      sliderLength;

  function slider(selection) {
    selection.each(function() {

      // Create scale if not defined by user
      if (!scale) {
        scale = d3.scale.linear().domain([min, max]);
      }  

      // Start value
      value = value || scale.domain()[0]; 

      // DIV container
      var div = d3.select(this).classed("d3-slider d3-slider-" + orientation, true);

      var drag = d3.behavior.drag();

      // Slider handle
      var handle = div.append("a")
          .classed("d3-slider-handle", true)
          .attr("xlink:href", "#")
          .on("click", stopPropagation)
          .call(drag);

      // Horizontal slider
      if (orientation === "horizontal") {

        div.on("click", onClickHorizontal);
        drag.on("drag", onDragHorizontal);
        handle.style("left", formatPercent(scale(value)));
        sliderLength = parseInt(div.style("width"), 10);

      } else { // Vertical

        div.on("click", onClickVertical);
        drag.on("drag", onDragVertical);
        handle.style("bottom", formatPercent(scale(value)));
        sliderLength = parseInt(div.style("height"), 10);

      }
      
      if (axis) {
        createAxis(div);
      }


      function createAxis(dom) {

        // Create axis if not defined by user
        if (typeof axis === "boolean") {

          axis = d3.svg.axis()
              .ticks(Math.round(sliderLength / 100))
              .tickFormat(tickFormat)
              .orient((orientation === "horizontal") ? "bottom" :  "right");

        }      

        // Copy slider scale to move from percentages to pixels
        axisScale = scale.copy().range([0, sliderLength]);
          axis.scale(axisScale);

          // Create SVG axis container
        var svg = dom.append("svg")
            .classed("d3-slider-axis d3-slider-axis-" + axis.orient(), true)
            .on("click", stopPropagation);

        var g = svg.append("g");

        // Horizontal axis
        if (orientation === "horizontal") {

          svg.style("left", -margin);

          svg.attr({ 
            width: sliderLength + margin * 2, 
            height: margin
          });  

          if (axis.orient() === "top") {
            svg.style("top", -margin);  
            g.attr("transform", "translate(" + margin + "," + margin + ")")
          } else { // bottom
            g.attr("transform", "translate(" + margin + ",0)")
          }

        } else { // Vertical

          svg.style("top", -margin);

          svg.attr({ 
            width: margin, 
            height: sliderLength + margin * 2
          });      

          if (axis.orient() === "left") {
            svg.style("left", -margin);
            g.attr("transform", "translate(" + margin + "," + margin + ")")  
          } else { // right          
            g.attr("transform", "translate(" + 0 + "," + margin + ")")      
          }

        }

        g.call(axis);  

      }


      // Move slider handle on click/drag
      function moveHandle(pos) {

        var newValue = stepValue(scale.invert(pos / sliderLength));

        if (value !== newValue) {
          var oldPos = formatPercent(scale(stepValue(value))),
              newPos = formatPercent(scale(stepValue(newValue))),
              position = (orientation === "horizontal") ? "left" : "bottom";

          dispatch.slide(d3.event.sourceEvent || d3.event, value = newValue);

          if (animate) {
            handle.transition()
                .styleTween(position, function() { return d3.interpolate(oldPos, newPos); })
                .duration((typeof animate === "number") ? animate : 250);
          } else {
            handle.style(position, newPos);          
          }
        }

      }


      // Calculate nearest step value
      function stepValue(val) {

        var valModStep = (val - scale.domain()[0]) % step,
            alignValue = val - valModStep;

        if (Math.abs(valModStep) * 2 >= step) {
          alignValue += (valModStep > 0) ? step : -step;
        }

        return alignValue;

      }


      function onClickHorizontal() {
        moveHandle(d3.event.offsetX || d3.event.layerX);
      }

      function onClickVertical() {
        moveHandle(sliderLength - d3.event.offsetY || d3.event.layerY);
      }

      function onDragHorizontal() {
        moveHandle(Math.max(0, Math.min(sliderLength, d3.event.x)));
      }

      function onDragVertical() {
        moveHandle(sliderLength - Math.max(0, Math.min(sliderLength, d3.event.y)));
      }      

      function stopPropagation() {
        d3.event.stopPropagation();
      }

    });

  } 

  // Getter/setter functions
  slider.min = function(_) {
    if (!arguments.length) return min;
    min = _;
    return slider;
  } 

  slider.max = function(_) {
    if (!arguments.length) return max; 
    max = _;
    return slider;
  }     

  slider.step = function(_) {
    if (!arguments.length) return step;
    step = _;
    return slider;
  }   

  slider.animate = function(_) {
    if (!arguments.length) return animate;
    animate = _;
    return slider;
  } 

  slider.orientation = function(_) {
    if (!arguments.length) return orientation;
    orientation = _;
    return slider;
  }     

  slider.axis = function(_) {
    if (!arguments.length) return axis;
    axis = _;
    return slider;
  }     

  slider.margin = function(_) {
    if (!arguments.length) return margin;
    margin = _;
    return slider;
  }  

  slider.value = function(_) {
    if (!arguments.length) return value;
    value = _;
    return slider;
  }  

  slider.scale = function(_) {
    if (!arguments.length) return scale;
    scale = _;
    return slider;
  }  

  d3.rebind(slider, dispatch, "on");

  return slider;

}

/**
 * US CENSUS POP CLOCK 
 * BAR CHART
 */
d3.svg.census.bar = function () {

	var  margin = {top: 10, right: 10, bottom: 20, left: 20}
		,width = 500
		,height = 500
		,padding = 0.55
		,tick_format = null
		,x_accessor = function(d){return d[0];}
		,y_accessor = function(d){return d[1];}
		,x_domain = null
		,y_domain = null
	;

	function bar (selection) {
		selection.each( function(datum, index){

			var data = datum.map(function(d){
				return [x_accessor(d), y_accessor(d)];
			});
// DEBUG:
//alert("data: "+data);
//alert("d0: "+d[0]+", d1: "+d[1]);

			var x_scale = d3.scale.linear()
				.domain( x_domain ? x_domain.call(this) : [0, d3.max(data, function(d){return d[0];})])
				.range([0, width - margin.left - margin.right]);

			var y_scale = d3.scale.ordinal()
				.domain( y_domain ? y_domain.call(this) : data.map(function(d){ return d[1];}))
				.rangeBands([height - margin.top - margin.bottom, 0], padding);	

			var x_axis = d3.svg.axis()
				.tickFormat(tick_format ? tick_format : null);
			
			var y_axis = d3.svg.axis()
				.orient('left')
				;

			var svg = d3.select(this).selectAll("svg").data([datum]);

			var g = svg.enter().append("svg")
					.attr({width: width, height: height})
					.append("g")
					.attr("transform", "translate(" + margin.left + "," + margin.top + ")" );

			g.append("g").attr("class", "bars");
			g.append("g").attr("class", "x axis");
			g.append("g").attr("class", "y axis");

			g = svg.select("g");

			var bar = g.select(".bars").selectAll(".bar")
				.data(data, function(d){return d[0];});

			bar.exit()
				.attr({y: height - margin.top - margin.bottom, height: 0})
				.remove();

			bar.enter().append("rect")
				.attr({
					"class" : "bar"
					,x: 0
					,y: function(d) { return y_scale(d[1]);}
					,width: 0
					,height: y_scale.rangeBand()
				})
				.attr('width', function(d){ return x_scale(d[0]);});
			
			bar.enter().append('text')	
		        .attr('x', function(d) { return x_scale(d[0]) + 0})
		        .attr('y', function(d) { return y_scale(d[1]) + y_scale.rangeBand()/2 + 4})
//		        .text(function(d) { return tick_format(d[0]);});
.text(function(d) { return formatBarNumber(d[0]);});


			g.select(".x.axis")
				.attr({
					"class" : "x axis"
					,"transform" : "translate(0, " + (height - margin.top - margin.bottom) + ")"
				})
				//uncomment the below to turn on the x-axis tick marks
				//.call(x_axis.scale(x_scale));

			g.select(".y.axis")
				.attr("class", "y axis")
				.call(y_axis.scale(y_scale));

			g.selectAll(".tick text")
				.attr("transform", "translate(" + 9 + ', -' + ( y_scale.rangeBand()/2 + 6 ) + ")")
				.style('text-anchor', 'start')
				;

		});
	}

	bar.margin = function(d) {
		if (!arguments.length) return margin;
		margin = d;
		return bar;
 	}

 	bar.width = function(d) {
 		if (!arguments.length) return width;
 		width = d;
 		return bar;
 	}

 	bar.height = function(d) {
 		if (!arguments.length) return height;
 		height = d;
 		return bar;
 	}

  	bar.padding = function(d) {
 		if (!arguments.length) return padding;
 		padding = d;
 		return bar;
 	}

 	bar.tickFormat = function(d) {
 		if (!arguments.length) return tick_format;
 		tick_format = d;
 		return bar;
 	}

 	bar.x = function(d) {
 		if (!arguments.length) return x_accessor;
 		x_accessor = d;
 		return bar;
 	}

 	bar.y = function(d) {
 		if (!arguments.length) return y_accessor;
 		y_accessor = d;
 		return bar; 		
 	}

 	bar.xDomain = function(d) {
 		if (!arguments.length) return x_domain;
 		x_domain = d3.functor(d);
 		return bar;
 	}

 	bar.yDomain = function(d) {
 		if (!arguments.length) return y_domain;
 		y_domain = d3.functor(d);
 		return bar;
 	}

 	return bar;

}


d3.svg.census.line = function () {

	var  margin = {top: 10, right: 10, bottom: 20, left: 20}
		,width = 500
		,height = 500
		,x_accessor = function(d){return d[0];}
		,y_accessor = function(d){return d[1];}
		,x_domain = null
		,y_domain = null
		,interpolate = 'linear'
	;		
	

	function line (selection) {
		selection.each(function( datum, index ){
			
			var data = datum.map(function(d){
				return [x_accessor(d), y_accessor(d)];
			});

			var x_scale = d3.time.scale()
				.domain( x_domain ? x_domain.call(this) : d3.extent(data, function(d){ return d[0]; }))
				.range([0, width - margin.left - margin.right]);

			var y_scale = d3.scale.linear()
				.domain( y_domain ? y_domain.call(this) : [0,d3.max(data, function(d){ return d[1]; }) * 1.3])
				.range([height - margin.top - margin.bottom, 0]);

			var valueline = d3.svg.line()
			    .x(function(d) { return x_scale(d[0]); })
			    .y(function(d) { return y_scale(d[1]); })
			    .interpolate(interpolate);

			var x_axis = d3.svg.axis().outerTickSize(0);
			
			var y_axis = d3.svg.axis()
				.orient('left')
				.tickFormat(tick_format ? tick_format : null).outerTickSize(0);
				;

			var svg = d3.select(this).selectAll("svg").data([datum]);

			var g = svg.enter().append("svg")
					.attr({width: width, height: height})
					.append("g")
					.attr("transform", "translate(" + margin.left + "," + margin.top + ")" );

			g.append("g").attr("class", "lines");
			g.append("g").attr("class", "x axis");
			g.append("g").attr("class", "y axis");

			g = svg.select("g");

			var line = g.select(".lines").append('g'); //selectAll(".line")
			//	.data(data, function(d){return d[0];});

			//line.exit()
			//	.attr({y: height - margin.top - margin.bottom, height: 0})
			//	.remove();

		    line.append("path")
		        .attr("class", "line")
		        .attr("d", valueline(data));
				
			g.select(".x.axis")
				.attr({
					"class" : "x axis"
					,"transform" : "translate(0, " + (height - margin.top - margin.bottom) + ")"
				})
				.call(x_axis.scale(x_scale));

			g.select(".y.axis")
				.attr("class", "y axis")
				.call(y_axis.scale(y_scale));

			// hide zero	
			g.select(".y.axis text").style('display', 'none');

		});
	}

	line.margin = function(d) {
		if (!arguments.length) return margin;
		margin = d;
		return line;
 	}

 	line.width = function(d) {
 		if (!arguments.length) return width;
 		width = d;
 		return line;
 	}

 	line.height = function(d) {
 		if (!arguments.length) return height;
 		height = d;
 		return line;
 	}

  	line.padding = function(d) {
 		if (!arguments.length) return padding;
 		padding = d;
 		return line;
 	}

 	line.tickFormat = function(d) {
 		if (!arguments.length) return tick_format;
 		tick_format = d;
 		return line;
 	}

 	line.x = function(d) {
 		if (!arguments.length) return x_accessor;
 		x_accessor = d;
 		return line;
 	}

 	line.y = function(d) {
 		if (!arguments.length) return y_accessor;
 		y_accessor = d;
 		return line; 		
 	}

 	line.xDomain = function(d) {
 		if (!arguments.length) return x_domain;
 		x_domain = d3.functor(d);
 		return line;
 	}

 	line.xDomain = function(d) {
 		if (!arguments.length) return y_domain;
 		y_domain = d3.functor(d);
 		return line;
 	}

	line.interpolate = function(d) {
		if (!arguments.length) return interpolate;
		interpolate = d;
		return line;
 	}

 	return line;
}

d3.svg.census.multi_line = function () {
	
	var  margin = {top: 10, right: 10, bottom: 20, left: 20}
		,width = 500
		,height = 500
		,x_accessor = function(d){return d[0];}
		,y_accessor = function(d){return d[1];}
		,x_domain = null
		,y_domain = null
		,interpolate = 'linear'
		,show_legend = true
		,ticks = 5
		,display_markers = false
	;		
	

	function multi_line (selection) {
		selection.each(function( datum, index ){
			
			//var data = datum.map(function(d){
			//	return [x_accessor(d), y_accessor(d)];
			//});

			var x_scale = d3.time.scale()
				.domain( x_domain ? x_domain.call(this) : d3.extent(datum[0].values, function(d){ return d.date; }))
				.range([0, width - margin.left - margin.right]);

			var color = d3.scale.ordinal()
				.domain(datum.map(function(d){return d.title;}))
				.range(datum.map(function(d){return d.title.toLowerCase();}));

			var y_scale = d3.scale.linear()
				.domain( y_domain ? y_domain.call(this) : [0/*d3.min(datum, function(c) { return d3.min(c.values, function(v) { return v['value']; }); })*/,
    													   d3.max(datum, function(c) { return d3.max(c.values, function(v) { return v['value']  }); }) * 1.3])
				.range([height - margin.top - margin.bottom, 0])
				;

			var valueline = d3.svg.line()
			    .x(function(d) { return x_scale(d.date); })
			    .y(function(d) { return y_scale(d.value); })
			    .interpolate(interpolate);

			var x_axis = d3.svg.axis().outerTickSize(0);
			
			var y_axis = d3.svg.axis()
				.orient('left')
				.outerTickSize(0)
				.tickFormat(tick_format ? tick_format : null)
				;

			var svg = d3.select(this).selectAll("svg").data([datum]);

			var g = svg.enter().append("svg")
					.attr({width: width, height: height})
					.append("g")
					.attr("transform", "translate(" + margin.left + "," + margin.top + ")" );

			g.append("g").attr("class", "lines");
			g.append("g").attr("class", "x axis");
			g.append("g").attr("class", "y axis");
			g.append("g").attr("class", "legend");

			g = svg.select("g");
			
			if ( typeof display_markers === 'boolean' && display_markers ) { 
				var markers = g.append('defs').selectAll('marker').data(datum).enter()
					.append('marker')
					.attr({
						'id': function(d){return "callout-" + d.title.toLowerCase();}
//	                        ,'viewBox': "5.0 -10.0 100.0 135.0"
//		                ,'refX':"52" 
//		                ,'refY':"120"
//		                ,'markerWidth':"32" 
//		                ,'markerHeight':"32"		                


                                ,'viewBox': "5.0 -10.0 100.0 135.0"
                                ,'refX':"52"
                                ,'refY':"100"
                                ,'markerWidth':"48"
                                ,'markerHeight':"48"


					});
				
				markers
					.append('path')
					//.attr('d', "M49.998,5C28.33,5,10.76,22.565,10.76,44.237c0,18.449,12.757,33.882,29.91,38.08L49.998,95l9.332-12.682   c17.16-4.198,29.91-19.633,29.91-38.083C89.24,22.565,71.67,5,49.998,5z")
					.attr('d', "M49.998,5C28.33,5,10.76,22.565,10.76,44.237c0,38.449,12.757,33.882,29.91,38.08L49.998,95l9.332-12.682   c17.16-4.198,29.91-19.633,29.91-38.083C89.24,22.565,71.67,5,49.998,5z")
					.style({
						'fill': 'none'
						//'fill': '#666'
						,'stroke': '#666'
						,'stroke-width': '4'
						,'stroke-dasharray': '60 0'
					});
				/*
				// this is buggy - Firefox renders this very small, sometimes disappears
				markers.append('text')
					.text(function(d){ return d3.svg.census.short_pop(d.values[d.values.length -1].value) })
					.attr('transform', "translate(20, 55)")
					.style({
						'font-size':'30'
						,'font-weight': 'bold'
					})
					;
				*/
			}

		  var lines = svg.selectAll(".line")
		      .data(datum)
		    .enter().append("g")
		      .attr("class", "line")
		      .attr("transform", "translate(" + margin.left + "," + margin.top + ")" );

		  lines.append("path")
		      .attr("class", "line")
		      .attr("d", function(d) { return valueline(d.values); })
		      .attr('class', function(d){return color(d.title);})
		      .attr('marker-end', function(d){return "url(#callout-" + d.title.toLowerCase() + ")";})

		      ;
		if ( typeof display_markers === 'boolean' && display_markers ) {

		   lines.selectAll('path')
		   		.each(function(d){
		   			
		   			coordinates = this.getPointAtLength(this.getTotalLength());

		   			var end_val = d.values[d.values.length -1].value;


		   			d3.select(this.parentNode).append('text')
		   				//.text(d3.svg.census.short_pop(end_val).replace('G', 'B').replace('k', 'K'))
//.text(d3.svg.census.short_pop(end_val).replace('G', 'B').replace('k', 'K'))
.text(formatBubbleValue2(end_val))
		   				.attr('transform', translation(coordinates.x, coordinates.y- 31))
		   				.attr('class', 'line label');
		   			
		   		})
		   		;
		   	}
				
			g.select(".x.axis")
				.attr({
					"class" : "x axis"
					,"transform" : "translate(0, " + (height - margin.top - margin.bottom) + ")"
				})
				.call(x_axis.scale(x_scale));

			g.select(".y.axis")
				.attr("class", "y axis")
				.call(y_axis.scale(y_scale));

			// hide zero	
			g.select(".y.axis text").style('display', 'none');

			if ( !!show_legend ) {
				g.select(".legend").selectAll('rect')
	      			.data(color.domain())
	      			.enter()
	      			.append('rect')
	      			.attr('x', function(d, i){ return 10 + (i*60);})
	      			.attr('width', 10)
	      			.attr('y', function(d,i){ return height - 20;} )
	      			.attr('height', 10)
		      		.attr('class', function(d){ return color(d);})

				g.select('.legend').selectAll('text')
	      			.data(color.domain())
	      			.enter()
	      			.append('text')
	      			.attr('class', 'legend')
	      			.attr('x', function(d, i){ return 25 + (i*60);})
	      			.attr('y', function(d,i){ return height - 12 ;} )
	      			.text(function(d,i){ return d; });
			}
		});
	}

	multi_line.margin = function(d) {
		if (!arguments.length) return margin;
		margin = d;
		return multi_line;
 	}

 	multi_line.width = function(d) {
 		if (!arguments.length) return width;
 		width = d;
 		return multi_line;
 	}

 	multi_line.height = function(d) {
 		if (!arguments.length) return height;
 		height = d;
 		return multi_line;
 	}

  	multi_line.padding = function(d) {
 		if (!arguments.length) return padding;
 		padding = d;
 		return multi_line;
 	}

 	multi_line.tickFormat = function(d) {
 		if (!arguments.length) return tick_format;
 		tick_format = d;
 		return multi_line;
 	}

 	multi_line.x = function(d) {
 		if (!arguments.length) return x_accessor;
 		x_accessor = d;
 		return multi_line;
 	}

 	multi_line.y = function(d) {
 		if (!arguments.length) return y_accessor;
 		y_accessor = d;
 		return multi_line; 		
 	}

 	multi_line.xDomain = function(d) {
 		if (!arguments.length) return x_domain;
 		x_domain = d3.functor(d);
 		return multi_line;
 	}

 	multi_line.yDomain = function(d) {
 		if (!arguments.length) return y_domain;
 		y_domain = d3.functor(d);
 		return multi_line;
 	}

	multi_line.interpolate = function(d) {
		if (!arguments.length) return interpolate;
		interpolate = d;
		return multi_line;
 	}

	multi_line.ticks = function(d) {
		if (!arguments.length) return ticks;
		ticks = d;
		return multi_line;
 	}

	multi_line.showLegend = function(d) {
		if (!arguments.length) return show_legend;
		show_legend = d;
		return multi_line;
 	}

	multi_line.displayMarkers= function(d) {
		if (!arguments.length)
			return show_markers;
		display_markers = d;
		return multi_line;
 	}

 	return multi_line;

}

/**
 * US CENSUS POP CLOCK 
 * WORLD MAP
 */

d3.svg.census.world_map = function () {

   var   width = 1008
   		//,height = 500
   		,height = 700
		,country_pop = {}
		,country_extent = new Array()
		,shapefile = "data/topo_11.json"
		,pop_data =  "data/fips_pop.json"
	;

    var projection = d3.geo.naturalEarth()
        .scale(180)
        .translate([width / 2, height / 2])
        .precision(1.0);

    var path = d3.geo.path()
        .projection(projection);

    var svg = d3.select("#world-map").append("svg")
        .attr("width", width)
        .attr("height", height);
   
    var nice_pop = d3.format(',');

    var pop_domain = [5e3, 5e6, 1e7, 5e7, 1e8];

    queue()
        .defer(d3.json, shapefile)
        .defer(d3.json, pop_data)
        .await(ready);

    function ready(error, world, fips) {

		var d3line2 = d3.svg.line()
                .x(function(d){return d.x;})
                .y(function(d){return d.y;})
                .interpolate("linear");

        if (!error) {
            country_pop = fips;
            country_extent = d3.entries(fips);

            var threshold = d3.scale.threshold()
                .domain(pop_domain)
                .range(d3.range(6).map(function(i) {
                    return "q" + i + "-5";
                }))
            ;

            var tooltip_scale = d3.scale.linear()
                .range([1, 3.5])
                .domain([1, 44]);
            	//.range([1, 3.5])
            	//.domain([1,44]);

            var first_time = false;

            function redraw() {
            	hud.selectAll('#tooltip').remove();

                if (!first_time) {
                    map.attr("transform", "translate(" + width + "," + height + " ) scale(1.000005)");
                    first_time = true;
                }

                var scale = d3.event.scale === 1 ? 1.000005 : d3.event.scale;
                var t1 = projection.translate(),
                    t2 = d3.event.translate,
                    t = [t2[0] - (t1[0] - width / 2), t2[1] - (t1[1] - height / 2)];

                map.attr("transform",
                    "translate(" + t + ") " +
                    "scale(" + (scale) + height / 2 + ")"
                );
            }

            svg.append('defs')
                .append('radialGradient')
                .attr('id', 'water')
                .attr('cx', '50%')
                .attr('cy', '50%')
                .attr('r', '50%')
                .attr('fx', '50%')
                .attr('fy', '50%')
                .selectAll('stop')
                .data([{
                    offset: '0%',
                    style: 'stop-color:#C6E2FF;stop-opacity:1'
                }, {
                    offset: '100%',
                    //style: 'stop-color:#80baff;stop-opacity:1'
                    //Changes the color of the water on the world map
                    style: 'stop-color:#CFD5DC;stop-opacity:1'
                }])
                .enter().append('stop')
                .attr('offset', function(d) {
                    return d.offset;
                })
                .attr('style', function(d) {
                    return d.style;
                });

		var filter = svg.select('defs').append("filter")
		    .attr("id", "drop-shadow")
		    .attr("height", "115%")
		    .attr("width", "115%");

		// SourceAlpha refers to opacity of graphic that this filter will be applied to
		// convolve that with a Gaussian with standard deviation 3 and store result
		// in blur
		filter.append("feGaussianBlur")
		    .attr("in", "SourceAlpha")
		    .attr("stdDeviation", 15)
		    .attr("result", "blur");

		// translate output of Gaussian blur to the right and downwards with 2px
		// store result in offsetBlur
		filter.append("feOffset")
		    .attr("in", "blur")
		    .attr("dx", 0)
		    .attr("dy", 3)
		    .attr("result", "offsetBlur");

		// overlay original SourceGraphic over translated blurred opacity by using
		// feMerge filter. Order of specifying inputs is important!
		var feMerge = filter.append("feMerge");

		feMerge.append("feMergeNode")
		    .attr("in", "offsetBlur")
		feMerge.append("feMergeNode")
		    .attr("in", "SourceGraphic");        

            var map_zoom  = d3.behavior.zoom()
            	.scaleExtent([1, 15.9])
            	.on("zoom", redraw);

            var bg = svg.append("g").call(map_zoom).on("dblclick.zoom", null)
				.on('drag.end', function(d){

					d3.event.sourceEvent.stopPropagation();
				})
            ;

            svg.append('g').attr('class', 'buttons').style('filter', 'url(#drop-shadow)');


            var plus = svg.select('.buttons')
            	.append('g')
            	.attr('class', 'button plus')
            	.attr('transform', 'translate(-250, -400) scale(.7,.7)')
            	.on('click', function(d){
                    d3.event.stopImmediatePropagation();
                    d3.event.stopPropagation();
                    d3.event.preventDefault();
					if (map_zoom.scale() < 15.9) {
						var newX = ((map_zoom.translate()[0] - (width / 2)) * 1.5) + width / 2;
						var newY = ((map_zoom.translate()[1] - (height / 2)) * 1.5) + height / 2;
						map_zoom.scale(map_zoom.scale() * 1.5).translate([newX,newY]);
						map_zoom.event(bg);
					}
			});

           	plus.append('rect')
           		.attr({
           			x: '383.4'
           			,y: '599'
           			,fill: "#ffffff"
           			,width: '25.3'
           			,height: '26.7'
           		})
           		.attr('filter', 'url(#drop-shadow')
            	;
           	plus.append('rect')
           		.attr({
           			x: '385.6'
           			,y: '625.2'
           			,fill: "#D2D3D4"
           			,width: '20.9'
           			,height: '20.5'
           		})
           		;
           	plus.append('path')
           		.attr('d', 'M397.1,606.1v4.8h4.6v2.1h-4.6v4.8H395V613h-4.6v-2.1h4.6v-4.8L397.1,606.1L397.1,606.1z');
           	

            var minus = svg.select('.buttons')
            	.append('g')
            	.attr('class', 'button minus')
            	.attr('transform', 'translate(-250, -382) scale(.7,.7)')

				.on('click', function(d){
                    d3.event.stopImmediatePropagation();
                    d3.event.stopPropagation();
                    d3.event.preventDefault();
                    var newX = ((map_zoom.translate()[0] - (width / 2)) * .75) + width / 2;
					var newY = ((map_zoom.translate()[1] - (height / 2)) * .75) + height / 2;    
					if (map_zoom.scale() > 1 ) {
						
						map_zoom.scale(map_zoom.scale() * 0.75).translate([newX, newY]);
						map_zoom.event(bg);
					}
				})
            	;
           	minus.append('rect')
           		.attr({
           			x: '383.4'
           			,y: '599.7'
           			,fill: "#ffffff"
           			,width: '25.3'
           			,height: '23.8'
           		})
            	;
           	minus.append('rect')
           		.attr({
           			x: '385.6'
           			,y: '599.7'
           			,fill: "#D2D3D4"
           			,width: '20.9'
           			,height: '0	.5'
           		})
           		;
           	minus.append('path')
           		.attr('d', 'M400.7,609.8v2.2h-9.3v-2.2H400.7z');




            var hud = svg.append("g").attr('id', 'hud');

            var legend_text = Object.create(pop_domain);
            legend_text.unshift('Data not available');

      		hud.append('g').selectAll('rect')
      			.data(threshold.range().map(function(color) {
				      var d = threshold.invertExtent(color);
				      //if (d[0] == null) d[0] = x.domain()[0];
				      //if (d[1] == null) d[1] = x.domain()[1];
				      return d;
				    }))
      			.enter()
      			.append('rect')
      			.attr('x', 10)
      			.attr('width', 10)
      			.attr('y', function(d,i){ return height - (i*12 + 30);} )
      			.attr('height', 10)
      			.attr('class', function(d, i){ return "q" + i + "-5";});


      		hud.append('g').selectAll('text')
      			.data(legend_text)
      			.enter()
      			.append('text')
      			.attr('class', 'legend')
      			.attr('x', 30)
      			.attr('y', function(d,i){ return height - (i*12 + 22);} )
      			.text(function(d,i){ 
      				if (!Number.isInteger(d)){
      					return d;
      				} else  if (typeof legend_text[i+1] !== 'undefined'){
          				return nice_pop(d) + ' - ' + nice_pop(legend_text[i+1] - 1) ;  
          			} else {
          				return nice_pop(d) + ' or more';
          			}
      			})

      		hud.append('g')
      			.attr('class', 'legend-header')
      			.append('text')
      			.text('Population, 2026')
      			.attr('transform', 'translate(10,600)')
      			;

        	hud.append('g')
                .attr('class', 'legend-header')
                .append('text')
                .text('*International Trade data, 2025')
                .attr('transform', 'translate(10,695)')
                ;

                bg.append("rect")
                .attr("class", "background")
                .attr('id', 'water_bg')
                .attr("width", width)
                .attr("height", height)
                .style('fill', 'url(#water)');

            var map = bg.append("g")
                .attr("transform", "translate(0, 0) scale(1)");

            	// water background
            	map.append("g")
                .attr("class", "countries")

                .selectAll("path")
                .data(topojson.feature(world, world.objects.global_map).features)
                .enter().append("path")
                .attr("class", function(d) {
                    try {
                        return threshold(+country_pop[d.id].pop);
                    } catch (e) {
                        return '';
                    }
                })
                .attr("d", path)
                .on('mouseover', function(d) {

		        var mouse = d3.mouse(this.parentNode.parentNode.parentNode);
			x = 120;
			threshold = 120;
			y = tooltip_scale(country_pop[d.id].st_name.length);
	 		no_data = country_pop[d.id].pop === null && typeof country_pop[d.id].pop === 'object' ? true : false;
//console.log('calling getUSExportmImport...');
//console.log('country id = '+country_pop[d.id].st_name); 
			var usExports=getUSExportImport(country_pop[d.id].st_name, "export");
			var usImports=getUSExportImport(country_pop[d.id].st_name, "import");

                    if ((country_pop[d.id].st_name=="United States") ||
                        (country_pop[d.id].st_name=="Guam") ||
                        (country_pop[d.id].st_name=="American Samoa") ||
                        (country_pop[d.id].st_name=="Puerto Rico") ||
                        (country_pop[d.id].st_name=="Virgin Islands, U.S.") ||
                        (country_pop[d.id].st_name=="Northern Mariana Islands")
                       ) {
                           text = [country_pop[d.id].st_name, 'Population', nice_pop(country_pop[d.id].pop)];
                    } else {

                          if (usExports == null || usExports == '')  {
                             text = [country_pop[d.id].st_name, 'Population: '+ nice_pop(country_pop[d.id].pop), 'International Trade data not available'];
                          } else {
                             text = [country_pop[d.id].st_name, 'Population: '+ nice_pop(country_pop[d.id].pop), 'U.S. Exports: '+usExports, 'U.S. Imports: '+usImports];
                          }
                    }

	        if ( no_data ) {
            	    text = [country_pop[d.id].st_name, 'Data not available'];	
		        	if( typeof this.__data__.available === 'undefined' ) {
		        		this.__data__.available = false;
			        	}				     
			        } else {
	                	d3.select(this).style('cursor','pointer');
			        }

			        seg_1 = [{x:mouse[0], y:mouse[1]},
			                 {x:mouse[0]+x/6, y:mouse[1]- x/2},
			                 {x:mouse[0]+x/2, y:mouse[1]-x/2}
			        ];
			                    //{x:140, y:100}];                           
            		seg_2 = [{x:mouse[0]+x/2, y:mouse[1]-x/2},
            				{x:mouse[0]-x*y, y:mouse[1]-x/2},
            				{x:mouse[0]-x*y, y:mouse[1]-x*1.5},
            				{x:mouse[0]+x/2, y:mouse[1]-x*1.5}
            		]; 

			        seg_3 = [{x:mouse[0], y:mouse[1]},
			                    {x:mouse[0]-x/6, y:mouse[1]+ x/2},
			                    {x:mouse[0]-x/2, y:mouse[1]+x/2}
			        ];
			                    //{x:140, y:100}];                           
            		seg_4 = [{x:mouse[0]-x/2, y:mouse[1]+x/2},
            				{x:mouse[0]+x*y, y:mouse[1]+x/2},
            				{x:mouse[0]+x*y, y:mouse[1]+x*1.5},
            				{x:mouse[0]-x/2, y:mouse[1]+x*1.5}
            		]; 

            		// flip if near top of container
text_x_offset = mouse[1] < threshold ? mouse[0]-x+x/2+15 : mouse[0]-x*y+15;
text_y_offset = mouse[1] < threshold ? mouse[1]+x-60 : mouse[1]-x-60;

            		//text_x_offset = mouse[1] < threshold ? mouse[0]-x+x/2+15 : mouse[0]-x*y+15;
            		//text_y_offset = mouse[1] < threshold ? mouse[1]+x-35 : mouse[1]-x-35;
            		tt_arrow_path = mouse[1] < threshold ? seg_3 :  seg_1;
            		tt_main_path  = mouse[1] < threshold ? seg_4 : seg_2;

                	hud.selectAll('#tooltip').remove();

                	var tooltip = hud.append('g').attr('id', 'tooltip');

               		tooltip.append("svg:path")
               			.attr('id', 'tooltip-arrow')
    					.attr("d", d3line2(tt_arrow_path) + 'Z')
    					;

               		tooltip.append("svg:path")
               			.attr('id', 'tooltip-main')
    					.attr("d", d3line2(tt_main_path) + 'Z')
    					;
    				
    				tooltip.append("svg:text")
        				.attr({x:text_x_offset, y:text_y_offset})
        				.selectAll('tspan')
        				.data(text)
        				.enter()
        				.append('tspan')
        				.attr({x:text_x_offset, dy:"1.5em"})
        				.text(function(d){return d;});
                    
                })
                .on('mouseout', function(d) {
                   d3.select(this).style('cursor','auto');
                   hud.selectAll('#tooltip').remove();
                })
				.on('click', function(d) {
					// prevent firing on drag event
				    if (d3.event.defaultPrevented) return;
					if ( typeof this.__data__.available === 'undefined' || this.__data__.available ) {
						window.location.href = window.location.href.replace(/\/?(\?|#|$)/, '/$1') + d.id.toLowerCase();
					}
				})
                ;

            var table = d3.select("#most-populous").append("ul").selectAll("li").data(country_extent.sort(function(x, y) {
                    return y['value'].pop - x['value'].pop;
                }).slice(0, 10)).enter()
                .append("li")
                .html(function(d, i) {
                    //var resultStr = '<div><span>' + (i + 1) + '. </span>' + d['value'].st_name + '</div><div>' + nice_pop(d['value'].pop) + '</div>';
                    var resultStr = '<div><span>' + (i+1) + '.</span>' + d['value'].st_name + '</div><div>' + nice_pop(d['value'].pop) + '</div>';

                    return resultStr;
                });
        }
    }

}

