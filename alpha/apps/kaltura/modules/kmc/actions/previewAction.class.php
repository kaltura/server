<?php

require_once ( "kalturaAction.class.php" );

class previewAction extends kalturaAction
{
	public function execute ( ) 
	{
		$html5_version = kConf::get('html5_version');

		$uiconf_id = $this->getRequestParameter('uiconf_id');
		if(!$uiconf_id)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'uiconf_id');

		$uiConf = uiConfPeer::retrieveByPK($uiconf_id);
		if(!$uiConf)
			KExternalErrors::dieError(KExternalErrors::UI_CONF_NOT_FOUND);

		$partner_id = $this->getRequestParameter('partner_id', $uiConf->getPartnerId());
		if(!$partner_id)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'partner_id');

		$entry_id = $this->getRequestParameter('entry_id');
		if(!$entry_id)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'entry_id');

		$partner_host = myPartnerUtils::getHost($partner_id);
		$partner_cdnHost = myPartnerUtils::getCdnHost($partner_id);
	}
}
?>