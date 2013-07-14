<?php
/**
 * @package api
 * @subpackage objects
 * @deprecated use KalturaRule instead
 */
class KalturaPreviewRestriction extends KalturaSessionRestriction 
{
	/**
	 * The preview restriction length 
	 * 
	 * @var int
	 */
	public $previewLength;
	
	private static $mapBetweenObjects = array
	(
		"previewLength",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaBaseRestriction::toRule()
	 */
	public function toRule(KalturaRestrictionArray $restrictions)
	{
		// Preview restriction became a rule action, it's not a rule.
		return null;
	}
}