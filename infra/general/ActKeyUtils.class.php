<?php

DEFINE('KCONF_FILE', dirname(__FILE__).'/../../alpha/config/kConf.php');
require_once(KCONF_FILE);
DEFINE('ACTIVATION_REPORT_URL', 'http://kalstats.kaltura.com/index.php/events/activation_event');


class ActKeyUtils
{
	
	const TYPE_ACTIVATION = 1; // activation key type
	const TYPE_EXTENSION  = 2; // extension key type
	const NO_EXPIRE = 'never'; //
	

	private static function decode($key)
	{
		$data = base64_decode($key);
		$data = explode('|', $data);
		if (!self::isKey($data)) {
			return false;
		}
		return $data;
	}
	
	private static function getPart($key, $part)
	{
		$data = self::decode($key);
		if (!$data || !isset($data[$part])) {
			return false;
		}
		return $data[$part];
	}
	
	private static function getEmail($key)
	{
		return self::getPart($key, 0);
	}
	
	
	private static function getExpiry($key)
	{
		return self::getPart($key, 2);
	}
	
	private static function getType($key)
	{
		return self::getPart($key, 1);
	}
	
	private static function getId($key)
	{
		return self::getPart($key, 3);
	}
	
	
	/**
	 * Returns the number of days till the given key expires.
	 * If already expired or not valid - returns false.
	 * If never expires - returns true.
	 * @param string $key activation key
	 */
	public static function daysToExpire($key)
	{
		$expiry_time = self::getExpiry($key);
		if (!$expiry_time) {
			return false;
		}
		if ($expiry_time == ActKeyUtils::NO_EXPIRE) {
			return true; //never expires
		}
		
		$days_left = $expiry_time - time();
		$days_left = ceil($days_left / (60*60*24));
		if ($days_left <= 0) {
			return false; //already expired
		}
		return $days_left; //days left
	}
	
	

	public static function isKey($data)
	{
		if (!isset($data[0]) || !isset($data[1]) || !isset($data[2]) || !isset($data[3])) {
			return false;
		}
		if ($data[1] != ActKeyUtils::TYPE_ACTIVATION && $data[1] != ActKeyUtils::TYPE_EXTENSION) {
			return false;
		}
		return true;
	}
	
	
	
	/**
	 * Set a new activation key for the system
	 * @param string $key the new key
	 * @param int $type key type TYPE_ACTIVATION / TYPE_EXTENSION
	 */
	public static function putNewKey($key)
	{
		
		$type = self::getType($key);
		if (!$type) {
			return false;
		}
		
		$cur_key = kConf::get('kaltura_activation_key');
		if ($cur_key && $type == ActKeyUtils::TYPE_ACTIVATION) {
			return false; // cannot recieve type activation when system was already activated
		}
		if (!$cur_key && $type == ActKeyUtils::TYPE_EXTENSION) {
			return false; // cannot recieve type extension on first activation
		}
		else {
			$data = @file_get_contents(KCONF_FILE);
			if (!$data) {
				return false;
			}
			else {
			
				$key_line = '/"kaltura_activation_key"(\s)*=>(\s)*(.+),/';
				$replacement = '"kaltura_activation_key" => "'.$key.'",';

				$data = preg_replace($key_line, $replacement ,$data);

				if (!@file_put_contents(KCONF_FILE, $data)) {
					return false;
				}
			}
			ActKeyUtils::sendReport($key);
			return true;
		}

	}
	
	
	public static function checkCurrent()
	{
		$cur_key = kConf::get('kaltura_activation_key');
		if ($cur_key == false) {
			$not_activated_msg = "Thank you for using the Kaltura On-Prem Video Platform.\n
To start your evaluation please activate your evaluation package from within the \"http://".kConf::get('apphome_url_no_protocol')."/start page\".<br/>
For support, please contact the Kaltura technical presales team.";
			die($not_activated_msg);
		}
		
		$result = ActKeyUtils::daysToExpire($cur_key);
		
		if ($result == false) {
			$expired_msg = "Thank you for using the Kaltura On-Prem Video Platform.<br/>
Your evaluation period has ended. Please contact Kaltura sales for transition to a permanent license";
			die($expired_msg);
		}
	}
	
	
	private static function sendReport($key)
	{
		// create a new cURL resource
		$ch = curl_init();
		
		$url = ACTIVATION_REPORT_URL;
		
		$params = array ( 
			'email' => kConf::get('report_admin_email'),
			'activation_key' => $key,
			'package_version' => kConf::get('kaltura_version'),
		);

		
		$url .= '?' . http_build_query($params);
		
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPGET, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); 
		
		// grab URL and pass it to the browser
		curl_exec($ch);
		
		// close cURL resource, and free up system resources
		curl_close($ch);
	}
	
}