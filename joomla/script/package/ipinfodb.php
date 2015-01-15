<?php

class oneScriptPackageIpinfodb extends One_Script_Package {

  const IPINFODBAPIKEY = '3b1a1c320867dd9756989b3b02a814b3f54e849616eeed83746caf9c027bff89';

  const IPACTIVE = false;
  
  
  // Hold the API URL (should have trailing /)
  const IPINFODBAPIURL = 'http://api.ipinfodb.com/v3/';

  public static function getCurrency() {
    if (!self::IPACTIVE) return 'EUR';
    $cy = self::getCountry();
    switch ($cy) {
      case 'BE' :
      case 'DE' :
      case 'EE' :
      case 'EL' :
      case 'ES' :
      case 'FR' :
      case 'IT' :
      case 'CY' :
      case 'LV' :
      case 'LU' :
      case 'MT' :
      case 'NL' :
      case 'AT' :
      case 'PL' :
      case 'PT' :
      case 'SI' :
      case 'SK' :
      case 'FI' :
      case 'SE' :
      case 'IE' :
        return 'EUR';
        break;
      case 'GB' :
        return 'GBP';
        break;
      case 'CH' :
        return 'CHF';
        break;
      case 'US' :
      default: 
        return 'USD';
    }
  }
  

  
  
  public static function getCountry()
  {
    if (!self::IPACTIVE) return 'BE';

    $ip = self::getIPAddress();
    if ($ip === false) return false;
    
   $data =  self::execute($ip, 'ip-country');
   $d = json_decode($data);
   return $d->countryCode;
  }

  public static function getCity()
  {
    if (!self::IPACTIVE) return '';

    $ip = self::getIPAddress();
    if ($ip === false) return false;
    
    return self::execute($ip, 'ip-city');
  }

  /**
    * execute
    *
    * Makes the specified CURL request - this is the meat of the class!
    *
    * @param string $ip - The users IP address
    * @param string $endpoint - The API endpoint we wish to query
    *
    * @return string/bool - data if we have it, otherwise false
    *
    */
  private function execute($ip, $endpoint)
  {
    // Build the URL
    $url = self::IPINFODBAPIURL .'/'. $endpoint .'/?key='. self::IPINFODBAPIKEY .'&ip='. $ip .'&format=json';

    // Initialise CURL
    $handle = curl_init();

    // Set the CURL options we need
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);

    // Grab the data
    $data = curl_exec($handle);

    // Grab the CURL error code and message as well as the HTTP Code
    $errorCode = curl_errno($handle);
    $errorMessage = curl_error($handle);
    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

    // Close the CURL connection
    curl_close($handle);

    // Check that we got a good HTTP response code
    if ($httpCode == '200')
    {
      // Check our CURL error code is 0 (0 means OK!)
      if ($errorCode == 0)
      {
        // Return the data
        return $data;
      }
      // Curl Error
      else
      {
//        error_log('CURL error: '. $errorMessage .' (URL: '. $url .').');
        return false;
      }
    }
    // Bad HTTP response code
    else
    {
//      error_log('Bad HTTP response: '. $httpCode .' (URL: '. $url .').');
      return false;
    }
  }

  
  /**
    * getIpAddress
    *
    * Returns the users IP Address
    * This data shouldn't be trusted. Faking HTTP headers is trivial.
    *
    * @return string/false - the users IP address or false
    *
    */
  public static function getIPAddress()
  {
    // Try REMOTE_ADDR
    if (isset($_SERVER['REMOTE_ADDR']) and $_SERVER['REMOTE_ADDR'] != '')
    {
      return $_SERVER['REMOTE_ADDR'];
    }
    // Fall back to HTTP_CLIENT_IP
    elseif (isset($_SERVER['HTTP_CLIENT_IP']) and $_SERVER['HTTP_CLIENT_IP'] != '')
    {
      return $_SERVER['HTTP_CLIENT_IP'];
    }
    // Finally fall back to HTTP_X_FORWARDED_FOR
    // I'm aware this can sometimes pass the users LAN IP, but it is a last ditch attempt
    elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and $_SERVER['HTTP_X_FORWARDED_FOR'] != '')
    {
      return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    // Nothing? Return false
    return false;
  }
}      