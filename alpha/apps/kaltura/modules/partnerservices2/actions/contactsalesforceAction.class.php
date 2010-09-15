<?php
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class contactsalesforceAction extends defPartnerservices2Action
{
	public function describe()
	{
		return false;
	}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		return false;
	}
	
	private function sendLeadToMarketo($lead_array)
	{
		return false;
	}
}
?>