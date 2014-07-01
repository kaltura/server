<?php
/**
 * @package plugins.tvinciDistribution
 * @subpackage model.data
 */
class kTvinciDistributionJobProviderData extends kConfigurableDistributionJobProviderData
{
	/**
	 * @var string
	 */
	public $xml;

	public function setXml($xml)	{ $this->xml = $xml; }
	public function getXml()		{ return $this->xml; }
	
	public function __construct(kDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
	}
}