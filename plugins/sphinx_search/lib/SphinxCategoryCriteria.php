<?php
/**
 * @package plugins.sphinxSearch
 * @subpackage model.filters
 */
class SphinxCategoryCriteria extends SphinxCriteria
{
	public function getIndexObjectName() {
		return "categoryIndex";
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::applyFilterFields()
	 */
	protected function applyFilterFields(baseObjectFilter $filter)
	{				
		
		$partnerId = kCurrentContext::getCurrentPartnerId();
		
		$categories = $filter->get( "_matchor_likex_full_name");
		if ($categories !== null)
		{
			$categories = explode(',', $categories);
			$parsedCategories = array();
			foreach ($categories as $category)
			{
				if(trim($category) == '')
					continue;
				
				$parsedCategories[] = $category . '\\*';
			}
			
			$fullNameMatchOr = '';
			if(count($parsedCategories))
				$fullNameMatchOr = implode(',' , $parsedCategories);
			
			if($fullNameMatchOr != '')
				$filter->set ( "_matchor_full_name", $fullNameMatchOr);
		}
		$filter->unsetByName('_matchor_likex_full_name');
		
		if($filter->get('_free_text'))
		{
			$freeTexts = $filter->get('_free_text');
			
			$additionalConditions = array();
			$advancedSearch = $filter->getAdvancedSearch();
			if($advancedSearch)
			{
				$additionalConditions = $advancedSearch->getFreeTextConditions($filter->getPartnerSearchScope(), $freeTexts);
			}
			
			$this->addFreeTextToMatchClauseByMatchFields($freeTexts, categoryFilter::FREE_TEXT_FIELDS, $additionalConditions);
		}
		$filter->unsetByName('_free_text');

		if($filter->get('_eq_privacy_context'))
		{
			if ($filter->get('_eq_privacy_context') == '*')
			{
				$this->addOr(categoryPeer::PRIVACY_CONTEXT, kEntitlementUtils::NOT_DEFAULT_CONTEXT, Criteria::LIKE);
				$filter->unsetByName('_eq_privacy_context');
			}
			elseif ($filter->get('_eq_privacy_context') != '')
			{
				$this->addOr(categoryPeer::PRIVACY_CONTEXT, $filter->get('_eq_privacy_context'), Criteria::LIKE);
				$filter->unsetByName('_eq_privacy_context');
			}
		}

		if($filter->get('_eq_manager'))
		{
			$puserId = $filter->get('_eq_manager');
			$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $puserId);
			if($kuser)
			{
				$manager = category::getPermissionLevelName(CategoryKuserPermissionLevel::MANAGER);
				$this->matchClause[] = '(@(' . categoryFilter::MEMBERS . ') ' . $manager . '_' . $kuser->getid() . ')';
			}
		}
		$filter->unsetByName('_eq_manager');
		
		if($filter->get('_eq_member'))
		{
			$puserId = $filter->get('_eq_member');
			$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $puserId);
			if($kuser)
			{
				$manager = category::getPermissionLevelName(CategoryKuserPermissionLevel::MANAGER);
				$member = category::getPermissionLevelName(CategoryKuserPermissionLevel::MEMBER);
				$moderator = category::getPermissionLevelName(CategoryKuserPermissionLevel::MODERATOR);
				$contributor = category::getPermissionLevelName(CategoryKuserPermissionLevel::CONTRIBUTOR);
				$kuserId = $kuser->getid();
				$this->matchClause[] = '(@(' . categoryFilter::MEMBERS . ') ' . 
					"({$member}_{$kuserId} | {$moderator}_{$kuserId} | {$contributor}_{$kuserId} ) !({$manager}_{$kuserId}))";
			}
		}
		$filter->unsetByName('_eq_member');
		
		
		if($filter->get('_eq_full_name'))
		{
			$filter->set('_matchor_full_name', $filter->get('_eq_full_name') . category::FULL_NAME_EQUAL_MATCH_STRING);
		}
		$filter->unsetByName('_eq_full_name');
		
		if($filter->get('_in_full_name'))
		{
			$fullnames = explode(',', $filter->get('_in_full_name'));
			
			$fullnameIn = '';
			foreach($fullnames as $fullname)
				$fullnameIn .= $fullname . category::FULL_NAME_EQUAL_MATCH_STRING . ',';
			
			$filter->set('_matchor_full_name', $fullnameIn);
			$filter->unsetByName('_in_full_name');
		}

		$categories = $filter->get( "_in_ancestor_id");
		if ($categories !== null)
		{
			//if the category exist or the category name is an empty string
			$categoriesParsed = $filter->categoryIdsToAllSubCategoriesIdsParsed ( $categories );
			if ( $categoriesParsed !=='' || $categories =='')
				$filter->set ( "_matchor_full_ids", $categoriesParsed);
			else
		  		$filter->set ( "_matchor_full_ids", category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
		}
		$filter->unsetByName('_in_ancestor_id');
		
		if($filter->get('_likex_full_ids'))
		{
			$fullids = explode(',', $filter->get('_likex_full_ids'));
			
			$fullIdsIn = '';
			foreach($fullids as $fullid)
				$fullIdsIn .= $fullid . '\\*,';
			
			$filter->set('_matchor_full_ids', $fullIdsIn);
			$filter->unsetByName('_likex_full_ids');
		}
		
		if($filter->get('_eq_full_ids'))
		{
			$filter->set('_matchor_full_ids', $filter->get('_eq_full_ids') . category::FULL_IDS_EQUAL_MATCH_STRING);
		}
		$filter->unsetByName('_eq_full_ids');

		if($filter->get('_likex_name_or_reference_id'))
		{
			$names = $filter->get('_likex_name_or_reference_id');
			$this->addFreeTextToMatchClauseByMatchFields($names, categoryFilter::NAME_REFERNCE_ID, null, true);
		}
		$filter->unsetByName('_likex_name_or_reference_id');
		
		
		if($filter->get('_eq_privacy')) {
			$filter->set('_eq_privacy', $filter->get('_eq_privacy') . "P" . $partnerId);
		}
		
		if($filter->get('_in_privacy'))  {
			$privacyIn = explode(',', $filter->get('_in_privacy'));
				
			$newPrivacyIn = array();
			foreach($privacyIn as $privacy)
				$newPrivacyIn[] =  $privacy . "P" . $partnerId;
				
			$filter->set('_in_privacy', implode(",", $newPrivacyIn));
		}
		
		if($filter->get('_eq_display_in_search')) {
			$filter->set('_eq_display_in_search', $filter->get('_eq_display_in_search'));
		}
		
		return parent::applyFilterFields($filter);
	}
	
	public function hasPeerFieldName($fieldName)
	{
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = "category.$fieldName";
		}

		$categoryFields = categoryPeer::getFieldNames(BasePeer::TYPE_COLNAME);
		
		return in_array($fieldName, $categoryFields);
	}
	
	public function translateSphinxCriterion(SphinxCriterion $crit)
	{
		$field = $crit->getTable() . '.' . $crit->getColumn();
		$partnerId = kCurrentContext::getCurrentPartnerId();

		if ($field == categoryPeer::FULL_NAME && $crit->getComparison() == Criteria::EQUAL)
		{
			return array(
					categoryPeer::FULL_NAME, 
					Criteria::LIKE, 
					$crit->getValue() . category::FULL_NAME_EQUAL_MATCH_STRING);
		}		
		else if ($field == categoryPeer::FULL_NAME && $crit->getComparison() == Criteria::IN)
		{
			return array(
					categoryPeer::FULL_NAME, 
					Criteria::IN_LIKE,
					kString::addSuffixToArray($crit->getValue(), category::FULL_NAME_EQUAL_MATCH_STRING));
		} else if ($field == categoryPeer::DISPLAY_IN_SEARCH  && $crit->getComparison() == Criteria::EQUAL)
		{
			return array(
					categoryPeer::DISPLAY_IN_SEARCH,
					Criteria::EQUAL,
					$crit->getValue() . "P" . $partnerId);
		} else if ($field == categoryPeer::PRIVACY_CONTEXT  && $crit->getComparison() == Criteria::EQUAL)
		{
			return array(
				categoryPeer::PRIVACY_CONTEXT,
				Criteria::EQUAL,
				kEntitlementUtils::PARTNER_ID_PREFIX . $partnerId.$crit->getValue());
		}else if ($field == categoryPeer::PRIVACY_CONTEXT  && $crit->getComparison() == Criteria::IN )
		{
			return array(
				categoryPeer::PRIVACY_CONTEXT,
				Criteria::IN_LIKE,
				kEntitlementUtils::addPrivacyContextsPrefix($crit->getValue(),$partnerId));
		}else if ($field == categoryPeer::PRIVACY_CONTEXT && $crit->getComparison() == Criteria::LIKE)
		{
			return array(
				categoryPeer::PRIVACY_CONTEXT,
				Criteria::LIKE,
				kEntitlementUtils::PARTNER_ID_PREFIX . $partnerId . $crit->getValue());
		}else if ($field == categoryPeer::PRIVACY_CONTEXTS  && $crit->getComparison() == Criteria::EQUAL)
		{
			return array(
				categoryPeer::PRIVACY_CONTEXTS,
				Criteria::EQUAL,
				kEntitlementUtils::PARTNER_ID_PREFIX . $partnerId.$crit->getValue());
		}else if ($field == categoryPeer::PRIVACY_CONTEXTS  && $crit->getComparison() == Criteria::IN )
		{
			return array(
				categoryPeer::PRIVACY_CONTEXTS,
				Criteria::IN_LIKE,
				kEntitlementUtils::addPrivacyContextsPrefix($crit->getValue(),$partnerId));
		}

		return parent::translateSphinxCriterion($crit);
	}
	
	protected function applyIds(array $ids)
	{
		return $ids;
	}
}
