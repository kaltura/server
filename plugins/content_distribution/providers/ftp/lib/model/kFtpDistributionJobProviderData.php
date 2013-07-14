<?php
/**
 * @package plugins.ftpDistribution
 * @subpackage model.data
 */
class kFtpDistributionJobProviderData extends kDistributionJobProviderData
{
	public function __construct(kDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
	}
}