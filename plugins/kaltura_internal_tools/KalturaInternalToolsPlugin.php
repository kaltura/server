<?php
class KalturaInternalToolsPlugin extends KalturaPlugin
{
	const PLUGIN_NAME = 'KalturaInternalTools';
	//const METADATA_FLOW_MANAGER_CLASS = 'kMetadataFlowManager';
	//const METADATA_COPY_HANDLER_CLASS = 'kMetadataObjectCopiedHandler';
	
	/**
	 * @return array<string,string> in the form array[serviceName] = serviceClass
	 */
	public static function getServicesMap()
	{
		$map = array(
			'KalturaInternalTools' => 'KalturaInternalToolsService',
			'KalturaInternalToolsSystemHelper' => 'KalturaInternalToolsSystemHelperService',
		);
		return $map;
	}
	
	/**
	 * @return string - the path to services.ct
	 */
	public static function getServiceConfig()
	{
		return realpath(dirname(__FILE__).'/config/kaltura_internal_tools.ct');
	}

	/**
	 * @return array<KalturaAdminConsolePlugin>
	 */
	public static function getAdminConsolePages()
	{
		$KalturaInternalTools = new KalturaInternalToolsPluginSystemHelperAction('KalturaInternalTools', 'KalturaInternalTools');
		$KalturaInternalToolsSystemHelp = new KalturaInternalToolsPluginSystemHelperAction('System Hendler', 'systemhendler', 'KalturaInternalTools');
		return array($KalturaInternalTools, $KalturaInternalToolsSystemHelp);
	}
}
