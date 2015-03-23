<?php
/**
 * @package plugins.KalturaInternalTools
 * @subpackage api.objects
 */
class KalturaInternalToolsSession extends KalturaObject 
{
	
	/**
	 * 
	 * @var int
	 */
	public $partner_id = null;
	
	/**
	 * 
	 * @var int
	 */
	public $valid_until = null;
	/**
	 * 
	 * @var string
	 */
	public $partner_pattern = null;
	/**
	 * 
	 * @var KalturaSessionType
	 */
	public $type;
	/**
	 * 
	 * @var string
	 */
	public $error;
	/**
	 * 
	 * @var int
	 */
	public $rand;
	/**
	 * 
	 * @var string
	 */
	public $user;
	/**
	 * 
	 * @var string
	 */
	public $privileges;
	
	public function doFromObject($source_object, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$this->partner_id = $source_object->partner_id;
		$this->valid_until = $source_object->valid_until;
		$this->partner_pattern = $source_object->partner_pattern;
		$this->type = $source_object->type;
		$this->error = $source_object->error;
		$this->rand = $source_object->rand;
		$this->user = $source_object->user;
		$this->privileges = $source_object->privileges;
	}
}