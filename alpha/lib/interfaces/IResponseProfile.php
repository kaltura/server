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
}
