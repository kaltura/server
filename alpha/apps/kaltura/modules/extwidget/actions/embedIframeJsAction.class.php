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
		$host = $partner_host;

		$url = null;
		$ui_conf_swf_url = $uiConf->getSwfUrl();
		if(kString::beginsWith($ui_conf_swf_url, "http"))
		{
			$url = 	$ui_conf_swf_url; // absolute URL
		}
		else
		{
			$use_cdn = $uiConf->getUseCdn();
			$host = $use_cdn ?  $partner_cdnHost : $partner_host;
			$url =  $host;
			$url .=  myPartnerUtils::getUrlForPartner($partner_id, $subp_id);
			$url .=  $ui_conf_swf_url;
		}
		
		$this->redirect($url);
	}
}

