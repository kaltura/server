<?php
/**
 * @service bumper
 * @package plugins.bumper
 * @subpackage api.services
 */

class BumperService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		if(!BumperPlugin::isAllowedPartner($this->getPartnerId()))
		{
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, BumperPlugin::PLUGIN_NAME);
		}
	}

	/**
	 * Adds a bumper to an entry
	 *
	 * @action add
	 * @param string $entryId
	 * @param KalturaBumper $bumper
	 * @return KalturaBumper
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::INVALID_USER_ID
	 */
	public function addAction( $entryId, KalturaBumper $bumper )
	{
		$dbEntry = $this->getEntry($entryId, true);
		return $this->saveBumperData($dbEntry, $bumper, null);
	}

	/**
	 * Allows to update a bumper
	 *
	 * @action update
	 * @param string $entryId
	 * @param KalturaBumper $bumper
	 * @return KalturaBumper
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::INVALID_USER_ID
	 */
	public function updateAction( $entryId, KalturaBumper $bumper )
	{
		$dbEntry = $this->getEntry($entryId, true);
		$dbBumper = $this->getBumperData($dbEntry);
		return $this->saveBumperData($dbEntry, $bumper, $dbBumper);
	}

	/**
	 * Allows to get the bumper
	 *
	 * @action get
	 * @param string $entryId
	 * @return KalturaBumper
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 *
	 */
	public function getAction( $entryId )
	{
		$dbEntry = $this->getEntry($entryId, false);
		$dbBumper = $this->getBumperData($dbEntry);

		$bumper = new KalturaBumper();
		$bumper->fromObject( $dbBumper );
		return $bumper;
	}

	/**
	 * Delete bumper by EntryId
	 *
	 * @action delete
	 * @param string $entryId
	 * @return KalturaBumper  empty object object
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::INVALID_USER_ID
	 */
	public function deleteAction($entryId)
	{
		$dbEntry = $this->getEntry($entryId, true);
		$this->getBumperData($dbEntry);
		return $this->saveBumperData($dbEntry, null, null);
	}

	/**
	 * @param string $entryId
	 * @param bool $checkEntitlement
	 * @return entry
	 */
	protected function getEntry($entryId, $checkEntitlement)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		}

		if ( $checkEntitlement && !kEntitlementUtils::isEntitledForEditEntry($dbEntry) )
		{
			KalturaLog::debug("User is not allowed to update entry $entryId");
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
		}

		return $dbEntry;
	}

	/**
	 * @param entry $dbEntry
	 * @return kBumper
	 */
	protected function getBumperData(entry $dbEntry)
	{
		$dbBumper = kBumper::getBumperData($dbEntry);
		if(!$dbBumper)
		{
			throw new kCoreException("Entry does not have bumper data",kCoreException::INVALID_ENTRY_ID, $dbEntry->getId());
		}
		return $dbBumper;
	}

	protected function saveBumperData(entry $dbEntry, $bumper, $dbBumper)
	{
		$dbBumper = kBumper::saveBumperData($dbEntry, $bumper, $dbBumper);

		$bumper = new KalturaBumper();
		$bumper->fromObject( $dbBumper );
		return $bumper;
	}
}