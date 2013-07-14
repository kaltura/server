<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model.data
 */
abstract class kAssetDistributionCondition
{
	/**
	 * @param asset $asset
	 */
	abstract public function fulfilled(asset $asset);
}