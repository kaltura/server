<?php
/**
 * @package plugins.document
 * @subpackage api.objects
 */
class KalturaDocumentEntry extends KalturaBaseEntry
{
	/**
	 * The type of the document
	 *
	 * @var KalturaDocumentType
	 * @insertonly
	 * @filter eq,in
	 */
	public $documentType;
	
	/**
	 * Conversion profile ID to override the default conversion profile
	 * 
	 * @var string
	 * @insertonly
	 * @deprecated use ingestionProfileId instead
	 */
	public $conversionProfileId;
	
	private static $map_between_objects = array
	(
		"documentType" => "mediaType",
		"conversionProfileId" => "conversionQuality",
	);
	
	public function __construct()
	{
		$this->type = KalturaEntryType::DOCUMENT;
	}
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	
	/**
	 * @return DocumentEntry
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new DocumentEntry();
			
		parent::toObject($dbObject, $skip);

		return $dbObject;		
	}
	
	
	public function fromObject($sourceObject)
	{
		if(!$sourceObject)
			return;
			
		parent::fromObject($sourceObject);
	}
}
