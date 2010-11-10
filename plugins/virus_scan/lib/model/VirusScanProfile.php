<?php


/**
 * Skeleton subclass for representing a row from the 'virus_scan_profile' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class VirusScanProfile extends BaseVirusScanProfile {

	const VIRUS_SCAN_PROFILE_STATUS_DISABLED = 1;
	const VIRUS_SCAN_PROFILE_STATUS_ENABLED = 2;
	
	/**
	 * @param      entryFilter $v
	 * @return     VirusScanProfile The current object (for fluent API support)
	 */
	public function setEntryFilterObject(entryFilter $v)
	{
		$xml = $v->toXml();
		return parent::setEntryFilter($xml->asXML());
	} // setEntryFilter()

	/**
	 * Get the [entry_filter] column value.
	 * 
	 * @return entryFilter
	 */
	public function getEntryFilterObject()
	{
		$v = parent::getEntryFilter();
		if(is_null($v) || !strlen(trim($v)))
			return null;
			
		$xml = new SimpleXMLElement($v);
		$entryFilter = new entryFilter();
		$entryFilter->fillObjectFromXml($xml);
		return $entryFilter;
	}
	
	
} // VirusScanProfile
