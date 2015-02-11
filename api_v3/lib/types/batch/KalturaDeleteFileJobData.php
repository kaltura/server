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
	public function fromObject($sourceObject, KalturaResponseProfileBase $responseProfile = null)
	{
		$this->localFileSyncPath = $sourceObject->getLocalFileSyncPath();
		return parent::fromObject($sourceObject, $responseProfile);
	}
	
}