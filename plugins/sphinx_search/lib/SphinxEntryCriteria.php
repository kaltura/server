<?php
/**
 * @package plugins.sphinxSearch
 * @subpackage model.filters
 */
class SphinxEntryCriteria extends SphinxCriteria
{
	public function getIndexObjectName() {
		return "entryIndex";
	} 
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::applyFilterFields()
	 */
	protected function applyFilterFields(baseObjectFilter $filter)
	{
		/* @var $filter entryFilter */

		if ( $filter->is_set('_eq_redirect_from_entry_id' ) )
		{
			$partnerGroup = array(kCurrentContext::getCurrentPartnerId(), PartnerPeer::GLOBAL_PARTNER);
			$criteriaFilter = entryPeer::getCriteriaFilter();
			$defaultCriteria = $criteriaFilter->getFilter();
			$defaultCriteria->remove(entryPeer::PARTNER_ID);
			$defaultCriteria->add(entryPeer::PARTNER_ID, $partnerGroup, Criteria::IN);
						
			$origEntryId = $filter->get( '_eq_redirect_from_entry_id' );
			$origEntry = entryPeer::retrieveByPK($origEntryId);				
			
			if ( ! empty( $origEntry ) )
			{
				if ( $origEntry->getType() == entryType::LIVE_STREAM )
				{
					// Set a relatively short expiry value in order to reduce the wait-time
					// until the cache is refreshed and a redirection kicks-in. 
					kApiCache::setExpiry( kApiCache::REDIRECT_ENTRY_CACHE_EXPIRY );
				}
								
				// Get the id of the entry id that is being redirected from the original entry
				$redirectEntryId = $origEntry->getRedirectEntryId(); 
				
				if ( is_null( $redirectEntryId ) ) // No redirection required? 
				{
					$filter->set( '_eq_id', $origEntryId ); // Continue with original entry id
				}
				else
				{
					// Get the redirected entry and check if it exists and is ready
					$redirectedEntry = entryPeer::retrieveByPK( $redirectEntryId );
					
					if ( ! empty( $redirectedEntry )
							&& $redirectedEntry->getStatus() == entryStatus::READY )
					{
						// Redirected entry is ready.
						// Set it as the replacement of the original one
						$filter->set( '_eq_id', $redirectEntryId );
					}
					else
					{
						// Can't redirect? --> Fallback to the original entry
						$filter->set( '_eq_id', $origEntryId );
					}
				}
			}
			else
			{
				throw new kCoreException( "Invalid entry id [\"$origEntryId\"]", kCoreException::INVALID_ENTRY_ID, $origEntryId );
			}

			$filter->unsetByName( '_eq_redirect_from_entry_id' );
		}
		
		$categoriesAncestorParsed = null;
		$categories = $filter->get( "_in_category_ancestor_id");
		if ($categories !== null)
		{
			//if the category exist or the category name is an empty string
			$categoriesAncestorParsed = $filter->categoryIdsToAllSubCategoriesIdsParsed ( $categories );

			if(!( $categoriesAncestorParsed !=='' || $categories ==''))
				$categoriesAncestorParsed = category::CATEGORY_ID_THAT_DOES_NOT_EXIST;
		}
		$filter->unsetByName('_in_category_ancestor_id');
		
		$categories = $filter->get("_matchor_categories_ids");
		if ($categories !== null)
		{
			//if the category exist or the category name is an empty string
			if(is_null($categoriesAncestorParsed))
				$categoriesParsed = $filter->categoryIdsToIdsParsed ( $categories );
			else 
				$categoriesParsed = $categoriesAncestorParsed;
			
			if ( $categoriesParsed !=='' || $categories =='')
				$filter->set ( "_matchor_categories_ids", $categoriesParsed);
			else
		  		$filter->set ( "_matchor_categories_ids", category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
		}else
		{
			$filter->set ( "_matchor_categories_ids", $categoriesAncestorParsed);
		}
		
		$categories = $filter->get( "_matchand_categories_ids");
		if ($categories !== null)
		{
			//if the category exist or the category name is an empty string
			$categoriesParsed = $filter->categoryIdsToIdsParsed ( $categories );
			
			if ( $categoriesParsed !=='' || $categories =='')
				$filter->set ( "_matchand_categories_ids", $categoriesParsed);
			else
		  		$filter->set ( "_matchand_categories_ids", category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
		}
		
		$categoriesIds = $filter->get("_notcontains_categories_ids");
		if ($categoriesIds !== null)
		{
			$categoriesParsed = $filter->categoryIdsToAllSubCategoriesIdsParsed($categoriesIds, CategoryEntryStatus::ACTIVE.','.CategoryEntryStatus::PENDING.','.CategoryEntryStatus::REJECTED);
			if ( $categoriesParsed !=='' || $categoriesIds =='')
				$filter->set ( "_notcontains_categories_ids", $categoriesParsed);
			else
		  		$filter->set ( "_notcontains_categories_ids", category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
		}
		
		$matchAndCats = $filter->get("_matchand_categories");
		if ($matchAndCats !== null)
		{
			//if the category exist or the category name is an empty string
			$categoriesParsed = $filter->categoryFullNamesToIdsParsed ( $matchAndCats, CategoryEntryStatus::ACTIVE );
			if ( $categoriesParsed !=='' || $matchAndCats =='')
				$filter->set ( "_matchand_categories_ids", $categoriesParsed );
			else
		  		$filter->set ( "_matchand_categories_ids", category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
			$filter->unsetByName('_matchand_categories');
		}
		
		$matchOrCats = $filter->get("_matchor_categories");
		if ($matchOrCats !== null)
		{
			//if the category exist or the category name is an empty string
			$categoriesParsed = $filter->categoryFullNamesToIdsParsed ( $matchOrCats, CategoryEntryStatus::ACTIVE );
			if( $categoriesParsed !=='' || $matchOrCats=='')
				$filter->set("_matchor_categories_ids", $categoriesParsed);
			else
				$filter->set ( "_matchor_categories_ids",category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
			$filter->unsetByName('_matchor_categories');
		}
		
		$notContainsCats = $filter->get("_notcontains_categories");
		if ($notContainsCats !== null)
		{
			//if the category exist or the category name is an empty string
			$categoriesParsed = $filter->categoryFullNamesToIdsParsed ($notContainsCats, CategoryEntryStatus::ACTIVE.','.CategoryEntryStatus::PENDING.','.CategoryEntryStatus::REJECTED);
			if( $categoriesParsed !=='' || $notContainsCats=='')
				$filter->set("_notcontains_categories_ids", $categoriesParsed);
			else
				$filter->set ( "_notcontains_categories_ids",category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
			$filter->unsetByName('_notcontains_categories');
		}
		
		// match categories by full name		
		$CatFullNameIn = $filter->get("_in_categories_full_name");
		if ($CatFullNameIn !== null)
		{
			//if the category exist or the category name is an empty string
			$categoriesParsed = $filter->categoryFullNamesToIdsParsed ( $CatFullNameIn, CategoryEntryStatus::ACTIVE );
			if( $categoriesParsed !=='' || $CatFullNameIn=='')
				$filter->set("_matchor_categories_ids", $categoriesParsed);
			else
				$filter->set ( "_matchor_categories_ids",category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
			$filter->unsetByName('_in_categories_full_name');
		}		
	
		if($filter->is_set('_is_live'))
		{
			$this->addCondition(entryIndex::DYNAMIC_ATTRIBUTES . '.' . LiveEntry::IS_LIVE . ' = ' . $filter->get('_is_live'));
			$filter->unsetByName('_is_live');
		}
		
		$matchOrRoots = array();
		if($filter->is_set('_eq_root_entry_id'))
		{
			$matchOrRoots[] = entry::ROOTS_FIELD_ENTRY_PREFIX . ' ' . $filter->get('_eq_root_entry_id');
			$filter->unsetByName('_eq_root_entry_id');
		}
		
		if($filter->is_set('_in_root_entry_id'))
		{
			$roots = explode(baseObjectFilter::IN_SEPARATOR, $filter->get('_in_root_entry_id'));
			foreach($roots as $root)
				$matchOrRoots[] = entry::ROOTS_FIELD_ENTRY_PREFIX . " $root";
				
			$filter->unsetByName('_in_root_entry_id');
		}
		
		if($filter->is_set('_is_root'))
		{
			if($filter->get('_is_root'))
				$filter->set('_notin_roots', entry::ROOTS_FIELD_ENTRY_PREFIX);
			else
				$matchOrRoots[] = entry::ROOTS_FIELD_ENTRY_PREFIX;
				
			$filter->unsetByName('_is_root');
		}
		if(count($matchOrRoots))
			$filter->set('_matchand_roots', $matchOrRoots);
			
//		if ($filter->get("_matchor_duration_type") !== null)
//			$filter->set("_matchor_duration_type", $filter->durationTypesToIndexedStrings($filter->get("_matchor_duration_type")));
			
		if($filter->get(baseObjectFilter::ORDER) === "recent" || $filter->get(baseObjectFilter::ORDER) === "-recent")
		{
			$filter->set("_lte_available_from", time());
			//$filter->set("_gteornull_end_date", time()); // schedule not finished
			$filter->set(baseObjectFilter::ORDER, "-available_from");
		}
			
		if($filter->get(baseObjectFilter::ORDER) === "+recent")
		{
			$filter->set(baseObjectFilter::ORDER, "+available_from");
		}
		
		$fps = $filter->get("_matchand_flavor_params_ids");
		if($fps) {
			if (empty($fps))
				$fps = array();
			else
				$fps = explode(",", $fps);
			$filter->unsetByName('_matchand_flavor_params_ids');
			$filter->set("_matchand_flavor_params_ids", entry::formatFlavorParamsIds($fps, kCurrentContext::getCurrentPartnerId()));
		}
		
		$fps = $filter->get("_matchor_flavor_params_ids");
		if($fps) {
			if (empty($fps))
				$fps = array();
			else
				$fps = explode(",", $fps);
			$filter->unsetByName('_matchor_flavor_params_ids');
			$filter->set("_matchor_flavor_params_ids", entry::formatFlavorParamsIds($fps, kCurrentContext::getCurrentPartnerId()));
		}

		if($filter->get('_free_text'))
		{
			$freeTexts = $filter->get('_free_text');
			KalturaLog::debug("Attach free text [$freeTexts]");
			
			$additionalConditions = array();
			$advancedSearch = $filter->getAdvancedSearch();
			if($advancedSearch)
			{
				$additionalConditions = $advancedSearch->getFreeTextConditions($filter->getPartnerSearchScope(), $freeTexts);
			}
			
			if(preg_match('/^"[^"]+"$/', $freeTexts))
			{
				$freeText = str_replace('"', '', $freeTexts);
				$freeText = SphinxUtils::escapeString($freeText);
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
						if(!is_numeric($valValue) && strlen($valValue) <= 0)
							unset($freeTextsArr[$valIndex]);
						else
							$freeTextsArr[$valIndex] = SphinxUtils::escapeString($valValue);
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
						if(!is_numeric($valValue) && strlen($valValue) <= 0)
							unset($freeTextsArr[$valIndex]);
						else
							$freeTextsArr[$valIndex] = SphinxUtils::escapeString($valValue);
					}
							
					$freeTextsArr = array_unique($freeTextsArr);
					$freeTextExpr = implode(baseObjectFilter::AND_SEPARATOR, $freeTextsArr);
					$additionalConditions[] = "@(" . entryFilter::FREE_TEXT_FIELDS . ") $freeTextExpr";
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
			// add nothing the the partner match
			
		}
		elseif ( $partner_search_scope == null  )
		{
			$this->add(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK);
		}
		else
		{
			if(count($partner_search_scope) == 1)
				$this->add(entryPeer::PARTNER_ID, $partner_search_scope, Criteria::EQUAL);
			else
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

	public function hasPeerFieldName($fieldName)
	{
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = "entry.$fieldName";
		}
		
		$entryFields = entryPeer::getFieldNames(BasePeer::TYPE_COLNAME);
		
		return in_array($fieldName, $entryFields);
	}
	
	public function getFieldPrefix ($fieldName)
	{
		if($fieldName == 'roots')
			return entry::ROOTS_FIELD_PREFIX;
		
		if ($fieldName == 'categories')
			return entry::CATEGORIES_INDEXED_FIELD_PREFIX.kCurrentContext::getCurrentPartnerId();	
			
		return parent::getFieldPrefix($fieldName);
	}
}
