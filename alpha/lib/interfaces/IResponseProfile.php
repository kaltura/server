<?php

interface IResponseProfile extends IResponseProfileBase
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
	 * @return array<string>
	 */
	public function getFields();
	
	/**
	 * @return array<IResponseProfileBase>
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
	 * @param array<string> $v
	 */
	public function setFields(array $v);
	
	/**
	 * @param array<IResponseProfileBase> $v
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
}
