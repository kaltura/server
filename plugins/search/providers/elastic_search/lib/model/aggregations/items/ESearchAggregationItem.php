<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
abstract class ESearchAggregationItem extends BaseObject
{
	const DEFAULT_SIZE = 5;
	const NESTED_BUCKET = 'NestedBucket';

	/**
	 * @var int
	 */
	protected $size;

	/**
	 * @var string
	 */
	protected $fieldName;

	/**
	 * @return int
	 */
	public function getSize()
	{
		if(!$this->size)
		{
			$this->size = self::DEFAULT_SIZE;
		}
		return $this->size;
	}

	/**
	 * @param int $size
	 */
	public function setSize($size)
	{
		$this->size = $size;
	}

	public function getFieldName()
	{
		return $this->fieldName;
	}

	public function setFieldName($fieldName)
	{
		$this->fieldName = $fieldName;
	}

	public function getAggregationCommand()
	{
		return array(ESearchAggregations::TERMS =>
				array(ESearchAggregations::FIELD => $this->fieldName, ESearchAggregations::SIZE => $this->getSize()));
	}

	public abstract function getAggregationKey();
}