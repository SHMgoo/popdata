<?php
/*****************************************************************************
CHANGE LOG:
===========
10 Sep 2015: Added entry
12 Feb 2020: Added code to use the MITD apis in place of the AITD apis.

******************************************************************************/
   // Includes
   include('./apiData.php');

   //Set this to the current trade year.
   $trade_year='2025';

   /* To test the APIs manually, copy and paste the following into a browser:

      Hover:  Two calls, one for exports, one for imports. The results are 
             combined. 
         https://api.census.gov/data/timeseries/intltrade/exports/hs?get=CTY_NAME,ALL_VAL_YR,CTY_CODE&YEAR=2019&MONTH=12&key=f4a93d15173229253a4f234727b2902053f61bbd;popclock
         https://api.census.gov/data/timeseries/intltrade/imports/hs?get=CTY_NAME,GEN_VAL_YR,CTY_CODE&YEAR=2019&MONTH=12&key=f4a93d15173229253a4f234727b2902053f61bbd;popclock

      Basic facts, country specific(Brazil):

      Merchandise graph, country specific (Brazil):
          This graph is made by combining the results of NAICS api import,
          and the NACIS api export. 

          https://api.census.gov/data/timeseries/intltrade/imports/naics?get=CTY_CODE,CTY_NAME,YEAR,GEN_VAL_YR&MONTH=12&COMM_LVL=MAN&CTY_CODE=3510&key=f4a93d15173229253a4f234727b2902053f61bbd;popclock
          https://api.census.gov/data/timeseries/intltrade/exports/naicstget=NAICS,CTY_CODE,CTY_NAME,YEAR,ALL_VAL_YR&MONTH=12&COMM_LVL=MAN&CTY_CODE=3510&key=f4a93d15173229253a4f234727b2902053f61bbd;popclock
   */

   
 //DEBUG
//    $_GET['get'] = "EXPMANF2025, IMPMANF2025, EXPMANF2024, IMPMANF2024, EXPMANF2023, IMPMANF2023, EXPMANF2022, IMPMANF2022, EXPMANF2021, IMPMANF2021, EXPMANF2020,IMPMANF2020,EXPMANF2019,IMPMANF2019,EXPMANF2018,IMPMANF2018,EXPMANF2017,IMPMANF2017,EXPMANF2016,IMPMANF2016";
//    $_GET["SCHEDULE"] = "3510";
   
 //  $url=$urls[1]; 
  $debug = false;   
 // $debug = true;   
   $paramString="";
   $get="";
   $SCHEDULE="";
   $basicFacts="";
   $hover="";
   $graph=""; 
   $impArray=array();
   $expArray=array();
   $parsedImpArray=array();
   $parsedExpArray=array();
   $get=$_GET["get"];
   $SCHEDULE=$_GET["SCHEDULE"];   
   $paramString="get=".$get."&SCHEDULE=".$SCHEDULE."&key=".$key;
   $json=""; 

   //NAICS ids for countries that should bypass some of the APIs
   $exceptionArray=array('5790');

   //var_dump($get);
   if ($debug) {
      echo "$get<br/>";
      var_dump($get);
      var_dump($paramString);
   }

  /* This code now has to call two MITD apis when invoked, so rather than
   * passing along the query parameters, we now have to determine what 
   * feature called this and combine two API calls into one result set.
   */ 
   $getArray = explode(',', $get); 
   if($debug) { 
      echo "schedule=".$SCHEDULE."<br/>"; 
      echo "get=".$get."<br/>"; 
     // var_dump($getArray);
     // var_dump($SCHEDULE);
   }

  /*  Basic Facts: The AITD api would retrieve all of the data for the
   * 'World Basic Facts' feature in one call. As of 2019, it is no 
   * longer supported, so we now have to make three calls to fetch
   * the same data.
   */
   if(strpos($getArray[0],'EXPALL') !== FALSE)  {

     /* We have to handle North Korea as a special case. The MITD apis
      * don't work at this time, so we need to bypass some of them.
      */
      if ($SCHEDULE == 5790) {
      }  else {
         //Current import
         $impUrl="https://api.census.gov/data/timeseries/intltrade/imports/hs";
         $impParams="get=GEN_VAL_YR&time=".$trade_year."-12&CTY_CODE=".$SCHEDULE."&key=".$key;
         $impArray= $api->getCurlData($impUrl,$impParams);

         //Current export
         $expUrl="https://api.census.gov/data/timeseries/intltrade/exports/hs";
         $expParams="get=ALL_VAL_YR&time=".$trade_year."-12&CTY_CODE=".$SCHEDULE."&key=".$key;
         $expArray= $api->getCurlData($expUrl,$expParams);

         //Historical export
         $expOldUrl="https://api.census.gov/data/timeseries/intltrade/exports/hs";
         $expOldParams="get=ALL_VAL_YR&time=2016-12&CTY_CODE=".$SCHEDULE."&key=".$key;
         $expOldArray= $api->getCurlData($expOldUrl,$expOldParams);


         if($debug) {
            echo "impUrl <br/>";
            var_dump($impUrl);
            echo "impParams<br/>";
            var_dump($impParams);
            echo "expUrl <br/>";
            var_dump($expUrl);
            echo "expParams<br/>";
            var_dump($expParams);
            echo "import data from intltrade<br/>";
            var_dump($impArray);
            echo "2015-11 export data from intltrade<br/>";
            var_dump($expOldArray);
            var_dump($expOldUrl); 
            var_dump($expOldParams);
            echo "2023-11 export data from intltrade<br/>";
            var_dump($expArray);
            var_dump($expUrl); 
            var_dump($expParams);
     
         }
      }

      if(empty($expArray[1][0])) {
         $currentExp = '0';  
      } else {
         $currentExp = $expArray[1][0];
      }

      if(empty($impArray[1][0])) {
         $currentImp = '0';  
      } else {
         $currentImp = $impArray[1][0]; 
      }
        
      if(empty($expOldArray[1][0])) {
         $oldExp = '0';  
      } else {
         $oldExp = $expOldArray[1][0];
      }
        
      $combinedResult= array( array($getArray[0],$getArray[1],$getArray[2],"SCHEDULE"),  array($currentExp,$currentImp,$oldExp,$SCHEDULE));
      //$combinedResult= array( array($getArray[0],$getArray[1],$getArray[2],"SCHEDULE"),  array($expArray[1][0],$impArray[1][0],$baseArray[1][0],$SCHEDULE));

      if($debug) {
         echo "imports...<br/>";
         var_dump($impUrl);
         var_dump($impParams);
         var_dump($impArray);

         echo "exports...<br/>";
         var_dump($expUrl);
         var_dump($expParams);
         var_dump($expArray);
      //   var_dump($baseArray); 
         echo "combined result: <br/>"; 
         var_dump($combinedResult);
         echo "json_encoded: <br/>"; 
         var_dump(json_encode($combinedResult));

      } 
      $json =  json_encode($combinedResult);
   }  //end of Basic Facts 



   /*   Hover data:
    */
   if(strpos($getArray[0],'COUNTRY') !== FALSE)  {

      //Get the export numbers for all countries
      $expUrl="https://api.census.gov/data/timeseries/intltrade/exports/hs";
      $expParams="get=CTY_NAME,ALL_VAL_YR,CTY_CODE&YEAR=".$trade_year."&MONTH=12&key=".$key;

      $expArray= $api->getCurlData($expUrl,$expParams);
      if($debug) {
         echo "hover data:EXPORTS... <br/>"; 
         var_dump($expUrl);
         var_dump($expParams);
      //   var_dump($expArray);

         for($i=0; $i < count($expArray); $i++) {
            var_dump($expArray[$i]);  
         }

      }


      //Get the import numbers for all countries
      $impUrl="https://api.census.gov/data/timeseries/intltrade/imports/hs";
      $impParams="get=CTY_NAME,GEN_VAL_YR,CTY_CODE&YEAR=".$trade_year."&MONTH=12&key=".$key;
      $impArray= $api->getCurlData($impUrl,$impParams);
      if($debug) {
         echo "import data <br/>";
         var_dump($impUrl);
         var_dump($impParams);

         for($i=0; $i < count($impArray); $i++) {
            var_dump($impArray[$i]);  
         }

    }

      if($debug) {
         var_dump($expUrl);
         var_dump($expParams);
         var_dump($expArray);
         var_dump($impUrl);
         var_dump($impParams);
         var_dump($impArray);
      }
      $combinedArray = array(array('COUNTRY','EXPALL'.$trade_year,'IMPALL'.$trade_year,'SCHEDULE')); 
      $innerArray = array();

     /* Loop through one array, and pull in the data from the second
      * array, creating a combined array. The outer array index is 
      * correlated (array[4][n] in import array is same country as 
      * array[4][n] in export array), and there are the same number 
      * of import and export arrays.  
      *  Note: The hover display searches the array by name of country
      *        and it is case sensitive. The search expects the first 
      *        letter of a country name to be capitalized, the rest
      *        lower-case. 
      */
      for($i=1; $i < count($impArray); $i++) {
        /* Ignore anything with a SCHEDULE number is less than 1000.
         * Anything less than1000 indicates something other than a 
         * specific country. 
         *
         * The import and export APIs are not always perfectly aligned,
         * so loop through the import array, and retrieve the export 
         * number using getArrayElement().  
         */
         if ((int)$impArray[$i][2] > 1000) {
            $name =formatCountryName($impArray[$i][0]);
            $fipsCode = (int)$impArray[$i][2]; 
            $exports = getExpArrayValue($fipsCode, $expArray); 
            $innerArray=array($name, 
                              $exports, 
                              $impArray[$i][1], 
                              $impArray[$i][2]);
            array_push($combinedArray,$innerArray);
         }
      }

      /* Exception processing: manually append North Korea to the array.
       */ 
      array_push($combinedArray,array("Korea, North","0","0","5790"));

      $json =  json_encode($combinedArray);

   } //end of Hover data

   /* Return the export number for a specific country FIPS code. 
    * This assumes a specific structure, the structure returned from the 
    * international trade export/import API.  This is necessary because the
    * two import and export API calls return different length array, so
    * merging the two will result in a misaligned array. 
    */
    function getExpArrayValue($fipsCode, $arrayToSearch) {
      $result = ''; 
      for($i=0; $i < count($arrayToSearch) && $result == ''; $i++) {
         if ($arrayToSearch[$i][2] == $fipsCode) {
            $result = $arrayToSearch[$i][1]; 
         } 
      }  
      return $result;
    }



   function  formatCountryName ($cname) {
      $name      = ucwords(strtolower($cname));
      $alteredName;
      $pos=false;  
      if ($pos=strpos($name,'(')) 
      {
         ++$pos;
         $name[$pos]=strtoupper($name[$pos]); 
      } 

      $pos=false;  
      if ($pos=strpos($name,'-')) 
      {
         ++$pos;
         $name[$pos]=strtoupper($name[$pos]); 
      } 

      $nameArray = explode(" ",$name);
      //var_dump($nameArray);

      //Format 'and'
      $key =  array_search('And', $nameArray);
      if($key) {
          //echo "array_search key = ".$key."<br/>";
          $nameArray[$key] = 'and';
      }  

      //Format 'by'
      $key =  array_search('By', $nameArray);
      if($key) {
          $nameArray[$key] = 'by';
      }  

      //Format 'the'
      $key =  array_search('The', $nameArray);
      if($key) {
          $nameArray[$key] = 'the';
      }  
 
      //Format for letter after '('
      $key =  array_search('(', $nameArray);
      if($key) {
          $nameArray[$key] = '';
      }  

      //Exceptions to the rules:
      if(strtolower($nameArray[0]) == 'cote') {
          $nameArray[0] = "Côte";  
          $nameArray[1] = "d'Ivoire";  
      }

      if(strtolower($nameArray[0]) == 'macedonia') {
          $nameArray[0] = "North Macedonia";  
      }
      $alteredName= implode(" ",$nameArray);
      return $alteredName;
   }

  /* U.S. Trade in Manufactured Goods graph: The data comes from three
   * API calls; two made to the NAICS api for years 2013 - present, then one
   * to the old AITD api call for 2012 - 2010. The AITD apis were 
   * no longer supported as of 2020 (the AITD were the apis written 
   * exclusively for World Popclock).  The result is that we have to make
   * three api calls, instead of one, and we have to combine the results.
   *
   * 2023/02/7: The AITD api has aged out.No longer needed.
   */
   if(strpos($getArray[0],'EXPMANF') !== FALSE)  {

      if($debug) {
        echo "entered Manufactured Good graph<br/>";     
      }
      $impUrl="https://api.census.gov/data/timeseries/intltrade/imports/naics";
      $impParams="get=NAICS,CTY_CODE,CTY_NAME,YEAR,GEN_VAL_YR&MONTH=12&COMM_LVL=MAN&CTY_CODE=".$SCHEDULE."&key=".$key;
    
      if($debug) {
         echo "naics imports/exports: <br/>";
         var_dump($impUrl); 
         var_dump($impParams); 
      }  
      if(!in_array($SCHEDULE, $exceptionArray,$SCHEDULE))  {
         if($debug) {
               echo " Not in array, call to NAICS...<br/"; 
         }
         $impArray= $api->getCurlData($impUrl,$impParams);
      } else {
         if($debug) {
            echo " Bypass call to CURL for NAICS imports <br/"; 
         }
      }
      $expUrl="https://api.census.gov/data/timeseries/intltrade/exports/naics";
      $expParams="get=NAICS,CTY_CODE,CTY_NAME,YEAR,ALL_VAL_YR&MONTH=12&COMM_LVL=MAN&CTY_CODE=".$SCHEDULE."&key=".$key;
      $expArray= $api->getCurlData($expUrl,$expParams);
//       $combinedArray = array(array("EXPMANF2024", "IMPMANF2024", "EXPMANF2023", "IMPMANF2023","EXPMANF2022", "IMPMANF2022","EXPMANF2021", "IMPMANF2021", "EXPMANF2020","IMPMANF2020","EXPMANF2019","IMPMANF2019","EXPMANF2018","IMPMANF2018","EXPMANF2017","IMPMANF2017","EXPMANF2016","IMPMANF2016","EXPMANF2015","IMPMANF2015")); 
      $combinedArray = array(array("EXPMANF2025", "IMPMANF2025", "EXPMANF2024", "IMPMANF2024", "EXPMANF2023", "IMPMANF2023","EXPMANF2022", "IMPMANF2022","EXPMANF2021", "IMPMANF2021", "EXPMANF2020","IMPMANF2020","EXPMANF2019","IMPMANF2019","EXPMANF2018","IMPMANF2018","EXPMANF2017","IMPMANF2017","EXPMANF2016","IMPMANF2016","EXPMANF2016","IMPMANF2016"));
      $innerArray = array();

     /* Push the various elements of the three arrays  on to one
      * combined array.  
      */
      if($debug) { 
         echo "NAICS exports....<br/>";
         var_dump($expArray);
         echo "NAICS imports....<br/>";
         var_dump($impArray);
      }
     
      for($i=1; $i < count($impArray); $i++) {
          if($impArray[$i][3] == '2025') {
              $parsedImpArray[0] = $impArray[$i][4];
          } else if ($impArray[$i][3] == '2024') {
              $parsedImpArray[1] = $impArray[$i][4];
          } else if ($impArray[$i][3] == '2023') {
              $parsedImpArray[2] = $impArray[$i][4];
          } else if ($impArray[$i][3] == '2022') {
              $parsedImpArray[3] = $impArray[$i][4];
          } else if ($impArray[$i][3] == '2021') {
              $parsedImpArray[4] = $impArray[$i][4];
          } else if ($impArray[$i][3] == '2020') {
              $parsedImpArray[5] = $impArray[$i][4];
          } else if ($impArray[$i][3] == '2019') {
              $parsedImpArray[6] = $impArray[$i][4];
          } else if ($impArray[$i][3] == '2018') {
              $parsedImpArray[7] = $impArray[$i][4];
          } else if ($impArray[$i][3] == '2017') {
              $parsedImpArray[8] = $impArray[$i][4];
          } else if ($impArray[$i][3] == '2016') {
              $parsedImpArray[9] = $impArray[$i][4];
          } else {
          }
      }
      
      
      
//       for($i=1; $i < count($impArray); $i++) {
//          if($impArray[$i][3] == '2024') {
//             $parsedImpArray[0] = $impArray[$i][4];
//          } else if ($impArray[$i][3] == '2023') {
//             $parsedImpArray[1] = $impArray[$i][4];
//          } else if ($impArray[$i][3] == '2022') {
//             $parsedImpArray[2] = $impArray[$i][4];
//          } else if ($impArray[$i][3] == '2021') {
//             $parsedImpArray[3] = $impArray[$i][4];
//          } else if ($impArray[$i][3] == '2020') {
//             $parsedImpArray[4] = $impArray[$i][4];
//          } else if ($impArray[$i][3] == '2019') {
//             $parsedImpArray[5] = $impArray[$i][4];
//          } else if ($impArray[$i][3] == '2018') {
//             $parsedImpArray[6] = $impArray[$i][4];
//          } else if ($impArray[$i][3] == '2017') {
//             $parsedImpArray[7] = $impArray[$i][4];
//          } else if ($impArray[$i][3] == '2016') {
//             $parsedImpArray[8] = $impArray[$i][4];
//          } else if ($impArray[$i][3] == '2015') {
//             $parsedImpArray[9] = $impArray[$i][4];
//          } else {
//          }
//       }

      for($i=1; $i < count($expArray); $i++) {
         if($expArray[$i][3] == '2025') {
            $parsedExpArray[0] = $expArray[$i][4];
         } else if ($expArray[$i][3] == '2024') {
            $parsedExpArray[1] = $expArray[$i][4];
         } else if ($expArray[$i][3] == '2023') {
            $parsedExpArray[2] = $expArray[$i][4];
         } else if ($expArray[$i][3] == '2022') {
            $parsedExpArray[3] = $expArray[$i][4];
         } else if ($expArray[$i][3] == '2021') {
            $parsedExpArray[4] = $expArray[$i][4];
         } else if ($expArray[$i][3] == '2020') {
            $parsedExpArray[5] = $expArray[$i][4];
         } else if ($expArray[$i][3] == '2019') {
            $parsedExpArray[6] = $expArray[$i][4];
         } else if ($expArray[$i][3] == '2018') {
            $parsedExpArray[7] = $expArray[$i][4];
         } else if ($expArray[$i][3] == '2017') {
            $parsedExpArray[8] = $expArray[$i][4];
         } else if ($expArray[$i][3] == '2016') {
            $parsedExpArray[9] = $expArray[$i][4];
         }  else {
         }
      }
      
//       for($i=1; $i < count($expArray); $i++) {
//           if($expArray[$i][3] == '2024') {
//               $parsedExpArray[0] = $expArray[$i][4];
//           } else if ($expArray[$i][3] == '2023') {
//               $parsedExpArray[1] = $expArray[$i][4];
//           } else if ($expArray[$i][3] == '2022') {
//               $parsedExpArray[2] = $expArray[$i][4];
//           } else if ($expArray[$i][3] == '2021') {
//               $parsedExpArray[3] = $expArray[$i][4];
//           } else if ($expArray[$i][3] == '2020') {
//               $parsedExpArray[4] = $expArray[$i][4];
//           } else if ($expArray[$i][3] == '2019') {
//               $parsedExpArray[5] = $expArray[$i][4];
//           } else if ($expArray[$i][3] == '2018') {
//               $parsedExpArray[6] = $expArray[$i][4];
//           } else if ($expArray[$i][3] == '2017') {
//               $parsedExpArray[7] = $expArray[$i][4];
//           } else if ($expArray[$i][3] == '2016') {
//               $parsedExpArray[8] = $expArray[$i][4];
//           } else if ($expArray[$i][3] == '2015') {
//               $parsedExpArray[9] = $expArray[$i][4];
//           }  else {
//           }
          
//       }
      
      if($debug) { 
         echo "NAICS parsed exports....<br/>";
         var_dump($parsedExpArray);
         echo "NAICS parsed imports....<br/>";
         var_dump($parsedImpArray);
      } 
     
      array_push($innerArray,$parsedExpArray[0],$parsedImpArray[0],
                             $parsedExpArray[1],$parsedImpArray[1],
                             $parsedExpArray[2],$parsedImpArray[2],
                             $parsedExpArray[3],$parsedImpArray[3],
                             $parsedExpArray[4],$parsedImpArray[4],
                             $parsedExpArray[5],$parsedImpArray[5],
                             $parsedExpArray[6],$parsedImpArray[6],
                             $parsedExpArray[7],$parsedImpArray[7],
                             $parsedExpArray[8],$parsedImpArray[8],
                             $parsedExpArray[9],$parsedImpArray[9], $SCHEDULE);
      if($debug) {
         echo "innerArray...<br/>";
         var_dump($innerArray); 
         echo "combinedArray...<br/>";
         var_dump($combinedArray); 
      }
      //Loop through the array, swapping out nulls for zeros. This is another
      //consequence of having to use the MITDs instead of the AITDs.
      for($i=0; $i < count($innerArray); $i++) {
          if (empty($innerArray[$i])) {
            //Assign a zero if null
            $innerArray[$i] = '0';
          } 
      }
 
      array_push($combinedArray,$innerArray);

      $json =  json_encode($combinedArray);

      if($debug) {
        var_dump($impUrl);
        var_dump($impParams);
        echo "impArray: <br/>";
        var_dump($impArray);
        var_dump($expUrl);
        echo "expArray: <br/>";
        var_dump($expParams);
        var_dump($expArray);
        //var_dump($aitdUrl); 
        //var_dump($aitdParams); 
        //echo "aitdArray: <br/>";
        //var_dump($aitdArray); 
        echo "innerArray: <br/>";
        var_dump($innerArray);
        echo "combinedArray: <br/>";
        var_dump($combinedArray);
        var_dump($json);
      }   


   }  //End of Manufactured Goods graph

//Sample of returned data:
//  $uncoded = $api->getCurlData($url,$paramString);
 // $json = json_encode($api->getCurlData($url,$paramString));
  echo $json;
?>
