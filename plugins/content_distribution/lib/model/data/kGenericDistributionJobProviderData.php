<?php
class kGenericDistributionJobProviderData extends kDistributionJobProviderData
{
	/**
	 * @var string
	 */
	private $xml;
	
	/**
	 * @var string
	 */
	private $resultParse;

	public function __construct(kDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
	}
	
	/**
	 * @return the $xml
	 */
	public function getXml()
	{
		return $this->xml;
	}

	/**
	 * @return the $resultParse
	 */
	public function getResultParse()
	{
		return $this->resultParse;
	}

	/**
	 * @param string $xml
	 */
	public function setXml($xml)
	{
		$this->xml = $xml;
	}

	/**
	 * @param string $resultParse
	 */
	public function setResultParse($resultParse)
	{
		$this->resultParse = $resultParse;
	}
}