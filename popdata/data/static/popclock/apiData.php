<?php
/********************************************************************************
CHANGE LOG:
===========
10 Sep 2015:	Added entry
28 Sep 2015:	Added https:// to url calls
17 May 2017:    Updated API Key
********************************************************************************/
  // Includes
  require('includes/api.php');

  // Instantiate the api class
  $api = new api();


//echo "apiData.php<br/>";
//var_dump($_GET);
  $key="f4a93d15173229253a4f234727b2902053f61bbd;popclock";

  $urls=array();
/*
  if(API_MODE == 'ER') {
      $urls[0]="https://data.er.ditd.census.gov/data/timeseries/idb/5year";
  } else  {
      $urls[0]="https://api.census.gov/data/timeseries/idb/5year";
  }
*/
      //$urls[0]="https://api.census.gov/data/timeseries/idb/5year";
  $urls[0]="https://api.census.gov/data/timeseries/idb/5year";
  //$urls[0]="https://data.er.ditd.census.gov/data/timeseries/idb/5year";
  //$urls[0]="https://data.fr.ditd.census.gov/data/timeseries/idb/5year";

  //$urls[0]="http://cedsci-web23.stage.ditd.census.gov:8080/data/timeseries/idb/5year";

  //$urls[0]="http://cedsci-web23.stage.ditd.census.gov:8080/data/timeseries/idb/5year";
 // $urls[0]="https://data.fr.ditd.census.gov/data/timeseries/idb/5year";


  $urls[1]="https://api.census.gov/data/2018/intltrade/imp_exp";
  //$urls[1]="https://web10.dev.rm.census.gov/data/2017/intltrade/imp_exp";

  $urls[2] = "https://api.census.gov/data/timeseries/intltrade/exports/statenaics";
  
 // $urls[2]="https://web10.dev.rm.census.gov/data/timeseries/intltrade/exports/statenaics";


?>
