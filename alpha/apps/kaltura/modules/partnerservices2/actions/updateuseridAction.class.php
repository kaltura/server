<?php
require_once ( "myInsertEntryHelper.class.php");
require_once ( "myKshowUtils.class.php");
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class updateuseridAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "updateUserId",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						"user_id" => array ("type" => "string", "desc" => ""),
						"new_user_id" => array ("type" => "string", "desc" => ""),
						),
					"optional" => array (
						)
					),
				"out" => array (
					"user" => array ("type" => "PuserKuser", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_USER_ID , 
					APIErrors::USER_ALREADY_EXISTS_BY_SCREEN_NAME ,
				)
			);
	}

	protected function ticketType()	{		return self::REQUIED_TICKET_ADMIN;	}

	// ask to fetch the kuser from puser_kuser
	public function needKuserFromPuser ( ) 	{ 		return self::KUSER_DATA_NO_KUSER; 	}

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$user_id = $this->getPM ( "user_id" );
		$new_user_id = $this->getPM ( "new_user_id" );
		
		$target_puser_kuser = PuserKuserPeer::retrieveByPartnerAndUid($partner_id , null /* $subp_id */, $user_id , true );
		
		if ( ! $target_puser_kuser )
		{
			$this->addError ( APIErrors::INVALID_USER_ID , $user_id );
			return;
		}
		
		$new_puser_kuser = PuserKuserPeer::retrieveByPartnerAndUid($partner_id , null /* $subp_id */ , $new_user_id , true );
		
		if ( $new_puser_kuser )
		{
			$this->addError ( APIErrors::DUPLICATE_USER_BY_ID , $new_user_id );
			return;
		}
		
		$target_puser_kuser->setPuserId( $new_user_id );
		$target_puser_kuser->save();
		
		PuserKuserPeer::removeFromCache($target_puser_kuser);
		
		$kuser = $target_puser_kuser->getKuser();
		
		$wrapper = objectWrapperBase::getWrapperClass( $target_puser_kuser , objectWrapperBase::DETAIL_LEVEL_DETAILED);
		$wrapper->removeFromCache( "PuserKuser" , $target_puser_kuser->getId() );
		
		$this->addMsg ( "user" , $wrapper );
	}
}
?>