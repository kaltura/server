<?php

/** 
 * @package Core
 * @subpackage Batch
 */
class kNotificationJobData extends kJobData
{
	const NOTIFICATION_MGR_NO_SEND = 0;
	const NOTIFICATION_MGR_SEND_ASYNCH = 1;
	const NOTIFICATION_MGR_SEND_SYNCH = 2;
	const NOTIFICATION_MGR_SEND_BOTH = 3;
	
	const NOTIFICATION_TYPE_ENTRY_ADD = 1;
	const NOTIFICATION_TYPE_ENTRY_UPDATE_PERMISSIONS = 2;
	const NOTIFICATION_TYPE_ENTRY_DELETE = 3;
	const NOTIFICATION_TYPE_ENTRY_BLOCK = 4;
	const NOTIFICATION_TYPE_ENTRY_UPDATE = 5;
	const NOTIFICATION_TYPE_ENTRY_UPDATE_THUMBNAIL = 6;
	const NOTIFICATION_TYPE_ENTRY_UPDATE_MODERATION = 7;
	const NOTIFICATION_TYPE_ENTRY_REPORT = 8;
	
	const NOTIFICATION_TYPE_KSHOW_ADD = 11;
	const NOTIFICATION_TYPE_KSHOW_UPDATE_INFO = 12;
	const NOTIFICATION_TYPE_KSHOW_DELETE = 13;
	const NOTIFICATION_TYPE_KSHOW_UPDATE_PERMISSIONS = 14;
	const NOTIFICATION_TYPE_KSHOW_RANK = 15;
	const NOTIFICATION_TYPE_KSHOW_BLOCK = 16;

	const NOTIFICATION_TYPE_USER_ADD = 21;
	const NOTIFICATION_TYPE_USER_BANNED = 26;

	const NOTIFICATION_TYPE_BATCH_JOB_STARTED = 30;
	const NOTIFICATION_TYPE_BATCH_JOB_SUCCEEDED = 31;
	const NOTIFICATION_TYPE_BATCH_JOB_FAILED = 32;
	const NOTIFICATION_TYPE_BATCH_JOB_SIMILAR_EXISTS = 33;
	
	const NOTIFICATION_TYPE_TEST = 99;
	
	const NOTIFICATION_RESULT_OK = 0; 
	const NOTIFICATION_RESULT_ERROR_RETRY = -1;
	const NOTIFICATION_RESULT_ERROR_NO_RETRY = -2;
		
	const NOTIFICATION_OBJECT_TYPE_ENTRY = 1;
	const NOTIFICATION_OBJECT_TYPE_KSHOW = 2;
	const NOTIFICATION_OBJECT_TYPE_USER = 3;	
	const NOTIFICATION_OBJECT_TYPE_BATCH_JOB = 4;	
	 
	public static $NOTIFICATION_OBJ_TYPE_MAP = array (
		self::NOTIFICATION_TYPE_ENTRY_ADD => self::NOTIFICATION_OBJECT_TYPE_ENTRY,
		self::NOTIFICATION_TYPE_ENTRY_UPDATE => self::NOTIFICATION_OBJECT_TYPE_ENTRY,
		self::NOTIFICATION_TYPE_ENTRY_UPDATE_PERMISSIONS => self::NOTIFICATION_OBJECT_TYPE_ENTRY,
		self::NOTIFICATION_TYPE_ENTRY_DELETE => self::NOTIFICATION_OBJECT_TYPE_ENTRY,
		self::NOTIFICATION_TYPE_ENTRY_BLOCK => self::NOTIFICATION_OBJECT_TYPE_ENTRY,
		self::NOTIFICATION_TYPE_ENTRY_UPDATE_THUMBNAIL => self::NOTIFICATION_OBJECT_TYPE_ENTRY,
		self::NOTIFICATION_TYPE_ENTRY_UPDATE_MODERATION => self::NOTIFICATION_OBJECT_TYPE_ENTRY,
		self::NOTIFICATION_TYPE_ENTRY_REPORT => self::NOTIFICATION_OBJECT_TYPE_ENTRY,
		self::NOTIFICATION_TYPE_KSHOW_ADD => self::NOTIFICATION_OBJECT_TYPE_KSHOW,
		self::NOTIFICATION_TYPE_KSHOW_UPDATE_INFO => self::NOTIFICATION_OBJECT_TYPE_KSHOW,
		self::NOTIFICATION_TYPE_KSHOW_UPDATE_PERMISSIONS => self::NOTIFICATION_OBJECT_TYPE_KSHOW,
		self::NOTIFICATION_TYPE_KSHOW_DELETE => self::NOTIFICATION_OBJECT_TYPE_KSHOW,
		self::NOTIFICATION_TYPE_KSHOW_RANK => self::NOTIFICATION_OBJECT_TYPE_KSHOW,
		self::NOTIFICATION_TYPE_KSHOW_BLOCK => self::NOTIFICATION_OBJECT_TYPE_KSHOW,
		self::NOTIFICATION_TYPE_USER_ADD => self::NOTIFICATION_OBJECT_TYPE_USER,
		self::NOTIFICATION_TYPE_USER_BANNED => self::NOTIFICATION_OBJECT_TYPE_USER,
		self::NOTIFICATION_TYPE_BATCH_JOB_STARTED => self::NOTIFICATION_OBJECT_TYPE_BATCH_JOB,
		self::NOTIFICATION_TYPE_BATCH_JOB_SUCCEEDED => self::NOTIFICATION_OBJECT_TYPE_BATCH_JOB,
		self::NOTIFICATION_TYPE_BATCH_JOB_FAILED => self::NOTIFICATION_OBJECT_TYPE_BATCH_JOB,
		self::NOTIFICATION_TYPE_BATCH_JOB_SIMILAR_EXISTS => self::NOTIFICATION_OBJECT_TYPE_BATCH_JOB,
		self::NOTIFICATION_TYPE_TEST => null ,
	);
	
	public static $NOTIFICATION_TYPE_MAP = array (
		self::NOTIFICATION_TYPE_ENTRY_ADD => "entry_add",
		self::NOTIFICATION_TYPE_ENTRY_UPDATE => "entry_update",
		self::NOTIFICATION_TYPE_ENTRY_UPDATE_PERMISSIONS => "entry_update_permissions" ,
		self::NOTIFICATION_TYPE_ENTRY_DELETE => "entry_delete" ,
		self::NOTIFICATION_TYPE_ENTRY_BLOCK => "entry_block" ,
		self::NOTIFICATION_TYPE_ENTRY_UPDATE_THUMBNAIL => "entry_update_thumbnail",
		self::NOTIFICATION_TYPE_ENTRY_UPDATE_MODERATION => "entry_update_moderation",
		self::NOTIFICATION_TYPE_ENTRY_REPORT => "entry_reported",
		self::NOTIFICATION_TYPE_KSHOW_ADD => "kshow_add" ,
		self::NOTIFICATION_TYPE_KSHOW_UPDATE_INFO => "kshow_update_info" ,
		self::NOTIFICATION_TYPE_KSHOW_UPDATE_PERMISSIONS => "kshow_update_permissions" ,
		self::NOTIFICATION_TYPE_KSHOW_DELETE => "kshow_delete" ,
		self::NOTIFICATION_TYPE_KSHOW_RANK => "kshow_rank" ,
		self::NOTIFICATION_TYPE_KSHOW_BLOCK => "kshow_block" ,
		self::NOTIFICATION_TYPE_USER_ADD => "user_add" ,
		self::NOTIFICATION_TYPE_USER_BANNED => "user_banned" ,
		self::NOTIFICATION_TYPE_BATCH_JOB_STARTED => "job_started",
		self::NOTIFICATION_TYPE_BATCH_JOB_SUCCEEDED => "job_succeeded",
		self::NOTIFICATION_TYPE_BATCH_JOB_FAILED => "job_failed",
		self::NOTIFICATION_TYPE_BATCH_JOB_SIMILAR_EXISTS => "job_similar_exists" ,
		self::NOTIFICATION_TYPE_TEST => "test" ,
	);
	
	private static $indicator = null;//= new myFileIndicator( "gogobatchjob" );
	
	public static function getNotificationTypeMap()
	{
		return self::$NOTIFICATION_TYPE_MAP;
	}
	
	public static function isEntryNotification  ( $type )
	{
		return ( $type >= self::NOTIFICATION_TYPE_ENTRY_ADD &&   $type <= 	self::NOTIFICATION_TYPE_ENTRY_UPDATE_THUMBNAIL );
	}

	public static function isKshowNotification  ( $type )
	{
		return ( $type >= self::NOTIFICATION_TYPE_KSHOW_ADD &&   $type <= 	self::NOTIFICATION_TYPE_KSHOW_BLOCK );
	}

	public function getTypeAsString()
	{
		return @self::$NOTIFICATION_TYPE_MAP[$this->getType()];
	}
	
	
	/* ------------------------ set of indicator functions --------------------------- */
	public static function isIndicatorSet ()
	{
		return self::getIndicator()->isIndicatorSet();
	}
	
	public static function addIndicator ( $id )
	{
		// TODO - remove the double indicator !
		self::getIndicator()->addIndicator( $id );
		self::getIndicator()->addIndicator( $id . "_"); // for now add an extra indicator 
	}
	
	
	public static function removeIndicator ( )
	{
		self::getIndicator()->removeIndicator();
	}
	
	private static function getIndicator()
	{
		if ( ! self::$indicator ) self::$indicator = new myFileIndicator( "gogonotifications" );
		return self::$indicator;
	}
	/**
	 * @param $sendType the $sendType to set
	 */
	public function setSendType($sendType)
	{
		$this->sendType = $sendType;
	}

	/**
	 * @return the $sendType
	 */
	public function getSendType()
	{
		return $this->sendType;
	}
	
	/* ------------------------ set of indicator functions --------------------------- */
	
	
	/**
	 * @var string
	 */
	private $userId;

	/**
	 * @var KalturaNotificationType
	 */
    private $type;

	/**
	 * @var KalturaNotificationSendType
	 */
    private $sendType;
    

	/**
	 * @var string
	 */
	private $objectId;  

	/**
	 * @var string
	 */   	
    private $data;
    
	/**
	 * @var int
	 */    
    private $numberOfAttempts;
    
	/**
	 * @var string
	 */    
    private $notificationResult;

	/**
	 * @var KalturaNotificationObjectType
	 */    
    private $objType;
    
    
    
	/**
	 * @return the $userId
	 */
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * @return the $type
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return the $objectId
	 */
	public function getObjectId()
	{
		return $this->objectId;
	}

	/**
	 * @return the $data
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @return the $numberOfAttempts
	 */
	public function getNumberOfAttempts()
	{
		return $this->numberOfAttempts;
	}

	/**
	 * @return the $notificationResult
	 */
	public function getNotificationResult()
	{
		return $this->notificationResult;
	}

	/**
	 * @return the $objType
	 */
	public function getObjType()
	{
		return $this->objType;
	}

	/**
	 * @param $userId the $userId to set
	 */
	public function setUserId($userId)
	{
		$this->userId = $userId;
	}

	/**
	 * @param $type the $type to set
	 */
	public function setType($type)
	{
		$this->type = $type;
		
		$this->setObjType(@self::$NOTIFICATION_OBJ_TYPE_MAP[$type]);
	}

	/**
	 * @param $objectId the $objectId to set
	 */
	public function setObjectId($objectId)
	{
		$this->objectId = $objectId;
	}

	/**
	 * @param $data the $data to set
	 */
	public function setData($data)
	{
		$this->data = $data;
	}

	/**
	 * @param $numberOfAttempts the $numberOfAttempts to set
	 */
	public function setNumberOfAttempts($numberOfAttempts)
	{
		$this->numberOfAttempts = $numberOfAttempts;
	}

	/**
	 * @param $notificationResult the $notificationResult to set
	 */
	public function setNotificationResult($notificationResult)
	{
		$this->notificationResult = $notificationResult;
	}

	/**
	 * @param $objType the $objType to set
	 */
	public function setObjType($objType)
	{
		$this->objType = $objType;
	}
   

    
}
