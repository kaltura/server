<?php
require_once ( "defPartnerservices2Action.class.php");

class updatekshowownerAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "updateKshowOwner",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"kshow_id" => array ("type" => "string", "desc" => "")
						),
					"optional" => array (
						"detailed" => array ("type" => "boolean", "desc" => "")
						)
					),
				"out" => array (
					"kshow" => array ("type" => "kshow", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_KSHOW_ID,
					APIErrors::INVALID_USER_ID,
				)
			); 
	}
	
	protected function ticketType ()
	{
		return self::REQUIED_TICKET_ADMIN;
	}

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
		$kshow_id = $this->getPM ( "kshow_id" );
		$target_puser_id = $this->getPM ( "user_id" );
		$detailed = $this->getP ( "detailed" , false );
		$kshow_indexedCustomData3 = $this->getP ( "indexedCustomData3" );
		$kshow = null;
		if ( $kshow_id )
		{
			$kshow = kshowPeer::retrieveByPK( $kshow_id );
		}
		elseif ( $kshow_indexedCustomData3 )
		{
			$kshow = kshowPeer::retrieveByIndexedCustomData3( $kshow_indexedCustomData3 );
		}

		if ( ! $kshow )
		{
			$this->addError ( APIErrors::INVALID_KSHOW_ID , $kshow_id );
		}
		else
		{
			$new_puser_kuser = PuserKuserPeer::retrieveByPartnerAndUid( $partner_id , null , $target_puser_id );
			if ( ! $new_puser_kuser )
			{
				$this->addError ( APIErrors::INVALID_USER_ID , $target_puser_id );
				return;
			}
 			$kshow->setProducerId ( $new_puser_kuser->getKuserId() );
 			$kshow->save();

 			$level = ( $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR );
			$wrapper = objectWrapperBase::getWrapperClass( $kshow , $level );
			// TODO - remove this code when cache works properly when saving objects (in their save method)
			$wrapper->removeFromCache( "kshow" , $kshow->getId() );
			$this->addMsg ( "kshow" , $wrapper ) ;
		}
	}
}
?>