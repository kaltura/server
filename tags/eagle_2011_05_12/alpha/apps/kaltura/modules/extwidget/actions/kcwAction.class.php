<?php

class kcwAction extends sfAction
{
	/**
	 * Will forward to the regular swf player according to the widget_id 
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
		if (!$subp_id)
			$subp_id = 0;
			
		
		$host = myPartnerUtils::getHost($partner_id);
		
		$ui_conf_swf_url = $uiConf->getSwfUrl();
		if (!$ui_conf_swf_url)
			$ui_conf_swf_url = "/swf/ContributionWizard.swf";
		
		if( kString::beginsWith( $ui_conf_swf_url , "http") )
		{
			$swf_url = 	$ui_conf_swf_url; // absolute URL 
		}
		else
		{
			$use_cdn = $uiConf->getUseCdn();
			$cdn_host = $use_cdn ?  myPartnerUtils::getCdnHost($partner_id) : myPartnerUtils::getHost($partner_id);
			$swf_url = 	$cdn_host . myPartnerUtils::getUrlForPartner( $partner_id , $subp_id ) .  $ui_conf_swf_url ; // relative to the current host 
		}
			
		$params = "contentUrl=".urlencode($swf_url).
			"&host=" . str_replace("http://", "", str_replace("https://", "", $host)).
			"&cdnHost=". str_replace("http://", "", str_replace("https://", "", myPartnerUtils::getCdnHost($partner_id))).
			"&uiConfId=" . $ui_conf_id;
			
		$wrapper_swf = myContentStorage::getFSFlashRootPath ()."/flexwrapper/".kConf::get('kcw_flex_wrapper_version')."/FlexWrapper.swf";
		$this->redirect(  $host . myPartnerUtils::getUrlForPartner( $partner_id , $subp_id ) . "$wrapper_swf?$params");
	}
}
?>
