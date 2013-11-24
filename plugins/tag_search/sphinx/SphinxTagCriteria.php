<?php
class SphinxTagCriteria extends SphinxCriteria
{
    public static $sphinxFields = array(
           TagPeer::TAG => 'tag',
           TagPeer::PARTNER_ID => 'partner_id',
           TagPeer::OBJECT_TYPE => 'object_type',
           TagPeer::PRIVACY_CONTEXT => 'privacy_context',
           TagPeer::INSTANCE_COUNT => 'instance_count',
           TagPeer::CREATED_AT => 'created_at',
           TagPeer::UPDATED_AT => 'updated_at',
       );
       
    public static $sphinxOrderFields = array(
		TagPeer::CREATED_AT => 'created_at',
		TagPeer::UPDATED_AT => 'updated_at',
		TagPeer::INSTANCE_COUNT => 'instance_count',
	);
	
	public function starEnabled()
	{
	    return true;
	}
	
    public function hasMatchableField($fieldName)
	{
		return in_array($fieldName, array(
			"tag",
			"partner_id",
			"object_type",
			"privacy_context"
		));
	}
	
	public function hasSphinxFieldName($fieldName)
	{
	    return isset(self::$sphinxFields[$fieldName]);
	}
	
    public function getSphinxFieldName($fieldName)
	{
		if(!isset(self::$sphinxFields[$fieldName]))
			return $fieldName;
			
		return self::$sphinxFields[$fieldName];
	}
	
	public function getSphinxOrderFields()
	{
	    return self::$sphinxOrderFields;
	}
	
	public function getSphinxFieldType($fieldName)
	{
	    $sphinxTypes = TagSearchPlugin::getSphinxSchemaFields();
		if(!isset($sphinxTypes[$fieldName]))
			return null;
			
		return kSphinxSearchManager::getSphinxDataType($sphinxTypes[$fieldName]);
	}
	
	protected function getSphinxIdField()
	{
	    return 'int_id';
	}
	
	
	protected function getDefaultCriteriaFilter()
	{
	    return TagPeer::getCriteriaFilter();
	}
	
	
	protected function getSphinxIndexName()
	{
	    return kSphinxSearchManager::getSphinxIndexName(TagSearchPlugin::INDEX_NAME);
	}
	
	protected function doCountOnPeer(Criteria $c)
	{
	    return TagPeer::doCount($c);
	}
	
	protected function getPropelIdField()
	{
	    return TagPeer::ID;
	}
	
	protected function getEnableStar ()
	{
	    return true;
	}

	protected function applyFilterFields(baseObjectFilter $filter)
	{
		if ($filter->get('_eq_object_type'))
		{
			$filter->set('_eq_object_type', Tag::getIndexedFieldValue('TagPeer::OBJECT_TYPE', $filter->get('_eq_object_type'), kCurrentContext::getCurrentPartnerId()));
		}
		
		parent::applyFilterFields($filter);
	}
	
	public function getSkipFields()
	{
		return array(TagPeer::TAG);
	}
	
	public function hasPeerFieldName($fieldName)
	{
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = "tag.$fieldName";
		}
	
		$entryFields = TagPeer::getFieldNames(BasePeer::TYPE_COLNAME);
	
		return in_array($fieldName, $entryFields);
	}

	public function translateSphinxCriterion(SphinxCriterion $crit)
	{
		$field = $crit->getTable() . '.' . $crit->getColumn();
		$value = $crit->getValue();
		
		$fieldName = null;
		if ($field == TagPeer::OBJECT_TYPE)
			$fieldName = "TagPeer::OBJECT_TYPE";
		if ($field == TagPeer::PRIVACY_CONTEXT)
			$fieldName = "TagPeer::PRIVACY_CONTEXT";

		if ($fieldName)
		{
			$partnerIdCrit = $this->getCriterion(TagPeer::PARTNER_ID);
			if ($partnerIdCrit && $partnerIdCrit->getComparison() == Criteria::EQUAL)
				$partnerId = $partnerIdCrit->getValue();
			else
				$partnerId = kCurrentContext::getCurrentPartnerId();
			
			$value = Tag::getIndexedFieldValue($fieldName, $value, $partnerId);
		}
		
		if ($field == TagPeer::TAG && in_array($crit->getComparison(), array(Criteria::EQUAL, Criteria::IN)))
		{
			$value = str_replace(kTagFlowManager::$specialCharacters, kTagFlowManager::$specialCharactersReplacement, $value);
		}

		return array($field, $crit->getComparison(), $value);
	}
}