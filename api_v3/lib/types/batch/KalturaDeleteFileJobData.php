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
	public function fromObject($sourceObject, IResponseProfile $responseProfile = null)
	{
		$this->localFileSyncPath = $sourceObject->getLocalFileSyncPath();
		return parent::fromObject($sourceObject, $responseProfile);
	}
	
}