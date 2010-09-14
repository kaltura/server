<?php
require_once ( "myInsertEntryHelper.class.php");
require_once ( "myKshowUtils.class.php");
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class addkshowAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "addKShow",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						"kshow" 				=> array ("type" => "kshow", "desc" => "kshow"),
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
					APIErrors::DUPLICATE_KSHOW_BY_NAME
				)
			);
	}
/*
	protected function ticketType ()
	{
		return self::REQUIED_TICKET_ADMIN;
	}
*/
	// check to see if already exists in the system = ask to fetch the puser & the kuser
	// don't ask for  KUSER_DATA_KUSER_DATA - because then we won't tell the difference between a missing kuser and a missing puser_kuser
	public function needKuserFromPuser ( )
	{
		return self::KUSER_DATA_KUSER_ID_ONLY;
	}

	protected function addUserOnDemand ( )
	{
		return self::CREATE_USER_FROM_PARTNER_SETTINGS;
	}

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$kshows_from_db = null;
		// works in one of 2 ways:
		// 1. get no requested name - will create a new kshow and return its details
		// 2. get some name - tries to fetch by name. if already exists - return it

		// get the new properties for the kuser from the request
		$kshow = new kshow();

		$allow_duplicate_names = $this->getP ( "allow_duplicate_names" , true , true );
		if ( $allow_duplicate_names === "false" || $allow_duplicate_names === 0 ) $allow_duplicate_names = false;

		$return_metadata = $this->getP ( "metadata" , false );
		$detailed = $this->getP ( "detailed" , false );
		$level = ( $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR );

		$obj_wrapper = objectWrapperBase::getWrapperClass( $kshow , 0 );

		$fields_modified = baseObjectUtils::fillObjectFromMap ( $this->getInputParams() , $kshow , "kshow_" , $obj_wrapper->getUpdateableFields() );
		// check that mandatory fields were set
		// TODO
		$kshow->setName( trim ( $kshow->getName() ) );
		// ASSUME - the name is UNIQUE per partner_id !

		if ( $kshow->getName() )
		{
			if ( myPartnerUtils::shouldForceUniqueKshow( $partner_id , $allow_duplicate_names ) )
			{
				// in this case willsearch for an existing kshow with this name and return with an error if found
				$kshows_from_db = kshowPeer::getKshowsByName ( trim ( $kshow->getName() ) );
				if ( $kshows_from_db )
				{
					$kshow_from_db = $kshows_from_db[0];
					$this->addDebug ( "already_exists_objects" , count ( $kshows_from_db ) );
					$this->addError ( APIErrors::DUPLICATE_KSHOW_BY_NAME, $kshow->getName() ) ;// This field in unique. Please change ");
					if( myPartnerUtils::returnDuplicateKshow( $partner_id ))
					{
						$this->addMsg ( "kshow" , objectWrapperBase::getWrapperClass( $kshow_from_db , $level  ) );
					}
					return;
				}
			}
		}


		// the first kuser to create this kshow will be it's producer
		$producer_id =   $puser_kuser->getKuserId();
		$kshow->setProducerId( $producer_id );
		// moved to the update - where there is

		$kshow->setPartnerId( $partner_id );
		$kshow->setSubpId( $subp_id );
		$kshow->setViewPermissions( kshow::KSHOW_PERMISSION_EVERYONE );

		// by default the permissions should be public
		if ( $kshow->getPermissions () === null )
		{ 
			$kshow->setPermissions( myPrivilegesMgr::PERMISSIONS_PUBLIC );
		}
		
		// have to save the kshow before creating the default entries
		$kshow->save();
		$show_entry = $kshow->createEntry( entry::ENTRY_MEDIA_TYPE_SHOW , $producer_id , "&auto_edit.jpg" , $kshow->getName() ); // roughcut
		$kshow->createEntry( entry::ENTRY_MEDIA_TYPE_VIDEO , $producer_id ); // intro
/*
		$sample_text = $kshow->getName();
		$host = requestUtils::getHost();
*/
		$sample_text = "";
		myEntryUtils::modifyEntryMetadataWithText ( $show_entry , $sample_text , "" );

		// set the roughcut to false so the update iwll override with better data
		$kshow->setHasRoughcut( false );

		$kshow->initFromTemplate ( $producer_id , $sample_text);

		$kshow->save();

		myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_KSHOW_ADD , $kshow );

		$this->addMsg ( "kshow" , objectWrapperBase::getWrapperClass( $kshow ,  $level  ) );

		if ( $return_metadata )
		{
			$this->addMsg ( "metadata" , $kshow->getMetadata() );
		}

		$this->addDebug ( "added_fields" , $fields_modified );
		if ( $kshows_from_db )
			$this->addDebug ( "already_exists_objects" , count ( $kshows_from_db ) );

	}
}
?>