<?php
class embedIframeJsAction extends sfAction
{
	/**
	 * Will forward to the regular swf player according to the widget_id 
	 */
	public function execute()
	{
		$uiconf_id = $this->getRequestParameter('uiconf_id');
		if(!$uiconf_id)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'uiconf_id');
			
		$uiConf = uiConfPeer::retrieveByPK($uiconf_id);
		if(!$uiConf)
			KExternalErrors::dieError(KExternalErrors::UI_CONF_NOT_FOUND);
			
		$partner_id = $this->getRequestParameter('partner_id', $uiConf->getPartnerId());
		if(!$partner_id)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'partner_id');

		$subp_id = $uiConf->getSubpId();
		if(!$subp_id)
		{
			$partner = PartnerPeer::retrieveByPK($partner_id);
			if(!$partner)
				KExternalErrors::dieError(KExternalErrors::PARTNER_NOT_FOUND);
				
			$subp_id = $partner->getSubpid();
		}
		
		$partner_host = myPartnerUtils::getHost($partner_id);
		$partner_cdnHost = myPartnerUtils::getCdnHost($partner_id);
		
		$html5_version = kConf::get('html5_version');

		$use_cdn = $uiConf->getUseCdn();
		$host = $use_cdn ?  $partner_cdnHost : $partner_host;
		
		$url =  $host;
		$url .=  "/html5/html5lib/v{$html5_version}/mwEmbedLoader.php";
		
		$this->redirect($url);
	}
}

