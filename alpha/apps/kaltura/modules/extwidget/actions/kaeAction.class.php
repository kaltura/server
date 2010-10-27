<?php

class kaeAction extends sfAction
{
	
	/**
	 * Will forward to the advanced editor swf according to the ui_conf_id 
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
		
		$host = myPartnerUtils::getHost($partner_id);
		
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
			$cdn_host = $use_cdn ?  myPartnerUtils::getCdnHost($partner_id) : myPartnerUtils::getHost($partner_id);
			$swf_url = 	$cdn_host . myPartnerUtils::getUrlForPartner( $partner_id , $subp_id ) . $ui_conf_swf_url ; // relative to the current host 
		}
			
		$params = "contentUrl=".urlencode($swf_url).
			"&host=" . str_replace("http://", "", str_replace("https://", "", myPartnerUtils::getHost($partner_id))).
			"&cdnHost=". str_replace("http://", "", str_replace("https://", "", myPartnerUtils::getCdnHost($partner_id))).
			"&uiConfId=" . $ui_conf_id . "&disableurlhashing=".kConf::get('disable_url_hashing');
		
		$wrapper_swf = myContentStorage::getFSFlashRootPath ()."/flexwrapper/".kConf::get('editors_flex_wrapper_version')."/FlexWrapper.swf";
		$this->redirect(  $cdn_host . myPartnerUtils::getUrlForPartner( $partner_id , $subp_id ) . "$wrapper_swf?$params");
	}
}
?>
