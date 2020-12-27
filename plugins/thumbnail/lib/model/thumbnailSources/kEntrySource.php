<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.thumbnailSources
 */

class kEntrySource extends kThumbnailSource
{
	protected $dbEntry;

	public function setEntryId($entryId)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
		{
			$data = array(kThumbnailErrorMessages::ENTRY_ID => $entryId);
			throw new kThumbnailException(kThumbnailException::ENTRY_NOT_FOUND, kThumbnailException::ENTRY_NOT_FOUND, $data);
		}

		$secureEntryHelper = new KSecureEntryHelper($dbEntry, kCurrentContext::$ks, null, ContextType::THUMBNAIL);
		$secureEntryHelper->validateAccessControl();
		$this->dbEntry = $dbEntry;
	}
	
	public function getEntryMediaType()
	{
		return $this->dbEntry->getMediaType();
	}

	public function getEntryType()
	{
		return $this->dbEntry->getType();
	}

	/**
	 * @param entry $dbEntry
	 */
	public function setEntry($dbEntry)
	{
		$this->dbEntry = $dbEntry;
	}

	/**
	 * @return entry
	 */
	public function getEntry()
	{
		return $this->dbEntry;
	}

	/**
	 * @return Imagick
	 * @throws ImagickException
	 * @throws kThumbnailException
	 */
	public function getImage()
	{
		if($this->getEntryMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE)
		{
			$fileSyncKey = $this->dbEntry->getSyncKey(kEntryFileSyncSubType::DATA);
			$imageBlob = kFileSyncUtils::file_get_contents($fileSyncKey);
			$imagick = new Imagick();
			$imagick->readImageBlob($imageBlob);
			return $imagick;
		}

		$data = array(kThumbnailErrorMessages::ENTRY_TYPE => $this->getEntryMediaType());
		throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::MISSING_SOURCE_ACTIONS_FOR_TYPE, $data);
	}

	public function getLastModified()
	{
		if($this->getEntryMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE)
		{
			$fileSyncKey = $this->dbEntry->getSyncKey(kEntryFileSyncSubType::DATA);
			$fileSync= kFileSyncUtils::getOriginFileSyncForKey($fileSyncKey,false);
			if($fileSync)
			{
				return $fileSync->getUpdatedAt(null);
			}

			return null;
		}
		else
		{
			$lastModifiedFlavor = assetPeer::retrieveLastModifiedFlavorByEntryId($this->dbEntry->getId());
			$lastModified = $lastModifiedFlavor ? $lastModifiedFlavor->getUpdatedAt(null) : null;
			return $lastModified;
		}
	}
}
