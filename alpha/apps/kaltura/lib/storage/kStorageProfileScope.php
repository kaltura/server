<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kStorageProfileScope extends kScope
{
	/**
	 * Storage profile id that is passed as part of the scope
	 * @var int
	 */
	protected $storageProfileId;
	
	public function __construct()
	{
		parent::__construct();
		$this->setContexts(array(ContextType::EXPORT));
	}
	
	/**
	 * @return the $storageProfileId
	 */
	public function getStorageProfileId() 
	{
		return $this->storageProfileId;
	}

	/**
	 * @param int $storageProfileId
	 */
	public function setStorageProfileId($storageProfileId) 
	{
		$this->storageProfileId = $storageProfileId;
	}

	

}