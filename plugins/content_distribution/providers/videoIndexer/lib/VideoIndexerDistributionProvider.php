<?php
/**
 * @package plugins.VideoIndexerDistribution
 * @subpackage lib
 */
class VideoIndexerDistributionProvider extends ConfigurableDistributionProvider
{
	/**
	 * @var VideoIndexerDistributionProvider
	 */
	protected static $instance;

	protected function __construct()
	{

	}

	/**
	 * @return VideoIndexerDistributionProvider
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new VideoIndexerDistributionProvider();

		return self::$instance;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getType()
	 */
	public function getType()
	{
		return VideoIndexerDistributionPlugin::getDistributionProviderTypeCoreValue(VideoIndexerDistributionProviderType::VIDEOINDEXER);
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'VideoIndexer';
	}

	public function getFieldEnumClass()
	{
		return 'VideoIndexerDistributionField';
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isDeleteEnabled()
	 */
	public function isDeleteEnabled()
	{
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isUpdateEnabled()
	 */
	public function isUpdateEnabled()
	{
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isMediaUpdateEnabled()
	 */
	public function isMediaUpdateEnabled()
	{
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isReportsEnabled()
	 */
	public function isReportsEnabled()
	{
		// not in scope
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isScheduleUpdateEnabled()
	 */
	public function isScheduleUpdateEnabled()
	{
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isAvailabilityUpdateEnabled()
	 */
	public function isAvailabilityUpdateEnabled()
	{
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isLocalFileRequired()
	 */
	public function isLocalFileRequired($jobType)
	{
		if($jobType == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT))
			return true;

		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::useDeleteInsteadOfUpdate()
	 */
	public function useDeleteInsteadOfUpdate()
	{
		return false;
	}

	/**
	 * returns how many seconds before sunrise the job could be created.
	 * @return int
	 */
	public function getJobIntervalBeforeSunrise()
	{
		return 0; //irrelevant
	}

	/**
	 * returns how many seconds before sunrise the job could be created.
	 * @return int
	 */
	public function getJobIntervalBeforeSunset()
	{
		return 0; //irrelevant
	}
}