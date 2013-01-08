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

		// Playlist Parameters
		$this->playlist_id = htmlspecialchars($this->getRequestParameter('playlist_id'));
		$this->playlist_name = htmlspecialchars($this->getRequestParameter('playlist_name'));

		$this->partner_host = myPartnerUtils::getHost($this->partner_id);
		$this->partner_cdnHost = myPartnerUtils::getCdnHost($this->partner_id);

		$embed_host = (kConf::hasParam('cdn_api_host')) ? kConf::get('cdn_api_host') : kConf::get('www_host');
		$embed_host_https = (kConf::hasParam('cdn_api_host_https')) ? kConf::get('cdn_api_host_https') : kConf::get('www_host');

		// Check if HTTPS enabled and set protocol
		$https_enabled = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? true : false;
		$protocol = ($https_enabled) ? 'https' : 'http';		

		// Set base URL for script tags
		$baseUrl = $protocol . '://' . (($https_enabled) ? $embed_host_https : $embed_host);

		// Script URL
		$this->scriptUrl = $baseUrl . "/p/". $this->partner_id ."/sp/". $this->partner_id ."00/embedIframeJs/uiconf_id/". $this->uiconf_id ."/partner_id/". $this->partner_id;

		// Build SWF Path
		$swfPath = "/index.php/kwidget";
		$swfPath .= "/cache_st/" . (time()+(60*15));
		$swfPath .= "/wid/_" . $this->partner_id;
		$swfPath .= "/uiconf_id/" . $this->uiconf_id;
		if( $this->entry_id ) {
			$swfPath .= "/entry_id/" . $this->entry_id;
		}
		// Set SWF URLs
		$this->swfUrl = $this->partner_host . $swfPath;
		$this->swfSecureUrl = 'https://' . kConf::get('cdn_host_https') . $swfPath;

		// Get delivery types from player.ini
		$map = kConf::getMap('players');
		$deliveryTypes = $map['delivery_types'];

		$flashVars = array();
		if( isset($deliveryTypes[$this->getRequestParameter('delivery')]) && 
				isset($deliveryTypes[$this->getRequestParameter('delivery')]['flashvars']) ) {
			$flashVars = $deliveryTypes[$this->getRequestParameter('delivery')]['flashvars'];
		}

		if( $this->playlist_id || ! $this->entry_id ) {
			$this->entry_name = 'Kaltura Player';
			$this->entry_description = '';
		}

		if( $this->playlist_id && $this->playlist_id != 'multitab_playlist') {
			// build playlist url
			$playlist_url = $this->partner_host ."/index.php/partnerservices2/executeplaylist?";
			$playlist_url .= "partner_id=" . $this->partner_id . "&subp_id=" . $this->partner_id . "00&format=8&ks={ks}&playlist_id=" . $this->playlist_id;

			// Add playlist flashVars
			$flashVars["playlistAPI.autoInsert"] = "true";
			$flashVars["playlistAPI.kpl0Name"] = $this->playlist_name;
			$flashVars["playlistAPI.kpl0Url"] = urlencode($playlist_url);
		}

		// Transform flashvars array to string
		$this->flashVarsString = $this->flashVarsToString($flashVars);

		// URL to this page
		$port = ($_SERVER["SERVER_PORT"] != "80") ? ":".$_SERVER["SERVER_PORT"] : '';
		$this->pageURL = $protocol . '://' . $_SERVER["SERVER_NAME"] . $port . $_SERVER["REQUEST_URI"];

		 //$_SERVER['PATH_INFO']
		if( isset($this->flavor_asset_id) ) {
			$this->flavorUrl = $this->partner_host . '/p/'. $this->partner_id .'/sp/' . $this->partner_id . '00/playManifest/entryId/' . $this->entry_id . '/flavorId/' . $this->flavor_asset_id . '/format/url/protocol/' . $protocol . '/a.mp4';
		}

		$this->embed = ($this->getRequestParameter('embed')) ? $this->getRequestParameter('embed') : 'legacy';

		// If case of auto embed, append extra params to script url
		if( $this->embed == 'auto' ) {
			$append = '?autoembed=true&playerId=kaltura_player';
			$append .= '&width=' . $this->uiConf->getWidth() . '&height=' . $this->uiConf->getHeight();
			$append .= ($this->entry_id) ? '&entry_id=' . $this->entry_id : '';
			$append .= '&' . $this->flashVarsToString($flashVars, 'flashvars');
			$this->scriptUrl .= $append;
		}

		// In case of dynamic or thumb embed, create kwidget object
		if( $this->embed == 'dynamic' || $this->embed == 'thumb' ) {
			$this->functionName = ($this->embed == 'dynamic') ? 'embed' : 'thumbEmbed';
			$this->kwidgetObj = array(
				'targetId' 	=> 'kaltura_player',
				'cache_st'	=> (time()+(60*15)),
				'wid' 		=> '_' . $this->partner_id,
				'uiconf_id'	=> $this->uiconf_id,
				'flashvars' => $flashVars,
			);

			if( $this->entry_id ) {
				$this->kwidgetObj[ 'entry_id' ] = $this->entry_id;
			}
		}

	}

	private function flashVarsToString( $fv = array(), $paramName = false ) 
	{
		$result = '';
		foreach( $fv as $key=>$value ) {
			$prefix = '&';
			if($paramName) {
				$prefix .= $paramName . '[';
			}			
			$suffix = ($paramName) ? ']=' : '=';
			if( is_array($value) ) {
				$pluginName = $key;
				foreach($value as $k=>$v) {
					$result .= $prefix . $pluginName . '.' . $k . $suffix . $v;
				}
			} else {
				$result .= $prefix . $key . $suffix . $value;
			}
		}
		return $result;
	}
}
