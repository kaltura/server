<?php

/**
 * Subclass for representing a row from the 'moderation' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class moderation extends Basemoderation
{
	const MODERATION_OBJECT_TYPE_KSHOW = 1;
	const MODERATION_OBJECT_TYPE_ENTRY = 2;
	const MODERATION_OBJECT_TYPE_USER = 3;
	
	const MODERATION_STATUS_PENDING = 1; 	// the object is waiting fore review - the object status is moderate - it cannot be viewd now
	const MODERATION_STATUS_APPROVED =   2;   	// the object was in the moderation list and was approved 
	const MODERATION_STATUS_BLOCK =   3;   	// the object was in the moderation list and was blocked
	const MODERATION_STATUS_DELETE =  4;  	// the object was in the moderation list and was deleted
	const MODERATION_STATUS_REVIEW =  5; 	// some user reported the object - it is waiting for a review. the object's tatus has not yet changed - it might be viewable  
	const MODERATION_STATUS_AUTO_APPROVED = 6; 	// the entry was automatically approved

	
	private $m_object = null;
	private static $MODERATION_TYPE_MAP = null;
	
	public function __construct( )
	{
		self::initModerationTypeMap();
	}
	
	/**
	 * Before saving - if new moderation - update the moderation count
	 */
	public function save(PropelPDO $con = null)
	{
		if ( $this->getObjectType()  == self::MODERATION_OBJECT_TYPE_ENTRY )
		{
			$entry = $this->getObject();
			if ( $entry )
			{
				if ( $this->isNew() )
				{
					// a new moderation - update the moderation_count of the entry
					$entry->incModerationCount ( );
				}
				// whether new or not - update the moderation_status of the entry to be the current status	
				// TODO - decide what status should effect the entry !!
				$entry->setModerationStatus( $this->getStatus() );	
				$entry->save();		
			}
			else
			{
				// Big problem !			
			}
		}
		$res = parent::save( $con );
		return $res;
	}
	
	public function getObject ( )
	{
		if ( $this->m_object ) return $this->m_object;
		
		$object_id = $this->object_id;
		if ( $object_id == null ) return null;
		
		switch ( $this->getObjectType()  )
		{
			case self::MODERATION_OBJECT_TYPE_KSHOW:
				$this->m_object = kshowPeer::retrieveByPK( $object_id );
				break;
			case self::MODERATION_OBJECT_TYPE_ENTRY:
				// be able to fetch entries that are deleted
				entryPeer::allowDeletedInCriteriaFilter();
				$this->m_object = entryPeer::retrieveByPK ( $object_id );
				entryPeer::blockDeletedInCriteriaFilter();
				break;
			case self::MODERATION_OBJECT_TYPE_USER:
				// $object_id is the puser_id
				$puser_kuser = PuserKuserPeer::retrieveByPartnerAndUid( $this->getPartnerId() , NULL , $object_id , true );
				if ( $puser_kuser && $puser_kuser->getKuser() ) $this->m_object = $puser_kuser->getKuser();
//				$this->m_object = kuserPeer::retrieveByPK( $object_id );
				break;
		}
		
		return $this->m_object ;
	}

	/**
	 * Will update the status of the moderation AND the status of the referenced object 
	 */
	public function updateStatus ($new_status)
	{
		$obj = $this->getObject(); // will load the object to $this->m_object;
		if ( $obj )
		{
			$current_status = $obj->getModerationStatus();
			
			if ( $new_status == moderation::MODERATION_STATUS_REVIEW && 
				( $current_status == moderation::MODERATION_STATUS_BLOCK || $current_status == moderation::MODERATION_STATUS_DELETE ) 
				)
			{
				// 	don't change the status if the $current_status is MODERATION_STATUS_BLOCK or MODERATION_STATUS_DELETE
				return;
			}
			else
			{
				$obj->moderate($new_status); // let the moderated object do its logic
			}
		}
		$this->setStatus($new_status); // set status on the moderation row
		$this->save();
	}	
	
	
	public function getObjectTypeAsString ()
	{
		return @self::$MODERATION_TYPE_MAP[$this->getObjectType()];
	}
	
	private static function initModerationTypeMap()
	{
		if ( self::$MODERATION_TYPE_MAP == null )
		{
			self::$MODERATION_TYPE_MAP = array (
				self::MODERATION_OBJECT_TYPE_KSHOW => "kshow",
				self::MODERATION_OBJECT_TYPE_ENTRY => "entry" ,
				self::MODERATION_OBJECT_TYPE_USER => "user" ,
			);
		}
	}	
}
