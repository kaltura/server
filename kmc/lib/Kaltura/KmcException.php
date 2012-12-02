<?php
/**
 * @package KMC
 * @subpackage Errors
 */
class Kaltura_KmcException extends Infra_Exception
{
	const KALTURA_HEADER_ERROR_CODE = 'X-Kaltura-ErrorCode';
	
	
	const ERROR_CODE_NO_IDENTITY = 'NO_IDENTITY';
	const ERROR_CODE_NO_DATA = 'NO_DATA';
	const ERROR_CODE_NO_CATEGORY = 'NO_CATEGORY';
	const ERROR_CODE_PAGE_NOT_FOUND = 'PAGE_NOT_FOUND';
	const ERROR_CODE_MISSING_OPTION = 'MISSING_OPTION';
	
	public function getPrefix()
	{
		return 'KMC';
	}
	
	public static function getErrorCode(Exception $e)
	{
		if($e instanceof Kaltura_KmcException)
			return $e->getPrefix() . ':' . $e->getCode();
			
		if($e instanceof Kaltura_Client_Exception)
			return 'Server:' . $e->getCode();
			
		if($e instanceof Kaltura_Client_ClientException)
			return 'API:' . $e->getCode();
			
		if($e instanceof Infra_Exception)
			return 'UI-Infra:' . $e->getCode();
		
		if($e instanceof Zend_Exception)
			return 'Zend:' . $e->getCode();
			
		return 'Runtime:' . $e->getCode();
	}
}
