<?php
/**
 * @package plugins.interactivity
 * @subpackage model.items
 */

class kInteractivityDataValidatorFactory
{
	/**
	 * @param string $entryId
	 * @return IInteractivityDataValidator
	 * @throws kCoreException
	 */
	public static function getValidator($entryId)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
		{
			throw new kCoreException("Entry [$entryId] not found", kCoreException::INVALID_ENTRY_ID);
		}

		if(self::isPathPlaylist($entry))
		{
			return new kInteractivityNodeValidator($entry);
		}

		return new kEntryInteractivityDataValidator($entry);
	}

	/**
	 * @param entry $entry
	 * @return bool
	 */
	protected static function isPathPlaylist($entry)
	{
		if($entry->getType() == entryType::PLAYLIST && $entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_TEXT)
		{
			return true;
		}

		return false;
	}
}