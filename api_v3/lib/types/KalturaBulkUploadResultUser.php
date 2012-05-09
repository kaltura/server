<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBulkUploadResultUser extends KalturaBulkUploadResult
{
    /**
     * @var string
     */
    public $screenName;
    
    /**
     * @var string
     */
    public $email;
    
    /**
     * @var string
     */
    public $description;
    
    /**
     * @var string
     */
    public $tags;
    
    /**
     * @var int
     */
    public $dateOfBirth;
    
    /**
     * @var string
     */
    public $country;
    
    /**
     * @var string
     */    
    public $state;
    
    /**
     * @var string
     */
    public $city;
    
    /**
     * @var string
     */
    public $zip;
    
    /**
     * @var int
     */
    public $gender;
    
    /**
     * @var string
     */
    public $firstName;
    
    /**
     * @var string
     */
    public $lastName;
    
    /**
     * @var bool
     */
    public $isAdmin;
    
    /**
     * @var string
     */
    public $roleIds;
    
    private static $mapBetweenObjects = array
	(
		"screenName",
	    "email",
	    "dateOfBirth",
	    "country",
		"state",
		"city",
		"zip",
	    "gender",
	    "firstName",
	    "lastName",
	    "isAdmin",
	    "tags",
	    "roleIds",
	);
	
    public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
    public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		$dbObject = parent::toInsertableObject(new BulkUploadResultKuser(), $props_to_skip);
		
		$pluginsData = $this->addPluginData();
		$dbObject->setPluginsData($pluginsData);
		
		return $dbObject;
	}
}