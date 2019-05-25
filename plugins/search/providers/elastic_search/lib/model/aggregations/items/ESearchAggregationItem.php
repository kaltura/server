<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
abstract class ESearchAggregationItem extends BaseObject
{
	const SIZE = 'size';

	const DEFAULT_SIZE = 5;

	/**
	 * @var int
	 */
	protected $size;

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

	public abstract function getAggregationCommand();

	public abstract function getAggregationKey();

	public function getCommandKey()
	{
		return $this->getAggregationKey().':'.$this->getFieldName();
	}


}