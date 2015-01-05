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
			
			$this->addFreeTextToMatchClauseByMatchFields($freeTexts, kuserFilter::PUSER_ID_OR_SCREEN_NAME, $additionalConditions, true);
		}
		$filter->unsetByName('_likex_puser_id_or_screen_name');
		
		if($filter->get('_likex_first_name_or_last_name'))
		{
			$names = $filter->get('_likex_first_name_or_last_name');
			KalturaLog::debug("Attach free text [$names]");
			
			$this->addFreeTextToMatchClauseByMatchFields($names, kuserFilter::FIRST_NAME_OR_LAST_NAME, null, true);
		}
		$filter->unsetByName('_likex_first_name_or_last_name');
		
		return parent::applyFilterFields($filter);
	}
    
}