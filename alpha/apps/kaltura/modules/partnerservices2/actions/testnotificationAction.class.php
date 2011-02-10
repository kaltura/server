<?php
/**
 * @package api
 * @subpackage ps2
 */
class testnotificationAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "testNotification",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						),
					"optional" => array (
						)
					),
				"out" => array (
					"notifications" => array ("type" => "*notification", "desc" => ""),
					// hit result ?
					),
				"errors" => array (
				)
			); 
	}
	
	protected function ticketType()	{		return self::REQUIED_TICKET_REGULAR;	}
	// ask to fetch the kuser from puser_kuser
	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_NO_KUSER ;	}

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$prefix = null;
		$notData = new kNotificationJobData();
		$notData->setData('');
		$notData->setType(kNotificationJobData::NOTIFICATION_TYPE_TEST);
		$notData->setUserId($puser_id);
		$job = new BatchJob();
		$job->setId(kNotificationJobData::NOTIFICATION_TYPE_TEST + (int)time());
		$job->setData($notData);
		$job->setPartnerId($partner_id);
		
		$partner = PartnerPeer::retrieveByPK($partner_id);
		list ( $url , $signature_key ) = myNotificationMgr::getPartnerNotificationInfo ( $partner );
		
		list ( $params , $raw_siganture ) = myNotificationMgr::prepareNotificationData($url , $signature_key , $job , $prefix );
		
		$this->send($url, $params);
	}
	
	private function send($url, $params)
	{
		$ch = curl_init();
		
		$header = array("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Accept-Language: en-us,en;q=0.5", "Accept-Encoding: gzip,deflate", "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7", "Keep-Alive: 300", "Connection: keep-alive");
		
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params, null, "&"));
		
		curl_setopt($ch, CURLOPT_URL, $url);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, '');
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		$result = curl_exec($ch);
	}	
}
?>