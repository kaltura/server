<?php

class SphinxCuePointCriteria extends SphinxCriteria
{
	const LIVE_ENTRY_CUE_POINT_CACHE_EXPIRY_SECONDS = 10;

	public function getIndexObjectName() {
		return "CuePointIndex";
	}
	
	public function hasPeerFieldName($fieldName)
	{
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = "cue_point.$fieldName";
		}
		
		$cuePointFields = CuePointPeer::getFieldNames(BasePeer::TYPE_COLNAME);
		
		return in_array($fieldName, $cuePointFields);
	}

	/* (non-PHPdoc)
	 * @see SphinxCriteria::applyFilterFields()
	 */
	protected function applyFilterFields(baseObjectFilter $filter)
	{
		/* @var $filter cuePointFilter */
		// Reduce the cache expiry when fetching live stream cuepoints
		$entryId = $filter->get( '_in_entry_id' );
		if ( $entryId && strpos($entryId, ',') === false ) // Single entry id?
		{
			$entry = entryPeer::retrieveByPK($entryId);
			if ( $entry && $entry->getType() == entryType::LIVE_STREAM )
			{
				kApiCache::setExpiry( self::LIVE_ENTRY_CUE_POINT_CACHE_EXPIRY_SECONDS );
			}
		}
		
		if($filter->get('_free_text'))
		{
			$this->sphinxSkipped = false;
			$freeTexts = $filter->get('_free_text');
			KalturaLog::debug("Attach free text [$freeTexts]");
			
			$this->addFreeTextToMatchClauseByMatchFields($freeTexts, CuePointFilter::FREE_TEXT_FIELDS);
		}
		$filter->unsetByName('_free_text');

		return parent::applyFilterFields($filter);
	}
}