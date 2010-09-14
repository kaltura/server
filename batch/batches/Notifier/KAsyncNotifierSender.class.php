<?php
/**
 * 
 * @package Scheduler
 * @subpackage Notifier
 * 
 */

class KAsyncNotifierSender
{
	const OK = 0; 
	const ERROR_RETRY = -1;
	const ERROR_NO_RETRY = -2;
	
	private static $curl;
	
	/**
	 * @param string $url
	 * @param array $params
	 */
	public static function createDebugHtml($url, $params)
	{
		$inputs = '';
		foreach($params as $param => $value)
			$inputs .= "<input name=\"$param\" value=\"$value\"/>";
		
		KalturaLog::debug('
		<html>
			<body>
				<form method="post" action="' . $url . '">
					' . $inputs . '
					<input type="submit" value="test"/>
				</form>
			</body>
		</html>
		'); 
	}
	
	/**
	 * @param string $url
	 * @param array $params
	 * @return array 
	 */
	public static function send($url, $params)
	{
		static $close_count = 0;
		
		KalturaLog::debug("send($url)"); 
		
		self::createDebugHtml($url, $params);
		
		// once every some time - close the connection and reconnect
		if($close_count > 50)
		{
			self::closeConnection();
			$close_count = 0;
		}
		
		$close_count ++;
		
		if(! self::$curl)
			self::$curl = curl_init();
		$ch = self::$curl;
		
		try
		{
			//TRACE ( "-- Hitting URL: $url" );
			$header = array("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Accept-Language: en-us,en;q=0.5", "Accept-Encoding: gzip,deflate", "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7", "Keep-Alive: 300", "Connection: keep-alive");
			
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params, null, "&"));
			
			// 			curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
			curl_setopt($ch, CURLOPT_URL, $url);
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, '');
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			//			curl_setopt($ch, CURLOPT_HEADER , true );
			

			//			curl_setopt($ch, CURLOPT_VERBOSE, true );
			//			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
			

			$result = curl_exec($ch);
			
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		}
		catch(Exception $ex)
		{
			self::closeConnection();
			KalturaLog::err('Sending notification failed with message: '.$ex->getMessage());
			throw $ex;
		}
		
		return array($params, $result, $http_code);
	}
	
	private static function closeConnection()
	{
		KalturaLog::debug("Closing connection");
		if(self::$curl != null)
			curl_close(self::$curl);
		self::$curl = null;
	}

}
?>