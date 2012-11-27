<?php
/**
 * @package infra
 * @subpackage Storage
 */
class kUrlUtils
{
	/**
	 * Method checks whether the URL passed to it as a parameter returns a response.
	 * @param string $url
	 * @return bool
	 */
	public static function urlExists ($url)
	{
		if (is_null($url)) 
			return false;  
		if (!function_exists('curl_init'))
		{
			KalturaLog::err('Unable to use util when php curl is not enabled');
		}
	    $ch = curl_init($url);  
	    curl_setopt($ch, CURLOPT_TIMEOUT, 5);  
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
	    $data = curl_exec($ch);  
	    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
	    curl_close($ch);  
	    if($httpcode>=200 && $httpcode<300)
	        return $data ? $data : true;  
	    else 
	        return false;  
	}	
	
	public static function urlExistsRecursive ($url)
	{
		$data = self::urlExists($url);
		if ($data)
		{
			preg_match_all("/http.*/", $data, $matches);
			$lastMatch = array_pop($matches[0]);
			if (!$lastMatch)
			{
				return true;
			}
			else 
			{
				return self::urlExistsRecursive($lastMatch);
			}
			
		}
		else
		{
			return false;
		}
	}
}