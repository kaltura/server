<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model
 */

class ESearchHighlight extends BaseObject
{
	/**
	 * @var string
	 */
	protected $fieldName;

	/**
	 * @var array
	 */
	protected $hits = array();

	/**
	 * @return string
	 */
	public function getFieldName()
	{
		return $this->fieldName;
	}

	/**
	 * @param string $fieldName
	 */
	public function setFieldName($fieldName)
	{
		$this->fieldName = $fieldName;
	}

	/**
	 * @return array
	 */
	public function getHits()
	{
		return $this->hits;
	}

	/**
	 * @param array $hits
	 */
	public function setHits($hits)
	{
		$this->hits = $hits;
	}
}