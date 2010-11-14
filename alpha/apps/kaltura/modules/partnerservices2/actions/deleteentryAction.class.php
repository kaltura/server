<?php
require_once ( "myInsertEntryHelper.class.php");
require_once ( "myKshowUtils.class.php");
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

/**
 * After making sure the ticket is a valid admin ticket - the setrvice is allowed and no other validations should be done
 */
class deleteentryAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "deleteEntry",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"entry_id" => array ("type" => "string", "desc" => "")
						),
					"optional" => array (
						)
					),
				"out" => array (
					"deleted_entry" => array ("type" => "entry", "desc" => "")
					),
				"errors" => array (
					 APIErrors::INVALID_ENTRY_ID ,
					 APIErrors::CANNOT_DELETE_ENTRY ,
				)
			); 
	}
	
	protected function ticketType()
	{
		return self::REQUIED_TICKET_ADMIN;
	}

	// ask to fetch the kuser from puser_kuser - so we can tel the difference between a
	public function needKuserFromPuser ( )
	{
		return self::KUSER_DATA_KUSER_ID_ONLY;
	}

	protected function getObjectPrefix () { return "entry"; }

	protected function getCriteria (  ) { return null; }

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$prefix = $this->getObjectPrefix();
		$entry_id_to_delete = $this->getPM ( "{$prefix}_id" );

		$kshow_id_for_entry_id_to_delete = $this->getP ( "kshow_id" );
		$c = $this->getCriteria(); 
		if ( $c == null )
		{
			$entry_to_delete = entryPeer::retrieveByPK( $entry_id_to_delete );
		}
		else
		{
			$entry_to_delete = entryPeer::doSelectOne( $c );
		}
				
		if ( ! $entry_to_delete )
		{
			$this->addError( APIErrors::INVALID_ENTRY_ID , $prefix , $entry_id_to_delete );
			return;
		}

		if ( $kshow_id_for_entry_id_to_delete != null )
		{
			// match the kshow_id
			if (  $kshow_id_for_entry_id_to_delete != $entry_to_delete->getKshowId() )
			{
				$this->addError( APIErrors::CANNOT_DELETE_ENTRY , $entry_id_to_delete , $kshow_id_for_entry_id_to_delete  );
				return;
			}
		}

		myEntryUtils::deleteEntry( $entry_to_delete ); 
		
		/*
			All move into myEntryUtils::deleteEntry
		
			$entry_to_delete->setStatus ( entryStatus::DELETED );
			
			// make sure the moderation_status is set to moderation::MODERATION_STATUS_DELETE
			$entry_to_delete->setModerationStatus ( moderation::MODERATION_STATUS_DELETE ); 
			$entry_to_delete->setModifiedAt( time() ) ;
			$entry_to_delete->save();
			
			myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_DELETE , $entry_to_delete );
		*/
		
		$this->addMsg ( "deleted_" . $prefix  , objectWrapperBase::getWrapperClass( $entry_to_delete , objectWrapperBase::DETAIL_LEVEL_REGULAR ) );

	}
}
?>