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
		    $this->addCondition('is_public' . " = " . $isPublic);
		}
		$filter->unsetByName('_eq_is_public');

		if($filter->get('_eq_type'))
		{
			$type = $filter->get('_eq_type');
			$cuePointType = kPluginableEnumsManager::apiToCore('CuePointType', $type);
			CuePoint::addTypes($this, kCurrentContext::getCurrentPartnerId(), array($cuePointType));
			$this->cuePointTypeEqual = null;
		}
		$filter->unsetByName('_eq_type');

		if($filter->get('_in_type'))
		{
			$types = explode(',', $filter->get('_in_type'));
			$cuePointTypes = array();
			foreach ($types as $type)
			{
				$cuePointType = kPluginableEnumsManager::apiToCore('CuePointType', $type);
				if ($cuePointType)
					$cuePointTypes[] = $cuePointType;
			}
			CuePoint::addTypes($this, kCurrentContext::getCurrentPartnerId(), $cuePointTypes);
		}
		$filter->unsetByName('_in_type');

		return parent::applyFilterFields($filter);
	}
}