<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAccessControlAction 
{
	/**
	 * @var int accessControlActionType
	 */
	protected $type;
	
	/**
	 * @param int $type accessControlActionType
	 */
	public function __construct($type) 
	{
		$this->setType($type);
	}
	
	/**
	 * @return int accessControlActionType
	 */
	public function getType() 
	{
		return $this->type;
	}

	/**
	 * @param int $type accessControlActionType
	 */
	protected function setType($type) 
	{
		$this->type = $type;
	}
}
