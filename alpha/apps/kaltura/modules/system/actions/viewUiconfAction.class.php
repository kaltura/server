<?php

require_once ( "kalturaSystemAction.class.php" );

class viewUiconfAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();
		myDbHelper::$use_alternative_con = null;
		
		$partnerId = $this->getRequestParameter("partnerId", null);
		
		if ($partnerId !== null)
			$partnerId = (integer)$partnerId;
		
		$c = new Criteria();
		$c->add(uiConfPeer::PARTNER_ID, $partnerId);
		$c->addDescendingOrderByColumn(uiConfPeer::CREATED_AT);
		$this->uiconfs = uiConfPeer::doSelect($c);
		
		$partner = PartnerPeer::retrieveByPK($partnerId);
		
		$this->partner = $partner;
		$this->partnerId = $partnerId;
	}
}

?>