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
	 * Comma separated asset params ids that exists for this media entry
	 * 
	 * @var string
	 * @readonly
	 * @filter matchor,matchand
	 */
	public $assetParamsIds;
	
	private static $map_between_objects = array
	(
		"documentType" => "mediaType",
		"assetParamsIds" => "flavorParamsIds",
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
	
	
	public function fromObject($sourceObject, IResponseProfile $responseProfile = null)
	{
		if(!$sourceObject)
			return;
			
		parent::fromObject($sourceObject, $responseProfile);
	}
}
