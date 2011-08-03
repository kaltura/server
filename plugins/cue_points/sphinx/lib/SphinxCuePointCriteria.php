<?php

class SphinxCuePointCriteria extends SphinxCriteria
{
	public static $sphinxFields = array(
		CuePointPeer::ID => 'int_cue_point_id',
		CuePointPeer::PARENT_ID => 'parent_id',
		CuePointPeer::ENTRY_ID => 'entry_id',
		CuePointPeer::NAME => 'name',
		CuePointPeer::SYSTEM_NAME => 'system_name',
		CuePointPeer::TEXT => 'text',
		CuePointPeer::TAGS => 'tags',
		CuePointPeer::ROOTS => 'roots',
		CuePointPeer::INT_ID => 'cue_point_int_id',
		CuePointPeer::PARTNER_ID => 'partner_id',
		CuePointPeer::START_TIME => 'start_time',
		CuePointPeer::END_TIME => 'end_time',
		CuePointPeer::DURATION => 'duration',
		CuePointPeer::STATUS => 'cue_point_status',
		CuePointPeer::TYPE => 'cue_point_type',
		CuePointPeer::SUB_TYPE => 'sub_type',
		CuePointPeer::KUSER_ID => 'kuser_id',
		CuePointPeer::PARTNER_SORT_VALUE => 'partner_sort_value',
		CuePointPeer::FORCE_STOP => 'force_stop',
		CuePointPeer::CREATED_AT => 'created_at',
		CuePointPeer::UPDATED_AT => 'updated_at',
		CuePointPeer::STR_ENTRY_ID => 'str_entry_id',
		CuePointPeer::STR_CUE_POINT_ID => 'str_cue_point_id',
	);
	
	public static $sphinxOrderFields = array(
		CuePointPeer::START_TIME => 'start_time',
		CuePointPeer::END_TIME => 'end_time',
		CuePointPeer::DURATION => 'duration',
		CuePointPeer::PARTNER_SORT_VALUE => 'partner_sort_value',
		CuePointPeer::CREATED_AT => 'created_at',
		CuePointPeer::UPDATED_AT => 'updated_at',
	);
	
	/**
	 * @return criteriaFilter
	 */
	protected function getDefaultCriteriaFilter()
	{
		return CuePointPeer::getCriteriaFilter();
	}
	
	public function getSphinxOrderFields()
	{
		return self::$sphinxOrderFields;
	}
	
	/**
	 * @return string
	 */
	protected function getSphinxIndexName()
	{
		return kSphinxSearchManager::getSphinxIndexName(CuePointPeer::TABLE_NAME);;
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::getSphinxIdField()
	 */
	protected function getSphinxIdField()
	{
		return 'str_cue_point_id';
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::getPropelIdField()
	 */
	protected function getPropelIdField()
	{
		return CuePointPeer::ID;
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::doCountOnPeer()
	 */
	protected function doCountOnPeer(Criteria $c)
	{
		return CuePointPeer::doCount($c);
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
	
	public function getSphinxFieldType($fieldName)
	{
		$sphinxTypes = CuePoint::getIndexFieldTypes();
		if(!isset($sphinxTypes[$fieldName]))
			return null;
			
		return $sphinxTypes[$fieldName];
	}
	
	public function hasMatchableField($fieldName)
	{
		return in_array($fieldName, array(
			'parent_id',
			'entry_id', 
			'name', 
			'system_name',
			'text', 
			'tags',  
			'roots', 
		));
	}

	public function getIdField()
	{
		return CuePointPeer::ID;
	}
}