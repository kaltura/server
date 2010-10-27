<?php
/**
 * Metadata service
 *
 * @service entryMetadata
 */
class EntryMetadataService extends KalturaEntryService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);

		myPartnerUtils::addPartnerToCriteria(new MetadataProfilePeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		myPartnerUtils::addPartnerToCriteria(new MetadataPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		myPartnerUtils::addPartnerToCriteria(new entryPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		myPartnerUtils::addPartnerToCriteria(new FileSyncPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		
		if(!MetadataPlugin::isAllowedPartner(kCurrentContext::$ks_partner_id))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN);
	}
	
	/**
	 * List base entries with their metadata by filter with paging support.
	 * 
	 * @action list
     * @param KalturaBaseEntryFilter $filter Entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @return KalturaEntryMetadataListResponse Wrapper for array of base entries and total count
	 */
	function listAction(KalturaBaseEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
	    myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;
		
	    list($entries, $totalCount) = parent::listEntriesByFilter($filter, $pager);
	    $entryIds = array();
	    foreach($entries as $entry)
	    	$entryIds[] = $entry->getId();
	    	
	    $metadatas = MetadataPeer::retrieveAllByObjectIds(Metadata::TYPE_ENTRY, $entryIds);
	    
		$ks = $this->getKs();
		$isAdmin = false;
		if($ks)
			$isAdmin = $ks->isAdmin();
			
	    $newList = KalturaEntryMetadataArray::fromEntryAndMetadataArray($entries, $isAdmin);
		$response = new KalturaEntryMetadataListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		return $response;
	}
    
	/**
	 * Get base entry with its metadata objects by entry id.
	 * 
	 * @action get
	 * @param string $entryId Entry id
	 * @param int $version entry version
	 * @return KalturaEntryMetadata The requested entry with its metadata objects
	 */
	function getAction($entryId, $version = -1)
	{
	    myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;
		
		$entryMetadata = new KalturaEntryMetadata();
		$entryMetadata->entry = $this->getEntry($entryId, $version);
		$entryMetadata->metadatas = new KalturaMetadataArray();
		
		$metadatas = MetadataPeer::retrieveAllByObject(Metadata::TYPE_ENTRY, $entryId);
	
		foreach ($metadatas as $obj)
		{
    		$metadata = new KalturaMetadata();
			$metadata->fromObject($obj);
			
			if($metadata->metadataObjectType != KalturaMetadataObjectType::ENTRY)
				continue;
				
			$entryMetadata->metadatas[] = $metadata;
		}
		return $entryMetadata;
	}
}
