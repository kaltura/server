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
	 * @var ResponseProfileType
	 */
	private $type;
	
	/**
	 * @var array
	 */
	private $fields;
	
	/**
	 * @var baseObjectFilter
	 */
	private $filter;
	
	/**
	 * @var string
	 */
	private $filterApiClassName;
	
	/**
	 * @var kFilterPager
	 */
	private $pager;
	
	/**
	 * @var array<IResponseProfileBase>
	 */
	private $relatedProfiles;
	
	/**
	 * @var array<kResponseProfileMapping>
	 */
	private $mappings;
	
	/* (non-PHPdoc)
	 * @see IResponseProfile::getMappings()
	 */
	public function getMappings()
	{
		return $this->mappings;
	}

	/* (non-PHPdoc)
	 * @see IResponseProfile::setMappings()
	 */
	public function setMappings(array $mappings)
	{
		$this->mappings = $mappings;
	}

	/* (non-PHPdoc)
	 * @see IResponseProfileBase::get()
	 */
	public function get()
	{
		return $this;
	}
	
	/* (non-PHPdoc)
	 * @see IResponseProfile::getFilterApiClassName()
	 */
	public function getFilterApiClassName()
	{
		return $this->filterApiClassName;
	}

	/* (non-PHPdoc)
	 * @see IResponseProfile::setFilterApiClassName()
	 */
	public function setFilterApiClassName($filterApiClassName)
	{
		$this->filterApiClassName = $filterApiClassName;
	}

	/* (non-PHPdoc)
	 * @see IResponseProfile::getFilter()
	 */
	public function getFilter()
	{
		return $this->filter;
	}

	/* (non-PHPdoc)
	 * @see IResponseProfile::getPager()
	 */
	public function getPager()
	{
		return $this->pager;
	}

	/* (non-PHPdoc)
	 * @see IResponseProfile::setFilter()
	 */
	public function setFilter(baseObjectFilter $filter)
	{
		$this->filter = $filter;
	}

	/* (non-PHPdoc)
	 * @see IResponseProfile::setPager()
	 */
	public function setPager(kFilterPager $pager)
	{
		$this->pager = $pager;
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
	 * @see IResponseProfile::getFieldsArray()
	 */
	public function getFieldsArray()
	{
		return explode(',', $this->getFields());
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
	public function setRelatedProfiles(array $relatedProfiles)
	{
		$this->relatedProfiles = $relatedProfiles;
	}
}