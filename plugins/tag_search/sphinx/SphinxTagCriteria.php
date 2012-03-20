<?php
class SphinxTagCriteria extends SphinxCriteria
{
    public static $sphinxFields = array(
           TagPeer::TAG => 'tag',
           TagPeer::PARTNER_ID => 'partner_id',
           TagPeer::OBJECT_TYPE => 'object_type',
           TagPeer::INSTANCE_COUNT => 'instance_count',
           TagPeer::CREATED_AT => 'created_at',
       );
       
    public static $sphinxOrderFields = array(
		TagPeer::CREATED_AT => 'created_at',
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
	
	
}