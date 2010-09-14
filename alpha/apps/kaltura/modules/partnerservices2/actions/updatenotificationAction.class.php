<?php
require_once ( "defPartnerservices2Action.class.php");

class updatenotificationAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "updateNotification",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"notification" => array ("type" => "*notification", "desc" => ""),
						),
					"optional" => array (
						)
					),
				"out" => array (
					"notifications" => array ("type" => "*notification", "desc" => ""),
					),
				"errors" => array (
					APIErrors::INVALID_NOTIFICATION_ID , 
				 	APIErrors::NO_NOTIFICATIONS_UPDATED , 
				)
			); 
	}
	
	protected function ticketType()	{		return self::REQUIED_TICKET_ADMIN;	}
	// ask to fetch the kuser from puser_kuser
	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_NO_KUSER ;	}

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$notifications_updated = 0;
		$notifications = array();
		for ( $i=0 ; $i<= 50 ; ++$i )
		{
			$index = ( $i === 0 ? "" : $i ); // the first notification can be with no index 
			 
			$prefix = "notification{$index}";
			$notification_id = $this->getP ( "{$prefix}_id" );
			
			if ( $i >= 1 && empty ( $notification_id ) )  break;
			$target_notification = notificationPeer::retrieveByPK( $notification_id ); 
			
			if ( ! $target_notification )
			{
				$this->addError ( APIErrors::INVALID_NOTIFICATION_ID , $notification_id);
				continue;
			}
	
			$notification_update_data = new notification();
			$obj_wrapper = objectWrapperBase::getWrapperClass( $notification_update_data , 0 );
			
			$fields_modified = baseObjectUtils::fillObjectFromMap ( $this->getInputParams() , $notification_update_data , "{$prefix}_" , $obj_wrapper->getUpdateableFields() );
			
			if ( count ( $fields_modified ) > 0 )
			{
				if ( $notification_update_data )
				baseObjectUtils::fillObjectFromObject( $obj_wrapper->getUpdateableFields() , $notification_update_data , $target_notification , baseObjectUtils::CLONE_POLICY_PREFER_NEW , null , BasePeer::TYPE_PHPNAME );
			
				$target_notification->save();
			}
			
		//	$this->addMsg ( "{$prefix}" , objectWrapperBase::getWrapperClass( $target_notification , objectWrapperBase::DETAIL_LEVEL_DETAILED) );
			$notifications[] = $target_notification;
			$notifications_updated++;
		}
		
		$this->addMsg ( "notifications" , objectWrapperBase::getWrapperClass( $notifications  , objectWrapperBase::DETAIL_LEVEL_DETAILED) );
		
		if ( $notifications_updated == 0 )
		{
			$this->addError ( APIErrors::NO_NOTIFICATIONS_UPDATED );
		}
		else
		{
			$this->addDebug( "notifications_updated" ,$notifications_updated );
		}

	}
}
?>