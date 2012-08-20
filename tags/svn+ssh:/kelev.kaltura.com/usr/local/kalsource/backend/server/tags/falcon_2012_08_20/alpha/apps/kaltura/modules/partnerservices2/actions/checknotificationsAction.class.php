<?php
/**
 * @package api
 * @subpackage ps2
 */
class checknotificationsAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "checkNotifications",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"notification_ids" => array ("type" => "string", "desc" => ""),
						),
					"optional" => array (
						"separator" => array ("type" => "string", "default" => ",", "desc" => ""),
						"detailed" => array ("type" => "boolean", "desc" => "")
						)
					),
				"out" => array (
					//"entries" => array ("type" => "*entry", "desc" => "")
					"done" => array ("type" => "integer", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_ENTRY_IDS ,
				)
			); 
	}
	
	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_NO_KUSER;	}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$this->addMsg ( "done" ,"1" );
		
		$notification_ids = $this->getPM ( "notification_ids" );
		$detailed = $this->getP ( "detailed" , false );
		$separator = $this->getP ( "separator" , "," );
		$retriesCount = $this->getP ( "retriesCount" , 0 );
		$retriesTotal = $this->getP ( "retriesTotal" , 0 );

		$id_arr = explode ( $separator , $notification_ids );
		$limit = min(50, count($id_arr));
		$id_arr = array_splice( $id_arr , 0 , $limit );

		$c = new Criteria();
		$c->add(notificationPeer::ID, $id_arr, Criteria::IN);
		$c->add(notificationPeer::STATUS, BatchJob::BATCHJOB_STATUS_FINISHED);
		
		$sentCount = notificationPeer::doCount($c);
		
		if ($retriesTotal && ($retriesCount + 1) >= $retriesTotal)
			$done = 1;
		else
			$done = $sentCount == $limit;
		
		$this->addMsg ( "done" , $done ? "1" : "0" );
	}
}
?>