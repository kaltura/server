<?php
/**
 * Entry Admin service
 *
 * @service entryAdmin
 * @package api
 * @subpackage extServices
 */
class EntryAdminService extends KalturaBaseService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);

		if(!AdminConsolePlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(SystemUserErrors::SERVICE_FORBIDDEN);
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
}
