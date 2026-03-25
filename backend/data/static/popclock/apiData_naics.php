<?php
/********************************************************************************
CHANGE LOG:
===========
10 Sep 2015:    Added entry
********************************************************************************/
  // Includes
  include('./apiData.php');

  $debug = false;
  $url=$urls[2];
  $paramString="";
  $get="";
  $CTY_CODE="";
  $get=$_GET["get"];
  $CTY_CODE=$_GET["CTY_CODE"];
  $naicsArray  = array(); 

   //NAICS ids for countries that should bypass some of the APIs
   $exceptionArray=array('5790');

  $paramString="get=".$get."&CTY_CODE=".$CTY_CODE."&YEAR=2025&MONTH=12&COMM_LVL=NA4"."&key=".$key;

   if($debug) {
      var_dump($url);
      var_dump($getParamArray);  
      var_dump($CTY_CODE); 
   }

   if(!in_array($CTY_CODE, $exceptionArray))  {
      if($debug) echo "CTY_CODE".$CTY_CODE."not found in exception array"; 
      $naicsArray  = $api->getCurlData($url,$paramString);
      $json = json_encode($naicsArray); 
   } else {
      $json = null; 
      if($debug) echo "CTY_CODE ".$CTY_CODE." found in exception array"; 
   }
   //$json = json_encode($naicsArray); 

  echo $json;

?>
