<?php
/**
 * @package plugins.verizonVcastDistribution
 * @subpackage model.data
 */
class kVerizonVcastDistributionJobProviderData extends kDistributionJobProviderData
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