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

		if(InteractivityPlugin::isInteractivityPlaylist($entry))
		{
			return new kInteractivityDataValidator($entry);
		}

		return new kEntryInteractivityDataValidator($entry);
	}
}