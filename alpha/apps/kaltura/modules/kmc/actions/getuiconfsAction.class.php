<?php
/**
 * @package    Core
 * @subpackage KMC
 */
class getuiconfsAction extends kalturaAction
{
	public function execute ( ) 
	{
		header('Access-Control-Allow-Origin:*');

		$this->partner_id = $this->getP ( "partner_id" );
		$this->ks = $this->getP ( "ks" );
		$type = $this->getP("type");
		
		$this->partner = PartnerPeer::retrieveByPK($this->partner_id);
		if (!$this->partner)
			KExternalErrors::dieError( KExternalErrors::PARTNER_NOT_FOUND );
					
		if (!$this->partner->validateApiAccessControl())
			KExternalErrors::dieError( KExternalErrors::SERVICE_ACCESS_CONTROL_RESTRICTED );
			
		$this->templatePartnerId = $this->partner ? $this->partner->getTemplatePartnerId() : 0;
		$this->isKDP3 = ($this->partner->getKmcVersion() != '1')? true: false;

		// FIXME: validate the ks!
		
		
		$partnerUiconfs = kmcUtils::getPartnersUiconfs($this->partner_id, $type);
		$partner_uiconfs_array = array();
		foreach($partnerUiconfs as $uiconf)
		{
			$uiconf_array = array();
			$uiconf_array["id"] = $uiconf->getId();
			$uiconf_array["name"] = $uiconf->getName();
			$uiconf_array["width"] = $uiconf->getWidth();
			$uiconf_array["height"] = $uiconf->getHeight();
			//$uiconf_array["swfUrlVersion"] = $uiconf->getSwfUrlVersion();
			$uiconf_array["swf_version"] = "v" . $uiconf->getSwfUrlVersion();
			$uiconf_array["html5Url"] = $uiconf->getHtml5Url();

			$partner_uiconfs_array[] = $uiconf_array;
		}
		
		// default uiconf array
		$this->kmc_swf_version = kConf::get('kmc_version');
		$kmcGeneralUiConf = kmcUtils::getAllKMCUiconfs('kmc',   $this->kmc_swf_version, $this->templatePartnerId);
		$kmcGeneralTemplateUiConf = kmcUtils::getAllKMCUiconfs('kmc',   $this->kmc_swf_version, $this->templatePartnerId);
		if($type == 'player')
		{
			$content_uiconfs_previewembed = kmcUtils::find_confs_by_usage_tag($kmcGeneralTemplateUiConf, "kmc_previewembed", true, $kmcGeneralUiConf);
		}
		else
		{
			$content_uiconfs_previewembed = kmcUtils::find_confs_by_usage_tag($kmcGeneralTemplateUiConf, "kmc_previewembed_list", true, $kmcGeneralUiConf);
		}
		
		$default_uiconfs_array = array();
		foreach($content_uiconfs_previewembed as $uiconf)
		{
			$uiconf_array = array();
			$uiconf_array["id"] = $uiconf->getId();
			$uiconf_array["name"] = $uiconf->getName();
			$uiconf_array["width"] = $uiconf->getWidth();
			$uiconf_array["height"] = $uiconf->getHeight();
			//$uiconf_array["swfUrlVersion"] = $uiconf->getSwfUrlVersion();
			$uiconf_array["swf_version"] = "v" . $uiconf->getSwfUrlVersion();
			$uiconf_array["html5Url"] = $uiconf->getHtml5Url();

			$default_uiconfs_array[] = $uiconf_array;
		}
		
		$kdp508_uiconfs = array();
		if($type == 'player' && $this->partner->getEnable508Players())
		{
			$kdp508_uiconfs = kmcUtils::getPlayerUiconfsByTag('kdp508');
		}

		// Add HTML5 v2.0.0 Preview Player
		$v2_preview_players = array();
		if( $type == 'player'&& PermissionPeer::isValidForPartner(PermissionName::FEATURE_HTML5_V2_PLAYER_PREVIEW, $this->partner_id)){
			$v2_preview_players = kmcUtils::getPlayerUiconfsByTag('html5_v2_preview');
		}
		
		$merged_list = array();
		if(count($default_uiconfs_array))
			foreach($default_uiconfs_array as $uiconf)
				$merged_list[] = $uiconf;
		if(count($kdp508_uiconfs))
			foreach($kdp508_uiconfs as $uiconf)
				$merged_list[] = $uiconf;
		if(count($v2_preview_players))
			foreach($v2_preview_players as $uiconf)
				$merged_list[] = $uiconf;			
		if(count($partner_uiconfs_array))
			foreach($partner_uiconfs_array as $uiconf)
				$merged_list[] = $uiconf;

		return $this->renderText(json_encode($merged_list));
	}
}
