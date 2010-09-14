<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaMetadataSearchItem extends KalturaSearchOperator
{
	/**
	 * @var int
	 */
	public $metadataProfileId;
	
//	public function getCondition($xPaths = null)
//	{
//		if(is_null($xPaths))
//		{
//			$xPaths = array();
//			$profileFields = MetadataProfileFieldPeer::retrieveActiveByMetadataProfileId($this->metadataProfileId);
//			foreach($profileFields as $profileField)
//				$xPaths[$profileField->getXpath()] = $profileField->getId();
//		}
//		
//		$conditions = array();
//		$pluginName = MetadataPlugin::PLUGIN_NAME;
//		
//		foreach($this->items as $item)
//		{
//			$condition = null;
//			
//			if($item instanceof KalturaSearchCondition && isset($xPaths[$item->field]))
//			{
//				$fieldId = $xPaths[$item->field];
//				$condition = "\"{$pluginName}_{$fieldId} {$item->value} mdend\"";
//			}
//			elseif($item instanceof KalturaMetadataSearchItem)
//			{
//				$condition = $item->getCondition($xPaths);
//			}
//			
//			if($condition)
//				$conditions[] = "($condition)";
//		}
//
//		if(!count($conditions))
//			return null;
//			
//		$glue = ($this->type == KalturaSearchOperatorType::SEARCH_AND ? ' & ' : ' | ');
//		return implode($glue, $conditions);
//	}

	private static $map_between_objects = array
	(
		"metadataProfileId"
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		KalturaLog::debug("To object: metadataProfileId [$this->metadataProfileId]");
		if(!$object_to_fill)
			$object_to_fill = new MetadataSearchFilter();
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
