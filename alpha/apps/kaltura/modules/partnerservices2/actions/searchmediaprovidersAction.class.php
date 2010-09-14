<?php
require_once ( "defPartnerservices2Action.class.php");

class searchmediaprovidersAction extends defPartnerservices2Action
{
	public function describe()
	{
		return array();		
	}
	
	// TODO - remove so this service will validate the session
	protected function ticketType ()
	{
		return self::REQUIED_TICKET_NONE;
	}
	
	// check to see if already exists in the system = ask to fetch the puser & the kuser
	// don't ask for  KUSER_DATA_KUSER_DATA - because then we won't tell the difference between a missing kuser and a missing puser_kuser
	public function needKuserFromPuser ( )
	{
		return self::KUSER_DATA_NO_KUSER;
	}

	/**
		the puser might not be a kuser in the system
	 */
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		self::$escape_text = true;
		
		$service_provider_list = myPartnerUtils::getMediaServiceProviders ( $partner_id , $subp_id );
		
		$this->addMsg( "config_" , $service_provider_list );
	}
}
?>