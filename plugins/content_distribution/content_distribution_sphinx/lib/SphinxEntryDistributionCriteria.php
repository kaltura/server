<?php
/**
 * @package plugins.contentDistribution
 * @subpackage DB
 */
class SphinxEntryDistributionCriteria extends SphinxCriteria
{
	/* (non-PHPdoc)
	 * @see SphinxCriteria::getIndexObjectName()
	*/
	public function getIndexObjectName() {
		return "EntryDistributionIndex";
	}
}