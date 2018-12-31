<?php
/**
 * @package plugins.videoIndexerDistribution
 * @subpackage lib
 */
class kVideoIndexerDistributionJobProviderData extends kConfigurableDistributionJobProviderData
{
	/**
	 * @var string
	 */
	public $filePath;

	public function setFilePath($filePath)	{ $this->filePath = $filePath; }
	public function getFilePath()		{ return $this->filePath; }


	public function __construct(kDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
	}

}