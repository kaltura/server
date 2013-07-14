<?php
/**
 * @package plugins.tag_search
 *  @subpackage model.enum
 */
class TagResolveBatchJobType implements IKalturaPluginEnum, BatchJobType
{
	const TAG_RESOLVE = 'TagResolve';
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues() {
		return array(
			'TAG_RESOLVE' => self::TAG_RESOLVE,
		);
		
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() {
		return array();
		
	}


}