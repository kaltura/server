<?php
class ProfesionalServicesPlugin implements IKalturaAdminConsolePagesPlugin
{
	const PLUGIN_NAME = 'ProfesionalServices';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public static function getAdminConsolePages()
	{
		$partnersList = new PartnerListAction();
		return array($partnersList);
	}

	public function getInstances($intrface)
	{
		if($this instanceof $intrface)
			return array($this);
			
		return array();
	}

}