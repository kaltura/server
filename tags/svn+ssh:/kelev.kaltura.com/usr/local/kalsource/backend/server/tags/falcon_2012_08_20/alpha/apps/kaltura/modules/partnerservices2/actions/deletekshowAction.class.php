<?php
/**
 * After making sure the ticket is a valid admin ticket - the setrvice is allowed and no other validations should be done
 * 
 * @package api
 * @subpackage ps2
 */
class deletekshowAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "deleteKShow",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"kshow_id" => array ("type" => "string", "desc" => "")
						),
					"optional" => array (
						)
					),
				"out" => array (
					"deleted_kshow" => array ("type" => "kshow", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_KSHOW_ID ,
				)
			); 
	}
	
	protected function ticketType()			{		return self::REQUIED_TICKET_ADMIN;	}
	// ask to fetch the kuser from puser_kuser - so we can tel the difference between a 
	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_KUSER_ID_ONLY;	}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$kshow_id_to_delete = $this->getPM ( "kshow_id" );
		
		$kshow_to_delete = kshowPeer::retrieveByPK( $kshow_id_to_delete );
		
		if ( ! $kshow_to_delete )
		{
			$this->addError( APIErrors::INVALID_KSHOW_ID , $kshow_id_to_delete );
			return;		
		}

		$kshow_to_delete->delete();

		myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_KSHOW_DELETE , $kshow_to_delete );
		
		$this->addMsg ( "deleted_kshow" , objectWrapperBase::getWrapperClass( $kshow_to_delete , objectWrapperBase::DETAIL_LEVEL_REGULAR) );
	}
}
?>