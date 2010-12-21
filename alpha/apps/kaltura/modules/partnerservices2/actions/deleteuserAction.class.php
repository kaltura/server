<?php
require_once ( "myInsertEntryHelper.class.php");
require_once ( "myKshowUtils.class.php");
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class deleteuserAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "deleteUser",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						"user_id" => array ("type" => "integer", "desc" => "")
						),
					"optional" => array (
						)
					),
				"out" => array (
					"deleted_user" => array ("type" => "PuserKuser", "desc" => "")
					),
				"errors" => array (
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

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$puser_id_to_delete = $this->getPM ( "user_id" );

		$puser_kuser_to_delete = PuserKuserPeer::retrieveByPartnerAndUid ( $partner_id , null /*$subp_id*/,  $puser_id_to_delete , true );
		if ( !$puser_kuser_to_delete )
		{
			$this->addError( APIErrors::INVALID_USER_ID , $puser_id_to_delete );
			return;
		}

		$kuser = $puser_kuser_to_delete->getKuser();
		if ( $kuser )
		{
//			$this->addMsg ( "deleted_kuser" , objectWrapperBase::getWrapperClass( $kuser , objectWrapperBase::DETAIL_LEVEL_REGULAR ) );

			try {
				$kuser->setStatus(KalturaUserStatus::DELETED);
			}
			catch (kUserException $e) {
				$code = $e->getCode();
				if ($code == kUserException::CANNOT_DELETE_ROOT_ADMIN_USER) {
					$this->addException( APIErrors::CANNOT_DELETE_ROOT_ADMIN_USER);
					return null;
				}
				throw $e;			
			}	
		}
		$puser_kuser_to_delete->delete();

		$this->addMsg ( "deleted_user" , objectWrapperBase::getWrapperClass( $puser_kuser_to_delete , objectWrapperBase::DETAIL_LEVEL_DETAILED) );

	}
}
?>