<?php
/**
 * @package plugins.metadata
 * @subpackage api.objects
 */
class KalturaMetadataProfile extends KalturaObject implements IFilterable 
{
	/**
	 * @var int
	 * @filter eq
	 * @readonly
	 */
	public $id;
	
	/**
	 * @var int
	 * @filter eq
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * @var KalturaMetadataObjectType
	 * @filter eq,in
	 */
	public $metadataObjectType;
	
	/**
	 * @var int
	 * @filter eq
	 * @readonly
	 */
	public $version;
	
	/**
	 * @var string
	 * @filter eq
	 */
	public $name;
	
	/**
	 * @var string
	 * @filter eq,in
	 */
	public $systemName;
	
	/**
	 * @var string
	 */
	public $description;
	
	/**
	 * @var time
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $createdAt;
	
	/**
	 * @var time
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $updatedAt;
	
	/**
	 * @var KalturaMetadataProfileStatus
	 * @filter eq,in
	 * @readonly
	 */
	public $status;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $xsd;

	/**
	 * @var string
	 * @readonly
	 */
	public $views;

	/**
	 * @var string
	 * @readonly
	 */
	public $xslt;

	/**
	 * @var KalturaMetadataProfileCreateMode
	 * @filter eq,not,in,notin
	 */
	public $createMode;
	
	private static $map_between_objects = array
	(
		"id",
		"partnerId",
		"metadataObjectType" => "objectType",
		"version",
		"name",
		"systemName",
		"description",
		"createdAt",
		"updatedAt",
		"status",
		"createMode",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbMetadataProfile = null, $propsToSkip = array())
	{
		if(is_null($dbMetadataProfile))
			$dbMetadataProfile = new MetadataProfile();
			
		return parent::toObject($dbMetadataProfile, $propsToSkip);
	}
	
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function fromObject($source_object)
	{
		parent::fromObject($source_object);

		$key = $source_object->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
		$this->xsd = kFileSyncUtils::file_get_contents($key, true, false);
		
		$key = $source_object->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_VIEWS);
		$this->views = kFileSyncUtils::file_get_contents($key, true, false);

		$key = $source_object->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_XSLT);
		$this->xslt = kFileSyncUtils::file_get_contents($key, true, false);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyMinLength("name", 1);
		
		if($this->systemName)
		{
			$c = KalturaCriteria::create(MetadataProfilePeer::OM_CLASS);
			$c->add(MetadataProfilePeer::SYSTEM_NAME, $this->systemName);
			if(MetadataProfilePeer::doCount($c))
				throw new KalturaAPIException(KalturaErrors::SYSTEM_NAME_ALREADY_EXISTS, $this->systemName);
		}
		
		return parent::validateForInsert($propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		if (!is_null($this->name))
			$this->validatePropertyMinLength("name", 1);
				    
	    if ($this->systemName)
	    {
	        $c = KalturaCriteria::create(MetadataProfilePeer::OM_CLASS);
	        $c->add(MetadataProfilePeer::ID, $sourceObject->getId(), Criteria::NOT_EQUAL);
			$c->add(MetadataProfilePeer::SYSTEM_NAME, $this->systemName);
			if(MetadataProfilePeer::doCount($c))
				throw new KalturaAPIException(KalturaErrors::SYSTEM_NAME_ALREADY_EXISTS, $this->systemName);
	    }
	    
	    return parent::validateForUpdate($sourceObject);
	}
}