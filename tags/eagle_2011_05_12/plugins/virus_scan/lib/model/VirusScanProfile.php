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
 * @package plugins.virusScan
 * @subpackage model
 */
class VirusScanProfile extends BaseVirusScanProfile {
	
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
		$entryFilter = new VirusScanEntryFilter();
		$entryFilter->fillObjectFromXml($xml, '_');
		return $entryFilter;
	}
	
	
} // VirusScanProfile
