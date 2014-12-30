<?php
/**
 * @package plugins.unicornDistribution
 * @subpackage lib
 */
class UnicornDistributionProvider extends ConfigurableDistributionProvider
{
	/**
	 * @var UnicornDistributionProvider
	 */
	protected static $instance;
	
	/**
	 * @return UnicornDistributionProvider
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new UnicornDistributionProvider();
		
		return self::$instance;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getType()
	 */
	public function getType()
	{
		return UnicornDistributionPlugin::getDistributionProviderTypeCoreValue(UnicornDistributionProviderType::UNICORN);
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getName()
	 */
	public function getName()
	{
		return 'Unicorn';
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::isDeleteEnabled()
	 */
	public function isDeleteEnabled()
	{
		return false;
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
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::isScheduleUpdateEnabled()
	 */
	public function isScheduleUpdateEnabled()
	{
		return false;
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