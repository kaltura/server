<?php
/**
 * @package plugins.captionSphinx
 * @subpackage DB
 */
class SphinxCaptionAssetItemCriteria extends SphinxCriteria 
{
	public static $sphinxFields = array(
		CaptionAssetItemPeer::ENTRY_ID => 'entry_id',
		CaptionAssetItemPeer::CAPTION_ASSET_ID => 'caption_asset_id',
		CaptionAssetItemPeer::TAGS => 'tags',
		CaptionAssetItemPeer::CONTENT => 'content',
		CaptionAssetItemPeer::PARTNER_DESCRIPTION => 'partner_description',
		CaptionAssetItemPeer::LANGUAGE => 'language',
		CaptionAssetItemPeer::LABEL => 'label',
		CaptionAssetItemPeer::FORMAT => 'format',
		
		CaptionAssetItemPeer::PARTNER_ID => 'partner_id',
		CaptionAssetItemPeer::STATUS => 'caption_asset_status',
		CaptionAssetItemPeer::STATUS => 'caption_asset_status',
		CaptionAssetItemPeer::SIZE => 'size',
		CaptionAssetItemPeer::START_TIME => 'start_time',
		CaptionAssetItemPeer::END_TIME => 'end_time',
		
		CaptionAssetItemPeer::CREATED_AT => 'created_at',
		CaptionAssetItemPeer::UPDATED_AT => 'updated_at',
	);
	
	public static $sphinxOrderFields = array(
		CaptionAssetItemPeer::SIZE => 'size',
		CaptionAssetItemPeer::START_TIME => 'start_time',
		CaptionAssetItemPeer::END_TIME => 'end_time',
		
		CaptionAssetItemPeer::CREATED_AT => 'created_at',
		CaptionAssetItemPeer::UPDATED_AT => 'updated_at',
	);
	
	/**
	 * @return criteriaFilter
	 */
	protected function getDefaultCriteriaFilter()
	{
		return CaptionAssetItemPeer::getCriteriaFilter();
	}
	
	public function getSphinxOrderFields()
	{
		return self::$sphinxOrderFields;
	}
	
	/**
	 * @return string
	 */
	protected function getSphinxIndexName()
	{
		return kSphinxSearchManager::getSphinxIndexName(CaptionSearchPlugin::INDEX_NAME);
	}

	/* (non-PHPdoc)
	 * @see SphinxCriteria::getSphinxIdField()
	 */
	protected function getSphinxIdField()
	{
		return 'int_id';
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::getPropelIdField()
	 */
	protected function getPropelIdField()
	{
		return CaptionAssetItemPeer::ID;
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::doCountOnPeer()
	 */
	protected function doCountOnPeer(Criteria $c)
	{
		return CaptionAssetItemPeer::doCount($c);
	}

	public function hasSphinxFieldName($fieldName)
	{
		return isset(self::$sphinxFields[$fieldName]);
	}
	
	public function getSphinxFieldName($fieldName)
	{
		if(!isset(self::$sphinxFields[$fieldName]))
			return $fieldName;
			
		return self::$sphinxFields[$fieldName];
	}
	
	public function getSphinxFieldType($fieldName)
	{
		$sphinxTypes = CaptionSphinxPlugin::getSphinxSchemaFields();
		if(!isset($sphinxTypes[$fieldName]))
			return null;
			
		return kSphinxSearchManager::getSphinxDataType($sphinxTypes[$fieldName]);
	}
	
	public function hasMatchableField($fieldName)
	{
		return in_array($fieldName, array(
			"entry_id", 
			"caption_asset_id", 
			"tags", 
			"content", 
			"partner_description",
			"language",
			"label",
			"format",
		));
	}
}