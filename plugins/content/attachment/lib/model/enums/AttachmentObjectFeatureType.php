<?php
/**
 * @package plugins.attachment
 * @subpackage model.enum
 */
class AttachmentObjectFeatureType implements IKalturaPluginEnum, ObjectFeatureType
{
	const ATTACHMENT = 'Attachment';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues() 
	{
		return array
		(
			'ATTACHMENT' => self::ATTACHMENT,
		);
		
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() {
		return array();
		
	}
}