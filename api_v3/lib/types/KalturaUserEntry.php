<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
abstract class KalturaUserEntry extends KalturaObject implements IRelatedFilterable
{

	/**
	 * unique auto-generated identifier
	 * @var int
	 * @readonly
	 * @filter eq,in,notin
	 */
	public $id;

	/**
	 * @var string
	 * @insertonly
	 * @filter eq,in,notin
	 */
	public $entryId;

	/**
	 * @var int
	 * @insertonly
	 * @filter eq,in,notin
	 */
	public $userId;

	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;

	/**
	 * @var KalturaUserEntryType
	 * @readonly
	 * @filter eq,in,notin
	 */
	public $type;

	/**
	 * @var KalturaUserEntryStatus
	 * @readonly
	 */
	public $status;

	/**
	 * @var time
	 * @readonly
	 */
	public $createdAt;

	/**
	 * @var time
	 * @readonly
	 */
	public $updatedAt;


	private static $map_between_objects = array
	(
		"id",
		"entryId",
		"userId" => "KuserId",
		"partnerId",
		"type",
		"status",
		"createdAt",
		"updatedAt"
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}


	/**
	 * Function returns KalturaUserEntry sub-type according to protocol
	 * @var string $type
	 * @return KalturaUserEntry
	 *
	 */
	public static function getInstanceByType ($type)
	{
		$obj = KalturaPluginManager::loadObject("KalturaUserEntry",$type);
		if (is_null($obj))
		{
			KalturaLog::warning("The type '$type' is unknown");
		}
		return $obj;
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = UserEntry::getInstanceByType($this->type);
		$object_to_fill->setPartnerId(kCurrentContext::getCurrentPartnerId());
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	/**
	 * Should return the extra filters that are using more than one field
	 * On inherited classes, do not merge the array with the parent class
	 *
	 * @return array
	 */
	function getExtraFilters()
	{
		return array(array("order" => "recent"));
	}

	/**
	 * Should return the filter documentation texts
	 *
	 */
	function getFilterDocs()
	{
		return array();
	}


}