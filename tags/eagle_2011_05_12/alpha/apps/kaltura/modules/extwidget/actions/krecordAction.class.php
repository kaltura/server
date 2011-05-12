<?php

class krecordAction extends sfAction
{
	/**
	 * Will forward to the uploader swf according to the ui_conf_id 
	 */
	public function execute()
	{
		$ui_conf_id = $this->getRequestParameter( "ui_conf_id" );
		
		$uiConf = uiConfPeer::retrieveByPK( $ui_conf_id );
		
		if ( !$uiConf )
		{
			die();	
		}
		
		$partner_id = $uiConf->getPartnerId();
		$subp_id = $uiConf->getSubpId();
		
		$host = requestUtils::getRequestHost();
		
		$ui_conf_swf_url = $uiConf->getSwfUrl();
		if (!$ui_conf_swf_url)
		{
			die();
		}
			
		if( kString::beginsWith( $ui_conf_swf_url , "http") )
		{
			$swf_url = 	$ui_conf_swf_url; // absolute URL 
		}
		else
		{
			$use_cdn = $uiConf->getUseCdn();
			$cdn_host = $use_cdn ?  myPartnerUtils::getCdnHost($partner_id) : myPartnerUtils::getHost($partner_id);;
			$swf_url = 	$cdn_host . myPartnerUtils::getUrlForPartner( $partner_id , $subp_id ) .  $ui_conf_swf_url ; // relative to the current host
		}
			
		$conf_vars = $uiConf->getConfVars();
		if ($conf_vars)
			$conf_vars = "&".$conf_vars;
			
		$params  = "host=" . $host.
			"&uiConfId=" . $ui_conf_id.
			$conf_vars;
			
		$this->redirect(  "$swf_url?$params");
	}
}
?>
