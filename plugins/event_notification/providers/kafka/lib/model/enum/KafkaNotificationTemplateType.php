<?php

/**
 * @package plugins.kafkaNotification
 * @subpackage model.enum
 */
class KafkaNotificationTemplateType implements IKalturaPluginEnum, EventNotificationTemplateType
{
	const KAFKA = 'Kafka';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			'KAFKA' => self::KAFKA,
		);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			self::KAFKA => 'Kafka event notification',
		);
	}
}
