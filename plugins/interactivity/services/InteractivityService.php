<?php
/**
 * @service interactivity
 * @package plugins.interactivity
 * @subpackage api.services
 */

class InteractivityService extends KalturaBaseService
{
	/**
	 * Retrieve a interactivity object by entry id
	 *
	 * @action get
	 * @param string $entryId
	 * @return KalturaInteractivity
	 * @throws Exception
	 */
	public function get($entryId)
	{
		$kInteractivity = new kInteractivity();
		$kInteractivity->setEntry($entryId);
		$kalturaInteractivity = new KalturaInteractivity();
		$kalturaInteractivity->fromObject($kInteractivity, $this->getResponseProfile());
		return $kalturaInteractivity;
	}

	/**
	 * Update an existing interactivity object
	 *
	 * @action update
	 * @param string $entryId
	 * @param int $version
	 * @param KalturaInteractivity $kalturaInteractivity
	 * @return KalturaInteractivity
	 * @throws kCoreException
	 * @throws kFileSyncException
	 * @validateUser entry kalturaInteractivity edit
	 */
	public function update($entryId, $version, $kalturaInteractivity)
	{
		$kInteractivity = new kInteractivity();
		$kalturaInteractivity->toUpdatableObject($kInteractivity);
		$validator = kInteractivityDataValidatorFactory::getValidator($entryId);
		$validator->validate(json_decode($kInteractivity->getData(), true));
		$kInteractivity->update($entryId, $version);
		$kalturaInteractivity->fromObject($kInteractivity, $this->getResponseProfile());
		return $kalturaInteractivity;
	}

	/**
	 * Delete a interactivity object by entry id
	 *
	 * @action delete
	 * @param string $entryId
	 * @throws FileSyncException
	 * @throws KalturaAPIException
	 * @validateUser entry entryId edit
	 */
	public function delete($entryId)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $entryId);
		}

		$syncKey = $entry->getSyncKey(kEntryFileSyncSubType::INTERACTIVITY_DATA);
		if (!kFileSyncUtils::fileSync_exists($syncKey))
		{
			throw new KalturaAPIException(KalturaInteractivityErrors::NO_INTERACTIVITY_DATA, $entryId);
		}

		kFileSyncUtils::deleteSyncFileForKey($syncKey);
	}

	/**
	 * Add a interactivity object
	 *
	 * @action add
	 * @param string $entryId
	 * @param KalturaInteractivity $kalturaInteractivity
	 * @return KalturaInteractivity
	 * @throws KalturaAPIException
	 * @throws kCoreException
	 * @validateUser entry entryId edit
	 */
	public function add($entryId, $kalturaInteractivity)
	{
		/* @var $kInteractivity kInteractivity */
		$kInteractivity = $kalturaInteractivity->toInsertableObject();
		$validator = kInteractivityDataValidatorFactory::getValidator($entryId);
		$validator->validate(json_decode($kInteractivity->getData(), true));
		try
		{
			$kInteractivity->insert($entryId);
		}
		catch (kFileSyncException $exception)
		{
			if($exception->getCode() == kFileSyncException::FILE_SYNC_ALREADY_EXISTS)
			{
				throw new KalturaAPIException(KalturaInteractivityErrors::INTERACTIVITY_DATA_ALREADY_EXISTS);
			}
			else
			{
				throw $exception;
			}
		}

		$kalturaInteractivity->fromObject($kInteractivity, $this->getResponseProfile());
		return $kalturaInteractivity;
	}
}