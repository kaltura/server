<?php
/**
 * @package plugins.quickPlayDistribution
 * @subpackage model.data
 */
class kQuickPlayDistributionJobProviderData extends kDistributionJobProviderData
{
	/**
	 * @var string
	 */
	public $xml;
	
	public function getXml()
	{
		return $this->xml;
	}

	public function setXml($xml)
	{
		$this->xml = $xml;
	}

	public function __construct(kDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
	}
}