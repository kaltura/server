<?php
/**
 * @package plugins.sphinxSearch
 * @subpackage model.filters
 */
class SphinxCategoryKuserCriteria extends SphinxCriteria
{
	public function getIndexObjectName() {
		return "categoryKuserIndex";
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::getFieldPrefix()
	 */
	public function getFieldPrefix ($fieldName)
	{
		switch ($fieldName)
		{
			case 'permission_names':
				return categoryKuser::PERMISSION_NAME_FIELD_INDEX_PREFIX. kCurrentContext::getCurrentPartnerId();
			case 'category_kuser_status':
				return categoryKuser::STATUS_FIELD_PREFIX . kCurrentContext::getCurrentPartnerId();
		}

		return parent::getFieldPrefix($fieldName);
	}

	/* (non-PHPdoc)
	 * @see SphinxCriteria::applyFilterFields()
	 */
	protected function applyFilterFields(baseObjectFilter $filter)
	{
		if ($filter->get('_in_status'))
		{
			$statusList = explode(',', $filter->get('_in_status'));
			$statusList = $this->translateToSearchIndexFieldValue(categoryKuserPeer::STATUS, $statusList);
			$filter->set('_in_status', implode(',', $statusList));
		}
		
		if ($filter->get('_eq_status'))
		{
			$filter->set('_eq_status', categoryKuser::getSearchIndexFieldValue(categoryKuserPeer::STATUS, $filter->get('_eq_status'), kCurrentContext::getCurrentPartnerId()));
		}
		
		if ($filter->is_set('_in_update_method') && $filter->get('_in_update_method') != "")
		{
			$updateMethodList = explode(',', $filter->get('_in_update_method'));
			$updateMethodList = $this->translateToSearchIndexFieldValue(categoryKuserPeer::UPDATE_METHOD, $updateMethodList);
			$filter->set('_in_update_method', implode(',', $updateMethodList));
		}
		
		if ($filter->get('_eq_update_method'))
		{
			$filter->set('_eq_update_method', categoryKuser::getSearchIndexFieldValue(categoryKuserPeer::UPDATE_METHOD, $filter->get('_eq_update_method'), kCurrentContext::getCurrentPartnerId()));
		}
		
		if (!is_null($filter->get('_eq_permission_level')))
		{
			$permissionLevel = $filter->get('_eq_permission_level');
			$permissionNamesList = categoryKuser::getPermissionNamesByPermissionLevel($permissionLevel);
			$negativePermissionNamesList = $this->fixPermissionNamesListForSphinx($permissionLevel);
			
			if($negativePermissionNamesList)
				$filter->set('_notcontains_permission_names', implode(',', $negativePermissionNamesList));
			
			if($filter->get('_matchand_permission_names'))
			{
				$permissionNamesList = $this->translateToSearchIndexFieldValue(categoryKuserPeer::PERMISSION_NAMES, $permissionNamesList);				
				$criterion = $this->getNewCriterion(categoryKuserPeer::PERMISSION_NAMES, $permissionNamesList, baseObjectFilter::MATCH_AND);
				$this->addAnd($criterion);
			}
			else 
				$filter->set('_matchand_permission_names', $permissionNamesList);
				
			$filter->unsetByName('_eq_permission_level');
		}
		
		if ($filter->get('_in_permission_level'))
		{
			$permissionLevels = $filter->get('_in_permission_level');
			$permissionLevels = explode(',', $permissionLevels);
			foreach ($permissionLevels as $permissionLevel)
			{
				$permissionNamesList = categoryKuser::getPermissionNamesByPermissionLevel($permissionLevel);
				$permissionNamesList = $this->translateToSearchIndexFieldValue(categoryKuserPeer::PERMISSION_NAMES, $permissionNamesList);
				$criterion = $this->getNewCriterion(categoryKuserPeer::PERMISSION_NAMES, $permissionNamesList, baseObjectFilter::MATCH_AND);
				$this->addOr($criterion);
			}
			
			$filter->unsetByName('_in_permission_level');
		}
		
		if ($filter->get('_matchor_permission_names'))
		{
			$permissionNamesList = explode(',', $filter->get('_matchor_permission_names'));
			$permissionNamesList = $this->translateToSearchIndexFieldValue(categoryKuserPeer::PERMISSION_NAMES, $permissionNamesList);
			$filter->set('_matchor_permission_names', implode(',', $permissionNamesList));
		}

		if ($filter->get('_matchand_permission_names'))
		{
			$permissionNamesList = explode(',', $filter->get('_matchand_permission_names'));
			$permissionNamesList = $this->translateToSearchIndexFieldValue(categoryKuserPeer::PERMISSION_NAMES, $permissionNamesList);
			$filter->set('_matchand_permission_names', implode(',', $permissionNamesList));
		}
		
		if ($filter->get('_notcontains_permission_names'))
		{
			$permissionNamesList = explode(',', $filter->get('_notcontains_permission_names'));
			$permissionNamesList = $this->translateToSearchIndexFieldValue(categoryKuserPeer::PERMISSION_NAMES, $permissionNamesList);
			$filter->set('_notcontains_permission_names', $permissionNamesList);
		}
		
		if ($filter->get('_eq_category_full_ids'))
		{
			$filter->set('_eq_category_full_ids', $filter->get('_eq_category_full_ids').category::FULL_IDS_EQUAL_MATCH_STRING);
		}
		
		return parent::applyFilterFields($filter);
	}
	
	public function fixPermissionNamesListForSphinx($permissionLevel)
	{
		switch ($permissionLevel)
	    {
	      case CategoryKuserPermissionLevel::MODERATOR:
	        $negativePermissionNamesArr[] = PermissionName::CATEGORY_CONTRIBUTE;
	        break;
	      case CategoryKuserPermissionLevel::CONTRIBUTOR:
	        $negativePermissionNamesArr[] = PermissionName::CATEGORY_MODERATE;
	        break;
	      case CategoryKuserPermissionLevel::MEMBER:
	        $negativePermissionNamesArr[] = PermissionName::CATEGORY_EDIT;
	        $negativePermissionNamesArr[] = PermissionName::CATEGORY_MODERATE;
	        $negativePermissionNamesArr[] = PermissionName::CATEGORY_CONTRIBUTE;
	        break;
	    }
	    
	    return $negativePermissionNamesArr;    
	}
	
	public function translateToSearchIndexFieldValue($fieldName, $toTranslate)
	{
		foreach ($toTranslate as &$translate)
		{
			$translate = categoryKuser::getSearchIndexFieldValue($fieldName, $translate, kCurrentContext::getCurrentPartnerId());
		}
		
		return $toTranslate;
	}
	
	public function translateSphinxCriterion (SphinxCriterion $crit)
	{
		$field = $crit->getTable() . '.' . $crit->getColumn();
		$value = $crit->getValue();
		
		$fieldName = null;
		if ($field == categoryKuserPeer::STATUS)
			$fieldName = categoryKuserPeer::STATUS;

		if ($fieldName)
		{
			$partnerIdCrit = $this->getCriterion(categoryKuserPeer::PARTNER_ID);
			if ($partnerIdCrit && $partnerIdCrit->getComparison() == Criteria::EQUAL)
				$partnerId = $partnerIdCrit->getValue();
			else
				$partnerId = kCurrentContext::getCurrentPartnerId();
			
			if (is_array($value))
			{
				foreach ($value as &$singleValue)
				{
					$singleValue = 	categoryKuser::getSearchIndexFieldValue($fieldName, $singleValue, $partnerId);
				}
			}
			else 
			{
				$value = categoryKuser::getSearchIndexFieldValue($fieldName, $value, $partnerId);
			}
		}

		return array($field, $crit->getComparison(), $value);
	}
	
}