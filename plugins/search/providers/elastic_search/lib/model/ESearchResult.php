<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model
 */
class ESearchResult extends BaseObject
{

	/**
	 * @var BaseObject
	 */
	protected $object;

	/**
	 * @var array
	 */
	protected $itemsData;

	/**
	 * @return BaseObject
	 */
	public function getObject()
	{
		return $this->object;
	}

	/**
	 * @param BaseObject $object
	 */
	public function setObject($object)
	{
		$this->object = $object;
	}

	/**
	 * @return array
	 */
	public function getItemsData()
	{
		return $this->itemsData;
	}

	/**
	 * @param array $itemsData
	 */
	public function setItemsData($itemsData)
	{
		$this->itemsData = $itemsData;
	}


}