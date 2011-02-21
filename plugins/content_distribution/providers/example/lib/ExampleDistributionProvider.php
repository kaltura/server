<?php
/**
 * @package plugins.exampleDistribution
 * @subpackage lib
 */
class ExampleDistributionProvider implements IDistributionProvider
{
	/**
	 * @var ExampleDistributionProvider
	 */
	protected static $instance;
	
	protected function __construct()
	{
		
	}
	
	/**
	 * @return ExampleDistributionProvider
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new ExampleDistributionProvider();
			
		return self::$instance;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getType()
	 */
	public function getType()
	{
		return ExampleDistributionPlugin::getDistributionProviderTypeCoreValue(ExampleDistributionProviderType::EXAMPLE);
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getName()
	 */
	public function getName()
	{
		return 'Example';
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
	 * @see IDistributionProvider::isReportsEnabled()
	 */
	public function isReportsEnabled()
	{
		return true;
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
		// irrelevant because update is enabled
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getJobIntervalBeforeSunrise()
	 */
	public function getJobIntervalBeforeSunrise()
	{
		// irrelevant because sending schedule is supported
		return 0;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getJobIntervalBeforeSunset()
	 */
	public function getJobIntervalBeforeSunset()
	{
		// irrelevant because sending schedule is supported
		return 0;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getUpdateRequiredEntryFields()
	 */
	public function getUpdateRequiredEntryFields($distributionProfileId = null)
	{
		return array(
			// entry columns
			entryPeer::NAME, 
			entryPeer::DESCRIPTION, 
			entryPeer::TAGS, 
			entryPeer::CATEGORIES,
			
			// customized properties
			'width', 
			'height', 
		);
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getUpdateRequiredMetadataXPaths()
	 */
	public function getUpdateRequiredMetadataXPaths($distributionProfileId = null)
	{
		return array(
			"/*[local-name()='metadata']/*[local-name()='ShortDescription']",
			"/*[local-name()='metadata']/*[local-name()='LongDescription']",
			"/*[local-name()='metadata']/*[local-name()='ExampleTitle']",
		);
	}
}