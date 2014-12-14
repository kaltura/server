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
			
			$additionalConditions = array();
			if(preg_match('/^"[^"]+"$/', $freeTexts))
			{
				$freeText = str_replace('"', '', $freeTexts);
				$freeText = SphinxUtils::escapeString($freeText);
				$freeText = "^$freeText$";
				$additionalConditions[] = "@(" . CuePointFilter::FREE_TEXT_FIELDS . ") $freeText";
			}
			else
			{
				$useInSeperator = true;
				if(strpos($freeTexts, baseObjectFilter::IN_SEPARATOR) > 0)
				{
					str_replace(baseObjectFilter::AND_SEPARATOR, baseObjectFilter::IN_SEPARATOR, $freeTexts);
					$freeTextsArr = explode(baseObjectFilter::IN_SEPARATOR, $freeTexts);
				}
				else{
					$useInSeperator = false;
					$freeTextsArr = explode(baseObjectFilter::AND_SEPARATOR, $freeTexts);	
				}
				
				foreach($freeTextsArr as $valIndex => $valValue)
				{
					if(!is_numeric($valValue) && strlen($valValue) <= 0)
						unset($freeTextsArr[$valIndex]);
					else
						$freeTextsArr[$valIndex] = SphinxUtils::escapeString($valValue);
				}
				
				if($useInSeperator)
				{
					foreach($freeTextsArr as $freeText)
					{
						$additionalConditions[] = "@(" . CuePointFilter::FREE_TEXT_FIELDS . ") $freeText";
					}
				}
				else
				{
					$freeTextsArr = array_unique($freeTextsArr);
					$freeTextExpr = implode(baseObjectFilter::AND_SEPARATOR, $freeTextsArr);
					$additionalConditions[] = "@(" . CuePointFilter::FREE_TEXT_FIELDS . ") $freeTextExpr";
				}
			}
			
			if(count($additionalConditions))
			{	
				$additionalConditions = array_unique($additionalConditions);
				$matches = reset($additionalConditions);
				if(count($additionalConditions) > 1)
					$matches = '( ' . implode(' ) | ( ', $additionalConditions) . ' )';
					
				$this->matchClause[] = $matches;
			}
		}
		$filter->unsetByName('_free_text');

		return parent::applyFilterFields($filter);
	}
}