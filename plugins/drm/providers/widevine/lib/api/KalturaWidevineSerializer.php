<?php

class KalturaWidevineSerializer extends KalturaSerializer
{
	/* (non-PHPdoc)
	 * @see KalturaSerializer::serialize()
	 */
	public function serialize($object) 
	{
		if (is_object($object) && $object instanceof Exception)
    	{
    		$assetid = 0;
    		$requestParams = requestUtils::getRequestParams();
			if(array_key_exists(WidevineLicenseProxyUtils::ASSETID, $requestParams))
			{
				$assetid = $requestParams[WidevineLicenseProxyUtils::ASSETID];
			}  
			$object = WidevineLicenseProxyUtils::createErrorResponse(KalturaWidevineErrorCodes::GENERAL_ERROR, $assetid);
    	}
		$this->_serializedString = $object;		
	}
	
	public function setHeaders()
	{
		header("Content-Type: text/plain");
		header("Content-Length: " . strlen($this->_serializedString));
		header("Content-Transfer-Encoding: base64");		
	}
}