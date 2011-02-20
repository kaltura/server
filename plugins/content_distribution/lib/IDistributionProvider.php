<?php
/**
 * @package plugins.contentDistribution
 * @subpackage lib
 * 
 * @todo add isMediaUpdateEnabled
 * @todo add isThumbnailUpdateEnabled
 */
interface IDistributionProvider
{
	/**
	 * value from enum DistributionProviderType
	 * @return int
	 */
	public function getType();
	
	/**
	 * @return string
	 */
	public function getName();
	
	/**
	 * indicates if this provider enables media removal.
	 * @return bool
	 */
	public function isDeleteEnabled();
	
	/**
	 * indicates if this provider enables media or metadata changes.
	 * @return bool
	 */
	public function isUpdateEnabled();
	
	/**
	 * indicates if this provider enables reports retrieval.
	 * @return bool
	 */
	public function isReportsEnabled();
	
	/**
	 * indicates that the sunrise and sunset attributes could be sent as part of the metadata and therefore there is no need to use the dirty flags for later submission or deletion.
	 * @return bool
	 */
	public function isScheduleUpdateEnabled();
	
	/**
	 * indicates that since this provider doesn’t support update action, delete and submit should be used instead.
	 * @return bool
	 */
	public function useDeleteInsteadOfUpdate();
	
	/**
	 * returns how many seconds before sunrise the job could be created.
	 * @return int
	 */
	public function getJobIntervalBeforeSunrise();
	
	/**
	 * returns how many seconds before sunrise the job could be created.
	 * @return int
	 */
	public function getJobIntervalBeforeSunset();
	
	/**
	 * returns array of entry fields, columns or custom data attributes that require updating of the remote destination
	 * @return array
	 */
	public function getUpdateRequiredEntryFields($distributionProfileId = null);
	
	/**
	 * returns array of xPaths that require updating of the remote destination
	 * @return array
	 */
	public function getUpdateRequiredMetadataXPaths($distributionProfileId = null);
}