<?php
/**
 * @package plugins.interactivity
 * @subpackage model.items
 */

abstract class kBaseInteractivity extends BaseObject
{
	/**
	 *
	 * @var string
	 */
	protected $data;

	/**
	 *
	 * @var int
	 */
	protected $version;

	/**
	 *
	 * @var entry
	 */
	protected $entry;

	/**
	 * Interactivity update date as Unix timestamp (In seconds)
	 *
	 * @var time
	 */
	protected $updatedAt;

	/**
	 * @return time
	 */
	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}

	/**
 * @return int
 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * @param int $version
	 */
	public function setVersion($version)
	{
		$this->version = $version;
	}

	/**
	 * @return string
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @param string $data
	 */
	public function setData($data)
	{
		$this->data = $data;
	}

	/**
	 * @return string
	 */
	public function getEntryId()
	{
		if($this->entry)
		{
			return $this->entry->getId();
		}

		return 'N\A';
	}

	public function getSyncKey()
	{
		if(!$this->entry)
		{
			throw new kCoreException('Entry is not set found', kCoreException::INVALID_ENTRY_ID);
		}

		return $this->entry->getSyncKey($this->getFileSyncSubType());
	}

	/**
	 * @param string $entryId
	 * @throws kCoreException
	 */
	public function setEntry($entryId)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
		{
			throw new kCoreException("Entry [$entryId] not found", kCoreException::INVALID_ENTRY_ID);
		}

		$this->entry = $entry;
	}

	/**
	 * @param string $entryId
	 * @throws kCoreException
	 * @throws kFileSyncException
	 */
	public function insert($entryId)
	{
		$this->setEntry($entryId);
		$syncKey = $this->getSyncKey();
		kFileSyncUtils::file_put_contents($syncKey, $this->data, true);
	}

	/**
	 * @param string $entryId
	 * @param int $version
	 * @throws kCoreException
	 * @throws kFileSyncException
	 * @throws kInteractivityException
	 */
	public function update($entryId, $version)
	{
		$this->setEntry($entryId);
		$syncKey = $this->getSyncKey();
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
		/* @var $fileSync FileSync */
		if (!$fileSync)
		{
			$type = array(kInteractivityErrorMessages::TYPE_PARAMETER => $this->getFileSyncSubType(), kInteractivityErrorMessages::ENTRY_ID => $entryId);
			throw new kInteractivityException(kInteractivityException::CANT_UPDATE_NO_DATA, kInteractivityException::CANT_UPDATE_NO_DATA, $type);
		}

		$currentVersion = $syncKey->getVersion();
		if($version != $currentVersion)
		{
			$data = array(kInteractivityErrorMessages::VERSION_PARAMETER => $currentVersion);
			throw new kInteractivityException( kInteractivityException::DIFFERENT_DATA_VERSION, kInteractivityException::DIFFERENT_DATA_VERSION, $data);
		}

		$newVersion = kFileSyncUtils::calcObjectNewVersion($entryId, $currentVersion, FileSyncObjectType::ENTRY, $this->getFileSyncSubType());
		$syncKey->setVersion($newVersion);
		kFileSyncUtils::file_put_contents($syncKey, $this->data, false);
		$this->setEntryInteractivityVersion($newVersion);
		$syncKey->setVersion($currentVersion);
		kFileSyncUtils::deleteSyncFileForKey($syncKey);
	}

	protected abstract function setEntryInteractivityVersion($newVersion);

	protected abstract function getFileSyncSubType();
}