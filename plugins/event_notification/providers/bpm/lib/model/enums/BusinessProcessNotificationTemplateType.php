<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage model.enum
 */
class BusinessProcessNotificationTemplateType implements IKalturaPluginEnum, EventNotificationTemplateType
{
	const BPM_START = 'BusinessProcessStart';
	const BPM_SIGNAL = 'BusinessProcessSignal';
	const BPM_ABORT = 'BusinessProcessAbort';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			'BPM_START' => self::BPM_START,
			'BPM_SIGNAL' => self::BPM_SIGNAL,
			'BPM_ABORT' => self::BPM_ABORT,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() 
	{
		return array(
			self::BPM_START => 'Start business-process event notification',
			self::BPM_SIGNAL => 'Signal running business-process event notification',
			self::BPM_ABORT => 'Abort running business-process event notification',
		);
	}
}
