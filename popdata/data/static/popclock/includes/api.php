<?php
/**
 * This class provides the functionality to get the latest url from api
 *
 */
class api
{
    public $key;
    public $paramString;
		
    function __construct($key,$paramString) {
        if (isset($key)) {
            $this->key = $key;
		}
		if (isset($paramString)) {
		    $this->paramString = $paramString;
		}
		
	}	
	
	/**
	 * This method has the functionality to process curl request
	 *
	 */
	public function getCurlData($url,$paramString){
	    $URL = $url."?".$paramString;
           
		//$URL= preg_replace('#^https?://#', '', $URL);
		
		$httpCall = $URL;
		
		$noResponseMsg = "We were unable to retrieve your data in a timely manner.<br/>Please try again later.'";
		$curlFailMsg = "curl failed";
		
		$curlOptions = array(		       
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HTTPHEADER => array('Content-type: application/json')
		);
		
		if(($ch = curl_init())==false){
			//If initiation of cURL object fails, throw the Exception
			throw new Exception($curlFailMsg);
		}
		
		curl_setopt_array( $ch, $curlOptions );
		curl_setopt($ch, CURLOPT_SSLVERSION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, $httpCall);
		
		try{
			$values =  curl_exec($ch);
			//if there are no values in the cURL result throw an Exception indicating that wrong/no data was returned
			if(!$values || strlen(trim($values)) == 0)
			{
                                // The MITD apis might not return
                                // a value, unlike the previous apis (AITD),
                                // which would return zereos if there were
                                // no data but the call was valid.

				throw new Exception($noResponseMsg);
			}else{
				$data_arr = json_decode($values,true);
				
				
				
			}
			return $data_arr;
		}catch(Exception $e){
			echo 'Message: ' .$e->getMessage();
		}
		
		Curl_close($ch);
		
	}		
	
	
}

?>
