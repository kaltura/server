<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model.data
 */
class kDistributionDeleteJobData extends kDistributionJobData
{
	/**
	 * Flag signifying that the associated distribution item should not be moved to 'removed' status
	 * @var bool
	 */
	protected $keepDistributionItem;
	/**
	 * @return the $keepDistributionItem
	 */
	public function getKeepDistributionItem() {
		return $this->keepDistributionItem;
	}

	/**
	 * @param bool $keepDistributionItem
	 */
	public function setKeepDistributionItem($keepDistributionItem) {
		$this->keepDistributionItem = $keepDistributionItem;
	}

}