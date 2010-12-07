<?php
class KalturaContentDistributionSearchItem extends KalturaSearchItem
{
	/**
	 * @var int
	 */
	public $distributionProfileId;
	
	/**
	 * @var KalturaEntryDistributionSunStatus
	 */
	public $distributionSunStatus;
	
	/**
	 * @var KalturaEntryDistributionFlag
	 */
	public $entryDistributionFlag;
	
	/**
	 * @var KalturaEntryDistributionStatus
	 */
	public $entryDistributionStatus;

	private static $map_between_objects = array
	(
		'distributionProfileId',
		'distributionSunStatus',
		'entryDistributionFlag',
		'entryDistributionStatus',
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new ContentDistributionSearchFilter();
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}