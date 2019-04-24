<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

class entrySource extends thumbnailSource
{
	protected $dbEntry;

	public function  __construct($entryId)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		}

		$securyEntryHelper = new KSecureEntryHelper($dbEntry, kCurrentContext::$ks, null, ContextType::THUMBNAIL);
		$securyEntryHelper->validateAccessControl();
		$this->dbEntry = $dbEntry;
	}


	public function getEntryMediaType()
	{
		return $this->dbEntry->getMediaType();
	}

	public function getImage()
	{
		if($this->getEntryMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE)
		{
			$fileSyncKey = $this->dbEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
			$imageBlob = kFileSyncUtils::file_get_contents($fileSyncKey);
			$imagick = new Imagick();
			$imagick->readImageBlob($imageBlob);
			return $imagick;
		}

		throw new KalturaAPIException(KalturaThumbnailErrors::MISSING_SOURCE_ACTIONS_FOR_TYPE, $this->getEntryMediaType());
	}
}