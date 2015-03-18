<?php

interface IResponseProfile
{
	/**
	 * @return string
	 */
	public function getName();
	
	/**
	 * @return ResponseProfileType
	 */
	public function getType();
	
	/**
	 * @return string
	 */
	public function getFields();
	
	/**
	 * @return array<string>
	 */
	public function getFieldsArray();
	
	/**
	 * @return array<IResponseProfile>
	 */
	public function getRelatedProfiles();
	
	/**
	 * @return baseObjectFilter
	 */
	public function getFilter();
	
	/**
	 * @return string
	 */
	public function getFilterApiClassName();

	/**
	 * @return kFilterPager
	 */
	public function getPager();
	
	/**
	 * @param string $v
	 */
	public function setName($v);
	
	/**
	 * @param ResponseProfileType $v
	 */
	public function setType($v);
	
	/**
	 * @param string $v
	 */
	public function setFields($v);
	
	/**
	 * @param array<IResponseProfile> $v
	 */
	public function setRelatedProfiles(array $v);

	/**
	 * @param baseObjectFilter $filter
	 */
	public function setFilter(baseObjectFilter $filter);

	/**
	 * @param string $filter
	 */
	public function setFilterApiClassName($filterApiClassName);

	/**
	 * @param kFilterPager $pager
	 */
	public function setPager(kFilterPager $pager);
	
	/**
	 * @return array
	 */
	public function getMappings();

	/**
	 * @param array<kResponseProfileMapping> $mappings
	 */
	public function setMappings(array $mappings);
}
