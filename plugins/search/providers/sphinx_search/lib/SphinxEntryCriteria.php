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
                $redirectEntry = entryPeer::retrieveByPK($redirectEntryId);
                if ( is_null( $redirectEntryId ) || $redirectEntry->getStatus() != entryStatus::READY) // No redirection required?
				{
					$filter->set( '_eq_id', $origEntryId ); // Continue with original entry id
				}
				else
				{
					// Get the redirected entry and check if it exists and is ready
					$redirectedEntry = entryPeer::retrieveByPK( $redirectEntryId );
					
					if (!empty($redirectedEntry) && 
						($redirectedEntry->getStatus() == entryStatus::READY || 
							myEntryUtils::shouldServeVodFromLive($redirectEntryId)
						)
					)
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

		if($filter->is_set('_has_media_server_hostname'))
		{
			//sphinx query: select in(dynamic_attributes.xyz, 'some_val') or in(dynamic_attributes.xyz, 'some_val') as cnd1 from ... where cnd1 > 0 ...
			$mediaServerHostname = $filter->get('_has_media_server_hostname');
			$cond = "in(" . entryIndex::DYNAMIC_ATTRIBUTES . "." . LiveEntry::PRIMARY_HOSTNAME .", '" . $mediaServerHostname . "') or in(" . entryIndex::DYNAMIC_ATTRIBUTES . "." . LiveEntry::SECONDARY_HOSTNAME .", '" . $mediaServerHostname . "')";
			$this->addCondition($cond);
			$filter->unsetByName('_has_media_server_hostname');
		}
	
		if($filter->is_set('_is_live'))
		{
			$this->addCondition(entryIndex::DYNAMIC_ATTRIBUTES . '.' . LiveEntry::IS_LIVE . ' = ' . ($filter->get('_is_live') == '1' ? '1' : '0') );
			$filter->unsetByName('_is_live');
		}
		
		if($filter->is_set('_is_recorded_entry_id_empty'))
		{
			$fieldName = entryIndex::DYNAMIC_ATTRIBUTES . '.' . LiveEntry::RECORDED_ENTRY_ID;
			$this->addWhere( "$fieldName " . ($filter->get('_is_recorded_entry_id_empty') ? "IS" : "IS NOT") . " NULL" );

			$filter->unsetByName('_is_recorded_entry_id_empty');
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
		
		//When setting parent entry ID we also set the root entry id so the entry should be indexed with the root entry prefix
		if($filter->is_set('_eq_parent_entry_id'))
		{
			$matchOrRoots[] = entry::ROOTS_FIELD_PARENT_ENTRY_PREFIX . '_' . $filter->get('_eq_parent_entry_id');
			$filter->unsetByName('_eq_parent_entry_id');
		}
		
		if($filter->is_set('_is_root'))
		{
			if($filter->get('_is_root'))
				$filter->set('_notin_roots', entry::ROOTS_FIELD_ENTRY_PREFIX);
			else
				$matchOrRoots[] = entry::ROOTS_FIELD_ENTRY_PREFIX;
				
			$filter->unsetByName('_is_root');
		}

		if($filter->is_set('_eq_reference_id'))
		{
			$refId = $filter->get('_eq_reference_id');
			if( $refId!=null && $refId!='' ) {
				$this->addMatch("@reference_id " . $this->buildReferenceIdMatchString($refId));
				$filter->unsetByName('_eq_reference_id');
			}
		}

		if ($filter->is_set('_in_reference_id'))
		{
			$refIds = explode(",",$filter->get('_in_reference_id'));
			$condition = "";
			for ($i=0; $i< count($refIds); $i++ ) {
				$condition .= "(" . $this->buildReferenceIdMatchString($refIds[$i]) . ")";
				if ( $i < count($refIds) - 1 )
					$condition .= " | ";
			}
			$this->addMatch("@reference_id $condition");
			$filter->unsetByName('_in_reference_id');
		}

		if(count($matchOrRoots))
			$filter->set('_matchand_roots', $matchOrRoots);
			
//		if ($filter->get("_matchor_duration_type") !== null)
//			$filter->set("_matchor_duration_type", $filter->durationTypesToIndexedStrings($filter->get("_matchor_duration_type")));
			
		if($filter->get(baseObjectFilter::ORDER) === "recent" || $filter->get(baseObjectFilter::ORDER) === "-recent")
		{
			$filter->set("_lte_available_from", kApiCache::getTime());
			//$filter->set("_gteornull_end_date", time()); // schedule not finished
			$filter->set(baseObjectFilter::ORDER, "-available_from");
		}
			
		if($filter->get(baseObjectFilter::ORDER) === "+recent")
		{
			$filter->set(baseObjectFilter::ORDER, "+available_from");
		}
		
		if($filter->get(baseObjectFilter::ORDER) === "-first_broadcast")
		{
			$this->addOrderBy(entryIndex::DYNAMIC_ATTRIBUTES . '.' . LiveEntry::FIRST_BROADCAST, Criteria::DESC);
			$filter->set(baseObjectFilter::ORDER, null);
		}
		
		if($filter->get(baseObjectFilter::ORDER) === "+first_broadcast")
		{
			$this->addOrderBy(entryIndex::DYNAMIC_ATTRIBUTES . '.' . LiveEntry::FIRST_BROADCAST, Criteria::ASC);
			$filter->set(baseObjectFilter::ORDER, null);
		}
		
		if($filter->get('_free_text'))
		{
			$freeTexts = $filter->get('_free_text');
			KalturaLog::debug("Attach free text [$freeTexts]");
			
			$additionalConditions = array();
			$advancedSearch = $filter->getAdvancedSearch();
			if($advancedSearch)
				$additionalConditions = $advancedSearch->getFreeTextConditions($filter->getPartnerSearchScope(), $freeTexts);

			$this->addFreeTextToMatchClauseByMatchFields($freeTexts, entryFilter::FREE_TEXT_FIELDS, $additionalConditions);
		}
		$filter->unsetByName('_free_text');
		
		return parent::applyFilterFields($filter);
	}

	private function buildReferenceIdMatchString( $refId )
	{
		$notEmpty = kSphinxSearchManager::HAS_VALUE . kCurrentContext::getCurrentPartnerId();
		return "\\\" " . mySearchUtils::getMd5EncodedString($refId) . " $notEmpty$\\\"";
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
