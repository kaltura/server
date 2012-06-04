<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBulkUploadResultEntry extends KalturaBulkUploadResult
{
    
    /**
     * @var string
     */
    public $entryId;
    
    /**
     * @var string
     */
    public $title;


	
	/**
     * @var string
     */
    public $description;


	
	/**
     * @var string
     */
    public $tags;


	
	/**
     * @var string
     */
    public $url;


	
	/**
     * @var string
     */
    public $contentType;


	
	/**
     * @var int
     */
    public $conversionProfileId;


	
	/**
     * @var int
     */
    public $accessControlProfileId;


	
	/**
     * @var string
     */
    public $category;


	
	/**
     * @var int
     */
    public $scheduleStartDate;


	
	/**
     * @var int
     */
    public $scheduleEndDate;

    /**
     * @var int
     */
    public $entryStatus;
	
	/**
     * @var string
     */
    public $thumbnailUrl;


	
	/**
     * @var bool
     */
    public $thumbnailSaved;
    
    /**
     * @var string
     */
    public $sshPrivateKey;
    
    /**
     * @var string
     */
    public $sshPublicKey;
    
    /**
     * @var string
     */
    public $sshKeyPassphrase;
    
    /**
	 * @var string
	 */
	public $creatorId;
	
	/**
	 * @var string
	 */
	public $entitledUsersEdit;
		
	/**
	 * @var string
	 */
	public $entitledUsersPublish;	
	
    
    private static $mapBetweenObjects = array
	(
	    "entryId",
		"entryStatus",
	    "title",
	    "description",
	    "tags",
	    "url",
	    "contentType",
	    "conversionProfileId",
	    "accessControlProfileId",
	    "category",
	    "scheduleStartDate",
	    "scheduleEndDate",
	    "thumbnailUrl",
		"thumbnailSaved",
	    "errorDescription",
	    "sshPrivateKey",
	    "sshPublicKey",
	    "sshKeyPassphrase",
		"creatorId",
		"entitledUsersEdit",
		"entitledUsersPublish",
	);
	
    public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
    public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if ($this->entryId)
		{
		    $this->objectId = $this->entryId;
		}
		
		if ($this->entryStatus)
		{
		    $this->objectStatus = $this->entryStatus;
		}
		
		return parent::toInsertableObject(new BulkUploadResultEntry(), $props_to_skip);
	}
}