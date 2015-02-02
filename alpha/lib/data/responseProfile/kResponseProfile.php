<?php

/**
 * @package Core
 * @subpackage model.data
 */
class kResponseProfile implements IResponseProfile
{
	/**
	 * Friendly name
	 * 
	 * @var string
	 */
	private $name;
	
	/**
	 * @var KalturaResponseProfileType
	 */
	private $type;
	
	/**
	 * @var KalturaStringArray
	 */
	private $fields;
	
	/**
	 * @var KalturaResponseProfileConditionArray
	 */
	private $conditions;
	
	/**
	 * @var KalturaNestedResponseProfileBaseArray
	 */
	private $relatedProfiles;
	
	/* (non-PHPdoc)
	 * @see IResponseProfileBase::get()
	 */
	public function get()
	{
		return $this;
	}
	
	/* (non-PHPdoc)
	 * @see IResponseProfile::getName()
	 */
	public function getName()
	{
		return $this->name;
	}

	/* (non-PHPdoc)
	 * @see IResponseProfile::getType()
	 */
	public function getType()
	{
		return $this->type;
	}

	/* (non-PHPdoc)
	 * @see IResponseProfile::getFields()
	 */
	public function getFields()
	{
		return $this->fields;
	}

	/* (non-PHPdoc)
	 * @see IResponseProfile::getRelatedProfiles()
	 */
	public function getRelatedProfiles()
	{
		return $this->relatedProfiles;
	}

	/* (non-PHPdoc)
	 * @see IResponseProfile::setName()
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/* (non-PHPdoc)
	 * @see IResponseProfile::setType()
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/* (non-PHPdoc)
	 * @see IResponseProfile::setFields()
	 */
	public function setFields($fields)
	{
		$this->fields = $fields;
	}

	/* (non-PHPdoc)
	 * @see IResponseProfile::setRelatedProfiles()
	 */
	public function setRelatedProfiles($relatedProfiles)
	{
		$this->relatedProfiles = $relatedProfiles;
	}
	
	/**
	 * @return array<kCondition>
	 */
	public function getConditions()
	{
		return $this->conditions;
	}

	/**
	 * @param array<kCondition> $conditions
	 */
	public function setConditions($conditions)
	{
		$this->conditions = $conditions;
	}
}