<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaListResponse extends KalturaObject
{
	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;

	/* (non-PHPdoc)
	 * @see KalturaObject::loadRelatedObjects($responseProfile)
	 */
	public function loadRelatedObjects(KalturaResponseProfileBase $responseProfile)
	{
		if($this->objects)
		{
			$this->objects->loadRelatedObjects($responseProfile);
		}
	}
}