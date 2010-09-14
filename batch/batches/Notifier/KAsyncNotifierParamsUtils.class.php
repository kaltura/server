<?php
/**
 * 
 * 
 * @package Scheduler
 * @subpackage Notifier
 *
 */
class KAsyncNotifierParamsUtils
{
	public static function prepareNotificationData($url, $signature_key, KalturaBatchJob $job, KalturaNotificationJobData $data, $prefix = null)
	{
		KalturaLog::debug("sendSingleNotification($url, $signature_key, $job->id, data, $prefix)");
		
		$params = array("notification_id" => $job->id, "notification_type" => $data->typeAsString, "puser_id" => $data->userId, "partner_id" => $job->partnerId);
		
		switch($data->objType)
		{
			case KalturaNotificationObjectType::USER:
				$params["user_id"] = $data->objectId;
				break;
			case KalturaNotificationObjectType::ENTRY:
				$params["entry_id"] = $data->objectId;
				break;
			case KalturaNotificationObjectType::KSHOW:
				$params["kshow_id"] = $data->objectId;
				break;
			case KalturaNotificationObjectType::BATCH_JOB:
				$params["job_id"] = $data->objectId;
				break;
			default:
			// VERY STARANGE - either objType not set properly or some error !
		}
		
		$object_data_params = self::getDataAsArray($data->data);
		
		if($object_data_params)
		{
			$params = array_merge($params, $object_data_params);
		}
		
		$params = self::fixParams($params, $prefix);
		
		$params['signed_fields'] = '';
		foreach($params as $key => $value)
		{
			$params['signed_fields'] .= $key . ',';
		}
		
		return self::signParams($signature_key, $params);
	}
	
	/**
	 * @param unknown_type $serialized_data
	 * @return unknown|mixed|multitype:unknown |NULL
	 */
	public static function getDataAsArray($serialized_data)
	{
		if(is_array($serialized_data))
			return $serialized_data;
			
		if(is_string($serialized_data))
		{
			try{
				$tmp = unserialize($serialized_data);
				if(is_array($tmp))
					return $tmp;
			}
			catch(Exception $e){
			}
			
			return array($serialized_data);
		}
			
		return null;
	}
	
	/**
	 * @param string $signature_key
	 * @param array $params
	 * @return array 
	 */
	public static function signParams($signature_key, &$params)
	{
		list($sig, $raw_str) = self::signature($signature_key, $params);
		$params["sig"] = $sig;
		
		return array($params, $raw_str);
	}
	
	/**
	 * @param string $signature_key
	 * @param array $params
	 * @return string 
	 */
	private static function signature($signature_key, $params)
	{
		ksort($params);
		$str = "";
		foreach($params as $k => $v)
		{
			if($k == "sig")
				continue;
			$str .= $k . $v;
		}
		
		return array(md5($signature_key . $str), $str);
	}
	
	/**
	 * @param array $params
	 * @param string $prefix
	 * @return array
	 */
	private static function fixParams(&$params, $prefix = null)
	{
		$new_params = array();
		foreach($params as $k => $v)
		{
			if($prefix)
				$new_params[$prefix . trim($k)] = trim($v);
			else
				$new_params[trim($k)] = trim($v);
		}
		return $new_params;
	}
}
?>