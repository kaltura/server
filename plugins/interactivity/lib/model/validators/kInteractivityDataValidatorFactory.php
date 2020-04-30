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

		if($entry->getType() == entryType::PLAYLIST)
		{
			if ($entry->getMediaType() == PlaylistType::PATH) 
			{
				return new kInteractivityDataValidator($entry);
			}
			
			throw new kInteractivityException(kInteractivityException::UNSUPPORTED_PLAYLIST_TYPE, kInteractivityException::UNSUPPORTED_PLAYLIST_TYPE);
		}

		return new kEntryInteractivityDataValidator($entry);
	}
}