<?php
/**
 * Aspera service
 *
 * @service aspera
 * @package plugins.aspera
 * @subpackage api.services
 */
class AsperaService extends KalturaBaseService
{

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass('asset');
		if(!AsperaPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, AsperaPlugin::PLUGIN_NAME);
	}

	/**
	 *
	 * @action getFaspUrl
	 * @param string $flavorAssetId
	 * @throws KalturaAPIException
	 * @return string
	 */
	function getFaspUrlAction($flavorAssetId)
	{
		KalturaResponseCacher::disableCache();
		
		$assetDb = assetPeer::retrieveById($flavorAssetId);
		if (!$assetDb || !($assetDb instanceof flavorAsset))
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $flavorAssetId);

		if (!$assetDb->isLocalReadyStatus())
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_IS_NOT_READY);

		$syncKey = $assetDb->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		/* @var $fileSync FileSync */
		list($fileSync, $isFileSyncLocal) = kFileSyncUtils::getReadyFileSyncForKey($syncKey);
		$filePath = $fileSync->getFilePath();

		$transferUser = $this->getFromAsperaConfig('transfer_user');
		$transferHost = $this->getFromAsperaConfig('transfer_host');
		$asperaNodeApi = new AsperaNodeApi(
			$this->getFromAsperaConfig('node_api_user'),
			$this->getFromAsperaConfig('node_api_password'),
			$this->getFromAsperaConfig('node_api_host'),
			$this->getFromAsperaConfig('node_api_port')
		);

		$options = array(
			'transfer_requests' => array(
				'transfer_request' => array(
					'remote_host' => $transferHost
				)
			)
		);
		$tokenResponse = $asperaNodeApi->getToken($filePath, $options);
		$token = $tokenResponse->transfer_spec->token;

		$urlParams = array(
			'auth' => 'no',
			'token' => $token
		);

		return 'fasp://'.$transferUser.'@'.$transferHost.$filePath.'?'.http_build_query($urlParams, '', '&');
	}

	protected function getFromAsperaConfig($key)
	{
		$asperaConfig = kConf::get('aspera');
		if (!is_array($asperaConfig))
			throw new kCoreException('Aspera config section is not an array');

		if (!isset($asperaConfig[$key]))
			throw new kCoreException('The key '.$key.' was not found in the aspera config section');

		return $asperaConfig[$key];
	}
}
