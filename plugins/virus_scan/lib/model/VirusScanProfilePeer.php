<?php


/**
 * Skeleton subclass for performing query and update operations on the 'virus_scan_profile' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class VirusScanProfilePeer extends BaseVirusScanProfilePeer
{

	/**
	 * Will return the first virus scan profile of the entry's partner, that defines an entry filter suitable for the given entry.
	 * @param int $entryId
	 * @return VirusScanProfile the suitable profile object, or null if none found
	 */
	public static function getSuitableProfile($entryId)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if (!$entryId)
		{
			KalturaLog::err('Cannot find entry with id ['.$entryId.']');
			return null;
		}
		
		$cProfile = new Criteria();
		$cProfile->addAnd(VirusScanProfilePeer::PARTNER_ID, $entry->getPartnerId());
		$cProfile->addAnd(VirusScanProfilePeer::STATUS, KalturaVirusScanProfileStatus::DISABLED, Criteria::NOT_EQUAL);
		$profiles = VirusScanProfilePeer::doSelect($cProfile);
		
		if (!$profiles)
		{
			KalturaLog::debug('No virus scan profiles found for partner ['.$entry->getPartnerId().']');
			return null;
		}
		
		foreach ($profiles as $profile)
		{
			entryPeer::setDefaultCriteriaFilter();
			$entryFilter = $profile->getEntryFilterObject();
			$cEntry = KalturaCriteria::create(entryPeer::OM_CLASS);
			$cEntry->addAnd(entryPeer::ID, $entryId);
			$entryFilter->setPartnerSearchScope($entry->getPartnerId());
			$entryFilter->attachToCriteria($cEntry);
			$cEntry->applyFilters();
			//$count = entryPeer::doCount($cEntry);
			$count = $cEntry->getRecordsCount();
			
			if ($count > 0)
			{
				return $profile;
			}
		}
	}
	
	
} // VirusScanProfilePeer
