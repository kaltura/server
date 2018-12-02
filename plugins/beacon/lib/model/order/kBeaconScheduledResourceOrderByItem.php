<?php
/**
 * @package plugins.beacon
 * @subpackage model.order
 */
class kBeaconScheduledResourceOrderByItem extends ESearchOrderByItem
{
	/**
	 * @var BeaconScheduledResourceOrderByFieldName
	 */
	protected $sortField;

	/**
	 * @return BeaconScheduledResourceOrderByFieldName
	 */
	public function getSortField()
	{
		return $this->sortField;
	}

	/**
	 * @param BeaconScheduledResourceOrderByFieldName $sortField
	 */
	public function setSortField($sortField)
	{
		$this->sortField = $sortField;
	}

}
