<?php
/**
 * @service volatileInteractivity
 * @package plugins.interactivity
 * @subpackage api.services
 */

class VolatileInteractivityService extends KalturaBaseService
{
	/**
	 * Retrieve a volatile interactivity object by entry id
	 *
	 * @action get
	 * @param string $entryId
	 * @return KalturaVolatileInteractivity
	 * @throws Exception
	 * @validateUser entry entryId edit
	 */
	public function get($entryId)
	{
		$kVolatileInteractivity = new kVolatileInteractivity();
		$kVolatileInteractivity->setEntry($entryId);
		$KalturaVolatileInteractivity = new KalturaVolatileInteractivity();
		$KalturaVolatileInteractivity->fromObject($kVolatileInteractivity, $this->getResponseProfile());
		return $KalturaVolatileInteractivity;
	}

	/**
	 * Update a volatile interactivity object
	 *
	 * @action update
	 * @param string $entryId
	 * @param int $version
	 * @param KalturaVolatileInteractivity $kalturaVolatileInteractivity
	 * @return KalturaVolatileInteractivity
	 * @throws kCoreException
	 * @throws kFileSyncException
	 * @validateUser entry entryId edit
	 */
	public function update($entryId, $version, $kalturaVolatileInteractivity)
	{
		$kVolatileInteractivity = new kVolatileInteractivity();
		$kalturaVolatileInteractivity->toUpdatableObject($kVolatileInteractivity);
		$kVolatileInteractivity->update($entryId, $version);
		$kalturaVolatileInteractivity->fromObject($kVolatileInteractivity, $this->getResponseProfile());
		return $kalturaVolatileInteractivity;
	}

	/**
	 * Delete a volatile interactivity object by entry id
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

		$syncKey = $entry->getSyncKey(kEntryFileSyncSubType::VOLATILE_INTERACTIVITY_DATA);
		if (!kFileSyncUtils::fileSync_exists($syncKey))
		{
			throw new KalturaAPIException(KalturaInteractivityErrors::NO_VOLATILE_INTERACTIVITY_DATA, $entryId);
		}

		kFileSyncUtils::deleteSyncFileForKey($syncKey);
	}

	/**
	 * add a volatile interactivity object
	 *
	 * @action add
	 * @param string $entryId
	 * @param KalturaVolatileInteractivity $kalturaVolatileInteractivity
	 * @return KalturaVolatileInteractivity
	 * @throws KalturaAPIException
	 * @throws kCoreException
	 * @throws kFileSyncException
	 * @validateUser entry entryId edit
	 */
	public function add($entryId, $kalturaVolatileInteractivity)
	{
		/* @var $kVolatileInteractivity kVolatileInteractivity */
		$kVolatileInteractivity = $kalturaVolatileInteractivity->toInsertableObject();
		try
		{
			$kVolatileInteractivity->insert($entryId);
		}
		catch (kFileSyncException $exception)
		{
			if($exception->getCode() == kFileSyncException::FILE_SYNC_ALREADY_EXISTS)
			{
				throw new KalturaAPIException(KalturaInteractivityErrors::VOLATILE_INTERACTIVITY_DATA_ALREADY_EXISTS);
			}
			else
			{
				throw $exception;
			}
		}

		$kalturaVolatileInteractivity->fromObject($kVolatileInteractivity, $this->getResponseProfile());
		return $kalturaVolatileInteractivity;
	}
}