<?php
/**
 * @package plugins.tvinciDistribution
 * @subpackage api.objects
 */
class KalturaTvinciDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * @var string
	 */
	public $xml;

	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);

		if( (!$distributionJobData) ||
			(!($distributionJobData->distributionProfile instanceof KalturaTvinciDistributionProfile)) ||
			(! $distributionJobData->entryDistribution) )
			return;

		$entry = null;
		if ( $distributionJobData->entryDistribution->entryId )
		{
			$entry = entryPeer::retrieveByPK($distributionJobData->entryDistribution->entryId);
		}

		if ( ! $entry ) {
			KalturaLog::err("Can't find entry with id: {$distributionJobData->entryDistribution->entryId}");
			return;
		}

		$feedHelper = new TvinciDistributionFeedHelper($distributionJobData->distributionProfile, $entry);

		if ($distributionJobData instanceof KalturaDistributionSubmitJobData)
		{
			$this->xml = $feedHelper->buildSubmitFeed();
		}
		elseif ($distributionJobData instanceof KalturaDistributionUpdateJobData)
		{
			$this->xml = $feedHelper->buildUpdateFeed();
		}
		elseif ($distributionJobData instanceof KalturaDistributionDeleteJobData)
		{
			$this->xml = $feedHelper->buildDeleteFeed();
		}
	}

	private static $map_between_objects = array
	(
		'xml',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
