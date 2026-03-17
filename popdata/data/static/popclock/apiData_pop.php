<?php
/********************************************************************************
CHANGE LOG:
===========
10 Sep 2015:    Added entry
********************************************************************************/
  // Includes
  include('./apiData.php');

  //Mapping of the FIPS code to the new GENC codes
  include('./fipsToGenc.php');
//   echo "apiData_pop.php<br/>";
//var_dump($_GET);


   $url=$urls[0];
   $paramString="";
   $get="";
   $YR="";
   $FIPS="";
   $time="";
   $get=$_GET["get"];
   if(isset($_GET["FIPS"])) {
       $FIPS=strtoupper($_GET["FIPS"]);
       //echo "FIPS ".$FIPS."maps to ".$map[$FIPS]."<br/>";
       $FIPS = $map[$FIPS];
   }

   

  /* The standard acronym 'FIPS' has been replaced by the string below. 'FIPS'
   * is passed into this program because it is configured as 'FIPS' in the 
   * apache and .htaccess files, so the code below will replace FIPS with 
   * this random phrase. Not to mention, FIPS is still used in the ITD APIs
   * and hardcoded into the topo data file. We will swap out the FIPS with the 
   * new GENC value here.
   */ 
   $fipsReplacement = "genc%20standard%20countries%20and%20areas";

   if((!empty($_GET["YR"])) && (strlen($_GET["YR"]) > 8)) {
   // $time=$_GET["time"];
      if(!empty($FIPS)) {
         $paramString="get=".$get."&for=".$fipsReplacement.":".$FIPS."&YR=2020:2060&key=".$key;
      } else {
         $paramString="get=".$get."&for=".$fipsReplacement.":*&YR=2020:2060&key=".$key;
      }
   } else if (!empty($_GET["YR"])) {
      $YR=$_GET["YR"];
      //$paramString="get=".$get."&YR=".$YR."&FIPS=".$FIPS."&key=".$key;
      if($_GET["FIPS"] == '') {
         $paramString="get=".$get."&for=".$fipsReplacement.":*&time=".$YR."&key=".$key;
      } else {
         $paramString="get=".$get."&for=".$fipsReplacement.":".$FIPS."&time=".$YR."&key=".$key;
      }
   } else if (!empty($_GET["time"])) {
      $time=$_GET["time"];
      $paramString="get=".$get."&for=".$fipsReplacement.":".$FIPS."&time=".$time."&key=".$key;
   }

  # DEBUG:
  //$paramString.="get=POP,AREA_KM2,MPOP,FPOP,TFR&key=".$key."&YR=2015&FIPS=US";
//var_dump($url);
//var_dump($paramString);

  $json = json_encode($api->getCurlData($url,$paramString));
 
  echo $json;
?>
