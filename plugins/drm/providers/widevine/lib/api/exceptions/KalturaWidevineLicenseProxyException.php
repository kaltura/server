<?php
/**
 * @package plugins.widevine
 * @subpackage api.errors
 */
class KalturaWidevineLicenseProxyException extends Exception 
{
	private $wvCode;
	
	public function __construct ($code = null) 
	{
		if(!$code || !is_int($code))
			$this->wvCode = KalturaWidevineErrorCodes::UNKNOWN_ERROR;
		else
			$this->wvCode = $code;
	}
	
	public function getWvErrorCode()
	{
		return $this->wvCode;
	}
}