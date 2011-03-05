<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage lib
 */
class YouTubeDistributionProvider implements IDistributionProvider
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
	 * @see IDistributionProvider::useDeleteInsteadOfUpdate()
	 */
	public function useDeleteInsteadOfUpdate()
	{
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getJobIntervalBeforeSunrise()
	 */
	public function getJobIntervalBeforeSunrise()
	{
//		maybe should be taken from local config and not kConf
		if(kConf::hasParam('msn_distribution_interval_before_sunrise'))
			return kConf::get('msn_distribution_interval_before_sunrise');
			
		return 0;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getJobIntervalBeforeSunset()
	 */
	public function getJobIntervalBeforeSunset()
	{
//		maybe should be taken from local config and not kConf
		if(kConf::hasParam('msn_distribution_interval_before_sunset'))
			return kConf::get('msn_distribution_interval_before_sunset');
			
		return 0;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getUpdateRequiredEntryFields()
	 */
	public function getUpdateRequiredEntryFields($distributionProfileId = null)
	{
		return array(entryPeer::NAME, entryPeer::DESCRIPTION, entryPeer::TAGS);
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getUpdateRequiredMetadataXPaths()
	 */
	public function getUpdateRequiredMetadataXPaths($distributionProfileId = null)
	{
//		e.g.
//		maybe should be taken from local config or kConf
//		return array(
//			"/*[local-name()='metadata']/*[local-name()='ShortDescription']",
//			"/*[local-name()='metadata']/*[local-name()='LongDescription']",
//		);
		
		return array();
	}
}