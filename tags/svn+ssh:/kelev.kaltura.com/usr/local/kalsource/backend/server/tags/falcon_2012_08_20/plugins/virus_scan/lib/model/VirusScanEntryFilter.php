<?php
/**
 *   * @package plugins.virusScan
 * @subpackage model.filters
 */
class VirusScanEntryFilter extends entryFilter
{
	
	/**
	 * This function checks if the entry type matches the filter, and return 'true' or 'false' accordingly.
	 * @param entry $entry
	 * @return boolean
	 */
	public function matches(entry $entry)
	{
		// check if type equals
		if ($entry->getType() == $this->get('_eq_type'))
		{
			return true;
		}
		
		// check if type in		
		if ( in_array($entry->getType(), explode(',', $this->get('_in_type'))) )
		{
			return true;
		}
		
		return false;		
	}	
	
}