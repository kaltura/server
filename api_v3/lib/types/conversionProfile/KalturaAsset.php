<?php
/**
 * @package api
 * @subpackage objects
 * @relatedService ignore
 */
class KalturaAsset extends KalturaObject implements IRelatedFilterable, IApiObjectFactory
{
	/**
	 * The ID of the Flavor Asset
	 * 
	 * @var string
	 * @readonly
	 * @filter eq,in
	 */
	public $id;
	
	/**
	 * The entry ID of the Flavor Asset
	 * 
	 * @var string
	 * @readonly
	 * @filter eq,in
	 */
	public $entryId;
	
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $partnerId;
	
	/**
	 * The version of the Flavor Asset
	 * 
	 * @var int
	 * @readonly
	 */
	public $version;
	
	/**
	 * The size (in KBytes) of the Flavor Asset
	 * 
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $size;
	
	/**
	 * Tags used to identify the Flavor Asset in various scenarios
	 * 
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $tags;
	
	/**
	 * The file extension
	 * 
	 * @var string
	 * @insertonly
	 */
	public $fileExt;
	
	
	/**
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	
	/**
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	
	/**
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $deletedAt;
	
	
	/**
	 * System description, error message, warnings and failure cause.
	 * @var string
	 * @readonly
	 */
	public $description;
	
	
	/**
	 * Partner private data
	 * @var string
	 */
	public $partnerData;
	
	/**
	 * Partner friendly description
	 * @var string
	 */
	public $partnerDescription;
	
	/**
	 * 
	 * Comma separated list of source flavor params ids
	 * @var string
	 */
	public $actualSourceAssetParamsIds;


	/**
	 * The size (in Bytes) of the asset
	 *
	 * @var int
	 * @readonly
	 */
	public $sizeInBytes;
	
	private static $map_between_objects = array
	(
		"id",
		"entryId",
		"partnerId",
		"version",
		"size",
		"tags",
		"fileExt",
		"createdAt",
		"updatedAt",
		"deletedAt",
		"description",
		"partnerData",
		"partnerDescription",
		"actualSourceAssetParamsIds",
		"sizeInBytes"
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
	
	public static function getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
	     $type = $sourceObject->getType();
	     $object = null;
	     switch ($type)
	     {
	         case KalturaAssetType::FLAVOR:
	             $object = new KalturaFlavorAsset();
	             break;
	         case KalturaAssetType::LIVE:
	             $object = new KalturaLiveAsset();
	             break;
	         case KalturaAssetType::THUMBNAIL:
	             $object = new KalturaThumbAsset();
	             break;
	         default:
	             if($sourceObject instanceof thumbAsset)
                     {
	                 $object = KalturaPluginManager::loadObject('KalturaThumbAsset', $type);
	             }
	             elseif($sourceObject instanceof flavorAsset)
                     {
	                 $object = KalturaPluginManager::loadObject('KalturaFlavorAsset', $type);
	             }
	             else
	             {
	                 $object = KalturaPluginManager::loadObject('KalturaAsset', $type);
	             }
	     }
	     
	     // verify object was really generated
	     if (!$object)
	         return null;
	     // otherwise, create from given object
	     $object->fromObject($sourceObject, $responseProfile);
	     return $object;
	}	
}
