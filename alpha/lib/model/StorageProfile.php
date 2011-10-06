<?php

/**
 * Subclass for representing a row from the 'storage_profile' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class StorageProfile extends BaseStorageProfile
{
	const PLAY_FORMAT_HTTP = 'http';
	const PLAY_FORMAT_RTMP = 'rtmp';
	const PLAY_FORMAT_SILVER_LIGHT = 'sl';
	const PLAY_FORMAT_APPLE_HTTP = 'applehttp';
	const PLAY_FORMAT_RTSP = 'rtsp';
	const PLAY_FORMAT_AUTO = 'auto';
	
	const STORAGE_SERVE_PRIORITY_KALTURA_ONLY = 1;
	const STORAGE_SERVE_PRIORITY_KALTURA_FIRST = 2;
	const STORAGE_SERVE_PRIORITY_EXTERNAL_FIRST = 3;
	const STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY = 4;
	
	const STORAGE_STATUS_DISABLED = 1;
	const STORAGE_STATUS_AUTOMATIC = 2;
	const STORAGE_STATUS_MANUAL = 3;
	
	const STORAGE_KALTURA_DC = 0;
	const STORAGE_PROTOCOL_FTP = 1;
	const STORAGE_PROTOCOL_SCP = 2;
	const STORAGE_PROTOCOL_SFTP = 3;
	const STORAGE_PROTOCOL_S3 = 6;
	
	const STORAGE_DEFAULT_KALTURA_PATH_MANAGER = 'kPathManager';
	const STORAGE_DEFAULT_EXTERNAL_PATH_MANAGER = 'kExternalPathManager';
	
	const CUSTOM_DATA_URL_MANAGER_PARAMS = 'url_manager_params';
	
	/**
	 * @return kPathManager
	 */
	
	public function getPathManager()
	{
		$class = $this->getPathManagerClass();
		if(!$class || !strlen(trim($class)) || !class_exists($class))
		{
			if($this->getProtocol() == self::STORAGE_KALTURA_DC)
			{
				$class = self::STORAGE_DEFAULT_KALTURA_PATH_MANAGER;
			}
			else
			{
				$class = self::STORAGE_DEFAULT_EXTERNAL_PATH_MANAGER;
			}
		}
			
		return new $class();
	}
	
	/* ---------------------------------- TODO - temp solution -----------------------------------------*/
	// remove after event manager implemented
	
	const STORAGE_TEMP_TRIGGER_CONVERT_FINISHED = 1;
	const STORAGE_TEMP_TRIGGER_MODERATION_APPROVED = 2;
	const STORAGE_TEMP_TRIGGER_FLAVOR_READY = 3;
		
	public function getTrigger() { return $this->getFromCustomData("trigger", null, self::STORAGE_TEMP_TRIGGER_CONVERT_FINISHED); }
	public function setTrigger( $v ) { $this->putInCustomData("trigger", (int)$v); }
	
	//external path format
	public function setPathFormat($v) { $this->putInCustomData('path_format', $v);}
	public function getPathFormat() { return $this->getFromCustomData('path_format', null);}
	
	/* ---------------------------------- TODO - temp solution -----------------------------------------*/
	
	/* URL Manager Params */
	
	public function setUrlManagerParams($params)
	{
	    $this->putInCustomData(self::CUSTOM_DATA_URL_MANAGER_PARAMS, serialize($params));
	}
	
	public function getUrlManagerParams()
	{
	    $params = $this->getFromCustomData(self::CUSTOM_DATA_URL_MANAGER_PARAMS);
	    $params = unserialize($params);
	    if (!$params) {
	        return array();
	    }
	    return $params;
	}
	
}
