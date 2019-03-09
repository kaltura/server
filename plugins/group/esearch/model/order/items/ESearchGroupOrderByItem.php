<?php
/**
 * @package plugins.group
 * @subpackage model.order
 */
class ESearchGroupOrderByItem extends ESearchOrderByItem
{
	/**
	 * @var ESearchGroupOrderByFieldName
	 */
	protected $sortField;

	/**
	 * @return ESearchGroupOrderByFieldName
	 */
	public function getSortField()
	{
		return $this->sortField;
	}

	/**
	 * @param ESearchGroupOrderByFieldName $sortField
	 */
	public function setSortField($sortField)
	{
		$this->sortField = $sortField;
	}
}
