<?php
/**
 * @package    Core
 * @subpackage KMC
 */
require_once ( "kalturaAction.class.php" );

/**
 * @package Core
 * @subpackage KMC
 */
class previewAction extends kalturaAction
{
	public function execute ( ) 
	{
		$this->uiconf_id = intval($this->getRequestParameter('uiconf_id'));
		if(!$this->uiconf_id)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'uiconf_id');

		$this->uiConf = uiConfPeer::retrieveByPK($this->uiconf_id);
		if(!$this->uiConf)
			KExternalErrors::dieError(KExternalErrors::UI_CONF_NOT_FOUND);

		$this->partner_id = intval($this->getRequestParameter('partner_id', $this->uiConf->getPartnerId()));
		if(!$this->partner_id)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'partner_id');

		// Single Player parameters
		$this->entry_id = htmlspecialchars($this->getRequestParameter('entry_id'));
		if( $this->entry_id ) {
			$entry = entryPeer::retrieveByPK($this->entry_id);
			if( $entry ) {
				$this->entry_name = $entry->getName();
				$this->entry_description = $entry->getDescription();
				$this->entry_thumbnail_url = $entry->getThumbnailUrl();

				$flavor_tag = $this->getRequestParameter('flavor_tag', 'iphone');
				$flavor_assets = assetPeer::retrieveReadyFlavorsByEntryIdAndTag($this->entry_id, $flavor_tag);
				$flavor_asset = reset($flavor_assets);
				/* @var $flavor_asset flavorAsset */
				$this->flavor_asset_id = null;
				if( $flavor_asset ) {
					$this->flavor_asset_id = $flavor_asset->getId();
				}
			} else {
				$this->entry_id = null;
			}
		}
		
		$this->delivery_type = $this->getRequestParameter('delivery');

		// Playlist Parameters
		$this->playlist_id = $this->getRequestParameter('playlist_id');
		$this->playlist_name = htmlspecialchars($this->getRequestParameter('playlist_name'));

		$this->partner_host = myPartnerUtils::getHost($this->partner_id);
		$this->partner_cdnHost = myPartnerUtils::getCdnHost($this->partner_id);
		$this->secure_host = kConf::get('cdn_host_https');
	}
}
