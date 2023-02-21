<?php
/**
 * @package plugins.cortexApiDistribution
 * @subpackage lib
 */
class CortexApiDistributionProvider extends ConfigurableDistributionProvider
{
	/**
	 * @var CortexApiDistributionProvider
	 */
	protected static $instance;
	
	protected function __construct()
	{
	}
	
	/**
	 * @return CortexApiDistributionProvider
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new CortexApiDistributionProvider();
			
		return self::$instance;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getType()
	 */
	public function getType()
	{
		return CortexApiDistributionPlugin::getDistributionProviderTypeCoreValue(CortexApiDistributionProviderType::CORTEX_API);
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getName()
	 */
	public function getName()
	{
		return 'CortexApi';
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
