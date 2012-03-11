<?php
/**
 * @package plugins.metadata
 * @subpackage api.objects
 */
class KalturaCompareMetadataCondition extends KalturaCompareCondition
{
	/**
	 * May contain the full xpath to the field in two formats
	 * 1. Slashed xPath, e.g. /metadata/myElementName
	 * 2. Using local-name function, e.g. /*[local-name()='metadata']/*[local-name()='myElementName']
	 * 3. Using only the field name, e.g. myElementName, it will be searched as //myElementName
	 * 
	 * @var string
	 */
	public $xPath;
	
	/**
	 * Metadata profile id
	 * @var int
	 */
	public $profileId;
	
	private static $mapBetweenObjects = array
	(
		'xPath',
		'profileId',
	);

	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = MetadataPlugin::getApiValue(MetadataConditionType::METADATA_FIELD_COMPARE);
	}
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		$this->validatePropertyNotNull('xPath');
		$this->validatePropertyNotNull('profileId');
	
		if(!$dbObject)
			$dbObject = new kCompareMetadataCondition();
			
		return parent::toObject($dbObject, $skip);
	}
}
