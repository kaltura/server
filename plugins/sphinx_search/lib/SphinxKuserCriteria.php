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
    
    public static $sphinxFields = array(
		kuserPeer::LOGIN_DATA_ID => "login_data_id",
        kuserPeer::IS_ADMIN => "is_admin",
        kuserPeer::SCREEN_NAME => "screen_name" ,
        kuserPeer::FULL_NAME => "full_name" ,
        kuserPeer::FIRST_NAME => "first_name" ,
        kuserPeer::LAST_NAME => "last_name" ,
        kuserPeer::EMAIL => "email" ,
        kuserPeer::ABOUT_ME => "about_me" ,
        kuserPeer::TAGS => "tags" ,
        kuserPeer::ENTRIES => "entries" ,
        kuserPeer::STORAGE_SIZE => "storage_size" ,
        kuserPeer::STATUS => "kuser_status" ,
        kuserPeer::CREATED_AT => "created_at" ,
        kuserPeer::UPDATED_AT => "updated_at" ,
        kuserPeer::PARTNER_ID => "partner_id" ,
        kuserPeer::DISPLAY_IN_SEARCH => "display_in_search" ,
        kuserPeer::PARTNER_DATA => "partner_data" ,
        kuserPeer::PUSER_ID => "puser_id" ,
        kuserPeer::INDEXED_PARTNER_DATA_INT => "indexed_partner_data_int" ,
        kuserPeer::INDEXED_PARTNER_DATA_STRING => "indexed_partner_data_string" ,
        'kuserPeer.PUSER_ID_OR_SCREEN_NAME' => '(puser_id,screen_name)',
        'kuserPeer.FIRST_NAME_OR_LAST_NAME' => '(full_name,last_name)',
        'kuser.PERMISSION_NAMES' => 'permission_names',
        'kuser.ROLE_IDS' => 'role_ids',
		
	);
	
	public static $sphinxOrderFields = array(
		kuserPeer::CREATED_AT => 'created_at',
		kuserPeer::UPDATED_AT => 'updated_at',
		kuserPeer::DISPLAY_IN_SEARCH => "display_in_search" ,
		kuserPeer::LOGIN_DATA_ID => "login_data_id",
        kuserPeer::IS_ADMIN => "is_admin",
		kuserPeer::INDEXED_PARTNER_DATA_INT => "indexed_partner_data_int",
	);
	
	public static $sphinxTypes = array ( 
        		"login_data_id" => IIndexable::FIELD_TYPE_INTEGER,
                "is_admin" => IIndexable::FIELD_TYPE_INTEGER,
                "screen_name" => IIndexable::FIELD_TYPE_STRING,
                "full_name" => IIndexable::FIELD_TYPE_STRING,
                "first_name" => IIndexable::FIELD_TYPE_STRING,
                "last_name" => IIndexable::FIELD_TYPE_STRING,
                "email" => IIndexable::FIELD_TYPE_STRING,
                "about_me" => IIndexable::FIELD_TYPE_STRING,
                "tags" => IIndexable::FIELD_TYPE_STRING,
                "entries" => IIndexable::FIELD_TYPE_INTEGER,
                "storage_size" => IIndexable::FIELD_TYPE_INTEGER,
                "kuser_status" => IIndexable::FIELD_TYPE_INTEGER,
                "created_at" => IIndexable::FIELD_TYPE_DATETIME,
                "updated_at" => IIndexable::FIELD_TYPE_DATETIME,
                "partner_id" => IIndexable::FIELD_TYPE_INTEGER,
                "display_in_search" => IIndexable::FIELD_TYPE_INTEGER,
                "partner_data" => IIndexable::FIELD_TYPE_STRING,
                "puser_id" => IIndexable::FIELD_TYPE_STRING,
                "indexed_partner_data_int" => IIndexable::FIELD_TYPE_INTEGER,
                "indexed_partner_data_string" => IIndexable::FIELD_TYPE_STRING,
				"permission_names" => IIndexable::FIELD_TYPE_STRING,
				"role_ids"	=> IIndexable::FIELD_TYPE_STRING,
        );
    
	/* (non-PHPdoc)
     * @see SphinxCriteria::doCountOnPeer()
     */
    protected function doCountOnPeer (Criteria $c)
    {
        return kuserPeer::doCount($c);
        
    }

	/* (non-PHPdoc)
     * @see SphinxCriteria::getDefaultCriteriaFilter()
     */
    protected function getDefaultCriteriaFilter ()
    {
        return kuserPeer::getCriteriaFilter();
        
    }

	/* (non-PHPdoc)
     * @see SphinxCriteria::getEnableStar()
     */
    protected function getEnableStar ()
    {
        return true;
        
    }

	/* (non-PHPdoc)
     * @see SphinxCriteria::getPropelIdField()
     */
    protected function getPropelIdField ()
    {
        return kuserPeer::ID;
        
    }

	/* (non-PHPdoc)
     * @see SphinxCriteria::getSphinxFieldName()
     */
    public function getSphinxFieldName ($fieldName)
    {
        if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = kuserPeer::TABLE_NAME.".$fieldName";
		}
			
		if(!isset(self::$sphinxFields[$fieldName]))
			return $fieldName;
			
		return self::$sphinxFields[$fieldName];
        
    }

	/* (non-PHPdoc)
     * @see SphinxCriteria::getSphinxFieldType()
     */
    public function getSphinxFieldType ($fieldName)
    {
        if(!isset(self::$sphinxTypes[$fieldName]))
			return null;
			
		return self::$sphinxTypes[$fieldName];
        
    }

	/* (non-PHPdoc)
     * @see SphinxCriteria::getSphinxIdField()
     */
    protected function getSphinxIdField ()
    {
        return 'id';
        
    }

	/* (non-PHPdoc)
     * @see SphinxCriteria::getSphinxIndexName()
     */
    protected function getSphinxIndexName ()
    {
        return kSphinxSearchManager::getSphinxIndexName(kuserPeer::getOMClass(false));
        
    }

	/* (non-PHPdoc)
     * @see SphinxCriteria::getSphinxOrderFields()
     */
    public function getSphinxOrderFields ()
    {
        return self::$sphinxOrderFields;
        
    }

	/* (non-PHPdoc)
     * @see SphinxCriteria::hasMatchableField()
     */
    public function hasMatchableField ($fieldName)
    {
        return in_array($fieldName, array(
			"screen_name" ,
            "full_name" ,
            "first_name" ,
            "last_name" ,
            "email" ,
            "about_me" ,
            "tags" ,
            "partner_data" ,
            "puser_id" ,
            "indexed_partner_data_string" ,
        	"permission_names",
        	"role_ids",
		));
        
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
			$filter->set('_mlikeand_permission_names', kuser::getIndexedFieldValue('kuserPeer::PERMISSION_NAMES', $filter->get('_mlikeand_permission_names'), kCurrentContext::getCurrentPartnerId()));
		}
		if ($filter->get('_mlikeor_permission_names'))
		{
			$filter->set('_mlikeor_permission_names', kuser::getIndexedFieldValue('kuserPeer::PERMISSION_NAMES', $filter->get('_mlikeor_permission_names'), kCurrentContext::getCurrentPartnerId()));
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
    
    

	/* (non-PHPdoc)
     * @see SphinxCriteria::hasSphinxFieldName()
     */
    public function hasSphinxFieldName ($fieldName)
    {
        if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = kuserPeer::TABLE_NAME.".$fieldName";
		}
			
		return isset(self::$sphinxFields[$fieldName]);
        
    }

    
}