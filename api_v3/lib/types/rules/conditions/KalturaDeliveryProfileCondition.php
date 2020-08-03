<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryProfileCondition extends KalturaCondition
{
	/**
	 * The delivery ids that are accepted by this condition
	 * 
	 * @var KalturaIntegerValueArray
	 */
	public $deliveryProfileIds;
	
	/**
         * Init object type
         */
        public function __construct()
        {
                $this->type = ConditionType::DELIVERY_PROFILE;
        }
	
	private static $mapBetweenObjects = array
	(
		'deliveryProfileIds',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kDeliveryProfileCondition();
		return parent::toObject($dbObject, $skip);
	}
}
