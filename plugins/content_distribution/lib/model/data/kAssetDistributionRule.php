<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model.data
 */
class kAssetDistributionRule
{
	/**
	 * @var string
	 */
	private $validationError;

	/**
	 * @var array<kAssetDistributionCondition>
	 */
	private $assetDistributionConditions;
	
	/**
	 * @param asset $asset
	 * @return boolean
	 */
	public function fulfilled(asset $asset)
	{	
		foreach ($this->assetDistributionConditions as $distributionCondition)
		{
			/* @var $distributionCondition kAssetDistributionCondition */
			if (!$distributionCondition->fulfilled($asset))
			{
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * @param array<kAssetDistributionCondition> $conditions
	 */
	public function setAssetDistributionConditions(array $conditions)
	{
		$this->assetDistributionConditions = $conditions;
	}
	
	/**
	 * @return array<kAssetDistributionCondition>
	 */
	public function getAssetDistributionConditions()
	{
		return $this->assetDistributionConditions;
	}

	/**
	 * @param string $validationError
	 */
	public function setValidationError($validationError)
	{
		$this->validationError = $validationError;
	}

	/**
	 * @return string
	 */
	public function getValidationError()
	{
		return $this->validationError;
	}
}
		