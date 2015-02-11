<?php

/**
 * @package Core
 * @subpackage model.data
 */
class kResponseProfileHolder implements IResponseProfileHolder
{
	/**
	 * @var int
	 */
	private $id;
	
	/**
	 * @var int
	 */
	private $systemName;
	
	/* (non-PHPdoc)
	 * @see IResponseProfileHolder::getId()
	 */
	public function getId()
	{
		return $this->id;
	}

	/* (non-PHPdoc)
	 * @see IResponseProfileHolder::setId()
	 */
	public function setId($id)
	{
		$this->id = $id;
	}
	
	/* (non-PHPdoc)
	 * @see IResponseProfileHolder::getSystemName()
	 */
	public function getSystemName()
	{
		return $this->systemName;
	}

	/* (non-PHPdoc)
	 * @see IResponseProfileHolder::setSystemName()
	 */
	public function setSystemName($systemName)
	{
		$this->systemName = $systemName;
	}

	/* (non-PHPdoc)
	 * @see IResponseProfileHolder::get()
	 */
	public function get()
	{
		if($this->id)
		{
			return ResponseProfilePeer::retrieveByPK($this->id);
		}
		elseif($this->systemName)
		{
			return ResponseProfilePeer::retrieveBySystemName($this->systemName);
		}
		
		return null;
	}
}