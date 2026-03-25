<?php
require_once ('/vs/www/php-bin/app-config/popclock/bootstrap.inc.php');

require_once (UTIL_DIR . 'JSON' . DS . 'funct_JSONLibrary.php');

require ('../apiData.php');

require('cacheAPI.php');

/*
ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', 1);
 error_reporting(E_ALL);
ini_set('display_errors','On');
*/
/* Murali - 12/3/2013 - Added getVintage() method to enable querying for the vintage estimates */

/* Murali - 12/27/2013 - Added getPercentageWIthPrecision() method to allow for specifying precision digits for the return value */

class population {
    private $path;

    private $query;

    private $methods = array();

    private $filters = array();

    private $fields = array();

    private $data = array();

    private static $hasDateRange = false;

    private static $hasCompDate = false;

    private static $dayLevelSpecific = false;

    private static $badRequest = false;

    private static $realTZ;

    private $manifest_options = array('fields');

    
    public function __construct () {
       date_default_timezone_set('America/New_York');
       self::$realTZ = date_default_timezone_get();

        //self::$dayLevelSpecific = true;
        try {
            if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'get') {
                $this->parseURL();
            } else {
                throw new Exception("Invalid request method.");
            }
        } catch (Exception $e) {
            $this->data['error'] = 403;
            $this->data['message'] = $e->getMessage();
        }
    }

    private function parseURL () {
        try {
            if (isset($_SERVER['SERVER_NAME'])) {
                $URL = 'http' . ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? 's://' : '://') . $_SERVER["SERVER_NAME"];
                if ($_SERVER["SERVER_PORT"] != "80") {
                    $URL .= ":".$_SERVER["SERVER_PORT"];
                }
                $URL .= $_SERVER["REQUEST_URI"];
                $urlBits = parse_url($URL);
                $this->parsePath ($urlBits);
                $this->parseQuerystring($urlBits);
            } else if (!defined('DEBUG_MODE') || (defined('DEBUG_MODE') && DEBUG_MODE === false)) {
                throw new Exception("Unable to parse the URL. Please ensure you are connecting via HTTP.");
            }
        } catch (Exception $e) {
            $this->data['error'] = 403;
            $this->data['message'] = $e->getMessage();
        }
    }

    private function parsePath ($urlBits) {
        $paths = array();
        
        $parents = array_keys(array_change_key_case(class_parents($this), CASE_LOWER));
        $current = strtolower(get_class($this));
        $actualPath = (strpos($urlBits['path'], '.php')) ? str_replace(NEW_BASE_URL.'.php', '', $urlBits['path']) : str_replace(NEW_BASE_URL, '', $urlBits['path']);
        foreach (explode('/', substr($actualPath, 1)) as $path) {
            if (($path !== $current || $path !== "$current\.php") && !in_array($path, $parents)) {
                $paths[] = $path;
            }
        }
        if (count($paths)) {
            $this->path = implode('_', $paths);
        } else {
            $this->path = '';
        }
    }

    private function parseQuerystring ($urlBits) {
        if (isset($urlBits['query'])) {
            parse_str($urlBits['query'], $q);
            if (array_key_exists('manifest', $q) && in_array($q['manifest'], $this->manifest_options)) {
                $this->registerMethod('manifest', $q['manifest']);
            } else {
                if (array_key_exists('manifest', $q)) {
                    unset($q['manifest']);
                }
                foreach ($q as $key => $value) {
                    if (method_exists($this, 'M_'.$key)) {
                        $this->registerMethod($key, $value);
                    } else if (method_exists($this, 'F_'.$key)) {
                        $this->registerFilter($key, $value);
                    }
                }
            }
        }
    }

    public function __toString () {
        $this->processRequest();
        return json_encode($this->data);
    }

    private function registerMethod ($method, $param) {
        $methodFunction = "M_".$method;
        $valid = $this->{$methodFunction}($param);
        if ($valid !== false) {
            $this->methods[$method] = $valid;
        }
    }

    private function registerFilter ($filter, $param) {
        $filterFunction = "F_".$filter;
        $valid = $this->{$filterFunction}($param);
        if ($valid !== false) {
            $this->filters[$filter] = $valid;
        }
    }

    public function processRequest() {
        if (!isset($this->data['error'])) {
            if (!strpos($this->path, '_') && !self::$badRequest) {
                switch ($this->path) {
                    case 'region':                        
                        if (isset($this->methods['manifest'])) {
                            //$this->getRegionalManifest();
                        } else {
                            $this->getRegionalAPI();
                        }
                        break;
                    case 'demographic':
                        if (isset($this->methods['manifest'])) {
                            //$this->getDemographicManifest();
                        } else {
                            //$this->getDemographic();
                            $this->getDemographicAPI();
                        }
                        break;
                    case 'rank':
                        if (isset($this->methods['manifest'])) {
                            //$this->getRankedManifest();
                        } else {
                            //$this->getRanked();
                            $this->getRankedAPI();
                        }
                        break;
                    case 'world':
                        if (isset($this->methods['manifest'])) {
                         //   $this->getWorldPopManifest();
                        } else {
                            $this->getWorldPop();
                        }
                        break;
                    case 'us':
                        if (isset($this->methods['manifest'])) {
                        //    $this->getUSPopManifest();
                        } else {
                            //$this->getUSPop();
                            $this->getUSPopAPI();
                        }
                        break;
                    case 'location':
                       //$this->getLocation();
                        break;
                    case 'vintage':
                        if (!empty($this->filters) && isset($this->filters['fips'])) 
                        {
                            //Does not appear to be used
                            //$this->getVintage();
                        }
                        else //throw an invalid request message
                        {
                            $this->data['error'] = 403;
                            $this->data['message'] = "An invalid request was submitted.";
                        }
                        break ;
                    default:
                        if (isset($this->methods['manifest'])) {
                            //$this->getUSPopManifest();
                            //$this->getWorldPopManifest();
                        } else {
                            //$this->getUSPop();
                            $this->getUSPopAPI();
                            $this->getWorldPop();
                        }
                        break;
                }
                if (empty($this->data)) {
                    $this->data['error'] = 400;
                    $this->data['message'] = "No data found matching request criteria.";
                }
            } else {
                $this->data['error'] = 403;
                $this->data['message'] = "An invalid request was submitted.";
            }
        }
    }

    private function appendQueryStatements(&$queryObj) {
        if (count($this->filters)) {
            if (isset($this->filters['daterange'])) {
                self::$hasDateRange = true;
                foreach ($this->filters['daterange'] as $dateunit => $query_param) {
                    if ($dateunit !== 'day') {
                        $queryObj->q_and($query_param);
                    } else if ($dateunit === 'day' && self::$dayLevelSpecific) {
                        $queryObj->q_and($query_param);
                    }
                }
            } else if (isset($this->filters['date'])) {
                if (self::$hasCompDate) {
                    unset($this->filters['date']['year']);
                    unset($this->filters['date']['month']);
                    unset($this->filters['date']['day']);
                } else {
                    unset($this->filters['date']['comp_date']);
                }
                foreach ($this->filters['date'] as $dateunit => $query_param) {
                    if ($dateunit !== 'day') {
                        $queryObj->q_and($query_param);
                    } else if ($dateunit === 'day' && self::$dayLevelSpecific) {
                        $queryObj->q_and($query_param);
                    }
                }
                $limit = 1;
            }
        }
        if (count($this->methods)) {
            // Check for sorts / orders
            if (isset($this->methods['sort'])) {
                $queryObj->q_order($this->methods['sort']['fields'], $this->methods['sort']['direction']);
            }
            // Limits and offsets
            if (isset($this->methods['limit']) && isset($this->methods['offset'])) {
                $limit = $this->methods['limit'];
                $offset = $this->methods['offset'];
                $queryObj->q_limit($limit, $offset);
            } else if (isset($this->methods['limit'])) {
                $queryObj->q_limit($this->methods['limit']);
            } else if (isset($limit)) {
                $queryObj->q_limit($limit);
            }
        }
    }

   //Return string 'EST' or 'EDT'

   private function getTimeZone($year, $month, $day) {

     //date_default_timezone_set('America/New_York');
     $tz = 'EDT'; 

     //Current date in Eastern US
     //list($year, $month, $day) = explode('-', date('Y-n-j'));
     if (($year == '2018') && ($month == '11') && ((int)$day > 3)){
        $tz='EST';
     } elseif  (($year == '2018') && ($month == '3') && ((int)$day < 11)){
        $tz='EST';
     } elseif  (($year == '2018') && (($month == '12') || ($month == '1') || ($month == '2'))) {
        $tz='EST';
     } elseif  (($year == '2019') &&  (($month == '12') || ($month == '1') || ($month =='2'))) {
        $tz='EST';
     } elseif  (($year == '2019') && ($month == '3') && ((int)$day < 10)){
        $tz='EST';
     } elseif  (($year == '2019') && ($month == '11') && ((int)$day > 2 )){
        $tz='EST';
     } elseif (($year == '2020') && (($month == '12') || ($month == '1') || ($month =='2'))) {
        $tz='EST';
     } elseif  (($year == '2020') && ($month == '3') && ((int)$day < 9)){
        $tz='EST';
     } elseif  (($year == '2020') && ($month == '11')) {
        $tz='EST';
     } elseif  (($year == '2021') && (($month == '12') || ($month == '1') || ($month =='2'))){
        $tz='EST';
     } elseif  (($year == '2021') && ($month == '3') && ((int)$day < 15)){
        $tz='EST';
     } elseif  (($year == '2021') && ($month == '11') && ((int)$day > 6 )){
        $tz='EST';
     } elseif  (($year == '2022') && (($month == '12') || ($month == '1') || ($month =='2'))){
        $tz='EST';
     } elseif  (($year == '2022') && ($month == '3') && ((int)$day < 14)){
        $tz='EST';
     } elseif  (($year == '2022') && ($month == '11') && ((int)$day > 5 )){
        $tz='EST';
     } elseif  (($year == '2023') && (($month == '12') || ($month == '1') || ($month =='2'))){
        $tz='EST';
     } elseif  (($year == '2023') && ($month == '3') && ((int)$day < 13)){
        $tz='EST';
     } elseif  (($year == '2023') && ($month == '11') && ((int)$day > 4 )){
        $tz='EST';
     } elseif  (($year == '2024') && (($month == '12') || ($month == '1') || ($month =='2'))){
        $tz='EST';
     } elseif  (($year == '2024') && ($month == '3') && ((int)$day < 11)){
        $tz='EST';
     } elseif  (($year == '2024') && ($month == '11') && ((int)$day > 2 )){
        $tz='EST';
     } elseif  (($year == '2025') && (($month == '12') || ($month == '1') || ($month =='2'))){
        $tz='EST';
     } elseif  (($year == '2025') && ($month == '3') && ((int)$day < 10)){
        $tz='EST';
     } elseif  (($year == '2025') && ($month == '11') && ((int)$day > 1 )){
        $tz='EST';
     } elseif  (($year == '2026') && (($month == '12') || ($month == '1') || ($month =='2'))){
        $tz='EST';
     } elseif  (($year == '2026') && ($month == '3') && ((int)$day < 9)){
        $tz='EST';
     } elseif  (($year == '2026') && ($month == '11')){
        $tz='EST';
     } elseif  (($year == '2027') && (($month == '12') || ($month == '1') || ($month =='2'))){
        $tz='EST';
     } elseif  (($year == '2027') && ($month == '3') && ((int)$day < 15)){
        $tz='EST';
     } elseif (($year == '2027') && ($month == '11') && ((int)$day > 8 )){
        $tz='EST';
     } elseif  (($year == '2028') && (($month == '12') || ($month == '1') || ($month =='2'))){
        $tz='EST';
     } elseif  (($year == '2028') && ($month == '3') && ((int)$day < 13)){
        $tz='EST';
     } elseif  (($year == '2028') && ($month == '11') && ((int)$day > 3 )){
        $tz='EST'; 
     } elseif  (($year == '2010') && (($month == '12') || ($month == '1') || ($month =='2'))){
        $tz='EST';
     } elseif  (($year == '2010') && ($month == '3') && ((int)$day < 14)){
        $tz='EST';
     } elseif  (($year == '2010') && ($month == '11') && ((int)$day > 6 )){
        $tz='EST'; 
     } elseif  (($year == '2011') && (($month == '12') || ($month == '1') || ($month =='2'))){
        $tz='EST';
     } elseif  (($year == '2011') && ($month == '3') && ((int)$day < 13)){
        $tz='EST';
     } elseif  (($year == '2011') && ($month == '11') && ((int)$day > 5 )){
        $tz='EST'; 
     } elseif  (($year == '2012') && (($month == '12') || ($month == '1') || ($month =='2'))){
        $tz='EST';
     } elseif  (($year == '2012') && ($month == '3') && ((int)$day < 11)){
        $tz='EST';
     } elseif  (($year == '2012') && ($month == '11') && ((int)$day > 3 )){
        $tz='EST'; 
     } elseif  (($year == '2013') && (($month == '12') || ($month == '1') || ($month =='2'))){
        $tz='EST';
     } elseif  (($year == '2013') && ($month == '3') && ((int)$day < 10)){
        $tz='EST';
     } elseif  (($year == '2013') && ($month == '11') && ((int)$day > 2 )){
        $tz='EST'; 
     } elseif  (($year == '2014') && (($month == '12') || ($month == '1') || ($month =='2'))){
        $tz='EST';
     } elseif  (($year == '2014') && ($month == '3') && ((int)$day < 9)){
        $tz='EST';
     } elseif  (($year == '2014') && ($month == '11') && ((int)$day > 1 )){
        $tz='EST'; 
     } elseif  (($year == '2015') && (($month == '12') || ($month == '1') || ($month =='2'))){
        $tz='EST';
     } elseif  (($year == '2015') && ($month == '3') && ((int)$day < 8)){
        $tz='EST';
     } elseif  (($year == '2015') && ($month == '11')){
        $tz='EST'; 
     } elseif  (($year == '2016') && (($month == '12') || ($month == '1') || ($month =='2'))){
        $tz='EST';
     } elseif  (($year == '2016') && ($month == '3') && ((int)$day < 13)){
        $tz='EST';
     } elseif  (($year == '2016') && ($month == '11') && ((int)$day > 5 )){
        $tz='EST'; 
     } elseif  (($year == '2017') && (($month == '12') || ($month == '1') || ($month =='2'))){
        $tz='EST';
     } elseif  (($year == '2017') && ($month == '3') && ((int)$day < 12)){
        $tz='EST';
     } elseif  (($year == '2017') && ($month == '11') && ((int)$day > 4 )){
        $tz='EST'; 
     } else {
        $tz = 'EDT'; 
     } 

      return $tz;
   }

   private function getUSPopAPI() {        

       //Get dates for yesterday, today, and tomorrow
       list($year, $month, $day) = explode('-', date('Y-n-j'));
       list($t_year, $t_month, $t_day) = explode('-', date('Y-n-j', mktime(0, 0, 0, date("m"), date("d")+1, date("Y"))));
       list($y_year, $y_month, $y_day) = explode('-', date('Y-n-j', mktime(0, 0, 0, date("m"), date("d")-1, date("Y"))));

       self::$dayLevelSpecific = true;

       //$this->query = new CBDBQueryBuilder();        
       $fields = "`year`, `month`, `day`, value, edtvalue";

       $tz = self::getTimeZone($year, $month, $day);
       //This is called for the U.S. odometer
       if (empty($this->filters)) {

           //U.S. Odometer
           list($c_year, $c_month, $c_day) = explode('-', date('Y-n-j'));

           // Get the pop data for today and tomorrow
           $popTodayURL = self::getDailyPEP($year, $month, $day);
           $popTomorrowURL= self::getDailyPEP($t_year, $t_month, $t_day);

           // 86,400 seconds
           $seconds = SECONDS_IN_A_DAY; // number of seconds in 24 hours
//var_dump($popTodayURL);

           // If the current DST is EDT, use EDTMIDNIGHT from the PEP API.
           // If DST is EST, use ESTMIDNIGHT. Also setTZ to the current 
           // Daylight Savings time zone.

           //Set this to the current time zone (EDT or EST)
           if ($tz == 'EDT' )  {
              //Set this to the current time zone (EDT or EST)
              self::setTZ('EDT');
              $midnightPop = $popTodayURL[0];
           } else {
              self::setTZ('EST');
              $midnightPop = $popTodayURL[1];
           }

           $midnightPop = round($midnightPop); 

           //Population growth over 24 hour period. Does not matter if we
           //use EST or EDT, it just the difference in pop over 24 hours
           $diff = $popTomorrowURL[0] - $popTodayURL[0];

           //Growth / number of seconds in a day (86400)
           $rate = $diff / $seconds;

           $start   = date('U', mktime(0, 1, 0, $month, $day, $year));
           $now = date('U');

           // (Number of seconds since midnight/seconds in a 24 hour period) x population increase 
           // for 24 hours = added population since midnight
           $pop_added = (($now - $start) / $seconds) * $diff;

           $currentEstimateURL = array('rate' => $rate, 'estimate' => ($midnightPop+ $pop_added));

/*
echo "tz = ".$tz."<br/>";
echo "start = ".$start."<br>";
echo "now= ".$now."<br>";
echo "seconds= ".$seconds."<br>";
echo "diff= ".$diff."<br>";
echo "percentage to apply to 24 pop increase".(($now-$start)/$seconds)."<br/>";
echo "pop added since midnight ".$pop_added."<br/>";
*/
            
           $change_components = array();

           $birth_rate_db = round($popTodayURL[2]);
           $death_rate_db = round($popTodayURL[3]); 
           $immigrant_rate_db = round($popTodayURL[4]); 

           $this->data['us'] = array(
                'label' => "United States",
                'estimate' => true,
                'population' => intval($currentEstimateURL['estimate']),
                'midnight_pop' => $midnightPop,
                'population_rate' => $currentEstimateURL['rate'],
                'rate_interval' => "second",
                'last_updated' => date('U'),
                'components' => array(
                    'birth_rate' => array(
                        'increment' => 1,
                        'interval' => $birth_rate_db,
                        'unit' => 'seconds'
                    ),
                    'death_rate' => array(
                        'increment' => 1,
                        'interval' => $death_rate_db,
                        'unit' => 'seconds'
                    ),
                    'immigrant_rate' => array(
                        'increment' => 1,
                        'interval' =>$immigrant_rate_db,
                        'unit' => 'seconds'
                    )
                ),
                    'date'      =>gmdate("m/d/Y"),
            );
       } else {
           //Having a problem finding where in Popclock this is called
           if (isset($this->filters['daterange'])) {
               $this->data = array('us' => array('label' => "United States"));
               foreach ($result as $row) {
                   $dt = mktime(0, 0, 0, $row->month, $row->day, $row->year);
                   $this->data['us'][$dt]['estimate'] = true;
                   $this->data['us'][$dt]['population'] = intval($row->edtvalue);
               }
                   $this->data['us']['last_updated'] = date('U');
           } else {

               // Select Pop on Date.

               $y_year = $this->filters['date']['year'];
               $y_month= $this->filters['date']['month'];
               $y_day = $this->filters['date']['day'];
               $yy_arr = explode(" ",$y_year);
               $ym_arr = explode(" ",$y_month);
               $yd_arr = explode(" ",$y_day);
               $yy = $yy_arr[2];
               $ym = $ym_arr[2];
               $yd = $yd_arr[2];

               $popYesterdayURL = self::getDailyPEP($yy, $ym, $yd);

               /* If the API returns nulls, then use the cached data 
                * and change the date to today.
                */
                if (empty($popYesterdayURL[0])) {
                   $popNow  = self::getDailyPEP($year, $month, $day);
                   $selectedPop= $popNow[0]; 
                   // U.S. Population On Date
                   $this->data['us'] = array(
                     'label' => "United States",
                     'estimate' => true,
                     'population' => intval($selectedPop),
                     'last_updated' => mktime(0, 0, 0, $month, $day, $year),
                     'date'          =>$year.$month.$day,
                     'cache' => true
                   );

                } else {

                   //The select a date will always use EDTmidnight in order
                   // to match a downloadable monthly file, which shows all
                   // pop in EDT.

                   $selectedPop= $popYesterdayURL[0]; 

                   // U.S. Population On Date
                   $this->data['us'] = array(
                     'label' => "United States",
                     'estimate' => true,
                     'population' => intval($selectedPop),
                     'last_updated' => mktime(0, 0, 0, $ym, $yd, $yy),
                     'date'          =>$yy.$ym.$yd,
                     'cache' => false
                   );
                }
           }

       }
       self::setTZ(self::$realTZ);
   } //end of getUSPopAPI


    public function getWorldPop() {

        //Data is from the includes/common.php file
        $world_pop =  $GLOBALS['world_pop'];

        list($c_year, $c_month, $c_day) = explode('-', date('Y-n-j'));
        $useLastYear = false;

        // Select the current year if after July 1, last year if before July 1
        if ($c_month < 7) { 
            $c_year--; 
            $useLastYear = true; 
        } else {
            $useLastYear = false; 
        }

        list($n_year, $n_month, $n_day) = explode('-', date('Y-n-j', mktime(0, 0, 0, 7, 1, $c_year+1)));

        self::setTZ('UTC');
        $popCurrDate = date('U', mktime(0, 0, 0, 7, 1, $c_year));
        $popNextDate = date('U', mktime(0, 0, 0, 7, 1, $n_year));
        $now = date('U');
        self::setTZ(self::$realTZ);
        $diff = $world_pop[$n_year] - $world_pop[$c_year]; 
        $timediff = $popNextDate - $popCurrDate;

        $numDays = floor(($now - $popCurrDate)/86400);
        // difference in seconds from year to year/Seconds in a day(86400)
        $daysdiff = ($timediff/86400) ;
        $rate = $diff / $timediff;

        //Population at 12:02AM EDT today
        $popMidnightPlusTwo = $world_pop[$c_year]+ round($rate*($numDays*86520));
        //$pop_added = ($popCurrYear->value + ($now-$popCurrDate) * $rate);
        $pop_added = ($world_pop[$c_year] + ($now-$popCurrDate) * $rate);

        $currentEstimate = array('rate' => $rate, 'estimate' => intval($pop_added));
        $monthlyEstimates = $this->monthlyWorldPop($world_pop[$c_year], $world_pop[$n_year], $popCurrDate, $popNextDate,$daysdiff);
        /*
        $debug = array(
            'Current Year (UTC) $popCurrDate' => $popCurrDate,
            'Upcoming Year (UTC) $popNextDate' => $popNextDate,
            'Current - Upcoming = $timediff' => $timediff,
            'Right Now (UTC) $now' => $now,
            'Next Pop - Current Pop = $diff' => $diff,
            '$diff / $timediff' => $rate,
            'Est = Current Value + ($now-$popCurrDate) * $rate' => $pop_added
        );
        */
        self::setTZ('UTC');
        $this->data['world'] = array(
            'label'             => "World Population",
            'estimate'          => true,
            'population'        => intval($currentEstimate['estimate']),
            'pop_midnight'      => $popMidnightPlusTwo,
            'population_rate'   => $currentEstimate['rate'],
            'rate_interval'     => "second",
            'last_updated'      => date('U'),
            'monthly_estimates' => $monthlyEstimates,
                        'date'              =>date("Ymd")
        );
        self::setTZ(self::$realTZ);
    }





    public function getRegionalAPI() {
        //$this->query = new CBDBQueryBuilder();
        if (isset($this->filters['regions'])) {
            if (count($this->filters)) {
                //echo "count non-zero in filters <br>";
                if (isset($this->filters['daterange'])) {
                    //echo  "has daterange <br>";
                    self::$hasDateRange = true;
                    foreach ($this->filters['daterange'] as $dateunit => $query_param) {
                        if ($dateunit !== 'day') {
                           // $joined .= " AND " . $query_param;
                        } else if ($dateunit === 'day' && self::$dayLevelSpecific) {
                           // $joined .= " AND " . $query_param;
                        }
                    }
                } else if (isset($this->filters['date'])) {
                    //echo  "has date <br>";
                       
                    if (self::$hasCompDate) {
                        unset($this->filters['date']['year']);
                        unset($this->filters['date']['month']);
                        unset($this->filters['date']['day']);
                    } else {
                        unset($this->filters['date']['comp_date']);
                    }
                    foreach ($this->filters['date'] as $dateunit => $query_param) {
                        if ($dateunit !== 'day') {
                            //$joined .= " AND " . $query_param;
                        } else if ($dateunit === 'day' && self::$dayLevelSpecific) {
                            //$joined .= " AND " . $query_param;
                        }
                    }
                    $limit = 1;
                }
            }
            if (self::$hasDateRange) {
            } else {
            }

           /* I cannot find any way that getRegionalAPI() is called 
            * without a date range. For now, the assumption is that
            * a date range will always be present in the query string.
            */ 
            $start = substr($this->filters['daterange']['comp_date'],52,4);
            $stop  = substr($this->filters['daterange']['comp_date'],67,4);
           
           $result= $this->getRegionalDateRange($start, $stop);  {
               self::setTZ('UTC');
               $dataset = $allValues = $sums = $totals = $dates = array();
                foreach ($result as $row) {
                    if (self::$hasDateRange) {
                        $resultSetDate = mktime(0, 0, 0, $row->month, $row->day, $row->year);
                        $range_start = (empty($range_start)) ? $resultSetDate : $range_start;
                        $range_end = $resultSetDate;
                        $sums[$resultSetDate] = (isset($sums[$resultSetDate])) ? $sums[$resultSetDate] + intval($row->value) : intval($row->value);
                        $dataset[strtolower($row->region_name)]['label'] = $row->region_name;
                        if (!isset($dataset[strtolower($row->region_name)]['values'])) {
                            $dataset[strtolower($row->region_name)]['values'] = array();
                        }
                        $vals = array('date' => $resultSetDate, 'population' => intval($row->value), 'estimate' => true);
                        array_push($dataset[strtolower($row->region_name)]['values'], $vals);
                        if (!in_array($resultSetDate, $dates)) {
                            array_push($dates, $resultSetDate);
                        }
                        array_push($allValues, intval($row->value));
                    } else {
                        $dataset[strtolower($row->region_name)] = array(
                            'label' => $row->region_name, 'population' => intval($row->value), 'estimate' => true,'date'=>"$row->year$row->month$row->day"
                        );
                        array_push($allValues, intval($row->value));
                        $lastUpdated = (empty($lastUpdated)) ? mktime(0, 0, 0, $row->month, $row->day, $row->year) : $lastUpdated;
                        $sums[$lastUpdated] = (isset($sums[$lastUpdated])) ? $sums[$lastUpdated] + intval($row->value) : intval($row->value);
                    }
                }
                if (empty($lastUpdated)) {
                    $lastUpdated = $this->findMaxValue($dates);
                }
                if (!empty($sums)) {
                    foreach ($sums as $date => $pop) {
                        array_push($totals, $pop);
                    }
                    $percs = $values = array();
                    foreach ($dataset as $region => &$row) {
                        if (self::$hasDateRange) {
                            foreach ($row as $vals => &$record) {
                                if (is_array($record)) {
                                    foreach ($record as $r => &$data) {
                                        $region_perc = $this->getPercentageWithPrecision($sums[$data['date']], $data['population'],1);
                                        $data['percentage'] = $region_perc;
                                        array_push($percs, $region_perc);
                                    }
                                }
                            }
                        } else {
                            $region_perc = $this->getPercentageWithPrecision($sums[$lastUpdated], $row['population'],1);
                            $row['percentage'] = $region_perc;
                            array_push($percs, $region_perc);
                        }
                    }
                    $dataset['max_percent'] = $this->findMaxValue($percs);
                    $dataset['max_population'] = $this->findMaxValue($totals);
                }
                if (!empty($dataset)) {
                    if (!self::$hasDateRange) {
                        $dataset['max_region_pop'] = $this->findMaxValue($allValues);
                    } else {
                        $dataset['max_region_pop'] = $this->findMaxValue($allValues);
                        $dataset['range_start'] = $range_start;
                        $dataset['range_end'] = $range_end;
                    }
                    $dataset['last_updated'] = $lastUpdated;
                    $this->data = $dataset;
                }
                self::setTZ(self::$realTZ);
            }
        }
    }




    private function getDemographicAPI() {
       // $this->query = new CBDBQueryBuilder();
        // Default select parameters
        $fields = "`year` , `month` , `day` , age, gender, value";
        // Get gender / age values ordered by date and age
        // Get gender totals ordered by date
      //  $total = new CBDBQueryBuilder();
        self::$hasDateRange = true;
        // Make both queries identical from this point forward
   //     $this->appendQueryStatements($this->query);
   //     $this->appendQueryStatements($total);


        /* Stub out the DB queries, use APIs instead. 
         */
        // Execute the queries
        $start = substr($this->filters['daterange']['comp_date'],52,4);
        $stop  = substr($this->filters['daterange']['comp_date'],67,4);
        $resultsAgeGender = self::getDemographicDateRangeAgeGender($start,$stop);
        $total_result = $resultsAgeGender[1];
        $result       = $resultsAgeGender[0];


        if ($result) {
            self::setTZ('UTC');
            $dataset = array(
                'male' => array('values' => array()),
                'female' => array('values' => array()),
                'max_value' => 0,
                'max_percent' => 0
            );
            if (self::$hasDateRange) {
                $ranged_values = array();
                foreach ($total_result as $total) {
                    $timestamp = mktime(0, 0, 0, $total->month, $total->day, $total->year);
                    if (!isset($ranged_values[$timestamp])) {
                        $ranged_values[$timestamp] = array(
                            'male' => array('gender_perc' => array(), 'total_perc' => array(), 'actual_values' => array() ),
                            'female' => array('gender_perc' => array(), 'total_perc' => array(), 'actual_values' => array() )
                        );
                    }
                    $ranged_values[$timestamp][((intval($total->gender) === 1) ? 'maleTotal' : 'femaleTotal')] = intval($total->value);
                }
                $currentDate = 0;
                $maxActual = $maxPercentage = array();
                $maleVals = $femaleVals = array();
                foreach ($result as $row) {
                    $timestamp = mktime(0, 0, 0, $row->month, $row->day, $row->year);
                    $gender = (intval($row->gender) === 1) ? 'male' : 'female';
                    if ($currentDate !== intval($timestamp)) {
                        $vals = array();
                        $currentDate  = intval($timestamp);
                        $maleTotal    = $ranged_values[$currentDate]['maleTotal'];
                        $femaleTotal  = $ranged_values[$currentDate]['femaleTotal'];
                        $ranged_values[$currentDate]['total_pop'] = ($maleTotal + $femaleTotal);
                        $maleVals[$currentDate]['age_group'] = array();
                        $femaleVals[$currentDate]['age_group'] = array();
                    }
                    $gTotal = (intval($row->gender) === 1) ? $maleTotal : $femaleTotal;
                    $ageGrp = (strlen($row->age) < 2) ? str_pad($row->age, 2, "0", STR_PAD_LEFT) : $row->age;
                    $age = array();
                    $gPercent = $this->getPercentage($gTotal, $row->value);
                    $tPercent = $this->getPercentage($ranged_values[$currentDate]['total_pop'], $row->value);

                    array_push($ranged_values[$currentDate][$gender]['gender_perc'], $gPercent);
                    array_push($ranged_values[$currentDate][$gender]['total_perc'],  $tPercent);
                    array_push($ranged_values[$currentDate][$gender]['actual_values'], intval($row->value));
                    $age = array(
                        'age'               => $ageGrp,
                        'gender_percentage' => $gPercent,
                        'total_percentage'  => $tPercent,
                        'actual_value'      => intval($row->value),
                    );
                    if ($gender === 'male') {
                        array_push($maleVals[$currentDate]['age_group'], $age);
                    } else {
                        array_push($femaleVals[$currentDate]['age_group'], $age);
                    }
                }
                $m = array();
                foreach ($maleVals as $date => $ageGroup) {
                    $m[]['date'] = $date;
                    end($m);
                    $d = key($m);
                    $m[$d]['max_gender_percentage']   = $this->findMaxValue($ranged_values[$date]['male']['gender_perc']);
                    $m[$d]['max_total_percentage']    = $this->findMaxValue($ranged_values[$date]['male']['total_perc']);
                    $m[$d]['max_actual_value']        = $this->findMaxValue($ranged_values[$date]['male']['actual_values']);
                    $m[$d]['age_groups'] = $ageGroup['age_group'];
                    array_push($maxActual,     $m[$d]['max_actual_value']);
                    array_push($maxPercentage, $m[$d]['max_gender_percentage']);
                }
                $f = array();
                foreach ($femaleVals as $date => $ageGroup) {
                    $f[]['date'] = $date;
                    end($f);
                    $d = key($f);
                    $f[$d]['max_gender_percentage']   = $this->findMaxValue($ranged_values[$date]['female']['gender_perc']);
                    $f[$d]['max_total_percentage']    = $this->findMaxValue($ranged_values[$date]['female']['total_perc']);
                    $f[$d]['max_actual_value']        = $this->findMaxValue($ranged_values[$date]['female']['actual_values']);
                    $f[$d]['age_groups'] = $ageGroup['age_group'];
                    array_push($maxActual,     $f[$d]['max_actual_value']);
                    array_push($maxPercentage, $f[$d]['max_gender_percentage']);
                }
                $dataset["male"]["values"] = $m;
                $dataset["female"]["values"] = $f;
                $dataset["max_value"] = $this->findMaxValue($maxActual);
                $dataset["max_percent"] = $this->findMaxValue($maxPercentage);
            } else {
                $ranged_values = array();
                foreach ($total_result as $total) {
                    $timestamp = mktime(0, 0, 0, $total->month, $total->day, $total->year);
                    if (!isset($ranged_values[$timestamp])) {
                        $ranged_values[$timestamp] = array(
                            'male' => array('gender_perc' => array(), 'total_perc' => array(), 'actual_values' => array() ),
                            'female' => array('gender_perc' => array(), 'total_perc' => array(), 'actual_values' => array() )
                        );
                    }
                    $ranged_values[$timestamp][((intval($total->gender) === 1) ? 'maleTotal' : 'femaleTotal')] = intval($total->value);
                }
                $currentDate = 0;
                $maxActual = $maxPercentage = array();
                $maleVals = $femaleVals = array();
                foreach ($result as $row) {
                                    #var_dump($row);
                    $timestamp = mktime(0, 0, 0, $row->month, $row->day, $row->year);
                    $gender = (intval($row->gender) === 1) ? 'male' : 'female';
                    if ($currentDate !== intval($timestamp)) {
                        $vals = array();
                        $currentDate  = intval($timestamp);
                        $maleTotal    = $ranged_values[$currentDate]['maleTotal'];
                        $femaleTotal  = $ranged_values[$currentDate]['femaleTotal'];
                        $ranged_values[$currentDate]['total_pop'] = ($maleTotal + $femaleTotal);
                        $maleVals[$currentDate]['age_group'] = array();
                        $femaleVals[$currentDate]['age_group'] = array();
                    }
                    $gTotal = (intval($row->gender) === 1) ? $maleTotal : $femaleTotal;
                    $ageGrp = (strlen($row->age) < 2) ? str_pad($row->age, 2, "0", STR_PAD_LEFT) : $row->age;
                    $age = array();
                    $gPercent = $this->getPercentage($gTotal, $row->value);
                    $tPercent = $this->getPercentage($ranged_values[$currentDate]['total_pop'], $row->value);
                    array_push($ranged_values[$currentDate][$gender]['gender_perc'], $gPercent);
                    array_push($ranged_values[$currentDate][$gender]['total_perc'],  $tPercent);
                    array_push($ranged_values[$currentDate][$gender]['actual_values'], intval($row->value));
                    $age = array(
                        'age'               => $ageGrp,
                        'gender_percentage' => $gPercent,
                        'total_percentage'  => $tPercent,
                        'actual_value'      => intval($row->value),
                    );
                    if ($gender === 'male') {
                        array_push($maleVals[$currentDate]['age_group'], $age);
                    } else {
                        array_push($femaleVals[$currentDate]['age_group'], $age);
                    }
                }
                $m = array();
                foreach ($maleVals as $date => $ageGroup) {
                    $m[]['date'] = date("Ymd", $date);
                    end($m);
                    $d = key($m);
                    $m[$d]['max_gender_percentage']   = $this->findMaxValue($ranged_values[$date]['male']['gender_perc']);
                    $m[$d]['max_total_percentage']    = $this->findMaxValue($ranged_values[$date]['male']['total_perc']);
                    $m[$d]['max_actual_value']        = $this->findMaxValue($ranged_values[$date]['male']['actual_values']);
                    $m[$d]['age_groups'] = $ageGroup['age_group'];
                    array_push($maxActual,     $m[$d]['max_actual_value']);
                    array_push($maxPercentage, $m[$d]['max_gender_percentage']);
                }
                $f = array();
                foreach ($femaleVals as $date => $ageGroup) {
                    $f[]['date'] = date("Ymd",$date);
                    end($f);
                    $d = key($f);
                    $f[$d]['max_gender_percentage']   = $this->findMaxValue($ranged_values[$date]['female']['gender_perc']);
                    $f[$d]['max_total_percentage']    = $this->findMaxValue($ranged_values[$date]['female']['total_perc']);
                    $f[$d]['max_actual_value']        = $this->findMaxValue($ranged_values[$date]['female']['actual_values']);
                    $f[$d]['age_groups'] = $ageGroup['age_group'];
                    array_push($maxActual,     $f[$d]['max_actual_value']);
                    array_push($maxPercentage, $f[$d]['max_gender_percentage']);
                }
                $dataset["male"]["values"] = $m;
                $dataset["female"]["values"] = $f;
                $dataset["max_value"] = $this->findMaxValue($maxActual);
                $dataset["max_percent"] = $this->findMaxValue($maxPercentage);
            }
            //die();
            $this->data = $dataset;
            self::setTZ(self::$realTZ);
        }
    }




    /*   Uses the PEP/population API as a data source for the state, county, 
     *  and city populations.
     */
    private function getRankedAPI() {
        // Default select statement
        $defaultSelect = "UNIX_TIMESTAMP(CONCAT(`year`,'-',`month`,'-',`day`)) AS pr_date, PR.geoid, PR.population, PR.density, GM.value AS name";

        // Population or density request
        if (isset($this->methods['sort']) && is_array($this->methods['sort'])) {
            $sortField = ($this->methods['sort']['fields'] === 'population') ? 'PR.population' : 'PR.density';
            $sortDir   = (strtoupper($this->methods['sort']['direction']) === 'DESC') ? 'DESC' : 'ASC';
            $sortBy = $sortField . ' ' . $sortDir;
            $vintage = ($sortField === 'population') ? RANK_POP_VINTAGE : RANK_DENSE_VINTAGE;
        } else {
            $sortBy = 'PR.population DESC';
            $vintage = RANK_POP_VINTAGE;
        }
        // Set our record limit
        $limit = (isset($this->methods['limit'])) ? intval($this->methods['limit']) : 10;
        if (!isset($this->filters['type']) || $this->filters['type'] === false) {
            $this->filters['type'] = array('cities', 'counties', 'states');
        }
        $date = array();
        // Which types of records are we looking for? Cities, counties, states
        if (in_array('cities', $this->filters['type']) || in_array('city', $this->filters['type'])) {

            if ($sortBy == 'PR.population DESC') {
                $result = self::getRankedPopulation('CITY', US_POP_CITY_VINTAGE,'POPULATION'); 
            } else {
                $result = self::getRankedPopulation('CITY',US_POP_CITY_VINTAGE,'DENSITY'); 
            }

            foreach ($result  as $row) {
                array_push($date, $row->pr_date);
                $this->data['cities'][] = array(
                                        
                    'name' => $row->name . ', ' . $row->state,
                    'population' => intval($row->population),
                    'density' => (($row->density >= 0) ? number_format(floatval($row->density),1) : 'N/A'),
                    'quickfacts' => QFR_BASE_URL . "/" . $row->geoid . "#",
                                        'date'      =>date("Ymd", $row->pr_date),
                );
            }
        }
        if (in_array('counties', $this->filters['type']) || in_array('county', $this->filters['type'])) {

            if ($sortBy == 'PR.population DESC') {
                $result = self::getRankedPopulation('COUNTY', US_POP_COUNTY_VINTAGE,'POPULATION'); 
            } else {
                $result = self::getRankedPopulation('COUNTY', US_POP_COUNTY_VINTAGE,'DENSITY'); 
            }
            foreach ($result  as $row) {
                array_push($date, $row->pr_date);
                $this->data['counties'][] = array(
                    'name' => $row->name . ', ' . $row->state,
                    'population' => intval($row->population),
                    'density' => number_format(floatval($row->density),1),                                    
                    'quickfacts' => QFR_BASE_URL_COUNTIES . '/' . $row->geoid . '#',
                                        'date'      =>date("Ymd", $row->pr_date),
                );
            }
        }

        if (in_array('states', $this->filters['type']) || in_array('state', $this->filters['type'])) {
            if ($sortBy == 'PR.population DESC') {
                $result = self::getRankedPopulation('STATE', US_POP_STATE_VINTAGE,'POPULATION'); 
            } else {
                $result = self::getRankedPopulation('STATE', US_POP_STATE_VINTAGE,'DENSITY'); 
            }

            foreach ($result  as $row) {
                array_push($date, $row->pr_date);

//echo QFR_BASE_URL_STATES . "/" . $row->state."<br/>";
                $this->data['states'][] = array(
                    'name' => $row->name,
                    'population' => intval($row->population),
                    'density' => number_format(floatval($row->density),1),
                    'quickfacts' => QFR_BASE_PATH . "/" . $row->state . "/" . QFR_BASE_STATES_VINTAGE, 'date'      =>date("Ymd", $row->pr_date),
                );
            }
        }
        if (!empty($this->data)) {
            sort($date);
            $this->data['last_updated'] = array_shift($date);
        }
    }

/*
    private function _getLocation() {
        self::$dayLevelSpecific = true;
        if (!empty($this->filters) && isset($this->filters['fips'])) {
            $fips = $this->filters['fips'];
            if (strlen($fips) <= 2) { //Begin conditional code for "US States" and "US"
                $fips = (strlen($fips) < 2) ? str_pad($fips, 2, "0", STR_PAD_LEFT) : $fips;
                if ($fips === '00') {
                    $baseSQL = "SELECT `population_region` . * , geoname.`value` AS label, abbr.`value` AS abbr
                        FROM `population_region`
                        LEFT JOIN `geo_meta` AS geoname ON ( geoname.`type` IN ('nation', 'state', 'city', 'county') AND geoname.`code` = '%s' )
                        LEFT JOIN `geo_meta` AS abbr ON ( abbr.`type` = 'postal' AND abbr.`code` = '%s' )
                        WHERE geoid = '%s'%s LIMIT 1";
                    list($c_year, $c_month, $c_day) = explode('-', date('Y-n-j'));
                    $today_clause = " AND `year` = $c_year AND `month` = $c_month AND `day` = $c_day";
                    list($t_year, $t_month, $t_day) = explode('-', date('Y-n-j', mktime(0, 0, 0, date("m"), date("d")+1, date("Y"))));
                    $tomorrow_clause = " AND `year` = $t_year AND `month` = $t_month AND `day` = $t_day";
                    $todayDB    = CBDB::query($baseSQL, $fips, $fips, $fips, $today_clause);//->fetch();
                    $tomorrowDB = CBDB::query($baseSQL, $fips, $fips, $fips, $tomorrow_clause);//->fetch();
                    $today = $todayDB->fetch();
                    $tomorrow = $tomorrowDB->fetch();
                    $seconds = 86400; // number of seconds in 24 hours
                    $diff = $tomorrow->value - $today->value;
                    $rate = $diff / $seconds;
                    self::setTZ('UTC');
                    $start = date('U', mktime(0, 1, 0, $today->month, $today->day, $today->year));
                    $now = date('U');
                    self::setTZ(self::$realTZ);
                    $pop_added = (($now - $start) / $seconds) * $diff;
                    self::setTZ('UTC');
                    $pop = new stdClass();
                    $pop->label = $today->label;
                    $pop->month = $today->month;
                    $pop->day   = $today->day;
                    $pop->year  = $today->year;
                    $pop->abbr  = $today->abbr;
                    $pop->value = ($today->value + $pop_added);
                                        
                } else {
                    $pop = CBDB::query("SELECT `population_region` . * , geoname.`value` AS label, abbr.`value` AS abbr
                        FROM `population_region`
                        LEFT JOIN `geo_meta` AS geoname ON ( geoname.`type` IN ('nation', 'state', 'city', 'county') AND geoname.`code` = '%s' )
                        LEFT JOIN `geo_meta` AS abbr ON ( abbr.`type` = 'postal' AND abbr.`code` = '%s' )
                        WHERE geoid = '%s' ORDER BY `year` DESC , `month` DESC , `day` DESC LIMIT 1", $fips, $fips, $fips)->fetch();
                }
            }//End Code for "US" and "US States"
                   else { //Begin code for geographies other than US and "US States"
                $pop = CBDB::query("SELECT `population_rank`.`year`, `population_rank`.`month`, `population_rank`.`day`, 
                    `population_rank`.`population` AS value, abbr.`value` AS abbr, 
                    CONCAT(geoname.`value`, ', ', abbr.`value`) AS label
                    FROM `population_rank` 
                    LEFT JOIN `geo_meta` AS geoname ON ( geoname.`type` IN ('nation', 'state', 'city', 'county') AND geoname.`code` = '%s' )
                    LEFT JOIN `geo_meta` AS abbr ON ( abbr.`type` = 'postal' AND abbr.`code` = LEFT('%s', 2) )
                    WHERE geoid = '%s' ORDER BY `population_rank`.`year` DESC , `population_rank`.`month` DESC , `population_rank`.`day` DESC LIMIT 1", $fips, $fips, $fips)->fetch();
            }//End code for geographies other than US and US states
            if (count($pop) || ($pop instanceof stdClass)) {
                self::setTZ('UTC');
                $popDate = date('U', mktime(0, 0, 0, $pop->month, $pop->day, $pop->year));
                $abbr = ($fips !== '00') ? strtolower($pop->abbr) : 'us';
                $this->data[$abbr] = array(
                    'label' => $pop->label,
                    'estimate' => true,
                    'population' => intval($pop->value),
                    'last_updated' => $popDate,
                                        'date'         => date("Ymd", $popDate),
                );
                self::setTZ(self::$realTZ);
            }
                        
        }
    }
*/
/*******************
 * Manifest Methods
 */
    private function getRegionalManifest() {
    }

    private function getDemographicManifest() {

    }
    private function getRankedManifest() {

    }

    private function getWorldPopManifest() {

    }

    private function getUSPopManifest() {

    }

/*
 * Support methods
 */
    private function monthlyWorldPop($startPop, $endPop, $startDate, $endDate,$daysdiff) {
        $estimates = array();
        $popDiff   = $endPop - $startPop;
        $dailyRate = $popDiff / $daysdiff;
        self::setTZ('UTC');
        $now = date('U');
        list($hour,$min,$sec,$mon,$day,$year) = explode('-', strftime('%H-%M-%S-%m-%d-%Y', $startDate));
        $estimates["$startDate"] = array(
            'population' => intval($startPop),
            'estimate' => true,
            'projected' => ($startDate > $now)
        );
        foreach (range(1,10) as $count) {
            $thisMonth = (!isset($thisMonth)) ? $startDate : $nextMonth;
            $lastpop = (!isset($lastpop)) ? $startPop : $estimates["$nextMonth"]['population'];
            $Wmonth = (($mon + $count) > 12) ? ($mon + $count) % 12 : $mon + $count;
            $Wyear = ($Wmonth >= 1 && $Wmonth < 7) ? $year+1 : $year;
            $nextMonth = mktime(0, 0, 0, $Wmonth, 1, $Wyear);
            $newPop = intval($lastpop + ((($nextMonth - $thisMonth) / SECONDS_IN_A_DAY) * $dailyRate));
            $estimates["$nextMonth"] = array(
                'population' => $newPop,
                'estimate' => true,
                'projected' => ($nextMonth > $now)
            );
        }
        self::setTZ(self::$realTZ);
        $estimates["$endDate"] = array(
            'population' => intval($endPop),
            'estimate' => true,
            'projected' => ($endDate > $now)
        );
        return $estimates;
    }

    private function findMaxValue($array) {
        $sortable = $array;
        rsort($sortable);
        return array_shift($sortable);
    }

    private function getPercentage($total, $fraction) {
        return  ($fraction * 100) / $total;
    }

    private function getPercentageWithPrecision($total, $fraction,$precision) {
        return round( ($fraction * 100) / $total,$precision);
    }

    private static function setTZ($tz) { date_default_timezone_set($tz); }

    public static function isValidDate($date) {
        $valid = false;
        if (strlen($date) <= 8 && strlen($date) >= 4 && ctype_digit($date)) {
            //self::setTZ('UTC');
            preg_match_all("/(\d{4})(\d{2})?(\d{2})?/", $date, $matches, PREG_PATTERN_ORDER);
            $d = array_shift($matches);
            $year = $matches[0][0];
            $month = (!empty($matches[1][0])) ? $matches[1][0] : '01';
            $day = (!empty($matches[2][0])) ? $matches[2][0] : '01';
            //$result = date('U', mktime(0, 0, 0, $month, $day, $year));
            $result = mktime(0, 0, 0, $month, $day, $year);
            $result = (date('U') < $result) ? false : $result;
            if ($result) {
                list($Ryear, $Rmonth, $Rday) = explode('-', date('Y-n-j', $result));
                $valid = array('year' => $Ryear, 'month' => $Rmonth, 'day' => $Rday, 'ts' => date('Y-n-j', $result), 'epoch' => $result);
            }
            //self::setTZ(self::$realTZ);
        }
        return $valid;
    }

    public function F_date ($value) {
        $value = self::isValidDate($value);
        $filter = null;
        if (is_array($value)) {
            $filter = array(
                'year' => '`year` = '.$value['year'], 
                'month' => '`month` = '.$value['month'],
                'day' => '`day` = '.$value['day']
            );
        } else {
            self::$badRequest = true;
        }
        return $filter;
    }

    public function F_daterange ($value) {
        list($start, $end) = explode('-', $value);
        $date1 = self::isValidDate($start);
        $date2 = self::isValidDate($end);
        if (is_array($date1) && is_array($date2)) {
            $filter['comp_date'] = "DATE(CONCAT(`year`,'-',`month`,'-',`day`)) BETWEEN '".$date1['ts']."' AND '".$date2['ts']."'";
        } else {
            self::$badRequest = true;
        }
        return ($date1 && $date2) ? $filter : $false;
    }

    public function F_fips ($value) {
        if (!is_array($value) && strpos($value, ',') === false && strlen($value) <= 7) {
            return $value;
        } else {
            return false;
        }
    }


    public function F_regions ($value) {
            return "region IN(1,2,3,4))";
    }


    public function F_type($type) {
        if (strpos($type, ',')) {
            $types = explode(',', $type);
        } else {
            $types = array($type);
        }
        // agebygender type is not necessary anymore
        if (in_array('agebygender', $types)) { return false; }
        // ranked types filter
        $validRegex = array('/cit[y|ies]/', '/count[y|ies]/', '/state(s)?/');
        $valid = array();
        foreach ($types as $type) {
            foreach ($validRegex as $pattern) {
                if (preg_match($pattern, $type)) {
                    array_push($valid, $type);
                }
            }
        }
        return (!empty($valid)) ? $valid : false;
    }

    public static function F_fields () { }

    public static function M_limit ($record_count = null) {
        if (ctype_digit($record_count)) {
            return $record_count;
        }
        return false;
    }

    public static function M_offset ($first_record = null) {
        if (ctype_digit($first_record)) {
            return $first_record;
        }
        return false;
    }

    public static function M_sort ($sort) {
        $valid = false;
        if (strpos($sort, ':')) {
            list($field, $direction) = explode(':', $sort);
            $valid = array('fields' => $field, 'direction' => $direction);
        }
        return $valid;
    }

    public static function M_manifest ($type) {
        switch($type) {
            case 'fields':
                return 'fields';
                break;
            case 'methods':
            case 'filters':
            case 'collections':
            case 'all':
                break;
        }
    }


  /* 
   * Returns the Projected Estimated Population data. Cache the 
   * midnight pop numbers in a file, which will be used in the event 
   * of a temporary API outage.
   */
   public function getDailyPEP($year, $month, $day) { 
      $results = array();
      $currentYear;
      $currentMonth;
      $currentDay;
      $fetchToday     = false; 
      $fetchTomorrow  = false; 
      $edtmidnight;
      $estmidnight;
      $birth;
      $death;
      $immigrant;
      $key = KEY; 
      $file = "daily_PEP.json";

      $pop_api = new api();
      $cacheAPI = new cacheAPI();


      //Cache array
      $cacheData = array(array());
      $cacheData[0] =  array('MONTH','DATE','EDTMIDNIGHT','ESTMIDNIGHT','BIRTHCOMP','DEATHCOMP','TOTMIGCOMP','POPCOMP','YEAR','MONTH','DATE');  

      list($currentYear, $currentMonth, $currentDay) = explode('-', date('Y-n-j'));
      //Tomorrow's date
      list($tomorrowYear, $tomorrowMonth, $tomorrowDay) = explode('-', date('Y-n-j', mktime(0, 0, 0, date("m"), date("d")+1, date("Y"))));

      if(($currentYear  == $year) &&  
         ($currentMonth == $month) &&  
         ($currentDay  == $day))    {
            $fetchToday = true;
      }
      if(($tomorrowYear  == $year) &&  
         ($tomorrowMonth == $month) &&  
         ($tomorrowDay  == $day))    {
            $fetchTomorrow = true;
      }

//NOTE: The DATA parameter was changed on 12/2018 to DATE_CODE
       //$url = 'https://api.census.gov/data/restricted/pep/daily';
        // $url = 'https://data.er.ditd.census.gov/data/restricted/pep/daily';

      if(API_MODE == 'ER') {
         $url = 'https://data.er.ditd.census.gov/data/restricted/pep/daily';
      } elseif (API_MODE == 'FR') {
         $url = 'https://data.fr.ditd.census.gov/data/restricted/pep/daily';
      } else {
         $url = 'https://api.census.gov/data/restricted/pep/daily';
      } 


       $paramString = 'get=MONTH,DATE_CODE,EDTMIDNIGHT,ESTMIDNIGHT,BIRTHCOMP,DEATHCOMP,TOTMIGCOMP,POPCOMP&YEAR='.$year.'&MONTH='.$month.'&DATE_CODE='.$day.'&key='.$key;
       $paramStringToday = 'get=MONTH,DATE_CODE,EDTMIDNIGHT,ESTMIDNIGHT,BIRTHCOMP,DEATHCOMP,TOTMIGCOMP,POPCOMP&YEAR='.$year.'&MONTH='.$month.'&DATE_CODE='.$day.'&key='.$key;
       $paramStringTomorrow = 'get=MONTH,DATE_CODE,EDTMIDNIGHT,ESTMIDNIGHT,BIRTHCOMP,DEATHCOMP,TOTMIGCOMP,POPCOMP&YEAR='.$tomorrowYear.'&MONTH='.$tomorrowMonth.'&DATE_CODE='.$tomorrowDay.'&key='.$key;

//var_dump($url);
//var_dump($paramString);

     /* The PEP API has proven to be unreliable, so it will be used mainly
      * as a means of updating the data file as infrequently as possible.
      * The data file will be the primary data source.
      */

     /* If the request is for the odometer and the data file is current 
      * and valid, use it as the data source.
      */
      if ($fetchToday || $fetchTomorrow) {
         $cachedFile   = $cacheAPI->getFileAsArray($file); 
         //echo "dump of the daily_PEP.json file <br/>";
         //var_dump($cachedFile);

         if ($cacheAPI->validateUSPEPPopData($cachedFile)) {
            if ($fetchToday) {
               //echo "fetchToday<br/>";
               array_push($results, $cachedFile[2], $cachedFile[3], $cachedFile[4], $cachedFile[5], $cachedFile[6]);
            }
 
            if ($fetchTomorrow) {
               //echo "fetchTomorrow<br/>";
               array_push($results, $cachedFile[13], $cachedFile[14], $cachedFile[15], $cachedFile[16], $cachedFile[17]);
            }
            //echo "return results <br/>";
            //var_dump($results); 

         //Block below is called when the cached file fails validation
         } else {
            echo "failed validation <br/>";
            /* This path is reached if the data file failed validation. 
             * That could be a result of there not being a file (new install),
             * the file could be out of date, or the file contents could be                  * invalid. 
             */
                
            //Call the APIs, once for today, again for tomorrow.
            $popDataToday    = $pop_api->getCurlData($url,$paramStringToday); 
            $popDataTomorrow = $pop_api->getCurlData($url,$paramStringTomorrow); 
            //Combine the API results into one array 
            $popCombined  = array_merge($popDataToday[1], $popDataTomorrow[1]);
  
             var_dump($popCombined);           
            if($cacheAPI->validateUSPEPPopData($popCombined)) {
               //echo "API data passed validation, encoding file <br/>";
               //Store the validated API data as a file. 
               $encode = json_encode($popCombined); 
               //echo "Encoded file <br/>";
               //var_dump($encode);

               //Write the validated API contents to a file
	       file_put_contents($file, $encode, LOCK_EX);
              
               //Still need to assign the correct API values to the return array
               if ($fetchToday) {
                   array_push($results, $popCombined[2], $popCombined[3], $popCombined[4], $popCombined[5], $popCombined[6]);
                }
                if ($fetchTomorrow) {
                   array_push($results, $popCombined[13], $popCombined[14], $popCombined[15], $popCombined[16], $popCombined[17]);
                }
             } 
         }
     /* The block below would be called for a 'Pop on date' request, which will
      * always use the API as the only data source. 
      */
      } else {
         $popData  = $pop_api->getCurlData($url,$paramString); 

         /* If the PEP API is down, use today's EDT pop number and date
          * from the cache file.
          */
         if (empty($popData)) {
            if ($cacheAPI->validateUSPEPPopData($cachedFile)) {
               $cachedFile   = $cacheAPI->getFileAsArray($file); 
            }
            //Get number for today
            array_push($results, $cachedFile[2], $cachedFile[3], $cachedFile[4], $cachedFile[5], $cachedFile[6]);
         } else {
            array_push($results, $popData[1][2], $popData[1][3], $popData[1][4], $popData[1][5], $popData[1][6]);
         }
      }
      return array($results[0],$results[1],$results[2],$results[3],$results[4]);
   } 





   /*
    * Return a 2D array of objects with the population for each of the four 
    * regions for each year within the given date range. All values 
    * are expected to be based on mid-year population estimates (july 1st).
    */
   public function getRegionalDateRange($startYear, $endYear) { 

       $yearInt = intval($startYear);

       //The key is defined in includes/common.php
       $key = KEY; 
       $popApi = new api();
       $cacheAPI = new cacheAPI();

       $regionFile   = 'region_PEP.json';
       $startYearInt = $startYear;
       $endYearInt   = $endYear;
       $allYearsArr  = array();
       $regionArr    = array(); 
       $allRegionsArr = array();
       $numYears = intval($endYear - $startYear)+1;
       $dateCodes;

       // Break the years down into a set of years and calculate the 
       // code, then store in an array.
       /* No date codes are needed here. The API was changed such that 
          each year has its own variable (eg. POP_2021).
        */
/*
       for ($i = 1; $i <= $numYears; $i++) {
            //Turn the year into a code.
            $yearSplit =  str_split($yearInt);
            $code = ($yearSplit[0]+$yearSplit[1]+$yearSplit[2]+$yearSplit[3]); 
            $regionArr    = array($yearInt, $code); 
            $allYearsArr[]  = $regionArr;
            $yearInt++;
            if ($i == $numYears)  {
                $dateCodes .= $code;            
            } else {
                $dateCodes .= $code.",";            
            }
       }
*/

      if(API_MODE == 'ER') {
         $url = "https://data.er.ditd.census.gov/data/".$endYear."/pep/population";
      } elseif (API_MODE == 'FR') {
         $url = "https://data.fr.ditd.census.gov/data/".$endYear."/pep/population";
      } else {
         $url = "https://api.census.gov/data/".$endYear."/pep/population";
      }

       //$url = "https://api.census.gov/data/".$endYear."/pep/population";
       //$url = "https://data.er.ditd.census.gov/data/".$endYear."/pep/population";
       //$url = "https://data.fr.ditd.census.gov/data/".$endYear."/pep/population";
       //$paramString = "get=POP,REGION,DATE_DESC&for=region:*&DATE_CODE=".$dateCodes."&key=".$key;
       /* 12/15/2021: The variables changed. */ 
       $paramString = "get=NAME,POP_2020,POP_2021,POP_2022,POP_2023&for=region:*&key=".$key;

//var_dump($url.'?'.$paramString);

        /* Use the cache file as the primary data source. If the file is
         * missing or invalid, call the API, and refresh the file. The data
         * in the file is stored as a string.
         */

         $cacheData = $cacheAPI->getFile($regionFile);

         // Decoding in this case is transforming a string to an array
         $decodedFile = json_decode($cacheData);
         
         if ($cacheAPI->validateRegionCache($regionFile, $decodedFile)) {
            $results = $decodedFile;
         } else {
//echo "validateRegionCache returned false<br/> ";
            //Get the data from the API
            $apiData = $popApi->getCurlData($url,$paramString);
            $encodedData  = json_encode($apiData);

            //Validate the API data and rebuild the cache file
            if ($cacheAPI->validateRegionAPI($apiData)) {
               $filePutResults = file_put_contents($regionFile, $encodedData, LOCK_EX);
               $results = $apiData;
            }
         }
/*
var_dump($url);
var_dump($paramString);
var_dump($apiData);
var_dump($encodedData);
 */    //Used for sorting by year, in descending order
       $sort_y = array();

//var_dump($results);
     /*  12/12/2022: Hardcode the data in this version. Do this by replacing
      * the '$results' variable above. 
      */
    //  $results = array(array('NAME', 'POP_2020','POP_2021','POP_2022','region'),      array('Northeast Region','57448898','57259257','57040406','1'), array('Midwest Region','68961043','68836505','68787595','2'), array('South Region','126450613','127346029','128716192','3'), array('West Region','78650958','78589763','78743364','4'));

     /* 12/7/2023: Hardcode the data, again. 
      */
      //$results = array(array('NAME', 'POP_2020','POP_2021','POP_2022','POP_2023','POP_2024','region'),array('Northeast Region','57430477','57243423','57026847','56983517','1'), array('Midwest Region','68969794','68850246','68783028','68909283','2'), array('South Region','126465281','127353282','128702030','130125290','3'), array('West Region','78661381','78602026','78759506','78896805','4'));

//echo "hardcoded..<br/>"; 
//var_dump($results);

      $results = array(array('NAME', 'POP_2020','POP_2021','POP_2022','POP_2023','POP_2024','POP_2025','region'), array('Northeast Region','57436027','57234503','57174375','57458539','57940522','58042054','1'), array('Midwest Region','68979566','68867096','68872112','69132050','69518281','69762666','2'), array('South Region','126473371','127380165','129066102','130894372','132662072','133833983','3'), array('West Region','78689140','78618402','78883715','79270091','79882922','80146154','4'));



       //Loop through the four regions for one year
       foreach ($results as $res) {
//var_dump($res);
           if ($res[0] != 'NAME')  {
/*
               if ($res[3] == "1") $regionName = "Northeast";
               if ($res[3] == "2") $regionName = "Midwest";
               if ($res[3] == "3") $regionName = "South";
               if ($res[3] == "4") $regionName = "West";
*/
/*
               if ($res[4] == "1") $regionName = "Northeast";
               if ($res[4] == "2") $regionName = "Midwest";
               if ($res[4] == "3") $regionName = "South";
               if ($res[4] == "4") $regionName = "West";

               if ($res[5] == "1") $regionName = "Northeast";
               if ($res[5] == "2") $regionName = "Midwest";
               if ($res[5] == "3") $regionName = "South";
               if ($res[5] == "4") $regionName = "West";

               if ($res[6] == "1") $regionName = "Northeast";
               if ($res[6] == "2") $regionName = "Midwest";
               if ($res[6] == "3") $regionName = "South";
               if ($res[6] == "4") $regionName = "West";
 */
               
               if ($res[7] == "1") $regionName = "Northeast";
               if ($res[7] == "2") $regionName = "Midwest";
               if ($res[7] == "3") $regionName = "South";
               if ($res[7] == "4") $regionName = "West";

               $sort_y[] = "2020";
               $sort_y[] = "2021";
               $sort_y[] = "2022";
               $sort_y[] = "2023";
               $sort_y[] = "2024";
               $sort_y[] = "2025";

               //Populate the regional record.
               $region2020 = array("year" =>"2020",                                                                "month" => "7",
                                   "day"      => "1", 
                                   "geoid"    => "00",
                                   "region"   =>  $res[3],
                                   "division" => "0",
                                   "value"    => $res[1],
                                   "edtvalue" => null,
                                   "region_name" => $regionName,
               );
               $region2021 = array("year" =>"2021",                                                                "month" => "7",
                                   "day"      => "1", 
                                   "geoid"    => "00",
                                   "region"   =>  $res[3],
                                   "division" => "0",
                                   "value"    => $res[2],
                                   "edtvalue" => null,
                                   "region_name" => $regionName,
               );

               $region2022 = array("year" =>"2022",                                                                "month" => "7",
                                   "day"      => "1", 
                                   "geoid"    => "00",
                                   "region"   =>  $res[3],
                                   "division" => "0",
                                   "value"    => $res[3],
                                   "edtvalue" => null,
                                   "region_name" => $regionName,
               );

               $region2023 = array("year" =>"2023",                                                                "month" => "7",
                                   "day"      => "1", 
                                   "geoid"    => "00",
                                   "region"   =>  $res[3],
                                   "division" => "0",
                                   "value"    => $res[4],
                                   "edtvalue" => null,
                                   "region_name" => $regionName,
               );

               $region2024 = array("year" =>"2024",                                                                "month" => "7",
                                   "day"      => "1", 
                                   "geoid"    => "00",
                                   "region"   =>  $res[3],
                                   "division" => "0",
                                   "value"    => $res[5],
                                   "edtvalue" => null,
                                   "region_name" => $regionName,
               );
               
               $region2025 = array("year" =>"2025",                                                                "month" => "7",
                                   "day"      => "1",
                                   "geoid"    => "00",
                                   "region"   =>  $res[3],
                                   "division" => "0",
                                   "value"    => $res[6],
                                   "edtvalue" => null,
                                   "region_name" => $regionName,
               );



               // The calling method expects an array of objects.
               $allRegionsArr[]  =   (object)$region2020;
               $allRegionsArr[]  =   (object)$region2021;
               $allRegionsArr[]  =   (object)$region2022;
               $allRegionsArr[]  =   (object)$region2023;
               $allRegionsArr[]  =   (object)$region2024;
               $allRegionsArr[]  =   (object)$region2025;
           }
       }

//var_dump($allRegionsArr);
//var_dump($sort_y);
       //Sort the results by year
       array_multisort($allRegionsArr, SORT_DESC, $sort_y, SORT_NUMERIC);
       return $allRegionsArr;
   } 



   /* Call the PEP CHARAGE API to get the population across age and 
    * gender.
    */
   public function getDemographicDateRangeAgeGender($startYear, $endYear) { 

//echo "startYear = ".$startYear."<br/>";
//echo "endYear = ".$endYear."<br/>";

       $file    = 'pyramid.json';
       $yearInt = intval($startYear);

       //The key is defined in includes/common.php
       $key = KEY; 
       $popApi = new api();
       $cacheAPI = new cacheAPI();

       $startYearInt = $startYear;
       $endYearInt   = $endYear;
       $allYearsArr  = array();
       $regionArr    = array(); 
       $allRegionsArr = array();
       $totDemoArr    = array();
       $numYears = intval($endYear - $startYear)+1;
       $dateCodes;

       for ($i = 1; $i <= $numYears; $i++) {
            //Turn the year into a code, then increment
            $yStr = $startYearInt++; 
            $code = self::dateYearToCode($yStr); 
            if ($i == $numYears)  {
                $dateCodes .= $code;            
            } else {
                $dateCodes .= $code.",";            
            }
       }

//var_dump($dateCodes);
      if(API_MODE == 'ER') {
         $url = "https://data.er.ditd.census.gov/data/".$endYear."/pep/charage";
      } elseif (API_MODE == 'FR') {
         $url = "https://data.fr.ditd.census.gov/data/".$endYear."/pep/charage";
      } else {
         $url = "https://api.census.gov/data/".$endYear."/pep/charage";
      }

     //$url = "https://data.er.ditd.census.gov/data/".$endYear."/pep/charage";
        /* The pyramid graph data is updated in April. */   
        //$url = "https://api.census.gov/data/".$endYear."/pep/charage";
       $paramString = "get=AGE,POP&for=us:*&SEX=1,2&DATE_CODE=".$dateCodes."&key=".$key;

//var_dump($url.'?'.$paramString);

     /* Use the cache file as the primary data source. If the file is 
      * missing or invalid, call the API, and refresh the file. The data 
      * in the file is stored as a string.
      */
      $cacheData = $cacheAPI->getPyramidFile($file);

      // Decoding in this case is transforming a string to an array
      $decodedFile = json_decode($cacheData);

      if ($cacheAPI->validatePyramidCache($file, $decodedFile)) {
         $results = $decodedFile;
         //echo "cache file passed validation <br/>";
      } else {
         //echo "cache file failed validation <br/>";
         //Get the data from the API
         $apiData = $popApi->getCurlData($url,$paramString); 
         $encodedData  = json_encode($apiData);

         //Validate the API data and rebuild the cache file
         if ($cacheAPI->validatePyramidAPI($apiData)) {
            //echo "refreshing cache with API data <br/>";
            $filePutResults = file_put_contents($file, $encodedData, LOCK_EX);
            $results = $apiData; 
         }
      }
       
      $results = $popApi->getCurlData($url,$paramString); 
      $results = json_decode($cacheData); 
      $results = $decodedFile;

       //Used for sorting by year, in descending order
       $sort_y = array();

       //Loop through the four regions for one year
       foreach ($results as $res) {
           //Two records per age per year
           if (($res[1] != 'POP') && ($res[0] != '999')) {
               //Temporary array to store age
               $sort_age[] = (string)$res[0];

               $year = self::dateCodeToYear($res[3]);

               //Temporary array to store year
               $sort_y[] = $year; 
               
               //Populate the age record.
               $agePop = array("year"    => $year,
                               "month"   => "7",
                               "day"     => "1", 
                               "age"     => $res[0], 
                               "gender"  => $res[2],
                               "value"   => $res[1]
               );
               // The calling method expects an array of objects.
              $allRegionsArr[]  =   (object)$agePop;

           //One record per gender per year with total population
           } elseif (($res[1] != 'POP') && ($res[0] == '999')) {

               $totYear = self::dateCodeToYear($res[3]);

               //Temporary array to store year
               $totSortYear[] = $totYear; 
               
               //Temporary array to store gender
               $totSortGender[] = $res[2];  
               
               //Populate the age record.
               $totPop = array("year"    => $totYear,
                               "month"   => "7",
                               "day"     => "1", 
                               "age"     => $res[0], 
                               "gender"  => $res[2],
                               "value"   => $res[1]
               );
               // The calling method expects an array of objects.
              $totDemoArr[]  =   (object)$totPop;
           } else {
           }
       }

       //Sort the results by year DESC, then gender DESC 
       array_multisort($totSortYear, SORT_DESC, $totSortGender, SORT_DESC, $totDemoArr);

       //Sort the results by year DESC, then age ASC
       array_multisort($sort_y, SORT_DESC, $sort_age, SORT_ASC, $allRegionsArr);
       return array($allRegionsArr, $totDemoArr);
   } 


   /* Get the  top ten most populous states, counties, or cities. This
    * method is called for one year.
    */
   public function getRankedPopulation($geography, $year, $sort) { 

      $yearInt = intval($year);

      //The key is defined in includes/common.php
      $key = KEY; 
      $popApi = new api();
      $cacheAPI = new cacheAPI();
      $countyFile = 'county_PEP.json';
      //$cityFile   = 'city_PEP.json';
      $cityFile   = 'city.json';
      $stateFile  = 'state_PEP.json';

      $popCountyArr = array();
      $popCityArr = array();
      $popStateArr = array();
      $result     = array();

      //Get the top 10 most populous counties
      if (strtoupper($geography) == 'COUNTY')  {
         $yearCode   = self::dateYearToCode(US_POP_COUNTY_VINTAGE);
         $fullDate   = $year."/07/01";
         $pr_date = (string)strtotime($fullDate);


      
      if(API_MODE == 'ER') {
         $url = "https://data.er.ditd.census.gov/data/".US_POP_COUNTY_VINTAGE."/pep/population"; 
      } elseif (API_MODE == 'FR') {
         $url = "https://data.fr.ditd.census.gov/data/".US_POP_COUNTY_VINTAGE."/pep/population"; 
      } else {
         $url = "https://api.census.gov/data/".US_POP_COUNTY_VINTAGE."/pep/population";
      }

     //$url = "https://data.fr.ditd.census.gov/data/".US_POP_COUNTY_VINTAGE."/pep/population"; 
     //$url = "https://data.er.ditd.census.gov/data/".US_POP_COUNTY_VINTAGE."/pep/population"; 
         //$url = "https://api.census.gov/data/".US_POP_COUNTY_VINTAGE."/pep/population";

     //$paramString = "get=POP,DENSITY,GEONAME&for=county:*&DATE_CODE=".$yearCode."&key=".$key;
         $paramString = "get=POP,DENSITY,NAME&for=county:*&DATE_CODE=".$yearCode."&key=".$key;

//var_dump($url)
//var_dump($paramString);


        /* Use the cache file as the primary data source. If the file is
         * missing or invalid, call the API, and refresh the file. The data
         * in the file is stored as a string.
         */
         $cacheData = $cacheAPI->getFile($countyFile);

         // Decoding in this case is transforming a string to an array
         $decodedFile = json_decode($cacheData);
         
         if ($cacheAPI->validateCountyCache($countyFile, $decodedFile)) {
            $results = $decodedFile;
         } else {
            //Get the data from the API
            $apiData = $popApi->getCurlData($url,$paramString);
            $encodedData  = json_encode($apiData);

            //Validate the API data and rebuild the cache file
            if ($cacheAPI->validateCountyAPI($apiData)) {
               $filePutResults = file_put_contents($countyFile, $encodedData, LOCK_EX);
               $results = $apiData;
            }
         }

         $sort_y = array();
         $sort_d = array();
         foreach ($results as $res) {
//var_dump($res);
            if ($res[0] != 'POP') {
              /* 3/2/2022: This version requires that we hardcode the 
               * population and  density values for the counties. 
               */
               $geoid = $res[4].$res[5];
               $state = self::FIPStoStateAbbr($res[4]) ;
               $lpos  = strrpos($res[2], ",");
               $name  = substr($res[2],0, $lpos);
               $density  = (string)round($res[1],1);
               //Utility array used for sorting by population
               //$sort_y[] = $res[0];
               //$sort_d[] = $density;

               //Populate the age record.
               $popCounty  = array("pr_date"    => $pr_date,
                                   "geoid"      => $geoid,
                                   "population" => $res[0], 
                                   "density"    => $density, 
                                   "name"       => $name,
                                   "state"      => $state
               );

              /* 3/2/2022: This version requires that we hardcode the 
               * population and  density values for the counties. Intercept
               * the 'Top 10' here and inject the hardcoded values. 
               *
               * 2/27/2023: Again hardcoding the COUNTY level population and 
               * density numbers.
               */
               if($sort == 'POPULATION') { 
                  if ($geoid == '06037') { 
                     $popCounty["population"] = '9757179';
                     $popCounty["density"] = '2403.1';
                  } elseif ($geoid == '17031') { 
                     $popCounty["population"] = '5182617';
                     $popCounty["density"] = '5484.7';
                  } elseif ($geoid == '48201') { 
                     $popCounty["population"] = '5009302';
                     $popCounty["density"] = '2934.1';
                  } elseif ($geoid == '04013') { 
                     $popCounty["population"] = '4673096';
                     $popCounty["density"] = '507.8';
                  } elseif ($geoid == '06073') { 
                     $popCounty["population"] = '3298799';
                     $popCounty["density"] = '783.5';
                  } elseif ($geoid == '06059')   { 
                     $popCounty["population"] = '3170435';
                     $popCounty["density"] = '3998.7';
                  } elseif ($geoid == '12086')   { 
                     $popCounty["population"] = '2838461';
                     $popCounty["density"] = '1494.0';
                  } elseif ($geoid == '48113')   { 
                     $popCounty["population"] = '2656028';
                     $popCounty["density"] = '3041.9';
                  } elseif ($geoid == '36047')   { 
                     $popCounty["population"] = '2617631';
                     $popCounty["density"] = '37730.8';
                  } elseif ($geoid == '06065')   { 
                     $popCounty["population"] = '2529933';
                     $popCounty["density"] = '350.9';
                  } else {}
               } //Population county-level

               if($sort == 'DENSITY') { 
                  if ($geoid == '36061') { 
                     $popCounty["population"] = '1660664';
                     $popCounty["density"] = '73292.7';
                  } elseif ($geoid == '36047') { 
                     $popCounty["population"] = '2617631';
                     $popCounty["density"] = '37730.8';
                  } elseif ($geoid == '36005') { 
                     $popCounty["population"] = '1384724';
                     $popCounty["density"] = '32831.9';
                  } elseif ($geoid == '36081') { 
                     $popCounty["population"] = '2316841';
                     $popCounty["density"] = '21309.4';
                  } elseif ($geoid == '06075') { 
                     $popCounty["population"] = '827526';
                     $popCounty["density"] = '17725.7';
                  } elseif ($geoid == '34017') { 
                     $popCounty["population"] = '736185';
                     $popCounty["density"] = '15937.3';
                  } elseif ($geoid == '25025') { 
                     $popCounty["population"] = '793144';
                     $popCounty["density"] = '13614.5';
                  } elseif ($geoid == '42101') { 
                     $popCounty["population"] = '1573916';
                     $popCounty["density"] = '11717.2';
                  } elseif ($geoid == '11001') { 
                     $popCounty["population"] = '702250';
                     $popCounty["density"] = '11488.5';
                  } elseif ($geoid == '51510') { 
                     $popCounty["population"] = '159102';
                     $popCounty["density"] = '10652.7';
                  } else {}
               } //Population county-level


               $sort_y[] = $popCounty["population"];
               $sort_d[] = $popCounty["density"];

               // The calling method expects an array of objects.
               $popCountyArr[] = (object)$popCounty;
           }           
         }
         if ($sort == 'DENSITY') {
            array_multisort($sort_d, SORT_DESC, $popCountyArr);
         } else {
            array_multisort($sort_y, SORT_DESC, $popCountyArr);
         }
         //array_multisort($sort_y, SORT_DESC, $popCountyArr);
         $popCountyArr = array_slice($popCountyArr,0,10);
//var_dump($popCountyArr);
/* 3/2/2022: This version requires that we hardcode the population and 
 *           density values for the counties. 
 */
           //$popCountyArr[0]['population'] = '9829544'; 



         $result = $popCountyArr;
      }

      if (strtoupper($geography) == 'CITY')  {
         $yearCode   = self::dateYearToCode(US_POP_CITY_VINTAGE);
         $fullDate   = $year."/07/01";
         $pr_date = (string)strtotime($fullDate);

         if(API_MODE == 'ER') {
            $url = "https://data.er.ditd.census.gov/data/".US_POP_CITY_VINTAGE."/pep/population"; 
         } elseif (API_MODE == 'FR') {
            $url = "https://data.fr.ditd.census.gov/data/".US_POP_CITY_VINTAGE."/pep/population"; 
         } else {
             $url = "https://api.census.gov/data/".US_POP_CITY_VINTAGE."/pep/population";
         }

//   $url = "https://data.fr.ditd.census.gov/data/".US_POP_CITY_VINTAGE."/pep/population"; 
   //$url = "https://data.er.ditd.census.gov/data/".US_POP_CITY_VINTAGE."/pep/population"; 
   //        $url = "https://api.census.gov/data/".US_POP_CITY_VINTAGE."/pep/population";
//2018 API will use DATE_CODE instead of DATE
//2019 API should use NAME instead of GEONAME
            $paramString = "get=POP,DENSITY,NAME&for=place:*&DATE_CODE=".$yearCode."&key=".$key;
    //      $paramString = "get=POP,DENSITY,GEONAME&for=place:*&DATE=".$yearCode."&key=".$key;

        /* Use the cache file as the primary data source. If the file is
         * missing or invalid, call the API, and refresh the file. The data
         * in the file is stored as a string.
         */
         $cacheData = $cacheAPI->getFile($cityFile);

         // Decoding in this case is transforming a string to an array
         $decodedFile = json_decode($cacheData);
 
         if ($cacheAPI->validateCityCache($cityFile, $decodedFile)) {
            $results = $decodedFile;
//echo "city cache good <br/>";
         } else {
//echo "city cache failed <br/>";
            //Get the data from the API
            $apiData = $popApi->getCurlData($url,$paramString);

//var_dump($url.'?'.$paramString);

            $encodedData  = json_encode($apiData);

            //Validate the API data and rebuild the cache file
            if ($cacheAPI->validateCityAPI($apiData)) {
//echo "city API  good<br/>";
               $filePutResults = file_put_contents($cityFile, $encodedData, LOCK_EX);
               $results = $apiData;
            }
         }
//print_r($results);
           //$results = $popApi->getCurlData($url,$paramString); 

           //$results = $popApi->getCurlData($url,$paramString); 
           $sort_y = array();
           $sort_d = array();
           foreach ($results as $res) {
              if ($res[0] != 'POP') {

                 $geoid = $res[4].$res[5];

//var_dump($res);
                 /* Limit this section to the hardcoded top 10 cities.*/
                 if(($geoid == '3651000') || 
                    ($geoid == '1235000') ||
                    ($geoid == '0644000') ||
                    ($geoid == '3651000') || 
                    ($geoid == '0644000') || 
                    ($geoid == '1714000') ||
                    ($geoid == '4835000') ||
                    ($geoid == '0455000') || 
                    ($geoid == '4260000') || 
                    ($geoid == '4865000') ||
                    ($geoid == '0666000') || 
                    ($geoid == '4819000') ||
                    ($geoid == '4805000') ||
                    ($geoid == '3428650') ||
                    ($geoid == '3479610') ||
                    ($geoid == '3474630') ||
                    ($geoid == '3432250') ||
                    ($geoid == '3638934') ||
                    ($geoid == '3413570') ||
                    ($geoid == '3639853') ||
                    ($geoid == '3650705') ||
                    ($geoid == '3630213')) {


                    $state = self::FIPStoStateAbbr($res[4]) ;
                    $lpos  = strrpos($res[2], ",");
                    $name  = substr($res[2],0, $lpos);
                    $density  = (string)round($res[1],1);

                    //Utility array used for sorting by population
                    //$sort_y[] = $res[0];
                    //$sort_d[] = $density;

                    //Populate the age record.
                    $popCity  = array("pr_date"    => $pr_date,
                                      "geoid"      => $geoid,
                                      "population" => $res[0], 
                                      "density"    => $density, 
                                      "name"       => $name,
                                      "state"      => $state
                    );

                    if($geoid == '3651000')  {
                       $popCity["population"] = '8478072'; 
                       $popCity["density"]    = '28217.3'; 
                    } elseif ($geoid == '0644000') {
                       $popCity["population"] = '3878704'; 
                       $popCity["density"]    = '8243.4'; 
                    } elseif ($geoid == '1714000') {
                       $popCity["population"] = '2721308'; 
                       $popCity["density"]    = '11948.8'; 
                    } elseif ($geoid == '4835000') {
                       $popCity["population"] = '2390125'; 
                       $popCity["density"]    = '3730.2'; 
                    } elseif ($geoid == '0455000') {
                       $popCity["population"] = '1673164'; 
                       $popCity["density"]    = '3228.0'; 
                    } elseif ($geoid == '4260000') {
                       $popCity["population"] = '1573916'; 
                       $popCity["density"]    = '11717.2'; 
                    } elseif ($geoid == '4865000') {
                       $popCity["population"] = '1526656'; 
                       $popCity["density"]    = '3059.7'; 
                    } elseif ($geoid == '0666000') {
                       $popCity["population"] = '1404452'; 
                       $popCity["density"]    = '4306.9'; 
                    } elseif ($geoid == '4819000') {
                       $popCity["population"] = '1326087'; 
                       $popCity["density"]    = '3903.9'; 
                    } elseif ($geoid == '1235000') {
                       $popCity["population"] = '1009833'; 
                       $popCity["density"]    = '1351.4'; 
                    } elseif ($geoid == '4805000') {
                       $popCity["population"] = '974447'; 
                       $popCity["density"]    = '3039.8'; 
                    } elseif ($geoid == '3428650') {
                       $popCity["population"] = '11945'; 
                       $popCity["density"]    = '61891.2'; 
                    } elseif ($geoid == '3479610') {
                       $popCity["population"] = '52975'; 
                       $popCity["density"]    = '53348.4'; 
                    } elseif ($geoid == '3474630') {
                       $popCity["population"] = '66918'; 
                       $popCity["density"]    = '51995.3'; 
                    } elseif ($geoid == '3432250') {
                       $popCity["population"] = '59149'; 
                       $popCity["density"]    = '47319.2.0'; 
                    } elseif ($geoid == '3638934') {
                       $popCity["population"] = '5970'; 
                       $popCity["density"]    = '34709.3'; 
                    } elseif ($geoid == '3413570') {
                       $popCity["population"] = '26183'; 
                       $popCity["density"]    = '27388.1'; 
                    } elseif ($geoid == '3639853') {
                       $popCity["population"] = '43863'; 
                       $popCity["density"]    = '30002.1'; 
                    } elseif ($geoid == '3650705') {
                       $popCity["population"] = '9973'; 
                       $popCity["density"]    = '27174.4'; 
                    } elseif ($geoid == '3630213') {
                       $popCity["population"] = '7824'; 
                       $popCity["density"]    = '25157.6'; 
                    } else {} 
                 
                //  var_dump($res); 
                    $sort_y[] = $popCity["population"]; 
                    $sort_d[] = $popCity["density"]; 

                   // The calling method expects an array of objects.
                    $popCityArr[]  =   (object)$popCity;
                  } 
                  /* More hardcoding. These entities are not found in the 
                   * for loop above.
                   */
                   
               }
            }
           if ($sort == 'DENSITY') {
               array_multisort($sort_d, SORT_DESC, $popCityArr);
           } else {
               array_multisort($sort_y, SORT_DESC, $popCityArr);
           }
           $popCityArr = array_slice($popCityArr,0,10);
          $result = $popCityArr;


       } /* End of CITY */


      if (strtoupper($geography) == 'STATE')  {
          //$yearCode   = self::dateYearToCode(US_POP_STATE_VINTAGE);
         $yearCode   = self::dateYearToCode("2022");
         $fullDate   = $year."/07/01";
         $pr_date = (string)strtotime($fullDate);

         if(API_MODE == 'ER') {
            $url = "https://data.er.ditd.census.gov/data/".US_POP_STATE_VINTAGE."/pep/population";
         } elseif (API_MODE == 'FR') {
            $url = "https://data.fr.ditd.census.gov/data/".US_POP_STATE_VINTAGE."/pep/population";
         } else {
            $url = "https://api.census.gov/data/".US_POP_STATE_VINTAGE."/pep/population";
         }
       //$url = "https://api.census.gov/data/".US_POP_STATE_VINTAGE."/pep/population";
        //   $url = "https://data.er.ditd.census.gov/data/".US_POP_STATE_VINTAGE."/pep/population";
           //$url = "https://data.fr.ditd.census.gov/data/".US_POP_STATE_VINTAGE."/pep/population";
          /* 12/15/2021: New API call, different variable names. 
             https://data.er.ditd.census.gov/data/2021/pep/population?get=NAME,POP_2021,DENSITY_2021&for=state:*
          */
          $paramString = "get=POP_2021,DENSITY_2021,NAME&for=state:*&key=".$key;
          //$paramString = "get=POP,DENSITY,NAME&for=state:*&DATE_CODE=".$yearCode."&key=".$key;

//var_dump(US_POP_STATE_VINTAGE);
//var_dump($url);
//var_dump($paramString);

        /* Use the cache file as the primary data source. If the file is
         * missing or invalid, call the API, and refresh the file. The data
         * in the file is stored as a string.
         */
         $cacheData = $cacheAPI->getFile($stateFile);
         // Decoding in this case is transforming a string to an array
         $decodedFile = json_decode($cacheData);
 
         if ($cacheAPI->validateStateCache($stateFile, $decodedFile)) {
            $results = $decodedFile;
         } else {

            //Get the data from the API
            $apiData = $popApi->getCurlData($url,$paramString);
            $encodedData  = json_encode($apiData);

            //Validate the API data and rebuild the cache file
            if ($cacheAPI->validateStateAPI($apiData)) {
               $filePutResults = file_put_contents($stateFile, $encodedData, LOCK_EX);
               $results = $apiData;
            }
         }


           $sort_y = array();
           $sort_d = array();

           foreach ($results as $res) {
//print('res<br/>');
//var_dump($res);
               if ($res[0] != 'POP_2021') {

                   //$geoid = $res[4].$res[5];
                   $geoid = $yearCode.$res[3];
//var_dump($geoid);
                   $state = self::FIPStoStateAbbr($res[3]) ;
                   $lpos  = strrpos($res[2], ",");
                   $name  = $res[2];
                   $density  = (string)round($res[1],1);

                   //Utility array used for sorting by population
                  // $sort_y[] = $res[0];
                  // $sort_d[] = $res[1];


                   //Populate the age record.
                   $popState= array("pr_date"    => $pr_date,
                                       "geoid"      => $geoid,
                                       "population" => $res[0], 
                                       "density"    => $density, 
                                       "name"       => $name,
                                       "state"      => $state
                   );

                    if($geoid == '1506')  {
                       $popState["population"] = '39355309'; 
                       $popState["density"]    = '252.5'; 
                       $sort_y[] = $popState["population"];
                       $sort_d[] = $popState["density"]; 
                    } elseif ($geoid == '1548') {
                       $popState["population"] = '31709821'; 
                       $popState["density"]    = '121.4'; 
                       $sort_y[] = $popState["population"];
                       $sort_d[] = $popState["density"]; 
                    } elseif ($geoid == '1512') {
                       $popState["population"] = '23462518'; 
                       $popState["density"]    = '437.3'; 
                       $sort_y[] = $popState["population"];
                       $sort_d[] = $popState["density"]; 
                    } elseif ($geoid == '1536') {
                       $popState["population"] = '20002427'; 
                       $popState["density"]    = '424.5'; 
                       $sort_y[] = $popState["population"];
                       $sort_d[] = $popState["density"]; 
                    } elseif ($geoid == '1542') {
                       $popState["population"] = '13059432'; 
                       $popState["density"]    = '291.9'; 
                       $sort_y[] = $popState["population"];
                       $sort_d[] = $popState["density"]; 
                    } elseif ($geoid == '1517') {
                       $popState["population"] = '12719141'; 
                       $popState["density"]    = '229.1'; 
                       $sort_y[] = $popState["population"];
                       $sort_d[] = $popState["density"]; 
                    } elseif ($geoid == '1539') {
                       $popState["population"] = '11900510'; 
                       $popState["density"]    = '291.3'; 
                       $sort_y[] = $popState["population"];
                       $sort_d[] = $popState["density"]; 
                    } elseif ($geoid == '1513') {
                       $popState["population"] = '11302748'; 
                       $popState["density"]    = '195.8'; 
                       $sort_y[] = $popState["population"];
                       $sort_d[] = $popState["density"]; 
                    } elseif ($geoid == '1537') {
                       $popState["population"] = '11197968'; 
                       $popState["density"]    = '230.3'; 
                       $sort_y[] = $popState["population"];
                       $sort_d[] = $popState["density"]; 
                    } elseif ($geoid == '1526') {
                       $popState["population"] = '10127844'; 
                       $popState["density"]    = '178.9'; 
                       $sort_y[] = $popState["population"];
                       $sort_d[] = $popState["density"]; 
                    } elseif ($geoid == '1511') {
                       $popState["population"] = '693645'; 
                       $popState["density"]    = '11347.5'; 
                       $sort_y[] = $popState["population"];
                       $sort_d[] = $popState["density"]; 
                    } elseif ($geoid == '1534') {
                       $popState["population"] = '9548215'; 
                       $popState["density"]    = '1298.2'; 
                       $sort_y[] = $popState["population"];
                       $sort_d[] = $popState["density"]; 
                    } elseif ($geoid == '1544') {
                       $popState["population"] = '1114521'; 
                       $popState["density"]    = '1078.0'; 
                       $sort_y[] = $popState["population"];
                       $sort_d[] = $popState["density"]; 
                    } elseif ($geoid == '1525') {
                       $popState["population"] = '7154084'; 
                       $popState["density"]    = '917.1'; 
                       $sort_y[] = $popState["population"];
                       $sort_d[] = $popState["density"]; 
                    } elseif ($geoid == '1509') {
                       $popState["population"] = '3688496'; 
                       $popState["density"]    = '761.7'; 
                       $sort_y[] = $popState["population"];
                       $sort_d[] = $popState["density"]; 
                    } elseif ($geoid == '1524') {
                       $popState["population"] = '6265347'; 
                       $popState["density"]    = '645.2'; 
                       $sort_y[] = $popState["population"];
                       $sort_d[] = $popState["density"]; 
                    } elseif ($geoid == '1510') {
                       $popState["population"] = '1059952'; 
                       $popState["density"]    = '544.0'; 
                       $sort_y[] = $popState["population"];
                       $sort_d[] = $popState["density"]; 
                    } else { 
                       $sort_y[] = $res[0];
                       $sort_d[] = $res[1];
                    }

                   // The calling method expects an array of objects.
               $popStateArr[]  =   (object)$popState;
               }           
           }
//var_dump($sort);
//var_dump($sort_d);


           if ($sort == 'DENSITY') {
               array_multisort($sort_d, SORT_DESC, $popStateArr);
           } else {
               array_multisort($sort_y, SORT_DESC, $popStateArr);
           }
//var_dump($popStateArr);

           //array_multisort($sort_y, SORT_DESC, $popStateArr);
           $popStateArr = array_slice($popStateArr,0,10);
//print('popStateArr<br>');
//var_dump($popStateArr);
          $result = $popStateArr;
       }

    return $result;

   }   //end of getRankedPopulation




   private function dateCodeToYear($code) {
       $result = null;
      
           switch($code) {
               case '3': 
                   $result = '2010'; 
                   break;
               case '4': 
                   $result = '2011'; 
                   break;
               case '5': 
                   $result = '2012'; 
                   break;
               case '6': 
                   $result = '2013'; 
                   break;
               case '7': 
                   $result = '2014'; 
                   break;
               case '8': 
                   $result = '2015'; 
                   break;
               case '9': 
                   $result = '2016'; 
                   break;
               case '10': 
                   $result = '2017'; 
                   break;
               case '11': 
                   $result = '2018'; 
                   break;
               case '12': 
                   $result = '2019'; 
                   break;
               case '13': 
                   $result = '2020'; 
                   break;
               case '14': 
                   $result = '2021'; 
                   break;
               case '15': 
                   $result = '2022'; 
                   break;
               case '16': 
                   $result = '2023'; 
                   break;
               case '17': 
                   $result = '2024'; 
                   break;
               case '18':
                   $result = '2025';
                   break;
               default: 
                   $result = null;
                   break;
           }
       return $result;
   }

   private function dateYearToCode($year) {
       $result = null;
      
           switch($year) {
               case '2010': 
                   $result = '3'; 
                   break;
               case '2011': 
                   $result = '4'; 
                   break;
               case '2012': 
                   $result = '5'; 
                   break;
               case '2013': 
                   $result = '6'; 
                   break;
               case '2014': 
                   $result = '7'; 
                   break;
               case '2015': 
                   $result = '8'; 
                   break;
               case '2016': 
                   $result = '9'; 
                   break;
               case '2017': 
                   $result = '10'; 
                   break;
               case '2018': 
                   $result = '11'; 
                   break;
               case '2019': 
                   $result = '12'; 
                   break;
               case '2020': 
                   $result = '13'; 
                   break;
               case '2021': 
                   $result = '14'; 
                   break;
               case '2022': 
                   $result = '15'; 
                   break;
               case '2023': 
                   $result = '16'; 
                   break;
               case '2024': 
                   $result = '17'; 
                   break;
               case '2025':
                   $result = '18';
                   break;
               default: 
                   $result = null;
                   break;
           }
       return $result;
   }




   /* Convert a state FIPS code into a two-digit state abbreviation.
    */
   private function FIPStoStateAbbr($StateFIPS) {
       $result = null;

       switch($StateFIPS) {
           case '02': 
               $result = 'AK'; 
               break;
           case '01':
               $result = 'AL'; 
               break;
           case '04':
               $result = 'AZ'; 
               break;
           case '05':
               $result = 'AR'; 
               break;
           case '06':
               $result = 'CA'; 
               break;
           case '60':
               $result = 'AS'; 
               break;
           case '08':
               $result = 'CO'; 
               break;
           case '09':
               $result = 'CT'; 
               break;
           case '11':
               $result = 'DC'; 
               break;
           case '10':
               $result = 'DE'; 
               break;
           case '12':
               $result = 'FL'; 
               break;
           case '13':
               $result = 'GA'; 
               break;
           case '66':
               $result = 'GU'; 
               break;
           case '15':
               $result = 'HI'; 
               break;
           case '19':
               $result = 'IA'; 
               break;
           case '16':
               $result = 'ID'; 
               break;
           case '17':
               $result = 'IL'; 
               break;
           case '18':
               $result = 'IN'; 
               break;
           case '20':
               $result = 'KS'; 
               break;
           case '21':
               $result = 'KY'; 
               break;
           case '22':
               $result = 'LA'; 
               break;
           case '25':
               $result = 'MA'; 
               break;
           case '24':
               $result = 'MD'; 
               break;
           case '23':
               $result = 'ME'; 
               break;
           case '26':
               $result = 'MI'; 
               break;
           case '27':
               $result = 'MN'; 
               break;
           case '28':
               $result = 'MS'; 
               break;
           case '29':
               $result = 'MO'; 
               break;
           case '30':
               $result = 'MT'; 
               break;
           case '37':
               $result = 'NC'; 
               break;
           case '38':
               $result = 'ND'; 
               break;
           case '31':
               $result = 'NE'; 
               break;
           case '33':
               $result = 'NH'; 
               break;
           case '34':
               $result = 'NJ'; 
               break;
           case '35':
               $result = 'NM'; 
               break;
           case '32':
               $result = 'NV'; 
               break;
           case '36':
               $result = 'NY'; 
               break;
           case '39':
               $result = 'OH'; 
               break;
           case '40':
               $result = 'OK'; 
               break;
           case '41':
               $result = 'OR'; 
               break;
           case '42':
               $result = 'PA'; 
               break;
           case '72':
               $result = 'PR'; 
               break;
           case '44':
               $result = 'RI'; 
               break;
           case '45':
               $result = 'SC'; 
               break;
           case '46':
               $result = 'SD'; 
               break;
           case '47':
               $result = 'TN'; 
               break;
           case '48':
               $result = 'TX'; 
               break;
           case '49':
               $result = 'TN'; 
               break;
           case '51':
               $result = 'VA'; 
               break;
           case '78':
               $result = 'VI'; 
               break;
           case '50':
               $result = 'VT'; 
               break;
           case '53':
               $result = 'WA'; 
               break;
           case '55':
               $result = 'WI'; 
               break;
           case '54':
               $result = 'WV'; 
               break;
           case '56':
               $result = 'WY'; 
               break;
           default: 
              $result = null;
              break;
       }
       return $result;
   }





}//End Population Class Definition
