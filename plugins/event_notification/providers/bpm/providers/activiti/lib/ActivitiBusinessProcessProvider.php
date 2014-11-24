<?php
/**
 * @package plugins.activitiBusinessProcessNotification
 * @subpackage model.enum
 */
class ActivitiBusinessProcessProvider implements IKalturaPluginEnum, BusinessProcessProvider
{
	const ACTIVITI = 'Activiti';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			'ACTIVITI' => self::ACTIVITI,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() 
	{
		return array(
			self::ACTIVITI => 'Activiti BPM Platform',
		);
	}
}
