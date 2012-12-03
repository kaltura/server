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
	    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
	    curl_close($ch);  
	    if($data && $httpcode>=200 && $httpcode<300)
	    {
	        return $contentType == 'application/vnd.apple.mpegurl' ? $data : true;
	    }  
	    else 
	        return false;  
	}	
	
	public static function urlExistsRecursive ($url)
	{
		$data = self::urlExists($url);
		if(is_bool($data))
			return $data;
		
		$lines = explode("\n", trim($data));
		if(!preg_match("/http.*/", array_pop($lines), $matches))
			return false;
			
		$lastMatch = $matches[0];
		return self::urlExistsRecursive($lastMatch);
	}
}