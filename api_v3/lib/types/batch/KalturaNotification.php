<?php

/**
 * @package api
 * @subpackage objects
 */

/**
 *  
 */
class KalturaNotification extends KalturaBaseJob
{
	/**
	 * @var string
	 */
	public $puserId;

	/**
	 * @var KalturaNotificationType
	 */
    public $type;
    
//    job_sub_type : smallint


	/**
	 * @var string
	 */
	public $objectId;  

	/**
	 * @var KalturaNotificationStatus
	 */	
   	public $status;
   	
	/**
	 * @var string
	 */   	
    public $notificationData;
    
	/**
	 * @var int
	 */    
    public $numberOfAttempts;
    
	/**
	 * @var string
	 */    
    public $notificationResult;

	/**
	 * @var KalturaNotificationObjectType
	 */    
    public $objType;  


    private static $map_between_objects = array
	(
		"puserId" ,
		"status" , 
		"type" , 
	 	"objectId" , "objType" , "status" , "notificationData" , "numberOfAttempts" , "createdAt" ,
		"updatedAt", "notificationResult" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	    
	public function fromNotification ( notification   $dbNotification )
	{
		parent::fromObject( $dbNotification );
		return $this;
	}
	
	public function toNotification () 
	{
		$dbNotification = new notification();
		return parent::toObject( $dbNotification )	;
	}    
}

?>