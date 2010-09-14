<?php
require_once ( "myInsertEntryHelper.class.php");
require_once ( "myKshowUtils.class.php");
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class updatekshowAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "updateKShow",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"kshow_id"				=> array ("type" => "string", "desc" => ""),
						"kshow" 				=> array ("type" => "kshow", "desc" => ""),
						),
					"optional" => array (
						"detailed" 				=> array ("type" => "boolean", "desc" => ""),
						"allow_duplicate_names" => array ("type" => "boolean", "desc" => "")
						)
					),
				"out" => array (
					"kshow" => array ("type" => "kshow", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_USER_ID , 
					APIErrors::INVALID_KSHOW_ID ,
					APIErrors::DUPLICATE_KSHOW_BY_NAME ,
					APIErrors::ERROR_KSHOW_ROLLBACK
				)
			); 
	}
	
	// ask to fetch the kuser from puser_kuser
	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_KUSER_ID_ONLY;	}
	public function requiredPrivileges () { return "edit:<kshow_id>" ; }

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		if ( ! $puser_kuser )
		{
			$this->addError ( APIErrors::INVALID_USER_ID ,  $puser_id );
			return;
		}

		// get the new properties for the kshow from the request
		$kshow_update_data = new kshow();

		$start_obj_creation = microtime( true );
		$kshow = new kshow();
		$obj_wrapper = objectWrapperBase::getWrapperClass( $kshow  , 0 );
//		$this->addDebug ( "timer_getWrapperClass1" , ( microtime( true ) - $start_obj_creation ) );

		$timer = microtime( true );
		$fields_modified = baseObjectUtils::fillObjectFromMap ( $this->getInputParams() ,
			$kshow ,
			"kshow_" ,
			$obj_wrapper->getUpdateableFields() );

//		$this->addDebug ( "timer_fillObjectFromMap" , ( microtime( true ) - $timer ) );

		$kshow->setName( trim ( $kshow->getName() ) );

		$kshow_id = $this->getPM ( "kshow_id");
		$detailed = $this->getP ( "detailed" , false );
		$allow_duplicate_names = $this->getP ( "allow_duplicate_names" , true , true );
		if ( $allow_duplicate_names === "false" || $allow_duplicate_names === 0 ) $allow_duplicate_names = false;

		if ( count ( $fields_modified ) > 0 )
		{
			$timer = microtime( true );
			$kshow_from_db = kshowPeer::retrieveByPK( $kshow_id );
			if ( ! $kshow_from_db )
			{
				// kshow with this id does not exists in the DB
				$this->addError ( APIErrors::INVALID_KSHOW_ID ,  $kshow_id );

				return;
			}

			if ( ! $this->isOwnedBy ( $kshow_from_db , $puser_kuser->getKuserId() ) )
				$this->verifyPrivileges ( "edit" , $kshow_id ); // user was granted explicit permissions when initiatd the ks

							
			if ( myPartnerUtils::shouldForceUniqueKshow( $partner_id , $allow_duplicate_names ) )
			{
				$kshow_with_name_from_db = kshowPeer::getFirstKshowByName( $kshow->getName() );
				if ( $kshow_with_name_from_db && $kshow_with_name_from_db->getId() != $kshow_id )
				{
					$this->addError( APIErrors::DUPLICATE_KSHOW_BY_NAME ,   $kshow->getName() );
					$level = ( $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR );
					if( myPartnerUtils::returnDuplicateKshow( $partner_id ))
					{
						$this->addMsg ( "kshow" , objectWrapperBase::getWrapperClass( $kshow_from_db , $level  ) );
					}					
					return;
				}
			}

			$this->addMsg ( "old_kshow" , objectWrapperBase::getWrapperClass( $kshow_from_db->copy() , objectWrapperBase::DETAIL_LEVEL_REGULAR ) );

//			$this->addDebug ( "timer_db_retrieve" , ( microtime( true ) - $timer ) );

			$timer = microtime( true );
			// copy relevant fields from $kshow -> $kshow_update_data
			baseObjectUtils::fillObjectFromObject( $obj_wrapper->getUpdateableFields() ,
				$kshow ,
				$kshow_from_db ,
				baseObjectUtils::CLONE_POLICY_PREFER_NEW , null , BasePeer::TYPE_PHPNAME );

//			$this->addDebug ( "timer_fillObjectFromObject" , ( microtime( true ) - $timer ) );

			$timer = microtime( true );

			// TODO - move to some generic place myKshowUtils / kshow.php
			// TODO - this should be called only for the first time or whenever the user wants to force overriding the sample_text
			$force_sample_text = $this->getP ( "force_sample_text" , false );
			$force_sample_text = false;

			$kuser_id = $puser_kuser->getKuserId();
/*
			$sample_text = "This is a collaborative video for &#xD;'" . $kshow_from_db->getIndexedCustomData3() . "'.&#xD;Click 'Add to Video' to get started";
			$kshow_from_db->initFromTemplate ( $kuser_id ,$sample_text );
*/
			// be sure to save the $kshow_from_db and NOT $kshow - this will create a new entry in the DB
			$kshow_from_db->save();
			
			// update the name of the roughcut too
			$show_entry_id = $kshow_from_db->getShowEntryId();
			$showEntry = entryPeer::retrieveByPK($show_entry_id);
			if ($showEntry)
			{
				$showEntry->setName($kshow_from_db->getName());
				$showEntry->save();
			}


			// TODO - decide which of the notifications should be called
			myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_KSHOW_UPDATE_INFO , $kshow_from_db );
			// or
			//myNotificationMgr::createNotification( notification::NOTIFICATION_TYPE_KSHOW_UPDATE_PERMISSIONS , $kshow_from_db );

//			$this->addDebug ( "timer_db_save" , ( microtime( true ) - $timer ) );


			$end_obj_creation = microtime( true );
			$this->addDebug ( "obj_creation_time" , ( $end_obj_creation - $start_obj_creation ) );
		}
		else
		{
			$kshow_from_db = $kshow;
			// no fiends to update !
		}


		// see if trying to rollback
		$desired_version = $this->getP ( "kshow_version");
		if ( $desired_version )
		{
			$result = $kshow_from_db->rollbackVersion ( $desired_version );

			if ( ! $result )
			{
				$this->addError ( APIErrors::ERROR_KSHOW_ROLLBACK , $kshow_id , $desired_version);
			}
		}

		$this->addMsg ( "kshow" , objectWrapperBase::getWrapperClass( $kshow_from_db , objectWrapperBase::DETAIL_LEVEL_REGULAR ) );
		$this->addDebug ( "modified_fields" , $fields_modified );

	}
}
?>