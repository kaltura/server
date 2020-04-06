<?php
/**
 * kParentChildEntryUtils is all utils needed for entry parent-child use cases.
 * @package Core
 * @subpackage utils
 *
 */
class kParentChildEntryUtils
{
	/**
	 * @param $entryId
	 * @return string
	 */
	public static function getCaptionAssetEntryId($entryId)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if ($entry && $entry->getParentEntryId())
		{
			return $entry->getParentEntryId();
		}

		return $entryId;
	}

	/**
	 * @param $entryIds
	 * @return array of entryIds - parent entries ids will return for each entry or the entryId itself in case he is not a child entry
	 * @throws PropelException
	 */
	public static function getParentEntryIds($entryIds)
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;
		// verify access to the relevant entries - either same partner as the KS or kaltura network
		$ids = entryPeer::filterEntriesByPartnerOrKalturaNetwork($entryIds, kCurrentContext::getCurrentPartnerId());
		$entries = entryPeer::retrieveByPKs($ids);
		$parentEntryIds = array();
		foreach ($entries as $entry)
		{
			/** @var $entry entry */
			$parentEntryIds[] = !is_null($entry->getParentEntryId()) ? $entry->getParentEntryId() : $entry->getId();
		}
		myDbHelper::$use_alternative_con = null;
		return array_unique($parentEntryIds);
	}
	
	/**
	 * @param $asset
	 * @param $entryId
	 * @return bool
	 */
	public static function shouldCopyAsset($asset, $originalEntryId)
	{
		if ($asset instanceof captionAsset && $asset->getEntryId() != $originalEntryId)
		{
			return false;
		}
		return true;
	}
}
