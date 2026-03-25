<?php

/* This class provides a set of  methods to store and validate the various
 * PEP APIs. The reason for this is to improve the consistency of the data
 * source in Popclock.
 */

require_once  ('../includes/common.php');

class cacheAPI {

   /* Validate the results from the PEP US API. Verify that the total U.S. 
    * population and components of population have reasonable values and
    * that the dates are current (first date is today's date). If so,
    * result a boolean TRUE, otherwise, FALSE.
    * 
    * Input: PEP daily results for US passed in as a one-diminsional array
    *        with 22 elements, following the format of the U.S. pop PEP API.
    */
   public function validateUSPEPPopData($usPOP)  {

      date_default_timezone_set('America/New_York');
      $result = TRUE; 

      /* Numbers are just approximate, though purposely low, and are used for
       * a sanity test. 
       */
      static $totalUSPop     = 328000000;
      static $birthRate      = 6.0;  
      static $deathRate      = 7.0; 
      static $immigrantRate  = 10.0;
      static $netGainRate    = 10.0;
      $debug = false;
      /* Get the date for today and tomorrow. The first date in the cache 
       * file should be today.
       */
      list($year, $month, $day) = explode('-', date('Y-n-j'));
/*
      echo "year = ".$year."<br/>";
      echo "month = ".$month."<br/>";
      echo "day= ".$day."<br/>";
*/
      list($t_year, $t_month, $t_day) = explode('-', date('Y-n-j', mktime(0, 0, 0, date("m"), date("d")+1, date("Y"))));

      //Passed in array must have the correct number of elements
      if (count($usPOP) <> 22) {
         $result = FALSE;
         if($debug) {
            echo "Invalid count <br/>";
         }
      } 

      //Month     
      if ((empty($usPOP[0])) || (int)$usPOP[0] <>  (int)$month) {
         $result = FALSE;
         if($debug) {
            echo "Invalid month<br/>";
         }
      }

      //Day
      if ((empty($usPOP[1])) || (int)$usPOP[1] <> (int)$day) {
         $result = FALSE;
         if($debug) {
            echo "Invalid day<br/>";
         }
      }

      //U.S. population, EDT midnight
      if ( (empty($usPOP[2])) || (int)$usPOP[2] < $totalUSPop) {
         $result = FALSE;
         if($debug) {
            echo "Invalid EDT pop<br/>";
         }
      }

      //U.S. population, EST midnight
      if ( (empty($usPOP[3])) || (int)$usPOP[3] < $totalUSPop) {
         $result = FALSE;
         if($debug) {
            echo "Invalid EST pop<br/>";
         }
      }

      //U.S. population, number of seconds per birth
      if ( (empty($usPOP[4])) || (float)$usPOP[4] < $birthRate) {
         if($debug) {
            echo "Invalid number of seconds per birth<br/>";
         }
         $result = FALSE;
      }

      //U.S. population, number of seconds per death 
      if ( (empty($usPOP[5])) || (float)$usPOP[5] < $deathRate) {
         if($debug) {
            echo "Invalid number of seconds per death <br/>";
         }
         $result = FALSE;
      }

      //U.S. population, number of seconds per immigrant
      if ( (empty($usPOP[6])) || (float)$usPOP[6] < $immigrantRate) {
         if($debug) {
            echo "Invalid number of seconds per immigrant<br/>";
         }
         $result = FALSE;
      }

      //U.S. population, net gain rate 
      if ( (empty($usPOP[7])) || (float)$usPOP[7] < $netGainRate) {
         if($debug) {
            echo "Invalid net gain rate<br/>";
         }
         $result = FALSE;
      }

      //Year    
      if ((empty($usPOP[8])) || (int)$usPOP[8] <> (int)$year) {
         if($debug) {
            echo "Invalid year<br/>";
         }
         $result = FALSE;
      }

      //Month     
      if ( (empty($usPOP[9])) || (int)$usPOP[9] <> (int)$month) {
         if($debug) {
            echo "Invalid month<br/>";
         }
         $result = FALSE;
      }

      //Day
      if ( (empty($usPOP[10])) || (int)$usPOP[10] <>(int)$day) {
         if($debug) {
            echo "Invalid day<br/>";
         }
         $result = FALSE;
      }

      //Tomorrow's data 
      //Month
      if ( (empty($usPOP[11])) || (int)$usPOP[11] <>(int)$t_month) {
         if($debug) {
            echo "Invalid month, tomorrow <br/>";
         }
         $result = FALSE;
      }
      //Day
      if ( (empty($usPOP[12])) || (int)$usPOP[12] <> (int)$t_day) {
         if($debug) {
            echo "Invalid day, tomorrow <br/>";
         }
         $result = FALSE;
      }
      //U.S. population, EDT midnight
      if ( (empty($usPOP[13])) || (int)$usPOP[13] < $totalUSPop) {
         if($debug) {
            echo "Invalid EDT pop, tomorrow <br/>";
         }
         $result = FALSE;
      }
      //U.S. population, EST midnight
      if ( (empty($usPOP[14])) || (int)$usPOP[14] < $totalUSPop) {
         if($debug) {
            echo "Invalid EST pop, tomorrow <br/>";
         }
         $result = FALSE;
      }
      //U.S. population, number of seconds per birth
      if ( (empty($usPOP[15])) || (float)$usPOP[15] < $birthRate) {
         if($debug) {
            echo "Invalid seconds per birth, tomorrow <br/>";
         }
         $result = FALSE;
      }
      //U.S. population, number of seconds per death 
      if ( (empty($usPOP[16])) || (float)$usPOP[16] < $deathRate) {
         if($debug) {
            echo "Invalid seconds per death, tomorrow <br/>";
         }
         $result = FALSE;
      }
      //U.S. population, number of seconds per immigrant
      if ( (empty($usPOP[17])) || (float)$usPOP[17] < $immigrantRate) {
         if($debug) {
            echo "Invalid seconds per immigrant tomorrow <br/>";
         }
         $result = FALSE;
      }
      //U.S. population, net gain rate 
      if ( (empty($usPOP[18])) || (float)$usPOP[18] < $netGainRate) {
         if($debug) {
            echo "Invalid net gain rate, tomorrow <br/>";
         }
         $result = FALSE;
      }
      //Year    
      if ((empty($usPOP[19])) || (int)$usPOP[19] <> (int)$t_year) {
         if($debug) {
            echo "Invalid year, tomorrow <br/>";
         }
         $result = FALSE;
      }
      //Month
      if ( (empty($usPOP[20])) || (int)$usPOP[20] <>(int)$t_month) {
         if($debug) {
            echo "Invalid month, tomorrow <br/>";
         }
         $result = FALSE;
      }
      //Day
      if ( (empty($usPOP[21])) || (int)$usPOP[21] <> (int)$t_day) {
         if($debug) {
            echo "Invalid day, tomorrow <br/>";
         }
         $result = FALSE;
      }
      return $result;
   } //EOM


  /* Read in the data file. The file is stored in JSON format in the 
   * /popclock/data/ directory. The directory should allow the apache
   * server to write to it.
   * 
   * Input: Filename, without the path
   * Return: File contents in JSON format. 
   */

   public function getFile($fileName) {
     // Open file
     $file = file_get_contents($fileName);
     //Return contents 
     return $file;
   }
   
   public function getFileAsArray($fileName) {
     // Open file
     $fileContents = file_get_contents($fileName);

     $filteredCacheStr = preg_replace("/[^a-zA-Z,.|0-9\s]/", "", $fileContents);
     $filteredCacheStr = str_replace("|", ",",$filteredCacheStr);
     $cacheArray = explode(",", $filteredCacheStr);

     //Return contents 
     return $cacheArray;
   }

  /* Validate the entire contents of the data file.  
   */
   public function validateCacheFile($fileName) {
      $result = TRUE;
      $file = self::getFile($fileName);
      return $result;
  } 



   public function getPyramidFile($fileName) {
     // Open file
     $fileContents = file_get_contents($fileName);
     return $fileContents;
   }    

   /* Runs a sanity check on the pyramid data. Expects 
    * the data to be passed in as a 2D array. 
    */
   public function validatePyramidCache($fileName, $fileData) {
      $result = TRUE; 
/*
      //The cache file must be newer that the PYRAMID_REFRESH_DATE.
      $refreshDate = strtotime(PYRAMID_REFRESH_DATE);
      $fileDate    =  filemtime($fileName);
      
      //echo "PYRAMID_REFRESH_DATE = ".strtotime($refreshDate)."<br/>"; 
      // If the cache is older than the refresh date, force a new cache file
      if ($fileDate <  $refreshDate) {
        //echo "cache file is out of date <br/>";
        $result = FALSE;
      } 
      if (empty($fileData) || (count($fileData) < 1800)) {
        $result = FALSE;
      }
*/
      return $result;
   }


   public function validatePyramidAPI($fileData) {
      $result = TRUE; 
      if (empty($fileData) || (count($fileData) < 1800)) {
        $result = FALSE;
      }
      return $result;
   }


   public function validateCountyCache($fileName, $fileData) {
      $result = TRUE; 

      //The cache file must be newer that the COUNTY_POP_DENSITY_REFRESH_DATE.
      $refreshDate = strtotime(COUNTY_POP_DENSITY_REFRESH_DATE);
      $fileDate    =  filemtime($fileName);
      
      //echo "COUNTY_POP_DENSITY_REFRESH_DATE= ".$refreshDate."<br/>"; 
      // If the cache is older than the refresh date, force a new cache file
      if ($fileDate <  $refreshDate) {
        //echo "cache file is out of date <br/>";
        $result = FALSE;
      } 
      if (empty($fileData) || (count($fileData) < 3000)) {
         $result = FALSE;
      }
      return $result;
   }



   public function validateCountyAPI($fileData) {
      $result = TRUE; 
      if (empty($fileData) || (count($fileData) < 3000)) {
         $result = FALSE;
      }
      return $result;
   }


   public function validateStateCache($fileName, $fileData) {
      $result = TRUE; 

      //The cache file must be newer that the STATE_POP_DENSITY_REFRESH_DATE.
      $refreshDate = strtotime(STATE_POP_DENSITY_REFRESH_DATE);
      $fileDate    =  filemtime($fileName);
      
       //echo "STATE_POP_DENSITY_REFRESH_DATE= ".$refreshDate."<br/>"; 
      // If the cache is older than the refresh date, force a new cache file
      if ($fileDate <  $refreshDate) {
        $result = FALSE;
      } 
      if (empty($fileData) || (count($fileData) < 50)) {
         $result = FALSE;
      }
      return $result;
   }

   public function validateStateAPI($fileData) {
      $result = TRUE; 
      if (empty($fileData) || (count($fileData) < 50)) {
         $result = FALSE;
      }
      return $result;
   }


   public function validateCityCache($fileName, $fileData) {
      $result = TRUE; 

      $refreshDate = strtotime(CITY_POP_DENSITY_REFRESH_DATE);
      $fileDate    =  filemtime($fileName);
      
      // If the cache is older than the refresh date, force a new cache file
      if ($fileDate <  $refreshDate) {
        $result = FALSE;
      } 
      if (empty($fileData) || (count($fileData) < 19000)) {
         $result = FALSE;
      }
      return $result;
   }

   public function validateCityAPI($fileData) {
      $result = TRUE; 
      if (empty($fileData) || (count($fileData) < 19000)) {
         $result = FALSE;
      }
      return $result;
   }

   public function validateRegionCache($fileName, $fileData) {
      $result = TRUE; 

      $refreshDate = strtotime(REGION_REFRESH_DATE);
      $fileDate    =  filemtime($fileName);
      
      // If the cache is older than the refresh date, force a new cache file
      if ($fileDate <  $refreshDate) {
        $result = FALSE;
      } 
      if (empty($fileData) || (count($fileData) < 4)) {
         $result = FALSE;
      }
      return $result;
   }

   public function validateRegionAPI($fileData) {
      $result = TRUE; 
      if (empty($fileData) || (count($fileData) < 4)) {
         $result = FALSE;
      }
      return $result;
   }



 

}

?>
