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
 * @package plugins.virusScan
 * @subpackage model
 */
class VirusScanProfilePeer extends BaseVirusScanProfilePeer
{

	public static function setDefaultCriteriaFilter ()
	{
		parent::setDefaultCriteriaFilter();
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}
		
		$c = new myCriteria(); 
		$c->addAnd ( self::STATUS, VirusScanProfileStatus::DELETED , Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter ( $c );
	}
	
	
	/**
	 * Will return the first virus scan profile of the entry's partner, that defines an entry filter suitable for the given entry.
	 * @param int $entryId
	 * @return VirusScanProfile the suitable profile object, or null if none found
	 */
	public static function getSuitableProfile($entryId)
	{
		
		$entry = entryPeer::retrieveByPK($entryId);
		if (!$entry)
		{
			KalturaLog::err('Cannot find entry with id ['.$entryId.']');
			return null;
		}
		
		$cProfile = new Criteria();
		$cProfile->addAnd(VirusScanProfilePeer::PARTNER_ID, $entry->getPartnerId());
		$cProfile->addAnd(VirusScanProfilePeer::STATUS, VirusScanProfileStatus::ENABLED, Criteria::EQUAL);
		$profiles = VirusScanProfilePeer::doSelect($cProfile);
		
		if (!$profiles)
		{
			KalturaLog::debug('No virus scan profiles found for partner ['.$entry->getPartnerId().']');
			return null;
		}
		
		
		foreach ($profiles as $profile)
		{
			$virusEntryFilter = $profile->getEntryFilterObject();
			
			if ($virusEntryFilter->matches($entry))
			{
				KalturaLog::debug('Returning profile with id ['.$profile->getId().']');
				return $profile;
			}
				
		}
		
		return null;
	}
	
	
} // VirusScanProfilePeer
