<?php
/**
 * @package plugins.synacorDistribution
 * @subpackage lib
 */
class SynacorDistributionProvider implements IDistributionProvider
{
	/**
	 * @var SynacorDistributionProvider
	 */
	protected static $instance;
	
	protected function __construct()
	{
		
	}
	
	/**
	 * @return SynacorDistributionProvider
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new SynacorDistributionProvider();
			
		return self::$instance;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getType()
	 */
	public function getType()
	{
		return SynacorDistributionPlugin::getDistributionProviderTypeCoreValue(SynacorDistributionProviderType::SYNACOR);
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getName()
	 */
	public function getName()
	{
		return 'Synacor';
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
		return false;
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
		return 0;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getJobIntervalBeforeSunset()
	 */
	public function getJobIntervalBeforeSunset()
	{
		return 0;
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getUpdateRequiredEntryFields()
	 */
	public function getUpdateRequiredEntryFields($distributionProfileId = null)
	{
		if(kConf::hasParam('synacor_update_required_entry_fields'))
			return kConf::get('synacor_update_required_entry_fields');
			
		return array();
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getUpdateRequiredMetadataXPaths()
	 */
	public function getUpdateRequiredMetadataXPaths($distributionProfileId = null)
	{
		if(kConf::hasParam('synacor_update_required_metadata_xpaths'))
			return kConf::get('synacor_update_required_metadata_xpaths');
			
		return array();
	}
}