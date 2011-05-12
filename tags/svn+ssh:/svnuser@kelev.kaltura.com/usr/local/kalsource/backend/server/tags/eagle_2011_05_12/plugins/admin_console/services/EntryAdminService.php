<?php
/**
 * Entry Admin service
 *
 * @service entryAdmin
 * @package plugins.adminConsole
 * @subpackage api.services
 */
class EntryAdminService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		if(!AdminConsolePlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
	}

	/**
	 * Get base entry by ID with no filters.
	 * 
	 * @action get
	 * @param string $entryId Entry id
	 * @param int $version Desired version of the data
	 * @return KalturaBaseEntry The requested entry
	 */
	function getAction($entryId, $version = -1)
	{
		$dbEntries = entryPeer::retrieveByPKsNoFilter(array($entryId));
		if (!count($dbEntries))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		
		$dbEntry = reset($dbEntries);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		if ($version !== -1)
			$dbEntry->setDesiredVersion($version);
			
	    $entry = KalturaEntryFactory::getInstanceByType($dbEntry->getType(), true);
	    
		$entry->fromObject($dbEntry);

		return $entry;
	}
	
	/**
	 * Get base entry by flavor ID with no filters.
	 * 
	 * @action getByFlavorId
	 * @param string $flavorId
	 * @param int $version Desired version of the data
	 * @return KalturaBaseEntry The requested entry
	 */
	public function getByFlavorIdAction($flavorId, $version = -1)
	{
		$flavorAssetDb = flavorAssetPeer::retrieveById($flavorId);
		if (!$flavorAssetDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $flavorId);
			
		return $this->getAction($flavorAssetDb->getEntryId(), $version);
	}

	/**
	 * Get base entry by ID with no filters.
	 * 
	 * @action getTracks
	 * @param string $entryId Entry id
	 * @return KalturaTrackEntryListResponse
	 */
	function getTracksAction($entryId)
	{
		$c = new Criteria();
		$c->add(TrackEntryPeer::ENTRY_ID, $entryId);
		
		$dbList = TrackEntryPeer::doSelect($c);
		
		$list = KalturaTrackEntryArray::fromDbArray($dbList);
		$response = new KalturaTrackEntryListResponse();
		$response->objects = $list;
		$response->totalCount = count($dbList);
		return $response;
	}
}
