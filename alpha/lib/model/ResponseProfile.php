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
class ResponseProfile extends BaseResponseProfile implements IResponseProfileHolder, IResponseProfile {
	
	const CUSTOM_DATA_FIELD_FIELDS = 'fields';
	const CUSTOM_DATA_FIELD_RELATED_PROFILES = 'relatedProfiles';
	
	/* (non-PHPdoc)
	 * @see IResponseProfileLoader::get()
	 */
	public function get()
	{
		return $this;
	}

	public function getFields()						{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_FIELDS);}
	public function getRelatedProfiles()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_RELATED_PROFILES);}

	public function setFields(array $v)				{$this->putInCustomData(self::CUSTOM_DATA_FIELD_FIELDS, $v);}
	public function setRelatedProfiles(array $v)	{$this->putInCustomData(self::CUSTOM_DATA_FIELD_RELATED_PROFILES, $v);}
	
} // ResponseProfile
