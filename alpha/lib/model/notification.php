<?php

/**
 * Subclass for representing a row from the 'notification' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class notification extends Basenotification
{
	const MAX_SEND_RETRIES = 10; 
	
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
	
	const NOTIFICATION_STATUS_PENDING = 1;
	const NOTIFICATION_STATUS_SENT = 2;
	const NOTIFICATION_STATUS_ERROR = 3;
	const NOTIFICATION_STATUS_SHOULD_RESEND = 4; // ??
	const NOTIFICATION_STATUS_ERROR_RESENDING = 5;
	const NOTIFICATION_STATUS_SENT_SYNCH = 6;
	const NOTIFICATION_STATUS_QUEUED = 7;
		
	const NOTIFICATION_RESULT_OK = 0; 
	const NOTIFICATION_RESULT_ERROR_RETRY = -1;
	const NOTIFICATION_RESULT_ERROR_NO_RETRY = -2;
		
	const NOTIFICATION_OBJECT_TYPE_ENTRY = 1;
	const NOTIFICATION_OBJECT_TYPE_KSHOW = 2;
	const NOTIFICATION_OBJECT_TYPE_USER = 3;	
	const NOTIFICATION_OBJECT_TYPE_BATCH_JOB = 4;	
	 
	public static $NOTIFICATION_TYPE_MAP = null;
	
	private static $indicator = null;//= new myFileIndicator( "gogobatchjob" );

	private static function initNotificationTypeMap()
	{
		if ( self::$NOTIFICATION_TYPE_MAP == null )
		{
			self::$NOTIFICATION_TYPE_MAP = array (
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
		}
	}
	
	public static function getNotificationTypeMap()
	{
		self::initNotificationTypeMap(); 	
		return self::$NOTIFICATION_TYPE_MAP;
	}
	
	public function __construct( )
	{
		self::initNotificationTypeMap();
	}
	
	public static function isEntryNotification  ( $type )
	{
		return ( $type >= self::NOTIFICATION_TYPE_ENTRY_ADD &&   $type <= 	self::NOTIFICATION_TYPE_ENTRY_UPDATE_THUMBNAIL );
	}

	public static function isKshowNotification  ( $type )
	{
		return ( $type >= self::NOTIFICATION_TYPE_KSHOW_ADD &&   $type <= 	self::NOTIFICATION_TYPE_KSHOW_BLOCK );
	}
	
	
	public function save(PropelPDO $con = null)
	{
		$is_new = $this->isNew() ;

		if ( $this->isNew() )
		{
			$this->setDc ( kDataCenterMgr::getCurrentDcId());
		}
		
		if ( self::isEntryNotification( $this->getType()) )
		{
			$this->setObjectType( self::NOTIFICATION_OBJECT_TYPE_ENTRY);
		}
		else
		{
			$this->setObjectType( self::NOTIFICATION_OBJECT_TYPE_KSHOW);
		}
		
		$res = parent::save( $con );
		// when new object or status is pending - add the indicator for the batch job to start running
		if ( $is_new || 
			( 	$this->getStatus() == self::NOTIFICATION_STATUS_PENDING ||
				$this->getStatus() == self::NOTIFICATION_STATUS_SHOULD_RESEND  ) )
		{
			self::addIndicator( $this->getId() );
		}
		return $res;
	}

	
	public function getTypeAsString()
	{
		return @self::$NOTIFICATION_TYPE_MAP[$this->getType()];
	}
	
	// the notification info is an unserialized version of the data
	public function getObjectInfo ()
	{
		$res = myNotificationMgr::getDataAsArray( $this->getNotificationData() ) ;
		return $res;
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
	/* ------------------------ set of indicator functions --------------------------- */

	private $m_partner;
	public function getPartner ()
	{
		if ( $this->m_partner == null )
		{
			$this->m_partner = PartnerPeer::retrieveByPK( $this->getPartnerId() );
		}
		return $this->m_partner;
	}
	
	public function isRetriesExceeded()
	{
		return ($this->execution_attempts >= self::MAX_EXECUTION_ATTEMPTS);
	}
}
