<?php
require_once ( "myInsertEntryHelper.class.php");
require_once ( "myKshowUtils.class.php");
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class getuserAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "getUser",
				"desc" => "",
				"in" => array (
					"mandatory" => array ( 
						"user_id" => array ("type" => "integer", "desc" => ""),
						),
					"optional" => array (
						"detailed" => array("type" => "boolean", "desc" => "")
						)
					),
				"out" => array (
					"user" => array ("type" => "PuserKuser", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_USER_ID ,
				)
			); 
	}
	
	protected function ticketType()	{		return self::REQUIED_TICKET_ADMIN;	}
	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_NO_KUSER;	}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		// the relevant puser_kuser is the one from the user_id NOT the uid (which is the logged in user investigationg
		$user_id = $this->getPM ( "user_id" );
		$target_puser_kuser = PuserKuserPeer::retrieveByPartnerAndUid($partner_id , null , $user_id , true ); 
		$detailed = $this->getP ( "detailed" , false );
		
		if ( ! $target_puser_kuser )
		{
			$this->addError ( APIErrors::INVALID_USER_ID , $user_id );
		}
		else
		{
			$level = ( $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_DETAILED );
			$this->addMsg ( "user" , objectWrapperBase::getWrapperClass( $target_puser_kuser , $level ) );
		}
	}
}
?>