<?php
/**
 * @package plugins.tag_search
 *  @subpackage model.enum
 */
class IndexTagsByPrivacyContextJobType implements IKalturaPluginEnum, BatchJobType
{
	const INDEX_TAGS = 'IndexTagsByPrivacyContext';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues() {
		return array(
			'INDEX_TAGS' => self::INDEX_TAGS,
		);
		
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() {
		return array();
		
	}


}