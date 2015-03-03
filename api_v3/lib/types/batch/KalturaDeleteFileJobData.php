<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeleteFileJobData extends KalturaJobData
{
	/**
	 * @var string
	 */
	public $localFileSyncPath;
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($source_object)
	 */
	public function doFromObject($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$this->localFileSyncPath = $sourceObject->getLocalFileSyncPath();
		parent::doFromObject($sourceObject, $responseProfile);
	}
	
}