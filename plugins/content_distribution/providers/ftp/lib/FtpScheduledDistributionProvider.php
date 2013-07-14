<?php
/**
 * @package plugins.ftpDistribution
 * @subpackage lib
 */
class FtpScheduledDistributionProvider extends FtpDistributionProvider
{
	/**
	 * @var FtpScheduledDistributionProvider
	 */
	protected static $instance;
	
	protected function __construct()
	{
		
	}
	
	/**
	 * @return FtpDistributionProvider
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new FtpScheduledDistributionProvider();
			
		return self::$instance;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getType()
	 */
	public function getType()
	{
		return FtpDistributionPlugin::getDistributionProviderTypeCoreValue(FtpDistributionProviderType::FTP_SCHEDULED);
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getName()
	 */
	public function getName()
	{
		return 'FTP (Scheduled)';
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isScheduleUpdateEnabled()
	 */
	public function isScheduleUpdateEnabled()
	{
		return false;
	}
}