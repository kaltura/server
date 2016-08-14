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
			$this->addFreeTextToMatchClauseByMatchFields($freeTexts, CuePointFilter::FREE_TEXT_FIELDS);
		}
		$filter->unsetByName('_free_text');
		
		if($filter->get('_eq_is_public'))
		{
		    $this->sphinxSkipped = false;
		    $isPublic = $filter->get('_eq_is_public');
		    $this->addAnd(CuePointPeer::IS_PUBLIC, CuePoint::getIndexPrefix(kCurrentContext::getCurrentPartnerId()).$isPublic, Criteria::EQUAL);	
		}
		$filter->unsetByName('_eq_is_public');

		return parent::applyFilterFields($filter);
	}

	public function translateSphinxCriterion(SphinxCriterion $crit)
	{
		$field = $crit->getTable() . '.' . $crit->getColumn();
		if ($field == CuePointPeer::TYPE && $crit->getComparison() == Criteria::EQUAL)
		{
			return array(
				CuePointPeer::TYPE,
				Criteria::LIKE,
				CuePoint::getIndexPrefix(kCurrentContext::getCurrentPartnerId()) . $crit->getValue());
		} else if ($field == CuePointPeer::TYPE && $crit->getComparison() == Criteria::IN)
		{
			return array(
				CuePointPeer::TYPE,
				Criteria::IN,
				$this::addPrefixToArray($crit->getValue(), CuePoint::getIndexPrefix(kCurrentContext::getCurrentPartnerId())));
		}
		return parent::translateSphinxCriterion($crit);
	}

	private function addPrefixToArray(array $strings, $prefix)
	{
		foreach ($strings as &$value)
			$value = $prefix.$value;
		return $strings;
	}
}
