<?php


/**
 * Skeleton subclass for representing a row from the 'response_profile' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class ResponseProfile extends BaseResponseProfile implements IResponseProfile {
	
	const CUSTOM_DATA_FIELD_FIELDS = 'fields';
	const CUSTOM_DATA_FIELD_RELATED_PROFILES = 'relatedProfiles';
	const CUSTOM_DATA_FIELD_MAPPINGS = 'mappings';
	const CUSTOM_DATA_FIELD_FILTER_API_CLASS_NAME = 'filterApiClassName';
	const CUSTOM_DATA_FIELD_FILTER = 'filter';
	const CUSTOM_DATA_FIELD_PAGER = 'pager';
	
	/* (non-PHPdoc)
	 * @see IResponseProfile::getFieldsArray()
	 */
	public function getFieldsArray()
	{
		return explode(',', $this->getFields());
	}
	
	public function getFields()						{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_FIELDS);}
	public function getRelatedProfiles()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_RELATED_PROFILES);}
	public function getMappings()					{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_MAPPINGS);}
	public function getFilterApiClassName()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_FILTER_API_CLASS_NAME);}
	public function getFilter()						{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_FILTER);}
	public function getPager()						{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_PAGER);}
	
	public function setFields($v)						{$this->putInCustomData(self::CUSTOM_DATA_FIELD_FIELDS, $v);}
	public function setRelatedProfiles(array $v)		{$this->putInCustomData(self::CUSTOM_DATA_FIELD_RELATED_PROFILES, $v);}
	public function setMappings(array $v)				{$this->putInCustomData(self::CUSTOM_DATA_FIELD_MAPPINGS, $v);}
	public function setFilterApiClassName($v)			{$this->putInCustomData(self::CUSTOM_DATA_FIELD_FILTER_API_CLASS_NAME, $v);}
	public function setFilter(baseObjectFilter $v)		{$this->putInCustomData(self::CUSTOM_DATA_FIELD_FILTER, $v);}
	public function setPager(kFilterPager $v)			{$this->putInCustomData(self::CUSTOM_DATA_FIELD_PAGER, $v);}
	
} // ResponseProfile
