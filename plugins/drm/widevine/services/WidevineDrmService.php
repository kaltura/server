<?php

/**
 * WidevineDrmService serves as a license proxy to a Widevine license server
 * @service widevineDrm
 * @package plugins.widevine
 * @subpackage api.services
 */
class WidevineDrmService extends KalturaBaseService
{	
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass('asset');
//		if (!WidevinePlugin::isAllowedPartner($this->getPartnerId()))
//			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
	}
		
	/**
	 * Get license for encrypted content playback
	 * 
	 * @action getLicense
	 * @param string $flavorAssetId
	 * @return string $response
	 * 
	 */
	public function getLicenseAction($flavorAssetId)
	{
		KalturaLog::debug('get license for flavor asset: '.$flavorAssetId);
		try 
		{
			$wvAssetId = $_GET[LicenseProxyUtils::ASSETID];
			$this->validateLicenseRequest($flavorAssetId, $wvAssetId);
			$response = LicenseProxyUtils::sendLicenseRequest($wvAssetId, kCurrentContext::$ks_object->getPrivileges());
		}
		catch(KalturaWidevineLicenseProxyException $e)
		{
			KalturaLog::err($e->getTraceAsString());
			$response = LicenseProxyUtils::createErrorResponse($e->getWvErrorCode(), $wvAssetId);
		}
		catch (Exception $e)
		{
			KalturaLog::err($e->getTraceAsString());
			$response = LicenseProxyUtils::createErrorResponse(KalturaWidevineErrorCodes::UNKNOWN_ERROR, $wvAssetId);
		}	
		
		LicenseProxyUtils::printLicenseResponseStatus($response);
		return $response;
	}
	
	private function validateLicenseRequest($flavorAssetId, $wvAssetId)
	{
		if(!$flavorAssetId)
			throw new KalturaWidevineLicenseProxyException(KalturaWidevineErrorCodes::FLAVOR_ASSET_ID_CANNOT_BE_NULL);
		if(!$wvAssetId)
			throw new KalturaWidevineLicenseProxyException(KalturaWidevineErrorCodes::WIDEVINE_ASSET_ID_CANNOT_BE_NULL);
		
		$flavorAsset = $this->getFlavorAssetObject($flavorAssetId);

		if($flavorAsset->getType() != WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			throw new KalturaWidevineLicenseProxyException(KalturaWidevineErrorCodes::WRONG_ASSET_TYPE);
			
		KalturaLog::debug("Widevine Asset Id from request: ".$wvAssetId);
		KalturaLog::debug("Widevine Asset Id from flavor asset object: ".$flavorAsset->getWidevineAssetId());
		
		if($wvAssetId != $flavorAsset->getWidevineAssetId())
			throw new KalturaWidevineLicenseProxyException(KalturaWidevineErrorCodes::FLAVOR_ASSET_ID_DONT_MATCH_WIDEVINE_ASSET_ID);
					
		$entry = entryPeer::retrieveByPK($flavorAsset->getEntryId());
		if(!$entry)
			throw new KalturaWidevineLicenseProxyException(KalturaWidevineErrorCodes::FLAVOR_ASSET_ID_NOT_FOUND);
			
		$this->validateAccessControl($entry);		
	}
	
	private function validateAccessControl($entry)
	{
		KalturaLog::debug("Validating access control");
		
		$secureEntryHelper = new KSecureEntryHelper($entry, kCurrentContext::$ks, null, accessControlContextType::PLAY);
		if(!$secureEntryHelper->isKsAdmin())
		{
			if(!$entry->isScheduledNow())
				throw new KalturaWidevineLicenseProxyException(KalturaWidevineErrorCodes::ENTRY_NOT_SCHEDULED_NOW);
			if($secureEntryHelper->isEntryInModeration())
				throw new KalturaWidevineLicenseProxyException(KalturaWidevineErrorCodes::ENTRY_MODERATION_ERROR);
		}
			
		$context = $secureEntryHelper->applyContext();
		if(count($context->getAccessControlActions()))
		{
			$actions = $context->getAccessControlActions();
			foreach($actions as $action)
			{
				/* @var $action kAccessControlAction */
				if($action->getType() == accessControlActionType::BLOCK)
					throw new KalturaWidevineLicenseProxyException(KalturaWidevineErrorCodes::ACCESS_CONTROL_RESTRICTED);
			}
		}
	}
	
	private function getFlavorAssetObject($flavorAssetId)
	{
		try
		{
			if (!kCurrentContext::$ks)
			{
				$flavorAsset = kCurrentContext::initPartnerByAssetId($flavorAssetId);							
				// enforce entitlement
				$this->setPartnerFilters(kCurrentContext::getCurrentPartnerId());
				kEntitlementUtils::initEntitlementEnforcement();
			}
			else 
			{	
				$flavorAsset = assetPeer::retrieveById($flavorAssetId);
			}
			
			if (!$flavorAsset || $flavorAsset->getStatus() == asset::ASSET_STATUS_DELETED)
				throw new KalturaWidevineLicenseProxyException(KalturaWidevineErrorCodes::FLAVOR_ASSET_ID_NOT_FOUND);		

			return $flavorAsset;
		}
		catch (PropelException $e)
		{
			throw new KalturaWidevineLicenseProxyException(KalturaWidevineErrorCodes::FLAVOR_ASSET_ID_NOT_FOUND);
		}
	}
}
