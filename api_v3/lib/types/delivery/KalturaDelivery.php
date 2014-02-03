<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDelivery extends KalturaObject implements IFilterable 
{
	/**
	 * The id of the Delivery
	 *
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $id;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * The name of the Delivery
	 *
	 * @var string
	 */
	public $name;
	
	/**
	 * Delivery type
	 * @var KalturaDeliveryType
	 */
	public $type;
	
	/**
	 * System name of the delivery
	 *
	 * @var string
	 * @filter eq,in
	 */
	public $systemName;
	
	/**
	 * The description of the Delivery
	 *
	 * @var string
	 */
	public $description;
	
	/**
	 * Creation time as Unix timestamp (In seconds)
	 *
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * Update time as Unix timestamp (In seconds)
	 *
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	/**
	 * @var KalturaPlaybackProtocol
	 */
	public $protocol;
	
	/**
	 * @var string
	 */
	public $url;
	
	/**
	 * the host part of the url
	 * @var string
	 */
	public $host;

	/**
	 * @var KalturaDeliveryStatus
	 */
	public $status;
	
	/**
	 * @var KalturaUrlRecognizer
	 */
	public $recognizer;
	
	/**
	 * @var KalturaUrlTokenizer
	 */
	public $tokenizer;
	
	/**
	 * True if this is the systemwide default for the protocol
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isDefault;

	/**
	 * True if this delivery is appropriate for protected entries (access control/entitlement)
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isSecure;
	
	/**
	 * the object from which this object was cloned (or 0)
	 * @var int
	 * @readonly
	 */
	public $parentId;			
	
	private static $map_between_objects = array
	(
			"createdAt",
			"description",
			"host" => "hostName",
			"id",
			"isDefault",
			"isSecure",
			"name",
			"parentId",
			"partnerId",
			"protocol",
			"recognizer",
			"status" => "deliveryStatus",
			"systemName",
			"tokenizer",
			"updatedAt",
			"url",
			"type",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			// @_!! TODO Generate the right object type based on Peer.
			$dbObject = new Delivery();
	
		parent::toObject($dbObject, $skip);
		return $dbObject;
	}
	
	public function fromObject($sourceObject)
	{
		if(!$sourceObject)
			return;
			
		parent::fromObject($sourceObject);
		
		$this->recognizer = $this->transformRecognizer($sourceObject);
		$this->tokenizer = $this->transformTokenizer($sourceObject);
	}
	
	protected function transformRecognizer($sourceObject) {
		$dbObject = $sourceObject->getRecognizer();
		if(is_null($dbObject))
			return null;
	
		$apiObject = KalturaDeliveryFactory::getRecognizerByType(get_class($dbObject));
		if(!is_null($apiObject))
			$apiObject->fromObject($dbObject);
		return $apiObject;
	}
	
	protected function transformTokenizer($sourceObject) {
		$dbObject = $sourceObject->getTokenizer();
		if(is_null($dbObject))
			return null;
	
		$apiObject = KalturaDeliveryFactory::getTokenizerInstanceByType(get_class($dbObject));
		if(!is_null($apiObject))
			$apiObject->fromObject($dbObject);
		return $apiObject;
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getExtraFilters()
	*/
	public function getExtraFilters()
	{
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getFilterDocs()
	*/
	public function getFilterDocs()
	{
		return array();
	}
}