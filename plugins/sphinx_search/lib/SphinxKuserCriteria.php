<?php
/**
 * @package plugins.sphinxSearch
 * @subpackage model.filters
 */
class SphinxKuserCriteria extends SphinxCriteria
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getIndexObjectName() {
    	return "kuserIndex";
    }
    
	/* (non-PHPdoc)
	 * @see SphinxCriteria::applyFilterFields()
	 */
	protected function applyFilterFields(baseObjectFilter $filter)
	{		
		//Role ids and kuser permission names are indexed with the partner ID
		if ($filter->get('_eq_role_ids'))
		{
			$filter->set('_eq_role_ids', kuser::getIndexedFieldValue('kuserPeer::ROLE_IDS', $filter->get('_eq_role_ids'), kCurrentContext::getCurrentPartnerId()));
		}
		if ($filter->get('_in_role_ids'))
		{
			$filter->set('_eq_role_ids', kuser::getIndexedFieldValue('kuserPeer::ROLE_IDS', $filter->get('_eq_role_ids'), kCurrentContext::getCurrentPartnerId()));
		}
		if ($filter->get('_mlikeand_permission_names'))
		{
			$permissionNames = kuser::getIndexedFieldValue('kuserPeer::PERMISSION_NAMES', $filter->get('_mlikeand_permission_names'), kCurrentContext::getCurrentPartnerId());
			$permissionNames = implode(' ', explode(',', $permissionNames));
			$universalPermissionName = kuser::getIndexedFieldValue('kuserPeer::PERMISSION_NAMES', kuser::UNIVERSAL_PERMISSION, kCurrentContext::getCurrentPartnerId());
			$value = "($universalPermissionName | ($permissionNames))";
			$this->addMatch("@permission_names $value");
			$filter->unsetByName('_mlikeand_permission_names');
		}
		if ($filter->get('_mlikeor_permission_names'))
		{
			$filter->set('_mlikeor_permission_names', kuser::getIndexedFieldValue('kuserPeer::PERMISSION_NAMES', $filter->get('_mlikeor_permission_names').','.kuser::UNIVERSAL_PERMISSION, kCurrentContext::getCurrentPartnerId()));
		}
		
		if($filter->get('_likex_puser_id_or_screen_name'))
		{
			$freeTexts = $filter->get('_likex_puser_id_or_screen_name');
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
				$additionalConditions[] = "@(" . kuserFilter::PUSER_ID_OR_SCREEN_NAME . ") $freeText\\\*";
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
						$additionalConditions[] = "@(" . kuserFilter::PUSER_ID_OR_SCREEN_NAME . ") $freeText\\\*";
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
					
					$additionalConditions[] = "@(" . kuserFilter::PUSER_ID_OR_SCREEN_NAME . ") $freeTextExpr\\\*";
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
		
		$filter->unsetByName('_likex_puser_id_or_screen_name');
		
		
		if($filter->get('_likex_first_name_or_last_name'))
		{
			$names = $filter->get('_likex_first_name_or_last_name');
			KalturaLog::debug("Attach free text [$names]");
			
			$additionalConditions = array();
			if(preg_match('/^"[^"]+"$/', $names))
			{
				$name = str_replace('"', '', $names);
				$name = SphinxUtils::escapeString($name);
				$name = "^$name$";
				$additionalConditions[] = "@(" . kuserFilter::FIRST_NAME_OR_LAST_NAME . ") $name\\\*";
			}
			else
			{
				if(strpos($names, baseObjectFilter::IN_SEPARATOR) > 0)
				{
					str_replace(baseObjectFilter::AND_SEPARATOR, baseObjectFilter::IN_SEPARATOR, $names);
				
					$namesArr = explode(baseObjectFilter::IN_SEPARATOR, $names);
					foreach($namesArr as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 0)
							unset($namesArr[$valIndex]);
						else
							$namesArr[$valIndex] = SphinxUtils::escapeString($valValue);
					}
							
					foreach($namesArr as $name)
					{
						$additionalConditions[] = "@(" . kuserFilter::FIRST_NAME_OR_LAST_NAME . ") $name\\\*";
					}
				}
				else
				{
					$namesArr = explode(baseObjectFilter::AND_SEPARATOR, $names);
					foreach($namesArr as $valIndex => $valValue)
					{
						if(!is_numeric($valValue) && strlen($valValue) <= 0)
							unset($namesArr[$valIndex]);
						else
							$namesArr[$valIndex] = SphinxUtils::escapeString($valValue);
					}
						
					$namesArr = array_unique($namesArr);
					$nameExpr = implode(baseObjectFilter::AND_SEPARATOR, $namesArr);
					
					$additionalConditions[] = "@(" . kuserFilter::FIRST_NAME_OR_LAST_NAME . ") $nameExpr\\\*";
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
		
		$filter->unsetByName('_likex_first_name_or_last_name');
		
		return parent::applyFilterFields($filter);
	}
    
}