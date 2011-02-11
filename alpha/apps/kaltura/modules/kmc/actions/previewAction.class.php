<?php

require_once ( "kalturaAction.class.php" );

class previewAction extends kalturaAction
{
	public function execute ( ) 
	{
		$this->html5_version = kConf::get('html5_version');

		$this->uiconf_id = $this->getRequestParameter('uiconf_id');
		if(!$this->uiconf_id)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'uiconf_id');

		$this->uiConf = uiConfPeer::retrieveByPK($this->uiconf_id);
		if(!$this->uiConf)
			KExternalErrors::dieError(KExternalErrors::UI_CONF_NOT_FOUND);

		$this->partner_id = $this->getRequestParameter('partner_id', $this->uiConf->getPartnerId());
		if(!$this->partner_id)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'partner_id');

		$this->entry_id = $this->getRequestParameter('entry_id');
		if(!$this->entry_id)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'entry_id');

		$this->partner_host = myPartnerUtils::getHost($this->partner_id);
		$this->partner_cdnHost = myPartnerUtils::getCdnHost($this->partner_id);
	}
}
?>