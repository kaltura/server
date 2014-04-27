<?php
/**
 * @package plugins.youtubeApiDistribution
 * @subpackage lib
 */
class YoutubeApiDistributionProvider extends ConfigurableDistributionProvider
{
	/**
	 * @var YoutubeApiDistributionProvider
	 */
	protected static $instance;
	
	protected function __construct()
	{
	}
	
	/**
	 * @return YoutubeApiDistributionProvider
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new YoutubeApiDistributionProvider();
			
		return self::$instance;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getType()
	 */
	public function getType()
	{
		return YoutubeApiDistributionPlugin::getDistributionProviderTypeCoreValue(YoutubeApiDistributionProviderType::YOUTUBE_API);
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getName()
	 */
	public function getName()
	{
		return 'YoutubeApi';
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
		return false; // TODO - check if reports supported
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isScheduleUpdateEnabled()
	 */
	public function isScheduleUpdateEnabled()
	{
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::useDeleteInsteadOfUpdate()
	 */
	public function useDeleteInsteadOfUpdate()
	{
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::isAvailabilityUpdateEnabled()
	 */
	public function isAvailabilityUpdateEnabled()
	{
		return true;
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
	 * @see IDistributionProvider::getJobIntervalBeforeSunrise()
	 */
	public function getJobIntervalBeforeSunrise()
	{
		return 0;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getJobIntervalBeforeSunset()
	 */
	public function getJobIntervalBeforeSunset()
	{
		return 0;
	}
		
}