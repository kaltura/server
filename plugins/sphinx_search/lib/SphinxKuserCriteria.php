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
        kuserPeer::SHA1_PASSWORD => "sha1_password" ,
        kuserPeer::SALT => "salt" ,
        kuserPeer::DATE_OF_BIRTH => "date_of_birth" ,
        kuserPeer::COUNTRY => "country" ,
        kuserPeer::STATE => "state" ,
        kuserPeer::CITY => "city" ,
        kuserPeer::ZIP => "zip" ,
        kuserPeer::URL_LIST => "url_list" ,
        kuserPeer::PICTURE => "picture" ,
        kuserPeer::ICON => "icon" ,
        kuserPeer::ABOUT_ME => "about_me" ,
        kuserPeer::TAGS => "tags" ,
        kuserPeer::TAGLINE => "tagline" ,
        kuserPeer::NETWORK_HIGHSCHOOL => "network_highschool" ,
        kuserPeer::NETWORK_COLLEGE => "network_college" ,
        kuserPeer::NETWORK_OTHER => "network_other" ,
        kuserPeer::MOBILE_NUM => "mobile_num" ,
        kuserPeer::MATURE_CONTENT => "mature_content" ,
        kuserPeer::GENDER => "gender" ,
        kuserPeer::REGISTRATION_IP => "registration_ip" ,
        kuserPeer::REGISTRATION_COOKIE => "registration_cookie" ,
        kuserPeer::IM_LIST => "im_list" ,
        kuserPeer::VIEWS => "views" ,
        kuserPeer::FANS => "fans" ,
        kuserPeer::ENTRIES => "entries" ,
        kuserPeer::STORAGE_SIZE => "storage_size" ,
        kuserPeer::PRODUCED_KSHOWS => "produced_kshows" ,
        kuserPeer::STATUS => "kuser_status" ,
        kuserPeer::CREATED_AT => "created_at" ,
        kuserPeer::UPDATED_AT => "updated_at" ,
        kuserPeer::PARTNER_ID => "partner_id" ,
        kuserPeer::DISPLAY_IN_SEARCH => "display_in_search" ,
        kuserPeer::PARTNER_DATA => "partner_data" ,
        kuserPeer::PUSER_ID => "puser_id" ,
        kuserPeer::ADMIN_TAGS => "admin_tags" ,
        kuserPeer::INDEXED_PARTNER_DATA_INT => "indexed_partner_data_int" ,
        kuserPeer::INDEXED_PARTNER_DATA_STRING => "indexed_partner_data_string" ,
        kuserPeer::CUSTOM_DATA => "custom_data",
        'kuserPeer.PUSER_ID_OR_SCREEN_NAME' => '(puser_id,screen_name)',
        'kuserPeer.FIRST_NAME_OR_LAST_NAME' => '(full_name,last_name)',
		
	);
	
	public static $sphinxOrderFields = array(
		kuserPeer::CREATED_AT => 'created_at',
		kuserPeer::UPDATED_AT => 'updated_at',
		kuserPeer::DISPLAY_IN_SEARCH => "display_in_search" ,
		kuserPeer::MATURE_CONTENT => "mature_content" ,
		kuserPeer::GENDER => "gender",
		kuserPeer::DATE_OF_BIRTH => "date_of_birth",
		kuserPeer::LOGIN_DATA_ID => "login_data_id",
        kuserPeer::IS_ADMIN => "is_admin",
        kuserPeer::VIEWS => "views",
		kuserPeer::FANS => "fans",
        kuserPeer::ENTRIES => "entries" ,
        kuserPeer::STORAGE_SIZE => "storage_size" ,
        kuserPeer::PRODUCED_KSHOWS => "produced_kshows" ,
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
                "sha1_password" => IIndexable::FIELD_TYPE_STRING,
                "salt" => IIndexable::FIELD_TYPE_STRING,
                "date_of_birth" => IIndexable::FIELD_TYPE_DATETIME,
                "country" => IIndexable::FIELD_TYPE_STRING,
                "state" => IIndexable::FIELD_TYPE_STRING,
                "city" => IIndexable::FIELD_TYPE_STRING,
                "zip" => IIndexable::FIELD_TYPE_STRING,
                "url_list" => IIndexable::FIELD_TYPE_STRING,
                "picture" => IIndexable::FIELD_TYPE_STRING,
                "icon" => IIndexable::FIELD_TYPE_STRING,
                "about_me" => IIndexable::FIELD_TYPE_STRING,
                "tags" => IIndexable::FIELD_TYPE_STRING,
                "tagline" => IIndexable::FIELD_TYPE_STRING,
                "network_highschool" => IIndexable::FIELD_TYPE_STRING,
                "network_college" => IIndexable::FIELD_TYPE_STRING,
                "network_other" => IIndexable::FIELD_TYPE_STRING,
                "mobile_num" => IIndexable::FIELD_TYPE_STRING,
                "mature_content" => IIndexable::FIELD_TYPE_INTEGER,
                "gender" => IIndexable::FIELD_TYPE_INTEGER,
                "registration_ip" => IIndexable::FIELD_TYPE_STRING,
                "registration_cookie" => IIndexable::FIELD_TYPE_STRING,
                "im_list" => IIndexable::FIELD_TYPE_STRING,
                "views" => IIndexable::FIELD_TYPE_INTEGER,
                "fans" => IIndexable::FIELD_TYPE_INTEGER,
                "entries" => IIndexable::FIELD_TYPE_INTEGER,
                "storage_size" => IIndexable::FIELD_TYPE_INTEGER,
                "produced_kshows" => IIndexable::FIELD_TYPE_INTEGER,
                "kuser_status" => IIndexable::FIELD_TYPE_INTEGER,
                "created_at" => IIndexable::FIELD_TYPE_DATETIME,
                "updated_at" => IIndexable::FIELD_TYPE_DATETIME,
                "partner_id" => IIndexable::FIELD_TYPE_INTEGER,
                "display_in_search" => IIndexable::FIELD_TYPE_INTEGER,
                "partner_data" => IIndexable::FIELD_TYPE_STRING,
                "puser_id" => IIndexable::FIELD_TYPE_STRING,
                "admin_tags" => IIndexable::FIELD_TYPE_STRING,
                "indexed_partner_data_int" => IIndexable::FIELD_TYPE_INTEGER,
                "indexed_partner_data_string" => IIndexable::FIELD_TYPE_STRING,
                "custom_data" => IIndexable::FIELD_TYPE_STRING,
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
            "sha1_password" ,
            "salt" ,
            "country" ,
            "state" ,
            "city" ,
            "zip" ,
            "url_list" ,
            "picture" ,
            "icon" ,
            "about_me" ,
            "tags" ,
            "tagline" ,
            "network_highschool" ,
            "network_college" ,
            "network_other" ,
            "mobile_num" ,
            "mature_content" ,
            "registration_ip" ,
            "registration_cookie" ,
            "im_list" ,
            "partner_data" ,
            "puser_id" ,
            "admin_tags" ,
            "indexed_partner_data_string" ,
            "custom_data",
		));
        
    }
    
	/* (non-PHPdoc)
	 * @see SphinxCriteria::applyFilterFields()
	 */
	protected function applyFilterFields(baseObjectFilter $filter)
	{		
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