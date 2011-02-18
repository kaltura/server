<?php

class MySqlEntryCriteria extends MySqlCriteria
{
	/**
	 * @var SearchEntryPeer
	 */
	private $peer = null;
	
	public static $mySqlFields = array(
		'plugins_data',
		'entry.DURATION_TYPE' => 'duration_type',
		'entry.SEARCH_TEXT' => 'search_text',
	);

	/* (non-PHPdoc)
	 * @see MySqlCriteria::getSearchPeer()
	 */
	protected function getSearchPeer()
	{
		if(!$this->peer)
			$this->peer = new SearchEntryPeer();
			
		return $this->peer;
	}	
	
	/**
	 * @return criteriaFilter
	 */
	protected function getDefaultCriteriaFilter()
	{
		return entryPeer::getCriteriaFilter();
	}
	
	/**
	 * Applies all filter fields and unset the handled fields
	 * 
	 * @param baseObjectFilter $filter
	 */
	protected function applyFilterFields(baseObjectFilter $filter)
	{
		if ($filter->get("_matchand_categories") !== null)
		{
			$filter->set("_matchand_categories_ids", $filter->categoryNamesToIds($filter->get("_matchand_categories")));
			$filter->unsetByName('_matchand_categories');
		}
			
		if ($filter->get("_matchor_categories") !== null)
		{
			$filter->set("_matchor_categories_ids", $filter->categoryNamesToIds($filter->get("_matchor_categories")));
			$filter->unsetByName('_matchor_categories');
		}
			
//		if ($filter->get("_matchor_duration_type") !== null)
//			$filter->set("_matchor_duration_type", $filter->durationTypesToIndexedStrings($filter->get("_matchor_duration_type")));
			
		if ($filter->get(baseObjectFilter::ORDER) === "recent")
		{
			$filter->set("_lte_available_from", time());
			$filter->set("_gteornull_end_date", time()); // schedule not finished
			$filter->set(baseObjectFilter::ORDER, "-available_from");
		}
		
		if($filter->get('_free_text'))
		{
			$freeTexts = $filter->get('_free_text');
			KalturaLog::debug("Attach free text [$freeTexts]");
			
			$additionalConditions = array();
			$advancedSearch = $filter->getAdvancedSearch();
			if($advancedSearch)
			{
				$additionalConditions = $advancedSearch->getFreeTextConditions($freeTexts);
			}
			
			if(preg_match('/^"[^"]+"$/', $freeTexts))
			{
				$freeText = str_replace('"', '', $freeTexts);
				$freeText = "^$freeText$";
				$additionalConditions[] = "@(" . entryFilter::FREE_TEXT_FIELDS . ") $freeText";
			}
			else
			{
				if(strpos($freeTexts, baseObjectFilter::IN_SEPARATOR) > 0)
				{
					str_replace(baseObjectFilter::AND_SEPARATOR, baseObjectFilter::IN_SEPARATOR, $freeTexts);
				
					$freeTextsArr = explode(baseObjectFilter::IN_SEPARATOR, $freeTexts);
					foreach($freeTextsArr as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($freeTextsArr[$valIndex]);
						else
							$freeTextsArr[$valIndex] = $valValue;
					}
							
					foreach($freeTextsArr as $freeText)
					{
						$additionalConditions[] = "@(" . entryFilter::FREE_TEXT_FIELDS . ") $freeText";
					}
				}
				else
				{
					$freeTextsArr = explode(baseObjectFilter::AND_SEPARATOR, $freeTexts);
					foreach($freeTextsArr as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 1)
							unset($freeTextsArr[$valIndex]);
						else
							$freeTextsArr[$valIndex] = $valValue;
					}
							
					$freeTextExpr = implode(baseObjectFilter::AND_SEPARATOR, $freeTextsArr);
					$additionalConditions[] = "@(" . entryFilter::FREE_TEXT_FIELDS . ") $freeTextExpr";
				}
			}
			if(count($additionalConditions))
				$this->matchClause[] = implode(' | ', $additionalConditions);
		}
		$filter->unsetByName('_free_text');
		
		return parent::applyFilterFields($filter);
	}
	
	/**
	 * Applies a single filter
	 * 
	 * @param baseObjectFilter $filter
	 */
	protected function applyPartnerScope(entryFilter $filter)
	{
		// depending on the partner_search_scope - alter the against_str 
		$partner_search_scope = $filter->getPartnerSearchScope();
		if ( baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE == $partner_search_scope )
		{
			// add nothing the the match
		}
		elseif ( $partner_search_scope == null  )
		{
			$this->add(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK);
		}
		else
		{
			$this->add(entryPeer::PARTNER_ID, $partner_search_scope, Criteria::IN);
		}
	}
	
	/**
	 * Applies a single filter
	 * 
	 * @param baseObjectFilter $filter
	 */
	protected function applyFilter(baseObjectFilter $filter)
	{
		$this->applyPartnerScope($filter);
		parent::applyFilter($filter);
	}

	public function getSearchFieldName($fieldName)
	{
		if(!isset(self::$mySqlFields[$fieldName]))
			return $fieldName;
			
		return self::$mySqlFields[$fieldName];
	}
	
	public function hasMatchableField ( $field_name )
	{
		return in_array($field_name, array("name", "description", "tags", "admin_tags", "categories_ids", "flavor_params_ids", "duration_type"));
	}
}
