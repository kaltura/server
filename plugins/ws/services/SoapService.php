<?php
/**
 * Expose Kaltura API over Soap 
 * 
 * @service soap
 * @package plugins.ws
 * @subpackage api.services
 */
class SoapService extends KalturaBaseService 
{
	/* (non-PHPdoc)
	 * @see KalturaBaseService::partnerRequired()
	 */
	protected function partnerRequired($actionName)
	{
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaBaseService::isPermitted()
	 */
	protected function isPermitted(&$allowPrivatePartnerData)
	{
		return true;
	}
	
	/**
	 * Serves the requested XSD according to the type and name. 
	 * 
	 * @action serve
	 * @param string $serviceName
	 * @param string $pluginName
	 * @return file 
	 */
	function serveAction($serviceName, $pluginName = null)
	{
		$server = new WsServer($serviceName, $pluginName);
		$postdata = file_get_contents("php://input");
		KalturaLog::debug("POST raw data: $postdata");
		$server->service($postdata);
		return new kRendererStandardOutput();
	}
}
