<?php

/**
 * Enable serving live conversion profile to the Wowza servers as XML
 * @service liveConversionProfile
 * @package plugins.wowza
 * @subpackage api.services
 */
class LiveConversionProfileService extends KalturaBaseService
{
	/* (non-PHPdoc)
	 * @see KalturaBaseService::initService()
	 */
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		$this->applyPartnerFilterForClass('conversionProfile2');
		$this->applyPartnerFilterForClass('assetParams');
	}
	
	/**
	 * Allows you to add a new KalturaDropFolderFile object
	 * 
	 * @action serve
	 * @param string $entryId
	 * @return file
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaDropFolderErrors::DROP_FOLDER_NOT_FOUND
	 */
	public function serveAction($entryId)
	{
		$entry = null;
		if (!kCurrentContext::$ks)
		{
			$entry = kCurrentContext::initPartnerByEntryId($entryId);
			
			if (!$entry || $entry->getStatus() == entryStatus::DELETED)
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
				
			// enforce entitlement
			$this->setPartnerFilters(kCurrentContext::getCurrentPartnerId());
		}
		else 
		{	
			$entry = entryPeer::retrieveByPK($entryId);
		}
			
		if (!$entry || $entry->getType() != KalturaEntryType::LIVE_STREAM || $entry->getSource() != KalturaSourceType::LIVE_STREAM)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		$conversionProfileId = $entry->getConversionProfileId();
		$liveParams = assetParamsPeer::retrieveByProfile($conversionProfileId);
		
		// TODO - translate the $liveParams to XML according to doc: http://www.wowza.com/forums/content.php?304#configTemplate
	}
}
