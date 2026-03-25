<?php 
   require_once 'includes/common.php';
   require_once 'includes/config.php';
   require_once 'includes/libraries/funct_JSONLibrary.php';
	
   global $config;
   $stringFips= $_GET['FIPS'];
   $boo=validate_func($stringFips);

  /* If the query string fails validation, redirect it 
   * to the main Popclock page.
   */
   if($boo){
      $fips_in =  $_GET['FIPS'];
      $FIPS = $_GET['FIPS'];
   }else{
    	header('HTTP/1.1 400 Bad Request', true, 401);
    	header("Location: //www.census.gov/popclock");
   }
   $filename = 'data/data.json';
   $world;
	
   function validate_func($string){
      $retVal=true;
      if(preg_match( '/[a-zA-Z]/',$string)) {
   	 $retVal=true;
      }
      if(preg_match( '/\d/', $string)) {
	 $retVal=false;
      }
      if(strlen($string) != 2) {
    	 $retVal= false;
      } 

     /* If there is more than one element in the array, reject it. 
      */
      if (count($_GET) != 1) {
    	 $retVal= false;
      }
      return  $retVal;
   }

   function arr_fil_callback($el){
      global $FIPS;
      return $el['ST_FIPS_Code'] == strtoupper($FIPS);
   }

   if ( file_exists($filename) ){
      $world = json_decode( file_get_contents($filename), true );
   }

   $country_key = array_filter( $world, "arr_fil_callback");
   $seq = key($country_key);
   $country = $world[$seq];
   $schedule = $country['FT_Sch_C_Code'];
   $countries = array();

   foreach($world as $d){
      if (isset($d['ST_FIPS_Code']) && strlen($d['ST_FIPS_Code']) > 0 ) {
         $countries[$d['ST_FIPS_Code']] = $d['ST_Short-form_Name'];
      }
   }


  /* (2/23/2022) Extract the map file locations and assemble the full path. 
   * For some reason this works differently in country_print.php than it
   * does in country.php. The full path will be composed of the _APPROOT_ 
   * and  the country[CIA_MAP_Region_URL]. 
   */ 
   $choppedInsetMap = substr($country['CIA_Map_Region_URL'],3);
   $choppedMap = substr($country['CIA_Map_Country_URL'],3); 
   $insetMap = _APPROOT_.$choppedInsetMap; 
   $countryMap = _APPROOT_.$choppedMap; 

   
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <title>Population Clock: World</title>
	
   <?php require_once 'includes/header.php'; ?>
   <script charset="utf-8" src="./js/d3/d3.min.js"></script>
   <script src="./js/selectize.min.js"></script>
   <script src="./js/plugin.min.js"></script>
   <script src="./js/d3/queue.min.js"></script>
   <script src="./js/d3/topojson.v1.min.js"></script>
   <script charset="utf-8" src="./js/d3/d3.geo.projection.v0.min.js"></script>
   <script charset="utf-8" src="./js/d3.svg.census.min.js"></script>
   <script src="./js/data.min.js"></script>
   <link rel="stylesheet" href="./css/selectize.min.css" type="text/css" media="all" >
    <link rel="stylesheet" href="./css/table.min.css" type="text/css" media="all" >

</head>
<body>
<!--#include virtual="/main/.in/cb_header.inc"-->
<br/>
<br/>
<script type="text/JavaScript" src="./js/akey.min.js"></script>
<script>
function roundPlaces(value, decimals)
{
   var result;
   result=Number(Math.round(value+'e'+decimals)+'e-'+decimals);
   if (result.toString().indexOf(".")==-1) {
      result+=".0";
   }
   return result;
} // roundPlaces()

function roundPlaces2(value, decimals)
{
	var result;
	var _value=value.replace("$", "");
	var suffix="";

	if (_value.indexOf("B")>0)
	{
	  suffix="B";
	  _value=_value.replace("B", "");
	}
	else if (_value.indexOf("M")>0)
	{
	  suffix="M";
	  _value=_value.replace("M", "");
	}
	else if (_value.indexOf("K")>0)
	{
	  suffix="K";
	  _value=_value.replace("K", "");
	}

	if (_value.indexOf(".")==-1)
	  _value+=".0";
	else
        {
	  _value=d3.round(_value, 1);
          if (_value.toString().indexOf(".")==-1)
            _value+=".0";
        }

	result="$"+_value;
	if (suffix!="")
	  result+=suffix;

	return result;
}

function roundGoodsCurrency(value)
{
  var result="";
  var _value;
  var suffix="";

  if (value>1000000000000)
  {
    suffix="T";
    _value=d3.round((value/1000000000000), 1);
  }
  else if (value>1000000000)
  {
    suffix="B";
    _value=d3.round((value/1000000000), 1);
  }
  else if (value>1000000)
  {
    suffix="M";
    _value=d3.round((value/1000000), 1);
  }
  else if (value>1000)
  {
    suffix="K";
    _value=d3.round((value/1000), 1);
  }
  else
  {
    _value=d3.round(value, 1);
  }

  if (_value.toString().indexOf(".")==-1)
    _value+=".0";
  result="$"+_value+suffix;

  return result;
} // roundGoodsCurrency()


$(document).ready(function(){

   var API_naics_error = 0;
   var API_pop_error = 0;
   var API_trade_error = 0;
 
	
   $('#world-select').change(function(e) {
      window.location.href =  '<?php echo _APPROOT_;?>world/' + this.options[this.selectedIndex].value.toLowerCase();
   });		

   $('#map-search').change(function(e) {
      that = this;
      if (typeof window._census_.countries !== 'undefined') {
         country = window._census_.countries.filter(function(d){
	    return d.value == that.value;
	 });		
      }

      if ( !!country[0].data_available ) {
         window.location.href =  '<?php echo _APPROOT_;?>world/' + this.value.toLowerCase();
      } else {
         this.value = null;
      }

   });	

   $('.share').click(function(){
		$('#social').toggle();
   })
   $("#close").click(function ( ) {
		$('#social').toggle();	
   });
   $('#world-select').selectize({create: true, sortField: 'text'});

   var percent = d3.format('.1%');
   var currency = d3.format('$.3s');
   var pop_format = function(number){
      var prefix = d3.formatPrefix(number);
      var roundedNumber = d3.round(prefix.scale(number),1);
      return roundedNumber.toFixed(1) + prefix.symbol;
   };
   var pop_format_y_axis = function(number){
      var prefix = d3.formatPrefix(number);
      var roundedNumber = d3.round(prefix.scale(number),1);

      if ( (roundedNumber=="1")||(roundedNumber=="2") )
                  roundedNumber+=".0";

		if (prefix.symbol=="M")
		  return roundedNumber + prefix.symbol;
		else
		  return roundedNumber + "0" + prefix.symbol;
	  };

var pop_format_y_axis2 = function(value){
  var result="";
  var _value;
  var suffix="";

  if (value>=1000000000000)
  {
    suffix="T";
    _value=d3.round((value/1000000000000), 1);
    if (_value.toString().indexOf(".")==-1)
      _value+=".0";
  }
  else if (value>=1000000000)
  {
    suffix="B";
    _value=d3.round((value/1000000000), 1);
    if (_value.toString().indexOf(".")==-1)
      _value+=".0";
  }
  else if (value>=1000000)
  {
    suffix="M";
    _value=d3.round((value/1000000), 1);
    if (_value.toString().indexOf(".")==-1)
      _value+=".0";
  }
  else if (value>=1000)
  {
    suffix="K";
    _value=d3.round((value/1000), 1);
    if (_value.toString().indexOf(".")==-1)
      _value+=".0";
  }
  else
  {
    _value=d3.round(value, 1);
  }

  result=_value+suffix;

  return result;
};


	var us_pop_format = d3.format('.4s',1);
         function round(number){
            var rounded = d3.round(number,1);
            return rounded;
        };
       
 
	var url = {
                "pop"  : "<?php echo _APPROOT_;?>apiData_pop.php",
                "trade": "<?php echo _APPROOT_;?>apiData_trade.php",
                "naics": "<?php echo _APPROOT_;?>apiData_naics.php"
	};


	var export_data = {
	    			get : "EXPALL2022,IMPALL2022,EXPALL2013"
						,SCHEDULE : "<?php echo $schedule; ?>"
						,key : key
					};

	var pop_data = {
						 get : "POP,AREA_KM2,MPOP,FPOP,TFR"
						,key : key
						,YR : "2022"
						,FIPS : "<?php echo $FIPS;?>"
					};

	var us_pop = {
						 get : "POP,AREA_KM2,MPOP,FPOP,TFR"
						,key : key
						,YR : "2022"
						,FIPS : "US"
					};


	var projected_pop_data = {
						 get : "POP"
						,key : key
						,YR : "2000:2060"
						,FIPS : "<?php echo $FIPS;?>"
					};

	var pop_returnedData = {
						 get : "POP,MPOP0_4,MPOP5_9,MPOP10_14,MPOP15_19,MPOP20_24,MPOP25_29,MPOP30_34,MPOP35_39,MPOP40_44,MPOP45_49,MPOP50_54,MPOP55_59,MPOP60_64,MPOP65_69,MPOP70_74,MPOP75_79,MPOP80_84,MPOP85_89,MPOP90_94,MPOP95_99,MPOP100_,FPOP0_4,FPOP5_9,FPOP10_14,FPOP15_19,FPOP20_24,FPOP25_29,FPOP30_34,FPOP35_39,FPOP40_44,FPOP45_49,FPOP50_54,FPOP55_59,FPOP60_64,FPOP65_69,FPOP70_74,FPOP75_79,FPOP80_84,FPOP85_89,FPOP90_94,FPOP95_99,FPOP100_"
						,key : key
						,YR : "2022"
						,FIPS : "<?php echo $FIPS;?>"
					};


	var trade_data = {
	    			get : "EXPMANF2024, IMPMANF2024, EXPMANF2023, IMPMANF2023, EXPMANF2022, IMPMANF2022, EXPMANF2021, IMPMANF2021, EXPMANF2020,IMPMANF2020,EXPMANF2019,IMPMANF2019,EXPMANF2018,IMPMANF2018,EXPMANF2017,IMPMANF2017,EXPMANF2016,IMPMANF2016,EXPMANF2015,IMPMANF2015"
						,key : key
						,SCHEDULE : "<?php echo $schedule; ?>"
					};

	var naics_data = {
						 get : "ALL_VAL_YR,STATE,NAICS"
						,key : key
						,CTY_CODE : "<?php echo $schedule; ?>"
					};

$.when( $.ajax({  //use when to group Population data calls together for error handling.
		type: 'GET',
		url: url["pop"], 
		data: pop_data,
		dataType: 'json',
		cache: true,
		success: function ( returnedData, textStatus, jqXHR ) {

			if (returnedData) {
				
				var  pop = +returnedData[1][returnedData[0].indexOf('POP')]
					,area = +returnedData[1][returnedData[0].indexOf('AREA_KM2')]
					,male = +returnedData[1][returnedData[0].indexOf('MPOP')]
					,female = +returnedData[1][returnedData[0].indexOf('FPOP')]
					,tfr = +returnedData[1][returnedData[0].indexOf('TFR')]
				;			
				$('*[data-population]').html( pop_format(pop).replace('G', 'B').replace('k', 'K') ).parent().css('visibility', 'visible')/*.addClass("fade")*/;
				$('*[data-density]').html( addThousandsSeparator((pop / area).toFixed(1)) ).parent().css('visibility', 'visible')/*.addClass("fade")*/;
				$('*[data-male-ratio]').html( (male/ female*100).toFixed(1) ).parent().css('visibility', 'visible')/*.addClass("fade")*/;
				$('*[data-child-ratio]').html( roundPlaces(tfr, 1) ).parent().css('visibility', 'visible')/*.addClass("fade")*/;
			} else {
				API_pop_error++;
			}
		},
		error : function(xhr, errorText, errorMessage) { //If something is wrong with the apiData_pop.php call
			alert('We were unable to retrieve U.S. and World Comparison data in a timely manner.' 
				+ '\nPlease try again in a bit.');
		}
			
	}),

	$.ajax({
		type: 'GET',
		url: url["pop"], 
		data: us_pop,
		dataType: 'json',
		cache: true,
		success: function ( returnedData, textStatus, jqXHR ) {

			if (returnedData) {
				var  pop = +returnedData[1][returnedData[0].indexOf('POP')]
					,area = +returnedData[1][returnedData[0].indexOf('AREA_KM2')]
					,male = +returnedData[1][returnedData[0].indexOf('MPOP')]
					,female = +returnedData[1][returnedData[0].indexOf('FPOP')]
					,tfr = +returnedData[1][returnedData[0].indexOf('TFR')]
				;
                        
			//$('*[data-comp-us-population]').html( us_pop_format(pop)).parent().css('visibility', 'visible')/*.addClass("fade")*/;
                        $('*[data-comp-us-population]').html( us_pop_format(342034432)). parent().css('visibility', 'visible')/*.addClass("fade")*/;

			//$('*[data-comp-us-density]').html( addThousandsSeparator((pop / area).toFixed(1)) ).parent().css('visibility', 'visible')/*.addClass("fade")*/;
                        $('*[data-comp-us-density]').html( addThousandsSeparator((37.4).toFixed(1)) ).parent().css('visibility', 'visible')/*.addClass("fade")*/;

                        //$('*[data-comp-us-male-ratio]').html( (male/ female*100).toFixed(1) ).parent().css('visibility', 'visible')/*.addClass("fade")*/;
                        $('*[data-comp-us-male-ratio]').html( (97.2).toFixed(1) ).parent().css('visibility', 'visible')/*.addClass("fade")*/;

			//$('*[data-comp-us-child-ratio]').html( tfr.toFixed(1) ).parent().css('visibility', 'visible')/*.addClass("fade")*/;	
                        $('*[data-comp-us-child-ratio]').html(1.8).parent().css('visibility', 'visible')/*.addClass("fade")*/;

			$('*[data-comp-world-population]').html( for_comparison.world.population ).parent().css('visibility', 'visible')/*.addClass("fade")*/;
			$('*[data-comp-world-density]').html( for_comparison.world.density ).parent().css('visibility', 'visible')/*.addClass("fade")*/;
			$('*[data-comp-world-male-ratio]').html( for_comparison.world.male_rate ).parent().css('visibility', 'visible')/*.addClass("fade")*/;
			$('*[data-comp-world-child-ratio]').html( for_comparison.world.child_rate ).parent().css('visibility', 'visible')/*.addClass("fade")*/;	
			} else {
				API_pop_error++;
			}
		},
		error : function(xhr, errorText, errorMessage) { //If something is wrong with the apiData_pop.php call
			alert('We were unable to retrieve U.S. and World Comparison data in a timely manner.' 
				+ '\nPlease try again in a bit.');
		}
	}),

	$.ajax({
		type: 'GET',
		url: url["pop"], 
		data: pop_returnedData,
		dataType: 'json',
		cache: true,
		success: function ( returnedData, textStatus, jqXHR ) {
			if (returnedData) {
				var exampleData = [
				];

				var over_85 = {
					group: '85+'
					,male: 0
					,female : 0
				};

				for ( i = 1; i < returnedData[1].length/2 - 1 ; i++) {
					d = returnedData[1][i];
					e = returnedData[0][i];
					if ( i < 18 ) {
						exampleData.push({
						 	group: e.slice(4).replace('_', '-')
							,male: +d
							,female: +returnedData[1][i + ( returnedData[1].length - 1 ) / 2  - 1]
						});
					} else {
						over_85.male += +d;
						over_85.female += +returnedData[1][i + ( returnedData[1].length - 1 ) / 2  - 1]; 
					}
				}	
				exampleData.push(over_85);
				var pyr = d3.svg.census.pyramid();
		 	 	d3.select("#pyramid")
		      	.datum(exampleData)
		      	.call(pyr);
			} else {
				API_pop_error++;
			}
		},
		error : function(xhr, errorText, errorMessage) { //If something is wrong with the apiData_pop.php call
			alert('We were unable to retrieve Population by Age and Sex data in a timely manner.' 
				+ '\nPlease try again in a bit.');
		}
		
	}),

	$.ajax({
		type: 'GET',
		url: url["pop"], 
		data: projected_pop_data,
		dataType: 'json',
		cache: true,
		success: function ( returnedData, textStatus, jqXHR ) { 	
			if (returnedData) {
       	 		returnedData.shift();			
				var current = 
				{
					'title': 'Current'
					,'values': new Array()
				};
				var projected = 
				{
					'title': 'Projected'
					,'values': new Array()
				};
			
				returnedData.forEach(function(d,i){				
					cur_y = date_parse(new Date().getFullYear().toString());
					y = date_parse(d[1]);							
					var _dVal;				
				
					temp = {
						 'date'  : y
						,'value' : +d[0]
					};
				
					if ( +y < +cur_y ) {
						current.values.push(temp);
					}  else if ( +y == +cur_y ) {
						current.values.push(temp);
						projected.values.push(temp);
					} else {
						projected.values.push(temp);
					}
				});
			
			  	var p = d3.svg.census.multi_line()
			      .margin({top: 20, right: 40, bottom: 40, left: 50})
			      .height(300)
			      .width(500)
			      .xDomain([date_parse('2000'), date_parse('2060')])
			      .displayMarkers(true)
                  .tickFormat(function(d){return pop_format_y_axis2(d);})
			      .showLegend(false);

		 	 	d3.select("#line")
		      	.datum([current, projected])
		      	.call(p);
			} else {
				API_pop_error++;
			}  
		},
		error : function(xhr, errorText, errorMessage) { //If something is wrong with the apiData_trade.php call
			alert('We were unable to retrieve Current and Projected Population data in a timely manner.' 
				+ '\nPlease try again in a bit.');
		}
		
	})).done(function(){
			if(API_pop_error != 0) {
				alert('We were unable to retrieve Population data in a timely manner.' 
						+ '\nPlease try again in a bit.');
			}
	});

	
	if ( <?php echo !is_null($schedule) ? 'true' : 'false'; ?> ){
	$.when( $.ajax({ //use when to group trade and naics data calls together for error handling.		
		type: 'GET',
		url: url["trade"], 
		data: export_data,
		dataType: 'json',
		cache: true,
		success: function ( returnedData, textStatus, jqXHR ) {

			if (returnedData) {
				var  exp_15 = +returnedData[1][returnedData[0].indexOf('EXPALL2022')] || 0
					,imp_15 = +returnedData[1][returnedData[0].indexOf('IMPALL2022')] || 0
					,exp_06 = +returnedData[1][returnedData[0].indexOf('EXPALL2013')] || 0
				;
     			var go = currency(round(parseFloat(exp_15)));                     


            	var change_value;

            	if(isFinite((exp_15 - exp_06 )/exp_06)){
                change_value = percent((exp_15 - exp_06 )/exp_06);
            	}else{
                change_value = "Not Available";
            	}

				var _dataExportGoods=roundGoodsCurrency(exp_15);
				var _dataImportGoods=roundGoodsCurrency(imp_15);


				$('*[data-export-goods]').html( _dataExportGoods ).parent().css('visibility', 'visible')/*.addClass("fade")*//* .addClass("fade") */;
				$('*[data-import-goods]').html( _dataImportGoods ).parent().css('visibility', 'visible')/*.addClass("fade")*//* .addClass("fade") */;

				$('*[data-export-difference]').html( change_value  ).parent().css('visibility', 'visible')/*.addClass("fade")*/;
			} else { //If no data is returned from ajax call
				API_trade_error++;
			}
		},
		error : function(xhr, errorText, errorMessage) { //If something is wrong with the apiData_trade.php call
			alert('We were unable to retrieve U.S. export and import data in a timely manner.' 
				+ '\nPlease try again in a bit.');
		}
	}),
	
	$.ajax({
		type: 'GET',
		url: url["trade"], 
		data: trade_data,
		dataType: 'json',
		cache: true,
		success: function ( returnedData, textStatus, jqXHR ) {
	
			if (returnedData) {
				var response_data = Object.create(returnedData[1])
				response_data.pop();

				var export_data = 
				{
					'title': 'Exports'
					,'values': new Array()
				};
				var import_data = 
				{
					'title': 'Imports'
					,'values': new Array()
				};

				response_data.forEach(function(d,i){
					temp = {
						 'date'  : date_parse(returnedData[0][i].slice(-4))
						,'value' : +d
					};

					if ( i % 2 === 0 ) {
					export_data.values.push(temp);
					}  else {
					import_data.values.push(temp);
					}
				});
			  
			  	var m = d3.svg.census.multi_line()
			      .margin({top: 10, right: 40, bottom: 40, left: 50})
			      .height(300)
			      .width(500)
				  .tickFormat(function(d){return '$' + pop_format_y_axis2(d);});


		 		d3.select("#multi-line")
		      	.datum([export_data, import_data])
		      	.call(m);
			} else { //If no data is returned from ajax call
				API_trade_error++;
			}
		},
		error : function(xhr, errorText, errorMessage) { //If something is wrong with the apiData_trade.php call
			alert('We were unable to retrieve U.S. Trade in Manufactured Goods data in a timely manner.' 
				+ '\nPlease try again in a bit.');
		}
		
	}),
	$.ajax({
		type: 'GET',
		url: url["naics"], 
		data: naics_data,
		dataType: 'json',
		cache: true,
		success: function ( returnedData, textStatus, jqXHR ) {
            // Filter out the records with missing STATE values.
            returnedData.shift();  
            //
            var parsedData = new Array();
            for (i = 0; i < returnedData.length; i++) {

                 //Changed 2/22/2022 POPCLOCK-1471.  
                 if ((returnedData[i][1] == '-')|| (returnedData[i][1] =='')) {
                //if (returnedData[i][1] == '') {
                 } else {
                     parsedData.push(returnedData[i]);

                 }
            }
			///

            returnedData = parsedData;
			if (returnedData) {
		    	state_exports = Object.create(returnedData);

		      	state_exports.shift();

		      	var exports = d3.nest()
		      		.key(function(d){return d[1]})
		      		.key(function(d){ 
		      			if ( d3.keys(naics_all).indexOf ( d[2] ) > -1 ) {
			      			return naics_all[d[2]].toLowerCase();
		      			} 
		      		})
		      		.rollup(function(d){return {value: d3.sum(d, function(e){return e[0];})}})
		      		.entries(state_exports);
	
		      	exports.sort(function(a, b){
		      		return d3.sum(b.values, function(d){return d.values.value;}) - d3.sum(a.values, function(d){return d.values.value});
		      	});

		      	var bar_max = d3.max(exports.slice(0,3), function(c) { return d3.max(c.values, function(v) { return v.values.value; })});
		      	var bar_min = 0;
		      	exports.slice(0,3).forEach(function(state, i) {
		      		d3.select('#bar-' + ( i + 1) + ' h5').text(states_abbr.filter(function(d){return d.abbr == state.key }).pop().state.toLowerCase());
				  	var barchart = d3.svg.census.bar()
					  .xDomain([0, bar_max])
				      .margin({top: 0, right: 40, bottom: 0, left: 40})
				      .height( state.values.length < 2 ? 38 : 80 )
				      .width(460)
				      .tickFormat(function(d){return currency(d).replace('G', 'B').replace('k', 'K');})
				      .x(function(d){ return d[0]; })
				      .y(function(d){ return d[1]; })
				      ;
			 	 	d3.select("#bar-" + ( i + 1 ) )
			      	.datum(state.values.map(function(d){ return [d.values.value, d.key]; }).sort(function(a,b){return a[0] - b[0];}).slice(-3))
			      	.call(barchart);

				});
			} else { //If no data is returned from ajax call
				API_naics_error++; 
			}
		},
		error : function(xhr, errorText, errorMessage) { //If something is wrong with the apiData_naics.php call
			alert('We were unable to retrieve the Top Exported Goods by U.S. State or Territory data in a timely manner.' 
				+ '\nPlease try again in a bit.');
		}
	})).done(function(){
		if((API_trade_error != 0)||(API_naics_error != 0)) {
			alert('We were unable to retrieve Trade data in a timely manner.' 
					+ '\nPlease try again in a bit.');
		}
});

} else {

	$('.trade, .chart-container.trade').css({'display':'none'});
	$('<ul><li>International Trade data not available</li></ul>').insertAfter('.data-cell .data-cell:last').css({'display':'block', 'font-size':'12px','margin': '1em 0', 'font-style': 'italic', 'float':'left'});
}

$.ajax({
	type: 'GET',
	url: './data/data.json', 
	dataType: 'json',
	cache: true,
	success: function ( returnedData, textStatus, jqXHR ) {
		if (returnedData) {
			var items = returnedData.map(function(d,i) { 
					return { 
						 'id': i
						,'text': d['ST_Short-form_Name']
						,'value': d.ST_FIPS_Code
						,'data_available': d.IDB_Name.length > 0 ? true : false	
				
					}; 
			});
		
			window._census_ = window._census_ || {};
			window._census_.countries = items;

			$('#map-search').selectize({
		    	maxItems: 1,
		    	options: items,
		    	labelField: "text",
		    	valueField: "value",
		    	searchField: "text",
                sortField: 'text',
                preload: true,
	        
	        	render: {
		        	option: function(item, escape) {

		        		if( !!item.data_available) {
			            	return '<ul>'
			            		+ '<li>'+escape(item.text)+'</span> '
			            	+ '</ul>';
						} else {
			            	return '<ul style="background-color:gainsboro">'
			            		+ '<li style="color:white">'+escape(item.text)+'</span> '
			            		+'<li style="color:white;font-style:italic;font-size:10px;">Data not available</span> '
			            	+ '</ul>';						
						}	
		        	}
		    	},
			});
		} else {
			alert('We were unable to retrieve Country data in a timely manner.' 
					+ '\nPlease try again in a bit.');
		}
	},
	error : function(xhr, errorText, errorMessage) { //If something is wrong with the apiData_trade.php call
		alert('We were unable to retrieve Country data in a timely manner.' 
			+ '\nPlease try again in a bit.');
	}
	
});

});

</script>	
	
	<div id="social" >
	<div class="row">
            Embed <div id="close"><a href="#close">close</a></div>
		<input value='<iframe src="<?php echo "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";?>" width="1008" height="950" frameBorder="0" allowtransparency="true"></iframe>' />
	</div>
	<div class="row">
		<a href="https://twitter.com/share?"><img src="./images/world/twitter.png"/></a>
		<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";?>"><img src="./images/world/facebook.png"/></a>
		<a href="https://www.pinterest.com/pin/create/button/?url=<?php echo "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";?>"
	        data-pin-do="buttonPin"
	        data-pin-config="above"><img src="./images/world/pinterest.png"/></a>
	</div>
	
</div>

	<div id="content-wrapper">
		<?php if ( strlen($FIPS) > 0  ) :  // fips country code is set ?>


		<div id="country-header">
			<h1 class="country">
				<?php echo 	$country['ST_Short-form_Name'];?>
			</h1>
		<span>Demographic data as of July 1, 2026, economic data for 2024<!-- (<a href="#world-footer">source</a>) --></span>

		</div>
		<div class="data-container">
			<div class="data-cell" id="basic-facts">
				<h3>Basic Facts</h3>
				<div class="data-cell" style="background-image: url('./images/world/Icon-Population_2x.png');">
					<p>Population</p>
					<h2 data-population></h2>
				</div>
				<div class="data-cell" style="background-image: url('./images/world/Icon-PeoplePer_2x.png');">
					<p>People per sq. km</p>					
					<h2 data-density></h2>
				</div>
				<div class="data-cell" style="background-image: url('./images/world/Icon-MaleFramel_2x.png');">
					<p>Males per 100 females</p>				
					<h2 data-male-ratio></h2>
				</div>
				<div class="data-cell" style="background-image: url('./images/world/Icon-ChildrenPer_2x.png');">
					<p>Children per woman</p>				
					<h2 data-child-ratio></h2>
				</div>
				<div class="data-cell trade" style="background-image: url('./images/world/Icon-GoodsExported_2x.png');">
					<p>Goods exported from U.S.</p>
					<h2 data-export-goods></h2>
				</div>
				<div class="data-cell trade" style="background-image: url('./images/world/Icon-GoodsImported_2x.png');">
					<p>Goods imported to U.S.</p>					
					<h2 data-import-goods></h2>
				</div>
				<div class="data-cell trade" style="background-image: url('./images/world/Icon-ChangeIn_2x.png');">
					<p>Change in exports from U.S. for 2013 to 2022</p>		
					<h2 data-export-difference></h2>
				</div>
			
			</div>
			
			<div class="data-cell" id="closer-look">
				<h3>A Closer Look</h3>
				<figure>
					<div class="inset-bg">
						<img class="region-inset" src="<?php echo $insetMap ?>"/>
					</div>
					<img class="country" src="<?php echo $countryMap;?>"/>
					<figcaption><a href="#world-footer">Source</a></figcaption>
				</figure>
			</div>
		</div>

		<!-- comparison table -->
		<div id="comparisons">
			<div class="comparisons-header">
				<div>
					<h3>Comparisons</h3>
				</div>
				<div style="background-image: url('./images/world/Icon-Population_2x.png');">Population</div>
				<div style="background-image: url('./images/world/Icon-PeoplePer_2x.png');">People per sq. km</div>
				<div style="background-image: url('./images/world/Icon-MaleFramel_2x.png');">Males per 100 females</div>
				<div style="background-image: url('./images/world/Icon-ChildrenPer_2x.png');">Children per woman</div>
			</div>
			<div class="comparisons-us">
				<div><h5>Compared to the U.S.</h5></div>
				<div data-comp-us-population></div>
				<div data-comp-us-density></div>
				<div data-comp-us-male-ratio></div>
				<div data-comp-us-child-ratio></div>
			</div>
			<div class="comparisons-world">
				<div><h5>Compared to the World</h5></div>
				<div data-comp-world-population></div>
				<div data-comp-world-density></div>
				<div data-comp-world-male-ratio></div>
				<div data-comp-world-child-ratio></div>
			</div>
		</div>
		
		<!-- key -->
		<div class="data-cell key">
			<ul class="key">
				<li>K = Thousands</li>
				<li>M = Millions</li>
				<li>B = Billions</li>
			</ul>
		</div>



<p class="breakhere">

<div class="chart-container trade">
  <h3 class="trade">Merchandise Trade</h3>
  <div class="chart-world container vertical-gradient shadow beveled default-border">
    <div class="chart-wrapper ">
      <div class="component-header horizontal-gradient outset">
        <h4>U.S. Trade in Manufactured Goods</h4>
      </div>
      <div id="multi-line" class="chart trade"></div>
    </div>
    <div class="chart-wrapper">
      <div class="component-header horizontal-gradient outset">
        <h4>Top Exported Goods by U.S. State or Territory</h4>
      </div>
      <div id="bar" class="chart trade">
        <div id="bar-1"><h5></h5></div>
        <div id="bar-2"><h5></h5></div>
        <div id="bar-3"><h5></h5></div>
      </div>
    </div>
  </div>
</div>
		
<div class="chart-container" class="">
  <h3>Population</h3>
  <div class="chart-world container vertical-gradient shadow beveled default-border">
    <div class="chart-wrapper ">
      <div class="component-header horizontal-gradient outset">
        <h4>Current and Projected Population</h4>
      </div>
      <div id="line" class="chart"></div>
    </div>
    <div class="chart-wrapper">	 
      <div class="component-header horizontal-gradient outset">
        <h4>Population by Age and Sex</h4>    
      </div>   
      <div id="pyramid" class="chart"></div>
      <div id="slider" ></div>
    </div>
  </div>
</div>
	<?php else :  // fips country code is not set ?>
       <!-- buttons --> 
       <?php require_once 'includes/tabs.php'; ?>
        <!-- world clock -->
        	<?php
				// individual sharable component
				require_once 'population_counters/population_counters.php';
				?>
			
        <div id="map-filter">	
			<input type="text" value="country" class="map-search selectized" id="map-search" style="display: block;">
				<span id="label" class="title">Search or select a country</span>
        </div>
        <div id="world-map"></div>

<div id="most-populous2">
<br>
<table border=0 cellspacing=10 width=100%>
  <tr>
    <td valign=top width=33%>
      <table border=0 cellspacing=0 cellpadding=10 width=100%>
              <tr>
          <th class=orange width=100%><b>Most Populous Countries</th>
        </tr>
        <?php 
        	$top_5_countries = array_slice($config->components->world_rates->tables[0]->rows ,0,5);
        	$top_5_links = array(
        		'China' => './world/ch',
        		'India' => './world/in',
        		'United States' => '.',
        		'Indonesia' => './world/id',
		        'Pakistan' => './world/pk',
        	);
        	$seq = 0;
        	foreach($top_5_countries as $country => $country_pop){
        		$seq++;
        		$class = $seq % 2 == 0 ? 'even' : 'odd' ;
        ?>
        		<tr>
        		<td class=<?php echo $class;?>><span style="float:left;"><?php echo $seq;?>. <a href="<?php echo $top_5_links[$country];?>"><?php echo $country;?></a></span><span style="float:right;"><?php echo number_format($country_pop,0);?></span></td>
        		</tr>
        <?php } ?>
      </table>
    </td>
    <td valign=top width=33%>
      <table border=0 cellspacing=0 cellpadding=10 width=100%>
        <tr>
          <th class=blue width=100%><b>Top U.S. Export Partners</th>
        </tr>
           <tr>
              <td class=odd><span style="float:left;">1. <a href="./world/ca">Canada</a></span><span style="float:right;">$349.9 B</span></td>
            </tr>
            <tr>
              <td class=even><span style="float:left;">2. <a href="./world/mx">Mexico</a></span><span style="float:right;">$334.0 B</span></td>
            </tr>
            <tr>
              <td class=odd><span style="float:left;">3. <a href="./world/ch">China</a></span><span style="float:right;">$143.2 B</span></td>
            </tr>
            <tr>
              <td class=even><span style="float:left;">4. <a href="./world/nl">Netherlands</a></span><span style="float:right;">$88.2 B</span></td>
            </tr>
            <tr>
              <td class=odd><span style="float:left;">5. <a href="./world/uk">United Kingdom</a></span><span style="float:right;">$79.5 B</span></td>
            </tr>
      </table>
    </td>
    <td valign=top width=33%>
      <table border=0 cellspacing=0 cellpadding=10 width=100%>
        <tr>
          <th class=blue width=100%><b>Top U.S. Import Partners</th>
        </tr>
         <tr>
          <td class=odd><span style="float:left;">1. <a href="./world/mx">Mexico</a></span><span style="float:right;">$505.5 B</span></td>
        </tr>
        <tr>
          <td class=even><span style="float:left;">2. <a href="./world/ch">China</a></span><span style="float:right;">$438.7 B</span></td>
        </tr>
        <tr>
          <td class=odd><span style="float:left;">3. <a href="./world/ca">Canada</a></span><span style="float:right;">$411.9 B</span></td>
        </tr>
        <tr>
          <td class=even><span style="float:left;">4. <a href="./world/gm">Germany</a></span><span style="float:right;">$160.4 B</span></td>
        </tr>
        <tr>
          <td class=odd><span style="float:left;">5. <a href="./world/ja">Japan</a></span><span style="float:right;">$148.4 B</span></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<font size=-2>&nbsp;&nbsp;&nbsp;K = Thousands &nbsp;&nbsp; M = Millions &nbsp;&nbsp; B = Billions</font>
<br><br><br>
</div>
	<?php endif; ?>
    <div id="world-footer">

		<h3><?php echo strlen($FIPS) > 0  ? $config->world_footnotes->country->label : $config->world_footnotes->map->label ;?></h3>
		<?php echo strlen($FIPS) > 0  ? $config->world_footnotes->country->content : $config->world_footnotes->map->content; ?>

	</div>
</div>
<!--#include virtual="/main/.in/cb_footer.inc"-->
	</div>

<script language="Javascript">
// once all Ajax calls have finished, print the page
$( document ).ajaxStop(function() {
	window.print(); 
});

</script>

</body>

</html>
