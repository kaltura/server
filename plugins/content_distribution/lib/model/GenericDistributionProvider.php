<?php


/**
 * Skeleton subclass for representing a row from the 'generic_distribution_provider' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class GenericDistributionProvider extends BaseGenericDistributionProvider implements IDistributionProvider 
{
	/* (non-PHPdoc)
	 * @see IDistributionProvider::isDeleteEnabled()
	 */
	public function isDeleteEnabled()
	{
		// TODO Auto-generated method stub
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isUpdateEnabled()
	 */
	public function isUpdateEnabled()
	{
		// TODO Auto-generated method stub
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isReportsEnabled()
	 */
	public function isReportsEnabled()
	{
		// TODO Auto-generated method stub
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::isScheduleUpdateEnabled()
	 */
	public function isScheduleUpdateEnabled()
	{
		// TODO Auto-generated method stub
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::useDeleteInsteadOfUpdate()
	 */
	public function useDeleteInsteadOfUpdate()
	{
		// TODO Auto-generated method stub
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getJobIntervalBeforeSunrise()
	 */
	public function getJobIntervalBeforeSunrise()
	{
		// TODO Auto-generated method stub
	}

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getJobIntervalBeforeSunset()
	 */
	public function getJobIntervalBeforeSunset()
	{
		// TODO Auto-generated method stub
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getUpdateRequiredEntryFields()
	 */
	public function getUpdateRequiredEntryFields()
	{
		// TODO Auto-generated method stub
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getUpdateRequiredMetadataXPaths()
	 */
	public function getUpdateRequiredMetadataXPaths()
	{
		// TODO Auto-generated method stub
	}
	
	/* (non-PHPdoc)
	 * @see BaseGenericDistributionProvider::postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		$objectDeleted = false;
		if($this->isColumnModified(GenericDistributionProviderPeer::STATUS) && $this->getStatus() == GenericDistributionProviderStatus::DELETED)
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return $ret;
	}
	
} // GenericDistributionProvider
