<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage lib
 */
class YouTubeDistributionProvider extends ConfigurableDistributionProvider
{
	/**
	 * @var YouTubeDistributionProvider
	 */
	protected static $instance;
	
	protected function __construct()
	{
		
	}
	
	/**
	 * @return YouTubeDistributionProvider
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new YouTubeDistributionProvider();
			
		return self::$instance;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getType()
	 */
	public function getType()
	{
		return YouTubeDistributionPlugin::getDistributionProviderTypeCoreValue(YouTubeDistributionProviderType::YOUTUBE);
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return 'YouTube';
	}
	
	public function getFieldEnumClass()
	{
	    return 'YouTubeDistributionField';
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
		return true;
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