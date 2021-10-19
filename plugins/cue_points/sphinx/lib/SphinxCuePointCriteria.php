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
	
	public function applyFilters()
	{
		if(kCurrentContext::getCurrentPartnerId() < 0)
		{
			$partnerId = $this->getValue("cue_point.PARTNER_ID");
			if(!$partnerId)
			{
				$entryId = $this->getValue("cue_point.ENTRY_ID");
				if($entryId)
				{
					if(is_array($entryId))
						$entryId = reset($entryId);
					
					$entry = entryPeer::retrieveByPK($entryId);
					if($entry)
						$partnerId = $entry->getPartnerId();
				}
			}
			
			if($partnerId)
				kCurrentContext::$partner_id = $partnerId;
		}
		
		return parent::applyFilters();
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

		if(!is_null($filter->get('_eq_parent_id')))
		{
			$this->sphinxSkipped = false;
			$parentId = $filter->get('_eq_parent_id');
			$this->addAnd(CuePointPeer::PARENT_ID, CuePoint::getIndexPrefix(kCurrentContext::getCurrentPartnerId()).$parentId, Criteria::EQUAL);
		}
		$filter->unsetByName('_eq_parent_id');

		if(!is_null($filter->get('_in_parent_id')))
		{
			$this->sphinxSkipped = false;
			$parentIds = explode(',', $filter->get('_in_parent_id'));

			for ($i=0; $i< count($parentIds); $i++ ) {
				$condition .= "(" . CuePoint::getIndexPrefix(kCurrentContext::getCurrentPartnerId()) . $parentIds[$i] . ")";
				if ( $i < count($parentIds) - 1 )
					$condition .= " | ";
			}
			$this->addMatch("@parent_id $condition");
		}
		$filter->unsetByName('_in_parent_id');

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
